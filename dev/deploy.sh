#!/usr/bin/env bash
# ส่งธีมขึ้น falconpro-th.com แล้วตรวจว่าเว็บได้ไฟล์ชุดใหม่จริง
#
# ลำดับงาน
#   1) push branch main ขึ้น GitHub ด้วยสิทธิ์ของ gh (บัญชี easpeciallab-max)
#   2) รอ 20 วินาที แล้วสั่ง GitHub ส่ง webhook ของ WP Pusher อีกรอบ
#      (webhook รอบแรกออกทันทีที่ push เสร็จ ซึ่งบางครั้งเร็วกว่าที่ GitHub เตรียมไฟล์ zip ของ commit ล่าสุด
#       WP Pusher จึงอาจติดตั้งชุดก่อนหน้า · รอบที่สองได้ชุดล่าสุดแน่นอน)
#   3) เทียบไฟล์ static ที่เปลี่ยนใน push นี้ (css js รูป ฟอนต์) กับไฟล์บนเว็บทีละไฟล์ และเทียบเลข Version ของ style.css
#      ทุกคำขอเติม ?v=<สุ่ม> ท้าย URL เพื่อข้ามแคชของ Cloudflare
#   4) ตรวจ header แคชของหน้า HTML สองคำขอ (หน้าปกติ และ URL ที่ไม่มีอยู่) แล้วพิมพ์ผลกับคำสั่งให้รันซ้ำเองได้
#
# ใช้งาน (จาก root ของ repo):   bash dev/deploy.sh
#   PHP_BIN="/path/to/php" bash dev/deploy.sh    รันชุดตรวจ dev/check-*.php ก่อน push (ไม่ผ่าน = ไม่ push)
#
# ข้อควรรู้
# - สคริปต์นี้ push ขึ้นเว็บจริง: รันเฉพาะเมื่อเจ้าของเว็บสั่ง deploy และ commit งานครบแล้ว
# - ไม่มีรหัสผ่านหรือ token ในไฟล์นี้ ใช้การล็อกอินของ gh บนเครื่องเท่านั้น (gh auth login / gh auth switch)
# - ไฟล์ PHP เทียบจากภายนอกไม่ได้ · หน้า HTML: โฮสต์เก็บสำเนาของแต่ละหน้าไว้ และอายุของสำเนาถูกกำหนดตอนที่เก็บ
#   · สำเนาที่เก็บหลังธีมรุ่นที่มีตัวจำกัดอายุแคช (inc/modules/infra.php ข้อ 6 · 5 นาที) เริ่มทำงาน หมดอายุภายใน 5 นาที
#   · สำเนาที่เก็บไว้ก่อน deploy ที่นำตัวจำกัดนี้ขึ้นเว็บเป็นครั้งแรก ยังใช้อายุเดิมของโฮสต์ (นานกว่า 5 นาที เคยวัดได้เกิน 17 นาที
#     โดยยังไม่หมดอายุ) ไม่มีปุ่มล้างใน WordPress ต้องรอหมดอายุเอง หน้าเหล่านั้นจึงยังเป็น HTML ชุดเก่าได้นานกว่า 5 นาที
#   · หน้า 404 และหน้า error ไม่ถูกจำกัดอายุและไม่ถูกเก็บ (ธีมคง no-store ของ WordPress ไว้)
#   ถ้าจะดูผลทันที เปิดหน้าเว็บโดยเติม ?v=<สุ่ม> ท้าย URL
set -euo pipefail
cd "$(dirname "$0")/.."

REPO="easpeciallab-max/falcon-pro-theme"
ACCOUNT="easpeciallab-max"
SITE="https://falconpro-th.com/wp-content/themes/falcon-pro"
THEME_DIR="falcon-pro"

say()  { printf '%s\n' "$*"; }
fail() { printf '%s\n' "$*" >&2; exit 1; }

command -v gh >/dev/null 2>&1 || fail "ไม่พบคำสั่ง gh (GitHub CLI) · ติดตั้งและล็อกอินก่อน"
command -v curl >/dev/null 2>&1 || fail "ไม่พบคำสั่ง curl"

# md5 ของ stdin (Git Bash / Linux ใช้ md5sum · macOS ใช้ md5)
if command -v md5sum >/dev/null 2>&1; then
	hash_stdin() { md5sum | cut -d' ' -f1; }
else
	hash_stdin() { md5 -q; }
fi

BRANCH=$(git rev-parse --abbrev-ref HEAD)
[ "$BRANCH" = "main" ] || fail "ตอนนี้อยู่ที่ branch $BRANCH · deploy ได้จาก main เท่านั้น"

LOGIN=$(gh api user --jq .login 2>/dev/null || true)
[ "$LOGIN" = "$ACCOUNT" ] || fail "gh ล็อกอินเป็น \"${LOGIN:-ไม่ได้ล็อกอิน}\" · ต้องเป็น $ACCOUNT (gh auth switch -u $ACCOUNT)"

if [ -n "$(git status --porcelain -- "$THEME_DIR")" ]; then
	say "หมายเหตุ: มีไฟล์ใน $THEME_DIR/ ที่ยังไม่ได้ commit · ไฟล์เหล่านั้นจะไม่ถูกส่งขึ้นเว็บในรอบนี้"
fi

# ชุดตรวจก่อนปล่อย (เมื่อบอกที่อยู่ของ php มาใน PHP_BIN) · โฟลเดอร์ ext ข้างตัว php ถูกใช้เป็น extension_dir ให้เอง
if [ -n "${PHP_BIN:-}" ]; then
	PHP_FLAGS=(-d extension=mbstring -d extension=gd)
	if [ -d "$(dirname "$PHP_BIN")/ext" ]; then
		PHP_FLAGS=(-d "extension_dir=$(dirname "$PHP_BIN")/ext" "${PHP_FLAGS[@]}")
	fi
	for check in check-settings check-content check-brand check-release; do
		"$PHP_BIN" "${PHP_FLAGS[@]}" "dev/$check.php" >/dev/null || fail "dev/$check.php ไม่ผ่าน · ยังไม่ได้ push อะไร"
	done
	# check-settings.php จบด้วยรหัส 0 เสมอ: ผ่าน = มีคำว่า (none) ครบสองบรรทัด
	NONE=$("$PHP_BIN" "${PHP_FLAGS[@]}" dev/check-settings.php | grep -c "(none)" || true)
	[ "$NONE" = "2" ] || fail "dev/check-settings.php: ค่าเริ่มต้นกับช่องใน Customizer ไม่ตรงกัน · ยังไม่ได้ push อะไร"
	say "ชุดตรวจ dev/check-*.php ผ่านทั้ง 4 ตัว"
else
	say "ข้ามชุดตรวจ dev/check-*.php (ไม่ได้ตั้ง PHP_BIN) · ตรวจเองให้ผ่านก่อน deploy ทุกครั้ง"
fi

# git ทุกคำสั่งที่คุยกับ GitHub ใช้ credential ของ gh เท่านั้น (ล้าง helper เดิมของเครื่องก่อน กันส่งด้วยบัญชี GitHub อื่น)
gh_git() { git -c credential.helper= -c 'credential.helper=!gh auth git-credential' "$@"; }

gh_git fetch --quiet origin main || true
BEFORE=$(git rev-parse --verify --quiet origin/main || echo "")
AFTER=$(git rev-parse HEAD)

# header แคชของหน้า HTML (เฉพาะบรรทัดสถานะ cache-control และ x-cache-status) · ไม่มีวันทำให้สคริปต์ล้ม
ORIGIN="${SITE%%/wp-content/*}"
cache_headers() { curl -sI --max-time 20 "$1" 2>/dev/null | tr -d '\r' | grep -i -E '^(HTTP/|cache-control:|x-cache-status:)' || true; }

# ก่อน push: ธีมที่อยู่บนเว็บตอนนี้ส่งตัวจำกัดอายุแคช (s-maxage) อยู่แล้วหรือยัง
# ยัง = deploy รอบนี้เป็นรอบที่นำตัวจำกัดขึ้นเว็บ สำเนาหน้าเว็บที่โฮสต์เก็บไว้ก่อนหน้านี้จะยังใช้อายุเดิมของโฮสต์
CAP_BEFORE=$(cache_headers "$ORIGIN/?v=$(date +%s)$RANDOM" | grep -i '^cache-control:' | grep -c -i 's-maxage' || true)

gh_git push origin main

HOOK=$(gh api "repos/$REPO/hooks" --jq '[.[] | select(.config.url | test("wppusher"))][0].id')
if [ -z "$HOOK" ] || [ "$HOOK" = "null" ]; then
	fail "push แล้ว แต่ไม่พบ webhook ของ WP Pusher ใน $REPO · กด Update theme เองที่ WP Pusher > Themes"
fi

say "รอ GitHub เตรียมไฟล์ของ commit ล่าสุด 20 วินาที"
sleep 20
DELIVERY=$(gh api "repos/$REPO/hooks/$HOOK/deliveries" --jq '[.[] | select(.event == "push")][0].id')
if [ -n "$DELIVERY" ] && [ "$DELIVERY" != "null" ]; then
	gh api -X POST "repos/$REPO/hooks/$HOOK/deliveries/$DELIVERY/attempts" >/dev/null
	say "สั่งส่ง webhook ของ WP Pusher อีกรอบแล้ว"
else
	say "ไม่พบประวัติ webhook แบบ push ให้ส่งซ้ำ · ใช้ผลของรอบแรก"
fi

# ไฟล์ static ที่เปลี่ยนใน push นี้ · style.css อยู่ในรายการเสมอ (ใช้เทียบเลข Version)
FILES="$THEME_DIR/style.css"
if [ -n "$BEFORE" ] && [ "$BEFORE" != "$AFTER" ]; then
	CHANGED=$(git diff --name-only --diff-filter=AM "$BEFORE" "$AFTER" -- "$THEME_DIR" | grep -E '\.(css|js|webp|png|jpe?g|svg|woff2?)$' || true)
	FILES=$(printf '%s\n%s\n' "$FILES" "$CHANGED" | sort -u)
fi

# เทียบไฟล์ที่ commit แล้ว (ไม่ใช่ไฟล์ที่กำลังแก้ค้างในเครื่อง) กับไฟล์บนเว็บ · ลองซ้ำได้ 6 รอบ ห่างกัน 10 วินาที
verify() {
	local bad=0 f rel want got
	while IFS= read -r f; do
		[ -n "$f" ] || continue
		rel="${f#"$THEME_DIR"/}"
		want=$(git show "HEAD:$f" | hash_stdin)
		got=$(curl -fsSL -H "Cache-Control: no-cache" "$SITE/$rel?v=$(date +%s)$RANDOM" | hash_stdin || true)
		if [ "$want" = "$got" ]; then
			[ "${1:-}" = "quiet" ] || say "  ตรงกัน  $rel"
		else
			[ "${1:-}" = "quiet" ] || say "  ยังไม่ตรง  $rel"
			bad=1
		fi
	done <<EOF
$FILES
EOF
	return $bad
}

say "รอ WP Pusher ติดตั้ง แล้วเทียบไฟล์บนเว็บ"
OK=0
for _ in 1 2 3 4 5 6; do
	sleep 10
	if verify quiet; then
		OK=1
		break
	fi
done
verify || true

LOCAL_VER=$(git show "HEAD:$THEME_DIR/style.css" | sed -n 's/^Version:[[:space:]]*//p' | head -n 1 | tr -d '\r' || true)
LIVE_VER=$(curl -fsSL -H "Cache-Control: no-cache" "$SITE/style.css?v=$(date +%s)$RANDOM" | sed -n 's/^Version:[[:space:]]*//p' | head -n 1 | tr -d '\r' || true)
say "Version ของ style.css · ในเครื่อง ${LOCAL_VER:-?} · บนเว็บ ${LIVE_VER:-อ่านไม่ได้}"

if [ "$OK" != "1" ] || [ "$LOCAL_VER" != "$LIVE_VER" ]; then
	fail "เว็บยังไม่ได้ไฟล์ชุดใหม่ครบ · รัน bash dev/deploy.sh ซ้ำ (ไม่มีอะไรต้อง push ก็ตรวจซ้ำได้) หรือกด Update theme ที่ WP Pusher > Themes"
fi
say "deploy เรียบร้อย: $(git log -1 --format='%h %s')"

# ตรวจ header แคชของหน้า HTML สองคำขอ · แสดงผลให้อ่าน และพิมพ์คำสั่งไว้ให้รันซ้ำเอง
STAMP="$(date +%s)$RANDOM"
CHECK_PAGE="$ORIGIN/pricing/?v=$STAMP"
CHECK_404="$ORIGIN/deploy-check-$STAMP/"
say ""
say "ตรวจ header แคชของหน้า HTML"
say "1) หน้าปกติ ต้องได้ 200 และ cache-control: public, max-age=0, s-maxage=300"
say "   curl -sI \"$CHECK_PAGE\" | grep -i -E '^(HTTP/|cache-control:|x-cache-status:)'"
PAGE_HEAD=$(cache_headers "$CHECK_PAGE")
printf '%s\n' "${PAGE_HEAD:-(อ่าน header ไม่ได้)}" | sed 's/^/     /'
say "2) URL ที่ไม่มีอยู่ ต้องได้ 404 และ cache-control ที่มี no-store กับ private (ไม่มี s-maxage) หน้า 404 ต้องไม่ถูกโฮสต์เก็บ"
say "   curl -sI \"$CHECK_404\" | grep -i -E '^(HTTP/|cache-control:|x-cache-status:)'"
MISS_HEAD=$(cache_headers "$CHECK_404")
printf '%s\n' "${MISS_HEAD:-(อ่าน header ไม่ได้)}" | sed 's/^/     /'
if ! printf '%s\n' "$PAGE_HEAD" | grep -i '^cache-control:' | grep -q -i 's-maxage=300'; then
	say "   หมายเหตุ: คำขอที่ 1 ยังไม่เห็น s-maxage=300 · ธีมรุ่นใหม่อาจยังไม่ทำงาน (opcache) หรือโฮสต์ตัด header นี้ ลองรันคำสั่งที่ 1 อีกครั้งในอีกสักครู่"
fi
if printf '%s\n' "$MISS_HEAD" | grep -i '^cache-control:' | grep -q -i 's-maxage'; then
	say "   หมายเหตุ: คำขอที่ 2 มี s-maxage ทั้งที่เป็นหน้า 404 · หน้า 404 กำลังถูกประกาศว่าเก็บได้ ให้ตรวจ fenix_infra_page_cache_ttl() ใน inc/modules/infra.php"
fi

say ""
if [ "${CAP_BEFORE:-0}" = "0" ]; then
	say "อายุแคชของหน้า HTML: ก่อน deploy รอบนี้เว็บยังไม่ส่งตัวจำกัดอายุแคช (หรืออ่าน header ก่อน push ไม่ได้)"
	say "  สำเนาหน้าเว็บที่โฮสต์เก็บไว้ก่อน deploy รอบนี้ยังใช้อายุเดิมของโฮสต์ ซึ่งนานกว่า 5 นาที (เคยวัดได้เกิน 17 นาทีโดยยังไม่หมดอายุ)"
	say "  ไม่มีปุ่มล้างใน WordPress ต้องรอหมดอายุเอง ระหว่างนั้น URL ปกติอาจยังได้ HTML ชุดเก่า"
	say "  หน้าที่ถูกเก็บใหม่หลังจากนี้จะหมดอายุภายใน 5 นาที (x-cache-status กลับเป็น MISS หรือ EXPIRED)"
else
	say "อายุแคชของหน้า HTML: เว็บส่งตัวจำกัดอายุแคชอยู่แล้วก่อน deploy รอบนี้ สำเนาที่โฮสต์เก็บไว้จะเป็นชุดใหม่ภายใน 5 นาที"
	say "  (ยกเว้นสำเนาที่ถูกเก็บไว้ตั้งแต่ก่อนธีมรุ่นแรกที่มีตัวจำกัดนี้ ซึ่งยังค้างตามอายุเดิมของโฮสต์จนกว่าจะหมดอายุเอง)"
fi
say "  ดูผลทันทีได้ทุกเมื่อด้วย ?v=<สุ่ม> ท้าย URL"

# CLAUDE.md — FALCON PRO EA Theme

ไฟล์นี้เป็นบริบทสำหรับ Claude Code อ่านก่อนเริ่มงานในโปรเจกต์นี้

## โปรเจกต์คืออะไร
ธีม WordPress แบบ **custom** สำหรับเว็บขาย **FALCON PRO EA** — ระบบช่วยเทรดอัตโนมัติ (Expert Advisor) บน MetaTrader 5 เนื้อหาภาษาไทย **ดีไซน์ตามแบนเนอร์แบรนด์ (Banner theme v3)**: โทนเข้ม `#172125` เป็นหลัก (ค่าเริ่มต้น `color_mode` = dark) + พื้นขาวสำหรับส่วนอ่านยาว + บล็อกตัดทแยง + เขียว FALCON ทึบ ไอคอนในวงกลมเข้ม ปุ่มสี่เหลี่ยมมุมมนสีเขียว หัวข้อยืนเดี่ยวไม่มีคำเล็กนำหน้า
- **โครงเว็บยึดตาม ea2000.co** (หน้าแรกยาว + หน้าคู่มือ/ทดสอบ/แพ็กเกจ/เอกสาร ~19 เพจ + บทความ SEO) แต่เนื้อหาเขียนใหม่ทั้งหมดเป็นของ FALCON
- **เว็บในเครือที่สมบูรณ์แล้วคือ eawing.co** (เจ้าของเดียวกัน · ธีมของเว็บนั้น fork ไปจาก FALCON v3 · repo `easpeciallab-max/eawing-theme`): เจ้าของสั่ง 12 ต.ค. 2026 ว่าส่วนที่ FALCON ยังไม่ครบให้อิงเว็บนั้นได้ทุกหน้า **ยกเว้นหน้าโพสต์/บทความ** (เจ้าของวางแผนเอง) และ**ไม่ตามสไตล์หรือหน้าตา**ของเว็บนั้น · อิงได้เฉพาะโครง ระบบ และข้อมูลธุรกิจ (โบรกเกอร์พาร์ตเนอร์ ราคา ข้อมูลเพจกฎหมาย) · ข้อเท็จจริงของตัวสินค้าเว็บนั้น (เวอร์ชัน โหมด ผลทดสอบ กลไกสิทธิ์) ห้ามนำมาใช้ · ข้อความต้องเขียนใหม่เสมอ (`dev/check-copy.php` ตรวจ)
- **ข้อเท็จจริงของสินค้า FALCON มาจากแดชบอร์ดสมาชิก** `https://dashboard.falconpro-th.com/` (ระบบของเจ้าของ ไม่ได้อยู่ใน repo นี้ · ลิงก์ `/#visit` เยี่ยมชมก่อนสมัคร `/#signup` สมัครด้วยรหัสเชิญ `/#signin`): เทรดทองคำ **XAUUSD บน MT5 เท่านั้น** · ชุด EA: EA FALCON รุ่น 1.10 (FALCON PRO / PLUS / ULTRA / FURY) และ FALCON 5.0 (SCOUT / HUNTER / APEX) · ทุกชุดเปิดออเดอร์แก้แบบกริดขยาย Lot และ**ไม่ตัดขาดทุนเอง** · ส่งมอบ **สองทาง** (เจ้าของยืนยัน 12 ต.ค. 2026): ติดตั้งเองบนคอม/VPS ของลูกค้า หรือรันบนเซิร์ฟเวอร์ผ่านแดชบอร์ด (บัญชีจริงของโบรกเกอร์พาร์ตเนอร์เท่านั้น ลูกค้าพิมพ์รหัสผ่านเทรด MT5 ในแดชบอร์ดเอง) · ทีมงานไม่ขอรหัสผ่านหรือรหัสยืนยันใดทางแชท · ผู้ให้บริการ = บุคคลธรรมดาภายใต้ชื่อทางการค้า FALCON PRO · ผู้ใช้อายุ 20 ปีขึ้นไป · ราคา ฟรี / 6,990 / 9,990 บาท จ่ายครั้งเดียว · เปิดสิทธิ์แล้วไม่คืนเงิน เว้นแต่ยังไม่เปิดสิทธิ์หรือให้บริการตามตกลงไม่ได้ (ขอคืนภายใน 7 วัน) · **ห้ามเขียนว่าใช้กับสินทรัพย์อื่น ผู้ใช้เขียนกฎเทรดเอง EA ไม่เพิ่ม Lot หรือ Stop Loss จำกัดขาดทุน**
- **ไม่มี build step** — PHP + CSS + vanilla JS ตรง ๆ แก้ไฟล์แล้วใช้ได้เลย ไม่มี npm/compile
- ธีมอยู่ในโฟลเดอร์ `falcon-pro/` (root ของ repo เก็บเอกสาร dev) WP Pusher ตั้ง subdirectory = `falcon-pro`
- ต้องการ WordPress 6.0+ / PHP 7.4+
- **ที่มา**: fork มาจากธีม FENIX PRO EA แล้วรีแบรนด์ (สี + ชื่อ + text domain) — ดูหัวข้อ "เรื่อง prefix ภายใน" ด้านล่าง

## หลักการสำคัญ — ห้ามทำผิด
1. **Customizer-driven**: ทุกข้อความ/รูป/ราคา/ลิงก์ แก้ได้ผ่าน WordPress Customizer โดยไม่ต้องแตะโค้ด เวลาเพิ่มเนื้อหาใหม่ ให้เพิ่มเป็น **setting** เสมอ **ห้าม hardcode** ข้อความลงใน template
   - ข้อยกเว้น: **เนื้อหายาว** ของหน้าคู่มือ/บทความอยู่ใน post_content (แก้ในหน้าแก้ไขเพจของ WP) โดยมีไฟล์ตั้งต้นใน `inc/content/` ที่ตัวช่วย FALCON Setup นำเข้า
   - ตรวจว่า default ทุกตัวมี control: `php dev/check-settings.php` (ต้องได้ "(none)" ทั้งสองบรรทัด)
2. **รีวิว**: `show_reviews` ปิดเป็น default จนกว่าจะมีรีวิวจริง — **ห้ามแต่งรีวิวปลอม**
3. **ตัวเลขผลทดสอบ**: **ห้ามใส่ตัวเลขสมมติ** · ผล Backtest จริงของ 7 ชุดอยู่ใน**เนื้อหาเพจ /backtest/ เท่านั้น** (ตารางจากข้อมูล `/api/visit/backtests` ของแดชบอร์ด อ่านเมื่อ 12 ต.ค. 2026 พร้อมเงื่อนไขการทดสอบและข้อจำกัดทุกข้อ · ใช้ชื่อแสดงผล FALCON … เสมอ ชื่อภายในของข้อมูลเป็นชื่อแบรนด์ในเครือ ห้ามพิมพ์) · ห้ามมีตัวเลขผลทดสอบที่หน้าแรก โฆษณา คำอธิบาย SEO schema หรือ alt · ตัวเลขตัวอย่างของโหมดเยี่ยมชมห้ามนำมาใช้ · Forward Test ยังไม่มีผลที่เผยแพร่ · ช่องสถิติใน Customizer ที่ยังเป็น placeholder ("ระบุ...") ให้เจ้าของกรอกเอง · ค่าที่ยังเป็น placeholder จะถูกซ่อน และแสดงการ์ด "ยังไม่มีผลทดสอบที่เผยแพร่" แทน (เทมเพลตใช้ `fenix_pages_pending()` ใน pages.php · `fenix_results_pending()` ยังอยู่เป็นตัวสำรอง)
   - แบนเนอร์ใน `assets/img/banners/` มีตัวเลขในภาพ → ทุกจุดที่ใช้ต้องมีคำบรรยาย "ภาพประกอบ ไม่ใช่ผลการเทรดจริง" · ไม่ใช้ภาพที่มีตัวเลขกำไร/Win Rate/คอลัมน์ Profit (`account-growth`, `desk-setup`, `laptop-running`, `tablet-metatrader`, `laptop-overview` ถูกถอดออกจากค่าเริ่มต้นแล้ว 12 ต.ค. 2026)
4. **ห้ามลบหรือลดทอน** ข้อความ disclaimer และ risk warning (สำคัญต่อความถูกต้อง/ความน่าเชื่อถือ)
   - กล่องคำเตือนท้ายเว็บ: เจ้าของตัดสิน 13 ต.ค. 2026 ให้ใช้ฉบับสั้น (setting `footer_risk_short` · ไม่เกิน 2 บรรทัด) เพราะฉบับเต็มยาวเกินไปสำหรับ footer · ฉบับสั้นแสดงเฉพาะเมื่อมีลิงก์ไปหน้าประกาศความเสี่ยง (`footer.php` ตรวจให้ ไม่มีลิงก์ = กลับไปแสดงฉบับเต็ม) และต้องยังบอกว่าเสียเงินทุนได้ทั้งหมด ผลในอดีตไม่รับประกันอนาคต ไม่ใช่คำแนะนำการลงทุน · คำเตือนฉบับเต็ม (`risk_text`) ต้องอยู่ที่หน้าแรก หน้า /go และหน้าประกาศความเสี่ยงเสมอ ห้ามย่อที่อื่น
5. **อังกฤษพิมพ์ใหญ่**: ทุกคำภาษาอังกฤษแสดงเป็นตัวพิมพ์ใหญ่อัตโนมัติด้วย CSS (`text-transform: uppercase` บน `body`) ยกเว้น `.keep-case` (อีเมล/URL) → เขียน HTML เป็นพิมพ์เล็กปกติได้ ปล่อยให้ CSS จัดการ · อีเมล โดเมน นามสกุลไฟล์ (.ex5 .set) และ iPhone/iPad/iOS/macOS ถูกห่อ `span.keep-case.kc` อัตโนมัติด้วย `fenix_nocaps_buffer()` (infra.php · ฟิลเตอร์ `fenix_nocaps_pattern`) ใส่ `.keep-case` เองเฉพาะแบบที่ตัวจับไม่ครอบคลุม
6. **Escape เสมอ**: output ทุกจุดผ่าน `esc_html()` / `esc_url()` / `esc_attr()` (inline SVG จาก `fenix_icon()` มี phpcs:ignore เพราะ trusted)
7. **หัวข้อยืนเดี่ยว** (เจ้าของสั่ง 12 ต.ค. 2026 "ดูเป็นเอไอ"): ห้ามเพิ่มคำเล็กเหนือหัวข้อ (kicker/eyebrow) เลขบท "01 / 09" แถบเลขบทด้านข้าง ป้ายเม็ดยาเหนือชื่อ ชิปตกแต่งใต้ชื่อ หรือตัวหนังสือลายน้ำตัวใหญ่ ทั้งในเทมเพลต ค่าเริ่มต้น และรูปที่สร้าง (ปกเพจ แผนภาพ) · ของเดิมถูกปิดด้วย `show_kickers` (ค่าเริ่มต้นปิด · `fenix_show_kickers()` / `fenix_kicker()`), `home_show_rail`, `home_hero_show_brand`, `hero_badge` ว่าง, `show_footer_watermark` ปิด, `go_badges` ว่าง · เลขที่ใช้งานจริงยังอยู่ได้ (เลขขั้นตอน ลำดับ FAQ คำบรรยายภาพ ป้าย "แนะนำ" ระดับความเสี่ยง)
8. **ปุ่มสี่เหลี่ยมมุมมน** (เจ้าของสั่ง 12 ต.ค. 2026): ปุ่มและตัวควบคุมที่กดได้ทุกชนิดใช้ token `--btn-radius` / `--btn-radius-sm` / `--btn-radius-xs` · ห้ามใช้ `999px` หรือ `50%` กับของที่กดได้ (วงกลมเหลือเฉพาะไอคอนในการ์ด เลขขั้นตอน จุดสถานะ)

## เรื่อง prefix ภายใน (สำคัญ — อ่านก่อนแก้)
ธีมนี้ fork มาจาก FENIX แบบ **คงชื่อ identifier ภายในไว้เป็น `fenix_` / `fenix-` โดยตั้งใจ** เพื่อกันพัง เพราะ identifier เหล่านี้ **ผูกกันข้ามไฟล์ CSS↔PHP↔JS** ถ้าจะ rename ต้องเปลี่ยน **ทุกจุดพร้อมกัน** ไม่งั้นพัง:
- ฟังก์ชัน/ค่าเริ่มต้น: `fenix_mod()`, `fenix_defaults()`, `fenix_lines()`, `fenix_icon()` ฯลฯ (PHP ภายใน)
- คลาส body/Elementor: `fenix-has`, `fenix-el`, `fenix-elementor` (ใช้ทั้งใน functions.php และ style.css)
- enqueue handles: `fenix-fonts`, `fenix-style`, `fenix-main`
- JS hooks ⇄ functions.php: `window.fenixLoadMore`, AJAX action `fenix_load_more`, cookie `fenix_consent`, `fenixTracking`
- constant: `FALCON_VERSION` (อันนี้ rename แล้ว เพราะอยู่ใน functions.php ไฟล์เดียว)

**ของที่รีแบรนด์ไปแล้ว**: ชื่อแบรนด์ `FALCON PRO EA`, text domain `falcon-pro`, สีทั้งหมดใน style.css, theme header
**ถ้าอยากเปลี่ยน prefix เป็น `falcon_` จริง ๆ**: ทำเป็นงานแยก ใช้ find/replace ทั้ง repo (`fenix_`→`falcon_`, `fenix-`→`falcon-`, `fenixLoadMore`→`falconLoadMore` ฯลฯ) แล้ว `php -l` + ทดสอบ load more / consent banner / Elementor ให้ครบ — ไม่ใช่ของจำเป็น (คนละเว็บ ไม่ชนกับ FENIX)

## โครงสร้างไฟล์ (ใน falcon-pro/)
- `functions.php` — theme setup, enqueue (Noto Sans Thai self-host), **`fenix_defaults()`** (ค่า default ของทุก setting — แหล่งความจริงของเนื้อหาเริ่มต้น), helpers:
  - `fenix_mod($key)` — อ่านค่า setting (theme_mod) พร้อม fallback เป็น default
  - `fenix_lines($text)` — แตก textarea เป็น array (บรรทัดละ 1 รายการ)
  - `fenix_logo_url()` — URL โลโก้ (custom_logo หรือโลโก้ที่ฝังในธีม)
  - `fenix_fallback_menu()` — เมนูสำรองชี้ไป slug หน้าย่อย
  - `fenix_page_hero($kicker,$title,$subtitle)` — หัวหน้าเพจ ใช้ร่วมทุก template (kicker ถูกพิมพ์เฉพาะเมื่อเปิด `show_kickers`)
  - `fenix_line_cta($title,$sub)` — บล็อกติดต่อ (LINE OA / OpenChat / QR + สิ่งที่ทีมจะถาม) ปิดท้ายหน้าย่อย
  - `fenix_page_longform()` — เนื้อหายาวจาก editor + สารบัญ ต่อท้ายเทมเพลตที่มีส่วนออกแบบด้านบน
  - `fenix_wordmark_url('dark'|'light')`, `fenix_icon_badge($icon,'dark'|'green'|'soft')`, `fenix_breadcrumbs()`, `fenix_results_pending()`
  - `fenix_icon($name,$class)` — inline SVG icon (~20 ไอคอน)
- `inc/customizer.php` — สร้าง Customizer panel + sections จาก array `$sections` ด้วย **loop เดียว** (auto-register setting/control/sanitizer) FAQ 10 ข้อ generate ด้วย for-loop
- `header.php` / `footer.php` — โครง + เมนู (wp_nav_menu + fallback) + ปุ่ม LINE ลอย
- `front-page.php` — **หน้าแรก (hub)** รวม: hero, highlight bar, pain, about, features, gallery, fit, การ์ดนำทาง, FAQ ย่อ, risk strip, CTA
- `template-backtest.php` / `template-forward.php` / `template-pricing.php` / `template-install.php` / `template-risk.php` — เทมเพลตเพจ (มี `Template Name:` header)
- `index.php` / `single.php` / `page.php` — บล็อก/เพจทั่วไป (เผื่อบทความ SEO)
- `style.css` — design system ทั้งหมด (theme header comment อยู่บนสุด ห้ามย้าย)
- `assets/js/main.js` — sticky header, mobile nav (aria-expanded), IntersectionObserver `.reveal`→`.in`, load more, consent banner
- `assets/img/` — โลโก้ FALCON (ไอคอนวงกลม `logo.png`) · `brand/` wordmark dark/light, favicon, การ์ดแชร์ 1200×630 · `banners/` แบนเนอร์แบรนด์ (ชื่อไฟล์ SEO) · `brand/falcon-pro-line-qr-v1.png` QR ของ LINE OA (ถอดรหัสแล้วได้ลิงก์เดียวกับค่า `line_url` บนเว็บจริง · เปลี่ยน LINE เมื่อไรต้องออกไฟล์ชื่อใหม่) · `covers/<slug>.webp` รูปปกบทความ 1200×630 (ชื่อบทความ + หมวด + ไอคอน ไม่มีตัวเลข · สร้างด้วย `dev/make-covers.php`) · `covers/page-<slug>.webp` รูปปกเพจ 14 เพจ ไม่มีบรรทัดป้ายเหนือชื่อ (`make-covers.php page-<slug>`) · `dashboard/` ภาพหน้าจอแดชบอร์ดจากโหมดเยี่ยมชม (ตัวเลขในภาพเป็นข้อมูลตัวอย่าง ต้องมีคำบรรยายบอก) · `guides/` ภาพหน้าจอขั้นตอนคู่มือ (ชื่อไฟล์กลาง ๆ `falcon-pro-ea-<หัวข้อ>-v1.webp` · มาจากคู่มือของเว็บในเครือ ครอป/ลบข้อมูลส่วนตัวแล้ว · ภาพจำลอง iPhone 2 ภาพสร้างด้วย `dev/make-guide-mocks.php`) · `illus/` แผนภาพในเนื้อหา (`dev/make-art.php <ชื่อ>` · แม่แบบ `dev/preview/art.php`) · **เปลี่ยนรูปเมื่อไรให้เปลี่ยนชื่อไฟล์ (-v2)** เพราะ Cloudflare และ Media แคชตามชื่อ
- `screenshot.png` — ภาพ preview ธีม FALCON
- `readme.txt` — คู่มือผู้ใช้ (ภาษาไทย) สำหรับเจ้าของเว็บ
- `inc/setup.php` — **`fenix_site_pages()`** (manifest ทุกเพจ: slug → template, ไฟล์เนื้อหา, กลุ่มเมนู) + `fenix_seed_articles()` (รายการบทความ: เพิ่มบทความใหม่ = เพิ่ม slug ที่นี่ + ไฟล์ใน `inc/content/articles/` + ไอคอนใน `dev/preview/cover.php` แล้วรัน `dev/make-covers.php`) + หน้า admin **รูปแบบ → FALCON Setup** (1 สร้างเพจที่ยังไม่มี, ตั้งหน้าแรก/หน้าบทความ, สร้างเมนู · 2 นำเข้าบทความเป็นฉบับร่าง + รูปปก + meta Yoast · 3 ปล่อยบทความ: เผยแพร่ชุดแรกแล้วตั้งเวลาที่เหลือ อังคาร/ศุกร์ 10:00 น. เวลาไทย · 4 ตั้งค่า SEO และระบบ: ค่า Yoast ขององค์กร/ชื่อหน้า ค้นหา/404 ปิด author/date archive ปิดคอมเมนต์ และตั้งรูปปกเพจ `covers/page-<slug>.webp` เป็นภาพเด่นของเพจที่ยังไม่มี `fenix_setup_seo()` + `fenix_setup_page_covers()` · ไม่แตะรูปแชร์เริ่มต้นของ Yoast และไม่แตะชื่อหน้าหมวดบทความเว้นแต่ติ๊กเอง)
  - "แทนด้วยรุ่นล่าสุด" แทนทั้งเนื้อหา ชื่อเพจ และ meta Yoast · ลำดับเพจ (`menu_order`) มาจาก manifest · แถวใน manifest ที่ไม่มีไฟล์เนื้อหาถือว่ายังไม่เปิดใช้ · คอมเมนต์ HTML ในไฟล์เนื้อหาถูกตัดออกตอนเขียนลงเพจ (`fenix_setup_page_content()`) · ลิงก์ไปเพจใน manifest ที่ยังไม่เผยแพร่ทุกเพจถูกตัดเป็นข้อความธรรมดา (`fenix_unreachable_slugs()`)
  - ปล่อยบทความ = `fenix_setup_release_to_total()` → `fenix_setup_release_articles()` · ลำดับใน `fenix_seed_publish_order()` · ฉบับร่างที่เจ้าของยังไม่เคยแก้ (`fenix_seed_fingerprint()` ตรงกับ meta `fenix_seed_hash`) ถูกอัปเดตตามไฟล์ล่าสุดก่อนปล่อย ฉบับร่างที่แก้เองไม่ถูกแตะ · `fenix_publish_missed_schedule()` กันบทความตั้งเวลาค้างเมื่อ WP-Cron ไม่ทำงาน
  - **กติกาวันที่ของ WordPress ที่ต้องจำ**: ตั้งเวลาฉบับร่างต้องส่ง `edit_date => true` (ไม่งั้นถูกเผยแพร่ทันที) · ห้ามใช้ `post_modified === post_date` ตัดสินว่า "ยังไม่แก้" (เท่ากันทุกครั้งที่บันทึกฉบับร่าง) ให้ดู `post_modified_gmt` เป็นศูนย์แทน
- `inc/content/pages/*.html`, `inc/content/articles/*.html` — เนื้อหาตั้งต้น (บรรทัดแรก `<!--meta {json} -->`) · สเปก/กติกาการเขียนอยู่ที่ `dev/content-spec.md`
  - ลิงก์ไปบทความ/เพจกฎหมายที่ยังเป็นฉบับร่างถูกตัดอัตโนมัติด้วย `fenix_unlink_slugs()` (`the_content` priority 15 · ก่อนสร้างสารบัญ): รายการ `<li>` ที่มีแต่ลิงก์ → ลบ, ประโยคใน `span.xref`/`p.xref` → ลบ, ลิงก์อื่น → ข้อความธรรมดา · **ห้ามใช้ regex ที่ข้าม `</a>` ได้** (เคยกินบทความหายครึ่งหน้า) · `dev/check-content.php` จำลองกรณีร่างทั้งหมดให้แล้ว
- `inc/shortcodes.php` — `[falcon_line]`, `[falcon_brand]`, `[falcon_broker field="name|server|url|disclosure"]`, `[falcon_calc type="lot|drawdown"]`, `[falcon_email]`, `[falcon_if has="email|partner|openchat|line"]…[/falcon_if]`, `[falcon_figure name="account-flow|vps-flow|how-it-works"]` (ทะเบียน `fenix_figure_registry()` · ไฟล์ `-v2` ใน `assets/img/illus/`), `[falcon_dashboard mode="visit|signup|signin" pos="..."]ข้อความ[/falcon_dashboard]` (ปุ่มไปแดชบอร์ด) · กติกาวาง shortcode ในเนื้อหาอยู่ที่ `dev/content-spec.md` และ `dev/check-content.php` บังคับ
  - **โบรกเกอร์พาร์ตเนอร์**: ชื่อ เซิร์ฟเวอร์ และลิงก์สมัคร เป็นค่าใน Customizer หมวด 29 เท่านั้น (ห้ามพิมพ์ชื่อโบรกเกอร์ในไฟล์ธีม `dev/check-brand.php` จะไม่ผ่าน) · ในข้อความใช้ตัวแทน `{brand}` `{broker}` `{server}` `{disclosure}` (`fenix_tokens()` · ห้ามใส่ `{server}` ในหัวข้อ/คำโปรย/CTA เพราะถูกแปลงเป็นพิมพ์ใหญ่) · ข้อความเปิดเผยค่าตอบแทน = setting `broker_disclosure` · **พาร์ตเนอร์ถือว่าตั้งแล้วเมื่อมีทั้งลิงก์สมัครและชื่อโบรกเกอร์จริง** (`fenix_partner_ready()`): เงื่อนไขเดียวนี้คุมข้อความเปิดเผย `[falcon_if has="partner"]` และปุ่มสมัครทั้งสองจุด จึงไม่มีปุ่ม sponsored ที่ไร้ข้อความเปิดเผย · ลบข้อความเปิดเผยทิ้งแล้วระบบใช้ค่าเริ่มต้นแทน · หน้า FALCON Setup เตือนเมื่อใส่ลิงก์แต่ยังไม่ใส่ชื่อ · ลิงก์สมัครติด `rel="sponsored noopener"` เสมอ · หน้า /go ไม่มีข้อความเปิดเผยใต้ปุ่มสมัคร (ตามที่เจ้าของตัดสินไว้กับเว็บในเครือ)
- `inc/seo.php` — FAQPage (หน้าแรก + ทุกเพจที่มี `details.faq-item`), SoftwareApplication (หน้าแรก/แพ็กเกจ), BreadcrumbList + favicon สำรอง (เมื่อไม่มีปลั๊กอิน SEO) · เมื่อมี Yoast: เติมรูปแชร์ของธีมให้หน้าที่ Yoast ไม่มีรูป (`fenix_seo_yoast_share_fallback`) และประกาศภาษาไทย (`lang`, `og:locale`, schema) ขณะที่ภาษาเว็บใน WordPress ยังเป็น en_US (`fenix_seo_forced_lang`) · คำตอบ FAQ ใน schema ผ่าน `fenix_seo_plain_text()` (ขยาย shortcode ชื่อแบรนด์/โบรกเกอร์) · Offer ใน schema ออกเฉพาะเมื่อโหมดราคา + ติ๊ก `seo_pricing_confirmed` (แพ็กเกจฟรีถูกข้าม) · รูปขั้นตอนคู่มือที่มากับธีมเข้า image sitemap ของ Yoast (`fenix_seo_sitemap_theme_images`)
- `inc/parts/ea-panel.php` — แผงควบคุม EA จำลองใน Hero (HTML ล้วน ไม่มีตัวเลขผลเทรด)
- `template-guide.php` — หน้าคู่มือ (หัวหน้าเพจ `fenix_guide_hero()` ที่มีสรุปขั้นตอนอยู่ในตัว + ขั้นตอน + ช่องรูป + เช็กลิสต์ + เนื้อหายาว + คู่มือที่เกี่ยวข้อง) · `template-go.php` — หน้าลิงก์รวม /go แบบ**หน้าเดี่ยว** (ไม่มี header/footer/dock แต่มี wp_head/wp_footer จึงยังมีการ์ดคุกกี้) · `page.php` — เพจเอกสาร (about/privacy/terms/data-deletion)

### สถาปัตยกรรม v3 (โมดูล) · โครงตาม ea2000 ลุค FALCON
- `inc/components.php` — ส่วนประกอบกลาง: `fenix_has_line_url()`, `fenix_contact_target()`/`fenix_contact_button()` (LINE → /go/ ที่เผยแพร่ → ซ่อนปุ่ม · **ห้ามมีปุ่มชี้ `#`**), `fenix_text()`, `fenix_rich_text()` (textarea → HTML: ย่อหน้า, `- ` รายการ, `1. ` ลำดับ, `### ` h3, ` | ` ตาราง, `[ข้อความ](/slug/)` ลิงก์ภายใน), `fenix_page_sections()`, `fenix_media_slot()`/`fenix_media_fields()` (ช่องรูป `{key}_img/_img_mobile/_img_alt/_img_caption/_img_note` · ไม่มีรูป = คนทั่วไปไม่เห็น แอดมินเห็นกรอบเส้นประ · alt/คำบรรยายใช้ตัวแทน `{broker}` ได้ · `fenix_media_info()` อ่านขนาดจริงของไฟล์ในธีมแล้วพิมพ์ `--media-w` ให้ CSS จำกัด `max-width` รูปเล็กจึงไม่ถูกขยาย · **ห้ามใช้ `width:auto` กับรูป lazy** · ข้อความป้ายขยายรูป = setting `media_open_label`), `fenix_chapter_head()`
- `inc/modules/*.php` — **auto-require ทุกไฟล์** (glob) · โมดูลเพิ่ม default ผ่านฟิลเตอร์ `fenix_defaults` และ section ผ่าน `fenix_customizer_sections` · โมดูล = `home` (hero + บทต่อกันสูงสุด 12 บท ไม่มีเลขบทและ rail เป็นค่าเริ่มต้น · ลำดับอยู่ใน `fenix_home_chapter_defs()`), `dashboard` (แนะนำแดชบอร์ดสมาชิก: setting `show_dashboard` `dashboard_url` · `fenix_dashboard_url($mode)` `fenix_dashboard_button()` `fenix_dashboard_block($context)` ใช้บนหน้าแรก /go แพ็กเกจ วิธีติดตั้ง · เพจ `/dashboard/` = `template-dashboard.php` · เหตุการณ์ GA4 `dashboard_click` หลังยินยอม), `homeplus` (บทเสริมของหน้าแรก: ตารางเทียบเทรดเอง/ใช้ EA, เส้นทางเริ่ม 6 ขั้นพร้อมลิงก์คู่มือ, การ์ดอุปกรณ์, เช็กลิสต์ความพร้อมแบบติ๊กได้ · FAQ 14 ช่องผ่าน `fenix_home_faq_count()`), `chrome` (header + ปุ่มติดต่อบนหัวเว็บ `fenix_chrome_header_cta()`, footer + คอลัมน์คู่มือจาก manifest, dock, **บล็อกติดต่อบล็อกเดียวต่อหน้า** `fenix_chrome_launch()` ที่ `fenix_line_cta()` เรียก, QR ของ LINE แสดงเปิดบนจอใหญ่), `consent` (คุกกี้ PDPA 3 หมวด, cookie `fenix_consent` มีเลขรุ่น), `guides` (คู่มือ 6 หน้า + install: ข้อมูลใน Customizer ตาม prefix `fenix_guide_map()` · gacct, gdep, gmt5, gvpsw, gvpsa, gvpsi · รูปขั้นตอนที่มากับธีม = สมาชิกตัวที่ 5 ของแต่ละขั้น เป็นชื่อไฟล์ใน `assets/img/guides/` ค่าเริ่มต้นเป็น URL เต็ม), `pages` (backtest/forward/pricing/risk/docs/articles/single/404/search), `go` (หน้า /go 6 ขั้น: ปุ่มสมัครโบรกเกอร์เมื่อมีลิงก์ ขั้นฝากเงินลิงก์ไป /deposit/), `infra` (REST `falcon/v1/authcheck|mods`, hardening, robots, ฟอนต์ self-host, verification, จำกัดอายุ page cache ของโฮสต์ 5 นาที `fenix_page_cache_ttl` เฉพาะคำตอบ 200 ของผู้ไม่ล็อกอิน (404 หน้า error และคำตอบที่มี Set-Cookie ไม่ถูกแคช), ตัวห่อ keep-case อัตโนมัติ)
- `assets/css/<module>.css` + `assets/js/<module>.js` — **auto-enqueue** ตามลำดับ `fenix_asset_modules()` (components, chrome, home, guides, pages, go, dashboard, consent) หลัง style.css · `dashboard-page.css` โหลดเฉพาะเพจแดชบอร์ด · ท้าย `chrome.css` มีบล็อก "9) LEGACY" สำหรับ markup เก่าที่ค้างใน page cache ของโฮสต์ ลบได้ในรุ่นถัดไป · `assets/css/fonts.css` + `assets/fonts/` = Noto Sans Thai self-host (ไม่โหลดจาก Google)
- โทนเข้ม: `body.theme-dark` (Customizer `color_mode`, ค่าเริ่มต้น dark) + `fenix_section_tone($key,$alt)` ให้คลาส `.is-dark` · **ส่วนอ่านยาว (longform/บทความ/เอกสาร) ต้องเป็นพื้นขาวเสมอ** (กฎอยู่ท้าย style.css)
- `dev/check-brand.php` — ตรวจแบรนด์รั่ว (FENIX/ea2000/Zaurix) และไฟล์ .zip/.ex5 ต้องได้ "no brand leaks" · `dev/image-shot-list.md` — รายการภาพหน้าจอที่เจ้าของต้องถ่ายลงช่องรูปของคู่มือ
- **ห้าม** ใช้กรอบ HUD มุมเหลี่ยม/ปุ่มคีย์แคป/ฟอนต์ mono/dot-grid ของ ea2000 และ**ห้ามคัดลอกข้อความ** ระหว่างแบรนด์ (duplicate content) · ทำได้แค่ "โครงเหมือน" เท่านั้น

## วิธีเพิ่ม setting ใหม่ (pattern ที่ต้องทำตาม)
1. เพิ่ม default ใน `fenix_defaults()` (functions.php): `'my_key' => 'ค่าเริ่มต้น',`
2. เพิ่ม field เข้า section ที่เหมาะสมใน `$sections` (customizer.php): `'my_key' => array( 'Label ภาษาไทย', 'text' ),`
   - type รองรับ: `text`, `textarea`, `url`, `checkbox`, `image`, `radio` (radio ต้องส่ง choices + มี sanitizer)
3. เรียกในเทมเพลต: `fenix_mod('my_key')` แล้ว escape
   - checkbox → `fenix_sanitize_checkbox`, ราคา radio → `fenix_sanitize_pricing_mode` (price|contact)
   - รายการ (บรรทัดละ 1) → `fenix_lines()`
- เพิ่ม **section** ใหม่: `$sections['fenix_xxx'] = array(...)` ก่อนบรรทัด `$priority = 10;`

## Design tokens (style.css :root) — FALCON Banner theme v3
- พื้น: **เข้ม** `--black #172125` / `--black-deep #10181B` / `--black-2 #1D2A2F` / `--black-3 #25343A` (พื้นหลักของโหมด dark: hero, บทส่วนใหญ่, CTA, footer, page hero) + ขาว `--void #FFFFFF` + เทาอ่อน `--coal #F4F6F8` (ส่วนอ่านยาวและบทที่ระบุเป็นโทนสว่าง)
- เขียว FALCON: `--ember #22C55E` (ปุ่ม/ไฮไลต์ ใช้ทึบ), `--ember-deep #16A34A` (hover / ตัวอักษรใหญ่บนขาว), `--flare #15803D` (ลิงก์/ข้อความเขียวบนขาว — ผ่าน AA), `--mint #4AF28E` (เขียวโลโก้ ใช้บนพื้นดำเท่านั้น)
- **ตัวอักษรบนปุ่มเขียว = ดำเข้ม `--on-ember #06140B`** (ขาวบนเขียวไม่ผ่าน contrast) · บนพื้นดำใช้ `--on-black` / `--on-black-2`
- ตัวอักษร: `--ink #0F1216`, `--ash #4B535C`, `--ash-2 #636C76` · semantic: `--ok`, `--bad`, `--warn #B26C09`
- เงา: อนุญาตเฉพาะเงานุ่มสีเทา `--shadow-soft` / `--shadow-lift` · **ห้าม gradient / glow สี** · มุมทแยงทำด้วย `clip-path` สีทึบ
- คอมโพเนนต์: `.btn-fire` (เขียว), `.btn-dark`, `.btn-ghost` (ทุกปุ่มมุมมนตาม `--btn-radius`), `.ic-badge` (ไอคอนในวงกลมดำ), `.card` (ขาว มุม 18px เงานุ่ม), `.spaced` (อังกฤษเว้นระยะ — **ห้ามใช้กับข้อความไทย** สระ/วรรณยุกต์จะเพี้ยน)
- เนื้อหายาว (`.guide-content` / `.entry-content`): `.callout--info|tip|warn`, `.checklist`, `.crosslist`, `.guide-steps`, `.table-wrap > .data-table`, `.faq-block > details.faq-item`, `.related-links`
- สไตล์ v3 อยู่ **ท้าย style.css** (หัวข้อ 40–41 + บล็อก "v3 ·") เพื่อทับสไตล์เดิม — แก้ดีไซน์ให้แก้ที่ท้ายไฟล์
- ชื่อ CSS var เดิม (`--ember`/`--flare`/`--gold`/`--void`/`--coal*`) คงไว้ เปลี่ยนเฉพาะค่า
- ฟอนต์: **Noto Sans Thai** (display + body) self-host ที่ `assets/fonts/` + `assets/css/fonts.css` (ไม่โหลดจาก Google Fonts)

## เพจ & slug (สำคัญต่อการลิงก์)
แหล่งความจริงคือ `fenix_site_pages()` ใน `inc/setup.php` · เจ้าของเว็บกด **รูปแบบ → FALCON Setup → ตั้งค่าเว็บทั้งหมด** เพื่อสร้างเพจที่ขาด (ไม่แก้เพจที่มีอยู่ เว้นแต่ติ๊กเขียนทับ)
| slug | Template | กลุ่ม |
|------|----------|------|
| `home` | front-page.php (ตั้งเป็นหน้าแรก) | hub |
| `backtest` / `forward-test` | template-backtest.php / template-forward.php | test |
| `how-to-install` | template-install.php | guide |
| `open-mt5-account`, `deposit`, `mt5-login`, `vps-windows`, `vps-android`, `vps-ios`, `tools` | template-guide.php | guide |
| `pricing` | template-pricing.php | pricing |
| `dashboard` | template-dashboard.php (หน้าแนะนำแดชบอร์ดสมาชิก · ไม่ใช่ตัวระบบ) | guide |
| `risk-disclosure` | template-risk.php | doc |
| `about` | page.php (เพจเอกสาร ไม่ตั้ง template) | doc |
| `privacy-policy`, `terms-of-use`, `data-deletion` | page.php (สร้างเป็น **ฉบับร่าง** รอเจ้าของตรวจ · terms/privacy มีบรรทัด Effective date + Version ใน `.doc-meta`) | doc |
| `go` | template-go.php (หน้าลิงก์รวม) | hub |
| `articles` | index.php (ตั้งเป็นหน้าบทความ) | hub |

เมนู / footer / ดัชนีคู่มือ ดึงจาก manifest นี้ (`fenix_guide_links()`) — เพิ่มเพจคู่มือใหม่ = เพิ่มแถวใน manifest + ไฟล์ใน `inc/content/pages/`
การ์ด hub บนหน้าแรกลิงก์ไป slug เหล่านี้ (แก้ URL ได้ที่ Customizer)
**อย่าเปิดเพจเหล่านี้ด้วย Elementor** — จะทับการแสดงผลของ template

## ทดสอบก่อน commit
- lint ทุกไฟล์ที่แก้: PHP 8.4 (WinGet) `"C:/Users/THANAWUT HR/AppData/Local/Microsoft/WinGet/Packages/PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe/php.exe" -l <file>` (Laragon ไม่มีในเครื่องนี้แล้ว)
- **พรีวิวในเบราว์เซอร์โดยไม่ต้องมี WordPress**: `.claude/launch.json` → `falcon-preview` (`php -S localhost:8765 dev/preview/router.php` + stub ใน `dev/preview/wp-stubs.php`)
  - ทุก slug ใน manifest เปิดได้ เช่น `/`, `/vps-windows/`, `/go/`, `/articles/` · บทความ: `/article/<slug>/`
  - ต้องเปิด extension: `-d extension_dir=<php>/ext -d extension=mbstring -d extension=gd`
  - ค้นหา Fatal/Warning/Notice ในหน้าที่เรนเดอร์ก่อน commit
- รูปปกบทความ: เปิด preview server แล้วรัน `php -d extension=mbstring -d extension=gd dev/make-covers.php [slug ...]` (เรนเดอร์ `/__cover/<slug>/` ด้วย Chrome headless → `assets/img/covers/<slug>.webp`)
- ตรวจ setting ครบ: `php -d extension=mbstring dev/check-settings.php` · เนื้อหาตั้งต้น: `dev/check-content.php` · แบรนด์รั่ว: `dev/check-brand.php` · ตรรกะปล่อยบทความ: `dev/check-release.php` (ตารางโพสต์จำลองที่ทำตามกติกาวันที่ของ WordPress core) · ข้อความซ้ำกับธีมในเครือ: `dev/check-copy.php <โฟลเดอร์ธีมในเครือ>` ต้องได้ "0 new" (ต้อง clone ธีมอ้างอิงมาก่อน) (ทั้งห้าต้องผ่านก่อน commit · check-content คืนรหัส 1 เมื่อมีคอมเมนต์ HTML ในไฟล์เพจ ลิงก์นอกที่ไม่มี target/noopener หรือรูปขั้นตอนที่ไฟล์หาย)
- **ระยะขอบ**: `node dev/check-spacing.mjs` (Node 22+ และ Chrome · ไม่ต้องลงแพ็กเกจ · ต้องเปิด preview server) วัดทุกหน้าที่ 1280/768/375 ว่ามีข้อความหรือกล่องชิดขอบพื้นหรือเส้นขอบหรือไม่ ต้องได้ "spacing ok" ก่อน commit ที่แตะ CSS หรือเทมเพลต · `--base=https://falconpro-th.com --query=v=<สุ่ม>` ตรวจเว็บจริงหลัง deploy · `--shots=<โฟลเดอร์>` เก็บภาพจุดที่พบ · `--pages=<โฟลเดอร์>` เก็บภาพเต็มหน้าไว้ดูด้วยตา · จุดที่ยอมรับได้ใส่ในรายการ `ALLOW` ในไฟล์พร้อมเหตุผล (เจ้าของเจอปุ่มหมวดบทความและกล่องคำเตือนท้ายเว็บชิดขอบ 13 ต.ค. 2026 จึงมีตัวตรวจนี้) · ใน Git Bash ให้ขึ้นต้นคำสั่งด้วย `MSYS_NO_PATHCONV=1` เมื่อส่ง path ขึ้นต้นด้วย /
- พรีวิวตอบตามพอร์ตที่เปิดจริง (`fx_preview_origin()` ใน wp-stubs.php) จึงเปิด repo สำเนาที่สองบนพอร์ตอื่นคู่กันได้
- สวิตช์ของพรีวิว: `?broker=ชื่อ&server=ชื่อ&email=a@b.co` (ลองข้อความตอนมีโบรกเกอร์/อีเมล) · `?fxprice=0|1|pending` · `?gdimg=0|shots` · `?gdeager=1` (ให้รูป lazy วาดทันทีตอนถ่ายด้วย Chrome headless) · `?fx_chrome_demo=1|live` · ตรวจทุกหน้าในสองสถานะเสมอ: ค่าโบรกเกอร์เริ่มต้น และมีชื่อโบรกเกอร์จริง
- แผนภาพ/ภาพจำลอง/ปกเพจ: เปิด preview server แล้วรัน `dev/make-art.php <ชื่อ>` · `dev/make-guide-mocks.php` · `dev/make-covers.php page-<slug>`
- ก่อน push ทุกครั้งที่แก้เยอะ: รัน workflow รีวิวก่อนปล่อย (มุมมอง: WP runtime จริง, security, front-end, Customizer/data, SEO+Yoast, เนื้อหา · ยืนยันทุก finding ด้วย 2 skeptic) แล้วแก้ที่ยืนยันแล้วให้ครบ
- **เว็บจริงมี page cache ระดับโฮสต์** (header `x-cache-status: HIT/MISS/EXPIRED` · ไม่มีปุ่มล้างใน WordPress · หมดอายุเอง): ตรวจผลหลัง deploy ให้เติม `?v=<สุ่ม>` ท้าย URL เพื่อดูคำตอบสด · ผู้เข้าชม/Google เห็นฉบับใหม่หลังแคชหมดอายุ (ธีมส่ง header จำกัดอายุแคชไว้ 5 นาทีแล้ว)
- ชื่อล็อกอินแอดมินต้องไม่โผล่หน้าเว็บ (`inc/modules/infra.php` ส่วน "ชื่อล็อกอินของผู้ดูแลต้องไม่หลุดออกหน้าเว็บ"): หลังแก้ส่วนผู้เขียน/schema ให้ grep หาชื่อล็อกอินในหน้าบทความ, `/feed/`, sitemap
- push ต้องใช้บัญชี gh `easpeciallab-max`: `git -c credential.helper= -c 'credential.helper=!gh auth git-credential' push origin main`

## Deploy flow
แก้โค้ด → `git commit` → `git push` → WP Pusher ดึงลงเว็บ FALCON (manual กด Update หรือเปิด Push-to-Deploy)
- WP Pusher subdirectory = `falcon-pro`
- `bash dev/deploy.sh` = รันตัวตรวจ push จาก main แล้วเทียบเลขเวอร์ชันของ style.css บนเว็บจริง (ไม่มีรหัสลับในไฟล์)
- **หลัง deploy ที่แก้ค่าเริ่มต้น**: ค่าที่เคยบันทึกใน Customizer ชนะค่าเริ่มต้นใหม่เสมอ ให้เทียบกับ `GET /wp-json/falcon/v1/mods` · ลำดับบนเว็บจริง: ตั้งค่าหมวด 29 (โบรกเกอร์) และอีเมลก่อน → FALCON Setup "ตั้งค่าเว็บทั้งหมด" → "แทนด้วยรุ่นล่าสุด" รายเพจ → "4) SEO และระบบ" → เพิ่มเพจใหม่เข้าเมนูด้วยมือ
- repo สาธารณะใช้ WP Pusher ฟรีได้ — ธีมไม่มี secret (config อยู่ใน WP DB ผ่าน Customizer)
- เว็บ FALCON เป็น WordPress คนละตัวกับ FENIX — WP Pusher / Customizer แยกขาดกัน

## เช็คลิสต์รีแบรนด์ที่ยัง "ค้าง" (ทำบนเว็บ/ไฟล์ก่อน go-live)
- [x] เปลี่ยนโลโก้ + wordmark + favicon + การ์ดแชร์ + `screenshot.png` เป็นของ FALCON
- [x] กด **FALCON Setup → ตั้งค่าเว็บทั้งหมด** บนเว็บจริง (30 ก.ย. 2026 · หน้าสาธารณะ 15 หน้าเผยแพร่แล้ว)
- [x] ปิด "ขอให้ search engines ไม่ทำดัชนี" (11 ต.ค. 2026 · เว็บเปิดให้ Google เก็บแล้ว) · ลิงก์ถาวรเป็น Post name อยู่แล้ว
- [ ] ตรวจ/กรอกเพจกฎหมาย (ค้นคำว่า "เจ้าของเว็บ:" · ชื่อผู้ให้บริการ ที่อยู่ นโยบายคืนเงิน วันที่มีผล) แล้วกดเผยแพร่
- [x] ปล่อยบทความ 22 เรื่อง (11 ต.ค. 2026): เผยแพร่ 6 เรื่อง · ตั้งเวลาอีก 16 เรื่อง อังคาร/ศุกร์ 10:00 น. ถึง 4 ธ.ค. 2026 (ออกเอง ไม่ต้องกด) · บทความใหม่ในอนาคต: นำเข้าแล้วกด "3) ปล่อยบทความฉบับร่าง"
- [ ] ถ่ายภาพหน้าจอตาม `dev/image-shot-list.md` (36 ภาพ) แล้วอัปโหลดลงช่องรูปของคู่มือใน Customizer (รูป FENIX ถูกลบแล้ว ตอนนี้ยังไม่มีรูปขั้นตอน)
- [x] ลิงก์ LINE OA จริง (`line_url` ใน Customizer · 11 ต.ค. 2026)
- [ ] ราคาแพ็กเกจ (ตอนนี้โหมดสอบถามราคา) · โบรกเกอร์และชื่อเซิร์ฟเวอร์ · โซเชียล/อีเมล · LINE OpenChat/QR
- [ ] ปิดปลั๊กอินที่ยิง GA/Pixel เอง (Site Kit/PixelYourSite) แล้วใส่ ID ที่ 18) คุกกี้ เท่านั้น เพื่อให้โหลดหลังยินยอม
- หมายเหตุ: เพจที่สร้างไปแล้วไม่ถูกทับเมื่อ seed เปลี่ยน · ดัน `rev` ในบรรทัด meta ของไฟล์ แล้วหน้า FALCON Setup จะขึ้น "มีรุ่นใหม่" ให้ติ๊ก "ยืนยัน" + กด "แทนด้วยรุ่นล่าสุด" รายหน้า
- [ ] ใส่ตัวเลข Backtest/Forward จริง (ห้ามสมมติ)

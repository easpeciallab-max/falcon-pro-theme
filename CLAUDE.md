# CLAUDE.md — FALCON PRO EA Theme

ไฟล์นี้เป็นบริบทสำหรับ Claude Code อ่านก่อนเริ่มงานในโปรเจกต์นี้

## โปรเจกต์คืออะไร
ธีม WordPress แบบ **custom** สำหรับเว็บ Landing ขาย **FALCON PRO EA** — ระบบช่วยเทรดอัตโนมัติ (Expert Advisor) บน MetaTrader 5 เนื้อหาภาษาไทย โทนดำ–ฟ้าเทอร์คอยซ์–เงิน (ตามโลโก้เหยี่ยว)
- **ไม่มี build step** — PHP + CSS + vanilla JS ตรง ๆ แก้ไฟล์แล้วใช้ได้เลย ไม่มี npm/compile
- ธีมอยู่ในโฟลเดอร์ `falcon-pro/` (root ของ repo เก็บเอกสาร dev) WP Pusher ตั้ง subdirectory = `falcon-pro`
- ต้องการ WordPress 6.0+ / PHP 7.4+
- **ที่มา**: fork มาจากธีม FENIX PRO EA แล้วรีแบรนด์ (สี + ชื่อ + text domain) — ดูหัวข้อ "เรื่อง prefix ภายใน" ด้านล่าง

## หลักการสำคัญ — ห้ามทำผิด
1. **Customizer-driven**: ทุกข้อความ/รูป/ราคา/ลิงก์ แก้ได้ผ่าน WordPress Customizer โดยไม่ต้องแตะโค้ด เวลาเพิ่มเนื้อหาใหม่ ให้เพิ่มเป็น **setting** เสมอ **ห้าม hardcode** ข้อความลงใน template
2. **รีวิว**: `show_reviews` ปิดเป็น default จนกว่าจะมีรีวิวจริง — **ห้ามแต่งรีวิวปลอม**
3. **ตัวเลขผลทดสอบ**: ค่า Backtest/Forward เป็น placeholder ("ระบุ...") — **ห้ามใส่ตัวเลขสมมติ** ให้เจ้าของกรอกเอง
4. **ห้ามลบหรือลดทอน** ข้อความ disclaimer และ risk warning (สำคัญต่อความถูกต้อง/ความน่าเชื่อถือ)
5. **อังกฤษพิมพ์ใหญ่**: ทุกคำภาษาอังกฤษแสดงเป็นตัวพิมพ์ใหญ่อัตโนมัติด้วย CSS (`text-transform: uppercase` บน `body`) ยกเว้น `.keep-case` (อีเมล/URL) → เขียน HTML เป็นพิมพ์เล็กปกติได้ ปล่อยให้ CSS จัดการ
6. **Escape เสมอ**: output ทุกจุดผ่าน `esc_html()` / `esc_url()` / `esc_attr()` (inline SVG จาก `fenix_icon()` มี phpcs:ignore เพราะ trusted)

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
- `functions.php` — theme setup, enqueue (Noto Sans Thai), **`fenix_defaults()`** (ค่า default ของทุก setting — แหล่งความจริงของเนื้อหาเริ่มต้น), helpers:
  - `fenix_mod($key)` — อ่านค่า setting (theme_mod) พร้อม fallback เป็น default
  - `fenix_lines($text)` — แตก textarea เป็น array (บรรทัดละ 1 รายการ)
  - `fenix_logo_url()` — URL โลโก้ (custom_logo หรือโลโก้ที่ฝังในธีม)
  - `fenix_fallback_menu()` — เมนูสำรองชี้ไป slug หน้าย่อย
  - `fenix_page_hero($kicker,$title,$subtitle)` — หัวหน้าเพจ ใช้ร่วมทุก template
  - `fenix_line_cta($title,$sub)` — แถบ CTA ทัก LINE ปิดท้ายหน้าย่อย
  - `fenix_icon($name,$class)` — inline SVG icon (~20 ไอคอน)
- `inc/customizer.php` — สร้าง Customizer panel + sections จาก array `$sections` ด้วย **loop เดียว** (auto-register setting/control/sanitizer) FAQ 10 ข้อ generate ด้วย for-loop
- `header.php` / `footer.php` — โครง + เมนู (wp_nav_menu + fallback) + ปุ่ม LINE ลอย
- `front-page.php` — **หน้าแรก (hub)** รวม: hero, highlight bar, pain, about, features, gallery, fit, การ์ดนำทาง, FAQ ย่อ, risk strip, CTA
- `template-backtest.php` / `template-forward.php` / `template-pricing.php` / `template-install.php` / `template-risk.php` — เทมเพลตเพจ (มี `Template Name:` header)
- `index.php` / `single.php` / `page.php` — บล็อก/เพจทั่วไป (เผื่อบทความ SEO)
- `style.css` — design system ทั้งหมด (theme header comment อยู่บนสุด ห้ามย้าย)
- `assets/js/main.js` — sticky header, mobile nav (aria-expanded), IntersectionObserver `.reveal`→`.in`, load more, consent banner
- `assets/img/` — โลโก้ (**ต้องเปลี่ยนเป็นโลโก้ FALCON** — ดู readme.txt)
- `screenshot.png` — ภาพ preview ธีม (**ต้องเปลี่ยนเป็นภาพ FALCON**)
- `readme.txt` — คู่มือผู้ใช้ (ภาษาไทย) สำหรับเจ้าของเว็บ

## วิธีเพิ่ม setting ใหม่ (pattern ที่ต้องทำตาม)
1. เพิ่ม default ใน `fenix_defaults()` (functions.php): `'my_key' => 'ค่าเริ่มต้น',`
2. เพิ่ม field เข้า section ที่เหมาะสมใน `$sections` (customizer.php): `'my_key' => array( 'Label ภาษาไทย', 'text' ),`
   - type รองรับ: `text`, `textarea`, `url`, `checkbox`, `image`, `radio` (radio ต้องส่ง choices + มี sanitizer)
3. เรียกในเทมเพลต: `fenix_mod('my_key')` แล้ว escape
   - checkbox → `fenix_sanitize_checkbox`, ราคา radio → `fenix_sanitize_pricing_mode` (price|contact)
   - รายการ (บรรทัดละ 1) → `fenix_lines()`
- เพิ่ม **section** ใหม่: `$sections['fenix_xxx'] = array(...)` ก่อนบรรทัด `$priority = 10;`

## Design tokens (style.css :root) — โทน FALCON
- สี: `--void #0A0A0E`, `--coal`, `--ember #16E0C8` (เทอร์คอยซ์หลัก = แทนส้มไฟเดิม), `--flare #5BF3DE`, `--gold #69E8D2`, `--line-green #06C755` (ใช้เฉพาะปุ่ม LINE), `--warn #FFC53D`
- **หมายเหตุ**: ชื่อ CSS var ยังเป็น `--ember`/`--flare`/`--gold` (คงชื่อไว้ เปลี่ยนเฉพาะค่าสี) — `--ember` ตอนนี้ = เทอร์คอยซ์ ไม่ใช่ส้ม
- ฟอนต์: **Noto Sans Thai** (display + body) จาก Google Fonts
- การ์ด: `--card-bg` สว่างกว่าพื้น section เพื่อไม่ให้กล่องกลืนพื้นหลัง
- section: สลับ `.section` กับ `.section-alt` + เส้นแบ่งบาง (`border-soft`)

## เพจ & slug (สำคัญต่อการลิงก์)
หน้าแรก = `front-page.php` เพจที่ต้องสร้างใน WP แล้วเลือก Template + ตั้ง slug ให้ตรง:
| slug | Template |
|------|----------|
| `backtest` | FALCON — หน้า Backtest |
| `forward-test` | FALCON — หน้า Forward Test |
| `pricing` | FALCON — หน้า Pricing |
| `how-to-install` | FALCON — หน้า How to Install |
| `risk-disclosure` | FALCON — หน้า Risk Disclosure |

การ์ด hub บนหน้าแรกลิงก์ไป slug เหล่านี้ (แก้ URL ได้ที่ Customizer)
**อย่าเปิดเพจเหล่านี้ด้วย Elementor** — จะทับการแสดงผลของ template

## ทดสอบก่อน commit
- lint ทุกไฟล์ที่แก้ด้วย Laragon php: `& "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" -l <file>` (ต้อง "No syntax errors")
- เช็ค render (ถ้าต้องการ): stub ฟังก์ชัน WP แล้ว require front-page.php / template-*.php ดูว่าไม่มี Fatal/Warning/Notice

## Deploy flow
แก้โค้ด → `git commit` → `git push` → WP Pusher ดึงลงเว็บ FALCON (manual กด Update หรือเปิด Push-to-Deploy)
- WP Pusher subdirectory = `falcon-pro`
- repo สาธารณะใช้ WP Pusher ฟรีได้ — ธีมไม่มี secret (config อยู่ใน WP DB ผ่าน Customizer)
- เว็บ FALCON เป็น WordPress คนละตัวกับ FENIX — WP Pusher / Customizer แยกขาดกัน

## เช็คลิสต์รีแบรนด์ที่ยัง "ค้าง" (ทำบนเว็บ/ไฟล์ก่อน go-live)
- [ ] เปลี่ยนโลโก้: `assets/img/logo.png` + `assets/img/logo-128.png` เป็นโลโก้เหยี่ยว FALCON (หรืออัปผ่าน Customizer → Site Identity → Logo)
- [ ] เปลี่ยน `screenshot.png` เป็นภาพ preview ของ FALCON
- [ ] เปลี่ยนรูปขั้นตอนติดตั้ง `assets/img/install/step-0*.jpg` (ถ้าจอ/แบรนด์ต่างจาก FENIX)
- [ ] กรอกเนื้อหา/ราคา/ลิงก์ LINE จริงผ่าน Customizer
- [ ] ใส่ตัวเลข Backtest/Forward จริง (ห้ามสมมติ)

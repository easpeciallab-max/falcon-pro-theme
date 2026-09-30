# CLAUDE.md — FALCON PRO EA Theme

ไฟล์นี้เป็นบริบทสำหรับ Claude Code อ่านก่อนเริ่มงานในโปรเจกต์นี้

## โปรเจกต์คืออะไร
ธีม WordPress แบบ **custom** สำหรับเว็บขาย **FALCON PRO EA** — ระบบช่วยเทรดอัตโนมัติ (Expert Advisor) บน MetaTrader 5 เนื้อหาภาษาไทย **ดีไซน์ตามแบนเนอร์แบรนด์ (Banner theme v3)**: โทนเข้ม `#172125` เป็นหลัก (ค่าเริ่มต้น `color_mode` = dark) + พื้นขาวสำหรับส่วนอ่านยาว + บล็อกตัดทแยง + เขียว FALCON ทึบ ไอคอนในวงกลมเข้ม ปุ่ม pill สีเขียว ข้อความอังกฤษเว้นระยะกว้าง
- **โครงเว็บยึดตาม ea2000.co** (หน้าแรกยาว + หน้าคู่มือ/ทดสอบ/แพ็กเกจ/เอกสาร ~18 เพจ + บทความ SEO) แต่เนื้อหาเขียนใหม่ทั้งหมดเป็นของ FALCON
- **ไม่มี build step** — PHP + CSS + vanilla JS ตรง ๆ แก้ไฟล์แล้วใช้ได้เลย ไม่มี npm/compile
- ธีมอยู่ในโฟลเดอร์ `falcon-pro/` (root ของ repo เก็บเอกสาร dev) WP Pusher ตั้ง subdirectory = `falcon-pro`
- ต้องการ WordPress 6.0+ / PHP 7.4+
- **ที่มา**: fork มาจากธีม FENIX PRO EA แล้วรีแบรนด์ (สี + ชื่อ + text domain) — ดูหัวข้อ "เรื่อง prefix ภายใน" ด้านล่าง

## หลักการสำคัญ — ห้ามทำผิด
1. **Customizer-driven**: ทุกข้อความ/รูป/ราคา/ลิงก์ แก้ได้ผ่าน WordPress Customizer โดยไม่ต้องแตะโค้ด เวลาเพิ่มเนื้อหาใหม่ ให้เพิ่มเป็น **setting** เสมอ **ห้าม hardcode** ข้อความลงใน template
   - ข้อยกเว้น: **เนื้อหายาว** ของหน้าคู่มือ/บทความอยู่ใน post_content (แก้ในหน้าแก้ไขเพจของ WP) โดยมีไฟล์ตั้งต้นใน `inc/content/` ที่ตัวช่วย FALCON Setup นำเข้า
   - ตรวจว่า default ทุกตัวมี control: `php dev/check-settings.php` (ต้องได้ "(none)" ทั้งสองบรรทัด)
2. **รีวิว**: `show_reviews` ปิดเป็น default จนกว่าจะมีรีวิวจริง — **ห้ามแต่งรีวิวปลอม**
3. **ตัวเลขผลทดสอบ**: ค่า Backtest/Forward เป็น placeholder ("ระบุ...") — **ห้ามใส่ตัวเลขสมมติ** ให้เจ้าของกรอกเอง · ค่าที่ยังเป็น placeholder จะถูกซ่อน และแสดงการ์ด "ยังไม่มีผลทดสอบที่เผยแพร่" แทน (`fenix_results_pending()`)
   - แบนเนอร์ใน `assets/img/banners/` มีตัวเลขในภาพ → ทุกจุดที่ใช้ต้องมีคำบรรยาย "ภาพประกอบ ไม่ใช่ผลการเทรดจริง" · ไม่ใช้ภาพที่อวดกำไรชัด ๆ (`account-growth`)
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
- `functions.php` — theme setup, enqueue (Noto Sans Thai self-host), **`fenix_defaults()`** (ค่า default ของทุก setting — แหล่งความจริงของเนื้อหาเริ่มต้น), helpers:
  - `fenix_mod($key)` — อ่านค่า setting (theme_mod) พร้อม fallback เป็น default
  - `fenix_lines($text)` — แตก textarea เป็น array (บรรทัดละ 1 รายการ)
  - `fenix_logo_url()` — URL โลโก้ (custom_logo หรือโลโก้ที่ฝังในธีม)
  - `fenix_fallback_menu()` — เมนูสำรองชี้ไป slug หน้าย่อย
  - `fenix_page_hero($kicker,$title,$subtitle)` — หัวหน้าเพจ ใช้ร่วมทุก template
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
- `assets/img/` — โลโก้ FALCON (ไอคอนวงกลม `logo.png`) · `brand/` wordmark dark/light, favicon, การ์ดแชร์ 1200×630 · `banners/` แบนเนอร์แบรนด์ (ชื่อไฟล์ SEO) · `covers/<slug>.webp` รูปปกบทความ 1200×630 (ชื่อบทความ + หมวด + ไอคอน ไม่มีตัวเลข · สร้างด้วย `dev/make-covers.php`)
- `screenshot.png` — ภาพ preview ธีม FALCON
- `readme.txt` — คู่มือผู้ใช้ (ภาษาไทย) สำหรับเจ้าของเว็บ
- `inc/setup.php` — **`fenix_site_pages()`** (manifest ทุกเพจ: slug → template, ไฟล์เนื้อหา, กลุ่มเมนู) + `fenix_seed_articles()` (รายการบทความ: เพิ่มบทความใหม่ = เพิ่ม slug ที่นี่ + ไฟล์ใน `inc/content/articles/` + ไอคอนใน `dev/preview/cover.php` แล้วรัน `dev/make-covers.php`) + หน้า admin **รูปแบบ → FALCON Setup** (สร้างเพจที่ยังไม่มี, ตั้งหน้าแรก/หน้าบทความ, สร้างเมนู, นำเข้าบทความเป็นฉบับร่าง + รูปปก + meta Yoast)
- `inc/content/pages/*.html`, `inc/content/articles/*.html` — เนื้อหาตั้งต้น (บรรทัดแรก `<!--meta {json} -->`) · สเปก/กติกาการเขียนอยู่ที่ `dev/content-spec.md`
  - ลิงก์ไปบทความ/เพจกฎหมายที่ยังเป็นฉบับร่างถูกตัดอัตโนมัติด้วย `fenix_unlink_slugs()` (`the_content` priority 25): รายการ `<li>` ที่มีแต่ลิงก์ → ลบ, ประโยคใน `span.xref`/`p.xref` → ลบ, ลิงก์อื่น → ข้อความธรรมดา · **ห้ามใช้ regex ที่ข้าม `</a>` ได้** (เคยกินบทความหายครึ่งหน้า) · `dev/check-content.php` จำลองกรณีร่างทั้งหมดให้แล้ว
- `inc/shortcodes.php` — `[falcon_line]`, `[falcon_brand]`, `[falcon_broker]`, `[falcon_calc type="lot|drawdown"]`
- `inc/seo.php` — FAQPage (หน้าแรก + ทุกเพจที่มี `details.faq-item`), SoftwareApplication (หน้าแรก/แพ็กเกจ), BreadcrumbList + favicon สำรอง (เมื่อไม่มีปลั๊กอิน SEO)
- `inc/parts/ea-panel.php` — แผงควบคุม EA จำลองใน Hero (HTML ล้วน ไม่มีตัวเลขผลเทรด)
- `template-guide.php` — หน้าคู่มือ (ขั้นตอน + ช่องรูป + เช็กลิสต์ + เนื้อหายาว + คู่มือที่เกี่ยวข้อง) · `template-go.php` — หน้าลิงก์รวม /go แบบ**หน้าเดี่ยว** (ไม่มี header/footer/dock แต่มี wp_head/wp_footer จึงยังมีการ์ดคุกกี้) · `page.php` — เพจเอกสาร (about/privacy/terms/data-deletion)

### สถาปัตยกรรม v3 (โมดูล) · โครงตาม ea2000 ลุค FALCON
- `inc/components.php` — ส่วนประกอบกลาง: `fenix_has_line_url()`, `fenix_contact_target()`/`fenix_contact_button()` (LINE → /go/ ที่เผยแพร่ → ซ่อนปุ่ม · **ห้ามมีปุ่มชี้ `#`**), `fenix_text()`, `fenix_rich_text()` (textarea → HTML: ย่อหน้า, `- ` รายการ, `1. ` ลำดับ, `### ` h3, ` | ` ตาราง, `[ข้อความ](/slug/)` ลิงก์ภายใน), `fenix_page_sections()`, `fenix_media_slot()`/`fenix_media_fields()` (ช่องรูป `{key}_img/_img_mobile/_img_alt/_img_caption/_img_note` · ไม่มีรูป = คนทั่วไปไม่เห็น แอดมินเห็นกรอบเส้นประ), `fenix_chapter_head()`
- `inc/modules/*.php` — **auto-require ทุกไฟล์** (glob) · โมดูลเพิ่ม default ผ่านฟิลเตอร์ `fenix_defaults` และ section ผ่าน `fenix_customizer_sections` · โมดูล = `home` (หน้าแรก 9 บท + rail), `chrome` (header/footer launch console/dock/CTA band `fenix_line_cta` hook), `consent` (คุกกี้ PDPA 3 หมวด, cookie `fenix_consent` มีเลขรุ่น), `guides` (คู่มือ 5 หน้า + install: ข้อมูลใน Customizer ตาม prefix `fenix_guide_map()`), `pages` (backtest/forward/pricing/risk/docs/articles/single/404/search), `go` (หน้า /go), `infra` (REST `falcon/v1/authcheck|mods`, hardening, robots, ฟอนต์ self-host, verification)
- `assets/css/<module>.css` + `assets/js/<module>.js` — **auto-enqueue** ตามลำดับ `fenix_asset_modules()` (components, chrome, home, guides, pages, go, consent) หลัง style.css · `assets/css/fonts.css` + `assets/fonts/` = Noto Sans Thai self-host (ไม่โหลดจาก Google)
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
- ตัวอักษร: `--ink #0F1216`, `--ash #4B535C`, `--ash-2 #6C757F` · semantic: `--ok`, `--bad`, `--warn #B26C09`
- เงา: อนุญาตเฉพาะเงานุ่มสีเทา `--shadow-soft` / `--shadow-lift` · **ห้าม gradient / glow สี** · มุมทแยงทำด้วย `clip-path` สีทึบ
- คอมโพเนนต์: `.btn-fire` (เขียว), `.btn-dark`, `.btn-ghost`, `.ic-badge` (ไอคอนในวงกลมดำ), `.card` (ขาว มุม 18px เงานุ่ม), `.spaced` (อังกฤษเว้นระยะ — **ห้ามใช้กับข้อความไทย** สระ/วรรณยุกต์จะเพี้ยน)
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
| `open-mt5-account`, `mt5-login`, `vps-windows`, `vps-android`, `vps-ios`, `tools` | template-guide.php | guide |
| `pricing` | template-pricing.php | pricing |
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
- ตรวจ setting ครบ: `php -d extension=mbstring dev/check-settings.php` · เนื้อหาตั้งต้น: `dev/check-content.php` · แบรนด์รั่ว: `dev/check-brand.php` (ทั้งสามต้องผ่านก่อน commit)
- ก่อน push ทุกครั้งที่แก้เยอะ: รัน workflow รีวิวก่อนปล่อย (มุมมอง: WP runtime จริง, security, front-end, Customizer/data, SEO+Yoast, เนื้อหา · ยืนยันทุก finding ด้วย 2 skeptic) แล้วแก้ที่ยืนยันแล้วให้ครบ
- push ต้องใช้บัญชี gh `easpeciallab-max`: `git -c credential.helper= -c 'credential.helper=!gh auth git-credential' push origin main`

## Deploy flow
แก้โค้ด → `git commit` → `git push` → WP Pusher ดึงลงเว็บ FALCON (manual กด Update หรือเปิด Push-to-Deploy)
- WP Pusher subdirectory = `falcon-pro`
- repo สาธารณะใช้ WP Pusher ฟรีได้ — ธีมไม่มี secret (config อยู่ใน WP DB ผ่าน Customizer)
- เว็บ FALCON เป็น WordPress คนละตัวกับ FENIX — WP Pusher / Customizer แยกขาดกัน

## เช็คลิสต์รีแบรนด์ที่ยัง "ค้าง" (ทำบนเว็บ/ไฟล์ก่อน go-live)
- [x] เปลี่ยนโลโก้ + wordmark + favicon + การ์ดแชร์ + `screenshot.png` เป็นของ FALCON
- [ ] กด **FALCON Setup → ตั้งค่าเว็บทั้งหมด** บนเว็บจริง (หน้าย่อยบนเว็บจริงยัง 404)
- [ ] ปิด "ขอให้ search engines ไม่ทำดัชนี" (ตั้งค่า → การอ่าน) + ตั้งลิงก์ถาวรเป็น Post name เมื่อพร้อมเปิดตัว
- [ ] ตรวจ/กรอกเพจกฎหมาย (ค้นคำว่า "เจ้าของเว็บ:" · ชื่อผู้ให้บริการ ที่อยู่ นโยบายคืนเงิน วันที่มีผล) แล้วกดเผยแพร่
- [ ] ตรวจบทความฉบับร่างทีละบทความ (22 บทความ) แล้วทยอยเผยแพร่
- [ ] ถ่ายภาพหน้าจอตาม `dev/image-shot-list.md` (36 ภาพ) แล้วอัปโหลดลงช่องรูปของคู่มือใน Customizer (รูป FENIX ถูกลบแล้ว ตอนนี้ยังไม่มีรูปขั้นตอน)
- [ ] กรอกลิงก์ LINE จริง (จนกว่าจะกรอก ปุ่มติดต่อทุกปุ่มจะไปที่ /go/ แทน) · ราคาแพ็กเกจ · โบรกเกอร์ (29) · โซเชียล (15)
- [ ] ปิดปลั๊กอินที่ยิง GA/Pixel เอง (Site Kit/PixelYourSite) แล้วใส่ ID ที่ 18) คุกกี้ เท่านั้น เพื่อให้โหลดหลังยินยอม
- [ ] ถ้าเคยกด FALCON Setup ไปแล้ว: เนื้อหา seed ที่แก้ใหม่ (คู่มือ/backtest/forward) จะไม่ทับเพจเดิม ต้องติ๊ก "เขียนทับ" หรือแก้เอง
- [ ] ใส่ตัวเลข Backtest/Forward จริง (ห้ามสมมติ)

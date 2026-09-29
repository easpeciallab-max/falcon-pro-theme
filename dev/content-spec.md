# FALCON PRO EA · Content spec (seed pages & articles)

Seed content lives in `falcon-pro/inc/content/pages/<slug>.html` (pages) and
`falcon-pro/inc/content/articles/<slug>.html` (blog posts). The setup tool in
WP admin (Appearance → FALCON Setup) imports them into WordPress. After import the
owner edits them in the normal WP editor, so the files are only the starting point.

## File format

The first line must be a meta comment with JSON, and the body is plain HTML (no `<html>`, `<head>` or `<body>` tags, and no `<h1>`, because the template prints the H1 from the title):

```html
<!--meta {"title":"...","seo_title":"...","description":"...","keyword":"...","excerpt":"...","category":"...","kicker":"..."} -->
<p class="lead">...</p>
<h2>...</h2>
...
```

- `title` is the page/post title (H1). Use Thai with the English keyword included, and keep it under about 70 characters.
- `seo_title` is the `<title>` for Yoast. Keep it at 60 characters or less and end it with ` · FALCON PRO EA`.
- `description` is the meta description. Aim for 120–155 Thai characters and include the focus keyword naturally.
- `keyword` is the focus keyword (Thai or mixed), e.g. `EA MT5`, `VPS สำหรับ EA`.
- `excerpt` is 1–2 sentences, used for article cards. It is for articles only.
- `category` is for articles only and must be one of: `พื้นฐาน EA`, `บริหารความเสี่ยง`, `MT5 & VPS`.
- `kicker` is a small English label above the H1 on pages (e.g. `Guide`, `Account`, `VPS · Windows`).

## Shortcodes (rendered live by the theme)

- `[falcon_line]ข้อความปุ่ม[/falcon_line]` renders a green LINE button (URL from Customizer). Use it 1–2 times per page.
- `[falcon_line pos="vps-guide"]...[/falcon_line]` adds the optional `pos` attribute, which is used for click tracking.
- `[falcon_brand]` renders "FALCON PRO EA".
- `[falcon_broker]` renders the broker name set in the Customizer. The default reads like "โบรกเกอร์ที่คุณเลือก", so write sentences that still work generically.
- `[falcon_broker field="server"]` renders the MT5 server name the owner sets. The default is a generic phrase like "ชื่อเซิร์ฟเวอร์ที่ได้รับทางอีเมล".
- `[falcon_calc type="lot"]` renders the lot size calculator, and `[falcon_calc type="drawdown"]` renders the drawdown recovery calculator. Use these only where relevant.

## Internal links

Write internal links as `{{home}}/slug/`, for example `<a href="{{home}}/how-to-install/">`. The token is replaced with the site URL on import.

Valid page slugs:
- `how-to-install`
- `backtest`
- `forward-test`
- `pricing`
- `risk-disclosure`
- `open-mt5-account`
- `mt5-login`
- `vps-windows`
- `vps-android`
- `vps-ios`
- `tools`
- `about`
- `privacy-policy`
- `terms-of-use`
- `data-deletion`
- `go`
- `articles`

Articles link each other as `{{home}}/<article-slug>/`, because posts use the /%postname%/ permalink.

## HTML components (styled by the theme; use exactly these classes)

```html
<p class="lead">ย่อหน้าเปิด 2–3 บรรทัด สรุปว่าหน้านี้ตอบอะไร</p>

<div class="callout callout--info"><p><strong>สรุปสั้น:</strong> ...</p></div>
<div class="callout callout--tip"><p>...</p></div>
<div class="callout callout--warn"><p>...</p></div>

<ul class="checklist"><li>...</li></ul>          <!-- green check bullets -->
<ul class="crosslist"><li>...</li></ul>          <!-- red x bullets -->

<ol class="guide-steps">                            <!-- numbered step cards -->
  <li><h3>ชื่อขั้นตอน</h3><p>...</p></li>
</ol>

<div class="table-wrap"><table class="data-table">
  <thead><tr><th>..</th><th>..</th></tr></thead>
  <tbody><tr><td>..</td><td>..</td></tr></tbody>
</table></div>

<div class="faq-block">
  <details class="faq-item"><summary><span>คำถาม?</span><i class="faq-plus" aria-hidden="true"></i></summary>
    <div class="faq-answer"><p>คำตอบ</p></div></details>
</div>

<div class="related-links"><h2>คู่มือที่เกี่ยวข้อง</h2><ul>
  <li><a href="{{home}}/vps-windows/">...</a></li>
</ul></div>
```

- The theme builds a table of contents from the H2/H3 headings automatically, so do not write your own TOC.
- The FAQ `details.faq-item` blocks are automatically turned into FAQPage schema.
- Do not add `<img>` tags. The owner adds real screenshots in the editor.
- Do not add inline styles or scripts.

## Writing rules (must follow)

1. **Write original Thai text.** Do not copy wording from other websites, including ea2000.co. Use a friendly, clear, professional tone written for Thai retail traders, with short paragraphs.
2. **Never invent performance numbers.** That means no win rate, profit %, drawdown %, trade counts or "results" for FALCON PRO EA. Generic educational examples are fine when they are clearly hypothetical (e.g. "สมมติทุน 1,000 USD เสี่ยง 1% = 10 USD"), are about maths or risk, and are never about FALCON's results.
3. **Never promise or guarantee profit.** Avoid words like "รวยเร็ว", "กำไรแน่นอน", "ไม่มีขาดทุน" or "การันตี", except when warning against them.
4. **Every page and article must include risk context.** At least one `callout--warn` that says trading carries risk and can lose capital, and that past results do not guarantee future results.
5. **Do not invent facts about the business.** That covers company name, address, registration, broker partnerships, prices, refund terms, and team names or photos. Where such a fact is needed, either keep it generic ("ทีมงาน", "โบรกเกอร์ที่รองรับ") or use the shortcodes above. For legal pages, write standard clauses and refer to the business as "[falcon_brand]" or "เรา".
6. **Keep technical facts about MT5, VPS and Windows App accurate.**
   - Microsoft's remote desktop app on Android/iOS is now called "Windows App" (formerly "Remote Desktop").
   - The MT5 data folder path is File → Open Data Folder → MQL5 → Experts.
   - The toolbar button is called "Algo Trading" in current MT5 builds (formerly "AutoTrading").
   - The Strategy Tester opens with Ctrl+R.
   - EAs cannot run in the MT5 mobile app.
   - Use .ex5 for compiled files.
7. **SEO:** use the focus keyword in the title, the first paragraph, at least one H2, and naturally 3–6 times in total. Add related Thai search phrases, such as บอทเทรด, โรบอทเทรด, EA เทรดทอง, EA forex, MT5, and วิธีติดตั้ง EA.
8. **Structure and length:** articles are 1,200–2,200 Thai words, with 6–12 H2s, some H3s, and a FAQ block (3–5 Qs) near the end. They end with a short CTA paragraph plus `[falcon_line]`. Guide pages follow the per-page outline you are given.
9. Write English technical terms in normal case (e.g. "Stop Loss"). The theme uppercases them visually.

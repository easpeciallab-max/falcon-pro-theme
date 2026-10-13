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
- `kicker` is a small English label for pages (e.g. `Guide`, `Account`, `VPS · Windows`). It is NOT printed by default: since 12 Oct 2026 headings stand alone (owner decision) and the label shows above the H1 only while the Customizer switch `show_kickers` (section 1) is on. Templates also use it as the fallback page title, so keep the key.
- `rev` (pages) is the seed revision number. Raise it by one whenever the file changes in a way the live page should receive: FALCON Setup then shows "มีรุ่นใหม่" for that page and the owner replaces it with one click.

Nothing but the body may follow the meta line. Do not leave HTML comments (notes to the owner, TODO markers) in a file. FALCON Setup strips them from page seeds on import (`fenix_setup_page_content()`), but article seeds are imported exactly as written, and a comment that reaches a page stays readable in its public source. Put notes for the owner in this file or in `readme.txt` instead. `dev/check-content.php` fails a page seed that carries an HTML comment after the meta line, or the old owner marker text "เจ้าของเว็บ:".

## Shortcodes (rendered live by the theme)

The full list is `fenix_shortcode_tags()` in `falcon-pro/inc/shortcodes.php`. `dev/check-content.php` fails on any other `[falcon_…]` tag, on unknown attribute values and on unbalanced pairs.

Buttons and inline values:

- `[falcon_line]ข้อความปุ่ม[/falcon_line]` renders a green LINE button (URL from Customizer). Use it 1–2 times per page.
- `[falcon_line pos="vps-guide"]...[/falcon_line]` adds the optional `pos` attribute, which is used for click tracking.
- `[falcon_brand]` renders "FALCON PRO EA".
- `[falcon_broker]` renders the broker name set in the Customizer. The default reads like "โบรกเกอร์ที่คุณเลือก", so write sentences that still work generically.
- `[falcon_broker field="server"]` renders the MT5 server name the owner sets. The default is a generic phrase like "ชื่อเซิร์ฟเวอร์ที่ได้รับทางอีเมล". Never put it in a heading: the theme uppercases headings and a server name must keep its exact case.
- `[falcon_email]` renders the contact e-mail from the Customizer as a `mailto:` link (encoded against harvesters, class `keep-case`). It prints nothing while no valid address is set, so the sentence around it must be wrapped in `[falcon_if has="email"]` (see below).
- `[falcon_calc type="lot"]` renders the lot size calculator, and `[falcon_calc type="drawdown"]` renders the drawdown recovery calculator. Use these only where relevant.

Member dashboard (prints nothing while the feature is switched off or the address is empty in Customizer section 1.1):

- `[falcon_dashboard mode="visit"]ข้อความปุ่ม[/falcon_dashboard]` renders a button to the FALCON PRO member dashboard. It opens in a new tab (`target="_blank" rel="noopener"`) and carries `data-dash="<mode>"` and `data-dash-pos="<pos>"` for the `dashboard_click` event. Modes: `visit` (visit mode: look around without an account, green button), `signup` (sign-up form, dark button), `signin` (sign-in form, dark button). No `mode` means `visit`. The optional `pos="dashboard-visit"` names the place for click tracking. An empty label uses the label of that mode from the Customizer. When the owner clears that Customizer label, every button of that mode is hidden, also the ones a seed gives its own text, so the sentence before the button must still read complete without it.
- The address is one Customizer setting (`dashboard_url`, module `inc/modules/dashboard.php`). Never type the dashboard address into a seed, and never write a plain `<a>` to it: the check fails a file that contains the host name.
- Put it on a line of its own (it prints a whole button block, like `[falcon_line]`). Never inside a sentence, a list item, a heading or an FAQ answer.
- A page with a `visit` button must say on the same page that accounts, orders, profit and loss figures and notices in visit mode are sample data (`ข้อมูลตัวอย่าง`), not trading results. A page with a `signup` button must say that signing up needs an invitation code (`รหัสเชิญ`) from the team. `dev/check-content.php` looks for both words.
- Internal links to the explanation page are written `{{home}}/dashboard/` like any other page.

Partner broker (all three print nothing until the owner sets a signup link in Customizer section 29):

- `[falcon_broker field="url"]` renders the signup button. The link always carries `rel="sponsored"`. Put it on a line of its own.
- `[falcon_broker field="disclosure"]` renders the partner disclosure as one whole block (a `<p>`), from the single Customizer setting `broker_disclosure`. Put it on a line of its own: never in the middle of a paragraph, and never inside a `callout` box you typed yourself (the empty frame would stay visible while the disclosure is hidden). `[falcon_broker field="disclosure" box="1"]` prints it inside an info callout instead.
- Every file that prints `[falcon_broker field="url"]` must also print `[falcon_broker field="disclosure"]`, directly above the button or at the first place the page talks about the partner. Once per page is enough. Guide pages whose top section already ends with the disclosure (the `{disclosure}` token in the Customizer intro text) still need it next to a signup button further down the long text.

Conditional text:

- `[falcon_if has="partner"]…[/falcon_if]` prints its content only while that Customizer value is set. Conditions: `email` (a valid contact e-mail), `partner` (the broker signup link AND a real broker name: `fenix_partner_ready()`, the same gate as the disclosure and both signup buttons; a link saved without a broker name renders exactly like "no partner"), `openchat` (LINE OpenChat link), `line` (LINE OA link). `has="email,partner"` needs both. `not="partner"` prints only while the value is NOT set, so `[falcon_if has="partner"]A[/falcon_if][falcon_if not="partner"]B[/falcon_if]` gives an either/or sentence.
- Use it for every sentence that is only true with that value: a sentence that names the partner broker as a partner, a "write to us at [falcon_email]" line, an invitation to the OpenChat group. A sentence that merely mentions `[falcon_broker]` and still reads correctly with the generic default needs no wrapper.
- Two ways to write it. Inside a sentence, with other text in the same paragraph: `<p>ติดต่อทีมงานได้ทาง LINE[falcon_if has="email"] หรืออีเมล [falcon_email][/falcon_if]</p>`. Around whole blocks (a paragraph, a list item, a heading with its paragraphs), with both tags OUTSIDE the block tags: `[falcon_if has="partner"]<p>…</p>[/falcon_if]`, or each tag on its own line around several blocks.
- Never make it the only content of a block tag (`<p>[falcon_if …]…[/falcon_if]</p>`, same for `<li>` and headings): WordPress removes that `<p>` when the condition holds and an empty tag is left when it does not. Never nest one `[falcon_if]` inside another (WordPress cannot parse it). Keep it out of FAQ questions.
- The FAQ schema and the fallback meta description follow the same conditions (`fenix_seo_plain_text()` in `inc/seo.php`): hidden text is not sent to search engines, `[falcon_brand]` and `[falcon_broker]` are replaced by their values, buttons are dropped.

Diagrams:

- `[falcon_figure name="vps-flow"]` renders a diagram that ships with the theme (`falcon-pro/assets/img/illus/`), with its real width and height, lazy loading, a narrow-screen file when one exists and the caption from the registry. Optional `alt="…"` and `caption="…"` override the registry text. Put it on a line of its own.
- Names come from `fenix_figure_registry()` in `inc/shortcodes.php` (filter `fenix_figure_registry`). Three figures are registered: `account-flow` (open-mt5-account), `vps-flow` (the three VPS pages) and `how-it-works` (not placed by a seed: the home page uses its file as the default of the image slot `home_what_img`). An entry is `'name' => array( 'file' => 'falcon-pro-ea-<name>-v<N>.webp', 'alt' => '…', 'caption' => '…' )`, and a file called `…-mobile-v<N>.webp` (or `…-v<N>-mobile.webp`) next to it is picked up as the narrow-screen version. Current files: `falcon-pro-ea-account-flow-v2.webp`, `falcon-pro-ea-vps-flow-v3.webp` (version 3 since 13 Oct 2026: the short tags inside the cards are small rounded rectangles, not capsules; same words as version 2) and `falcon-pro-ea-how-it-works-v2.webp`, each with its `-mobile-v<N>.webp` file of the same version (older versions are deleted). An entry without its file prints nothing, and `dev/check-content.php` fails a seed that uses a name without a file.
- The pictures are rendered by `dev/make-art.php` from the texts in `dev/preview/art.php` (preview: `/__art/<name>/?layout=wide|tall`). All text is baked into the image, so a changed sentence needs a new version number in `art.php`, the same file name in the registry and, for `how-it-works`, in `fenix_home_defaults()` (`inc/modules/home.php`), and the files of the older version are deleted (`make-art.php` lists the ones still on disk). A diagram starts with its title and one sub line: no label line above the title (owner, 12 Oct 2026), short tags inside a card are rounded rectangles like the status labels of the site (never a capsule), and the same no-label rule holds for the page covers `assets/img/covers/page-<slug>.webp` (`dev/make-covers.php pages`). What a diagram says must match how the product really works: the user picks an EA set, capital and Lot method and does not write trading rules, the EA does not cut losses by itself, stopping the EA does not close open orders, and the team asks for no password or code.
- File names stay neutral (no other brand, no broker name), a changed picture gets a new version number in its name, alt and caption are new FALCON text, and no registered picture may show trade results, profit or win-rate figures.

## Internal links

Write internal links as `{{home}}/slug/`, for example `<a href="{{home}}/how-to-install/">`. The token is replaced with the site URL on import.

Valid page slugs:
- `how-to-install`
- `backtest`
- `forward-test`
- `pricing`
- `risk-disclosure`
- `open-mt5-account`
- `deposit`
- `mt5-login`
- `vps-windows`
- `vps-android`
- `vps-ios`
- `tools`
- `dashboard`
- `about`
- `privacy-policy`
- `terms-of-use`
- `data-deletion`
- `go`
- `articles`

Articles link each other as `{{home}}/<article-slug>/`, because posts use the /%postname%/ permalink.

Articles are imported as drafts and published one by one. Until a seed article is published, the theme removes links that point to it (`fenix_unlink_slugs()` in `inc/setup.php`): an item in a `related-links` list that holds only that link disappears, and a link inside a sentence becomes plain text. So write sentences that still read correctly without the link.

On pages that go live before the articles (guides, backtest, forward-test), wrap a "read more in article X" sentence in `<span class="xref">…</span>` (or use `<p class="xref">` for a whole paragraph). The theme then hides the whole sentence while its target is still a draft, instead of leaving an unlinked title behind. `dev/check-content.php` simulates "every article is a draft" and fails if headings, tables, FAQ items or risk warnings would be lost.

## External links

- A link that leaves the site is written with the full address and must carry both attributes: `<a href="https://…" target="_blank" rel="noopener">…</a>`. `dev/check-content.php` fails a seed that has an external `<a>` without `target="_blank"` or without `noopener` in `rel`.
- Link to primary sources only: the maker of the software or the authority itself (MetaQuotes help pages for MT5, Microsoft documentation for Remote Desktop and Windows App, the official app store page, a regulator's own site). Never link to another EA seller, a signal or copy-trade service, a VPS provider, a broker (the partner broker is reached only through `[falcon_broker field="url"]`), or any other site of the same owner.
- One or two per page is enough, and the sentence around the link must still make sense if the target page moves: say what the reader finds there, and that it is in English when it is.
- Guide pages already print the official download links in the Customizer download cards (MT5, Windows App), so the long text does not need to repeat those store links.

A new article needs three things: the file in `inc/content/articles/`, its slug in `fenix_seed_article_covers()` (`inc/setup.php`), and a cover built with `dev/make-covers.php` (icon per slug in `dev/preview/cover.php`). Put the keyword phrase before a colon in the title when you can ("Drawdown คืออะไร: ..."), because the cover prints the part before the colon large and the rest as a subtitle.

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
- Do not add `<img>` tags. Step screenshots of the guide pages come from the Customizer image slots (several steps ship a default picture from `falcon-pro/assets/img/guides/`, named in the step data of `inc/modules/guides.php`; `dev/check-content.php` fails when a named file is missing), diagrams from `[falcon_figure]`, and the owner can add further pictures in the editor.
- The long text of a guide page sits under its steps, so it may refer to "ขั้นที่ 2 ด้านบน", but never to what a step picture shows: the owner can replace or remove any picture.
- Do not put a plain `<p>` directly after a table or after a callout: the two touch (paragraphs carry no top margin, tables and callouts no bottom margin). Put the paragraph before the table, or follow the table with a callout, a list, a `[falcon_line]` button or a heading.
- Do not add inline styles or scripts.

## Legal pages (privacy-policy, terms-of-use, data-deletion) and about

- Contact details and partner statements follow the Customizer: write the e-mail as `[falcon_email]` inside `[falcon_if has="email"]`, and every sentence about the partner broker inside `[falcon_if has="partner"]` with the broker named through `[falcon_broker]`. The pages must read as complete documents in all four states (with or without an e-mail, with or without a partner link). Preview them with `?email=…` and `?broker=…&server=…` (switches in `dev/preview/wp-stubs.php`).
- On every material change raise `rev` in the meta line and update the effective date and the version id in the `.doc-meta` line of the page. Adding or removing the e-mail address or the partner link on the live site changes the published text as well, so the owner should refresh that line then too (said in `readme.txt`).
- The cookie table in the privacy policy must match what Customizer section 18 actually loads (analytics and marketing ids), and the handling times on the data-deletion page must equal the retention table of the privacy policy.
- These seeds are starting drafts, not legal advice. That reminder for the owner lives in `readme.txt`, not in comments inside the files.

## Dashboard page (dashboard) and every text about the dashboard

- The designed top part of `/dashboard/` (hero, the two ways to run FALCON, screenshots, features, the five start steps) is Customizer text (section 24.1, defaults in `inc/modules/dashboard.php`). The seed holds the long text only: visit mode, the EA line-up, password handling, what stopping does, real accounts, Guardian, FAQ.
- No backtest figure and no figure taken from visit mode on this page or in any dashboard text: the balances, orders and profit and loss shown to a visitor are samples, and backtest figures live on `/backtest/` only, next to their test conditions and limitations. Mechanism facts are fine (the lot step and the number of recovery steps of a system, how many times the lot-sizing capital a mode expects in the account).
- Name the EA sets by their display names only (FALCON PRO, FALCON PLUS, FALCON ULTRA, FALCON FURY, FALCON SCOUT, FALCON HUNTER, FALCON APEX). Every text that lists the sets also says that all of them hold losing orders and do not cut losses by themselves.
- Say what the dashboard itself states, in new words: real-money accounts only, stopping the EA does not close open orders, Guardian is off until the user switches it on and is not a guarantee, the team never asks for the member password, 2FA code or verification code, the MT5 trading password is typed only into the dashboard's own form. Do not write that the hosted service is fully open, how long something takes (except what the dashboard states), where servers are, uptime figures or customer counts.
- The broker the dashboard works with is named through `[falcon_broker]` inside `[falcon_if has="partner"]` only, and the page that names it also prints `[falcon_broker field="disclosure" box="1"]`.
- A screenshot of the dashboard must not show the broker's name (crop the side menu), and one that shows sample balances or profit and loss needs a caption saying they are sample data, not trading results.

## Backtest page (backtest) and backtest figures

- Backtest figures are printed in one place on the whole site: the section of `pages/backtest.html` that starts at `<h2 id="backtest-results">`, in the tables `table.data-table.bt-table` (one per EA system, the sets named by their display names). The `id` must stay on that heading: the top card of the page (`fenix_pages_backtest_has_results()`), the note in the home page tests chapter (`fenix_home_backtest_live()`) and the check below all look for it.
- Each table holds the test conditions (symbol, period, chart, leverage, price model, starting money) in the same table as the results, and is followed by its own list of limitations (`<ul class="crosslist">`) with every warning the dashboard shows for that system, in faithful wording. The section also states the date the figures were read and ends with a `[falcon_dashboard mode="visit"]` button. Never drop or shorten a limitation.
- Money figures of a system get a currency label only when the dashboard states one. No superlatives, no return promises, no monthly return figures in running text, two decimals as in the source (no rounding that flatters).
- No backtest figure anywhere else: not in the lead, FAQ or meta description of the backtest page, not in another page seed, not in a Customizer default (home page, pricing cards, /go, dashboard texts, meta descriptions and schema read from those), not in a cover or an alt text. `dev/check-content.php` (row "backtest figures stay in tables") reads the figures from the tables and fails when one of them appears outside the section, in another page seed or in a default, when a table has no list of limitations after it, or when the section has no dashboard button.
- When the dashboard's data changes: update the cells one by one from the source, update both read dates, compare the limitation lists with the dashboard's warnings again, and raise `rev`.

## Writing rules (must follow)

1. **Write original Thai text.** Do not copy wording from other websites, including ea2000.co and every other site of the same owner (a sentence shared between two of the owner's domains is duplicate content). Use a friendly, clear, professional tone written for Thai retail traders, with short paragraphs.
2. **Never invent performance numbers.** That means no win rate, profit %, drawdown %, trade counts or "results" for FALCON PRO EA. Generic educational examples are fine when they are clearly hypothetical (e.g. "สมมติทุน 1,000 USD เสี่ยง 1% = 10 USD"), are about maths or risk, and are never about FALCON's results.
3. **Never promise or guarantee profit.** Avoid words like "รวยเร็ว", "กำไรแน่นอน", "ไม่มีขาดทุน" or "การันตี", except when warning against them.
4. **Every page and article must include risk context.** At least one `callout--warn` that says trading carries risk and can lose capital, and that past results do not guarantee future results.
5. **Do not invent facts about the business.** That covers company name, address, registration, prices, refund terms, and team names or photos. Where such a fact is needed, either keep it generic ("ทีมงาน", "โบรกเกอร์ที่รองรับ") or use the shortcodes above. For legal pages, write standard clauses and refer to the business as "[falcon_brand]" or "เรา".
   - **Broker partnership:** never type a broker's name, its server name or its links into a file under `falcon-pro/` (`dev/check-brand.php` fails on it), and never hand-write a "we are a partner of …" or "we are paid by …" sentence. The partnership is stated in one place only, the Customizer setting `broker_disclosure`, and pages show it with `[falcon_broker field="disclosure"]` (Customizer texts of the guide pages use the `{disclosure}` token). It appears by itself once the owner sets the partner signup link and stays hidden before that. Other sentences that are only true for a partner go inside `[falcon_if has="partner"]`. Inside that condition a sentence may say which broker the signup button leads to and call `[falcon_broker]` the partner broker (the terms clause and the about page do), but what the team receives from the partnership is never written by hand: the sentence points at the disclosure, and `[falcon_broker field="disclosure"]` follows on its own line.
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

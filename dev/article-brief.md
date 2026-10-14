# Brief: write one FALCON PRO EA article seed (Thai)

You write ONE new blog article for the FALCON PRO EA website (Thai, WordPress theme in `D:\EA VIDEO\falcon-pro-repo`). The owner approved an article plan on 14 Oct 2026. Other writers work on other articles at the same time: touch only your own file.

## Read first (all of them, fully)
1. `D:\EA VIDEO\falcon-pro-repo\CLAUDE.md` — product facts and house rules. The product facts paragraph near the top is binding.
2. `D:\EA VIDEO\falcon-pro-repo\dev\content-spec.md` — file format, shortcodes, components, link rules, writing rules 1–9. Follow every rule.
3. `D:\EA VIDEO\falcon-pro-repo\dev\article-plan.md` — your row (slug, draft title, primary and secondary keywords, links in, external links), section 5 (linking rules), section 7 (cautions), and the list of checked external links.
4. Two existing articles for format and tone (do NOT reuse their sentences): `falcon-pro\inc\content\articles\cent-account.html`, `falcon-pro\inc\content\articles\drawdown.html`.

## Deliverable
Create `D:\EA VIDEO\falcon-pro-repo\falcon-pro\inc\content\articles\<slug>.html` and nothing else. Do not edit any other file, do not commit.

### Meta line (first line, one line of JSON)
`<!--meta {"title":"…","seo_title":"…","description":"…","keyword":"…","excerpt":"…","category":"…","kicker":"…"} -->`
- `title`: Thai with the primary keyword BEFORE a colon (the cover prints that part large), about 70 characters at most. You may improve the plan's draft title.
- `seo_title`: 60 characters at most including the ending ` · FALCON PRO EA`.
- `description`: 120–155 characters, contains the primary keyword naturally, says what the reader gets.
- `keyword`: the primary keyword of the plan (natural Thai spelling and spacing, e.g. `บอทเทรดทอง`).
- `excerpt`: 1–2 sentences for the article card.
- `category`: the group name exactly as in content-spec (`EA และบอทเทรดทอง`, `เทรดทองคำ XAUUSD`, `บริหารความเสี่ยง`, `Backtest และผลทดสอบ`, `MT5 และบัญชีเทรด`, `ข่าวเศรษฐกิจ`).
- `kicker`: a short English label (not printed), e.g. `Guide`.

### Body
- Order: `<p class="lead">` → `<div class="callout callout--info"><p><strong>สรุปสั้น:</strong> …</p></div>` → H2 sections (with some H3) → at least one `callout--warn` with the risk context (trading can lose the whole capital, past results do not guarantee future results) → `<h2>คำถามที่พบบ่อย</h2>` + FAQ block → related-links block (`<h2>บทความและคู่มือที่เกี่ยวข้อง</h2>`) → a short closing paragraph → `[falcon_line pos="article-<slug>"]ข้อความปุ่ม[/falcon_line]` on its own line (button text = what the reader gets from the team, e.g. an answer; never "installation").
- Length: PILLAR articles (#1 #3 #4 #5 #8 #10 of the plan) 13,000–17,000 characters of visible text, up to 14 H2, 5–6 FAQ items, and they link DOWN to every article of their group (existing + planned) inside the text and in the related-links list. CLUSTER articles 8,000–11,000 characters, 6–12 H2, 3–5 FAQ, one link UP to their pillar within the first three paragraphs.
- Measure: visible characters = text with tags and shortcodes removed, whitespace collapsed (a quick node one-liner is fine).
- Use the components of content-spec only (callouts, checklist/crosslist, guide-steps, table-wrap + data-table, faq-block, related-links). Never a plain `<p>` directly after a table or a callout: put a list, heading, callout or the button there, or move the paragraph before the table. No `<img>`, no inline style, no script, no HTML comment, no `<h1>`.

### Keywords (SEO)
- Primary keyword: in the title, the lead paragraph, at least one H2, and 3–6 times in total, always in natural Thai.
- Each secondary keyword of the plan appears naturally at least once (an H2/H3, an FAQ question or a sentence). Never stuff.
- The bare word "EA" is ambiguous in Thai search (stock, games). Say "EA Forex", "EA เทรดทอง", "บอทเทรด" where the context needs it.

### Links
- Internal links: `{{home}}/<slug>/`. Allowed targets: the page slugs listed in content-spec, the 22 existing article slugs (files in `falcon-pro/inc/content/articles/`), and the planned slugs of `dev/article-plan.md` (they will exist later). Never a retired page (`how-to-install`, `vps-windows`, `vps-android`, `vps-ios`, `forward-test`).
- A link to an article that is not published yet is turned into plain text by the theme until it goes live, so every sentence with a link must still read correctly without the link. A sentence whose only job is "read more in article X" about a PLANNED article goes inside `<span class="xref">…</span>` (it disappears entirely until X is live). The related-links list may contain planned articles (an item that holds only an unpublished link disappears by itself).
- About one internal link per 150–200 words; no target twice in the body.
- External links: 1–2, only from the checked list in the plan (primary sources), written `<a href="https://…" target="_blank" rel="noopener">…</a>`; say in the sentence what the reader finds there and that the page is in English when it is.

## Product facts you must respect (from CLAUDE.md; when in doubt, leave it out)
- FALCON PRO EA trades gold XAUUSD on MT5 only. Sets: EA FALCON 1.10 (FALCON PRO, FALCON PLUS, FALCON ULTRA, FALCON FURY) and FALCON 5.0 (FALCON SCOUT, FALCON HUNTER, FALCON APEX). Every set opens recovery orders in a grid with growing Lot and does NOT cut losses by itself. Whenever you list the sets, say that too.
- Used through the member dashboard only: the EA runs on FALCON's servers. No EA file, no settings/Preset file, no installation by the customer, no computer that stays on, no VPS to rent. The MT5 app (phone or PC) is only to watch the account (and close orders by hand when the EA is stopped). Stopping the EA does not close open orders.
- Real accounts of the partner broker only (no demo accounts; Cent accounts work). The customer types the MT5 trading (master) password into the dashboard's own form himself. The team never asks for any password or code in chat. The investor password only reads.
- Price: free / paid once; running on the dashboard is included, no monthly fee. Do not print prices or the number of accounts: link to `{{home}}/pricing/`.
- Users must be 20 or older.
- No backtest figures, no profit, win-rate, drawdown or return figures of FALCON anywhere in an article: link to `{{home}}/backtest/` instead. No numbers from the dashboard's visit mode. No Forward Test topic: FALCON has no published forward results (owner decided 14 Oct 2026 not to write about it) — do not write a section about forward testing and never promise live results.
- FALCON is a rule-based EA: never call it AI.
- Never type a broker's name, server name or broker link (`dev/check-brand.php` fails). Do not use `[falcon_broker field="url"]`. Do not type the dashboard's host name; link the explanation page `{{home}}/dashboard/`. Do not use `[falcon_dashboard]` buttons in articles.
- First-time advice is "start with a small amount you can afford to lose completely, for example a Cent account". Never "test FALCON on a demo account first". General education about demo accounts elsewhere is fine but not as a FALCON step.
- No profit promises ("การันตี", "กำไรแน่นอน", "รวยเร็ว") except when warning against them. No invented statistics, testimonials or business facts.
- General knowledge must be correct and hedged where it varies by broker (contract size, server time, trading hours, symbol suffixes, stop-out level). Dates of FOMC/NFP/CPI: use only the ones in the plan.

## Lessons from the first batch
- The sister site (eawing) has about 150 articles on nearly the same topics, some with the same slug. Do not open its files; write from your own knowledge and the FALCON sources above. The copy check (path below, the theme folder `…/eawing-theme/eawing`) compares 40-character passages; stock phrases such as "คำเตือนความเสี่ยง: การเทรดทองคำ Forex และ CFD …" or "ผลในอดีตไม่ได้รับประกันผลในอนาคต ใช้เฉพาะเงินที่…" were flagged, so word the risk box and common definitions in your own way.
- Already written in this plan (link to them freely): every slug that has a file in `falcon-pro/inc/content/articles/`.
- `dev/check-content.php` accepts links to every planned slug of the plan.

## Originality
Write original Thai. Never copy wording from any website, especially ea2000.co, eawing.co or any other site of the same owner, and do not reuse sentences of the existing FALCON articles. Friendly, clear, professional tone for Thai retail traders; short paragraphs; English technical terms in normal case (the theme uppercases them).

## Checks (run from `D:\EA VIDEO\falcon-pro-repo`, Git Bash)
```
PHP="/c/Users/THANAWUT HR/AppData/Local/Microsoft/WinGet/Packages/PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe/php.exe"
EXT="/c/Users/THANAWUT HR/AppData/Local/Microsoft/WinGet/Packages/PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe/ext"
"$PHP" -d extension_dir="$EXT" -d extension=mbstring dev/check-content.php
"$PHP" -d extension_dir="$EXT" -d extension=mbstring dev/check-brand.php
"$PHP" -d extension_dir="$EXT" -d extension=mbstring dev/check-copy.php --articles "<clone of easpeciallab-max/eawing-theme>/eawing"
```
All three must be clean for YOUR file (check-content "all good", check-brand "no brand leaks", check-copy: no NEW passage from your file). If another writer's file makes a check fail, ignore that one and say so. Fix your own file until it passes.

## Report back (short)
File path · visible characters · H2 count · FAQ count · where the primary keyword appears · internal and external links used · check results · anything you were unsure about (facts you left out on purpose).

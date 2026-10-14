# แผนบทความ FALCON PRO EA · Pillar และ Cluster · วันละ 1 เรื่อง

เจ้าของอนุมัติ 14 ต.ค. 2026 (ปกใหม่ไม่มีป้ายหมวด · ไม่เขียนเรื่อง Forward Test) · ช่วงที่วางไว้: จันทร์ 19 ต.ค. ถึงเสาร์ 12 ธ.ค. 2026 (55 วัน)

- บทความใหม่ 41 เรื่อง + บทความเดิมที่ตั้งเวลาไว้แล้ว 14 เรื่อง (อังคาร/ศุกร์ เดิม ไม่ย้าย) = ออกวันละ 1 เรื่องครบทุกวัน 10:00 น.
- บทความเดิมที่เผยแพร่แล้ว 7 เรื่องและเรื่องที่ออก 16 ต.ค. ถูกจัดเข้ากลุ่มด้วย รวมทั้งหมด 63 เรื่องใน 6 กลุ่ม
- ทุกเรื่องเขียนตาม `dev/content-spec.md` และกติกาใน `CLAUDE.md` (ข้อเท็จจริงของสินค้าจากแดชบอร์ด ไม่มีตัวเลขผลทดสอบนอกหน้า /backtest/ ไม่สัญญากำไร ไม่ลดทอนคำเตือน)

## 1. คำค้นมาจากไหน

- ดึงคำแนะนำการค้นหาของ Google ภาษาไทย (hl=th, gl=th) ราว 1,380 ครั้ง จากคำตั้งต้นราว 220 คำ × คำต่อท้าย (คือ ดีไหม ยังไง วิธี pantip ไหน เท่าไหร่ ทำไม) และไล่ตัวอักษร ก–ฮ ของคำหลัก ได้วลีไม่ซ้ำ 3,917 วลี
- **ไม่มีตัวเลขปริมาณค้นหาจริง** (ต้องใช้ Google Keyword Planner ที่มีบัญชีโฆษณา) จึงใช้คะแนนแทน: วลีที่ Google เสนอจากหลายคำตั้งต้นและอยู่อันดับต้นของรายการได้คะแนนสูง คะแนนบอกว่า "มีคนค้นจริงและค้นบ่อยกว่ากัน" แต่บอกจำนวนครั้งไม่ได้
- หลังบทความออก 4 ถึง 6 สัปดาห์ ให้ดู Search Console (คำที่ได้ impression จริง) แล้วปรับชื่อเรื่องและลำดับที่เหลือ

คำที่คะแนนสูงสุด (คะแนนในวงเล็บ): backtest mt5 ยังไง (62) · mt4 mt5 คือ (56) · mt5 คือ (55) · ea เทรดทอง ดีไหม (53) · บอทเทรด forex pantip (50) · ea เทรดทอง pantip (48) · margin call คือ (47) · forex คือ (46) · ea เทรดทอง ตัวไหนดี (44) · forward test คือ (44) · money management คือ (43) · ea forex คือ (43) · grid trading คือ (42) · mt5 มือถือ (42) · ea เทรดทองที่ดีที่สุด (41) · ข่าวกล่องแดง forex คือ (38) · xauusd คือ (36) · ea โกงยังไง (35) · fomc คือ (33) · xauusd นับจุดยังไง (33) · พอร์ตแตก/ล้างพอร์ต คือ (33) · cpi คือ (32) · copy trade คือ (32) · non farm payroll คือ (29)

สิ่งที่ได้จากข้อมูล:
- **คำว่า "EA" เดี่ยว ๆ กำกวม**: Google เติมเป็นหุ้น EA (พลังงานบริสุทธิ์) เกม EA และวิชาเคมี ชื่อเรื่องและคีย์จึงใช้ "EA Forex" "EA เทรดทอง" "บอทเทรด" แทน
- คนไทยค้นคู่กับ **pantip** เยอะ (อยากรู้ประสบการณ์จริง) ตอบด้วยเช็กลิสต์ที่ตรวจได้และความเสี่ยงที่ตรงไปตรงมา ห้ามแต่งรีวิว
- หน้าแรกของ Google ภาษาไทยในหัวข้อทองและ Forex เป็นบล็อกของโบรกเกอร์เกือบทั้งหมด ช่องว่างที่ชนะได้คือ ความตรงไปตรงมาเรื่องความเสี่ยง เวลาไทย หน่วยเป็นบาท และ FAQ ที่ตอบคำถามแบบ pantip
- คำที่เกี่ยวกับสินค้าตรง ๆ มีจริง: ea มือถือ · ea รันบนมือถือ · ea ไม่ต้องเช่า vps ไหม · ea ไม่มี stop loss · copy trade กับ ea ต่างกันอย่างไร · บอทเทรดทอง ขาดทุนเพราะอะไร

## 2. โครง 6 กลุ่ม (บทหลัก → บทเสริม → หน้าเว็บที่ต้องพาไป)

| กลุ่ม | บทหลัก (Pillar) | บทเสริมใหม่ | บทความเดิมที่เข้ากลุ่ม | หน้าเว็บปลายทาง |
|---|---|---|---|---|
| A · EA และบอทเทรดทอง | #1 บอทเทรดทอง (19 ต.ค.) | #2 #9 #18 #21 #23 #26 #27 #28 #29 #35 #38 #40 | what-is-ea-mt5, ea-not-trading, ea-scam-warning, free-ea-vs-paid, grid-martingale-ea, ea-monitoring-routine | /dashboard/ /pricing/ /backtest/ /risk-disclosure/ /go/ |
| B · เทรดทองคำ XAUUSD | #10 XAUUSD คืออะไร (1 พ.ย.) | #11 #13 #20 #30 #33 #34 | ea-gold-xauusd, spread-slippage, swap-commission | /backtest/ /tools/ |
| C · บริหารความเสี่ยง | #5 Money Management (25 ต.ค.) | #7 #16 #17 #25 #32 | drawdown, lot-size-calculation, leverage, margin-call-stop-out, cent-account | /tools/ /risk-disclosure/ |
| D · Backtest และผลทดสอบ | #3 Backtest คืออะไร (22 ต.ค.) | #14 #24 | profit-factor, ea-optimization-overfitting | /backtest/ |
| E · MT5 และบัญชีเทรด | #8 MT5 คืออะไร (29 ต.ค.) | #19 #22 #31 #37 #39 #41 | mt4-vs-mt5, choose-broker-for-ea, demo-account-ea, vps-for-ea, vps-mt5-stable | /mt5-login/ /open-mt5-account/ /deposit/ /dashboard/ |
| F · ข่าวเศรษฐกิจ | #4 ข่าวกล่องแดง (24 ต.ค.) | #6 #12 #15 #36 | ea-news-trading | /risk-disclosure/ |

บทหลักออกก่อนบทเสริมของกลุ่มเสมอ และออกก่อนบทความเดิมที่อยู่ในกลุ่มเดียวกัน (เช่น Backtest คืออะไร 22 ต.ค. ก่อน profit-factor 23 ต.ค.)

## 3. ปฏิทิน (บทใหม่ = #, บทเดิมที่ตั้งเวลาไว้ = เดิม)

| วันที่ | บทความ | กลุ่ม | ประเภท |
|---|---|---|---|
| จ. 19 ต.ค. | #1 บอทเทรดทองคืออะไร ดีไหม | A | **บทหลัก** |
| อ. 20 ต.ค. | เดิม · vps-mt5-stable | E | เดิม |
| พ. 21 ต.ค. | #2 ใช้ EA บนมือถือได้ไหม | A | เสริม |
| พฤ. 22 ต.ค. | #3 Backtest คืออะไร | D | **บทหลัก** |
| ศ. 23 ต.ค. | เดิม · profit-factor | D | เดิม |
| ส. 24 ต.ค. | #4 ข่าวกล่องแดง Forex คืออะไร | F | **บทหลัก** |
| อา. 25 ต.ค. | #5 Money Management คืออะไร | C | **บทหลัก** |
| จ. 26 ต.ค. | #6 FOMC คืออะไร (ก่อนประชุม Fed 27–28 ต.ค.) | F | เสริม |
| อ. 27 ต.ค. | เดิม · margin-call-stop-out | C | เดิม |
| พ. 28 ต.ค. | #7 Balance Equity Margin Level บน MT5 | C | เสริม |
| พฤ. 29 ต.ค. | #8 MT5 คืออะไร | E | **บทหลัก** |
| ศ. 30 ต.ค. | เดิม · choose-broker-for-ea | E | เดิม |
| ส. 31 ต.ค. | #9 วิธีใช้ EA เทรดทองสำหรับมือใหม่ | A | เสริม (พาไปสมัคร) |
| อา. 1 พ.ย. | #10 XAUUSD คืออะไร | B | **บทหลัก** |
| จ. 2 พ.ย. | #11 ตลาดทองเปิดกี่โมง (สหรัฐเลิกเวลาออมแสง 1 พ.ย.) | B | เสริม |
| อ. 3 พ.ย. | เดิม · ea-gold-xauusd | B | เดิม |
| พ. 4 พ.ย. | #12 Non-Farm Payroll (ประกาศ 6 พ.ย.) | F | เสริม |
| พฤ. 5 พ.ย. | #13 XAUUSD นับจุดยังไง 1 Lot ทองกี่บาท | B | เสริม |
| ศ. 6 พ.ย. | เดิม · ea-optimization-overfitting | D | เดิม |
| ส. 7 พ.ย. | #14 วิธี Backtest บน MT5 | D | เสริม |
| อา. 8 พ.ย. | #15 CPI คืออะไร (ประกาศ 10 พ.ย.) | F | เสริม |
| จ. 9 พ.ย. | #16 พอร์ตแตกคืออะไร เป็นหนี้ไหม | C | เสริม |
| อ. 10 พ.ย. | เดิม · leverage | C | เดิม |
| พ. 11 พ.ย. | #17 EA ไม่มี Stop Loss | C | เสริม |
| พฤ. 12 พ.ย. | #18 บอทเทรดทองขาดทุนเพราะอะไร | A | เสริม |
| ศ. 13 พ.ย. | เดิม · mt4-vs-mt5 | E | เดิม |
| ส. 14 พ.ย. | #19 ล็อกอิน MT5 ไม่ได้ การอนุญาตล้มเหลว | E | เสริม |
| อา. 15 พ.ย. | #20 ทองขึ้น/ลงเพราะอะไร | B | เสริม |
| จ. 16 พ.ย. | #21 AI เทรดทอง ใช้ ChatGPT ได้ไหม | A | เสริม |
| อ. 17 พ.ย. | เดิม · spread-slippage | B | เดิม |
| พ. 18 พ.ย. | #22 Investor Password คืออะไร | E | เสริม |
| พฤ. 19 พ.ย. | #23 Copy Trade กับ EA ต่างกันอย่างไร | A | เสริม |
| ศ. 20 พ.ย. | เดิม · ea-monitoring-routine | A | เดิม |
| ส. 21 พ.ย. | #24 Win Rate คืออะไร | D | เสริม |
| อา. 22 พ.ย. | #25 ใช้ EA ต้องมีทุนเท่าไหร่ | C | เสริม |
| จ. 23 พ.ย. | #26 Grid Trading คืออะไร | A | เสริม |
| อ. 24 พ.ย. | เดิม · grid-martingale-ea | A | เดิม |
| พ. 25 พ.ย. | #27 Hedging คืออะไร | A | เสริม |
| พฤ. 26 พ.ย. | #28 EA เทรดทองตัวไหนดี | A | เสริม |
| ศ. 27 พ.ย. | เดิม · free-ea-vs-paid | A | เดิม |
| ส. 28 พ.ย. | #29 ซื้อ EA ที่ไหนดี ซื้อขาดหรือเช่า | A | เสริม (พาไปราคา) |
| อา. 29 พ.ย. | #30 ทองแท่งกับ XAUUSD ต่างกันอย่างไร | B | เสริม |
| จ. 30 พ.ย. | #31 ถอนเงินจาก MT5 ยังไง | E | เสริม |
| อ. 1 ธ.ค. | เดิม · swap-commission | B | เดิม |
| พ. 2 ธ.ค. | #32 Gap ทองคืออะไร | C | เสริม |
| พฤ. 3 ธ.ค. | #33 ราคาทองแต่ละโบรกเกอร์ไม่เท่ากัน โกงกราฟจริงไหม | B | เสริม |
| ศ. 4 ธ.ค. | เดิม · ea-news-trading (วันเดียวกับ NFP) | F | เดิม |
| ส. 5 ธ.ค. | #34 XAUUSD กับ XAUUSDm XAUUSDc | B | เสริม |
| อา. 6 ธ.ค. | #35 EA Scalping ทองกับ EA Grid | A | เสริม |
| จ. 7 ธ.ค. | #36 DXY คืออะไร (ก่อนประชุม Fed 8–9 ธ.ค.) | F | เสริม |
| อ. 8 ธ.ค. | #37 บัญชี Standard Cent Raw ต่างกันอย่างไร | E | เสริม |
| พ. 9 ธ.ค. | #38 วิธีเช็กมิจฉาชีพชวนลงทุนเทรดทอง | A | เสริม |
| พฤ. 10 ธ.ค. | #39 เปิดบัญชีเทรดต้องอายุเท่าไหร่ ใช้เอกสารอะไร | E | เสริม |
| ศ. 11 ธ.ค. | #40 EA เป็น Passive Income ได้จริงไหม | A | เสริม |
| ส. 12 ธ.ค. | #41 ดูประวัติการเทรดบน MT5 มือถือ | E | เสริม |

วันประกาศข่าวที่ใช้จัดลำดับ (จากเว็บทางการ อ่าน 14 ต.ค. 2026): FOMC 27–28 ต.ค. และ 8–9 ธ.ค. 2026 (ผลออก 14:00 น. เวลานิวยอร์ก = ตี 1 ของ 29 ต.ค. และตี 2 ของ 10 ธ.ค. เวลาไทย) · ปี 2027: 26–27 ม.ค., 16–17 มี.ค., 27–28 เม.ย., 8–9 มิ.ย., 27–28 ก.ค., 14–15 ก.ย., 26–27 ต.ค., 7–8 ธ.ค. · Non-Farm 6 พ.ย., 4 ธ.ค. 2026, 8 ม.ค. 2027 · CPI 10 พ.ย., 10 ธ.ค. 2026, 13 ม.ค. 2027 (ทั้งสองตัว 08:30 น. นิวยอร์ก = 20:30 น. เวลาไทยหลัง 1 พ.ย.)

## 4. รายละเอียดรายเรื่อง

รูปแบบช่องลิงก์ใน: **ขึ้น** = บทหลักของกลุ่ม · **ข้าง** = บทพี่น้อง · **หน้า** = หน้าเว็บปลายทาง ลิงก์นอกใส่ 1–2 ลิงก์ต่อเรื่อง เฉพาะแหล่งต้นทาง (ผู้ทำซอฟต์แวร์ หน่วยงานรัฐ ผู้ดูแลข้อมูล) ตามข้อ "External links" ของ content-spec ทุกลิงก์เปิดได้จริงเมื่อ 14 ต.ค. 2026

### กลุ่ม A · EA และบอทเทรดทอง

| # | slug ที่เสนอ · ชื่อร่าง | คีย์หลัก | คีย์รอง | ลิงก์ใน | ลิงก์นอก |
|---|---|---|---|---|---|
| 1 | `gold-trading-bot` · บอทเทรดทองคืออะไร ดีไหม: คู่มือ EA เทรดทองฉบับเต็มก่อนตัดสินใจใช้ | บอทเทรดทอง | ea เทรดทอง ดีไหม · ea ทองคำ · บอทเทรดทองดีไหม · ea เทรดทอง คืออะไร · ea xauusd mt5 · บอทเทรด forex | **บทหลัก**: ลงไปทุกบทในกลุ่ม A + what-is-ea-mt5, ea-scam-warning, free-ea-vs-paid, grid-martingale-ea, ea-monitoring-routine · ข้ามกลุ่ม: #3 #5 #10 · หน้า: /dashboard/ /backtest/ /pricing/ /risk-disclosure/ | คำเตือนของ CFTC เรื่องบอทเทรดอ้าง AI (อังกฤษ) · คู่มือ Algo Trading ของ MetaQuotes |
| 2 | `ea-on-mobile` · ใช้ EA บนมือถือได้ไหม: รันบอทเทรดโดยไม่ต้องเปิดคอมและไม่ต้องเช่า VPS | ea มือถือ | ea รันบนมือถือ · ea mt5 มือถือ · ea ไม่ต้องเปิดคอม · ea ไม่ต้องเช่า vps ไหม · บอทเทรด mt5 มือถือ · ea forex มือถือ | ขึ้น: #1 · ข้าง: vps-for-ea, vps-mt5-stable, #8 · หน้า: **/dashboard/** /mt5-login/ | คู่มือ MT5 บน iPhone/Android ของ MetaQuotes |
| 9 | `how-to-use-gold-ea` · วิธีใช้ EA เทรดทองสำหรับมือใหม่: เริ่มจากศูนย์จนระบบทำงาน | วิธีใช้ ea เทรด | ใช้ ea เทรด forex ต้องทำยังไง · บอทเทรดทอง วิธีใช้ · ใช้ ea เทรดทอง · ea forex วิธีใช้ | ขึ้น: #1 · ข้าง: #2 #25 ea-monitoring-routine · หน้า: **/go/** /open-mt5-account/ /deposit/ /mt5-login/ /dashboard/ | หน้า Autotrading ของ MetaQuotes (อธิบายวิธีทั่วไป) |
| 18 | `gold-bot-losses` · บอทเทรดทองขาดทุนเพราะอะไร: ได้เงินจริงไหม และสัญญาณที่ควรหยุดคิด | บอทเทรดทอง ขาดทุน | บอทเทรดทองขาดทุนเพราะอะไร · บอทเทรด ได้เงินจริงไหม · ea forex ได้เงินจริงไหม · เทรดทองได้เงินจริงไหม · ea ขาดทุน | ขึ้น: #1 · ข้าง: #17 #16 #24 spread-slippage, ea-news-trading · หน้า: /risk-disclosure/ /backtest/ | ประกาศ ESMA (สัดส่วนบัญชีรายย่อยที่ขาดทุนใน CFD) · CFTC |
| 21 | `ai-gold-trading` · AI เทรดทองคืออะไร: ใช้ ChatGPT เทรดทองได้ไหม และต่างจาก EA อย่างไร | ai เทรดทอง | ใช้ chatgpt เทรดทอง · ai ช่วยเทรดทอง · ai เทรด forex · ai เทรดทอง ฟรี | ขึ้น: #1 · ข้าง: what-is-ea-mt5, ea-scam-warning, #23 #38 · หน้า: /backtest/ | CFTC "AI Won't Turn Trading Bots into Money Machines" |
| 23 | `copy-trade-vs-ea` · Copy Trade กับ EA ต่างกันอย่างไร: เลือกแบบไหนเมื่ออยากให้ระบบเทรดแทน | copy trade กับ ea ต่างกันอย่างไร | copy trade คือ · copy trade ทอง · สัญญาณเทรดทอง · copy trade ดีไหม | ขึ้น: #1 · ข้าง: #21 #29 ea-scam-warning · หน้า: /dashboard/ | ESMA Supervisory Briefing on Copy Trading |
| 26 | `grid-trading` · Grid Trading คืออะไร: ระยะกริด จำนวนชั้น และทุนที่ต้องเผื่อเมื่อราคาวิ่งทางเดียว | grid trading คือ | ea grid คือ · grid trading strategy · ea grid mt5 · grid trading ดีไหม | ขึ้น: #1 · ข้าง: grid-martingale-ea (ออกวันถัดไป) #27 #35 #17 · หน้า: /backtest/ /risk-disclosure/ | ไม่จำเป็น |
| 27 | `hedging` · Hedging คืออะไร: EA ที่เปิด Buy และ Sell พร้อมกันทำงานอย่างไร | hedging forex คือ | hedging คือ · hedging forex ea · hedging strategy คือ · บัญชี hedging กับ netting | ขึ้น: #1 · ข้าง: #26 grid-martingale-ea, swap-commission | หน้า General Concepts ของ MetaQuotes (Hedging/Netting) |
| 28 | `choose-gold-ea` · EA เทรดทองตัวไหนดี: 10 คำถามที่ต้องได้คำตอบก่อนเลือก | ea เทรดทอง ตัวไหนดี | ea เทรดทองที่ดีที่สุด · ea forex ตัวไหนดี · ea forex แนะนำ · ea xauusd ดีไหม | ขึ้น: #1 · ข้าง: ea-scam-warning, free-ea-vs-paid, #24 #29 #3 · หน้า: /backtest/ /pricing/ | ไม่จำเป็น |
| 29 | `buy-or-rent-ea` · ซื้อ EA ที่ไหนดี: ซื้อขาด เช่ารายเดือน หรือจ่ายครั้งเดียวพร้อมระบบรัน | ซื้อ ea ที่ไหนดี | เช่า ea · ea forex ราคา · ea ราคาเท่าไหร่ · ขาย ea forex | ขึ้น: #1 · ข้าง: free-ea-vs-paid, #28, vps-for-ea · หน้า: **/pricing/** /dashboard/ | ไม่จำเป็น |
| 35 | `ea-scalping-vs-grid` · EA Scalping ทองคืออะไร: ต่างจาก EA Grid อย่างไร และแบบไหนไวต่อ Spread | ea scalping ทอง | scalping คือ · เทรดทอง scalping · ea scalping gold mt5 · ea xauusd m1 | ขึ้น: #1 · ข้าง: #26 spread-slippage #28 · หน้า: /backtest/ | ไม่จำเป็น |
| 38 | `investment-scam-check` · วิธีเช็กมิจฉาชีพชวนลงทุนเทรดทอง: แอปปลอม เพจปลอม และช่องทางแจ้งความ | forex หลอกลวง | โดนโกง forex · แอปเทรดปลอม · เพจปลอมชวนลงทุน · เช็กก่อนลงทุน | ขึ้น: #1 · ข้าง: ea-scam-warning, #22 #33 #21 · หน้า: /about/ | ศูนย์รับแจ้งความออนไลน์ของตำรวจ (thaipoliceonline.go.th) · แอป SEC Check First ของ ก.ล.ต. |
| 40 | `ea-passive-income` · EA เป็น Passive Income ได้จริงไหม: งานที่ยังต้องทำและความเสี่ยงที่ต้องยอมรับ | passive income forex | passive income forex คือ · ea forex กำไร · รายได้จาก ea | ขึ้น: #1 · ข้าง: ea-monitoring-routine, #18 #25 · หน้า: /risk-disclosure/ /dashboard/ | ประกาศ ESMA |

### กลุ่ม B · เทรดทองคำ XAUUSD

| # | slug ที่เสนอ · ชื่อร่าง | คีย์หลัก | คีย์รอง | ลิงก์ใน | ลิงก์นอก |
|---|---|---|---|---|---|
| 10 | `what-is-xauusd` · XAUUSD คืออะไร: คู่มือเทรดทองคำออนไลน์สำหรับมือใหม่ | xauusd คือ | เทรดทองคำ มือใหม่ · เทรด xauusd คือ · ทองคำ cfd คือ · xauusd คือ forex ไหม · เทรดทอง ยังไง | **บทหลัก**: ลงไป #11 #13 #20 #30 #33 #34 ea-gold-xauusd, spread-slippage, swap-commission · ข้ามกลุ่ม: #1 #5 · หน้า: /backtest/ /tools/ | LBMA (ราคาอ้างอิงทองโลก) · World Gold Council Gold Demand Trends |
| 11 | `gold-market-hours` · ตลาดทองเปิดกี่โมง: เวลาเทรด XAUUSD ตามเวลาไทย ช่วงไหนผันผวน วันไหนปิด | ตลาดทองเปิดกี่โมง | xauusd ปิดกี่โมง · เทรดทองเสาร์อาทิตย์ได้ไหม · xauusd ปิดวันไหน · เทรดทอง เวลาไหนดีที่สุด · session forex ไทย | ขึ้น: #10 · ข้าง: #32 #4 ea-news-trading | LBMA (เวลากำหนดราคาลอนดอน) |
| 13 | `xauusd-pip-lot` · XAUUSD นับจุดยังไง: Pip Point ของทอง และ 1 Lot ทองเท่ากับกี่บาท | xauusd นับจุดยังไง | 1 lot ทองเท่ากับกี่บาท · 0.01 lot ทองเท่ากับกี่บาท · pip ทอง · 1 pip ทองเท่ากับกี่จุด · เทรดทอง 1000 จุด ได้กี่บาท · lot ทอง คือ | ขึ้น: #10 · ข้าง: lot-size-calculation, #34 #25 · หน้า: **/tools/** | General Concepts ของ MetaQuotes (ขนาดสัญญา) |
| 20 | `gold-price-drivers` · ทองขึ้นเพราะอะไร ทองลงเพราะอะไร: ปัจจัยหลักที่ขับราคา XAUUSD | ราคาทองขึ้นลงเพราะอะไร | ทองขึ้นเพราะอะไร · ทองลงเพราะอะไร · ดอกเบี้ย fed มีผลอย่างไร · ทองคำ สินทรัพย์ปลอดภัย | ขึ้น: #10 · ข้าง: #6 #15 #36 #12 · ข้ามกลุ่ม: #4 | World Gold Council Q2 2026 · ปฏิทิน FOMC ของ Fed |
| 30 | `gold-bar-vs-xauusd` · ทองแท่งกับ XAUUSD ต่างกันอย่างไร: ซื้อทองจริงกับเทรดทองออนไลน์ | ทองแท่งกับ xauusd ต่างกันอย่างไร | เทรดทองกับซื้อทองต่างกันยังไง · xauusd กับ gold ต่างกันอย่างไร · ทองคำ cfd คือ | ขึ้น: #10 · ข้าง: #13 leverage, swap-commission | สมาคมค้าทองคำ (ราคาทองไทย) · LBMA |
| 33 | `broker-price-difference` · ราคาทองแต่ละโบรกเกอร์ไม่เท่ากันเพราะอะไร: "โกงกราฟ" จริงไหม | forex โกงกราฟ | forex โกงยังไง · ราคาทองแต่ละโบรกไม่เท่ากัน · โบรกเกอร์โกงกราฟ | ขึ้น: #10 · ข้าง: spread-slippage, choose-broker-for-ea, ea-scam-warning, #38 | ไม่จำเป็น |
| 34 | `xauusd-symbols` · XAUUSD กับ XAUUSDm XAUUSDc ต่างกันอย่างไร และบัญชี Cent เทรดทองได้ไหม | xauusd กับ xauusdm ต่างกันอย่างไร | xauusdm คือ · xauusdc · บัญชี cent เทรดทองได้ไหม | ขึ้น: #10 · ข้าง: cent-account, #37 #13 · หน้า: /open-mt5-account/ | General Concepts ของ MetaQuotes (สเปกสัญลักษณ์) |

### กลุ่ม C · บริหารความเสี่ยง

| # | slug ที่เสนอ · ชื่อร่าง | คีย์หลัก | คีย์รอง | ลิงก์ใน | ลิงก์นอก |
|---|---|---|---|---|---|
| 5 | `money-management` · Money Management คืออะไร: บริหารเงินและความเสี่ยงเมื่อใช้ EA เทรดทอง | money management คือ | บริหารความเสี่ยง forex · money management forex · การบริหารความเสี่ยง forex | **บทหลัก**: ลงไป drawdown, lot-size-calculation, leverage, margin-call-stop-out, cent-account, #7 #16 #17 #25 #32 · ข้ามกลุ่ม: #1 · หน้า: /tools/ /risk-disclosure/ | ประกาศ ESMA |
| 7 | `margin-level` · Balance Equity Margin Free Margin และ Margin Level บน MT5 แต่ละค่าบอกอะไร | margin level คือ | balance equity คือ · free margin คือ · margin level ควรเหลือเท่าไหร่ · margin level เท่าไหร่ล้างพอร์ต | ขึ้น: #5 · ข้าง: margin-call-stop-out (ออกวันก่อน), leverage, #16 #41 · หน้า: /mt5-login/ /dashboard/ | หน้า Margin ของ MetaQuotes |
| 16 | `blown-account` · พอร์ตแตกคืออะไร: เกิดจากอะไร เป็นหนี้ไหม และเงินไปไหน | พอร์ตแตก คือ | ล้างพอร์ต คือ · พอร์ตแตกเป็นหนี้ไหม · พอร์ตแตกแล้วเงินไปไหน · เทรดทองพอร์ตแตก · negative balance protection | ขึ้น: #5 · ข้าง: margin-call-stop-out, #7 #17 drawdown · หน้า: /risk-disclosure/ | ประกาศ ESMA (Negative balance protection) |
| 17 | `ea-no-stop-loss` · EA ไม่มี Stop Loss: เสี่ยงแค่ไหน และจำกัดความเสียหายได้ด้วยอะไร | ea ไม่มี stop loss | stop loss คือ · stop loss ทอง คือ · ไม่ตั้ง stop loss · take profit คือ | ขึ้น: #5 · ข้าง: grid-martingale-ea, #26 #16 #25 cent-account · หน้า: /risk-disclosure/ /backtest/ | General Concepts ของ MetaQuotes (ประเภทคำสั่ง) |
| 25 | `ea-capital` · ใช้ EA เทรดทองต้องมีทุนเท่าไหร่: คิดจาก Lot ระยะกริด และ Drawdown ที่รับไหว | ea ทุนเท่าไหร่ดี | เทรดทองทุนน้อย · ea forex ทุนน้อย · ทุน 100 เหรียญ · เทรดทอง 1000 บาท · เทรดทองขั้นต่ำเท่าไหร่ | ขึ้น: #5 · ข้าง: cent-account, lot-size-calculation, #13 #17 · หน้า: /tools/ /dashboard/ | ไม่จำเป็น |
| 32 | `gold-gap` · Gap ทองคืออะไร: ทำไมราคากระโดดตอนเปิดตลาดวันจันทร์ และ EA ต้องเผื่ออะไร | gap ทอง คือ | gap ทองคำ · ราคาทองกระโดด · ตลาดทองเปิดวันจันทร์ | ขึ้น: #5 · ข้าง: #11 #17 spread-slippage, ea-news-trading · หน้า: /risk-disclosure/ | ไม่จำเป็น |

### กลุ่ม D · Backtest และผลทดสอบ

| # | slug ที่เสนอ · ชื่อร่าง | คีย์หลัก | คีย์รอง | ลิงก์ใน | ลิงก์นอก |
|---|---|---|---|---|---|
| 3 | `what-is-backtest` · Backtest คืออะไร: อ่านผลทดสอบย้อนหลังของ EA ให้เป็นและรู้ข้อจำกัด | backtest คือ | backtest คืออะไร · backtest forex · strategy tester คืออะไร · backtest ea | **บทหลัก**: ลงไป #14 #24 profit-factor, ea-optimization-overfitting, spread-slippage · ข้ามกลุ่ม: #1 · หน้า: **/backtest/** (ตัวอย่างจริงของ FALCON) | หน้า Testing Report ของ MetaQuotes |
| 14 | `backtest-mt5-how-to` · วิธี Backtest EA บน MT5 ด้วย Strategy Tester ทีละขั้น (และทำไมทำบนมือถือไม่ได้) | backtest mt5 ยังไง | วิธี backtest mt5 · strategy tester mt5 · backtest ea mt5 · backtest mt5 มือถือ · backtest mt5 ไม่ได้ | ขึ้น: #3 · ข้าง: ea-optimization-overfitting (ออกวันก่อน), profit-factor, #24 · หน้า: /backtest/ | หน้า Strategy Tester และ Tick Generation ของ MetaQuotes |
| 24 | `win-rate` · Win Rate คืออะไร: ทำไม EA ชนะเกือบทุกไม้แต่ยังพอร์ตแตกได้ | win rate คือ | win rate 100 คือ · risk reward คือ · อัตราชนะ | ขึ้น: #3 · ข้าง: profit-factor, drawdown, grid-martingale-ea, #18 · หน้า: /backtest/ | หน้า Testing Report ของ MetaQuotes |

### กลุ่ม E · MT5 และบัญชีเทรด

| # | slug ที่เสนอ · ชื่อร่าง | คีย์หลัก | คีย์รอง | ลิงก์ใน | ลิงก์นอก |
|---|---|---|---|---|---|
| 8 | `what-is-mt5` · MT5 คืออะไร: รู้จัก MetaTrader 5 แอปที่ใช้ดูบัญชีเทรดและการทำงานของ EA | mt5 คือ | mt5 คือแอพอะไร · metatrader 5 คือ · mt5 มือถือ · mt5 วิธีใช้ · mt5 download pc | **บทหลัก**: ลงไป mt4-vs-mt5, #19 #22 #31 #37 #39 #41 choose-broker-for-ea, demo-account-ea, vps-for-ea · ข้ามกลุ่ม: #2 · หน้า: /mt5-login/ /open-mt5-account/ /deposit/ /dashboard/ | คู่มือ MT5 iPhone/Android ของ MetaQuotes |
| 19 | `mt5-login-failed` · ล็อกอิน MT5 ไม่ได้ ขึ้น "การอนุญาตล้มเหลว": สาเหตุและวิธีแก้ทีละจุด | mt5 การอนุญาตล้มเหลว | mt5 login ไม่ได้ · mt5 เข้าไม่ได้ · mt5 รหัสผ่านไม่ถูกต้อง · mt5 ลืมรหัสผ่าน | ขึ้น: #8 · ข้าง: #22 ea-not-trading · หน้า: **/mt5-login/** /open-mt5-account/ | หน้า Authorization ของ MetaQuotes |
| 22 | `investor-password` · Investor Password คืออะไร: ต่างจากรหัสผ่านเทรดอย่างไร และทำไม MT5 ไม่มีปุ่มซื้อขาย | investor password คือ | investor password mt5 · รหัสผ่าน mt5 · mt5 ไม่มีปุ่มซื้อขาย · mt5 ปุ่มซื้อขายหาย | ขึ้น: #8 · ข้าง: #19 #38 #41 · หน้า: /dashboard/ /mt5-login/ | หน้า Opening an Account ของ MetaQuotes |
| 31 | `mt5-withdraw` · ถอนเงินจาก MT5 ยังไง: ถอนที่ไหน ใช้เวลากี่วัน และทำไมถอนไม่ได้ | ถอนเงิน mt5 ยังไง | ถอนเงิน mt5 ไม่ได้ · ถอนเงิน forex กี่วัน · ฝากเงิน mt5 ยังไง · mt5 ฝากขั้นต่ำเท่าไหร่ | ขึ้น: #8 · ข้าง: #39 #7 choose-broker-for-ea · หน้า: **/deposit/** /dashboard/ | ไม่จำเป็น (ห้ามลิงก์หน้าโบรกเกอร์) |
| 37 | `trading-account-types` · บัญชีเทรด Standard Cent Raw Spread ต่างกันอย่างไร: เลือกแบบไหนเมื่อใช้ EA | บัญชี cent กับ standard ต่างกันอย่างไร | บัญชี raw spread คือ · ประเภทบัญชี forex · บัญชี zero spread ดีไหม | ขึ้น: #8 · ข้าง: cent-account, #34 swap-commission, choose-broker-for-ea · หน้า: /open-mt5-account/ | ไม่จำเป็น |
| 39 | `open-account-requirements` · เปิดบัญชีเทรดต้องอายุเท่าไหร่ ใช้เอกสารอะไร: เตรียมยืนยันตัวตนให้ผ่านในรอบเดียว | เปิดบัญชี forex อายุเท่าไหร่ | วิธีเปิดบัญชี forex · สมัคร mt5 ยังไง · mt5 วิธีสมัคร | ขึ้น: #8 · ข้าง: #37 #31 choose-broker-for-ea · หน้า: **/open-mt5-account/** /go/ | ไม่จำเป็น |
| 41 | `mt5-trade-history` · ดูประวัติการเทรดบน MT5 มือถือ: เช็กออเดอร์ของ EA กำไรขาดทุน และ Swap ย้อนหลัง | mt5 ดูประวัติ | mt5 ดูประวัติการซื้อ · ประวัติการเทรด mt5 · mt5 ดูประวัติการโอนเงิน | ขึ้น: #8 · ข้าง: #7 ea-monitoring-routine, swap-commission · หน้า: /dashboard/ /mt5-login/ | คู่มือ MT5 iPhone/Android ของ MetaQuotes |

### กลุ่ม F · ข่าวเศรษฐกิจ

| # | slug ที่เสนอ · ชื่อร่าง | คีย์หลัก | คีย์รอง | ลิงก์ใน | ลิงก์นอก |
|---|---|---|---|---|---|
| 4 | `economic-calendar` · ข่าวกล่องแดง Forex คืออะไร: อ่านปฏิทินเศรษฐกิจให้รู้ว่าข่าวไหนเขย่าราคาทอง | ข่าวกล่องแดง forex | ปฏิทินเศรษฐกิจ ทอง · ข่าว forex วันนี้ · forex factory ภาษาไทย · forex factory เวลาไทย | **บทหลัก**: ลงไป #6 #12 #15 #36 ea-news-trading · ข้ามกลุ่ม: #20 #32 · หน้า: /risk-disclosure/ | ปฏิทิน FOMC ของ Fed · ตารางประกาศของ BLS |
| 6 | `fomc` · FOMC คืออะไร: ประชุม Fed ครั้งต่อไปเมื่อไหร่ และดอกเบี้ย Fed มีผลกับราคาทองอย่างไร | fomc คือ | ประชุม fed ครั้งต่อไป · ดอกเบี้ย fed มีผลอย่างไร · fomc วันไหน · ประชุม fed 2569 | ขึ้น: #4 · ข้าง: #20 #36 ea-news-trading | ปฏิทิน FOMC ของ Fed |
| 12 | `non-farm-payroll` · Non-Farm Payroll คืออะไร: ประกาศกี่โมงตามเวลาไทย และมีผลกับราคาทองอย่างไร | non farm payroll คือ | ข่าว nfp กี่โมง · non farm คือ · ข่าว non farm มีผลยังไงกับทอง | ขึ้น: #4 · ข้าง: #15 #6 ea-news-trading | ตาราง Employment Situation ของ BLS |
| 15 | `cpi` · CPI คืออะไร: ตัวเลขเงินเฟ้อสหรัฐประกาศเมื่อไหร่ และทำไมราคาทองขยับแรง | cpi คือ | cpi สหรัฐ · ตัวเลขเงินเฟ้อสหรัฐ · cpi ประกาศกี่โมง | ขึ้น: #4 · ข้าง: #6 #12 #20 | ตาราง CPI ของ BLS |
| 36 | `dollar-index-dxy` · DXY คืออะไร: ดัชนีดอลลาร์สหรัฐเกี่ยวกับราคาทองอย่างไร | dxy คือ | ดัชนีดอลลาร์ · dxy forex คือ · ค่า dxy คือ | ขึ้น: #4 · ข้าง: #20 #6 #15 | หน้า US Dollar Index ของ ICE (ผู้ดูแลดัชนี) |

## 5. กติกาลิงก์ให้เป็นธรรมชาติ

- บทเสริมลิงก์ขึ้นบทหลักของกลุ่ม 1 ครั้งภายใน 3 ย่อหน้าแรก ด้วยข้อความที่เป็นประโยค เช่น "ถ้ายังไม่แน่ใจว่าบอทเทรดทองทำงานอย่างไร อ่านภาพรวมก่อน" ไม่ใช้คีย์หลักของบทหลักตรงตัวทุกครั้ง
- บทเสริมลิงก์ข้าง 2–3 เรื่อง ในจุดที่ผู้อ่านจะสงสัยต่อจริง และลิงก์ข้ามกลุ่มได้ 1 เรื่อง
- ทุกเรื่องพาไปหน้าเว็บ 1–2 หน้าที่ตรงกับเรื่อง (ช่อง "หน้า" ข้างบน) หน้าที่เป็นตัวหนาคือปลายทางหลักของเรื่องนั้น และปิดท้ายด้วยบล็อกติดต่อตามสเปก
- บทหลักลิงก์ลงไปทุกบทเสริมในกลุ่ม: ลิงก์ในเนื้อหาตรงหัวข้อที่เกี่ยว + รายการ `related-links` ท้ายเรื่อง
- เขียนลิงก์ไปบทที่ยังไม่ออกไว้ได้เลย ธีมซ่อนลิงก์ไปบทความที่ยังไม่เผยแพร่ให้เอง (`fenix_unlink_slugs()`) และลิงก์โผล่เองเมื่อบทนั้นออก ประโยค "อ่านต่อ" ให้ห่อ `span.xref` เพื่อให้หายทั้งประโยคระหว่างรอ
- ราว 1 ลิงก์ในต่อ 150–200 คำ ไม่ลิงก์ซ้ำปลายทางเดิมในเรื่องเดียว
- ลิงก์นอก 1–2 ลิงก์ต่อเรื่อง เฉพาะแหล่งต้นทาง (MetaQuotes, Fed, BLS, ICE, LBMA, World Gold Council, ESMA, CFTC, ก.ล.ต., ตำรวจไซเบอร์, สมาคมค้าทองคำ) บอกในประโยคว่าเป็นภาษาอังกฤษ ติด `target="_blank" rel="noopener"` ห้ามลิงก์โบรกเกอร์ ผู้ขาย EA บริการสัญญาณ ผู้ให้บริการ VPS หรือเว็บปฏิทินของโบรกเกอร์

ลิงก์นอกที่ตรวจแล้วว่าเปิดได้ (14 ต.ค. 2026):
- MetaQuotes: https://www.metatrader5.com/en/terminal/help/algotrading/testing · …/algotrading/testing_report · …/algotrading/tick_generation · …/algotrading/autotrading · …/trading/general_concept · …/trading_advanced/margin_forex · …/startworking/authorization · …/startworking/acc_open · https://www.metatrader5.com/en/mobile-trading/iphone/help · …/android/help
- Fed: https://www.federalreserve.gov/monetarypolicy/fomccalendars.htm
- BLS: https://www.bls.gov/schedule/news_release/empsit.htm · https://www.bls.gov/schedule/news_release/cpi.htm
- ICE: https://www.ice.com/products/194/US-Dollar-Index-Futures
- LBMA: https://www.lbma.org.uk/prices-and-data/precious-metal-prices
- World Gold Council: https://www.gold.org/goldhub/research/gold-demand-trends
- ESMA (CFD/Negative balance): https://www.esma.europa.eu/press-news/esma-news/esma-agrees-prohibit-binary-options-and-restrict-cfds-protect-retail-investors
- ESMA (Copy trading): https://www.esma.europa.eu/sites/default/files/2023-03/ESMA35-42-1428_Supervisory_Briefing_on_Copy_Trading.pdf
- CFTC: https://www.cftc.gov/LearnAndProtect/AdvisoriesAndArticles/AITradingBots.html
- สมาคมค้าทองคำ: https://www.goldtraders.or.th/ · ตำรวจไซเบอร์: https://www.thaipoliceonline.go.th/

## 6. ปรับบทความเดิมให้เข้าแผน

- `what-is-ea-mt5`: เปลี่ยนคีย์หลักเป็น "EA Forex คืออะไร" และใส่ "EA Forex" ในชื่อเรื่อง (คำว่า EA เดี่ยว ๆ คนค้นหาหุ้นและเกม) · ลิงก์ไป #1 และ #9
- `ea-gold-xauusd`: เปลี่ยนคีย์หลักเป็น "EA XAUUSD" (มุมสเปกสัญญา) ให้ #1 ถือคำ "บอทเทรดทอง / EA เทรดทอง" คนเดียว ไม่แย่งอันดับกันเอง
- `grid-martingale-ea` ถือ "martingale คือ / ea martingale" · #26 ถือ "grid trading คือ / ea grid คือ"
- `margin-call-stop-out` ถือ "margin call คือ / stop out คือ" · #7 ถือ "margin level / balance equity / free margin"
- `lot-size-calculation` ถือ "คำนวณ lot ตามความเสี่ยง" · #13 ถือ "นับจุด / 1 lot ทองกี่บาท"
- `vps-for-ea`, `vps-mt5-stable`: เพิ่มประโยคพาไป #2 (ทางที่ไม่ต้องเช่า VPS)
- ทุกเรื่องเดิม 22 เรื่อง: เพิ่มลิงก์ขึ้นบทหลักของกลุ่ม 1 ประโยค และรายการใน `related-links` แล้วใช้ปุ่ม "อัปเดตบทความตามไฟล์รุ่นล่าสุด" ที่มีอยู่ (เนื้อหาความรู้เดิมไม่เขียนใหม่)
- หมวดบทความ: จาก 3 หมวดเป็น 6 หมวดตามกลุ่ม A–F · "พื้นฐาน EA" → "EA และบอทเทรดทอง" · "MT5 & VPS" → "MT5 และบัญชีเทรด" · เพิ่ม "เทรดทองคำ XAUUSD" "Backtest และผลทดสอบ" "ข่าวเศรษฐกิจ" · "บริหารความเสี่ยง" คงเดิม

## 7. ข้อควรระวังรายเรื่อง

- ทุกเรื่องที่พูดถึง FALCON ต้องตรงกับข้อเท็จจริงใน `CLAUDE.md`: XAUUSD บน MT5 เท่านั้น · กริดขยาย Lot และไม่ตัดขาดทุนเอง · ใช้ผ่านแดชบอร์ดอย่างเดียว ไม่มีไฟล์ EA ไม่ต้องเช่า VPS · บัญชีจริงของโบรกเกอร์พาร์ตเนอร์ (Cent ได้ Demo ไม่ได้) · จ่ายครั้งเดียวรวมการรันตลอดไป · ทีมงานไม่ขอรหัสผ่านทางแชท
- #2 #9 #14 #29: บอกวิธีทั่วไปของ EA แบบติดตั้งเองได้ในฐานะความรู้ แต่ต้องแยกให้ชัดว่า FALCON ไม่ใช่แบบนั้น
- #17 #18 #26 #40: พูดตรง ๆ ว่า FALCON ไม่ตัดขาดทุนเองและเสียเงินทั้งบัญชีได้
- #21: FALCON เป็น EA แบบกฎตายตัว ห้ามเรียกว่า AI
- #22: แดชบอร์ดใช้รหัสผ่านเทรดที่ลูกค้าพิมพ์เอง ส่วน Investor Password ใช้ดูอย่างเดียว ต้องเขียนให้ตรงกับระบบจริง
- #25: ไม่มีตัวเลขทุนขั้นต่ำของ FALCON ในข้อมูลที่มี ห้ามใส่เอง ให้สอนวิธีคิดแทน
- #39: อายุผู้ใช้ FALCON 20 ปีขึ้นไป ส่วนเกณฑ์อายุของโบรกเกอร์ให้บอกว่าเช็กที่โบรกเกอร์ ห้ามเดา
- เรื่องข่าว (#4 #6 #12 #15 #36): ไม่ทำนายราคา ตารางวันประกาศใส่ทั้งปีที่เหลือและปีถัดไปจากเว็บทางการ จะได้ไม่ต้องตามแก้บ่อย

## 8. หัวข้อที่คนค้นเยอะแต่ไม่ใส่ในปฏิทิน

- **"forex ผิดกฎหมายไหม"** (คะแนน 20–31 หลายรูปแบบ) และ "บอทเทรดทอง ถูกกฎหมายไหม": ธปท. ออกข่าว 25 มิ.ย. 2026 ว่าไม่มีนโยบายออกใบอนุญาตธุรกิจ Forex เพื่อเก็งกำไร และการโฆษณาหรือชักชวนให้เทรด Forex อาจเข้าข่ายความผิดตามกฎหมายฉ้อโกงประชาชน (https://www.bot.or.th/th/news-and-media/news/news-20260625.html) ควรให้ทนายตรวจทั้งเรื่องนี้และวิธีพูดถึงการเปิดบัญชีบนเว็บก่อน
- **ภาษีกำไรจากการเทรด** ("เทรด forex เสียภาษีไหม" "ภาษี forex 2569"): กติกาเงินได้จากต่างประเทศเปลี่ยนหลายรอบ ต้องให้นักบัญชีตรวจก่อน
- **เช็กใบอนุญาตโบรกเกอร์**: ผูกกับประเด็นกฎหมายข้อแรก
- **Forex คืออะไร** (46): คนค้นเยอะแต่กว้างเกินสินค้า (FALCON เทรดทองอย่างเดียว) และคู่แข่งเป็นโบรกเกอร์ใหญ่ทั้งหมด
- รีวิว/จัดอันดับ EA หรือโบรกเกอร์ ("ไหนดี pantip"): ห้ามแต่งรีวิวและห้ามเอ่ยชื่อโบรกเกอร์ในธีม
- **Forward Test คืออะไร** (44): เจ้าของตัดสิน 14 ต.ค. 2026 ไม่เขียน (FALCON ยังไม่มีผล Forward Test ที่เผยแพร่) · บทอื่นไม่ต้องอธิบาย Forward Test เป็นหัวข้อของตัวเอง

## 9. งานที่ต้องทำก่อนเริ่มลงวันละเรื่อง

1. ตัวปล่อยบทความรองรับแค่ 1–3 เรื่องต่อสัปดาห์ (`fenix_release_weekdays()`) และต่อคิวหลังเรื่องสุดท้ายที่ตั้งเวลาไว้ ต้องเพิ่มโหมดวันละเรื่องที่เติมวันว่างระหว่างคิวเดิม (เรื่องเดิมคงวันเดิม) และทดสอบใน `dev/check-release.php`
2. เพิ่ม 41 slug ใน `fenix_seed_articles()` + ลำดับใน `fenix_seed_publish_order()` + ไอคอนปกใน `dev/preview/cover.php`
3. ปกบทความใหม่: ไม่มีบรรทัดป้ายหมวด (กติกาข้อ 7 · เจ้าของยืนยัน 14 ต.ค. 2026) · ปกเดิม 22 รูปไม่ต้องสร้างใหม่
4. บทหลัก 6 เรื่องยาวกว่าเกณฑ์เดิมของสเปก (1,200–2,200 คำ) เสนอ 2,500–3,500 คำ ต้องเพิ่มข้อยกเว้นใน `dev/content-spec.md`
5. เขียนล่วงหน้าทีละสัปดาห์ (7 เรื่อง) นำเข้าเป็นฉบับร่างแล้วตั้งเวลา ตัวตรวจทั้งห้าต้องผ่านทุกชุด

# FALCON PRO EA — WordPress Theme

ธีม WordPress (custom, ไม่มี build step) สำหรับเว็บ Landing ขาย **FALCON PRO EA** — EA ช่วยเทรดอัตโนมัติบน MetaTrader 5 โทนดำ·ฟ้าเทอร์คอยซ์·เงิน เนื้อหาภาษาไทย ปรับแต่งทุกอย่างผ่าน WordPress Customizer

> Fork มาจากธีม FENIX PRO EA แล้วรีแบรนด์ (สี + ชื่อ + text domain) — รายละเอียดสำหรับ dev อยู่ใน [CLAUDE.md](CLAUDE.md)

## โครงสร้าง repo
- `falcon-pro/` — ตัวธีม (WP Pusher ตั้ง subdirectory = `falcon-pro`)
- `CLAUDE.md` — บริบท/กติกาสำหรับแก้โค้ด
- `falcon-pro/readme.txt` — คู่มือผู้ใช้ (เจ้าของเว็บ)

## Deploy
แก้โค้ด → `git commit` → `git push` → WP Pusher บนเว็บ FALCON ดึงลงไปติดตั้ง (subdirectory = `falcon-pro`)

## ก่อน go-live (เช็คลิสต์รีแบรนด์ที่ยังค้าง)
ดูหัวข้อ "เช็คลิสต์รีแบรนด์ที่ยังค้าง" ใน [CLAUDE.md](CLAUDE.md) — สำคัญสุดคือ **เปลี่ยนโลโก้ + screenshot เป็นของ FALCON** และกรอกเนื้อหา/ราคา/LINE จริงผ่าน Customizer

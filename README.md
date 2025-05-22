# 🐾 ECP Online Petition PHP

(ECP Online Petition) ระบบยื่นคำร้อง สาขาคอมพิวเตอร์ คณะวิศวกรรมศาสตร์ มหาวิทยาลัยเทคโนโลยีราชมงคลอีสาน วิทยาเขตขอนแก่น พัฒนาโดยใช้ PHP และ Docker เพื่อความสะดวกในการติดตั้งและใช้งาน

---

## 🚀 วิธีการติดตั้ง (บน Ubuntu Server)

### 1. Clone โปรเจกต์จาก GitHub

```bash
git clone https://github.com/pratchayapol/ecp_online_petition_php.git

2. ติดตั้ง Docker
กรุณาติดตั้ง Docker Engine ตามคำแนะนำอย่างเป็นทางการ:

🔗 คู่มือติดตั้ง Docker บน Ubuntu

3. เข้าไปยังโฟลเดอร์ของโปรเจกต์
bash
คัดลอก
แก้ไข
cd ecp_online_petition_php
4. เริ่มต้นระบบด้วย Docker Compose
bash
คัดลอก
แก้ไข
docker compose up -d
ระบบจะถูกติดตั้งและรันอัตโนมัติในเบื้องหลัง 🎉

🌐 การเข้าถึงระบบ
📌 หน้าบ้าน (Frontend)
https://ecpreq.pcnone.com

หรือ http://203.158.201.73

🔐 หลังบ้าน (Backend/Admin)
https://ecpreq-data.pcnone.com

หรือ http://203.158.201.73:8080

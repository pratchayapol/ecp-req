# 🐾 ECP Online Petition PHP

**ECP Online Petition**  
ระบบยื่นคำร้องออนไลน์ของสาขาคอมพิวเตอร์ คณะวิศวกรรมศาสตร์  
มหาวิทยาลัยเทคโนโลยีราชมงคลอีสาน วิทยาเขตขอนแก่น  
พัฒนาโดยใช้ PHP และ Docker เพื่อความสะดวกในการติดตั้งและใช้งาน

---

## 🚀 วิธีการติดตั้ง (บน Ubuntu Server)

### 1. Clone โปรเจกต์จาก GitHub

```bash
git clone https://github.com/pratchayapol/ecp_online_petition_php.git
```

---

### 2. ติดตั้ง Docker

กรุณาติดตั้ง Docker Engine ตามคำแนะนำอย่างเป็นทางการ:  
🔗 [คลิกที่นี่เพื่อติดตั้ง Docker บน Ubuntu](https://docs.docker.com/engine/install/ubuntu/)

---

### 3. เข้าไปยังโฟลเดอร์ของโปรเจกต์

```bash
cd ecp_online_petition_php
```

---

### 4. เริ่มต้นระบบด้วย Docker Compose

```bash
docker compose up -d
```

✅ ระบบจะถูกติดตั้งและรันอัตโนมัติในเบื้องหลัง

---

## 🌐 การเข้าถึงระบบ

### 📌 หน้าบ้าน (Frontend)

- 🌐 [https://ecpreq.pcnone.com](https://ecpreq.pcnone.com)  
- 🌐 [http://203.158.201.73](http://203.158.201.73)

### 🔐 หลังบ้าน (Backend/Admin)

- 🔐 [https://ecpreq-data.pcnone.com](https://ecpreq-data.pcnone.com)  
- 🔐 [http://203.158.201.73:8080](http://203.158.201.73:8080)

---
### 🔐 อย่าลืมตั้งรหัสผ่านใหม่ของฐานข้อมูลเพื่อความปลอดภัยครับ
## 🛠 ความต้องการของระบบ

- ✅ Ubuntu Server  
- ✅ Docker  
- ✅ Docker Compose

---

## 🙋‍♂️ ผู้พัฒนา

- GitHub: [Pratchayapol](https://github.com/pratchayapol)

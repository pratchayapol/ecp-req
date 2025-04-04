📦 ติดตั้งระบบฝั่งหน้าบ้าน (Frontend Setup) บน Ubuntu Server 24.04
✅ ขั้นตอนการติดตั้ง
1. อัปเดตระบบ

sudo apt update && sudo apt upgrade -y

2. ติดตั้ง Nginx

sudo apt install nginx -y
sudo systemctl enable nginx
sudo systemctl start nginx

3. เพิ่ม PPA สำหรับ PHP 8.2

sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

4. ติดตั้ง PHP 8.2 และ Extensions ที่จำเป็น

sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-curl php8.2-mbstring \
php8.2-xml php8.2-zip php8.2-bcmath php8.2-cli php8.2-common unzip -y

php -v

5. ติดตั้ง Composer

cd ~
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
php -r "unlink('composer-setup.php');"

composer --version

6. Clone โค้ดจาก GitHub

cd /var/www/html/
sudo git clone https://github.com/pratchayapol/ecp-req.git

7. ติดตั้ง Dependencies ด้วย Composer

cd /var/www/html/ecp-req/src
git config --global --add safe.directory /var/www/html/ecp-req
sudo chown -R $USER:$USER /var/www/html/ecp-req
sudo chmod -R 755 /var/www/html/ecp-req
composer install

8. ตั้งค่า Nginx เพื่อใช้งาน PHP

sudo rm -r /etc/nginx/sites-available/default
sudo cp /var/www/html/ecp-req/default.conf/default.conf /etc/nginx/sites-available/default


9. Reload Nginx

sudo nginx -t
sudo systemctl reload nginx

10. สร้างไฟล์ .env สำหรับใช้งาน Google Auth
สร้างไฟล์ .env ภายในโฟลเดอร์โปรเจกต์ แล้วเพิ่มค่าดังนี้:

GOOGLE_CLIENT_ID=YOUR_CLIENT_ID
GOOGLE_CLIENT_SECRET=YOUR_CLIENT_SECRET
GOOGLE_REDIRECT_URI=https://ecpreq.pcnone.com/google_auth

💡 หมายเหตุ: อย่าลืมเปลี่ยน YOUR_CLIENT_ID, YOUR_CLIENT_SECRET ให้ตรงกับค่าจริงที่ได้จาก Google Developer Console


ติดตั้งหลังบ้าน install docker ก่อน

for pkg in docker.io docker-doc docker-compose docker-compose-v2 podman-docker containerd runc; do sudo apt-get remove $pkg; done
# Add Docker's official GPG key:
sudo apt-get update
sudo apt-get install ca-certificates curl
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc

# Add the repository to Apt sources:
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu \
  $(. /etc/os-release && echo "${UBUNTU_CODENAME:-$VERSION_CODENAME}") stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt-get update
sudo apt-get install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin -y

# สร้างกลุ่ม user และให้สิทธิ์
sudo groupadd docker
sudo usermod -aG docker $USER
logout

cd /var/www/html/ecp-req
docker compose up -d

จบแล้ว
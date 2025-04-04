ขั้นตอนการติดตั้ง ฝั่งหน้าบ้าน os ubuntu server 24.04
1. update upgrade

sudo apt update && sudo apt upgrade -y

2. Install Nginx

sudo apt install nginx -y
sudo systemctl enable nginx
sudo systemctl start nginx


3. เพิ่ม PPA ของ PHP 8.2:

sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

4. ติดตั้ง PHP 8.2 และ extensions:

sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-curl php8.2-mbstring php8.2-xml php8.2-zip php8.2-bcmath php8.2-cli php8.2-common unzip -y
php -v

5. ติดตั้ง Composer
cd ~
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
php -r "unlink('composer-setup.php');"

composer --version

6. clone code

git clone https://github.com/pratchayapol/ecp-req.git

7. ติดตั้ง lib composer ที่จะใช้

cd /var/www/html/etc-req/src

composer install

8. ตั้งค่า Nginx ให้ทำงานกับ PHP config: def

sudo rm -r /etc/nginx/sites-available/default
sudo cp /var/www/html/ecp-req/src/default.conf/default.conf /etc/nginx/sites-available/default

9. reload

sudo nginx -t
sudo systemctl reload nginx

10. สร้างไฟล์ .env ใน server

GOOGLE_CLIENT_ID=YOUR_CLIENT_ID
GOOGLE_CLIENT_SECRET=YOUR_CLIENT_SECRET
GOOGLE_REDIRECT_URI=https://ecpreq.pcnone.com/google_auth

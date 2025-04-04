<?php
// โหลดไฟล์ให้ครบทุกตัว
require_once 'vendor/autoload.php'; // โหลด Google Client
use Dotenv\Dotenv;

// โหลดไฟล์ .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// สร้างอ็อบเจ็กต์ของ Google_Client
$client = new Google_Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
$client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);

// ตรวจสอบการรับ code จาก URL
if (isset($_GET['code'])) {
    // รับ authorization code และแลกเป็น access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    // บันทึก access token ลงในไฟล์ token.json
    file_put_contents('token.json', json_encode($client->getAccessToken()));
    echo 'Authorization successful!';
    exit;
} else {
    // แสดงลิงก์สำหรับให้ผู้ใช้อนุมัติการเข้าถึง
    $authUrl = $client->createAuthUrl();
    echo "<a href='$authUrl'>Authorize</a>";
}

// ตรวจสอบว่า token.json มีค่า
if (file_exists('token.json')) {
    // อ่าน token จากไฟล์
    $token = json_decode(file_get_contents('token.json'), true);

    // เช็คว่า token หมดอายุหรือไม่
    if ($client->isAccessTokenExpired()) {
        // ถ้า access token หมดอายุ ใช้ refresh token เพื่อขอใหม่
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        file_put_contents('token.json', json_encode($client->getAccessToken()));
    }

    // ตั้งค่า access token
    $client->setAccessToken($token);
}

// ถ้ามี access token ที่ถูกต้อง
if ($client->getAccessToken()) {
    // ดึงข้อมูลจาก Google API
    $oauth2 = new Google_Service_Oauth2($client);
    $userInfo = $oauth2->userinfo->get();

    // แสดงข้อมูลผู้ใช้
    echo 'Hello, ' . $userInfo->name;
    echo '<br>Email: ' . $userInfo->email;
    echo '<br><img src="' . $userInfo->picture . '" alt="Profile Picture">';
} else {
    // ถ้าไม่มี access token หรือ expired
    echo 'No access token available.';
}
?>

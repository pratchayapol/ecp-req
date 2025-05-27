<?php
$servername = "mariadb";
$port = 3306;
$username = "system";
$password = "!?K#\Uy|Wt6:5g£49O{y&,MH8smnhG(506f";
$dbname = "ecp_req";

try {
    $dsn = "mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);

    // ตั้งค่า PDO ให้แสดงข้อผิดพลาดแบบ exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //echo "เชื่อมต่อฐานข้อมูลสำเร็จ!";
} catch (PDOException $e) {
    echo "เชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage();
}
?>

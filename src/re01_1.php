<?php
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    echo "Token ที่ได้รับคือ: " . htmlspecialchars($token);
} else {
    echo "ไม่พบ token ใน URL";
}
?>
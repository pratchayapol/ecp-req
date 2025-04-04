<?php
session_start();  // เริ่มต้น session

// ล้างตัวแปรทั้งหมดใน session
$_SESSION = array();

// ลบ session cookie ถ้ามีการใช้งาน
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// ทำลาย session
session_destroy();


// เปลี่ยนเส้นทางไปยังหน้าอื่นหลังจากออกจากระบบสำเร็จ
header("Location: https://ssonext.kku.ac.th/logout?app=01917deb-1950-72d3-8b83-bf359696c8cb");
exit;
?>
<!-- <script>
    // Redirect to the logout page after 0 milliseconds (immediately)
    window.location.href = "https://ssonext.kku.ac.th/logout?app=01917deb-1950-72d3-8b83-bf359696c8cb";
</script> -->
<?php
// if (isset($_SESSION['code'])) {
//     header("Location: https://ssonext.kku.ac.th/logout?app=01917deb-1950-72d3-8b83-bf359696c8cb");
//     exit(); // สำคัญ: ใช้ exit() เพื่อหยุดการทำงานของสคริปต์หลังจากการ redirect
// }
?>
<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting...</title>
</head>
<body>

<script>
    var code = "";

    // Redirect to the logout page after 0 milliseconds (immediately)
    window.location.href = "https://sso-uat-web.kku.ac.th/logout?app=" + code;

    // Open the second tab after 1000 milliseconds (1 second)
    setTimeout(function() {
        window.open("https://pmnu.kku.ac.th", "_blank");
    }, 10);
</script>

</body>
</html> -->

<?php
session_start();
include 'connect/dbcon.php';

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google-Login</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom fonts for this template-->
    <link rel="shortcut icon" href="./image/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="./css/fonts.css">
    <link rel="stylesheet" href="./css/bg.css">
    <!-- animation -->
    <link rel="stylesheet" href="./css/animation.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="flex items-center justify-center min-h-screen bg t1">
    <?php include './loadtab/h.php'; ?>
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
    $client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);  // เพิ่มขอบเขตสำหรับอีเมล

    // ตรวจสอบการรับ code จาก URL
    if (isset($_GET['code'])) {
        // รับ authorization code และแลกเป็น access token
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token);

        // บันทึก access token ลงในไฟล์ token.json
        file_put_contents('token.json', json_encode($client->getAccessToken()));

        // ตรวจสอบการล็อกอินจาก Google
        if ($client->getAccessToken()) {
            if (!isset($_SESSION['logged_in'])) {
                // ดึงข้อมูลจาก Google API
                $oauth2 = new Google_Service_Oauth2($client);
                $userInfo = $oauth2->userinfo->get();

                $_SESSION['user'] = [
                    'name' => $userInfo->name,
                    'email' => $userInfo->email,
                    'picture' => $userInfo->picture
                ];
                $_SESSION['logged_in'] = true;

                // ตรวจสอบว่าอีเมลมีในฐานข้อมูลหรือไม่
                $email = $userInfo->email;
                $stmt = $pdo->prepare("SELECT * FROM accounts WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $userAccount = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($userAccount) {
                    // ถ้ามีข้อมูลในฐานข้อมูล
                    $_SESSION['iname'] = $userAccount['name'];
                    $_SESSION['role'] = $userAccount['role'];
                    $_SESSION['id'] = $userAccount['id'];
                    
                    /**
                     * ฟังก์ชันคำนวณปีการศึกษาอัตโนมัติ
                     */
                    function getAcademicYear(): int
                    {
                        $today = new DateTime();
                        $year = (int)$today->format('Y');
                        $month = (int)$today->format('m');
                        $day = (int)$today->format('d');

                        return ($month > 6 || ($month == 6 && $day >= 15)) ? $year + 543 : ($year - 1) + 543;
                    }

                    /**
                     * ฟังก์ชันแปลงรหัสหลักสูตรเป็นระดับชั้น
                     */
                    function getAcademicLevel(string $courseLevel, int $academicYear): ?string
                    {
                        if (!preg_match('/(ECP)\/([A-Z])\s*\((\d+)\)/', $courseLevel, $matches)) {
                            return null;
                        }

                        $program = $matches[1]; // ECP
                        $type = $matches[2];    // N, R, Q
                        $batch = (int)$matches[3];
                        $batchYear = 2500 + $batch;
                        $yearDiff = $academicYear - $batchYear;

                        if ($yearDiff < 0 || $yearDiff >= 4) {
                            // ยังไม่เข้าเรียน หรือ เกิน 4 ปีแล้ว
                            return "{$program}/{$type} ({$batch})";
                        }

                        $yearLevel = $yearDiff + 1;

                        if ($type === 'N') {
                            // ปี 1-4 ได้ทั้งหมด
                            return "ECP{$yearLevel}N";
                        }

                        if (in_array($type, ['R', 'Q']) && $yearLevel >= 2 && $yearLevel <= 4) {
                            return "ECP{$yearLevel}{$type}";
                        }

                        return null; // ไม่ตรงเงื่อนไข
                    }

                    // เรียกใช้ฟังก์ชัน
                    $academicYear = getAcademicYear(); // คำนวณปีการศึกษา
                    $academicLevel = getAcademicLevel($userAccount['course_level'], $academicYear);

                    // เก็บใน session
                    $_SESSION['course_level'] = $userAccount['course_level'];
                    $_SESSION['academic_year'] = $academicYear;
                    $_SESSION['academic_level'] = $academicLevel;



                    $_SESSION['faculty'] = $userAccount['faculty'];
                    $_SESSION['field'] = $userAccount['field'];
                    $_SESSION['dep'] = $userAccount['dep'];

                    // อัปเดตภาพโปรไฟล์ในฐานข้อมูล
                    if ($userAccount['picture'] !== $userInfo->picture) {
                        $updateStmt = $pdo->prepare("UPDATE accounts SET picture = :picture WHERE email = :email");
                        $updateStmt->bindParam(':picture', $userInfo->picture);
                        $updateStmt->bindParam(':email', $email);
                        $updateStmt->execute();
                    }

                    // ตรวจสอบ role และ redirect ไปที่หน้า Dashboard ที่เหมาะสม
                    switch ($_SESSION['role']) {
                        case 'Admin':
                            header("Location: admin/dashboard");
                            exit();
                        case 'Teacher':
                            header("Location: teacher/dashboard");
                            exit();
                        case 'Student':
                            header("Location: student/dashboard");
                            exit();
                        default:
                            // ถ้า role ไม่ตรงกับที่คาดหวัง
                            header("Location: index");
                            exit();
                    }
                } else {
                    // ถ้าอีเมลไม่มีในฐานข้อมูล
                    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
                    echo '<script>
    Swal.fire({
        icon: "error",
        title: "อีเมลของคุณไม่พบในฐานข้อมูล",
        text: "โปรดใช้เมลมหาวิทยาลัยของคุณในการเข้าสู่ระบบ",
        showCancelButton: false,
        confirmButtonText: "ตกลง",
        backdrop: "rgba(0,0,0,0.4)",
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "?logout=true";
        }
    });
</script>';
                }
                exit();
            }

            // แสดงข้อมูลผู้ใช้ที่เก็บใน session (อยู่ได้จนกว่าจะ logout)
            $user = $_SESSION['user'];
            $role = $_SESSION['role'];

            echo '<div class="w-full max-w-md p-8 m-6 bg-white rounded-2xl shadow-2xl transform transition duration-500 hover:scale-105 mx-auto">
    <div class="flex flex-col items-center">';
            echo '<h1 class="text-3xl font-semibold text-gray-800 mb-6">ยินดีต้อนรับ</h1>';
            echo '<div class="mb-6">';
            echo '<img src="' . htmlspecialchars($user['picture']) . '" alt="Profile Picture" class="w-36 h-36 rounded-full mx-auto border-4 border-indigo-500 shadow-lg transform transition-transform duration-300 hover:scale-110">';
            echo '</div>';
            echo '<h1 class="text-3xl font-semibold text-gray-800 mb-6">' . htmlspecialchars($user['name']) . '</h1>';
            echo '<div class="mb-4 text-gray-700 text-lg">อีเมล: <span class="font-semibold">' . htmlspecialchars($user['email']) . '</span></div>';

            // เพิ่มปุ่มเข้าใช้งานตาม role ของผู้ใช้
            if ($role == 'Admin') {
                echo '<a href="admin/dashboard" class="mt-6 bg-green-500 hover:bg-green-600 text-white py-2 px-6 rounded-lg transition">ไปยังแผงควบคุมผู้ดูแลระบบ</a>';
            } elseif ($role == 'Teacher') {
                echo '<a href="teacher/dashboard" class="mt-6 bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded-lg transition">ไปยังแผงควบคุมอาจารย์</a>';
            } elseif ($role == 'Student') {
                echo '<a href="student/dashboard" class="mt-6 bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-6 rounded-lg transition">ไปยังแผงควบคุมของนักเรียน</a>';
            }

            echo '<a href="?logout=true" class="mt-6 bg-red-500 hover:bg-red-600 text-white py-2 px-6 rounded-lg transition">Logout</a>';
            echo '</div></div>';
        } else {
            echo "ไม่สามารถเชื่อมต่อกับ Google API ได้";
        }
    } else {
        // แสดงลิงก์สำหรับให้ผู้ใช้อนุมัติการเข้าถึง
        $authUrl = $client->createAuthUrl();

        echo '<div class="w-full max-w-md p-8 m-6 bg-white rounded-2xl shadow-2xl transform transition duration-500 hover:scale-105">
        <div class="flex flex-col items-center">';

        echo '  <h2 class="text-2xl font-bold text-gray-800 mb-3">เพื่อดำเนินการต่อ</h2>';
        echo '  <h3 class="text-lg font-medium text-gray-700 mb-4">กรุณาอนุมัติการเข้าถึงข้อมูลของคุณ</h3>';
        echo '  <p class="text-gray-600 mb-2">เราต้องการเข้าถึงข้อมูลพื้นฐานของคุณจาก Google</p>';
        echo '  <p class="text-gray-600 mb-4">เพื่อให้คุณสามารถใช้งานระบบได้อย่างราบรื่น</p>';
        echo '  <ul class="text-gray-700 text-left list-disc list-inside mb-6">';
        echo '    <li>ชื่อ - สกุล</li>';
        echo '    <li>Email</li>';
        echo '    <li>ภาพโปรไฟล์</li>';
        echo '  </ul>';
        echo "  <a href='$authUrl' class='inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-lg shadow-md transition-transform transform hover:scale-105'>";
        echo "    อนุมัติ";
        echo '  </a></div>
    </div>';
    }

    if (isset($_GET['logout'])) {
        // เริ่มต้น session ถ้ายังไม่ได้เริ่ม
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // เคลียร์ตัวแปร session ทั้งหมด
        $_SESSION = [];

        // ถ้ามีการใช้ session cookie ให้ลบ cookie ด้วย
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000, // ทำให้ cookie หมดอายุ
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // ทำลาย session
        session_destroy();

        // URL สำหรับ redirect หลัง logout
        $logoutRedirect = 'https://ecpreq.pcnone.com';

        // Redirect ไป logout ของ Google และกลับมายังระบบ
        header('Location: https://accounts.google.com/Logout?continue=https://appengine.google.com/_ah/logout?continue=' . urlencode($logoutRedirect));
        exit();
    }


    ?>
    <?php include './loadtab/f.php'; ?>
</body>

</html>
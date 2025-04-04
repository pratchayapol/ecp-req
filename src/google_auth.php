<!DOCTYPE html>
<html lang="en">

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

<body class="flex items-center justify-center min-h-screen bg">
    <div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-2xl transform transition duration-500 hover:scale-105">
        <div class="flex flex-col items-center">
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

                // ถ้ามี access token ที่ถูกต้อง
                if ($client->getAccessToken()) {
                    // ดึงข้อมูลจาก Google API
                    $oauth2 = new Google_Service_Oauth2($client);
                    $userInfo = $oauth2->userinfo->get();

                    // แสดงข้อมูลผู้ใช้

                    echo '<h1 class="text-3xl font-semibold text-gray-800 mb-6">ยินดีต้อนรับ <br>' . $userInfo->name . '</h1>';
                    echo '<div class="mb-4 text-gray-700 text-lg">อีเมล: <span class="font-semibold">' . $userInfo->email . '</span></div>';
                    echo '<div class="mb-6">';
                    echo '<img src="' . $userInfo->picture . '" alt="Profile Picture" class="w-36 h-36 rounded-full mx-auto border-4 border-indigo-500 shadow-lg transform transition-transform duration-300 hover:scale-110">';
                    echo '</div>';
                    echo '<div class="text-gray-600">';
                    echo '<p class="text-xl">ขอบคุณที่เข้าร่วมกับเรา!</p>';
                    echo '</div>';
                } else {
                    // ถ้าไม่มี access token หรือ expired

                    echo '<div class="bg-red-200 p-8 rounded-lg shadow-lg max-w-sm w-full text-center">';
                    echo 'ไม่มี access token หรือ expired';
                    echo '</div>';
                }
            } else {
                // แสดงลิงก์สำหรับให้ผู้ใช้อนุมัติการเข้าถึง
                $authUrl = $client->createAuthUrl();


                echo '<div class="max-w-xl mx-auto p-6 bg-white rounded-2xl shadow-lg mt-10 text-center">';
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
                echo "    <i class='fas fa-check-circle mr-2'></i>";
                echo "    อนุมัติ";
                echo '  </a>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</body>

</html>
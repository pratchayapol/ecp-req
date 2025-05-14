<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
include '../connect/dbcon.php';
$date = date('Y-m-d H:i:s');


// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';

// ตรวจสอบว่ามีข้อมูลใน session หรือไม่
if (isset($_SESSION['user'])) {
    $name = $_SESSION['user']['name'];
    $email = $_SESSION['user']['email'];
    $picture = $_SESSION['user']['picture'];
    $logged_in = $_SESSION['logged_in'] ?? 0;
    $iname = $_SESSION['iname'] ?? '';
    $role = $_SESSION['role'] ?? '';
    $id = $_SESSION['id'] ?? '';
    $course_level = $_SESSION['course_level'] ?? '';
    $faculty = $_SESSION['faculty'] ?? '';
    $field = $_SESSION['field'] ?? '';
    $dep = $_SESSION['dep'] ?? '';
    $academicYear = $_SESSION['academic_year'] ?? '';
    $academicLevel = $_SESSION['academic_level'] ?? '';
} else {
    header('location: ../session_timeout');
}

// ดึงชื่อตัวเอง
$sql = "SELECT name, email FROM accounts WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->execute(['email' => $email]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แบบฟอร์ม RE.01</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome (สำหรับไอคอน) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Custom fonts for this template-->
    <link rel="shortcut icon" href="../image/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="../css/fonts.css">
    <!-- animation -->
    <link rel="stylesheet" href="../css/animation.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body class="bg-cover bg-center bg-no-repeat t1" style="background-image: url('/image/bg.jpg'); background-size: cover; background-position: center; background-attachment: fixed; height: 100vh;">
    <?php include '../loadtab/h.php'; ?>
    <div class="flex flex-col sm:flex-row h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="sm:w-1/4 md:w-1/5 bg-white shadow-lg p-4 m-6 flex flex-col justify-between rounded-[20px]">
            <div class="text-center">
                <img src="/image/logo.png" class="w-24 h-24 sm:w-32 sm:h-32 rounded-full shadow-lg mx-auto" alt="Logo">

                <div class="mt-4 space-y-2">
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-center py-2 px-4 rounded-[12px] shadow-md" id="dashboard-btn"> Dashboard </button>
                    <button class="w-full bg-[#EF6526] text-white hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="re1">คำร้องทั่วไป RE.01</button>
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="re6">คำร้องขอเพิ่มที่นั่ง RE.06</button>
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="re7">คำร้องขอเปิดนอกแผน RE.07</button>
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="form_all">คำร้องของนักศึกษา</button>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="profile" class="flex items-center justify-center sm:justify-start space-x-2 hover:opacity-80 transition">
                    <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-gray-400">
                        <img src="<?= $picture ?>" alt="Profile Picture" class="w-full h-full object-cover">
                    </div>
                    <span class="text-sm sm:text-base text-gray-800"><?= htmlspecialchars($profile['name']) ?></span>
                </a>
                <button id="logoutBtn" class="w-full mt-4 bg-white text-[#2C2C2C] py-2 rounded-[12px] hover:bg-[#2C2C2C] hover:text-white transition-colors duration-200 shadow-md">
                    ออกจากระบบ
                </button>
            </div>
        </div>

        <!-- Hamburger Menu Button (Mobile) -->
        <button id="hamburgerBtn" class="sm:hidden absolute top-6 left-6 p-3 bg-[#EF6526] text-white rounded-full shadow-md">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col justify-between bg-white/60 mt-6 me-6 mb-6 rounded-[20px] overflow-auto">
            <div class="p-8">
                <div class="bg-white rounded-lg shadow-lg h-auto">
                    <h1 class="text-orange-500 bg-white p-2 text-xl h-12 font-bold shadow-md rounded-[12px] text-center">แบบฟอร์มคำร้องทั่วไป RE.01</h1>

                    <form class="space-y-4 m-6" action="" method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-medium mb-1 text-red-600">เรื่อง *</label>
                                <input type="text" name="title" class="w-full border rounded px-3 py-2" placeholder="กรุณาระบุชื่อเรื่อง" required>
                            </div>
                            <div>
                                <label class="block font-medium mb-1 text-red-600">เรียน *</label>
                                <input type="text" name="to" class="w-full border rounded px-3 py-2" placeholder="คณบดีคณะ, หัวหน้าสาขา, อาจารย์" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-medium mb-1 text-red-600">ข้าพเจ้า</label>
                                <input type="text" name="name" class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-500 cursor-not-allowed" value="<?php echo $iname ?>" readonly>
                            </div>
                            <div>
                                <label class="block font-medium mb-1 text-red-600">รหัสประจำตัวนักศึกษา</label>
                                <input type="text" name="student_id" class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-500 cursor-not-allowed" value="<?php echo $id ?>" readonly>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-medium mb-1 text-red-600">คณะ</label>
                                <input type="text" name="faculty" class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-500 cursor-not-allowed" value="<?php echo $faculty ?>" readonly>
                            </div>
                            <div>
                                <label class="block font-medium mb-1 text-red-600">สาขาวิชา</label>
                                <input type="text" name="field" class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-500 cursor-not-allowed" value="<?php echo $field ?>" readonly>
                            </div>
                            <div>
                                <label class="block font-medium mb-1 text-red-600">ชั้นปีที่</label>
                                <input type="text" name="course_level" class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-500 cursor-not-allowed" value="<?php echo $course_level ?>" readonly>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                            <div>
                                <label class="block font-medium mb-1 text-red-600">มีความประสงค์ *</label>
                                <textarea name="request" class="w-full border rounded px-3 py-2" placeholder="กรุณาระบุเหตุผล..." required></textarea>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php

                            try {
                                // ดึงอาจารย์ที่ปรึกษา
                                $sql = "SELECT name, email FROM accounts WHERE role = 'Teacher' AND Advisor LIKE :course_level";
                                $stmt = $pdo->prepare($sql);
                                $courseLevelWildcard = "%$course_level%";
                                $stmt->bindParam(':course_level', $courseLevelWildcard, PDO::PARAM_STR);
                                $stmt->execute();
                                $advisors = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // ดึงหัวหน้าสาขา
                                $sql = "SELECT name, email FROM accounts WHERE role = 'Teacher' AND dep = 'TRUE'";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute();
                                $heads = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                // จัดการ error เช่น log หรือแสดงข้อความ
                                $teacher_email = 'เกิดข้อผิดพลาด';
                                $head_department = 'เกิดข้อผิดพลาด';
                                error_log("PDO Error: " . $e->getMessage());
                            }
                            ?>

                            <div>
                                <label class="block font-medium mb-1 text-red-600">อาจารย์ที่ปรึกษา *</label>
                                <select name="teacher_email" class="w-full border rounded px-3 py-2 bg-white text-gray-800" required>
                                    <option value="">กรุณาเลือกอาจารย์ที่ปรึกษา</option>
                                    <?php foreach ($advisors as $advisor): ?>
                                        <option value="<?php echo htmlspecialchars($advisor['email']); ?>">
                                            <?php echo htmlspecialchars($advisor['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium mb-1 text-red-600">หัวหน้าสาขา</label>
                                <select name="head_department" class="w-full border rounded px-3 py-2 bg-white text-gray-800">
                                    <?php foreach ($heads as $head): ?>
                                        <option value="<?php echo htmlspecialchars($head['email']); ?>" readonly>
                                            <?php echo htmlspecialchars($head['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>





                        <div class="text-center pt-4">
                            <button type="submit" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                                บันทึกคำร้อง
                            </button>
                        </div>
                        <br>
                    </form>
                </div>

            </div>


            <footer class="text-center py-4 bg-orange-500 text-white m-4 rounded-[12px]">
                2025 All rights reserved by Software Engineering 3/67
            </footer>
        </div>
    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script -->
    <script>
        // Handle the hamburger menu button
        document.getElementById("hamburgerBtn").addEventListener("click", function() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("hidden");
        });

        // Handle logout button
        document.getElementById("logoutBtn").addEventListener("click", function() {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: 'คุณต้องการออกจากระบบหรือไม่',
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'ยกเลิก',
                confirmButtonText: 'ตกลง',
                reverseButtons: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                customClass: {
                    confirmButton: 'swal-confirm-btn',
                    cancelButton: 'swal-cancel-btn'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'https://ecpreq.pcnone.com/google_auth?logout=true';
                }
            });
        });


        document.getElementById('dashboard-btn').addEventListener('click', function() {
            window.location.href = 'dashboard';
        });
        document.getElementById('re1').addEventListener('click', function() {
            window.location.href = 'form_re01';
        });
        document.getElementById('re6').addEventListener('click', function() {
            window.location.href = 'form_re06';
        });
        document.getElementById('re7').addEventListener('click', function() {
            window.location.href = 'form_re07';
        });
        document.getElementById('form_all').addEventListener('click', function() {
            window.location.href = 'form_all';
        });
    </script>

    <!-- Custom Style -->
    <style>
        .swal-confirm-btn {
            background-color: #0059FF !important;
            color: white !important;
        }

        .swal-confirm-btn:hover {
            background-color: #0D4ABD !important;
        }

        .swal-cancel-btn {
            background-color: #EC2828 !important;
            color: white !important;
        }

        .swal-cancel-btn:hover {
            background-color: #BD0D0D !important;
        }

        /* ทำให้พื้นหลังคงที่ */
        body {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* z-index: -1; */
            /* ให้ภาพพื้นหลังอยู่หลังเนื้อหา */
        }

        /* ทำให้เนื้อหาหลักเลื่อน */
        .flex-1 {
            overflow-y: auto;
        }

        /* ซ่อนแถบเลื่อน */
        .flex-1::-webkit-scrollbar {
            display: none;
        }
    </style>
    <?php include '../loadtab/f.php'; ?>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {


        try {
            //สุ่มสร้าง token 15 ตัว
            function generateToken($length = 15)
            {
                $characters = array_merge(
                    range('A', 'Z'),
                    range('a', 'z'),
                    range('0', '9'),
                    ['-']
                );

                if ($length > count($characters)) {
                    throw new Exception("ความยาวเกินจำนวนอักขระที่ไม่ซ้ำกันได้");
                }

                shuffle($characters);
                return implode('', array_slice($characters, 0, $length));
            }

            //แบบฟอร์มคำร้องทั่วไป RE.01
            // รับค่าจากฟอร์ม
            $title         = $_POST['title']; //เรื่อง
            $to            = $_POST['to']; //เรียน
            $faculty       = $_POST['faculty']; //คณะ
            $field         = $_POST['field']; //สาขาวิชา
            $course_level  = $_POST['course_level']; //ชั้นปีที่
            $request       = $_POST['request']; //มีความประสงค์
            $teacher_email       = $_POST['teacher_email']; //เมลที่ปรึกษา
            $head_department       = $_POST['head_department']; //เมลหัวหน้าสาขา
            $token = generateToken(); //สร้างปุ่มและแนบ token คือ https://ecpreq.pcnone.com/sendmail_re1-1?token=


            require_once __DIR__ . '/vendor/autoload.php';


            $mail = new PHPMailer(true);

            try {
                $mail->CharSet = 'UTF-8';
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'botpcnone@gmail.com';
                $mail->Password   = 'lbro evfy ipng zpqf';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('botpcnone@gmail.com', 'ECP Online Petition');
                $mail->addAddress($teacher_email, 'อาจารย์ที่ปรึกษา');
                $mail->Subject = 'คำร้องทั่วไป (RE.01) ของ ' . $iname . '';
                $mail->isHTML(true); // เพิ่มบรรทัดนี้เพื่อให้รองรับ HTML

                $mail->isHTML(true);

                $mail->Body = '
                <div style="font-family: Tahoma, sans-serif; background-color:rgb(46, 46, 46); padding: 20px; border-radius: 10px; color: #f0f0f0; font-size: 18px;">
                    <h2 style="color: #ffa500; font-size: 24px;">📄 ยี่นคำร้องทั่วไป (RE.01)</h2>
                    <p style="margin-top: 10px; color:rgb(255, 255, 255); ">เรียน <strong>' . htmlspecialchars($to) . '</strong></p>
            
                    <div style="margin-top: 15px; padding: 15px; background-color:rgb(171, 166, 166); border-left: 4px solid #ffa500; color: #000;">
                        <p><strong>ชื่อ:</strong> ' . htmlspecialchars($iname) . '</p>
                        <p><strong>รหัสนักศึกษา:</strong> ' . htmlspecialchars($id) . '</p>
                        <p><strong>เรื่อง:</strong> ' . htmlspecialchars($title) . '</p>
                        <p><strong>คณะ:</strong> ' . htmlspecialchars($faculty) . '</p>
                        <p><strong>สาขาวิชา:</strong> ' . htmlspecialchars($field) . '</p>
                        <p><strong>ชั้นปีที่:</strong> ' . htmlspecialchars($course_level) . '</p>
                        <p><strong>ความประสงค์:</strong> ' . nl2br(htmlspecialchars($request)) . '</p>
                    </div>
            
                    <p style="margin-top: 20px;">📧 <strong>อีเมลที่ปรึกษา:</strong> ' . htmlspecialchars($teacher_email) . '<br>
                    📧 <strong>อีเมลหัวหน้าสาขา:</strong> ' . htmlspecialchars($head_department) . '</p>
            
                    <div style="margin-top: 30px;">
                        <a href="https://ecpreq.pcnone.com/re01_1?token=' . urlencode($token) . '" 
                            style="display: inline-block; padding: 12px 20px; background-color: #ffa500; color: #000; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 18px;">
                            ✅ คลิกเพื่อดำเนินการ
                        </a>
                    </div>
            
                    <p style="margin-top: 30px; font-size: 14px; color: #888;">ระบบยื่นคำร้อง สาขาคอมพิวเตอร์  คณะวิศวกรรมศาสตร์ มหาวิทยาลัยเทคโนโลยีราชมงคลอีสาน วิทยาเขตขอนแก่น</p>
                </div>
            ';



                $mail->send();
                echo 'Message has been sent';
            } catch (Exception $e) {
                echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
            }

            // คำสั่ง SQL สำหรับบันทึกข้อมูล
            $sql = "INSERT INTO form_re01 (title, `to`, email, faculty, field, course_level, request_text, teacher_email, head_department, token)
            VALUES (:title, :to, :email, :faculty, :field, :course_level, :request, :teacher_email, :head_department, :token)";


            // เตรียมการ query
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title'        => $title,
                ':to'           => $to,
                ':email'        => $email,
                ':faculty'      => $faculty,
                ':field'        => $field,
                ':course_level' => $course_level,
                ':request'      => $request,
                ':teacher_email'      => $teacher_email,
                ':head_department'      => $head_department,
                ':token'      => $token
            ]);

            // ทำให้แน่ใจว่าไม่มีการแสดง HTML หรือ JavaScript อื่น ๆ ก่อน
            echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body>";
            echo "
            <script>
            Swal.fire({
                title: 'สำเร็จ!',
                text: 'บันทึกข้อมูลเรียบร้อยแล้ว',
                icon: 'success',
                confirmButtonText: 'ตกลง'
            }).then(() => {
                window.location.href = 'form_all'; // กำหนดลิงก์ที่ถูกต้อง
                exit; // ป้องกันไม่ให้มีการแสดงอะไรหลังจากนี้
            });
            </script>
            ";
            echo "</body></html>";
            exit; // ปิด script ทันทีหลังจากเรียกใช้ SweetAlert2
        } catch (PDOException $e) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด!',
            text: '" . $e->getMessage() . "',
        });
        </script>";
        }
    }
    ?>
</body>

</html>
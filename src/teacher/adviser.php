<?php
session_start();
include '../connect/dbcon.php';
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

// ดึงข้อมูลจากฐานข้อมูล
$stmt = $pdo->prepare("SELECT * FROM `accounts` WHERE role = 'Teacher'");
$stmt->execute();
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$currentYear = date("Y") + 543;
$yearSuffixes = [];
for ($i = 0; $i < 8; $i++) {
    $yearSuffixes[] = substr((string)($currentYear - $i), -2); // ได้เช่น 67, 66, ...
}

$showSwal = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_advisor = $_POST['email'];
    $advisor = isset($_POST['advisor']) ? implode(", ", $_POST['advisor']) : '';
    // echo "<pre>Debug: $advisor</pre>"; // ใช้ <pre> เพื่อให้ดูง่ายขึ้น

    // Uncomment if needed
    $stmt = $pdo->prepare("UPDATE accounts SET Advisor = :advisor WHERE email = :email");
    $stmt->execute([
        'advisor' => $advisor,
        'email' => $email_advisor
    ]);
    // trigger ให้แสดง Swal
    $showSwal = true;
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการที่ปรึกษา</title>
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
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="form_all">คำร้องของนักศึกษา</button>

                    <?php
                    //ถ้า หัวหน้าสาขา dep = TRUE ให้แสดงปุ่มดังนี้
                    if ($dep === "TRUE") {
                    ?>
                        <button class="w-full bg-[#EF6526] text-white hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="adviser"> จัดการที่ปรึกษา </button>
                        <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="course"> จัดการรายวิชา </button>
                        <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="news"> จัดการข้อมูลประชาสัมพันธ์ </button>
                    <?php } ?>


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
                    <h1 class="text-orange-500 bg-white p-2 text-xl h-12 font-bold shadow-md rounded-[12px] text-center">จัดการที่ปรึกษา</h1>
                    <div class="overflow-x-auto ms-6 mt-6 me-6 mb-6">
                        <table class="min-w-full text-sm text-center border mb-6">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="py-2 px-4 border">ชื่อ - สกุล</th>
                                    <th class="py-2 px-4 border">คณะ</th>
                                    <th class="py-2 px-4 border">สาขาวิชา</th>
                                    <th class="py-2 px-4 border">เมล</th>
                                    <th class="py-2 px-4 border">ที่ปรึกษากลุ่มเรียน</th>
                                    <th class="py-2 px-4 border">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($teachers as $index => $row): ?>
                                    <?php
                                    $advisorGroups = explode(', ', $row['Advisor']); // เว้นวรรคหลัง comma ให้ match กับค่าจริง
                                    ?>
                                    <tr class="border-b">
                                        <td class="py-2 px-4 border">
                                            <?= htmlspecialchars($row['name']) ?>
                                            <?php if (!empty($row['dep'])): ?>
                                                <span class="text-sm text-gray-500">(หัวหน้าสาขา)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-2 px-4 border"><?= htmlspecialchars($row['faculty']) ?></td>
                                        <td class="py-2 px-4 border"><?= htmlspecialchars($row['field']) ?></td>
                                        <td class="py-2 px-4 border"><?= htmlspecialchars($row['email']) ?></td>
                                        <td class="py-2 px-4 border"><?= htmlspecialchars($row['Advisor']) ?></td>
                                        <td class="py-2 px-4 border">
                                            <button onclick="openModal('modal-<?= $index ?>')" class="bg-orange-500 text-white px-4 py-1 rounded hover:bg-orange-600">
                                                จัดการ
                                            </button>

                                            <!-- Modal -->
                                            <div id="modal-<?= $index ?>" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
                                                <div class="bg-white rounded-lg shadow-lg p-6 w-[500px] relative max-h-[90vh] overflow-y-auto">
                                                    <h2 class="text-lg font-bold mb-4">แก้ไขกลุ่มเรียนของ <?= htmlspecialchars($row['name']) ?></h2>
                                                    <form method="post" action="">
                                                        <input type="hidden" name="email" value="<?= htmlspecialchars($row['email']) ?>">

                                                        <div class="grid grid-cols-3 gap-2 text-sm mb-4">
                                                            <?php foreach (['ECP/N', 'ECP/R', 'ECP/Q'] as $prefix): ?>
                                                                <?php foreach ($yearSuffixes as $year):
                                                                    $value = "$prefix ($year)";
                                                                    $checked = in_array($value, $advisorGroups) ? 'checked' : '';
                                                                ?>
                                                                    <label class="flex items-center space-x-1">
                                                                        <input type="checkbox" name="advisor[]" value="<?= $value ?>" class="accent-orange-500" <?= $checked ?>>
                                                                        <span><?= $value ?></span>
                                                                    </label>
                                                                <?php endforeach; ?>
                                                            <?php endforeach; ?>

                                                        </div>

                                                        <div class="flex justify-end mt-4 space-x-2">
                                                            <button type="submit" class="bg-green-500 text-white px-4 py-1 rounded hover:bg-green-600">บันทึก</button>
                                                            <button type="button" onclick="closeModal('modal-<?= $index ?>')" class="bg-gray-300 text-black px-4 py-1 rounded hover:bg-gray-400">ยกเลิก</button>
                                                        </div>
                                                    </form>
                                                    <button onclick="closeModal('modal-<?= $index ?>')" class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl">
                                                        ✕
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- JavaScript สำหรับ Modal -->
            <script>
                function openModal(id) {
                    document.getElementById(id).classList.remove('hidden');
                    document.getElementById(id).classList.add('flex');
                }

                function closeModal(id) {
                    document.getElementById(id).classList.add('hidden');
                    document.getElementById(id).classList.remove('flex');
                }
            </script>


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
        document.getElementById('form_all').addEventListener('click', function() {
            window.location.href = 'form_all';
        });
        document.getElementById('adviser').addEventListener('click', function() {
            window.location.href = 'adviser';
        });
        document.getElementById('course').addEventListener('click', function() {
            window.location.href = 'course';
        });
        document.getElementById('news').addEventListener('click', function() {
            window.location.href = 'news';
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
    <?php if ($showSwal): ?>
        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: 'อัปเดตสำเร็จ!',
                    text: 'ข้อมูลกลุ่มเรียนของอาจารย์ได้รับการบันทึกแล้ว',
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'teacher/adviser'; // เปลี่ยนเส้นทาง
                    }
                });
            });
        </script>
    <?php endif; ?>



    <?php include '../loadtab/f.php'; ?>
</body>

</html>
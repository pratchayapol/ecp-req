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


$stmt = $pdo->prepare("SELECT * FROM course");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
// ดึงอาจารย์ทั้งหมด 
$teacher_stmt = $pdo->prepare("SELECT * FROM accounts WHERE role = 'Teacher'");
$teacher_stmt->execute();
$teachers = $teacher_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการรายวิชา</title>
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
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="adviser"> จัดการที่ปรึกษา </button>
                    <button class="w-full bg-[#EF6526] text-white hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="course"> จัดการรายวิชา </button>
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="news"> จัดการข้อมูลประชาสัมพันธ์ </button>



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
                    <h1 class="text-orange-500 bg-white p-2 text-xl h-12 font-bold shadow-md rounded-[12px] text-center">จัดการรายวิชา</h1>
                    <div class="overflow-x-auto ms-6 mt-6 me-6 mb-6">
                        <table class="min-w-full text-sm text-center border mb-6">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border p-2">รหัสวิชา</th>
                                    <th class="border p-2">ชื่อวิชาภาษาไทย</th>
                                    <th class="border p-2">ชื่อวิชาภาษาอังกฤษ</th>
                                    <th class="border p-2">หน่วยกิต</th>
                                    <th class="border p-2">อาจารย์</th>
                                    <th class="border p-2">จัดการ</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($courses as $index => $course): ?>
                                    <tr class="border">
                                        <td class="border p-2"><?= htmlspecialchars($course['course_id']) ?></td>
                                        <td class="border p-2"><?= htmlspecialchars($course['course_nameTH']) ?></td>
                                        <td class="border p-2"><?= htmlspecialchars($course['course_nameEN']) ?></td>
                                        <td class="border p-2"><?= htmlspecialchars($course['credits']) ?></td>
                                        <td class="border p-2">
                                            <?php
                                            $emails = explode(', ', $course['email']); // แยกอีเมลออกมาเป็น array
                                            $names = [];
                                            foreach ($emails as $email) {
                                                $email = trim($email); // ตัดช่องว่างข้างหน้า-หลังออก
                                                foreach ($teachers as $teacher) {
                                                    if ($teacher['email'] === $email) {
                                                        $names[] = htmlspecialchars($teacher['name']);
                                                        break;
                                                    }
                                                }
                                            }
                                            echo implode(', ', $names); // รวมชื่ออาจารย์แล้วแสดง
                                            ?>
                                        </td>
                                        <td class="border p-2">
                                            <button onclick="openModal('modal<?= $index ?>')" class="bg-blue-500 text-white px-3 py-1 rounded">
                                                จัดการ
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal สำหรับจัดการอาจารย์ -->
                                    <div id="modal<?= $index ?>" class="fixed inset-0 bg-black bg-opacity-30 hidden items-center justify-center z-50">
                                        <div class="bg-white p-6 rounded-lg w-[400px] shadow-lg">
                                            <h2 class="text-lg font-semibold mb-4 text-center">เลือกอาจารย์ผู้สอน</h2>

                                            <form method="POST" action="">
                                                <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">

                                                <div class="max-h-60 overflow-y-auto mb-4 text-left">
                                                    <?php
                                                    $selectedEmails = explode(", ", $course['email']);
                                                    foreach ($teachers as $teacher):
                                                        $checked = in_array($teacher['email'], $selectedEmails) ? 'checked' : '';
                                                    ?>
                                                        <label class="block mb-2">
                                                            <input type="checkbox" name="emails[]" value="<?= $teacher['email'] ?>" <?= $checked ?> class="mr-2">
                                                            <?= htmlspecialchars($teacher['name']) ?>
                                                        </label>
                                                    <?php endforeach; ?>
                                                </div>

                                                <div class="flex justify-end space-x-2">
                                                    <button type="submit" class="bg-green-500 text-white px-4 py-1 rounded">บันทึก</button>
                                                    <button type="button" onclick="closeModal('modal<?= $index ?>')" class="bg-gray-400 text-white px-4 py-1 rounded">ปิด</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>


                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <script>
                function openModal(id) {
                    const modal = document.getElementById(id);
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }

                function closeModal(id) {
                    const modal = document.getElementById(id);
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
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
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $showSwal = false;
        // รับค่า
        $course_id = $_POST['course_id'] ?? '';
        $emails = $_POST['emails'] ?? [];

        if ($course_id && is_array($emails)) {
            // แปลง emails array เป็นสตริงที่คั่นด้วย comma
            $email_string = implode(', ', array_map('trim', $emails));

            // อัปเดตลงฐานข้อมูล
            $update_stmt = $pdo->prepare("UPDATE course SET email = :email WHERE course_id = :course_id");
            $update_stmt->bindParam(':email', $email_string);
            $update_stmt->bindParam(':course_id', $course_id);

            if ($update_stmt->execute()) {
                // trigger ให้แสดง Swal
                $showSwal = true;
            } else {
                echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล');</script>";
            }
        } else {
            echo "<script>alert('ข้อมูลไม่ครบถ้วน');</script>";
        }
    }
    ?>
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
                        window.location.href = 'course'; // เปลี่ยนเส้นทาง
                    }
                });
            });
        </script>
    <?php endif; ?>
    <?php include '../loadtab/f.php'; ?>
</body>

</html>
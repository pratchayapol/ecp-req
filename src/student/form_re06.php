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
} else {
    $name = $email = $picture = null;
}

$logged_in = $_SESSION['logged_in'] ?? 0;
$role = $_SESSION['role'] ?? '';
$id = $_SESSION['id'] ?? '';
$course_level = $_SESSION['course_level'] ?? '';



// ดึงข้อมูลรายวิชาทั้งหมดเพื่อแสดงใน <select>
$sql = "SELECT course_id, course_nameTH FROM course";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ตรวจสอบข้อมูลหลังจากการดึงข้อมูล
if (isset($_GET['course_id'])) {
    $courseId = $_GET['course_id'];

    // ดึงข้อมูลของวิชาและอาจารย์จากฐานข้อมูล
    $sql = "SELECT c.course_id, c.course_nameTH, a.name AS instructor_name
            FROM course c
            LEFT JOIN accounts a ON a.email = c.email
            WHERE c.course_id = :course_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':course_id', $courseId, PDO::PARAM_STR);
    $stmt->execute();
    $courseInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    // ตรวจสอบข้อมูลที่ดึงมา
    var_dump($courseInfo);  // แสดงข้อมูลที่ดึงมา
    exit; // ปิดสคริปต์หลังตรวจสอบ
}

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แบบฟอร์ม RE.06</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome (สำหรับไอคอน) -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>

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
    <script>
        // ฟังก์ชันสำหรับอัปเดตข้อมูลวิชาที่แสดง
        function updateCourseInfo(course) {
            document.getElementById('courseId').textContent = course.course_id || 'N/A';
            document.getElementById('courseNameTH').textContent = course.course_nameTH || 'N/A';
            document.getElementById('courseInstructor').textContent = course.instructor_name || 'N/A';
        }

        // เมื่อเลือกวิชาใน dropdown
        function loadCourseInfo(courseId) {
            if (courseId) {
                fetch(`?course_id=${courseId}`)
                    .then(response => response.json())
                    .then(data => updateCourseInfo(data))
                    .catch(error => console.error('Error fetching course data:', error));
            } else {
                updateCourseInfo({
                    course_id: 'N/A',
                    course_nameTH: 'N/A',
                    instructor_name: 'N/A'
                });
            }
        }
    </script>

</head>

<body class="bg-cover bg-center bg-no-repeat t1" style="background-image: url('/image/bg.jpg'); background-size: cover; background-position: center; background-attachment: fixed; height: 100vh;">
    <div class="flex flex-col sm:flex-row h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="sm:w-1/4 md:w-1/5 bg-white shadow-lg p-4 m-6 flex flex-col justify-between rounded-[20px]">
            <div class="text-center">
                <img src="/image/logo.png" class="w-24 h-24 sm:w-32 sm:h-32 rounded-full shadow-lg mx-auto" alt="Logo">

                <div class="mt-4 space-y-2">
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-center py-2 px-4 rounded-[12px] shadow-md" id="dashboard-btn"> Dashboard </button>
                    <button class="w-full bg-[#EF6526] text-white hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="re6">คำร้องขอเพิ่มที่นั่ง RE.06</button>
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="re7">คำร้องขอเปิดนอกแผน RE.07</button>
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="form_all">คำร้องที่ขอของนักศึกษา</button>
                </div>
            </div>
            <div class="text-center mt-4">
                <div class="flex items-center justify-center sm:justify-start space-x-2">
                    <div class="bg-gray-300 rounded-full w-10 h-10 flex items-center justify-center">
                        <img src="<?= $picture ?>" alt="Profile Picture" class="w-10 h-10 rounded-full object-cover">
                    </div>
                    <span class="text-sm sm:text-base"><?= $name; ?></span>
                </div>
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
                    <h1 class="text-orange-500 bg-white p-2 text-xl h-12 font-bold shadow-md rounded-[12px] text-center">แบบฟอร์มคำร้องขอเพิ่มที่นั่ง RE.06</h1>

                    <form class="space-y-4 m-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-medium mb-1 text-red-600">คำร้องขอเพิ่มที่นั่ง ภาคเรียนที่ *</label>
                                <select class="w-full border rounded px-3 py-2">
                                    <option value="" disabled selected>เลือกภาคเรียน</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium mb-1 text-red-600">ปีการศึกษาที่ *</label>
                                <select class="w-full border rounded px-3 py-2" id="academicYear">
                                    <option value="" disabled selected>เลือกปีการศึกษา</option>
                                </select>

                                <script>
                                    // Get the current year in the Buddhist Era (B.E.)
                                    const currentYearBE = new Date().getFullYear() + 543;

                                    // Get the select element
                                    const select = document.getElementById('academicYear');

                                    // Generate the academic years: 1 year before, current year, and 1 year after (in B.E.)
                                    for (let i = currentYearBE - 1; i <= currentYearBE; i++) {
                                        const option = document.createElement('option');
                                        option.value = i;
                                        option.textContent = i;
                                        select.appendChild(option);
                                    }
                                </script>


                            </div>
                        </div>

                        <div>
                            <label class="block font-medium mb-1 text-red-600">รายวิชาที่ต้องการขอเพิ่มที่นั่ง *</label>
                            <select class="w-full border rounded px-3 py-2" id="courseSelect" onchange="loadCourseInfo(this.value)">
                                <option value="">เลือกรหัสรายวิชา</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?= htmlspecialchars($course['course_id']) ?>">
                                        <?= htmlspecialchars($course['course_id']) ?> - <?= htmlspecialchars($course['course_nameTH']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-600">รหัสรายวิชา: <span class="text-black" id="courseId"><?= $courseInfo['course_id'] ?? 'N/A' ?></span></p>
                            </div>
                            <div>
                                <p class="text-gray-600">ชื่อรายวิชา: <span class="text-black" id="courseNameTH"><?= $courseInfo['course_nameTH'] ?? 'N/A' ?></span></p>
                            </div>
                            <div>
                                <p class="text-gray-600">อาจารย์ผู้สอน: <span class="text-black" id="courseInstructor"><?= $courseInfo['instructor_name'] ?? 'N/A' ?></span></p>
                            </div>
                            <div>
                                <label class="block font-medium mb-1 text-red-600">กลุ่มเรียน *</label>
                                <select class="w-full border rounded px-3 py-2" id="academicGroup">
                                    <option value="" disabled selected>เลือกกลุ่มเรียน</option>
                                </select>
                            </div>

                            <script>
                                // Get the current year in the Buddhist Era (B.E.)
                                const currentYearBE1 = new Date().getFullYear() + 543;

                                // Get the select element
                                const select1 = document.getElementById('academicGroup');

                                // Group prefixes
                                const groups = ['ECP/N', 'ECP/R', 'ECP/Q'];

                                // Generate options for each group with years from current year back to 8 years ago
                                for (let i = 0; i <= 8; i++) {
                                    const yearBE = currentYearBE1 - i;
                                    groups.forEach(group => {
                                        const option = document.createElement('option');
                                        option.value = `${group}(${yearBE.toString().slice(-2)})`; // Get last 2 digits of the year
                                        option.textContent = `${group}(${yearBE.toString().slice(-2)})`;
                                        select1.appendChild(option);
                                    });
                                }
                            </script>

                        </div>

                        <div>
                            <label class="block font-medium mb-1 text-red-600">ขอเพิ่มที่นั่ง เนื่องจาก *</label>
                            <select class="w-full border rounded px-3 py-2">
                                <option>เลือกเหตุผลที่ขอเพิ่มที่นั่ง</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="block font-medium text-red-600">ปัจจุบันรายวิชานี้มียอดลงทะเบียนแล้ว *</label>
                            <input type="number" class="border rounded px-2 py-1 w-20" />
                            <span>คน</span>
                        </div>

                        <div>
                            <label class="block font-medium mb-2 text-red-600">สถานภาพการลงทะเบียนวิชาที่ขอเพิ่มที่นั่ง *</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="reg_status" />
                                    ลงทะเบียนตามแผนการเรียน
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="reg_status" />
                                    ลงทะเบียนเพิ่ม “ปกติ”
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="reg_status" />
                                    ลงทะเบียนเพิ่ม “รีเกรด”
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="reg_status" />
                                    ลงทะเบียนเพิ่ม “ซ่อม”
                                </label>
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
            z-index: -1;
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
</body>

</html>
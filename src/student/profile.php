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
        $yearLevel2 = $yearDiff + 2;

        if ($type === 'N') {
            // ปี 1-4 ได้ทั้งหมด
            return "ECP{$yearLevel}N";
        }

        if (in_array($type, ['R', 'Q']) && $yearLevel2 >= 2 && $yearLevel2 <= 4) {
            return "ECP{$yearLevel2}{$type}";
        } else {
            return "{$program}/{$type} ({$batch})";
        }

        return null; // ไม่ตรงเงื่อนไข
    }

    // เรียกใช้ฟังก์ชัน
    $academicYear = getAcademicYear(); // คำนวณปีการศึกษา
    $academicLevel = getAcademicLevel($course_level, $academicYear);

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
} else {
    header('location: ../session_timeout');
}

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="re1">คำร้องทั่วไป RE.01</button>
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
                    <span class="text-sm sm:text-base text-gray-800"><?= $iname; ?></span>
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
                    <h1 class="text-orange-500 bg-white p-2 text-xl h-12 font-bold shadow-md rounded-[12px] text-center">ข้อมูลส่วนตัว</h1>
                    <div class="p-6 grid grid-cols-1 gap-4 place-items-center">
                        <label>รูปภาพ</label>
                        <img src="<?= $picture; ?>" alt="Profile Picture" class="w-32 h-32 object-cover rounded-full">
                    </div>

                    <form class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4" action="" method="POST">
                        <div>
                            <label class="block font-medium mb-1">เลขประจำตัวนักศึกษา</label>
                            <input type="text" name="student_id" value="<?= $id; ?>" class="border p-2 w-full bg-gray-100 text-gray-500 cursor-not-allowed" readonly>
                        </div>

                        <div>
                            <label class="block font-medium mb-1">ชื่อ - สกุล</label>
                            <input type="text" name="name" value="<?= $iname; ?>" class="border p-2 w-full">
                        </div>

                        <div>
                            <label class="block font-medium mb-1">คณะ</label>
                            <input type="text" name="faculty" value="<?= $faculty; ?>" class="border p-2 w-full bg-gray-100 text-gray-500 cursor-not-allowed" readonly>
                        </div>

                        <div>
                            <label class="block font-medium mb-1">สาขาวิชา</label>
                            <input type="text" name="field" value="<?= $field; ?>" class="border p-2 w-full bg-gray-100 text-gray-500 cursor-not-allowed" readonly>
                        </div>

                        <div>
                            <label class="block font-medium mb-1">ชั้นปี</label>
                            <input type="text" name="academic_level" value="<?= $academicLevel; ?>" class="border p-2 w-full bg-gray-100 text-gray-500 cursor-not-allowed" readonly>
                        </div>

                        <div>
                            <label class="block font-medium mb-1">อีเมล</label>
                            <input type="email" name="email" value="<?= $email; ?>" class="border p-2 w-full bg-gray-100 text-gray-500 cursor-not-allowed" readonly>
                        </div>

                        <div>
                            <label class="block font-medium mb-1">สิทธิ์การใช้งาน</label>
                            <input type="text" name="role" value="<?= $role; ?>" class="border p-2 w-full bg-gray-100 text-gray-500 cursor-not-allowed" readonly>
                        </div>

                        <div>
                            <label class="block font-medium mb-1">อาจารย์ที่ปรึกษา *</label>
                            <select name="teacher_email" class="w-full border rounded px-3 py-2 bg-white text-gray-800" required>

                                <?php foreach ($advisors as $advisor): ?>
                                    <option value="<?php echo htmlspecialchars($advisor['email']); ?>">
                                        <?php echo htmlspecialchars($advisor['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block font-medium mb-1">หัวหน้าสาขา</label>
                            <select name="head_department" class="border p-2 w-full bg-gray-100 text-gray-500 cursor-not-allowed">
                                <?php foreach ($heads as $head): ?>
                                    <option value="<?php echo htmlspecialchars($head['email']); ?>" readonly>
                                        <?php echo htmlspecialchars($head['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>



                        <div class="col-span-2 text-center mt-4">
                            <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-md">บันทึก</button>
                        </div>
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
</body>

</html>
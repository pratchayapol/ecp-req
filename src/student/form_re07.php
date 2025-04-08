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


// ตรวจสอบว่ามีการส่งคำขอจากการเลือกวิชา
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

    // ส่งข้อมูลกลับในรูป JSON
    echo json_encode($courseInfo);
    exit; // ปิดสคริปต์หลังส่งข้อมูล
}



// ตรวจสอบการส่งแบบ POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // ดึงค่าจากฟอร์ม
        $semester = $_POST['semester'] ?? '';
        $academicYear = $_POST['academicYear'] ?? '';
        $course_id = $_POST['course_id'] ?? '';
        $academicGroup = $_POST['academicGroup'] ?? '';
        $reason = $_POST['reason'] ?? '';
        $other_reason = $_POST['other_reason'] ?? null;
        $GPA = $_POST['GPA'] ?? '';
        $gpa_all = $_POST['gpa_all'] ?? '';
        $reg_status = $_POST['reg_status'] ?? '';
        $Yearend = $_POST['Yearend'] ?? '';
        $email_advisor_comment = $_POST['email_advisor_comment'] ?? '';
        $status = NULL;  // Set default status to 0 if not provided

        // หากเลือกเหตุผล "อื่นๆ" ให้ใช้ค่าที่กรอกเพิ่ม
        $final_reason = ($reason === 'other') ? $other_reason : $reason;

        // เตรียมคำสั่ง SQL
        $stmt = $pdo->prepare("INSERT INTO form_re07 (
            term, year, course_id, `Group`, reason, gpa, gpa_all, reg_status, expected_graduation, email, status, email_advisor_comment
        ) VALUES (
            :term, :year, :course_id, :group, :reason, :GPA, :gpa_all, :reg_status, :Yearend, :email, :status, :email_advisor_comment
        )");

        // bindParam และ execute
        $stmt->execute([
            ':term' => $semester,
            ':year' => $academicYear,
            ':course_id' => $course_id,
            ':group' => $academicGroup,
            ':reason' => $final_reason,
            ':GPA' => $GPA,
            ':gpa_all' => $gpa_all,
            ':reg_status' => $reg_status,
            ':Yearend' => $Yearend,
            ':email' => $email,
            ':status' => $status,
            ':email_advisor_comment' => $email_advisor_comment
        ]);

        echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body>";
        echo "
        <script>
        Swal.fire({
            title: 'สำเร็จ!',
            text: 'บันทึกข้อมูลเรียบร้อยแล้ว',
            icon: 'success',
            confirmButtonText: 'ตกลง'
        }).then(() => {
            window.location.href = 'form_all';
        });
        </script>
        ";
        echo "</body></html>";
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


// ดึงเฉพาะผู้ที่เป็นอาจารย์ (role = 'Teacher')
$stmt = $pdo->prepare("SELECT name, email FROM accounts WHERE role = 'Teacher' ORDER BY name ASC");
$stmt->execute();
$advisors = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แบบฟอร์ม RE.07</title>
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
    <?php include '../loadtab/h.php'; ?>
    <div class="flex flex-col sm:flex-row h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="sm:w-1/4 md:w-1/5 bg-white shadow-lg p-4 m-6 flex flex-col justify-between rounded-[20px]">
            <div class="text-center">
                <img src="/image/logo.png" class="w-24 h-24 sm:w-32 sm:h-32 rounded-full shadow-lg mx-auto" alt="Logo">

                <div class="mt-4 space-y-2">
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-center py-2 px-4 rounded-[12px] shadow-md" id="dashboard-btn"> Dashboard </button>
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="re6">คำร้องขอเพิ่มที่นั่ง RE.06</button>
                    <button class="w-full bg-[#EF6526] text-white hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="re7">คำร้องขอเปิดนอกแผน RE.07</button>
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
                    <h1 class="text-orange-500 bg-white p-2 text-xl h-12 font-bold shadow-md rounded-[12px] text-center">แบบฟอร์มคำร้องขอเปิดนอกแผน RE.07</h1>

                    <form class="space-y-4 m-6" action="" method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-medium mb-1 text-red-600">คำร้องขอเปิดนอกแผนการเรียน ภาคเรียนที่ *</label>
                                <select class="w-full border rounded px-3 py-2" name="semester" required>
                                    <option value="" disabled selected>เลือกภาคเรียน</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium mb-1 text-red-600">ปีการศึกษาที่ *</label>
                                <select class="w-full border rounded px-3 py-2" name="academicYear" id="academicYear" required>
                                    <option value="" disabled selected>เลือกปีการศึกษา</option>
                                </select>
                            </div>
                        </div>

                        <script>
                            const currentYearBE = new Date().getFullYear() + 543;
                            const academicYearSelect = document.getElementById('academicYear');
                            for (let i = currentYearBE - 1; i <= currentYearBE; i++) {
                                const option = document.createElement('option');
                                option.value = i;
                                option.textContent = i;
                                academicYearSelect.appendChild(option);
                            }
                        </script>

                        <div>
                            <label class="block font-medium mb-1 text-red-600">รายวิชาที่ต้องการขอเปิดนอกแผนการเรียน *</label>
                            <select class="w-full border rounded px-3 py-2" name="course_id" id="courseSelect" required onchange="loadCourseInfo(this.value)">
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
                                <select class="w-full border rounded px-3 py-2" name="academicGroup" id="academicGroup" required>
                                    <option value="" disabled selected>เลือกกลุ่มเรียน</option>
                                </select>
                            </div>
                        </div>

                        <script>
                            const academicGroupSelect = document.getElementById('academicGroup');
                            const groups = ['ECP/N', 'ECP/R', 'ECP/Q'];
                            for (let i = 0; i <= 8; i++) {
                                const yearBE = currentYearBE - i;
                                groups.forEach(group => {
                                    const option = document.createElement('option');
                                    option.value = `${group}(${yearBE.toString().slice(-2)})`;
                                    option.textContent = option.value;
                                    academicGroupSelect.appendChild(option);
                                });
                            }
                        </script>

                        <div>
                            <label class="block font-medium mb-1 text-red-600">ขอเปิดนอกแผนการเรียน เนื่องจาก *</label>
                            <select class="w-full border rounded px-3 py-2" name="reason" id="reason-select" required onchange="toggleOtherReason()">
                                <option value="" disabled selected>เลือกเหตุผลที่ขอเพิ่มที่นั่ง</option>
                                <option value="เป็นรายวิชาตามแผนการเรียนที่ต้องเรียนในภาคการศึกษานี้เพื่อสำเร็จการศึกษา">เป็นรายวิชาตามแผนการเรียนที่ต้องเรียนในภาคการศึกษานี้เพื่อสำเร็จการศึกษา</option>
                                <option value="ต้องการเรียนเพื่อเสริมความรู้และทักษะที่จำเป็นสำหรับการทำงานในอนาคต">ต้องการเรียนเพื่อเสริมความรู้และทักษะที่จำเป็นสำหรับการทำงานในอนาคต</option>
                                <option value="วิชานี้เป็นพื้นฐานสำหรับการเรียนวิชาอื่นๆ ที่สำคัญในหลักสูตร">วิชานี้เป็นพื้นฐานสำหรับการเรียนวิชาอื่นๆ ที่สำคัญในหลักสูตร</option>
                                <option value="ไม่สามารถลงเรียนในภาคเรียนอื่นได้ เนื่องจากการวางแผนการเรียนให้สอดคล้องกับการจบการศึกษา">ไม่สามารถลงเรียนในภาคเรียนอื่นได้ เนื่องจากการวางแผนการเรียนให้สอดคล้องกับการจบการศึกษา</option>
                                <option value="มีความจำเป็นต้องเรียนในวิชานี้เพื่อไม่ให้การศึกษาล่าช้าและสำเร็จการศึกษาในเวลาที่กำหนด">มีความจำเป็นต้องเรียนในวิชานี้เพื่อไม่ให้การศึกษาล่าช้าและสำเร็จการศึกษาในเวลาที่กำหนด</option>
                                <option value="other">อื่นๆ (โปรดระบุ)</option>
                            </select>

                            <div id="other-reason-container" class="mt-2 hidden">
                                <input type="text" name="other_reason" id="other-reason-input" class="w-full border rounded px-3 py-2" placeholder="กรุณาระบุเหตุผลอื่นๆ">
                            </div>
                        </div>

                        <script>
                            function toggleOtherReason() {
                                const select = document.getElementById('reason-select');
                                const otherContainer = document.getElementById('other-reason-container');
                                const otherInput = document.getElementById('other-reason-input');

                                if (select.value === 'other') {
                                    otherContainer.classList.remove('hidden');
                                    otherInput.setAttribute('required', 'required');
                                } else {
                                    otherContainer.classList.add('hidden');
                                    otherInput.removeAttribute('required');
                                }
                            }
                        </script>

                        <div class="flex items-center gap-2">
                            <label class="block font-medium text-red-600">หน่วยกิต GPA ปัจจุบัน *</label>
                            <input type="number" name="GPA" id="GPA" required step="0.01" min="1.75" max="4.00" class="border rounded px-2 py-1 w-20" />
                        </div>

                        <script>
                            const gpaInput = document.getElementById('GPA');
                            gpaInput.addEventListener('input', function() {
                                let value = parseFloat(gpaInput.value);
                                if (isNaN(value) || value < 1.75) {
                                    gpaInput.value = "1.75";
                                } else if (value > 4.00) {
                                    gpaInput.value = "4.00";
                                } else {
                                    gpaInput.value = value.toFixed(2);
                                }
                            });
                        </script>

                        <div class="flex items-center gap-2">
                            <label class="block font-medium text-red-600">จำนวนหน่วยกิตที่ลงทะเบียนในภาคการศึกษานี้ (รวมรายวิชาที่ขอเปิดด้วย) *</label>
                            <input type="number" name="gpa_all" id="gpa_all" required step="0.01" min="1.75" max="4.00" class="border rounded px-2 py-1 w-20" />
                        </div>

                        <script>
                            const gpaAllInput = document.getElementById('gpa_all');
                            gpaAllInput.addEventListener('input', function() {
                                let value = parseFloat(gpaAllInput.value);
                                if (isNaN(value) || value < 1.75) {
                                    gpaAllInput.value = "1.75";
                                } else if (value > 4.00) {
                                    gpaAllInput.value = "4.00";
                                } else {
                                    gpaAllInput.value = value.toFixed(2);
                                }
                            });
                        </script>

                        <div>
                            <label class="block font-medium mb-2 text-red-600">สถานภาพการลงทะเบียนวิชาที่ขอเพิ่มที่นั่ง *</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="reg_status" value="ลงปกติ" required />
                                    ลงปกติ
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="reg_status" value="รีเกรด" />
                                    รีเกรด
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="reg_status" value="ซ่อม" />
                                    ซ่อม
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block font-medium mb-1 text-red-600">ภาคการศึกษาที่คาดว่าจะสำเร็จการศึกษา *</label>
                            <select class="w-full border rounded px-3 py-2" name="Yearend" id="Yearend" required>
                                <option value="" disabled selected>เลือกภาคเรียน/ปีการศึกษา</option>
                            </select>
                        </div>

                        <script>
                            const yearEndSelect = document.getElementById('Yearend');
                            const currentYear = new Date().getFullYear();
                            const currentYearBE1 = currentYear + 543;

                            for (let year = currentYearBE1 - 1; year <= currentYearBE1 + 2; year++) {
                                for (let term = 1; term <= 3; term++) {
                                    const option = document.createElement('option');
                                    option.value = `${term}/${year}`;
                                    option.textContent = `${term}/${year}`;
                                    yearEndSelect.appendChild(option);
                                }
                            }
                        </script>

                        <div>
                            <label class="block font-medium mb-1 text-red-600">อาจารย์ที่ปรึกษา *</label>
                            <select class="w-full border rounded px-3 py-2" name="email_advisor_comment" required>
                                <option value="" disabled selected>เลือกอาจารย์ที่ปรึกษา</option>
                                <?php foreach ($advisors as $advisor): ?>
                                    <option value="<?= htmlspecialchars($advisor['email']) ?>">
                                        <?= htmlspecialchars($advisor['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
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
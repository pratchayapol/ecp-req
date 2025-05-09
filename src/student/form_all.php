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


function getNameByEmail($pdo, $email)
{
    $stmt2 = $pdo->prepare("SELECT name FROM accounts WHERE email = :email LIMIT 1");
    $stmt2->execute(['email' => $email]);
    $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    return $result2 ? $result2['name'] : 'ไม่พบชื่อ';
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คำร้องที่ขอของนักศึกษา</title>
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
                    <button class="w-full bg-[#EF6526] text-white hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="form_all">คำร้องของนักศึกษา</button>
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
                    <h1 class="text-orange-500 bg-white p-2 text-xl h-12 font-bold shadow-md rounded-[12px] text-center">คำร้องที่ขอของนักศึกษา</h1>
                    <div class="m-6">
                        <div class="mb-4 border-b border-gray-200">
                            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
                                <li class="me-2" role="presentation">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="re01-tab" data-tabs-target="#re01" type="button" role="tab" aria-controls="re01" aria-selected="false" onclick="selectTab()">คำร้องทั่วไป RE.01</button>
                                </li>
                                <li class="me-2" role="presentation">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="re06-tab" data-tabs-target="#re06" type="button" role="tab" aria-controls="re06" aria-selected="false" onclick="selectTab()">คำร้องขอเพิ่มที่นั่ง RE.06</button>
                                </li>
                                <li class="me-2" role="presentation">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="re07-tab" data-tabs-target="#re07" type="button" role="tab" aria-controls="re07" aria-selected="false" onclick="selectTab()">คำร้องขอเปิดนอกแผนการเรียน RE.07</button>
                                </li>
                            </ul>
                        </div>





























                        <div id="default-tab-content">
                            <p id="alert-message" class="text-red-500 font-bold text-center">โปรดเลือก Tab คำร้อง</p>
                            <div class="hidden p-4 rounded-lg bg-gray-50" id="re01" role="tabpanel" aria-labelledby="re01-tab">
                                <!-- Filters -->
                                <div class="flex items-center gap-4 mb-4 justify-center">
                                    <div>
                                        <label class="mr-2">สถานะคำร้อง:</label>
                                        <select id="statusFilter1" class="border px-3 py-2 rounded">
                                            <option value="" disabled selected>เลือกสถานะคำร้อง</option>
                                            <option value="null">รอดำเนินการ</option>
                                            <option value="1">ที่ปรึกษาพิจารณาแล้ว</option>
                                            <option value="2">หัวหน้าสาขาพิจารณาแล้ว</option>
                                        </select>
                                    </div>
                                    <button class="bg-gray-600 text-white px-4 py-2 rounded" onclick="clearFilters1()">ล้างข้อมูล</button>
                                </div>
                                <!-- Table -->
                                <table class="min-w-full table-auto border-collapse rounded-[12px]">
                                    <thead class="bg-orange-500 text-white text-center shadow-md">
                                        <tr>
                                            <th class="px-4 py-2">เลขคำร้อง</th>
                                            <!-- <th class="px-4 py-2">ชื่อ - สกุล</th> -->
                                            <th class="px-4 py-2">เรื่อง</th>
                                            <th class="px-4 py-2">เรียน</th>
                                            <th class="px-4 py-2">สถานะคำร้อง</th>
                                            <th class="px-4 py-2">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // การดึงข้อมูลจากฐานข้อมูล
                                        try {
                                            $stmt1 = $pdo->prepare("SELECT * FROM form_re01 WHERE email = :email ORDER BY form_id DESC");
                                            $stmt1->execute(['email' => $email]);
                                            $forms1 = $stmt1->fetchAll();
                                        } catch (PDOException $e) {
                                            echo "Database error: " . $e->getMessage();
                                            exit;
                                        }

                                        if (!empty($forms1)): ?>
                                            <?php foreach ($forms1 as $row1): ?>
                                                <tr data-status="<?= $row1['status'] === null ? 'null' : $row1['status'] ?>">
                                                    <td class="px-4 py-2 text-center"><?= htmlspecialchars('RE.01' . '-' . $row1['form_id']) ?></td>
                                                    <!-- <td class="px-4 py-2 text-center"><?= htmlspecialchars(getNameByEmail($pdo, $row1['email'])) ?></td> -->
                                                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row1['title']) ?></td>
                                                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row1['to']) ?></td>
                                                    <td class="px-4 py-2 text-center 
        <?= $row1['status'] === null ? 'text-red-600' : ($row1['status'] == 1 ? 'text-yellow-600' : ($row1['status'] == 2 ? 'text-green-600' : '')) ?>">
                                                        <?= $row1['status'] === null ? 'รอดำเนินการ' : ($row1['status'] == 1 ? 'ที่ปรึกษาพิจารณาแล้ว' : ($row1['status'] == 2 ? 'หัวหน้าสาขาพิจารณาแล้ว' : '')) ?>
                                                    </td>

                                                    <td class="px-4 py-2 text-center">
                                                        <?php
                                                        try {
                                                            // ดึงชื่ออาจารย์ที่ปรึกษา
                                                            $stmt = $pdo->prepare("SELECT name FROM accounts WHERE email = :email LIMIT 1");
                                                            $stmt->execute(['email' => $row1['teacher_email']]);
                                                            $advisor = $stmt->fetch(PDO::FETCH_ASSOC);

                                                            // ดึงชื่อหัวหน้าสาขา
                                                            $stmt = $pdo->prepare("SELECT name FROM accounts WHERE email = :email LIMIT 1");
                                                            $stmt->execute(['email' => $row1['head_department']]);
                                                            $head = $stmt->fetch(PDO::FETCH_ASSOC);
                                                        } catch (PDOException $e) {
                                                            $advisor['name'] = 'เกิดข้อผิดพลาด';
                                                            $head['name'] = 'เกิดข้อผิดพลาด';
                                                            error_log("PDO Error: " . $e->getMessage());
                                                        }

                                                        ?>
                                                        <button
                                                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded open-modal1"
                                                            data-id="<?= $row1['form_id'] ?>"
                                                            data-name="<?= htmlspecialchars(getNameByEmail($pdo, $row1['email'])) ?>"
                                                            data-title="<?= htmlspecialchars($row1['title']) ?>"
                                                            data-to="<?= htmlspecialchars($row1['to']) ?>"
                                                            data-request="<?= htmlspecialchars($row1['request_text']) ?>"
                                                            data-advisor-comment="<?= htmlspecialchars($row1['comment_teacher'] ?? 'จึงเรียนมาเพื่อโปรดพิจารณา') ?>"
                                                            data-advisor-name="<?= htmlspecialchars($advisor['name']) ?>"
                                                            data-head-comment="<?= htmlspecialchars($row1['comment_head_dep'] ?? 'จึงเรียนมาเพื่อโปรดพิจารณา') ?>"
                                                            data-head-name="<?= htmlspecialchars($head['name']) ?>">ดูรายละเอียด</button>
                                                    </td>

                                                </tr>


                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-gray-500 py-4">ไม่พบข้อมูล</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>


                                <!-- ข้อความแสดงเมื่อกรองแล้วไม่พบข้อมูล -->
                                <div id="noDataMessage1" class="text-center text-gray-500 py-4" style="display: none;">ไม่พบข้อมูลที่ตรงกับเงื่อนไข</div>
                                <br>


                            </div>

                            <!-- Modal Background -->
                            <div id="detailModal1" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center transition-opacity duration-300 ease-in-out">

                                <!-- Modal Content -->
                                <div id="modalContent1" class="bg-white rounded-2xl shadow-2xl w-[90%] max-w-2xl p-6 relative transform scale-95 opacity-0 transition-all duration-300 ease-in-out">
                                    <button id="closeModal1" class="absolute top-3 right-4 text-gray-500 hover:text-red-500 text-2xl font-bold">&times;</button>
                                    <h2 class="text-2xl font-semibold text-center mb-6 text-orange-600">รายละเอียดคำร้อง</h2>

                                    <div class="space-y-4 text-base">
                                        <p><strong>เลขคำร้องที่:</strong> <span id="modalFormId1"></span></p>
                                        <p><strong>ชื่อ - สกุล นักศึกษา:</strong> <span id="modalName"></span></p>
                                        <p><strong>เรื่อง:</strong> <span id="modalTitle"></span></p>
                                        <p><strong>เรียน:</strong> <span id="modalTo"></span></p>
                                        <p><strong>มีความประสงค์:</strong></p>
                                        <textarea id="modalRequest" readonly
                                            class="w-full mt-1 p-2 border border-gray-300 rounded-md resize-none bg-gray-100"
                                            rows="2"></textarea>
                                        <hr>
                                        <p><strong>ความคิดเห็นของที่ปรึกษา:</strong></span></p>
                                        <textarea id="modalAdvisorComment" readonly
                                            class="w-full mt-1 p-2 border border-gray-300 rounded-md resize-none bg-gray-100"
                                            rows="2"></textarea>
                                        <p><strong>ชื่ออาจารย์ที่ปรึกษา:</strong> <span id="modalAdvisorName"></span></p>
                                        <hr>
                                        <p><strong>ความคิดเห็นของหัวหน้าสาขา:</strong></p>
                                        <textarea id="modalHeadComment" readonly
                                            class="w-full mt-1 p-2 border border-gray-300 rounded-md resize-none bg-gray-100"
                                            rows="2"></textarea>
                                        <p><strong>ชื่อหัวหน้าสาขา:</strong> <span id="modalHeadName"></span></p>
                                    </div>
                                    <div id="statusStepper1" class="flex justify-between items-center my-4">
                                        <!-- Step 1 -->
                                        <div class="flex flex-col items-center">
                                            <div id="step1Circle" class="w-8 h-8 rounded-full border-2 border-gray-400 flex items-center justify-center text-gray-500">1</div>
                                            <span class="mt-1 text-sm text-gray-600 text-center">รอพิจารณาคำร้อง</span>
                                        </div>

                                        <div class="flex-auto h-0.5 bg-gray-300 mx-1" id="line1"></div>

                                        <!-- Step 2 -->
                                        <div class="flex flex-col items-center">
                                            <div id="step2Circle" class="w-8 h-8 rounded-full border-2 border-gray-400 flex items-center justify-center text-gray-500">2</div>
                                            <span class="mt-1 text-sm text-gray-600 text-center">ที่ปรึกษาพิจารณาแล้ว</span>
                                        </div>

                                        <div class="flex-auto h-0.5 bg-gray-300 mx-1" id="line2"></div>

                                        <!-- Step 3 -->
                                        <div class="flex flex-col items-center">
                                            <div id="step3Circle" class="w-8 h-8 rounded-full border-2 border-gray-400 flex items-center justify-center text-gray-500">3</div>
                                            <span class="mt-1 text-sm text-gray-600 text-center">หัวหน้าสาขาพิจารณาแล้ว</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- แถบสถานะ RE01 -->
                            <script>
                                function updateStatusStepper1(status) {
                                    const steps = [{
                                            circle: 'step1Circle',
                                            line: 'line1'
                                        },
                                        {
                                            circle: 'step2Circle',
                                            line: 'line2'
                                        },
                                        {
                                            circle: 'step3Circle',
                                            line: 'line3'
                                        }
                                    ];

                                    steps.forEach((step, i) => {
                                        // อัปเดตวงกลม
                                        document.getElementById(step.circle).className = 'w-8 h-8 rounded-full border-2 flex items-center justify-center ' +
                                            (i <= status ? 'border-green-500 bg-green-500 text-white' : 'border-gray-400 text-gray-500');

                                        // อัปเดตเส้นเชื่อม
                                        if (step.line) {
                                            document.getElementById(step.line).className = 'flex-auto h-0.5 mx-1 ' + (i < status ? 'bg-green-500' : 'bg-gray-300');
                                        }
                                    });
                                }

                                // ใช้เมื่อเปิด modal:
                                document.querySelectorAll('.open-modal1').forEach(btn => {
                                    btn.addEventListener('click', function() {
                                        const status = parseInt(this.closest('tr').dataset.status) || 0;
                                        updateStatusStepper1(status);
                                    });
                                });
                            </script>



                            <script>
                                const modal = document.getElementById('detailModal1');
                                const modalContent1 = document.getElementById('modalContent1');
                                const closeModal1 = document.getElementById('closeModal1');

                                document.querySelectorAll('.open-modal1').forEach(button => {
                                    button.addEventListener('click', () => {
                                        // ใส่ข้อมูลใน modal
                                        document.getElementById('modalFormId1').textContent = 'RE.01-' + button.dataset.id;
                                        document.getElementById('modalName').textContent = button.dataset.name;
                                        document.getElementById('modalTitle').textContent = button.dataset.title;
                                        document.getElementById('modalTo').textContent = button.dataset.to;
                                        document.getElementById('modalRequest').textContent = button.dataset.request;
                                        document.getElementById('modalAdvisorComment').textContent = button.dataset.advisorComment;
                                        document.getElementById('modalAdvisorName').textContent = button.dataset.advisorName;
                                        document.getElementById('modalHeadComment').textContent = button.dataset.headComment;
                                        document.getElementById('modalHeadName').textContent = button.dataset.headName;

                                        // เปิด modal พร้อม transition
                                        modal.classList.remove('hidden');
                                        setTimeout(() => {
                                            modalContent1.classList.remove('opacity-0', 'scale-95');
                                        }, 10);
                                    });
                                });

                                closeModal1.addEventListener('click', () => {
                                    // ปิด transition
                                    modalContent1.classList.add('opacity-0', 'scale-95');
                                    setTimeout(() => {
                                        modal.classList.add('hidden');
                                    }, 300); // ตรงกับ duration ใน tailwind (300ms)
                                });
                            </script>




























                            <!-- ตาราง RE06 -->
                            <div class="hidden p-4 rounded-lg bg-gray-50" id="re06" role="tabpanel" aria-labelledby="re06-tab">
                                <!-- Filter -->
                                <div class="flex items-center gap-4 mb-4 justify-center">
                                    <div>
                                        <label class="mr-2">สถานะคำร้อง:</label>
                                        <select id="statusFilter2" class="border px-3 py-2 rounded">
                                            <option value="" disabled selected>เลือกสถานะคำร้อง</option>
                                            <option value="null">รอดำเนินการ</option>
                                            <option value="1">อาจารย์ประจำรายวิชาพิจารณาแล้ว</option>
                                        </select>
                                    </div>
                                    <button class="bg-gray-600 text-white px-4 py-2 rounded" onclick="clearFilters2()">ล้างข้อมูล</button>
                                </div>

                                <!-- Table -->
                                <table class="min-w-full table-auto border-collapse rounded-[12px]">
                                    <thead class="bg-orange-500 text-white text-center shadow-md">
                                        <tr>
                                            <th class="px-4 py-2">เลขคำร้อง</th>
                                            <th class="px-4 py-2">ภาคเรียน/ปีการศึกษา</th>
                                            <th class="px-4 py-2">รายวิชา</th>
                                            <th class="px-4 py-2">กลุ่มเรียน</th>
                                            <th class="px-4 py-2">สถานะคำร้อง</th>
                                            <th class="px-4 py-2">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {
                                            $stmt2 = $pdo->prepare("SELECT 'RE06' as form_type, form_id as form_id, term, year, f.course_id, `Group`, status, reason, coutter, 	reg_status, teacher_email,
                                            c.course_nameTH, c.credits
                                            FROM form_re06 AS f
                                            LEFT JOIN course AS c ON f.course_id = c.course_id
                                            WHERE f.email = :email
                                            ORDER BY form_id DESC");
                                            $stmt2->execute(['email' => $email]);
                                            $forms2 = $stmt2->fetchAll();
                                        } catch (PDOException $e) {
                                            echo "Database error: " . $e->getMessage();
                                            exit;
                                        }

                                        if (!empty($forms2)) :
                                            foreach ($forms2 as $row2) :
                                                $statusText = $row2['status'] === null ? 'รอดำเนินการ' : ($row2['status'] == 1 ? 'อนุมัติแล้ว' : 'ไม่อนุมัติ');
                                                $statusClass = $row2['status'] === null ? 'text-gray-600' : ($row2['status'] == 1 ? 'text-green-600' : 'text-orange-600');
                                        ?>
                                                <tr data-status="<?= $row2['status'] === null ? 'null' : $row2['status'] ?>">
                                                    <td class="px-4 py-2 text-center"><?= htmlspecialchars('RE.06' . '-' . $row2['form_id']) ?></td>
                                                    <!-- <td class="px-4 py-2 text-center"><?= htmlspecialchars(getNameByEmail($pdo, $row2['email'])) ?></td> -->
                                                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row2['term'] . ' / ' . $row2['year']) ?></td>
                                                    <td class="px-4 py-2"><?= htmlspecialchars($row2['course_id'] . ' ' . $row2['course_nameTH'] . ' (' . $row2['credits'] . ' หน่วยกิต)') ?></td>
                                                    <td class="text-center px-4 py-2"><?= $row2['Group'] ?? '-' ?></td>
                                                    <td class="text-center px-4 py-2 <?= $statusClass ?>"><?= $statusText ?></td>
                                                    <?php
                                                    try {

                                                        // ดึงชื่ออาจารย์ประจำวิชา
                                                        $stmt = $pdo->prepare("SELECT name FROM accounts WHERE email = :email LIMIT 1");
                                                        $stmt->execute(['email' => $row2['teacher_email']]);
                                                        $CommentTeacher = $stmt->fetch(PDO::FETCH_ASSOC);
                                                    } catch (PDOException $e) {
                                                        echo "Database error: " . $e->getMessage();
                                                        exit;
                                                    }
                                                    ?>
                                                    <td class="text-center px-4 py-2">
                                                        <button class="open-modal2 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded"
                                                            data-form-id="<?= $row2['form_id'] ?>"
                                                            data-term="<?= $row2['term'] ?>"
                                                            data-year="<?= $row2['year'] ?>"
                                                            data-reason="<?= htmlspecialchars($row2['reason']) ?>"
                                                            data-group="<?= htmlspecialchars($row2['Group'] ?? '-') ?>"
                                                            data-course-id="<?= htmlspecialchars($row2['course_id'] . ' ' . $row2['course_nameTH'] . ' (' . $row2['credits'] . ' หน่วยกิต)') ?>"
                                                            data-counter="<?= $row2['coutter'] ?? '-' ?>"
                                                            data-reg-status="<?= $row2['reg_status'] ?? '-' ?>"
                                                            data-comment-teacher="<?= $row2['comment_teacher'] ?? 'จึงเรียนมาเพื่อโปรดพิจารณา' ?>"
                                                            data-teacher-email="<?= htmlspecialchars($CommentTeacher['name']) ?>">
                                                            ดูรายละเอียด
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach;
                                        else : ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-gray-500 py-4">ไม่พบข้อมูล</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>


                                <!-- Modal -->
                                <div id="detailModal2" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center transition-opacity duration-300 ease-in-out">
                                    <div id="modalContent2" class="bg-white rounded-xl w-[90%] max-w-2xl p-6 relative transform scale-95 opacity-0 transition-all duration-300 ease-in-out">
                                        <button id="closeModal2" class="absolute top-3 right-4 text-gray-600 hover:text-red-500 text-2xl">&times;</button>
                                        <h2 class="text-2xl text-center font-semibold text-orange-600 mb-6">รายละเอียดคำร้อง</h2>
                                        <div class="space-y-3 text-base">
                                            <p><strong>เลขคำร้องที่:</strong> <span id="modalFormId2"></span></p>
                                            <p><strong>ภาคเรียน/ปีการศึกษา:</strong> <span id="modalTermYear"></span></p>
                                            <p><strong>เหตุผลเนื่องจาก:</strong></p>
                                            <textarea id="modalReason" readonly
                                                class="w-full mt-1 p-2 border border-gray-300 rounded-md resize-none bg-gray-100"
                                                rows="2"></textarea>
                                            <p><strong>กลุ่มเรียน:</strong> <span id="modalGroup"></span></p>
                                            <p><strong>รหัสรายวิชา:</strong> <span id="modalCourseId"></span></p>
                                            <p><strong>ยอดลงทะเบียน:</strong> <span id="modalCounter"></span> <strong>คน</strong></p>
                                            <p><strong>ประเภทการลงทะเบียนเรียน:</strong> <span id="modalRegStatus"></span></p>
                                            <p><strong>ความคิดเห็นของอาจารย์:</strong></p>
                                            <textarea id="modalCommentTeacher" readonly
                                                class="w-full mt-1 p-2 border border-gray-300 rounded-md resize-none bg-gray-100"
                                                rows="2"></textarea>

                                            <p><strong>อีเมลอาจารย์:</strong> <span id="modalTeacherEmail"></span></p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    const modal2 = document.getElementById('detailModal2');
                                    const modalContent2 = document.getElementById('modalContent2');
                                    const closeModal2 = document.getElementById('closeModal2');

                                    document.querySelectorAll('.open-modal2').forEach(button => {
                                        button.addEventListener('click', function() {
                                            // ใส่ข้อมูล
                                            document.getElementById('modalFormId2').textContent = 'RE.06-' + this.dataset.formId;
                                            document.getElementById('modalTermYear').textContent = this.dataset.term + ' / ' + this.dataset.year;
                                            document.getElementById('modalReason').textContent = this.dataset.reason || '-';
                                            document.getElementById('modalGroup').textContent = this.dataset.group || '-';
                                            document.getElementById('modalCourseId').textContent = this.dataset.courseId || '-';
                                            document.getElementById('modalCounter').textContent = this.dataset.counter || '-';
                                            document.getElementById('modalRegStatus').textContent = this.dataset.regStatus || '-';
                                            document.getElementById('modalCommentTeacher').textContent = this.dataset.commentTeacher || '-';
                                            document.getElementById('modalTeacherEmail').textContent = this.dataset.teacherEmail || '-';

                                            // แสดง modal พร้อม animation
                                            modal2.classList.remove('hidden');
                                            setTimeout(() => {
                                                modalContent2.classList.remove('opacity-0', 'scale-95');
                                            }, 10); // ให้เวลา browser render ก่อนเอา class ออก
                                        });
                                    });

                                    closeModal2.addEventListener('click', function() {
                                        // ซ่อน modal พร้อม animation
                                        modalContent2.classList.add('opacity-0', 'scale-95');
                                        setTimeout(() => {
                                            modal2.classList.add('hidden');
                                        }, 300); // duration ต้องตรงกับ tailwind (300ms)
                                    });
                                });
                            </script>























                            <div class="hidden p-4 rounded-lg bg-gray-50" id="re07" role="tabpanel" aria-labelledby="re07-tab">
                                <!-- Filters -->
                                <div class="flex items-center gap-4 mb-4 justify-center">
                                    <div>
                                        <label class="mr-2">สถานะคำร้อง:</label>
                                        <select id="statusFilter3" class="border px-3 py-2 rounded">
                                            <option value="" disabled selected>เลือกสถานะคำร้อง</option>
                                            <option value="null">รอดำเนินการ</option>
                                            <option value="1">ที่ปรึกษาพิจารณาแล้ว</option>
                                            <option value="2">หัวหน้าสาขาพิจารณาแล้ว</option>
                                        </select>
                                    </div>
                                    <button class="bg-gray-600 text-white px-4 py-2 rounded" onclick="clearFilters3()">ล้างข้อมูล</button>
                                </div>



                                <!-- Table -->
                                <table class="min-w-full table-auto border-collapse rounded-[12px]">
                                    <thead class="bg-orange-500 text-white text-center shadow-md">
                                        <tr>
                                            <th class="px-4 py-2">เลขคำร้อง</th>
                                            <!-- <th class="px-4 py-2">ชื่อ - สกุล</th> -->
                                            <th class="px-4 py-2">ภาคเรียน/ปีการศึกษา</th>
                                            <th class="px-4 py-2">รายวิชา</th>
                                            <th class="px-4 py-2">กลุ่มเรียน</th>
                                            <th class="px-4 py-2">สถานะคำร้อง</th>
                                            <th class="px-4 py-2">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // การดึงข้อมูลจากฐานข้อมูล
                                        try {
                                            $stmt3 = $pdo->prepare("SELECT 'RE07' as form_type, form_id as form_id, term, year, f.course_id, `group`, status, 
                                            c.course_nameTH, c.credits
                                            FROM form_re07 AS f
                                            LEFT JOIN course AS c ON f.course_id = c.course_id
                                            WHERE f.email = :email
                                            ORDER BY form_id DESC");
                                            $stmt3->execute(['email' => $email]);
                                            $forms3 = $stmt3->fetchAll();
                                        } catch (PDOException $e) {
                                            echo "Database error: " . $e->getMessage();
                                            exit;
                                        }

                                        if (!empty($forms3)): ?>
                                            <?php foreach ($forms3 as $row3): ?>
                                                <tr data-status="<?= $row3['status'] === null ? 'null' : $row3['status'] ?>">
                                                    <td class="px-4 py-2 text-center"><?= htmlspecialchars('RE.07' . '-' . $row3['form_id']) ?></td>
                                                    <!-- <td class="px-4 py-2 text-center"><?= htmlspecialchars(getNameByEmail($pdo, $row3['email'])) ?></td> -->
                                                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row3['term'] . ' / ' . $row3['year']) ?></td>
                                                    <td class="px-4 py-2"><?= htmlspecialchars($row3['course_id'] . ' ' . $row3['course_nameTH'] . ' (' . $row3['credits'] . ' หน่วยกิต)') ?></td>
                                                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row3['group'] ?? $row3['academic_group']) ?></td>
                                                    <td class="px-4 py-2 text-center <?= $row3['status'] === null ? 'text-gray-600' : ($row3['status'] == 1 ? 'text-green-600' : 'text-orange-600') ?>">
                                                        <?= $row3['status'] === null ? 'รอดำเนินการ' : ($row3['status'] == 1 ? 'อนุมัติแล้ว' : 'ไม่อนุมัติ') ?>
                                                    </td>
                                                    <td class="px-4 py-2 text-center">
                                                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">ดูรายละเอียด</button>
                                                    </td>

                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-gray-500 py-4">ไม่พบข้อมูล</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                                <!-- ข้อความแสดงเมื่อกรองแล้วไม่พบข้อมูล -->
                                <div id="noDataMessage3" class="text-center text-gray-500 py-4" style="display: none;">ไม่พบข้อมูลที่ตรงกับเงื่อนไข</div>
                                <br>
                            </div>

                        </div>
                        <script>
                            function selectTab() {
                                // เมื่อมีการกดเลือก Tab ให้ซ่อนข้อความเตือน
                                const alertMessage = document.getElementById('alert-message');
                                if (alertMessage) {
                                    alertMessage.style.display = 'none';
                                }
                            }
                        </script>
                        <script>
                            // Get all tab buttons
                            const tabs = document.querySelectorAll('[role="tab"]');

                            // Get all tab panels
                            const tabPanels = document.querySelectorAll('[role="tabpanel"]');

                            // Function to switch active tab
                            function switchTab(event) {
                                // Remove the 'aria-selected' attribute and 'border-b-2' class from all tabs
                                tabs.forEach(tab => {
                                    tab.setAttribute('aria-selected', 'false');
                                    tab.classList.remove('border-b-2', 'text-gray-800');
                                    tab.classList.add('text-gray-500');
                                });

                                // Add the 'aria-selected' attribute and 'border-b-2' class to the clicked tab
                                const clickedTab = event.target;
                                clickedTab.setAttribute('aria-selected', 'true');
                                clickedTab.classList.add('border-b-2', 'text-gray-800');
                                clickedTab.classList.remove('text-gray-500');

                                // Hide all tab panels
                                tabPanels.forEach(panel => {
                                    panel.classList.add('hidden');
                                });

                                // Show the clicked tab's corresponding panel
                                const targetPanel = document.querySelector(clickedTab.dataset.tabsTarget);
                                targetPanel.classList.remove('hidden');
                            }

                            // Add event listeners to all tabs
                            tabs.forEach(tab => {
                                tab.addEventListener('click', switchTab);
                            });
                        </script>


                        <!-- Filter 1 -->
                        <script>
                            function filterTable1() {
                                const statusFilter1 = document.getElementById('statusFilter1').value;
                                const rows = document.querySelectorAll('table tbody tr');
                                let noDataFound = true;

                                rows.forEach(row => {
                                    const status = row.dataset.status;
                                    let showRow = true;

                                    if (statusFilter1 && status !== statusFilter1) {
                                        showRow = false;
                                    }

                                    row.style.display = showRow ? '' : 'none';
                                    if (showRow) noDataFound = false;
                                });

                                const noDataMessage = document.getElementById('noDataMessage1');
                                if (noDataMessage) {
                                    noDataMessage.style.display = noDataFound ? '' : 'none';
                                }
                            }

                            function clearFilters1() {
                                document.getElementById('statusFilter1').value = '';
                                filterTable1();
                            }

                            document.getElementById('statusFilter1').addEventListener('change', filterTable1);
                        </script>


                        <!-- Filter 2 -->
                        <script>
                            function filterTable2() {
                                const statusFilter2 = document.getElementById('statusFilter2').value;
                                const rows = document.querySelectorAll('table tbody tr');
                                let noDataFound = true;

                                rows.forEach(row => {
                                    const status = row.dataset.status;
                                    let showRow = true;

                                    if (statusFilter2 && status !== statusFilter2) {
                                        showRow = false;
                                    }

                                    row.style.display = showRow ? '' : 'none';
                                    if (showRow) noDataFound = false;
                                });

                                const noDataMessage = document.getElementById('noDataMessage2');
                                if (noDataMessage) {
                                    noDataMessage.style.display = noDataFound ? '' : 'none';
                                }
                            }

                            function clearFilters2() {
                                document.getElementById('statusFilter2').value = '';
                                filterTable2();
                            }

                            document.getElementById('statusFilter2').addEventListener('change', filterTable2);
                        </script>

                        <!-- Filter 3 -->
                        <script>
                            function filterTable3() {
                                const statusFilter3 = document.getElementById('statusFilter3').value;
                                const rows = document.querySelectorAll('table tbody tr');
                                let noDataFound = true;

                                rows.forEach(row => {
                                    const status = row.dataset.status;
                                    let showRow = true;

                                    if (statusFilter3 && status !== statusFilter3) {
                                        showRow = false;
                                    }

                                    row.style.display = showRow ? '' : 'none';
                                    if (showRow) noDataFound = false;
                                });

                                const noDataMessage = document.getElementById('noDataMessage3');
                                if (noDataMessage) {
                                    noDataMessage.style.display = noDataFound ? '' : 'none';
                                }
                            }


                            function clearFilters3() {
                                document.getElementById('statusFilter3').value = '';
                                filterTable3();
                            }

                            document.getElementById('statusFilter3').addEventListener('change', filterTable3);
                        </script>


                        <br>
                    </div>

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
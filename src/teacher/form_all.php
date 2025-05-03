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
} else {
    header('location: ../session_timeout');
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
                    <button class="w-full bg-[#EF6526] text-white hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="form_all">คำร้องที่ขอของนักศึกษา</button>
                </div>
            </div>
            <div class="text-center mt-4">
                <div class="flex items-center justify-center sm:justify-start space-x-2">
                    <div class="bg-gray-300 rounded-full w-10 h-10 flex items-center justify-center">
                        <img src="<?= $picture ?>" alt="Profile Picture" class="w-10 h-10 rounded-full object-cover">
                    </div>
                    <span class="text-sm sm:text-base"><?= $iname; ?></span>
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
                    <h1 class="text-orange-500 bg-white p-2 text-xl h-12 font-bold shadow-md rounded-[12px] text-center">คำร้องที่ขอของนักศึกษา</h1>
                    <div class="m-6">
                        <div class="mb-4 border-b border-gray-200">
                            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
                                <li class="me-2" role="presentation">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="re01-tab" data-tabs-target="#re01" type="button" role="tab" aria-controls="re01" aria-selected="false">คำร้องทั่วไป RE.01</button>
                                </li>
                                <li class="me-2" role="presentation">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="re06-tab" data-tabs-target="#re06" type="button" role="tab" aria-controls="re06" aria-selected="false">คำร้องขอเพิ่มที่นั่ง RE.06</button>
                                </li>
                                <li class="me-2" role="presentation">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="re07-tab" data-tabs-target="#re07" type="button" role="tab" aria-controls="re07" aria-selected="false">คำร้องขอเปิดนอกแผนการเรียน RE.07</button>
                                </li>
                            </ul>
                        </div>
                        <div id="default-tab-content">
                            <div class="hidden p-4 rounded-lg bg-gray-50" id="re01" role="tabpanel" aria-labelledby="re01-tab">
                                <!-- Filters -->
                                <div class="flex items-center gap-4 mb-4 justify-center">
                                    <div>
                                        <label class="mr-2">สถานะคำร้อง:</label>
                                        <select id="statusFilter1" class="border px-3 py-2 rounded">
                                            <option value="" disabled selected>เลือกสถานะคำร้อง</option>
                                            <option value="">รอดำเนินการ</option>
                                            <option value="1">อนุมัติ</option>
                                            <option value="2">ไม่อนุมัติ</option>
                                        </select>
                                    </div>
                                    <button class="bg-gray-600 text-white px-4 py-2 rounded" onclick="clearFilters()">ล้างข้อมูล</button>
                                </div>

                                <script>
                                    // ฟังก์ชันสำหรับกรองข้อมูล
                                    function filterTable1() {
                                        const statusFilter1 = document.getElementById('statusFilter1').value;
                                        const rows = document.querySelectorAll('table tbody tr');
                                        let noDataFound = true; // เพิ่มตัวแปรเพื่อตรวจสอบว่ามีข้อมูลที่ตรงกับเงื่อนไขหรือไม่

                                        rows.forEach(row => {
                                            const status = row.cells[4].textContent.trim(); // ดึงข้อมูลสถานะคำร้อง

                                            let showRow = true;

                                            // ตรวจสอบสถานะคำร้อง
                                            if (statusFilter1 && statusFilter1 !== '' && !status.includes(statusFilter1)) {
                                                showRow = false;
                                            }

                                            // ซ่อนหรือแสดงแถวตามผลการกรอง
                                            if (showRow) {
                                                row.style.display = '';
                                                noDataFound = false; // ถ้าพบข้อมูลที่ตรงกับเงื่อนไข
                                            } else {
                                                row.style.display = 'none';
                                            }
                                        });

                                        // แสดงข้อความ "ไม่พบข้อมูล" ถ้าไม่มีข้อมูลที่ตรงกับเงื่อนไข
                                        const noDataMessage = document.getElementById('noDataMessage1');
                                        if (noDataFound) {
                                            noDataMessage.style.display = ''; // แสดงข้อความ "ไม่พบข้อมูล"
                                        } else {
                                            noDataMessage.style.display = 'none'; // ซ่อนข้อความ
                                        }
                                    }

                                    // ฟังก์ชันสำหรับล้างข้อมูล
                                    function clearFilters() {
                                        document.getElementById('statusFilter1').value = '';
                                        filterTable1(); // ใช้ฟังก์ชันกรองที่ถูกต้อง
                                    }

                                    // ผูกฟังก์ชันกับอีเวนต์ของ dropdowns
                                    document.getElementById('statusFilter1').addEventListener('change', filterTable1);
                                </script>

                                <!-- Table -->
                                <table class="min-w-full table-auto border-collapse rounded-[12px]">
                                    <thead class="bg-orange-500 text-white text-center shadow-md">
                                        <tr>
                                            <th class="px-4 py-2">เลขคำร้อง</th>
                                            <th class="px-4 py-2">เรื่อง</th>
                                            <th class="px-4 py-2">เรียน</th>
                                            <th class="px-4 py-2">เหตุผล</th>
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
                                                <tr>
                                                    <td class="px-4 py-2 text-center"><?= htmlspecialchars('RE.01' . '-' . $row1['form_id']) ?></td>
                                                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row1['title']) ?></td>
                                                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row1['to']) ?></td>
                                                    <td class="px-4 py-2 text-center" style="width: 150px;">
                                                        <!-- ปุ่มสำหรับดูเหตุผล -->
                                                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded view-reason" data-reason="<?= htmlspecialchars($row1['request_text']) ?>">ดูเหตุผล</button>
                                                    </td>
                                                    <td class="px-4 py-2 text-center <?= $row1['status'] === null ? 'text-gray-600' : ($row1['status'] == 1 ? 'text-green-600' : 'text-orange-600') ?>">
                                                        <?= $row1['status'] === null ? 'รอดำเนินการ' : ($row1['status'] == 1 ? 'อนุมัติแล้ว' : 'ไม่อนุมัติ') ?>
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
                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                <script>
                                    // เพิ่ม event listener ให้กับปุ่ม "ดูเหตุผล"
                                    document.querySelectorAll('.view-reason').forEach(button => {
                                        button.addEventListener('click', function() {
                                            const reason = this.getAttribute('data-reason');
                                            // ใช้ SweetAlert แสดงข้อความ
                                            Swal.fire({
                                                title: 'เหตุผลการขออนุมัติ',
                                                text: reason,
                                                icon: 'info',
                                                confirmButtonText: 'ปิด'
                                            });
                                        });
                                    });
                                </script>

                                <!-- ข้อความแสดงเมื่อกรองแล้วไม่พบข้อมูล -->
                                <div id="noDataMessage1" class="text-center text-gray-500 py-4" style="display: none;">ไม่พบข้อมูลที่ตรงกับเงื่อนไข</div>
                                <br>


                            </div>




                            <div class="hidden p-4 rounded-lg bg-gray-50" id="re06" role="tabpanel" aria-labelledby="re06-tab">
                                <!-- Filters -->
                                <div class="flex items-center gap-4 mb-4 justify-center">
                                    <div>
                                        <label class="mr-2">สถานะคำร้อง:</label>
                                        <select id="statusFilter2" class="border px-3 py-2 rounded">
                                            <option value="" disabled selected>เลือกสถานะคำร้อง</option>
                                            <option value="">รอดำเนินการ</option>
                                            <option value="1">อนุมัติ</option>
                                            <option value="2">ไม่อนุมัติ</option>
                                        </select>
                                    </div>
                                    <button class="bg-gray-600 text-white px-4 py-2 rounded" onclick="clearFilters()">ล้างข้อมูล</button>
                                </div>

                                <script>
                                    // ฟังก์ชันสำหรับกรองข้อมูล
                                    function filterTable2() {
                                        const statusFilter2 = document.getElementById('statusFilter2').value;
                                        const rows = document.querySelectorAll('table tbody tr');
                                        let noDataFound = true; // เพิ่มตัวแปรเพื่อตรวจสอบว่ามีข้อมูลที่ตรงกับเงื่อนไขหรือไม่

                                        rows.forEach(row => {
                                            const status = row.cells[4].textContent;

                                            let showRow = true;

                                            // ตรวจสอบสถานะคำร้อง
                                            if (statusFilter2 && statusFilter2 !== '' && !status.includes(statusFilter2)) {
                                                showRow = false;
                                            }

                                            // ซ่อนหรือแสดงแถวตามผลการกรอง
                                            if (showRow) {
                                                row.style.display = '';
                                                noDataFound = false; // ถ้าพบข้อมูลที่ตรงกับเงื่อนไข
                                            } else {
                                                row.style.display = 'none';
                                            }
                                        });

                                        // แสดงข้อความ "ไม่พบข้อมูล" ถ้าไม่มีข้อมูลที่ตรงกับเงื่อนไข
                                        const noDataMessage = document.getElementById('noDataMessage2');
                                        if (noDataFound) {
                                            noDataMessage.style.display = ''; // แสดงข้อความ "ไม่พบข้อมูล"
                                        } else {
                                            noDataMessage.style.display = 'none'; // ซ่อนข้อความ
                                        }
                                    }

                                    // ฟังก์ชันสำหรับล้างข้อมูล
                                    function clearFilters() {
                                        document.getElementById('statusFilter2').value = '';
                                        filterTable2();
                                    }

                                    // ผูกฟังก์ชันกับอีเวนต์ของ dropdowns
                                    document.getElementById('statusFilter2').addEventListener('change', filterTable2);
                                </script>

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
                                        // การดึงข้อมูลจากฐานข้อมูล
                                        try {
                                            $stmt2 = $pdo->prepare("SELECT 'RE06' as form_type, form_id as form_id, term, year, f.course_id, `group`, status, 
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

                                        if (!empty($forms2)): ?>
                                            <?php foreach ($forms2 as $row2): ?>
                                                <tr>
                                                    <td class="px-4 py-2 text-center"><?= htmlspecialchars('RE.06' . '-' . $row2['form_id']) ?></td>
                                                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row2['term'] . ' / ' . $row2['year']) ?></td>
                                                    <td class="px-4 py-2"><?= htmlspecialchars($row2['course_id'] . ' ' . $row2['course_nameTH'] . ' (' . $row2['credits'] . ' หน่วยกิต)') ?></td>
                                                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row2['group'] ?? $row2['academic_group']) ?></td>
                                                    <td class="px-4 py-2 text-center <?= $row2['status'] === null ? 'text-gray-600' : ($row2['status'] == 1 ? 'text-green-600' : 'text-orange-600') ?>">
                                                        <?= $row2['status'] === null ? 'รอดำเนินการ' : ($row2['status'] == 1 ? 'อนุมัติแล้ว' : 'ไม่อนุมัติ') ?>
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
                                <div id="noDataMessage2" class="text-center text-gray-500 py-4" style="display: none;">ไม่พบข้อมูลที่ตรงกับเงื่อนไข</div>
                                <br>

                            </div>
                            <div class="hidden p-4 rounded-lg bg-gray-50" id="re07" role="tabpanel" aria-labelledby="re07-tab">
                                 <!-- Filters -->
                                 <div class="flex items-center gap-4 mb-4 justify-center">
                                    <div>
                                        <label class="mr-2">สถานะคำร้อง:</label>
                                        <select id="statusFilter3" class="border px-3 py-2 rounded">
                                            <option value="" disabled selected>เลือกสถานะคำร้อง</option>
                                            <option value="">รอดำเนินการ</option>
                                            <option value="1">อนุมัติ</option>
                                            <option value="2">ไม่อนุมัติ</option>
                                        </select>
                                    </div>
                                    <button class="bg-gray-600 text-white px-4 py-2 rounded" onclick="clearFilters()">ล้างข้อมูล</button>
                                </div>

                                <script>
                                    // ฟังก์ชันสำหรับกรองข้อมูล
                                    function filterTable3() {
                                        const statusFilter3 = document.getElementById('statusFilter3').value;
                                        const rows = document.querySelectorAll('table tbody tr');
                                        let noDataFound = true; // เพิ่มตัวแปรเพื่อตรวจสอบว่ามีข้อมูลที่ตรงกับเงื่อนไขหรือไม่

                                        rows.forEach(row => {
                                            const status = row.cells[4].textContent;

                                            let showRow = true;

                                            // ตรวจสอบสถานะคำร้อง
                                            if (statusFilter3 && statusFilter3 !== '' && !status.includes(statusFilter3)) {
                                                showRow = false;
                                            }

                                            // ซ่อนหรือแสดงแถวตามผลการกรอง
                                            if (showRow) {
                                                row.style.display = '';
                                                noDataFound = false; // ถ้าพบข้อมูลที่ตรงกับเงื่อนไข
                                            } else {
                                                row.style.display = 'none';
                                            }
                                        });

                                        // แสดงข้อความ "ไม่พบข้อมูล" ถ้าไม่มีข้อมูลที่ตรงกับเงื่อนไข
                                        const noDataMessage = document.getElementById('noDataMessage3');
                                        if (noDataFound) {
                                            noDataMessage.style.display = ''; // แสดงข้อความ "ไม่พบข้อมูล"
                                        } else {
                                            noDataMessage.style.display = 'none'; // ซ่อนข้อความ
                                        }
                                    }

                                    // ฟังก์ชันสำหรับล้างข้อมูล
                                    function clearFilters() {
                                        document.getElementById('statusFilter3').value = '';
                                        filterTable3();
                                    }

                                    // ผูกฟังก์ชันกับอีเวนต์ของ dropdowns
                                    document.getElementById('statusFilter3').addEventListener('change', filterTable3);
                                </script>

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
                                                <tr>
                                                    <td class="px-4 py-2 text-center"><?= htmlspecialchars('RE.07' . '-' . $row3['form_id']) ?></td>
                                                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row3['term'] . ' / ' . $row3['year']) ?></td>
                                                    <td class="px-4 py-2"><?= htmlspecialchars($row3['course_id'] . ' ' . $row3['course_nameTH'] . ' (' . $row3['credits'] . ' หน่วยกิต)') ?></td>
                                                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row3['group'] ?? $row3['academic_group']) ?></td>
                                                    <td class="px-4 py-2 text-center <?= $row3['status'] === null ? 'text-gray-600' : ($row3['status'] == 1 ? 'text-green-600' : 'text-orange-600') ?>">
                                                        <?= $row3['status'] === null ? 'รอดำเนินการ' : ($row3['status'] == 1 ? 'อนุมัติแล้ว' : 'ไม่อนุมัติ') ?>
                                                    </td>
                                                    <td class="px-4 py-2 text-center">
<!-- Modal toggle -->
<button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
  Toggle modal
</button>
<!-- Main modal -->
<div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Create New Product
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="crud-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form class="p-4 md:p-5">
                <div class="grid gap-4 mb-4 grid-cols-2">
                    <div class="col-span-2">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                        <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Type product name" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Price</label>
                        <input type="number" name="price" id="price" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="$2999" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category</label>
                        <select id="category" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">Select category</option>
                            <option value="TV">TV/Monitors</option>
                            <option value="PC">PC</option>
                            <option value="GA">Gaming/Console</option>
                            <option value="PH">Phones</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Product Description</label>
                        <textarea id="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write product description here"></textarea>                    
                    </div>
                </div>
                <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                    Add new product
                </button>
            </form>
        </div>
    </div>
</div> 
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
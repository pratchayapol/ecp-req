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
    <div class="flex flex-col sm:flex-row h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="sm:w-1/4 md:w-1/5 bg-white shadow-lg p-4 m-6 flex flex-col justify-between rounded-[20px]">
            <div class="text-center">
                <img src="/image/logo.png" class="w-24 h-24 sm:w-32 sm:h-32 rounded-full shadow-lg mx-auto" alt="Logo">

                <div class="mt-4 space-y-2">
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-center py-2 px-4 rounded-[12px] shadow-md" id="dashboard-btn"> Dashboard </button>
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="re6">คำร้องขอเพิ่มที่นั่ง RE.06</button>
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="re7">คำร้องขอเปิดนอกแผน RE.07</button>
                    <button class="w-full bg-[#EF6526] text-white hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md" id="form_all">คำร้องที่ขอของนักศึกษา</button>
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
                    <h1 class="text-orange-500 bg-white p-2 text-xl h-12 font-bold shadow-md rounded-[12px] text-center">คำร้องที่ขอของนักศึกษา</h1>

                    <!-- Filters -->
                    <div class="flex items-center gap-4 mb-4 justify-center">
                        <div>
                            <label class="mr-2">ประเภทคำร้อง:</label>
                            <select id="typeFilter" class="border px-3 py-2 rounded">
                                <option value="">เลือกประเภทคำร้อง</option>
                                <option value="re6">คำร้องขอเพิ่มที่นั่ง</option>
                                <option value="re7">คำร้องขอเปิดนอกแผนการเรียน</option>
                            </select>
                        </div>
                        <div>
                            <label class="mr-2">สถานะคำร้อง:</label>
                            <select id="statusFilter" class="border px-3 py-2 rounded">
                                <option value="">เลือกสถานะคำร้อง</option>
                                <option value="1">รออนุมัติ</option>
                                <option value="2">อนุมัติ</option>
                                <option value="3">ไม่อนุมัติ</option>
                            </select>
                        </div>
                        <button id="clearFilters" class="bg-gray-600 text-white px-4 py-2 rounded">ล้างข้อมูล</button>

                        <script>
                            document.getElementById('clearFilters').addEventListener('click', () => {
                                document.getElementById('typeFilter').value = '';
                                document.getElementById('statusFilter').value = '';
                                const email = $email;
                                window.location.href = `?email=${email}`;
                            });
                        </script>

                    </div>

                    <script>
                        document.querySelectorAll('#typeFilter, #statusFilter').forEach(select => {
                            select.addEventListener('change', () => {
                                const type = document.getElementById('typeFilter').value;
                                const status = document.getElementById('statusFilter').value;
                                const email = $email;

                                const query = new URLSearchParams({
                                    email,
                                    type,
                                    status
                                }).toString();
                                window.location.href = `?${query}`;
                            });
                        });
                    </script>



                    <!-- Table -->
                    <div class="overflow-x-auto w-full">
                        <table class="min-w-full table-auto border rounded overflow-hidden">
                            <thead class="bg-orange-500 text-white text-left">
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


                                if ($type === 're6') {
                                    $sql = "SELECT CONCAT('RE06-', form_re06_id) AS req_id, CONCAT(term, '/', year) AS term_year, 
                   course_id, `group` AS class_group, status 
            FROM form_re06 
            WHERE email = :email";

                                    if ($status !== '') {
                                        $sql .= " AND status = :status";
                                    }

                                    $stmt = $pdo->prepare($sql);
                                    $stmt->bindParam(':email', $email);
                                    if ($status !== '') {
                                        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
                                    }
                                    $stmt->execute();
                                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                } elseif ($type === 're7') {
                                    $sql = "SELECT CONCAT('RE07-', form_re07_id) AS req_id, CONCAT(semester, '/', academic_year) AS term_year, 
                   course_id, academic_group AS class_group, reg_status 
            FROM form_re07 
            WHERE email = :email";

                                    $stmt = $pdo->prepare($sql);
                                    $stmt->bindParam(':email', $email);
                                    $stmt->execute();
                                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    // Mapping reg_status string to status number if needed
                                    if ($status !== '') {
                                        $results = array_filter($results, function ($row) use ($status) {
                                            $map = [
                                                'รออนุมัติ' => 1,
                                                'อนุมัติ' => 2,
                                                'ไม่อนุมัติ' => 3
                                            ];
                                            return isset($map[$row['reg_status']]) && $map[$row['reg_status']] == $status;
                                        });
                                    }
                                }

                                foreach ($requests as $request):
                                    $code = $request['req_id'];
                                    $term = $request['term_year'];
                                    $subject = $request['course_id'];
                                    $section = $request['class_group'];
                                    $status_text = $type === 're7' ? $request['reg_status'] : $request['status'];
                                ?>
                                    <tr class="<?= $status_text == 'ไม่อนุมัติ' ? 'bg-orange-100' : 'bg-white' ?>">
                                        <td class="px-4 py-2"><?= $code ?></td>
                                        <td class="px-4 py-2"><?= $term ?></td>
                                        <td class="px-4 py-2"><?= $subject ?></td>
                                        <td class="px-4 py-2"><?= $section ?></td>
                                        <td class="px-4 py-2 <?= $status_text == 'อนุมัติ' ? 'text-green-600' : ($status_text == 'ไม่อนุมัติ' ? 'text-orange-600' : 'text-gray-600') ?>">
                                            <?= $status_text ?>
                                        </td>
                                        <td class="px-4 py-2">
                                            <a href="detail.php?id=<?= $request['req_id'] ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">ดูรายละเอียด</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                

                            </tbody>
                        </table>
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
</body>

</html>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'connect/dbcon.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    // echo "Token ที่ได้รับคือ: " . htmlspecialchars($token);
} else {
    echo "ไม่พบ token ใน URL";
}


?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECP Online Petition</title>
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

<body class="flex items-center justify-center min-h-screen bg t1">
    <?php include './loadtab/h.php';

    try {
        $stmt = $pdo->prepare("SELECT * FROM form_re01 WHERE token = :token");
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            extract($row);
            $form_id = $row['form_id'];
            $title = $row['title'];
            $to = $row['to'];
            $email = $row['email'];
            $faculty = $row['faculty'];
            $field = $row['field'];
            $course_level = $row['course_level'];
            $request_text = $row['request_text'];
            $comment_teacher = $row['comment_teacher'];
            $comment_head_dep = $row['comment_head_dep'];
            $status = $row['status'];
            $created_at = $row['created_at'];
            $token = $row['token'];
            $teacher_email = $row['teacher_email'];
            $head_department = $row['head_department'];

            // ดึงชื่อตัวเอง
            $sql = "SELECT name, email, id FROM accounts WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['email' => $email]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
            <div class="w-full max-w-3xl p-8 m-6 bg-white rounded-2xl shadow-2xl transform transition duration-500 hover:scale-105">
                <div class="flex flex-col space-y-6 text-gray-800">
                    <!-- Header -->
                    <div class="text-center">
                        <h2 class="text-3xl font-extrabold text-gray-800">คำร้องทั่วไป RE.01</h2>
                    </div>

                    <!-- Form Information -->
                    <div class="w-full bg-gray-50 rounded-xl p-6 shadow-sm space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><span class="font-semibold">FORM ID:</span> RE.01-<?php echo htmlspecialchars($form_id); ?></div>
                            <div><span class="font-semibold">เรื่อง:</span> <?php echo htmlspecialchars($title); ?></div>
                            <div><span class="font-semibold">เรียน:</span> <?php echo htmlspecialchars($to); ?></div>
                            <div><span class="font-semibold">ชื่อนักศึกษา:</span> <?php echo htmlspecialchars($profile['name']); ?></div>
                            <div><span class="font-semibold">รหัสนักศึกษา:</span> <?php echo $profile['id']; ?></div>
                            <div><span class="font-semibold">คณะ:</span> <?php echo htmlspecialchars($faculty); ?></div>
                            <div><span class="font-semibold">สาขา:</span> <?php echo htmlspecialchars($field); ?></div>
                            <div><span class="font-semibold">ระดับชั้น:</span> <?php echo htmlspecialchars($course_level); ?></div>
                        </div>
                        <!-- Request Text -->
                        <div>
                            <label for="request_text" class="font-semibold block mb-1">ข้อความร้องขอ:</label>
                            <textarea id="request_text" name="request_text" rows="3" class="w-full text-gray-600 border rounded p-2 bg-gray-100 cursor-default" readonly><?php echo htmlspecialchars($request_text); ?></textarea>
                        </div>
                        <hr>
                        <form method="POST" action="" onsubmit="return validateForm()">
                            <!-- Approval Section -->
                            <div class="space-y-3 mb-6">
                                <div>
                                    <label class="font-semibold block mb-1">ความคิดเห็นอาจารย์:</label>
                                    <div class="flex items-center space-x-4">
                                        <label class="flex items-center space-x-2">
                                            <input type="radio" name="approval_status" value="1">
                                            <span>อนุมัติ</span>
                                        </label>
                                        <label class="flex items-center space-x-2">
                                            <input type="radio" name="approval_status" value="0">
                                            <span>ไม่อนุมัติ</span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label for="comment_teacher" class="font-semibold block mb-1">คำอธิบายเพิ่มเติม (ถ้ามี):</label>
                                    <textarea id="comment_teacher" name="comment_teacher" rows="3"
                                        class="w-full text-gray-600 border rounded p-2"
                                        placeholder="โปรดกรอกความคิดเห็นของท่าน"></textarea>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center">
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-xl shadow">
                                    พิจารณาแล้ว
                                </button>
                            </div>
                        </form>

                        <script>
                            function validateForm() {
                                const radios = document.getElementsByName('approval_status');
                                const comment = document.getElementById('comment_teacher').value.trim();

                                let selected = false;
                                for (let i = 0; i < radios.length; i++) {
                                    if (radios[i].checked) {
                                        selected = true;
                                        break;
                                    }
                                }

                                if (!selected) {
                                    alert('กรุณาเลือกผลการพิจารณา (อนุมัติ หรือ ไม่อนุมัติ)');
                                    return false;
                                }

                                if (comment === '') {
                                    alert('กรุณากรอกความคิดเห็นของอาจารย์');
                                    return false;
                                }

                                return true;
                            }
                        </script>

                    </div>

                    <!-- Status Stepper -->
                    <div class="w-full">
                        <span class="font-semibold">สถานะ:</span>
                        <div id="statusStepper1" class="flex justify-between items-center my-4">
                            <!-- Step 1 -->
                            <div class="flex flex-col items-center">
                                <div id="step1Circle1" class="w-8 h-8 rounded-full border-2 border-gray-400 flex items-center justify-center text-gray-500">1</div>
                                <span class="mt-1 text-sm text-gray-600 text-center">รอพิจารณาคำร้อง</span>
                            </div>

                            <div class="flex-auto h-0.5 bg-gray-300 mx-1" id="line11"></div>

                            <!-- Step 2 -->
                            <div class="flex flex-col items-center">
                                <div id="step2Circle1" class="w-8 h-8 rounded-full border-2 border-gray-400 flex items-center justify-center text-gray-500">2</div>
                                <span class="mt-1 text-sm text-gray-600 text-center">ที่ปรึกษาพิจารณาแล้ว</span>
                            </div>

                            <div class="flex-auto h-0.5 bg-gray-300 mx-1" id="line12"></div>

                            <!-- Step 3 -->
                            <div class="flex flex-col items-center">
                                <div id="step3Circle1" class="w-8 h-8 rounded-full border-2 border-gray-400 flex items-center justify-center text-gray-500">3</div>
                                <span class="mt-1 text-sm text-gray-600 text-center">หัวหน้าสาขาพิจารณาแล้ว</span>
                            </div>
                        </div>
                    </div>

                    <!-- Metadata -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                        <div><span class="font-semibold">วันที่สร้าง:</span> <?php echo htmlspecialchars($created_at); ?></div>
                        <div><span class="font-semibold">อีเมลอาจารย์:</span> <?php echo htmlspecialchars($teacher_email); ?></div>
                    </div>
                </div>
            </div>


    <?php
        } else {
            echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'ไม่พบข้อมูล',
            text: 'ไม่พบข้อมูลที่ตรงกับ token นี้',
            confirmButtonText: 'กลับหน้าหลัก',
            allowOutsideClick: false
        }).then((result) => {
            // ไม่ว่าจะกดปุ่มไหนหรือปิด popup ก็ redirect
            window.location.href = 'index';
        });
    </script>
    ";
        }
    } catch (PDOException $e) {
        echo "เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: " . $e->getMessage();
    }
    ?>


    <!-- JavaScript for stepper -->
    <script>
        function updateStatusStepper1(status) {
            const steps = [{
                    circle: 'step1Circle1',
                    line: 'line11'
                },
                {
                    circle: 'step2Circle1',
                    line: 'line12'
                },
                {
                    circle: 'step3Circle1',
                    line: null
                }
            ];

            steps.forEach((step, i) => {
                const circle = document.getElementById(step.circle);
                const line = step.line ? document.getElementById(step.line) : null;

                if (circle) {
                    circle.className = 'w-8 h-8 rounded-full border-2 flex items-center justify-center ' +
                        (i <= status ? 'border-green-500 bg-green-500 text-white' : 'border-gray-400 text-gray-500');
                }

                if (line) {
                    line.className = 'flex-auto h-0.5 mx-1 ' + (i < status ? 'bg-green-500' : 'bg-gray-300');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const currentStatus = <?php echo (int)$status; ?>;
            updateStatusStepper1(currentStatus);
        });
    </script>


    <?php
    // ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // รับค่าจากฟอร์ม
        $approvalStatus = $_POST['approval_status'];  // approved หรือ not_approved
        $commentTeacher = $_POST['comment_teacher'];  // คำอธิบายเพิ่มเติม
        $status = 2;
        $token = $_GET['token'];  // รับค่า token จาก URL หรือในกรณีที่ใช้เป็น hidden field

        // SQL Query เพื่อ update ข้อมูลในฐานข้อมูล
        $sql = "UPDATE form_re01 SET approval_status_teacher = ?, comment_teacher = ? , status = ? WHERE token = ?";

        // เตรียมคำสั่ง SQL
        if ($stmt = $conn->prepare($sql)) {
            // ผูกตัวแปรกับคำสั่ง SQL
            $stmt->bind_param("ssss", $approvalStatus, $commentTeacher, $status, $token);

            // รันคำสั่ง SQL
            if ($stmt->execute()) {
                echo "ข้อมูลถูกอัปเดตเรียบร้อยแล้ว!";
            } else {
                echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $conn->error;
            }

            // ปิดการเตรียมคำสั่ง
            $stmt->close();
        } else {
            echo "เกิดข้อผิดพลาดในการเตรียมคำสั่ง: " . $conn->error;
        }

        // ปิดการเชื่อมต่อ
        $conn->close();
    }
    ?>

    <?php include './loadtab/f.php'; ?>
</body>

</html>
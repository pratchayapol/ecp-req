<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'connect/dbcon.php';


?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECP Online Petition RE06</title>
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

    if (isset($_GET['token'])) {
        $token = $_GET['token'];


        try {
            $stmt = $pdo->prepare("SELECT * FROM form_re06 WHERE token = :token");
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                extract($row);
                $form_id = $row['form_id'];
                $term = $row['term'];
                $year = $row['year'];
                $reason = $row['reason'];
                $group = $row['Group']; // ชื่อคอลัมน์คือ 'Group' (G ใหญ่)
                $course_id = $row['course_id'];
                $course_nameTH = $row['course_nameTH'];
                $coutter = $row['coutter'];
                $reg_status = $row['reg_status'];
                $status = $row['status'];
                $comment_teacher = $row['comment_teacher'];
                $created_at = $row['created_at'];
                $email = $row['email'];
                $token = $row['token'];
                $teacher_email = $row['teacher_email'];

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
                            <h5 class="text-2xl font-extrabold text-gray-800">คำร้องขอเพิ่มที่นั่ง RE.06</h5>
                        </div>

                        <!-- Form Information -->
                        <div class="w-full bg-gray-50 rounded-xl p-6 shadow-sm space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div><span class="font-semibold">FORM ID:</span> RE.06-<?php echo htmlspecialchars($form_id); ?></div>
                                <div><span class="font-semibold">ชื่อ:</span> <?php echo htmlspecialchars($profile['name']); ?></div>
                                <div><span class="font-semibold">รหัสนักศึกษา:</span> <?php echo $profile['id']; ?></div>
                                <div><span class="font-semibold">รหัสวิชา:</span> <?php echo htmlspecialchars($course_id); ?></div>
                                <div><span class="font-semibold">ชื่อวิชา:</span> <?php echo htmlspecialchars($course_nameTH); ?></div>
                                <div><span class="font-semibold">กลุ่มเรียน:</span> <?php echo htmlspecialchars($group); ?></div>
                                <div><span class="font-semibold">ภาคเรียน:</span> <?php echo htmlspecialchars($term); ?></div>
                                <div><span class="font-semibold">ปีการศึกษา:</span> <?php echo htmlspecialchars($year); ?></div>
                                <div><span class="font-semibold">ประเภทการลงทะเบียน:</span> <?php echo htmlspecialchars($reg_status); ?></div>
                                <div><span class="font-semibold">ยอดลงทะเบียนปัจจุบัน:</span> <?php echo htmlspecialchars($coutter); ?> <span class="font-semibold"> คน</span></div>

                            </div>

                            <div><span class="font-semibold">เหตุผลในการขอเพิ่มที่นั่ง:</span><textarea id="request_text" name="request_text" rows="2"
                                    class="w-full text-gray-600 border rounded p-2 bg-gray-100 cursor-default" readonly><?php echo htmlspecialchars($reason); ?></textarea></div>

                            <hr>

                            <form method="POST" action="" onsubmit="return validateForm()">
                                <!-- Approval Section -->
                                <div class="space-y-3 mb-6">
                                    <div>
                                        <label class="font-semibold block mb-1">ความคิดเห็นของอาจารย์ประจำรายวิชา:</label>
                                        <div class="flex items-center space-x-4">
                                            <?php if (is_null($status)): ?>
                                                <label class="flex items-center space-x-2">
                                                    <input type="radio" name="approval_status" value="1">
                                                    <span>อนุมัติ</span>
                                                </label>
                                                <label class="flex items-center space-x-2">
                                                    <input type="radio" name="approval_status" value="0">
                                                    <span>ไม่อนุมัติ</span>
                                                </label>
                                            <?php else: ?>
                                                <?php if ($approval_status_teacher == 1): ?>
                                                    <span class="text-green-600 font-semibold">อนุมัติ</span>
                                                <?php else: ?>
                                                    <span class="text-red-600 font-semibold">ไม่อนุมัติ</span>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                        </div>
                                    </div>

                                    <div>
                                        <label for="comment_teacher" class="font-semibold block mb-1">คำอธิบายเพิ่มเติม (ถ้ามี):</label>
                                        <?php if (is_null($status)): ?>
                                            <textarea id="comment_teacher" name="comment_teacher" rows="2"
                                                class="w-full text-gray-600 border rounded p-2"
                                                placeholder="โปรดกรอกความคิดเห็นของท่าน"><?= htmlspecialchars($comment_teacher ?? '') ?></textarea>
                                        <?php else: ?>
                                            <textarea id="comment_teacher" name="comment_teacher" rows="2"
                                                class="w-full text-gray-600 border rounded p-2 bg-gray-100" readonly><?= htmlspecialchars($comment_teacher ?? '') ?></textarea>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <?php if (is_null($status)): ?>
                                    <div class="text-center">
                                        <button type="submit"
                                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-xl shadow">
                                            พิจารณาแล้ว
                                        </button>
                                    </div>
                                <?php endif; ?>

                            </form>

                            <script>
                                function validateForm() {
                                    const radios = document.getElementsByName('approval_status');

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


                                    return true;
                                }
                            </script>


                        </div>

                        <!-- Status Stepper -->
                        <span class="font-semibold">สถานะ:</span>
                        <?php
                        if (is_null($status)) {
                        ?>
                            <!-- Stepper -->
                            <div id="statusStepper2" class="flex justify-between items-center my-4">
                                <!-- Step 0 -->
                                <div class="flex flex-col items-center">
                                    <div id="step1Circle2" class="w-8 h-8 rounded-full border-2 border-gray-400 flex items-center justify-center text-gray-500">1</div>
                                    <span class="mt-1 text-sm text-gray-600 text-center">รออาจารย์ประจำรายวิชาพิจารณาคำร้อง</span>
                                </div>

                                <div class="flex-auto h-0.5 bg-gray-300 mx-1" id="line1"></div>

                                <!-- Step 1 -->
                                <div class="flex flex-col items-center">
                                    <div id="step2Circle2" class="w-8 h-8 rounded-full border-2 border-gray-400 flex items-center justify-center text-gray-500">2</div>
                                    <span class="mt-1 text-sm text-gray-600 text-center">อาจารย์ประจำรายวิชาพิจารณาแล้ว</span>
                                </div>
                            </div>
                        <?php
                        } elseif ($status == "0") {
                            echo "ไม่พิจารณา";
                        } else { ?>
                            <!-- Stepper -->
                            <div id="statusStepper2" class="flex justify-between items-center my-4">
                                <!-- Step 0 -->
                                <div class="flex flex-col items-center">
                                    <div id="step1Circle2" class="w-8 h-8 rounded-full border-2 border-gray-400 flex items-center justify-center text-gray-500">1</div>
                                    <span class="mt-1 text-sm text-gray-600 text-center">รออาจารย์ประจำรายวิชาพิจารณาคำร้อง</span>
                                </div>

                                <div class="flex-auto h-0.5 bg-gray-300 mx-1" id="line1"></div>

                                <!-- Step 1 -->
                                <div class="flex flex-col items-center">
                                    <div id="step2Circle2" class="w-8 h-8 rounded-full border-2 border-gray-400 flex items-center justify-center text-gray-500">2</div>
                                    <span class="mt-1 text-sm text-gray-600 text-center">อาจารย์ประจำรายวิชาพิจารณาแล้ว</span>
                                </div>
                            </div>
                        <?php } ?>


                        <!-- Metadata -->
                        <div class="grid grid-cols-1 md:grid-cols-1 gap-4 text-sm text-gray-700">
                            <div class="text-right">
                                <span class="font-semibold">วันเวลาที่นักศึกษาบันทึกคำร้องนี้ :</span>
                                <?php echo htmlspecialchars($created_at); ?> น.
                            </div>

                        </div>
                        <span class="font-semibold text-center">ระบบยื่นคำร้อง สาขาคอมพิวเตอร์ คณะวิศวกรรมศาสตร์ มหาวิทยาลัยเทคโนโลยีราชมงคลอีสาน วิทยาเขตขอนแก่น</span>
                    </div>
                </div>


                <?php


                ?>


                <!-- JavaScript for stepper -->
                <script>
                    const statusValue = "<?= $status ?>"; // string
                    updateStatusStepper2(statusValue);

                    function updateStatusStepper2(status) {
                        status = parseInt(status); // 👈 แปลง string เป็น number

                        const step1 = document.getElementById('step1Circle2');
                        const step2 = document.getElementById('step2Circle2');
                        const line21 = document.getElementById('line1');

                        if (isNaN(status)) {
                            console.log("ไม่มีข้อมูลสถานะ");
                            return;
                        }

                        if (status === 0) {
                            // ไม่พิจารณา (แดง)
                            step1.className = 'w-8 h-8 rounded-full border-2 border-red-500 bg-red-500 text-white flex items-center justify-center';
                            step2.className = 'w-8 h-8 rounded-full border-2 border-red-500 bg-red-500 text-white flex items-center justify-center';
                            line21.className = 'flex-auto h-0.5 mx-1 bg-red-500';
                        } else if (status === 1) {
                            // อนุมัติแล้ว (เขียว)
                            step1.className = 'w-8 h-8 rounded-full border-2 border-green-500 bg-green-500 text-white flex items-center justify-center';
                            step2.className = 'w-8 h-8 rounded-full border-2 border-green-500 bg-green-500 text-white flex items-center justify-center';
                            line21.className = 'flex-auto h-0.5 mx-1 bg-green-500';
                        }
                    }
                </script>


    <?php
                // ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // รับค่าจากฟอร์ม
                    $approvalStatus = $_POST['approval_status'];  // approved หรือ not_approved

                    // ถ้าไม่อนุมัติจากอาจารย์ประจำรายวิชา อีเมลจะแจ้งเตือนกลับไปที่นักศึกษาให้ทราบ
                    if ($approvalStatus == "0") {
                        $comment_teacher = $_POST['comment_teacher'];  // คำอธิบายเพิ่มเติมจากอาจารย์ประจำรายวิชา
                        $status = 0; // ให้จบไปเลย
                        $token = $_GET['token'];  // หรือ $_POST ถ้าส่งมาจาก hidden field

                        // SQL Query
                        $sql = "UPDATE form_re06
 SET approval_status_teacher = :approval_status, 
     comment_teacher = :comment_teacher,
     status = :status 
 WHERE token = :token";

                        // เตรียมและ execute
                        $stmt = $pdo->prepare($sql);
                        $success = $stmt->execute([
                            ':approval_status' => $approvalStatus,
                            ':comment_teacher' => $comment_teacher,
                            ':status' => $status,
                            ':token' => $token
                        ]);

                        if ($success) {
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
                                $mail->addAddress($email, 'นักศึกษา');
                                $mail->Subject = 'คำร้องทั่วไป (RE.06) ของ ' . htmlspecialchars($profile['name']) . ' ไม่ผ่านการพิจารณา จากอาจารย์ประจำรายวิชา';
                                $mail->isHTML(true); // เพิ่มบรรทัดนี้เพื่อให้รองรับ HTML

                                $mail->Body = '
     <div style="font-family: Tahoma, sans-serif; background-color:rgb(46, 46, 46); padding: 20px; border-radius: 10px; color: #f0f0f0; font-size: 18px;">
         <h2 style="color: #ffa500; font-size: 24px;">📄 ยี่นคำร้องทั่วไป (RE.06)</h2>
 
                    <div style="margin-top: 15px; padding: 15px; background-color:rgb(171, 166, 166); border-left: 4px solid #ffa500; color: #000;">
                        <p><strong>ชื่อ:</strong> ' . htmlspecialchars($profile['name']) . '</p>
                        <p><strong>รหัสนักศึกษา:</strong> ' . $profile['id'] . '</p>
                        <p><strong>รหัสวิชา:</strong> ' . htmlspecialchars($course_id) . '</p>
                        <p><strong>ชื่อวิชา:</strong> ' . htmlspecialchars($course_nameTH) . '</p>
                        <p><strong>กลุ่มเรียน:</strong> ' . htmlspecialchars($group) . '</p>
                        <p><strong>ภาคเรียน:</strong> ' . htmlspecialchars($term) . ' / <strong>ปีการศึกษา:</strong> ' . htmlspecialchars($year) . '</p>
                        <p><strong>ประเภทการลงทะเบียน:</strong> ' . htmlspecialchars($reg_status) . '</p>
                        <p><strong>ยอดลงทะเบียนปัจจุบัน:</strong> ' . htmlspecialchars($coutter) . ' คน</p>
                        <p><strong>เหตุผลในการขอเพิ่มที่นั่ง:</strong> ' . htmlspecialchars($reason) . '</p>
                    </div>
               
                    <p style="margin-top: 20px;">📧 <strong>อีเมลอาจารย์ประจำรายวิชา:</strong> ' . htmlspecialchars($teacher_email) . '<br>

         <p style="margin-top: 30px; font-size: 14px; color: #888;">ระบบยื่นคำร้อง สาขาคอมพิวเตอร์  คณะวิศวกรรมศาสตร์ มหาวิทยาลัยเทคโนโลยีราชมงคลอีสาน วิทยาเขตขอนแก่น</p>
     </div>
 ';



                                $mail->send();
                                // echo 'Message has been sent';
                            } catch (Exception $e) {
                                echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
                            }

                            // แสดง Swal และ redirect ไปหน้า index.php
                            echo <<<HTML
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script>
     Swal.fire({
         title: 'สำเร็จ!',
         text: 'ข้อมูลถูกอัปเดตเรียบร้อยแล้ว',
         icon: 'success',
         confirmButtonText: 'ตกลง'
     }).then(() => {
         window.location.href = '';
     });
 </script>
 HTML;
                        } else {
                            echo <<<HTML
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script>
     Swal.fire({
         title: 'เกิดข้อผิดพลาด!',
         text: 'ไม่สามารถอัปเดตข้อมูลได้',
         icon: 'error',
         confirmButtonText: 'ตกลง'
     });
 </script>
 HTML;
                        }
                        // ถ้าอนุมัติจากหัวหน้าสาขา อีเมลจะแจ้งเตือนกลับไปที่นักศึกษาให้ทราบ
                    } else {

                        $comment_teacher = $_POST['comment_teacher'];  // คำอธิบายเพิ่มเติม
                        $status = 1;


                        // SQL Query
                        $sql = "UPDATE form_re01 
            SET approval_status_dep = :approval_status, 
                comment_teacher = :comment_teacher, 
                status = :status 
            WHERE token = :token";

                        // เตรียมและ execute
                        $stmt = $pdo->prepare($sql);
                        $success = $stmt->execute([
                            ':approval_status' => $approvalStatus,
                            ':comment_teacher' => $comment_teacher,
                            ':status' => $status,
                            ':token' => $token
                        ]);

                        if ($success) {
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
                                $mail->addAddress($email, 'นักศึกษา');
                                $mail->Subject = 'คำร้องขอเพิ่มที่นั่ง (RE.06) ของ ' . htmlspecialchars($profile['name']) . ' ผ่านการพิจารณา จากหัวหน้าสาขาแล้ว';
                                $mail->isHTML(true); // เพิ่มบรรทัดนี้เพื่อให้รองรับ HTML


                                $mail->Body = '
                <div style="font-family: Tahoma, sans-serif; background-color:rgb(46, 46, 46); padding: 20px; border-radius: 10px; color: #f0f0f0; font-size: 18px;">
         <h2 style="color: #ffa500; font-size: 24px;">📄 ยี่นคำร้องทั่วไป (RE.06)</h2>
                    <div style="margin-top: 15px; padding: 15px; background-color:rgb(171, 166, 166); border-left: 4px solid #ffa500; color: #000;">
                        <p><strong>ชื่อ:</strong> ' . htmlspecialchars($profile['name']) . '</p>
                        <p><strong>รหัสนักศึกษา:</strong> ' . $profile['id'] . '</p>
                        <p><strong>รหัสวิชา:</strong> ' . htmlspecialchars($course_id) . '</p>
                        <p><strong>ชื่อวิชา:</strong> ' . htmlspecialchars($course_nameTH) . '</p>
                        <p><strong>กลุ่มเรียน:</strong> ' . htmlspecialchars($group) . '</p>
                        <p><strong>ภาคเรียน:</strong> ' . htmlspecialchars($term) . ' / <strong>ปีการศึกษา:</strong> ' . htmlspecialchars($year) . '</p>
                        <p><strong>ประเภทการลงทะเบียน:</strong> ' . htmlspecialchars($reg_status) . '</p>
                        <p><strong>ยอดลงทะเบียนปัจจุบัน:</strong> ' . htmlspecialchars($coutter) . ' คน</p>
                        <p><strong>เหตุผลในการขอเพิ่มที่นั่ง:</strong> ' . htmlspecialchars($reason) . '</p>
                    </div>
               
                    <p style="margin-top: 20px;">📧 <strong>อีเมลอาจารย์ประจำรายวิชา:</strong> ' . htmlspecialchars($teacher_email) . '<br>

         <p style="margin-top: 30px; font-size: 14px; color: #888;">ระบบยื่นคำร้อง สาขาคอมพิวเตอร์  คณะวิศวกรรมศาสตร์ มหาวิทยาลัยเทคโนโลยีราชมงคลอีสาน วิทยาเขตขอนแก่น</p>
     </div>
            ';
                                $mail->send();
                                // echo 'Message has been sent';
                            } catch (Exception $e) {
                                echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
                            }

                            // แสดง Swal และ redirect ไปหน้า index.php
                            echo <<<HTML
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                Swal.fire({
                    title: 'สำเร็จ!',
                    text: 'ข้อมูลถูกอัปเดตเรียบร้อยแล้ว',
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    window.location.href = '';
                });
            </script>
            HTML;
                        } else {
                            echo <<<HTML
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: 'ไม่สามารถอัปเดตข้อมูลได้',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
            </script>
            HTML;
                        }
                    }
                }
            } else {
                echo "<div class='text-center p-6'>ไม่พบข้อมูลคำร้อง กรุณาตรวจสอบลิงก์อีกครั้ง</div>";
                echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'ไม่พบข้อมูลคำร้อง',
            text: 'กรุณาตรวจสอบลิงก์อีกครั้ง',
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
            echo "Database error: " . $e->getMessage();
        }
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
            window.location.href = '';
        });
    </script>
    ";
    }

    ?>
    <?php include './loadtab/f.php'; ?>
</body>

</html>
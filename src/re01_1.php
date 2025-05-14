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
    } else {
        echo "ไม่พบข้อมูลที่ตรงกับ token นี้";
    }
} catch (PDOException $e) {
    echo "เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: " . $e->getMessage();
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

<body class="flex items-center justify-center min-h-screen bg">
    <?php include './loadtab/h.php'; ?>
    <div class="w-full max-w-3xl p-8 m-6 bg-white rounded-2xl shadow-2xl transform transition duration-500 hover:scale-105">
        <div class="flex flex-col items-center">

            <div class="flex flex-col items-center space-y-4 text-gray-800">
                <h2 class="text-3xl font-extrabold t1">คำร้องทั่วไป RE.01</h2>

                <div class="w-full bg-gray-50 rounded-xl p-4 shadow-sm">
                    <div class="mb-2">
                        <span class="font-semibold">FORM ID:</span> RE.01-<?php echo htmlspecialchars($form_id); ?>
                    </div>
                    <div class="mb-2">
                        <span class="font-semibold">เรื่อง:</span> <?php echo htmlspecialchars($title); ?>
                    </div>
                    <div class="mb-2">
                        <span class="font-semibold">เรียน:</span> <?php echo htmlspecialchars($to); ?>
                    </div>
                    <div class="mb-2">
                        <span class="font-semibold">อีเมลนักศึกษา:</span> <?php echo htmlspecialchars($email); ?>
                    </div>
                    <div class="mb-2">
                        <span class="font-semibold">คณะ:</span> <?php echo htmlspecialchars($faculty); ?>
                    </div>
                    <div class="mb-2">
                        <span class="font-semibold">สาขา:</span> <?php echo htmlspecialchars($field); ?>
                    </div>
                    <div class="mb-2">
                        <span class="font-semibold">ระดับชั้น:</span> <?php echo htmlspecialchars($course_level); ?>
                    </div>
                    <div class="mb-2">
                        <label for="request_text" class="font-semibold">ข้อความร้องขอ:</label>
                        <textarea id="request_text" name="request_text" rows="5" class="mt-1 w-full text-gray-600 border rounded p-2" readonly><?php echo htmlspecialchars($request_text); ?></textarea>
                    </div>

                    <div class="mb-2">
                        <label for="comment_teacher" class="font-semibold">ความคิดเห็นอาจารย์:</label>
                        <textarea id="comment_teacher" name="comment_teacher" rows="5" class="mt-1 w-full text-gray-600 border rounded p-2"><?php echo $comment_teacher; ?></textarea>
                    </div>
                    <div class="mb-2">
                        <span class="font-semibold">สถานะ:</span> <?php echo htmlspecialchars($status); ?>
                    </div>
                    <div class="mb-2">
                        <span class="font-semibold">วันที่สร้าง:</span> <?php echo htmlspecialchars($created_at); ?>
                    </div>
                    <div class="mb-2">
                        <span class="font-semibold">อีเมลอาจารย์:</span> <?php echo htmlspecialchars($teacher_email); ?>
                    </div>
                </div>
            </div>



        </div>
    </div>
    <?php include './loadtab/f.php'; ?>
</body>

</html>
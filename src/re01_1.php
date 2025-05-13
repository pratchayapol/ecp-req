<?php
include '../connect/dbcon.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    echo "Token ที่ได้รับคือ: " . htmlspecialchars($token);
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
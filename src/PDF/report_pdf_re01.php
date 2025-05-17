<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require('fpdf.php');
include '../connect/dbcon.php';

if (!isset($_GET['token'])) {
    die("ไม่พบ token ใน URL");
}

$token = $_GET['token'];

try {
    // STEP 1: ดึงข้อมูลจาก form_re01
    $stmtForm = $pdo->prepare("SELECT * FROM form_re01 WHERE token = :token");
    $stmtForm->bindParam(':token', $token, PDO::PARAM_STR);
    $stmtForm->execute();
    $row = $stmtForm->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // ดึง email จาก form_re01
        $email = $row['email'];

        // STEP 2: ดึงข้อมูลจาก accounts โดยใช้ email
        $stmtAccount = $pdo->prepare("SELECT id, name, email FROM accounts WHERE email = :email");
        $stmtAccount->execute(['email' => $email]);
        $profile = $stmtAccount->fetch(PDO::FETCH_ASSOC);

        if ($profile) {
            // ดึงข้อมูลจาก form_re01 มาใช้
            $form_id = $row['form_id'];
            $title = $row['title'];
            $to = $row['to'];
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

            // ดึงข้อมูลจาก accounts มาใช้
            $name = $profile['name'];
            $id = $profile['id'];

//แปลงวันเดือนปีเวลา
        $datetime = new DateTime($created_at);
        $formatted_date = $datetime->format('d/m/Y H:i'); // 15/05/2025 10:45

        function formatDateThai($dateStr, $spacing = [' ', ' ', ' ', ' ']) {
    // spacing[0] = เว้นวรรคหลังวัน
    // spacing[1] = เว้นวรรคหลังเดือน
    // spacing[2] = เว้นวรรคหลังปี
    // spacing[3] = เว้นวรรคหลัง "เวลา"

    $thaiMonths = [
        "", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
        "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
    ];

    $dt = new DateTime($dateStr);
    $day = $dt->format('j');
    $month = (int)$dt->format('n');
    $year = (int)$dt->format('Y') + 543;
    $time = $dt->format('H:i');

    return $day . $spacing[0] . $thaiMonths[$month] . $spacing[1] . $year . $spacing[2] . 'เวลา' . $spacing[3] . $time . ' น.';
 }
}





    } else {
        echo "ไม่พบข้อมูลที่ตรงกับ token นี้";
    }
} catch (PDOException $e) {
    echo "เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: " . $e->getMessage();
}



$pdf = new FPDF();
$pdf->AddPage('P');
$pdf->AddFont('sara', '', 'THSarabun.php');
$pdf->Image('RE.01bg.jpg', 0, 0, 210, 297);
$pdf->SetXY(190, 0);

// //เรื่อง
$pdf->SetY(40.5);
$pdf->SetX(23);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(40, 2, iconv('utf-8', 'cp874', $title), 0, 1, 'L');

// //เรียน
$pdf->SetY(49);
$pdf->SetX(23);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(40, 2, iconv('utf-8', 'cp874', $to), 0, 1, 'L');

// //email นักศึกษา
$pdf->SetY(121.5);
$pdf->SetX(32.5);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(70, 2, iconv('utf-8', 'cp874',  $email), 0, 1, 'L');

// //คณะ
$pdf->SetY(79);
$pdf->SetX(23);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(168, 2, iconv('utf-8', 'cp874',  $faculty), 0, 1, 'L');



// //สาขา
$pdf->SetY(79);
$pdf->SetX(35);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(165, 2, iconv('utf-8', 'cp874', $field), 0, 1, 'C');

// //ชั้นปี
$pdf->SetY(79);
$pdf->SetX(31);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(168, 2, iconv('utf-8', 'cp874', $course_level), 0, 1, 'R');

// //เหตุผล
$pdf->SetY(89);
$pdf->SetX(50);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(168, 2, iconv('utf-8', 'cp874', $request_text), 0, 1, 'L');

// //ความคิดเห็นอาจารย์ที่ปรึกษา
$pdf->SetY(140.5);
$pdf->SetX(15);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $comment_teacher), 0, 1, 'L');


// //ความคิดเห็นหัวหน้าสาขา
$pdf->SetY(168.5);
$pdf->SetX(15);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $comment_head_dep), 0, 1, 'L');

// // //เวลา่

$created_at_thai = formatDateThai($created_at, ['                  ','                  ','    ', ' ']);
// เว้นวรรคหลัง: วัน 2 ช่อง, เดือน 3 ช่อง, ปี 4 ช่อง, "เวลา" 1 ช่อง

$pdf->SetY(32);
$pdf->SetX(120);
$pdf->SetFont('sara', '', 11.5);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $created_at_thai), 0, 1, 'L');


//ชื่อ
$pdf->SetY(60);
$pdf->SetX(65);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(40, 2, iconv('utf-8', 'cp874', $name), 0, 1, 'L');

//เลขนศ
$pdf->SetY(60);
$pdf->SetX(155);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(40, 2, iconv('utf-8', 'cp874',$id), 0, 1, 'L');

$pdf->Output();

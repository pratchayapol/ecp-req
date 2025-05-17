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
    $stmtForm = $pdo->prepare("SELECT * FROM form_re06 WHERE token = :token");
    $stmtForm->bindParam(':token', $token, PDO::PARAM_STR);
    $stmtForm->execute();
    $row = $stmtForm->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // เก็บข้อมูลจาก form_re06
        $form_id = $row['form_id'];
        $term = $row['term'];
        $year = $row['year'];
        $reason = $row['reason'];
        $group_name = $row['Group']; // เปลี่ยนชื่อเพื่อไม่ให้ชนกับ keyword
        $course_id = $row['course_id'];
        $course_nameTH = $row['course_nameTH'];
        $coutter = $row['coutter'];
        $reg_status = $row['reg_status'];
        $status = $row['status'];
        $comment_teacher = $row['comment_teacher'];
        $approval_status_teacher = $row['approval_status_teacher'];
        $created_at = $row['created_at'];
        $email = $row['email'];
        $teacher_email = $row['teacher_email'];
        $token = $row['token'];

        // 2. ดึงข้อมูลจาก accounts โดยใช้ email ที่ได้จาก form_re06
        $stmtAcc = $pdo->prepare("SELECT id, name, faculty, field, course_level FROM accounts WHERE email = :email");
        $stmtAcc->execute(['email' => $email]);
        $profile = $stmtAcc->fetch(PDO::FETCH_ASSOC);

        if ($profile) {
            // เก็บข้อมูลจาก accounts
            $id = $profile['id'];
            $name = $profile['name'];
            $faculty = $profile['faculty'];
            $field = $profile['field'];
            $course_level = $profile['course_level'];
        }
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


    } else {
        echo "ไม่พบข้อมูลที่ตรงกับ token นี้";
    }
} catch (PDOException $e) {
    echo "เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: " . $e->getMessage();
}



$pdf = new FPDF();
$pdf->AddPage('P');
$pdf->AddFont('sara', '', 'THSarabun.php');
$pdf->Image('RE.06bg.jpg', 0, 0, 210, 297);
$pdf->SetXY(190, 0);

// //	ภาคเรียน
$pdf->SetY(21);
$pdf->SetX(120);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(40, 2, iconv('utf-8', 'cp874', $term), 0, 1, 'L');

// //ปีการศึกษา	
$pdf->SetY(21);
$pdf->SetX(155);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(40, 2, iconv('utf-8', 'cp874', $year), 0, 1, 'L');

// //email นักศึกษา
$pdf->SetY(184);
$pdf->SetX(32.5);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(70, 2, iconv('utf-8', 'cp874',  $email), 0, 1, 'L');

// //กลุ่มเรียน
$pdf->SetY(138);
$pdf->SetX(116);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(168, 2, iconv('utf-8', 'cp874',  $group), 0, 1, 'L');



// //รหัสรายวิชา1
$pdf->SetY(130);
$pdf->SetX(20);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(165, 2, iconv('utf-8', 'cp874', $course_id), 0, 1, 'L');

// //ชื่อวิชาภาษาไทย1
$pdf->SetY(130);
$pdf->SetX(52);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(168, 2, iconv('utf-8', 'cp874', $course_nameTH), 0, 1, 'L');

// //รหัสรายวิชา2
$pdf->SetY(149);
$pdf->SetX(20);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(165, 2, iconv('utf-8', 'cp874', $course_id), 0, 1, 'L');


// //ชื่อวิชาภาษาไทย2
$pdf->SetY(149);
$pdf->SetX(52);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(168, 2, iconv('utf-8', 'cp874', $course_nameTH), 0, 1, 'L');

// //ยอดลงทะเบียน
$pdf->SetY(138);
$pdf->SetX(135);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(168, 2, iconv('utf-8', 'cp874', $coutter), 0, 1, 'L');

// //ความคิดเห็นอาจารย์ที่ปรึกษา
$pdf->SetY(218);
$pdf->SetX(50);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $comment_teacher), 0, 1, 'L');


// //เหตุผล
$pdf->SetY(84);
$pdf->SetX(68.5);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $reason), 0, 1, 'L');

// //เวลา่
$created_at_thai = formatDateThai($created_at, ['                  ','                  ','    ', ' ']);
// เว้นวรรคหลัง: วัน 2 ช่อง, เดือน 3 ช่อง, ปี 4 ช่อง, "เวลา" 1 ช่อง

$pdf->SetY(34);
$pdf->SetX(136);
$pdf->SetFont('sara', '', 11.5);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $created_at_thai), 0, 1, 'L');

// //ชื่อ สกุล
$pdf->SetY(90);
$pdf->SetX(68.5);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $name), 0, 1, 'L');

// //เลขนศ
$pdf->SetY(100);
$pdf->SetX(68.5);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $id), 0, 1, 'L');

// //คณะ
$pdf->SetY(110);
$pdf->SetX(68.5);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $faculty), 0, 1, 'L');

// //สาขาวิชา
$pdf->SetY(120);
$pdf->SetX(68.5);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $field), 0, 1, 'L');

// //ชั้นปี
$pdf->SetY(130);
$pdf->SetX(68.5);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $field), 0, 1, 'L');

$pdf->Output();

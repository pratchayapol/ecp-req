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
$pdf->SetY(40);
$pdf->SetX(30);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(168, 2, iconv('utf-8', 'cp874', $course_level), 0, 1, 'R');

// //วันที่ .......... เดือน ..............พ.ศ.............เวลา .......... ออกเวร
// $pdf->SetY(90);
// $pdf->SetX(60);
// $pdf->SetFont('sara', '', 14);
// $pdf->Cell(168, 2, iconv('utf-8', 'cp874', ThDate3()), 0, 1, 'C');

// //สถานะการณ์
// $pdf->SetY(97.5);
// $pdf->SetX(45);
// $pdf->SetFont('sara', '', 14);
// $pdf->Cell(42, 2, iconv('utf-8', 'cp874', $row['report_event']), 0, 1, 'L');


// //ไม่ปกติเนื่องจาก
// $pdf->SetY(97.5);
// $pdf->SetX(85);
// $pdf->SetFont('sara', '', 14);
// $pdf->Cell(42, 2, iconv('utf-8', 'cp874', "เนื่องจาก  ".$row['report_event1']), 0, 1, 'L');

// //สถานะการณ์เพิ่มเติม
// $pdf->SetY(105);
// $pdf->SetX(30);
// $pdf->SetFont('sara', '', 11.5);
// $pdf->Cell(42, 2, iconv('utf-8', 'cp874', $row['report']), 0, 1, 'L');


// $id = $_GET['id'];
// $cer = "SELECT * FROM img_event WHERE id_van = $id";
// $query_cer = $q1 = $conn->query($cer);



// $stmt6 = $conn->query("SELECT * FROM signature WHERE id_van = $id");
// $row6 = $stmt6->fetch(PDO::FETCH_ASSOC);
// extract($row6);
// $id4 = $row6['signature_user'];
// $id5 = $row6['signature_inspector'];
// $id6 = $row6['signature_manager'];


// //ลายเซนต์ผู้เข้าเวร
// $pdf->Image("../signatures/$id4", 130, 110, 15.5, 12.5);

// //ตำแหน่ง ครูเวร / นักการ
// $pdf->SetY(120.5);
// $pdf->SetX(90);
// $pdf->SetFont('sara', '', 14);
// $pdf->Cell(168, 2, iconv('utf-8', 'cp874', $row['position']), 0, 1, 'C');

// //ชื่อผู้เข้าเวร ล่าง
// $pdf->SetY(126.5);
// $pdf->SetX(68.5);
// $pdf->SetFont('sara', '', 14);
// $pdf->Cell(143, 2, iconv('utf-8', 'cp874', $row2['firstname'] . '  ' . $row2['lastname']), 0, 1, 'C');

// //ชื่อผู้ตรวจเวร บน
// $pdf->SetY(137);
// $pdf->SetX(43);
// $pdf->SetFont('sara', '', 14);
// $pdf->Cell(168, 2, iconv('utf-8', 'cp874', $row3['firstname'] . '  ' . $row3['lastname']), 0, 1, 'L');

// //บันทึกข้อความผู้ตรวจเวร
// $ins_text = $row['ins_text'];
// if ($row['ins_text'] != null) {
//     $pdf->SetY(152);
//     $pdf->SetX(30);
//     $pdf->SetFont('sara', '', 11.5);
//     $pdf->Cell(42, 2, iconv('utf-8', 'cp874', "$ins_text"), 0, 1, 'L');
// } else {

//     $pdf->SetY(152);
//     $pdf->SetX(30);
//     $pdf->SetFont('sara', '', 11.5);
//     $pdf->Cell(42, 2, iconv('utf-8', 'cp874', "$ins_text"), 0, 1, 'L');
// }


// //ลายเซนต์ผู้ตรวจเวร
// $pdf->Image("../signatures/$id5", 130, 165, 15.5, 12.5);

// //ชื่อผู้ตรวจเวร ล่าง
// $pdf->SetY(181.5);
// $pdf->SetX(68.5);
// $pdf->SetFont('sara', '', 14);
// $pdf->Cell(143, 2, iconv('utf-8', 'cp874', $row3['firstname'] . '  ' . $row3['lastname']), 0, 1, 'C');

// //บันทึกข้อความผู้อำนวยการ
// $mana_text = $row['mana_text'];
// if ($row['mana_text'] != null) {
//     $pdf->SetY(196);
//     $pdf->SetX(30);
//     $pdf->SetFont('sara', '', 11.5);
//     $pdf->Cell(42, 2, iconv('utf-8', 'cp874', "$mana_text"), 0, 1, 'L');
// } else {
//     $pdf->SetY(196);
//     $pdf->SetX(30);
//     $pdf->SetFont('sara', '', 11.5);
//     $pdf->Cell(42, 2, iconv('utf-8', 'cp874', "$mana_text"), 0, 1, 'L');
// }


// //ลายเซนต์ผู้อำนวยการ
// $pdf->Image("../signatures/$id6", 130, 216, 15.5, 12.5);

// //ชื่อผู้อำนวยการ
// $pdf->SetY(233.5);
// $pdf->SetX(68.5);
// $pdf->SetFont('sara', '', 14);
// $pdf->Cell(143, 2, iconv('utf-8', 'cp874', $row4['firstname'] . '  ' . $row4['lastname']), 0, 1, 'C');

// //บันทึกเกี่ยวกับเหตุการณ์ทั่วไป
// $mana_text1 = $row['mana_text1'];
// if ($row['mana_text'] != null) {
//     $pdf->SetY(248.5);
//     $pdf->SetX(30);
//     $pdf->SetFont('sara', '', 11.5);
//     $pdf->Cell(42, 2, iconv('utf-8', 'cp874', "$mana_text1"), 0, 1, 'L');
// } else {
//     $pdf->SetY(248.5);
//     $pdf->SetX(30);
//     $pdf->SetFont('sara', '', 11.5);
//     $pdf->Cell(42, 2, iconv('utf-8', 'cp874', "$mana_text1"), 0, 1, 'L');
// }

$pdf->Output();

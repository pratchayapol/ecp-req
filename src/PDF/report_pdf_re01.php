<?php
require('fpdf.php');
require('../config/db.php');
date_default_timezone_set('Asia/Bangkok');



function ThDate1()
{
    require('../config/db.php');
    $id = $_GET['id'];
    $stmt = $conn->query("SELECT * FROM datavan WHERE id_van = $id");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    extract($row);
    //วันภาษาไทย
    $ThDay = array("อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัสบดี", "ศุกร์", "เสาร์");
    //เดือนภาษาไทย
    $ThMonth = array("มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฏาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
    //วันที่ ที่ต้องการเอามาเปลี่ยนฟอแมต
    $myDATE = $row['start_time']; //อาจมาจากฐานข้อมูล
    //$myDATE = date("d-m-Y h:i:s"); //อาจมาจากฐานข้อมูล
    //กำหนดคุณสมบัติ
    $week = date("w", strtotime($myDATE)); // ค่าวันในสัปดาห์ (0-6)
    $months = date("m", strtotime($myDATE)) - 1; // ค่าเดือน (1-12)
    $day = date("d", strtotime($myDATE)); // ค่าวันที่(1-31)
    $years = date("Y", strtotime($myDATE)) + 543; // ค่า ค.ศ.บวก 543 ทำให้เป็น ค.ศ.
    return
        "วัน$ThDay[$week] ที่ $day  เดือน $ThMonth[$months] พ.ศ. $years";
}
//echo ThDate1(); // แสดงวันที่ 

function ThDate2()
{
    require('../config/db.php');
    $id = $_GET['id'];
    $stmt = $conn->query("SELECT * FROM datavan WHERE id_van = $id");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    extract($row);
    //วันภาษาไทย
    $ThDay = array("อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัสบดี", "ศุกร์", "เสาร์");
    //เดือนภาษาไทย
    $ThMonth = array("มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฏาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
    //วันที่ ที่ต้องการเอามาเปลี่ยนฟอแมต
    $myDATE = $row['start_time']; //อาจมาจากฐานข้อมูล
    //กำหนดคุณสมบัติ
    $time = date("H:i:s", strtotime($myDATE)); // ค่าวันในสัปดาห์ (0-6)
    $week = date("w", strtotime($myDATE)); // ค่าวันในสัปดาห์ (0-6)
    $months = date("m", strtotime($myDATE)) - 1; // ค่าเดือน (1-12)
    $day = date("d", strtotime($myDATE)); // ค่าวันที่(1-31)
    $years = date("Y", strtotime($myDATE)) + 543; // ค่า ค.ศ.บวก 543 ทำให้เป็น ค.ศ.
    return
        "วันที่ $day $ThMonth[$months] $years เวลา $time";
}
//echo ThDate2(); // แสดงวันที่

function ThDate3()
{
    require('../config/db.php');
    $id = $_GET['id'];
    $stmt = $conn->query("SELECT * FROM datavan WHERE id_van = $id");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    extract($row);
    //วันภาษาไทย
    $ThDay = array("อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัสบดี", "ศุกร์", "เสาร์");
    //เดือนภาษาไทย
    $ThMonth = array("มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฏาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
    //วันที่ ที่ต้องการเอามาเปลี่ยนฟอแมต
    $myDATE = $row['end_time']; //อาจมาจากฐานข้อมูล
    //กำหนดคุณสมบัติ
    $time = date("H:i:s", strtotime($myDATE)); // ค่าวันในสัปดาห์ (0-6)
    $week = date("w", strtotime($myDATE)); // ค่าวันในสัปดาห์ (0-6)
    $months = date("m", strtotime($myDATE)) - 1; // ค่าเดือน (1-12)
    $day = date("d", strtotime($myDATE)); // ค่าวันที่(1-31)
    $years = date("Y", strtotime($myDATE)) + 543; // ค่า ค.ศ.บวก 543 ทำให้เป็น ค.ศ.
    return
        "วันที่ $day $ThMonth[$months] $years เวลา $time";
}
//echo ThDate3(); // แสดงวันที่
$id = $_GET['id'];
//echo $id;
$stmt = $conn->query("SELECT * FROM datavan WHERE id_van = $id");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
extract($row);
$id1 = $row['user'];
$id2 = $row['inspector'];
$id3 = $row['manager'];

//หาชื่อ นามสกุลผู้เข้าเวร
$stmt2 = $conn->query("SELECT * FROM account WHERE id = $id1");
$row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
extract($row2);

//หาชื่อ นามสกุลผู้ตรวจเวร
$stmt3 = $conn->query("SELECT * FROM account WHERE id = $id2");
$row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
extract($row3);

//หาชื่อ นามสกุลผู้อำนวยการ
$stmt4 = $conn->query("SELECT * FROM account WHERE id = $id3");
$row4 = $stmt4->fetch(PDO::FETCH_ASSOC);
extract($row4);

$pdf = new FPDF();
$pdf->AddPage('P');
$pdf->AddFont('sara', '', 'THSarabun.php');
$pdf->Image('bg.jpg', 0, 0, 210, 297);
$pdf->SetXY(190, 0);

//ลำดับที่ .........
$pdf->SetY(51.5);
$pdf->SetX(22);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(40, 2, iconv('utf-8', 'cp874', $row['id_van']), 0, 1, 'C');

//ภาคเรียนที่ ..............
$pdf->SetY(51.5);
$pdf->SetX(45);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(40, 2, iconv('utf-8', 'cp874', $row['term_year']), 0, 1, 'C');

//วันที่ .......... เดือน ..............พ.ศ................ ประจำ
$pdf->SetY(51.5);
$pdf->SetX(90);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(70, 2, iconv('utf-8', 'cp874', ThDate1()), 0, 1, 'C');

//ชื่อผู้เข้าเวร บน
$pdf->SetY(75.5);
$pdf->SetX(43);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(168, 2, iconv('utf-8', 'cp874', $row2['firstname'] . '  ' . $row2['lastname']), 0, 1, 'L');



//ช่วงเวลา
$pdf->SetY(66.5);
$pdf->SetX(15);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(165, 2, iconv('utf-8', 'cp874', $row['period']), 0, 1, 'R');

//วันที่ .......... เดือน ..............พ.ศ.............เวลา .......... เริ่มเข้าเวร 
$pdf->SetY(90);
$pdf->SetX(44);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(168, 2, iconv('utf-8', 'cp874', ThDate2()), 0, 1, 'L');

//วันที่ .......... เดือน ..............พ.ศ.............เวลา .......... ออกเวร
$pdf->SetY(90);
$pdf->SetX(60);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(168, 2, iconv('utf-8', 'cp874', ThDate3()), 0, 1, 'C');

//สถานะการณ์
$pdf->SetY(97.5);
$pdf->SetX(45);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $row['report_event']), 0, 1, 'L');


//ไม่ปกติเนื่องจาก
$pdf->SetY(97.5);
$pdf->SetX(85);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', "เนื่องจาก  ".$row['report_event1']), 0, 1, 'L');

//สถานะการณ์เพิ่มเติม
$pdf->SetY(105);
$pdf->SetX(30);
$pdf->SetFont('sara', '', 11.5);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $row['report']), 0, 1, 'L');


$id = $_GET['id'];
$cer = "SELECT * FROM img_event WHERE id_van = $id";
$query_cer = $q1 = $conn->query($cer);



$stmt6 = $conn->query("SELECT * FROM signature WHERE id_van = $id");
$row6 = $stmt6->fetch(PDO::FETCH_ASSOC);
extract($row6);
$id4 = $row6['signature_user'];
$id5 = $row6['signature_inspector'];
$id6 = $row6['signature_manager'];


//ลายเซนต์ผู้เข้าเวร
$pdf->Image("../signatures/$id4", 130, 110, 15.5, 12.5);

//ตำแหน่ง ครูเวร / นักการ
$pdf->SetY(120.5);
$pdf->SetX(90);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(168, 2, iconv('utf-8', 'cp874', $row['position']), 0, 1, 'C');

//ชื่อผู้เข้าเวร ล่าง
$pdf->SetY(126.5);
$pdf->SetX(68.5);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(143, 2, iconv('utf-8', 'cp874', $row2['firstname'] . '  ' . $row2['lastname']), 0, 1, 'C');

//ชื่อผู้ตรวจเวร บน
$pdf->SetY(137);
$pdf->SetX(43);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(168, 2, iconv('utf-8', 'cp874', $row3['firstname'] . '  ' . $row3['lastname']), 0, 1, 'L');

//บันทึกข้อความผู้ตรวจเวร
$ins_text = $row['ins_text'];
if ($row['ins_text'] != null) {
    $pdf->SetY(152);
    $pdf->SetX(30);
    $pdf->SetFont('sara', '', 11.5);
    $pdf->Cell(42, 2, iconv('utf-8', 'cp874', "$ins_text"), 0, 1, 'L');
} else {

    $pdf->SetY(152);
    $pdf->SetX(30);
    $pdf->SetFont('sara', '', 11.5);
    $pdf->Cell(42, 2, iconv('utf-8', 'cp874', "$ins_text"), 0, 1, 'L');
}


//ลายเซนต์ผู้ตรวจเวร
$pdf->Image("../signatures/$id5", 130, 165, 15.5, 12.5);

//ชื่อผู้ตรวจเวร ล่าง
$pdf->SetY(181.5);
$pdf->SetX(68.5);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(143, 2, iconv('utf-8', 'cp874', $row3['firstname'] . '  ' . $row3['lastname']), 0, 1, 'C');

//บันทึกข้อความผู้อำนวยการ
$mana_text = $row['mana_text'];
if ($row['mana_text'] != null) {
    $pdf->SetY(196);
    $pdf->SetX(30);
    $pdf->SetFont('sara', '', 11.5);
    $pdf->Cell(42, 2, iconv('utf-8', 'cp874', "$mana_text"), 0, 1, 'L');
} else {
    $pdf->SetY(196);
    $pdf->SetX(30);
    $pdf->SetFont('sara', '', 11.5);
    $pdf->Cell(42, 2, iconv('utf-8', 'cp874', "$mana_text"), 0, 1, 'L');
}


//ลายเซนต์ผู้อำนวยการ
$pdf->Image("../signatures/$id6", 130, 216, 15.5, 12.5);

//ชื่อผู้อำนวยการ
$pdf->SetY(233.5);
$pdf->SetX(68.5);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(143, 2, iconv('utf-8', 'cp874', $row4['firstname'] . '  ' . $row4['lastname']), 0, 1, 'C');

//บันทึกเกี่ยวกับเหตุการณ์ทั่วไป
$mana_text1 = $row['mana_text1'];
if ($row['mana_text'] != null) {
    $pdf->SetY(248.5);
    $pdf->SetX(30);
    $pdf->SetFont('sara', '', 11.5);
    $pdf->Cell(42, 2, iconv('utf-8', 'cp874', "$mana_text1"), 0, 1, 'L');
} else {
    $pdf->SetY(248.5);
    $pdf->SetX(30);
    $pdf->SetFont('sara', '', 11.5);
    $pdf->Cell(42, 2, iconv('utf-8', 'cp874', "$mana_text1"), 0, 1, 'L');
}

$pdf->Output();

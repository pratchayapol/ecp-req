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
    $stmt = $pdo->prepare("SELECT * FROM form_re07 WHERE token = :token");
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // เก็บข้อมูลจาก form_re07
        $term = $row['term'];
        $year = $row['year'];
        $course_id = $row['course_id'];
        $course_nameTH = $row['course_nameTH'];
        $group = $row['Group'];
        $reason = $row['reason'];
        $gpa = $row['gpa'];
        $git_unit = $row['git_unit'];
        $reg_status = $row['reg_status'];
        $expected_graduation = $row['expected_graduation'];
        $comment_teacher = $row['comment_teacher'];
        $approval_status_teacher = $row['approval_status_teacher'];
        $approval_status_dep = $row['approval_status_dep'];
        $comment_head_dep = $row['comment_head_dep'];
        $email = $row['email'];
        $status = $row['status'];
        $created_at = $row['created_at'];
        $token = $row['token'];
        $token_new = $row['token_new'];
        $teacher_email = $row['teacher_email'];
        $head_department = $row['head_department'];

        // ดึงข้อมูลจาก accounts โดยใช้ email
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

        // แปลงวันเดือนปีเวลา
        $datetime = new DateTime($created_at);
        $formatted_date = $datetime->format('d/m/Y H:i'); // เช่น 15/05/2025 10:45

        function formatDateThai($dateStr, $spacing = [' ', ' ', ' ', ' ']) {
            $thaiMonths = [
                "", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
                "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
            ];

            $dt = new DateTime($dateStr);
            $day = $dt->format('j');
            $month = (int)$dt->format('n');
            $year = (int)$dt->format('Y') + 543;
            $time = $dt->format('H:i');

            return $day . $spacing[0] . $thaiMonths[$month] . $spacing[1] . $year . $spacing[2] ;
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
$pdf->Image('RE.07bg.jpg', 0, 0, 210, 297);
$pdf->SetXY(190, 0);

$pdf->SetFont('sara', '', 14);

// // ภาคเรียน1
$pdf->SetY(14);
$pdf->SetX(140);
$pdf->Cell(40, 8, iconv('utf-8', 'cp874', $term), 0, 1, 'L');

// // ปีการศึกษา1
$pdf->SetY(14);
$pdf->SetX(172);
$pdf->Cell(40, 8, iconv('utf-8', 'cp874', $year), 0, 1, 'L');

// // ภาคเรียน2
$pdf->SetY(78.5);
$pdf->SetX(160);
$pdf->Cell(40, 8, iconv('utf-8', 'cp874', $term), 0, 1, 'L');

// // ปีการศึกษา2
$pdf->SetY(78.5);
$pdf->SetX(188);
$pdf->Cell(40, 8, iconv('utf-8', 'cp874', $year), 0, 1, 'L');

// // รหัสวิชา
$pdf->SetY(85.5);
$pdf->SetX(40);
$pdf->Cell(60, 8, iconv('utf-8', 'cp874', $course_id), 0, 1, 'L');

// // ชื่อวิชา
$pdf->SetY(85.5);
$pdf->SetX(100);
$pdf->MultiCell(150, 8, iconv('utf-8', 'cp874', $course_nameTH), 0, 'L');

// // เหตุผล
$pdf->SetY(94);
$pdf->SetX(30);
$pdf->MultiCell(150, 8, iconv('utf-8', 'cp874', $reason), 0, 'L');

// // ความคิดเห็นอาจารย์ที่ปรึกษา
$pdf->SetY(130);
$pdf->SetX(20);
$pdf->MultiCell(150, 8, iconv('utf-8', 'cp874', $comment_teacher), 0, 'L');


// // ความคิดเห็นหัวหน้าสาขา
$pdf->SetY(157);
$pdf->SetX(20);
$pdf->MultiCell(150, 8, iconv('utf-8', 'cp874', $comment_head_dep), 0, 'L');

// // email นักศึกษา
$pdf->SetY(112);
$pdf->SetX(85);
$pdf->Cell(100, 8, iconv('utf-8', 'cp874', $email), 0, 1, 'L');


// //เวลา่
$created_at_thai = formatDateThai($created_at, ['              ','            ','    ', ' ']);
// เว้นวรรคหลัง: วัน 2 ช่อง, เดือน 3 ช่อง, ปี 4 ช่อง, "เวลา" 1 ช่อง

$pdf->SetY(29);
$pdf->SetX(122);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $created_at_thai), 0, 1, 'L');

// //ชื่อ สกุล
$pdf->SetY(49);
$pdf->SetX(68.5);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $name), 0, 1, 'L');

// //เลขนศ
$pdf->SetY(49);
$pdf->SetX(160);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $id), 0, 1, 'L');

// //สาขาวิชา
$pdf->SetY(74);
$pdf->SetX(37);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $field), 0, 1, 'L');

// //ชั้นปี
$pdf->SetY(74);
$pdf->SetX(145);
$pdf->SetFont('sara', '', 14);
$pdf->Cell(42, 2, iconv('utf-8', 'cp874', $course_level), 0, 1, 'L');


$pdf->Output();

<?php
session_start();
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom fonts for this template-->
    <link rel="shortcut icon" href="../image/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="../css/fonts.css">
    <!-- animation -->
    <link rel="stylesheet" href="./css/animation.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-cover bg-center bg-no-repeat t1" style="background-image: url('/image/bg.jpg');">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-1/6 bg-white shadow-lg p-4 m-6 flex flex-col justify-between rounded-[20px]">
            <div class="text-center">
                <img src="/image/logo.png" class="w-32 h-32 rounded-full shadow-lg mx-auto" alt="Logo">
                <button class="w-full bg-[#EF6526] text-white py-2 rounded-[12px] mt-4 shadow-md">Dashboard</button>
                <div class="mt-4 space-y-2">
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md">คำร้องขอเพิ่มที่นั่ง RE.06</button>
                    <button class="w-full bg-white text-[#EF6526] hover:bg-[#EF6526] hover:text-white text-left py-2 px-4 rounded-[12px] shadow-md">คำร้องขอเปิดดอกแนน RE.07</button>
                </div>
            </div>
            <div class="text-center">
                <div class="flex items-center space-x-2">
                    <div class="bg-gray-300 rounded-full w-10 h-10 flex items-center justify-center">
                        <span class="text-gray-600">👤</span>
                    </div>
                    <span>Student Ecp</span>
                </div>
                <button id="logoutBtn" class="w-full mt-4 bg-white text-[#2C2C2C] py-2 rounded-[12px] hover:bg-[#2C2C2C] hover:text-white transition-colors duration-200 shadow-md">
                    ออกจากระบบ
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col justify-between bg-white/60 mt-6 mb-6 me-6 rounded-[20px]">
            <div class="p-8">
                <div class="bg-white rounded-lg shadow-lg h-96">
                    <h1 class="text-orange-500 bg-white p-2 text-xl h-12 font-bold shadow-md rounded-[12px] text-center">ประชาสัมพันธ์</h1>
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
  document.getElementById("logoutBtn").addEventListener("click", function () {
    Swal.fire({
      title: 'คุณแน่ใจหรือไม่?',
      text: 'คุณต้องการออกจากระบบหรือไม่',
      icon: 'warning',
      showCancelButton: true,
      cancelButtonText: 'ยกเลิก',
      confirmButtonText: 'ตกลง',
      reverseButtons: true, // เพื่อสลับตำแหน่งให้ "ยกเลิก" อยู่ซ้าย
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
</style>
</body>

</html>
<?php
session_start();

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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

<body class="bg-cover bg-center bg-no-repeat" style="background-image: url('/image/bg.jpg');">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-1/4 bg-white shadow-lg p-4 flex flex-col justify-between rounded-2xl">
            <div class="text-center">
                <img src="/image/logo.png" class="w-32 h-32 rounded-full shadow-lg mx-auto" alt="Logo">
                <button class="w-full bg-orange-500 text-white py-2 rounded mt-4">Dashboard</button>
                <div class="mt-4 space-y-2">
                    <button class="w-full text-left py-2 px-4 bg-gray-200 rounded-lg hover:bg-gray-300">คำร้องขอเพิ่มที่นั่ง RE.06</button>
                    <button class="w-full text-left py-2 px-4 bg-gray-200 rounded-lg hover:bg-gray-300">คำร้องขอเปิดดอกแนน RE.07</button>
                </div>
            </div>
            <div class="text-center">
                <div class="flex items-center space-x-2">
                    <div class="bg-gray-300 rounded-full w-10 h-10 flex items-center justify-center">
                        <span class="text-gray-600">👤</span>
                    </div>
                    <span>Student Ecp</span>
                </div>
                <button class="w-full mt-4 bg-red-500 text-white py-2 rounded-lg">ออกจากระบบ</button>
            </div>
        </div>


        <!-- Main Content -->
        <div class="flex-1 flex flex-col justify-between">
            <div class="p-8">
                <h1 class="text-orange-500 text-xl font-bold">ประชาสัมพันธ์</h1>
                <div class="bg-white p-4 rounded-lg shadow-lg h-96 mt-4"></div>
            </div>
            <footer class="text-center py-4 bg-orange-500 text-white">
                2025 All rights reserved by Software Engineering 3/67
            </footer>
        </div>
    </div>

</body>

</html>
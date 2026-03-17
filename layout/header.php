
<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require_once 'db.php';

    try {
        $stmt = $pdo->query('SELECT * FROM "user" LIMIT 1');
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "❌ Query failed: " . $e->getMessage();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'My App'; ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Sarabun Font -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
        }
    </style>


</head>
<body class="bg-light">

<nav>
    <div class="nav-brand"><span>💍</span> Wedding Manager
        <p class="mb-0">
                สวัสดีจ้า
            <i class="fa-regular fa-face-smile-wink"></i>
            <?= $user['fullName']; ?>
        </p>
    </div>
  <div class="nav-actions">
    <a href="index.php" class="nav-btn">🏠 หน้าหลัก</a>
    <a href="setup_table.php" class="nav-btn">จัดการโต๊ะแขก</a>
    <a href="/" class="nav-btn">ออกจากระบบ</a>
  </div>
</nav>



<div class="container mt-4">
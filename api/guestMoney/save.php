<?php
require_once __DIR__ . '/../../db.php';
date_default_timezone_set('Asia/Bangkok');
$date = date('Y-m-d H:i:s');

try {

    $action = $_POST['action'] ?? '';
    $id     = $_POST['id'] ?? null;
    $guestName   = trim($_POST['guestName'] ?? '');
    $money = isset($_POST['money']) ? intval($_POST['money']) : 0;
    $remark   = trim($_POST['remark'] ?? '');
    $guestType = intval($_POST['guestType']) ?? null;

    // validate
    if ($guestName === '' || $money <= 0) {
        throw new Exception('กรอกข้อมูลไม่ครบ');
    }

    if ($action === 'add') {

        $stmt = $pdo->prepare("
            INSERT INTO guests (guest_name, money, remark,created_at,guest_type)
            VALUES (:guest_name, :money, :remark,:create_at,:guestType)
        ");

        $stmt->execute([
            ':guest_name'   => $guestName,
            ':money' => $money,
            ':remark'   => $remark,
            ':create_at'   => $date,
            ':guestType' => $guestType
        ]);

        header("Location: /guest.php");
        exit;

    } elseif ($action === 'edit') {

        if (!$id || !is_numeric($id)) {
            throw new Exception('ID ไม่ถูกต้อง');
        }

        $stmt = $pdo->prepare("
            UPDATE guests
            SET guest_name = :guest_name,
                money = :money,
                remark = :remark,
                guest_type = :guestType,
                edit_at = :edit_at
            WHERE id = :id
        ");

        $stmt->execute([
            ':guest_name'   => $guestName,
            ':money' => $money,
            ':remark'   => $remark,
            ':id'     => $id,
            ':guestType' => $guestType,
            ':edit_at' => $date
        ]);

        header("Location: /guest.php");
        exit;

    } else {
        throw new Exception('Invalid action');
    }

} catch (Exception $e) {

    http_response_code(400);

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
<?php
require_once __DIR__ . '/../../db.php';
date_default_timezone_set('Asia/Bangkok');
$date = date('Y-m-d H:i:s');

try {

    $action = $_POST['action'] ?? '';
    $id     = $_POST['id'] ?? null;
    $costName   = trim($_POST['costName'] ?? '');
    $money = isset($_POST['money']) ? intval($_POST['money']) : 0;
    $remark   = trim($_POST['remark'] ?? '');
    // validate
    if ($costName === '' || $money <= 0) {
        throw new Exception('กรอกข้อมูลไม่ครบ');
    }

    if ($action === 'add') {

        $stmt = $pdo->prepare("
            INSERT INTO cost_wedding (cost_name, money, reamark,created_at) VALUES (:cost_name, :money, :remark,:created_at)");

        $stmt->execute([
            ':cost_name'   => $costName,
            ':money' => $money,
            ':remark'   => $remark,
            ':created_at'   => $date,
        ]);

        header('Location: /cost.php');
        exit;

    } elseif ($action === 'edit') {

        if (!$id || !is_numeric($id)) {
            throw new Exception('ID ไม่ถูกต้อง');
        }

        $stmt = $pdo->prepare("
            UPDATE cost_wedding
            SET cost_name = :cost_name,
                money = :money,
                reamark = :remark
            WHERE id = :id
        ");

        $stmt->execute([
            ':cost_name'   => $costName,
            ':money' => $money,
            ':remark'   => $remark,
            ':id'     => $id
        ]);

        header('Location: /cost.php');
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
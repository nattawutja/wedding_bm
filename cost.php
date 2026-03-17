<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db.php';

$title = "จัดการข้อมูลค่าใช้จ่าย";

// Handle actions
$success = '';
$error   = '';

// DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id) {
        try {
            $pdo->prepare('UPDATE cost_wedding SET "statusDelete" = 1 WHERE id = ?')->execute([$id]);
            $success = 'ลบข้อมูลเรียบร้อยแล้ว';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } catch (PDOException $e) { $error = $e->getMessage(); }
    }
}

// EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $id     = (int) ($_POST['id'] ?? 0);
    $costName   = trim($_POST['cost_name'] ?? '');
    $money = (int) ($_POST['money'] ?? 0);
    $remark   = trim($_POST['remark'] ?? '');
    if ($id && $name) {
        try {
            $pdo->prepare('UPDATE cost_wedding SET cost_name=?, money=?, remark=? WHERE id=?')
                ->execute([$costName, $money, $remark, $id]);
            $success = 'แก้ไขข้อมูลเรียบร้อยแล้ว ✨';
        } catch (PDOException $e) { $error = $e->getMessage(); }
    }
}

// Fetch all
$search = trim($_GET['q'] ?? '');
try {
    if ($search) {
        $sql = "SELECT * FROM cost_wedding WHERE \"cost_name\" ILIKE ? AND \"statusDelete\" = 0 ORDER BY id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['%' . $search . '%']);
    } else {
        $sql = "SELECT * FROM cost_wedding WHERE \"statusDelete\" = 0 ORDER BY id DESC ";
        $stmt = $pdo->query($sql);
    }
    $guests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $guests = [];
    $error  = $e->getMessage();
}

$total_guests = count($guests);
// รวมเงินทั้งหมด (แนะนำ query แยก)
$sumStmt = $pdo->query('SELECT COALESCE(SUM(money),0) AS total FROM cost_wedding where "statusDelete" = 0');
$total_amount = $sumStmt->fetch(PDO::FETCH_ASSOC)['total'];

?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($title) ?></title>
  <link rel="icon" type="image/x-icon" href="/wedding-cost/favicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --blush:      #f2c4ce;
      --rose:       #e8a0b0;
      --rose-deep:  #d4829a;
      --sage:       #b8d4c0;
      --sage-deep:  #8ab89a;
      --lavender:   #d4c4e8;
      --champagne:  #f5e6d0;
      --gold:       #c9a96e;
      --gold-light: #e8d4a8;
      --cream:      #fdf6f0;
      --white:      #ffffff;
      --text-dark:  #4a3a4a;
      --text-mid:   #8a7a8a;
      --text-light: #b8a8b8;
    }

    * { margin:0; padding:0; box-sizing:border-box; }

    body {
      font-family: 'Sarabun', sans-serif;
      background-color: var(--cream);
      background-image:
        radial-gradient(ellipse at 0% 0%,   rgba(242,196,206,.35) 0%, transparent 50%),
        radial-gradient(ellipse at 100% 100%,rgba(212,196,232,.30) 0%, transparent 50%),
        radial-gradient(ellipse at 80% 10%,  rgba(184,212,192,.25) 0%, transparent 40%);
      min-height: 100vh;
      color: var(--text-dark);
    }

    /* ── TOP NAV ── */
    nav {
      background: rgba(255,255,255,.75);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(242,196,206,.4);
      padding: 0 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 62px;
      position: sticky; top: 0; z-index: 100;
    }
    .nav-brand {
      font-family: 'Cormorant Garamond', serif;
      font-size: 22px;
      color: var(--text-dark);
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .nav-brand span { font-size: 20px; }
    .nav-actions { display:flex; gap:10px; align-items:center; }
    .nav-btn {
      padding: 7px 18px;
      border-radius: 20px;
      border: 1.5px solid var(--rose);
      background: transparent;
      color: var(--rose-deep);
      font-family: 'Sarabun', sans-serif;
      font-size: 13px;
      cursor: pointer;
      transition: all .2s;
      text-decoration: none;
    }
    .nav-btn:hover { background: var(--blush); }

    /* ── PAGE WRAPPER ── */
    .page { max-width: 1100px; margin: 0 auto; padding: 36px 24px 60px; }

    /* ── PAGE HEADER ── */
    .page-header {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      margin-bottom: 28px;
      flex-wrap: wrap;
      gap: 16px;
    }
    .page-title h1 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 38px;
      font-weight: 600;
      color: var(--text-dark);
      line-height: 1.1;
    }
    .page-title h1 em { font-style: italic; color: var(--rose-deep); }
    .page-title p { font-size: 14px; color: var(--text-mid); margin-top: 4px; }

    .add-btn {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 12px 24px;
      background: linear-gradient(135deg, var(--blush), var(--rose));
      color: #fff;
      border: none;
      border-radius: 14px;
      font-family: 'Sarabun', sans-serif;
      font-size: 15px;
      font-weight: 500;
      cursor: pointer;
      transition: all .2s;
      box-shadow: 0 4px 16px rgba(232,160,176,.4);
    }
    .add-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(232,160,176,.5); }

    /* ── STAT CARDS ── */
    .stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 28px;
    }
    .stat-card {
      background: rgba(255,255,255,.7);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,.85);
      border-radius: 18px;
      padding: 20px 24px;
      box-shadow: 0 2px 12px rgba(200,160,176,.12);
      position: relative;
      overflow: hidden;
      animation: fadeUp .5s ease both;
    }
    .stat-card:nth-child(1) { animation-delay: .05s; }
    .stat-card:nth-child(2) { animation-delay: .10s; }
    .stat-card:nth-child(3) { animation-delay: .15s; }
    .stat-card::before {
      content:'';
      position: absolute;
      top:-20px; right:-20px;
      width:80px; height:80px;
      border-radius:50%;
      opacity:.12;
    }
    .stat-card.pink::before  { background: var(--rose); }
    .stat-card.green::before { background: var(--sage-deep); }
    .stat-card.gold::before  { background: var(--gold); }
    .stat-label { font-size:12px; color:var(--text-mid); letter-spacing:.08em; text-transform:uppercase; margin-bottom:6px; }
    .stat-value { font-family:'Cormorant Garamond',serif; font-size:32px; font-weight:600; color:var(--text-dark); }
    .stat-value.gold-text { color:var(--gold); }
    .stat-icon { position:absolute; bottom:14px; right:18px; font-size:26px; opacity:.35; }

    /* ── TOOLBAR ── */
    .toolbar {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 18px;
      flex-wrap: wrap;
    }
    .search-wrap {
      flex: 1;
      min-width: 220px;
      position: relative;
    }
    .search-wrap input {
      width: 100%;
      padding: 10px 16px 10px 40px;
      border: 1.5px solid rgba(232,160,176,.35);
      border-radius: 12px;
      background: rgba(255,255,255,.7);
      font-family: 'Sarabun', sans-serif;
      font-size: 14px;
      color: var(--text-dark);
      outline: none;
      transition: border-color .2s, box-shadow .2s;
    }
    .search-wrap input:focus { border-color:var(--rose); box-shadow:0 0 0 3px rgba(232,160,176,.15); }
    .search-wrap .s-icon { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--rose); font-size:15px; pointer-events:none; }

    /* ── TABLE ── */
    .table-wrap {
      background: rgba(255,255,255,.72);
      backdrop-filter: blur(14px);
      border: 1px solid rgba(255,255,255,.85);
      border-radius: 20px;
      box-shadow: 0 4px 24px rgba(200,160,176,.14);
      overflow: hidden;
      animation: fadeUp .5s .2s ease both;
    }
    table { width:100%; border-collapse:collapse; }
    thead tr {
      background: linear-gradient(135deg, rgba(242,196,206,.35), rgba(212,196,232,.25));
      border-bottom: 1.5px solid rgba(242,196,206,.5);
    }
    th {
      padding: 14px 18px;
      text-align: left;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--text-mid);
    }
    th.right { text-align:right; }
    tbody tr {
      border-bottom: 1px solid rgba(242,196,206,.2);
      transition: background .15s;
    }
    tbody tr:last-child { border-bottom:none; }
    tbody tr:hover { background: rgba(242,196,206,.1); }
    td {
      padding: 14px 18px;
      font-size: 14px;
      color: var(--text-dark);
      vertical-align: middle;
    }
    td.right { text-align:right; }

    .guest-num {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      width:28px; height:28px;
      background: linear-gradient(135deg,var(--blush),var(--lavender));
      border-radius:50%;
      font-size:12px;
      font-weight:600;
      color:var(--text-dark);
    }
    .guest-name { font-weight:500; }
    .amount-badge {
      display:inline-block;
      padding: 4px 12px;
      background: linear-gradient(135deg, rgba(197,223,197,.4), rgba(184,212,192,.5));
      border: 1px solid rgba(138,184,154,.35);
      border-radius:20px;
      font-weight:600;
      color: #3a7a5a;
      font-size:14px;
    }
    .note-text { color:var(--text-mid); font-size:13px; font-style:italic; }

    /* action buttons */
    .actions { display:flex; gap:8px; justify-content:center; }
    .act-btn {
      width:32px; height:32px;
      border-radius:9px;
      border:none;
      display:flex; align-items:center; justify-content:center;
      cursor:pointer;
      font-size:14px;
      transition: all .15s;
    }
    .act-btn.edit  { background:rgba(212,196,232,.4); color:#7a5a9a; }
    .act-btn.del   { background:rgba(242,196,206,.45); color:#b0607a; }
    .act-btn:hover { transform:scale(1.12); }

    /* empty state */
    .empty {
      text-align:center;
      padding:60px 20px;
      color:var(--text-mid);
    }
    .empty-icon { font-size:48px; margin-bottom:12px; }
    .empty h3 { font-family:'Cormorant Garamond',serif; font-size:22px; margin-bottom:6px; color:var(--text-dark); }

    /* ── MODAL ── */
    .overlay {
      position:fixed; inset:0;
      background:rgba(90,60,80,.35);
      backdrop-filter:blur(4px);
      z-index:200;
      display:flex; align-items:center; justify-content:center;
      opacity:0; pointer-events:none;
      transition:opacity .25s;
      padding:16px;
    }
    .overlay.show { opacity:1; pointer-events:all; }
    .modal {
      background:rgba(255,255,255,.92);
      border:1px solid rgba(255,255,255,.9);
      border-radius:24px;
      padding:36px 32px 28px;
      width:100%; max-width:440px;
      box-shadow:0 20px 60px rgba(180,120,140,.25);
      transform:scale(.94) translateY(16px);
      transition:transform .3s cubic-bezier(.22,1,.36,1);
      position:relative;
    }
    .overlay.show .modal { transform:scale(1) translateY(0); }

    .modal-title {
      font-family:'Cormorant Garamond',serif;
      font-size:26px;
      color:var(--text-dark);
      margin-bottom:22px;
    }
    .modal-title em { font-style:italic; color:var(--rose-deep); }
    .close-btn {
      position:absolute; top:16px; right:16px;
      width:34px; height:34px;
      border-radius:50%; border:none;
      background:rgba(242,196,206,.4);
      color:var(--text-mid); font-size:16px;
      cursor:pointer; display:flex; align-items:center; justify-content:center;
      transition:background .2s;
    }
    .close-btn:hover { background:var(--blush); }

    

    .form-field { margin-bottom:16px; }
    .form-field label {
      display:block;
      font-size:11px; font-weight:600; letter-spacing:.1em;
      text-transform:uppercase; color:var(--text-mid);
      margin-bottom:6px;
    }
    .form-field input, .form-field textarea, .form-field select {
        width: 100%;
        padding: 11px 14px;
        border: 1.5px solid rgba(232,160,176,.35);
        border-radius: 12px;
        background: rgba(255,255,255,.6);
        font-family: 'Sarabun', sans-serif;
        font-size: 15px;
        color: var(--text-dark);
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
ด
    .form-field textarea { resize:vertical; min-height:80px; }

    .modal-footer { display:flex; gap:10px; margin-top:24px; }
    .btn-cancel {
      flex:1; padding:12px;
      border:1.5px solid rgba(232,160,176,.4);
      border-radius:12px; background:transparent;
      font-family:'Sarabun',sans-serif; font-size:14px;
      color:var(--text-mid); cursor:pointer; transition:all .2s;
    }
    .btn-cancel:hover { background:rgba(242,196,206,.2); }
    .btn-save {
      flex:2; padding:12px;
      background:linear-gradient(135deg,var(--blush),var(--rose));
      border:none; border-radius:12px;
      font-family:'Sarabun',sans-serif; font-size:15px; font-weight:500;
      color:#fff; cursor:pointer;
      box-shadow:0 4px 14px rgba(232,160,176,.4);
      transition:all .2s;
    }
    .btn-save:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(232,160,176,.5); }

    /* ── TOAST ── */
    .toast {
      position:fixed; bottom:28px; right:28px; z-index:300;
      background:rgba(255,255,255,.92);
      border:1px solid rgba(255,255,255,.9);
      border-left:4px solid var(--sage-deep);
      border-radius:14px;
      padding:14px 20px;
      box-shadow:0 8px 28px rgba(180,130,150,.2);
      font-size:14px; color:var(--text-dark);
      display:flex; align-items:center; gap:10px;
      animation:slideIn .35s cubic-bezier(.22,1,.36,1) both;
    }
    .toast.error { border-left-color:var(--rose-deep); }
    @keyframes slideIn {
      from { opacity:0; transform:translateX(30px); }
      to   { opacity:1; transform:translateX(0); }
    }
    @keyframes fadeUp {
      from { opacity:0; transform:translateY(20px); }
      to   { opacity:1; transform:translateY(0); }
    }

    @media (max-width:600px) {
      .page { padding:20px 14px 50px; }
      .page-title h1 { font-size:28px; }
      th, td { padding:11px 12px; }
      .modal { padding:28px 20px 22px; }
    }

    .text-center {
        text-align: center;
    }
  </style>
</head>
<body>

<!-- NAV -->
<nav>
  <div class="nav-brand"><span>💍</span> <em>Wedding Manager</em></div>
  <div class="nav-actions">
    <a href="guest.php" class="nav-btn">🏠 หน้าหลัก</a>
    <a href="cost.php" class="nav-btn">ค่าใช้จ่าย</a>
    <a href="/" class="nav-btn">ออกจากระบบ</a>
  </div>
</nav>

<!-- TOAST -->
<?php if ($success): ?>
<div class="toast" id="toast">✅ <?= htmlspecialchars($success) ?></div>
<?php elseif ($error): ?>
<div class="toast error" id="toast">⚠️ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="page">

  <!-- PAGE HEADER -->
  <div class="page-header">
    <button class="add-btn" onclick="openModal()">＋ เพิ่มรายการ</button>
    <div class="page-title">
      <p>บันทึกค่าใช้จ่ายต่างๆ 🌸</p>
    </div>
  </div>

  <!-- STATS -->
  <div class="stats">
    <div class="stat-card pink">
      <div class="stat-label">จำนวนรายการค่าใช้จ่าย</div>
      <div class="stat-value"><?= number_format($total_guests) ?> <small style="font-size:18px;color:var(--text-mid)">รายการ</small></div>
    </div>
    <div class="stat-card gold">
      <div class="stat-label">ยอดรวมค่าใช้จ่าย</div>
      <div class="gold-text stat-value"><?= number_format($total_amount) ?> <small style="font-size:18px">฿</small></div>
    </div>
  </div>

  <!-- TABLE -->
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:48px">ลำดับ</th>
          <th class="text-center">จัดการ</th>
          <th>รายการค่าใช้จ่าย</th>
          <th>จำนวนเงิน(บาท)</th>
          <th>หมายเหตุ</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($guests)): ?>
        <tr><td colspan="5">
          <div class="empty">
            <div class="empty-icon">💌</div>
            <h3><?= $search ? 'ไม่พบรายการที่ค้นหา' : 'ยังไม่มีรายการค่าใช้จ่าย' ?></h3>
            <p><?= $search ? 'ลองค้นหาด้วยคำอื่น' : 'กดปุ่ม "เพิ่มรายการ" เพื่อเริ่มต้น' ?></p>
          </div>
        </td></tr>
        <?php else: ?>
        <?php foreach ($guests as $i => $g): ?>
        <tr>
          <td><span class="guest-num"><?= $i + 1 ?></span></td>
          <td class="text-center">
            <div class="actions">
              <button class="act-btn edit" title="แก้ไข"
                onclick="openEdit(<?= $g['id'] ?>, '<?= addslashes(htmlspecialchars($g['cost_name'])) ?>', <?= $g['money'] ?>, '<?= addslashes(htmlspecialchars($g['remark'] ?? '')) ?>')">✏️</button>
              <form method="POST" onsubmit="return confirm('ลบ <?= addslashes(htmlspecialchars($g['cost_name'])) ?> ใช่ไหม?')" style="display:inline">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $g['id'] ?>">
                <button class="act-btn del" type="submit" title="ลบ">🗑️</button>
              </form>
            </div>
          </td>
          <td class="guest-name"><?= $g['cost_name'] ?></td>
          <td><span class="amount-badge">฿<?= $g['money'] ?></span></td>
          <td class="note-text"><?= $g['remark'] ? htmlspecialchars($g['remark']) : '—' ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>

<!-- MODAL: ADD / EDIT -->
<div class="overlay" id="overlay" onclick="closeOnBg(event)">
  <div class="modal">
    <button class="close-btn" onclick="closeModal()">✕</button>
    <div class="modal-title" id="modal-title">เพิ่มรายการ</div>

    <form method="POST" id="modal-form" action="/api/costWedding/save.php">
      <input type="hidden" name="action" id="form-action" value="add">
      <input type="hidden" name="id"     id="form-id"     value="">

      <div class="form-field">
        <label>รายการค่าใช้จ่าย *</label>
        <input type="text" name="costName" id="form-name" placeholder="เช่น ค่างานแต่ง" required>
      </div>

      <div class="form-field">
        <label>จำนวนเงิน(บาท) *</label>
        <input type="number" name="money" id="form-amount" placeholder="เช่น 1000" min="0" step="100" required>
      </div>

      <div class="form-field">
        <label>หมายเหตุ</label>
        <textarea name="remark" id="form-note" placeholder="เช่น ค่าใช้จ่ายเพิ่มเติม รายละเอียดต่างๆ"></textarea>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn-save" id="save-btn">✦ บันทึก</button>
        <button type="button" class="btn-cancel" onclick="closeModal()">ยกเลิก</button>
      </div>
    </form>
  </div>
</div>

<script>
  // Toast auto-hide
  const toast = document.getElementById('toast');
  if (toast) setTimeout(() => toast.style.opacity = '0', 3500);

  function openModal() {
    document.getElementById('modal-title').innerHTML = 'เพิ่มข้อมูล';
    document.getElementById('form-action').value = 'add';
    document.getElementById('form-id').value = '';
    document.getElementById('form-name').value = '';
    document.getElementById('form-amount').value = '';
    document.getElementById('form-note').value = '';
    document.getElementById('save-btn').textContent = '✦ บันทึกข้อมูล';
    document.getElementById('overlay').classList.add('show');
    setTimeout(() => document.getElementById('form-name').focus(), 200);
  }

  function openEdit(id, name, amount, note) {
    document.getElementById('modal-title').innerHTML = 'แก้ไขข้อมูล';
    document.getElementById('form-action').value = 'edit';
    document.getElementById('form-id').value = id;
    document.getElementById('form-name').value = name;
    document.getElementById('form-amount').value = amount;
    document.getElementById('form-note').value = note;
    document.getElementById('save-btn').textContent = '✦ บันทึกการแก้ไข';
    document.getElementById('overlay').classList.add('show');
    setTimeout(() => document.getElementById('form-name').focus(), 200);
  }

  function closeModal() {
    document.getElementById('overlay').classList.remove('show');
  }

  function closeOnBg(e) {
    if (e.target === document.getElementById('overlay')) closeModal();
  }

  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

  // Auto-search on type
  let searchTimer;
  const searchInput = document.querySelector('.search-wrap input');
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(() => searchInput.closest('form').submit(), 500);
    });
  }
</script>

<?php include 'layout/footer.php'; ?>
</body>
</html>
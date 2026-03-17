<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db.php';

$title = "เข้าสู่ระบบ - งานแต่งงาน";

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare('SELECT * FROM "user" WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            session_start();
            $_SESSION['user_id'] = $user['id'];

            header('Location: guest.php');
            exit;
        } else {
            $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
        }
    } catch (PDOException $e) {
        $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($title) ?></title>
  <link rel="icon" type="image/x-icon" href="/wedding-cost/favicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Sarabun:wght@300;400;500&display=swap" rel="stylesheet">
  <style>
    :root {
      --blush:     #f2c4ce;
      --rose:      #e8a0b0;
      --sage:      #b8d4c0;
      --lavender:  #d4c4e8;
      --cream:     #fdf6f0;
      --champagne: #f5e6d0;
      --gold:      #c9a96e;
      --text-dark: #5a4a5a;
      --text-mid:  #8a7a8a;
      --white:     #ffffff;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Sarabun', sans-serif;
      background-color: var(--cream);
      background-image:
        radial-gradient(ellipse at 15% 20%, rgba(242,196,206,0.45) 0%, transparent 55%),
        radial-gradient(ellipse at 85% 75%, rgba(212,196,232,0.40) 0%, transparent 55%),
        radial-gradient(ellipse at 60% 10%, rgba(184,212,192,0.30) 0%, transparent 45%);
      overflow: hidden;
    }

    /* Floating petals */
    .petal {
      position: fixed;
      pointer-events: none;
      opacity: 0;
      animation: fall linear infinite;
    }
    .petal svg { width: 100%; height: 100%; }

    @keyframes fall {
      0%   { transform: translateY(-60px) rotate(0deg);   opacity: 0; }
      10%  { opacity: 0.7; }
      90%  { opacity: 0.5; }
      100% { transform: translateY(110vh) rotate(360deg); opacity: 0; }
    }

    /* Card */
    .card-wrap {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 420px;
      padding: 16px;
    }

    .card {
      background: rgba(255,255,255,0.72);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      border: 1px solid rgba(255,255,255,0.85);
      border-radius: 28px;
      padding: 48px 40px 40px;
      box-shadow:
        0 8px 32px rgba(200,160,176,0.18),
        0 2px 8px rgba(200,160,176,0.10),
        inset 0 1px 0 rgba(255,255,255,0.9);
      animation: fadeUp 0.7s cubic-bezier(0.22,1,0.36,1) both;
    }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(28px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* Monogram ring */
    .monogram {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 28px;
    }

    .ring {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--blush), var(--lavender));
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      margin-bottom: 14px;
      box-shadow: 0 4px 20px rgba(232,160,176,0.35);
    }
    .ring::before {
      content: '';
      position: absolute;
      inset: -5px;
      border-radius: 50%;
      border: 1.5px dashed rgba(201,169,110,0.5);
    }
    .ring-icon {
      font-size: 32px;
      line-height: 1;
    }

    .title {
      font-family: 'Playfair Display', serif;
      font-size: 26px;
      font-weight: 600;
      color: var(--text-dark);
      text-align: center;
      letter-spacing: 0.02em;
    }
    .title em {
      font-style: italic;
      color: var(--rose);
    }

    .subtitle {
      font-size: 13px;
      color: var(--text-mid);
      text-align: center;
      margin-top: 6px;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    /* Divider */
    .divider {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 24px 0;
    }
    .divider-line {
      flex: 1;
      height: 1px;
      background: linear-gradient(to right, transparent, rgba(201,169,110,0.4), transparent);
    }
    .divider-icon { font-size: 14px; color: var(--gold); }

    /* Form */
    .field {
      margin-bottom: 18px;
    }
    .field label {
      display: block;
      font-size: 12px;
      font-weight: 500;
      color: var(--text-mid);
      letter-spacing: 0.1em;
      text-transform: uppercase;
      margin-bottom: 7px;
    }

    .input-wrap {
      position: relative;
    }
    .input-wrap .icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 16px;
      color: var(--rose);
      pointer-events: none;
    }
    .field input {
      width: 100%;
      padding: 13px 16px 13px 42px;
      border: 1.5px solid rgba(232,160,176,0.35);
      border-radius: 14px;
      background: rgba(255,255,255,0.6);
      font-family: 'Sarabun', sans-serif;
      font-size: 15px;
      color: var(--text-dark);
      outline: none;
      transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
    }
    .field input::placeholder { color: #c0b0c0; }
    .field input:focus {
      border-color: var(--rose);
      background: rgba(255,255,255,0.88);
      box-shadow: 0 0 0 4px rgba(232,160,176,0.15);
    }

    /* Error */
    .error-msg {
      background: rgba(242,196,206,0.4);
      border: 1px solid rgba(232,160,176,0.5);
      border-radius: 10px;
      padding: 10px 14px;
      font-size: 13px;
      color: #b06070;
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* Button */
    .btn {
      width: 100%;
      padding: 14px;
      border: none;
      border-radius: 14px;
      background: linear-gradient(135deg, var(--blush) 0%, var(--rose) 50%, #d4829a 100%);
      color: #fff;
      font-family: 'Sarabun', sans-serif;
      font-size: 16px;
      font-weight: 500;
      letter-spacing: 0.05em;
      cursor: pointer;
      transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
      box-shadow: 0 4px 18px rgba(232,160,176,0.45);
      position: relative;
      overflow: hidden;
    }
    .btn::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(180deg, rgba(255,255,255,0.2) 0%, transparent 60%);
      pointer-events: none;
    }
    .btn:hover  { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 6px 22px rgba(232,160,176,0.5); }
    .btn:active { transform: translateY(1px); }

    /* Footer note */
    .card-footer {
      text-align: center;
      margin-top: 20px;
      font-size: 12px;
      color: var(--text-mid);
    }
    .card-footer a {
      color: var(--rose);
      text-decoration: none;
      font-weight: 500;
    }
    .card-footer a:hover { text-decoration: underline; }

    /* Floral corners */
    .corner {
      position: absolute;
      width: 70px;
      height: 70px;
      opacity: 0.55;
    }
    .corner-tl { top: -4px;  left: -4px;  transform: rotate(0deg); }
    .corner-br { bottom: -4px; right: -4px; transform: rotate(180deg); }
  </style>
</head>
<body>

<!-- Floating petals -->
<script>
  const colors = ['#f2c4ce','#d4c4e8','#b8d4c0','#f5e6d0','#e8a0b0'];
  for (let i = 0; i < 18; i++) {
    const p = document.createElement('div');
    p.className = 'petal';
    const size = 10 + Math.random() * 14;
    const color = colors[Math.floor(Math.random() * colors.length)];
    p.style.cssText = `
      left: ${Math.random()*100}vw;
      width: ${size}px; height: ${size}px;
      animation-duration: ${6+Math.random()*8}s;
      animation-delay: ${Math.random()*10}s;
    `;
    p.innerHTML = `<svg viewBox="0 0 40 40"><ellipse cx="20" cy="20" rx="14" ry="9" fill="${color}" opacity="0.8" transform="rotate(${Math.random()*360},20,20)"/></svg>`;
    document.body.appendChild(p);
  }
</script>

<div class="card-wrap">
  <div class="card">

    <!-- Floral SVG corners -->
    <svg class="corner corner-tl" viewBox="0 0 80 80" fill="none">
      <circle cx="10" cy="10" r="6" fill="#f2c4ce" opacity=".7"/>
      <circle cx="26" cy="8"  r="4" fill="#d4c4e8" opacity=".6"/>
      <circle cx="8"  cy="26" r="4" fill="#b8d4c0" opacity=".6"/>
      <path d="M10 10 Q30 5 35 30" stroke="#c9a96e" stroke-width="1" fill="none" opacity=".4"/>
      <path d="M10 10 Q5 30 30 35"  stroke="#c9a96e" stroke-width="1" fill="none" opacity=".4"/>
      <circle cx="35" cy="30" r="3" fill="#f2c4ce" opacity=".5"/>
      <circle cx="30" cy="35" r="3" fill="#e8a0b0" opacity=".5"/>
    </svg>
    <svg class="corner corner-br" viewBox="0 0 80 80" fill="none">
      <circle cx="10" cy="10" r="6" fill="#f2c4ce" opacity=".7"/>
      <circle cx="26" cy="8"  r="4" fill="#d4c4e8" opacity=".6"/>
      <circle cx="8"  cy="26" r="4" fill="#b8d4c0" opacity=".6"/>
      <path d="M10 10 Q30 5 35 30" stroke="#c9a96e" stroke-width="1" fill="none" opacity=".4"/>
      <path d="M10 10 Q5 30 30 35"  stroke="#c9a96e" stroke-width="1" fill="none" opacity=".4"/>
      <circle cx="35" cy="30" r="3" fill="#f2c4ce" opacity=".5"/>
      <circle cx="30" cy="35" r="3" fill="#e8a0b0" opacity=".5"/>
    </svg>

    <!-- Header -->
    <div class="monogram">
      <div class="ring">
        <span class="ring-icon">💍</span>
      </div>
      <h1 class="title"><em>Wedding Management</em></h1>
      <p class="subtitle">เข้าสู่ระบบ</p>
    </div>

    <div class="divider">
      <div class="divider-line"></div>
      <span class="divider-icon">✦</span>
      <div class="divider-line"></div>
    </div>

    <!-- Error -->
    <?php if (!empty($error)): ?>
    <div class="error-msg">
      <span>🌸</span>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Form -->
    <form method="POST" action="">
      <div class="field">
        <label for="username">ชื่อผู้ใช้</label>
        <div class="input-wrap">
          <input
            type="text"
            id="username"
            name="username"
            placeholder="กรอกชื่อผู้ใช้"
            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
            required
            autocomplete="username"
          />
        </div>
      </div>

      <div class="field">
        <label for="password">รหัสผ่าน</label>
        <div class="input-wrap">
          <input
            type="password"
            id="password"
            name="password"
            placeholder="กรอกรหัสผ่าน"
            required
            autocomplete="current-password"
          />
        </div>
      </div>

      <button type="submit" class="btn">✦ เข้าสู่ระบบ ✦</button>
    </form>

  </div>
</div>

<?php include 'layout/footer.php'; ?>
</body>
</html>
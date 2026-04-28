<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>MangoNet — Setup</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#f8f9fa}.card{max-width:600px;margin:60px auto}</style>
</head>
<body>
<div class="container">
  <div class="card shadow-sm">
    <div class="card-header bg-warning text-dark fw-bold fs-5">MangoNet — Database Setup</div>
    <div class="card-body">
<?php
$submitted = isset($_POST['submit']);
$error = '';
$success = false;

if ($submitted) {
    $host   = trim($_POST['db_host'] ?? 'localhost');
    $port   = trim($_POST['db_port'] ?? '3306');
    $name   = trim($_POST['db_name'] ?? '');
    $user   = trim($_POST['db_user'] ?? '');
    $pass   = $_POST['db_pass'] ?? '';
    $admin  = trim($_POST['admin_user'] ?? 'admin');
    $apwd   = $_POST['admin_pass'] ?? '';

    if (!$name || !$user) {
        $error = 'Database name and username are required.';
    } elseif (strlen($apwd) < 6) {
        $error = 'Admin password must be at least 6 characters.';
    } else {
        try {
            $pdo = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            $pdo->exec("CREATE TABLE IF NOT EXISTS `settings` (
                `key` VARCHAR(100) PRIMARY KEY,
                `value` LONGTEXT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `admin_users` (
                `id` VARCHAR(36) PRIMARY KEY,
                `username` VARCHAR(100) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL,
                `role` VARCHAR(20) DEFAULT 'admin'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `submissions` (
                `id` VARCHAR(36) PRIMARY KEY,
                `first_name` TEXT NOT NULL,
                `last_name` TEXT NOT NULL,
                `email` VARCHAR(255) NOT NULL,
                `phone` VARCHAR(30) NOT NULL,
                `address` TEXT NOT NULL,
                `city` VARCHAR(100) NOT NULL,
                `state` VARCHAR(100) NOT NULL,
                `zip_code` VARCHAR(20) DEFAULT NULL,
                `plan` VARCHAR(100) NOT NULL,
                `wifi_ssid` VARCHAR(100) NOT NULL,
                `wifi_password` VARCHAR(100) NOT NULL,
                `installation_date` VARCHAR(50) NOT NULL,
                `notes` TEXT DEFAULT NULL,
                `status` VARCHAR(20) DEFAULT 'pending',
                `payment_ref` VARCHAR(100) DEFAULT NULL,
                `submitted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `passport_photo` LONGTEXT DEFAULT NULL,
                `govt_id` LONGTEXT DEFAULT NULL,
                `proof_of_address` LONGTEXT DEFAULT NULL,
                `nin` VARCHAR(30) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $pdo->exec("CREATE TABLE IF NOT EXISTS `plans` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL,
                `price` VARCHAR(50) NOT NULL,
                `speed` VARCHAR(30) NOT NULL,
                `category` VARCHAR(30) NOT NULL DEFAULT 'Residential',
                `location_zone` VARCHAR(50) NOT NULL DEFAULT 'default',
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `sort_order` INT NOT NULL DEFAULT 0
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // Seed default plans if table is empty
            $planCount = $pdo->query("SELECT COUNT(*) FROM plans")->fetchColumn();
            if ($planCount == 0) {
                $seedPlans = [
                    ['Mango Basic','NGN 14,067','25Mbps','Residential','default',1],
                    ['Mango Plus','NGN 19,929','40Mbps','Residential','default',2],
                    ['Mango Premium','NGN 25,790','60Mbps','Residential','default',3],
                    ['Mango Premium+','NGN 35,172','80Mbps','Residential','default',4],
                    ['Mango Gold','NGN 40,447','120Mbps','Residential','default',5],
                    ['Mango Diamond','NGN 48,362','165Mbps','Residential','default',6],
                    ['Mango Platinum','NGN 58,245','200Mbps','Residential','default',7],
                    ['Mango SME','NGN 29,306','65Mbps','Corporate','default',8],
                    ['Mango Corporate Plus','NGN 52,751','100Mbps','Corporate','default',9],
                    ['Mango Corporate Premium','NGN 58,613','140Mbps','Corporate','default',10],
                    ['Mango Preferred','NGN 67,404','200Mbps','Corporate','default',11],
                    ['Mango Advantage','NGN 89,648','250Mbps','Corporate','default',12],
                    ['Mango Ultimate','NGN 107,578','350Mbps','Corporate','default',13],
                    ['Mango Basic','NGN 14,067','25Mbps','Residential','oniru',1],
                    ['Mango Plus','NGN 19,929','40Mbps','Residential','oniru',2],
                    ['Mango Premium','NGN 25,790','60Mbps','Residential','oniru',3],
                    ['Mango Premium+','NGN 35,172','80Mbps','Residential','oniru',4],
                    ['Mango Gold','NGN 40,447','120Mbps','Residential','oniru',5],
                    ['Mango Diamond','NGN 48,362','165Mbps','Residential','oniru',6],
                    ['Mango Platinum','NGN 58,245','200Mbps','Residential','oniru',7],
                    ['Mango SME','NGN 29,306','65Mbps','Corporate','oniru',8],
                    ['Mango Corporate Plus','NGN 52,751','100Mbps','Corporate','oniru',9],
                    ['Mango Corporate Premium','NGN 58,613','140Mbps','Corporate','oniru',10],
                    ['Mango Preferred','NGN 67,404','200Mbps','Corporate','oniru',11],
                    ['Mango Advantage','NGN 89,648','250Mbps','Corporate','oniru',12],
                    ['Mango Ultimate','NGN 107,578','350Mbps','Corporate','oniru',13],
                    ['Mango Basic','NGN 14,067','25Mbps','Residential','abuja_banex',1],
                    ['Mango Plus','NGN 19,929','40Mbps','Residential','abuja_banex',2],
                    ['Mango Premium','NGN 25,790','60Mbps','Residential','abuja_banex',3],
                    ['Mango Premium+','NGN 35,172','80Mbps','Residential','abuja_banex',4],
                    ['Mango Gold','NGN 40,447','120Mbps','Residential','abuja_banex',5],
                    ['Mango Diamond','NGN 48,362','165Mbps','Residential','abuja_banex',6],
                    ['Mango Platinum','NGN 58,245','200Mbps','Residential','abuja_banex',7],
                    ['Mango SME','NGN 29,306','65Mbps','Corporate','abuja_banex',8],
                    ['Mango Corporate Plus','NGN 52,751','100Mbps','Corporate','abuja_banex',9],
                    ['Mango Corporate Premium','NGN 58,613','140Mbps','Corporate','abuja_banex',10],
                    ['Mango Preferred','NGN 67,404','200Mbps','Corporate','abuja_banex',11],
                    ['Mango Advantage','NGN 89,648','250Mbps','Corporate','abuja_banex',12],
                    ['Mango Ultimate','NGN 107,578','350Mbps','Corporate','abuja_banex',13],
                ];
                $ins = $pdo->prepare("INSERT INTO plans (name,price,speed,category,location_zone,sort_order) VALUES (?,?,?,?,?,?)");
                foreach ($seedPlans as $p) $ins->execute($p);
            }

            $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0,0xffff),mt_rand(0,0xffff),mt_rand(0,0xffff),
                mt_rand(0,0x0fff)|0x4000,mt_rand(0,0x3fff)|0x8000,
                mt_rand(0,0xffff),mt_rand(0,0xffff),mt_rand(0,0xffff));
            $hash = password_hash($apwd, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT IGNORE INTO admin_users (id, username, password, role) VALUES (?,?,?,'admin')");
            $stmt->execute([$uuid, $admin, $hash]);

            $config = "<?php\n// Auto-generated by setup/install.php\ndefine('DB_HOST', " . var_export($host,true) . ");\n"
                . "define('DB_PORT', " . var_export($port,true) . ");\n"
                . "define('DB_NAME', " . var_export($name,true) . ");\n"
                . "define('DB_USER', " . var_export($user,true) . ");\n"
                . "define('DB_PASS', " . var_export($pass,true) . ");\n"
                . "define('APP_URL', " . var_export((isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'],true) . ");\n";
            file_put_contents(__DIR__ . '/../config.php', $config);

            $success = true;
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<?php if ($success): ?>
  <div class="alert alert-success">
    <strong>Setup complete!</strong> Database configured and admin account created.
  </div>
  <p>You can now <a href="/admin/login.php" class="btn btn-primary btn-sm">Go to Admin Login</a></p>
  <p class="text-muted small mt-3">⚠️ For security, delete or restrict access to the <code>setup/</code> folder after setup.</p>
<?php else: ?>
  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif ?>
  <form method="post">
    <h6 class="text-muted mb-3">Database Connection</h6>
    <div class="row g-2 mb-2">
      <div class="col-8"><label class="form-label small">Host</label>
        <input class="form-control" name="db_host" value="localhost" required></div>
      <div class="col-4"><label class="form-label small">Port</label>
        <input class="form-control" name="db_port" value="3306" required></div>
    </div>
    <div class="mb-2"><label class="form-label small">Database Name</label>
      <input class="form-control" name="db_name" required></div>
    <div class="mb-2"><label class="form-label small">Username</label>
      <input class="form-control" name="db_user" required></div>
    <div class="mb-3"><label class="form-label small">Password</label>
      <input class="form-control" type="password" name="db_pass"></div>
    <hr>
    <h6 class="text-muted mb-3">Admin Account</h6>
    <div class="mb-2"><label class="form-label small">Admin Username</label>
      <input class="form-control" name="admin_user" value="admin" required></div>
    <div class="mb-3"><label class="form-label small">Admin Password</label>
      <input class="form-control" type="password" name="admin_pass" required></div>
    <button class="btn btn-warning w-100 fw-bold" type="submit" name="submit">Run Setup</button>
  </form>
<?php endif ?>
    </div>
  </div>
</div>
</body>
</html>

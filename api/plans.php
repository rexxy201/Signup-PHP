<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

// Ensure plans table exists (auto-migrate for existing installs)
function ensurePlansTable(): void {
    $db = getDb();
    $db->exec("CREATE TABLE IF NOT EXISTS `plans` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `price` VARCHAR(50) NOT NULL,
        `speed` VARCHAR(30) NOT NULL,
        `category` VARCHAR(30) NOT NULL DEFAULT 'Residential',
        `location_zone` VARCHAR(50) NOT NULL DEFAULT 'default',
        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
        `sort_order` INT NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Seed if empty
    $count = $db->query("SELECT COUNT(*) FROM plans")->fetchColumn();
    if ($count == 0) {
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
        $ins = $db->prepare("INSERT INTO plans (name,price,speed,category,location_zone,sort_order) VALUES (?,?,?,?,?,?)");
        foreach ($seedPlans as $p) $ins->execute($p);
    }
}

ensurePlansTable();

$zone = $_GET['zone'] ?? 'default';
$db = getDb();
$stmt = $db->prepare("SELECT * FROM plans WHERE location_zone = ? AND is_active = 1 ORDER BY sort_order, id");
$stmt->execute([$zone]);
echo json_encode($stmt->fetchAll());

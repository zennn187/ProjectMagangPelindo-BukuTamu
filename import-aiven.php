<?php
// Import database/buku_tamu_export.sql ke Aiven via PHP PDO.
// Alasan: mysql.exe XAMPP lawas tidak bisa memuat plugin caching_sha2_password
// modern milik Aiven, sedangkan PDO php.exe (mysqlnd) mendukungnya.
// Cara pakai: php import-aiven.php   (password & CA sudah terisi di bawah)

$host = getenv('AIVEN_HOST') ?: 'mysql-37bc1d-bukutamu-pelindo.b.aivencloud.com';
$port = getenv('AIVEN_PORT') ?: 13192;
$db   = getenv('AIVEN_DB') ?: 'buku_tamu';
$user = getenv('AIVEN_USER') ?: 'avnadmin';
$pass = getenv('AIVEN_PASSWORD') ?: ''; // isi via env, JANGAN hardcode password asli
$ca   = __DIR__ . '/database/aiven-ca.pem';
$sqlFile = __DIR__ . '/database/buku_tamu_export.sql';

if (! file_exists($ca)) { fwrite(STDERR, "[GAGAL] CA tidak ketemu: $ca\n"); exit(1); }
if (! file_exists($sqlFile)) { fwrite(STDERR, "[GAGAL] SQL tidak ketemu: $sqlFile\n"); exit(1); }

echo "Mengimpor ke $host:$port/$db ...\n";
try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_SSL_CA => $ca,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, // CA project Aiven self-signed
        ]
    );
} catch (Throwable $e) {
    fwrite(STDERR, '[GAGAL] Konek: ' . $e->getMessage() . "\n");
    exit(1);
}

$sql = file_get_contents($sqlFile);
// Parser mysqldump yang benar:
// - buang baris komentar (-- dan /*...*/)
// - abaikan perintah LOCK/UNLOCK/ALTER ... KEYS
// - pecah statement pada baris yang hanya berisi ';'
$clean = [];
foreach (explode("\n", $sql) as $line) {
    $t = ltrim($line);
    if ($t === '' || str_starts_with($t, '--') || str_starts_with($t, '/*') || str_starts_with($t, '*/')) continue;
    if (preg_match('/^(LOCK TABLES|UNLOCK TABLES|ALTER TABLE .* (DISABLE|ENABLE) KEYS)/i', $t)) continue;
    $clean[] = $line;
}
$statements = array_filter(array_map('trim', explode(";\n", implode("\n", $clean))));

$ok = 0; $fail = 0;
try { $pdo->exec('SET FOREIGN_KEY_CHECKS=0'); } catch (Throwable $e) {}
foreach ($statements as $stmt) {
    $stmt = rtrim($stmt, ';');
    if ($stmt === '' || str_starts_with($stmt, '/*!')) continue;
    try { $pdo->exec($stmt); $ok++; }
    catch (Throwable $e) { $fail++; fwrite(STDERR, '[SKIP] ' . substr($stmt, 0, 80) . ' ... :: ' . $e->getMessage() . "\n"); }
}
try { $pdo->exec('SET FOREIGN_KEY_CHECKS=1'); } catch (Throwable $e) {}
echo "[SELESAI] OK=$ok Gagal=$fail\n";
foreach (['visits' => 'visits', 'users' => 'users', 'employees' => 'employees', 'gate_logs' => 'gate_logs'] as $label => $table) {
    try { echo "$label=" . $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn() . ' '; }
    catch (Throwable $e) { echo "$label=ERR "; }
}
echo "\n";

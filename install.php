<?php
/**
 * True Chain Infrastructure Company - one-time installer.
 *
 * Upload the site, browse to /install.php, fill in the form, then DELETE
 * this file. The installer also locks itself after a successful run.
 */
declare(strict_types=1);

define('ROOT_PATH', __DIR__);
define('APP_PATH', __DIR__ . '/app');
const LOCK_FILE = APP_PATH . '/storage/installed.lock';

error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();

require APP_PATH . '/core/Database.php';
require APP_PATH . '/core/Schema.php';
require APP_PATH . '/core/Seed.php';

function h($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

$installed = is_file(LOCK_FILE) && is_file(APP_PATH . '/config.php');

// ---------------- requirement checks ----------------
// Each check is [passed?, how to fix it, blocks the install?]. The two database
// drivers do not block on their own - one working driver is enough.
$hasPdoMysql  = extension_loaded('pdo_mysql');
$hasPdoSqlite = extension_loaded('pdo_sqlite');

$checks = [
    'PHP 8.0 or newer (you have ' . PHP_VERSION . ')' => [
        version_compare(PHP_VERSION, '8.0.0', '>='),
        'cPanel → Select PHP Version → choose PHP 8.1 or newer, then reload this page.',
        true,
    ],
    'PDO extension' => [
        extension_loaded('pdo'),
        'cPanel → Select PHP Version → Extensions → tick "pdo", then reload this page.',
        true,
    ],
    'PDO MySQL driver (for GoDaddy)' => [
        $hasPdoMysql,
        'cPanel → Select PHP Version → Extensions → tick "pdo_mysql" (listed as "nd_pdo_mysql" on some GoDaddy plans), save, then reload this page.',
        false,
    ],
    'PDO SQLite driver (fallback)' => [
        $hasPdoSqlite,
        'cPanel → Select PHP Version → Extensions → tick "pdo_sqlite". Only needed if you cannot enable MySQL.',
        false,
    ],
    'app/ directory writable' => [
        is_writable(APP_PATH),
        'cPanel → File Manager → right-click app/ → Change Permissions → 755.',
        true,
    ],
    'uploads/ directory writable' => [
        is_writable(ROOT_PATH . '/uploads') || @mkdir(ROOT_PATH . '/uploads', 0755),
        'Create an uploads/ folder beside index.php and set its permissions to 755.',
        true,
    ],
    'OpenSSL / random_bytes' => [
        function_exists('random_bytes'),
        'cPanel → Select PHP Version → Extensions → tick "openssl", then reload this page.',
        true,
    ],
];

$blocked = false;
foreach ($checks as $check) {
    if ($check[2] && !$check[0]) {
        $blocked = true;
    }
}
$checksPass = !$blocked && ($hasPdoMysql || $hasPdoSqlite);
$defaultDriver = $hasPdoMysql ? 'mysql' : 'sqlite';

// ---------------- CSRF for installer ----------------
if (empty($_SESSION['ins_token'])) {
    $_SESSION['ins_token'] = bin2hex(random_bytes(24));
}

$errors = [];
$done = false;
$adminUrl = preg_replace('#/install\.php.*$#', '/admin', $_SERVER['REQUEST_URI'] ?? '/') ?: '/admin';
$homeUrl = preg_replace('#/install\.php.*$#', '/', $_SERVER['REQUEST_URI'] ?? '/') ?: '/';

if (!$installed && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['ins_token'], (string)($_POST['_token'] ?? ''))) {
        $errors[] = 'Security token mismatch. Please try again.';
    } else {
        $driver  = ($_POST['driver'] ?? 'mysql') === 'sqlite' ? 'sqlite' : 'mysql';
        $host    = trim((string)($_POST['db_host'] ?? 'localhost')) ?: 'localhost';
        $port    = trim((string)($_POST['db_port'] ?? '3306')) ?: '3306';
        $name    = trim((string)($_POST['db_name'] ?? ''));
        $user    = trim((string)($_POST['db_user'] ?? ''));
        $pass    = (string)($_POST['db_pass'] ?? '');
        $prefix  = trim((string)($_POST['db_prefix'] ?? 'tcic_'));
        $aName   = trim((string)($_POST['admin_name'] ?? ''));
        $aEmail  = strtolower(trim((string)($_POST['admin_email'] ?? '')));
        $aPass   = (string)($_POST['admin_pass'] ?? '');
        $aPass2  = (string)($_POST['admin_pass2'] ?? '');

        if ($prefix !== '' && !preg_match('/^[A-Za-z0-9_]{1,20}$/', $prefix)) {
            $errors[] = 'Table prefix may only contain letters, numbers and underscores.';
        }
        if ($driver === 'mysql' && !$hasPdoMysql) {
            $errors[] = 'This server has no PDO MySQL driver. Enable "pdo_mysql" in cPanel → Select PHP Version → Extensions, or choose SQLite as the database type.';
        }
        if ($driver === 'sqlite' && !$hasPdoSqlite) {
            $errors[] = 'This server has no PDO SQLite driver. Enable "pdo_sqlite" in cPanel → Select PHP Version → Extensions, or choose MySQL as the database type.';
        }
        if ($driver === 'mysql' && ($name === '' || $user === '')) {
            $errors[] = 'Please provide the MySQL database name and user.';
        }
        if ($aName === '') {
            $errors[] = 'Please provide the administrator name.';
        }
        if (!filter_var($aEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please provide a valid administrator email address.';
        }
        if (strlen($aPass) < 10) {
            $errors[] = 'Administrator password must be at least 10 characters.';
        }
        if ($aPass !== $aPass2) {
            $errors[] = 'The two administrator passwords do not match.';
        }

        if (!$errors) {
            $dbCfg = [
                'driver' => $driver, 'host' => $host, 'port' => $port,
                'name' => $name, 'user' => $user, 'pass' => $pass,
                'prefix' => $prefix,
                'sqlite_path' => APP_PATH . '/storage/tcic.sqlite',
            ];
            try {
                if (!is_dir(APP_PATH . '/storage')) {
                    mkdir(APP_PATH . '/storage', 0755, true);
                }
                DB::init($dbCfg);

                // Create schema (idempotent for tables; indexes may already exist)
                foreach (Schema::statements($driver, $prefix) as $sql) {
                    try {
                        DB::pdo()->exec($sql);
                    } catch (PDOException $e) {
                        // Ignore "already exists" index collisions on re-run
                        if (stripos($e->getMessage(), 'exist') === false && stripos($e->getMessage(), 'Duplicate') === false) {
                            throw $e;
                        }
                    }
                }

                // Seed only when empty
                $userCount = (int)DB::val('SELECT COUNT(*) FROM ' . DB::table('users'));
                if ($userCount === 0) {
                    Seed::run($aName, $aEmail, $aPass);
                }

                // Write config
                $config = [
                    'db'  => $dbCfg,
                    'app' => [
                        'key'          => bin2hex(random_bytes(32)),
                        'session_name' => 'TCICSESS',
                        'session_idle' => 3600,
                        'debug'        => false,
                    ],
                ];
                $php = "<?php\n// Generated by install.php on " . date('c') . "\nreturn " . var_export($config, true) . ";\n";
                if (file_put_contents(APP_PATH . '/config.php', $php, LOCK_EX) === false) {
                    throw new RuntimeException('Could not write app/config.php. Check permissions.');
                }
                @chmod(APP_PATH . '/config.php', 0640);

                file_put_contents(LOCK_FILE, date('c'));
                // Protect storage even if .htaccess in /app is removed
                @file_put_contents(APP_PATH . '/storage/.htaccess', "Require all denied\n");

                $done = true;
            } catch (Throwable $ex) {
                $errors[] = 'Installation failed: ' . $ex->getMessage();
            }
        }
    }
}
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Install · True Chain Infrastructure Company</title>
<style>
:root{--blue:#17579E;--navy:#0B1B33;--ink:#15243B;--line:#E3E9F2;--bg:#F4F7FB;--ok:#157F3D;--err:#B42318}
*{box-sizing:border-box}body{margin:0;font:15px/1.6 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;background:var(--bg);color:var(--ink)}
.wrap{max-width:760px;margin:40px auto;padding:0 20px}
.card{background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 10px 30px rgba(11,27,51,.06);overflow:hidden}
.head{background:linear-gradient(135deg,var(--navy),#123969);color:#fff;padding:28px 32px}
.head h1{margin:0 0 4px;font-size:22px}.head p{margin:0;opacity:.85}
.body{padding:28px 32px}
h2{font-size:16px;margin:26px 0 10px;color:var(--navy)}
table.checks{width:100%;border-collapse:collapse;margin:8px 0 4px}
table.checks td{padding:7px 4px;border-bottom:1px solid var(--line)}
.ok{color:var(--ok);font-weight:600}.bad{color:var(--err);font-weight:600}
label{display:block;font-weight:600;font-size:13px;margin:14px 0 4px}
input,select{width:100%;padding:10px 12px;border:1px solid #C9D4E3;border-radius:8px;font-size:14px;background:#fff}
input:focus,select:focus{outline:2px solid rgba(23,87,158,.35);border-color:var(--blue)}
.row{display:grid;grid-template-columns:1fr 1fr;gap:0 18px}
@media(max-width:600px){.row{grid-template-columns:1fr}}
.btn{display:inline-block;background:var(--blue);color:#fff;border:0;border-radius:9px;padding:12px 26px;font-size:15px;font-weight:600;cursor:pointer;margin-top:22px}
.btn:hover{background:#124a87}
.alert{border-radius:9px;padding:12px 16px;margin:16px 0;font-size:14px}
.alert.err{background:#FDF0EF;border:1px solid #F3C6C2;color:var(--err)}
.alert.ok{background:#EFFBF3;border:1px solid #BFE8CC;color:var(--ok)}
.note{font-size:13px;color:#5B6B82;margin-top:6px}
.success-icon{font-size:44px}
code{background:#EEF3FA;border-radius:5px;padding:2px 6px;font-size:13px}
.del{background:#FFF8E6;border:1px solid #F1DFAE;border-radius:9px;padding:12px 16px;margin-top:18px;font-size:14px}
a{color:var(--blue)}
</style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <div class="head">
      <h1>True Chain Infrastructure Company</h1>
      <p>Website installer</p>
    </div>
    <div class="body">

    <?php if ($installed && !$done): ?>
      <div class="alert ok"><strong>Already installed.</strong> This installer is locked.</div>
      <p>For security, please delete <code>install.php</code> from your hosting account now.</p>
      <p><a href="<?= h($homeUrl) ?>">Go to the website</a> · <a href="<?= h($adminUrl) ?>">Go to the admin panel</a></p>

    <?php elseif ($done): ?>
      <p class="success-icon">✅</p>
      <h2 style="margin-top:0">Installation complete</h2>
      <p>Your corporate website is live and fully seeded with content, and your administrator account is ready.</p>
      <div class="del"><strong>Important:</strong> delete <code>install.php</code> from the server now (File Manager → public_html → install.php → Delete). The installer has locked itself, but removing the file entirely is best practice.</div>
      <p style="margin-top:20px">
        <a class="btn" href="<?= h($adminUrl) ?>">Open the admin panel</a>&nbsp;&nbsp;
        <a href="<?= h($homeUrl) ?>">View the website</a>
      </p>

    <?php else: ?>

      <h2>Step 1 · Server requirements</h2>
      <table class="checks">
        <?php foreach ($checks as $label => [$ok, $hint, $required]): ?>
          <tr>
            <td>
              <?= h($label) ?>
              <?php if (!$ok): ?><div class="note"><strong>How to fix:</strong> <?= h($hint) ?></div><?php endif; ?>
            </td>
            <td style="text-align:right;vertical-align:top" class="<?= $ok ? 'ok' : 'bad' ?>"><?= $ok ? 'Pass' : ($required ? 'Fail' : 'Not available') ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
      <p class="note">On GoDaddy all of these pass by default with PHP 8.1+ selected in cPanel.</p>

      <?php if (!$hasPdoMysql && $hasPdoSqlite): ?>
        <div class="alert err">
          <strong>MySQL is not available on this server.</strong>
          The <code>pdo_mysql</code> extension is switched off for the PHP version your hosting account is using, so the installer cannot reach a MySQL database yet.
          <br><br>
          <strong>Recommended:</strong> in cPanel open <em>Select PHP Version</em> → <em>Extensions</em>, tick <code>pdo_mysql</code> (some GoDaddy plans list it as <code>nd_pdo_mysql</code>), save, then reload this page — the row above will turn green.
          <br><br>
          <strong>Or install now:</strong> leave the database type below set to <em>SQLite</em>. The site runs fully on SQLite; you can move to MySQL later.
        </div>
      <?php elseif (!$hasPdoMysql && !$hasPdoSqlite): ?>
        <div class="alert err">
          <strong>No database driver is available.</strong>
          Neither <code>pdo_mysql</code> nor <code>pdo_sqlite</code> is enabled for your PHP version, so the site cannot be installed yet.
          In cPanel open <em>Select PHP Version</em>, choose PHP 8.1 or newer, then under <em>Extensions</em> tick <code>pdo_mysql</code>, save, and reload this page.
        </div>
      <?php elseif ($blocked): ?>
        <div class="alert err">
          <strong>Some requirements are not met.</strong> Follow the "How to fix" steps above, then reload this page.
        </div>
      <?php endif; ?>

      <?php foreach ($errors as $er): ?>
        <div class="alert err"><?= h($er) ?></div>
      <?php endforeach; ?>

      <form method="post" autocomplete="off">
        <input type="hidden" name="_token" value="<?= h($_SESSION['ins_token']) ?>">

        <h2>Step 2 · Database</h2>
        <p class="note">In GoDaddy cPanel, create a MySQL database and user first (cPanel → MySQL Databases), grant the user <em>all privileges</em> on the database, then enter the details here.</p>
        <?php $selectedDriver = ($_POST['driver'] ?? $defaultDriver) === 'sqlite' ? 'sqlite' : 'mysql'; ?>
        <label>Database type</label>
        <select name="driver" id="driver" onchange="document.getElementById('mysqlFields').style.display=this.value==='mysql'?'':'none'">
          <option value="mysql" <?= $selectedDriver === 'mysql' ? 'selected' : '' ?> <?= $hasPdoMysql ? '' : 'disabled' ?>>
            MySQL (recommended for GoDaddy)<?= $hasPdoMysql ? '' : ' — pdo_mysql not enabled' ?>
          </option>
          <option value="sqlite" <?= $selectedDriver === 'sqlite' ? 'selected' : '' ?> <?= $hasPdoSqlite ? '' : 'disabled' ?>>
            SQLite (no database server required)<?= $hasPdoSqlite ? '' : ' — pdo_sqlite not enabled' ?>
          </option>
        </select>
        <div id="mysqlFields"<?= $selectedDriver === 'mysql' ? '' : ' style="display:none"' ?>>
          <div class="row">
            <div><label>Database host</label><input name="db_host" value="<?= h($_POST['db_host'] ?? 'localhost') ?>"></div>
            <div><label>Port</label><input name="db_port" value="<?= h($_POST['db_port'] ?? '3306') ?>"></div>
          </div>
          <label>Database name</label><input name="db_name" value="<?= h($_POST['db_name'] ?? '') ?>" placeholder="e.g. tcic_site">
          <div class="row">
            <div><label>Database user</label><input name="db_user" value="<?= h($_POST['db_user'] ?? '') ?>"></div>
            <div><label>Database password</label><input name="db_pass" type="password" value=""></div>
          </div>
        </div>
        <label>Table prefix</label><input name="db_prefix" value="<?= h($_POST['db_prefix'] ?? 'tcic_') ?>">
        <p class="note">Leave the default unless you share this database with another application.</p>

        <h2>Step 3 · Administrator account</h2>
        <label>Your name</label><input name="admin_name" value="<?= h($_POST['admin_name'] ?? '') ?>" placeholder="e.g. Osamede Evbakhavbokun">
        <label>Email address (used to sign in)</label><input name="admin_email" type="email" value="<?= h($_POST['admin_email'] ?? '') ?>" placeholder="you@company.com">
        <div class="row">
          <div><label>Password (min 10 characters)</label><input name="admin_pass" type="password"></div>
          <div><label>Confirm password</label><input name="admin_pass2" type="password"></div>
        </div>

        <button class="btn" type="submit" <?= $checksPass ? '' : 'disabled title="Fix the failed requirements above first"' ?>>Install website</button>
        <?php if (!$checksPass): ?>
          <p class="note">This button stays disabled until the requirements above are met. Fix them in cPanel, then reload this page.</p>
        <?php endif; ?>
      </form>
    <?php endif; ?>

    </div>
  </div>
  <p style="text-align:center;color:#7D8CA3;font-size:13px;margin-top:18px">True Chain Infrastructure Company · Installer v1.0</p>
</div>
</body>
</html>

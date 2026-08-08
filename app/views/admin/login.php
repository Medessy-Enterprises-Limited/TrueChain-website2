<?php /** @var string $error */ ?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Sign in · <?= e(setting('site_short', 'True Chain')) ?> Admin</title>
<link rel="icon" type="image/png" href="<?= e(upload_url(setting('favicon', 'assets/img/favicon.png'))) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Sora:wght@600;700&display=swap" rel="stylesheet">
<style>
:root{--blue:#17579E;--navy:#0B1B33;--line:#E4EAF2;--err:#B42318}
*{box-sizing:border-box}
body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;
  font:15px/1.6 "Inter",system-ui,sans-serif;color:#182A45;
  background:radial-gradient(900px 500px at 80% -10%,rgba(46,124,214,.35),transparent),linear-gradient(135deg,#0B1B33,#10305B)}
.card{width:min(420px,calc(100% - 40px));background:#fff;border-radius:18px;padding:42px 40px;box-shadow:0 30px 80px rgba(4,12,26,.5)}
.logo{display:block;margin:0 auto 10px;height:54px;width:auto}
h1{font-family:"Sora",sans-serif;font-size:19px;text-align:center;margin:0 0 4px;color:#0B1B33}
.sub{text-align:center;color:#64748B;font-size:13.5px;margin:0 0 28px}
label{display:block;font-weight:600;font-size:13.5px;margin:16px 0 6px;font-family:"Sora",sans-serif}
input{width:100%;padding:13px 15px;border:1.5px solid #D5DEEA;border-radius:10px;font:inherit;font-size:15px}
input:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 4px rgba(23,87,158,.12)}
button{width:100%;margin-top:24px;padding:14px;border:0;border-radius:11px;background:var(--blue);color:#fff;
  font-family:"Sora",sans-serif;font-weight:600;font-size:15.5px;cursor:pointer;transition:background .2s}
button:hover{background:#114478}
.err{background:#FDF0EF;border:1px solid #F3C6C2;color:var(--err);border-radius:10px;padding:11px 14px;font-size:14px;margin-bottom:6px}
.back{display:block;text-align:center;margin-top:22px;font-size:13.5px;color:#64748B;text-decoration:none}
.back:hover{color:var(--blue)}
</style>
</head>
<body>
  <form class="card" method="post" action="<?= e(url('admin?r=login')) ?>">
    <img class="logo" src="<?= e(upload_url(setting('logo', 'assets/img/logo.png'))) ?>" alt="<?= e(setting('site_name')) ?>">
    <h1>Administration</h1>
    <p class="sub">Sign in to manage your website</p>
    <?php if (!empty($error)): ?><div class="err"><?= e($error) ?></div><?php endif; ?>
    <?= csrf_field() ?>
    <label for="email">Email address</label>
    <input id="email" name="email" type="email" required autofocus autocomplete="username">
    <label for="password">Password</label>
    <input id="password" name="password" type="password" required autocomplete="current-password">
    <button type="submit">Sign in</button>
    <a class="back" href="<?= e(url('')) ?>">← Back to the website</a>
  </form>
</body>
</html>

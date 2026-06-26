<?php
declare(strict_types=1);

use App\Html;

/**
 * @var string      $title
 * @var string      $orgName
 * @var string      $content   pre-rendered, already-escaped HTML
 * @var string      $nonce
 * @var string|null $csrfToken
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php if ($csrfToken !== null): ?>
<meta name="csrf-token" content="<?= Html::e($csrfToken) ?>">
<?php endif; ?>
<title><?= Html::e($title) ?></title>
<style nonce="<?= Html::e($nonce) ?>">
:root{--fg:#222;--fg-light:#666;--err:#d32f2f;--success:#388e3c;--accent:#2196f3;--line:#ddd;--line-focus:#bbb;--bg:#fafafa;--card:#fff;--radius:4px;--shadow:0 2px 4px rgba(0,0,0,.08)}
*{box-sizing:border-box}
html{scroll-behavior:smooth}
body{margin:0;padding:0;font:16px/1.6 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;color:var(--fg);background:var(--bg)}
main{display:flex;flex-direction:column;min-height:100vh;justify-content:center}
.container{max-width:500px;margin:0 auto;padding:2rem 1.5rem;padding-bottom:calc(2.5rem + env(safe-area-inset-bottom));width:100%;background:var(--card);box-shadow:var(--shadow)}
h1{font-size:1.75rem;line-height:1.2;margin:0 0 .5rem;color:var(--fg);font-weight:600}
h2{font-size:1.1rem;margin:1.5rem 0 1rem;color:var(--fg-light);font-weight:600}
form{display:grid;gap:1.25rem}
label{display:block;margin-bottom:.4rem;font-weight:500;color:var(--fg);font-size:.95rem}
input[type=text],input[type=email],input[type=date],input[type=password]{width:100%;padding:.75rem;font:inherit;color:var(--fg);background:#fff;border:1px solid var(--line);border-radius:var(--radius);transition:border-color .2s,box-shadow .2s;outline:none}
input[type=text]:focus,input[type=email]:focus,input[type=date]:focus,input[type=password]:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(33,150,243,.1)}
input::placeholder{color:#999}
.check{display:flex;gap:.75rem;align-items:flex-start;margin-top:.5rem;padding:.5rem;background:#f5f5f5;border-radius:var(--radius)}
.check input[type=checkbox]{margin-top:.35rem;width:18px;height:18px;cursor:pointer;accent-color:var(--accent)}
.check label{margin:0;cursor:pointer;font-weight:400}
button{position:relative;width:100%;margin-top:1rem;padding:.9rem;font:inherit;font-weight:600;font-size:1rem;color:#fff;background:var(--accent);border:none;border-radius:var(--radius);cursor:pointer;transition:background .2s,box-shadow .2s;box-shadow:0 2px 4px rgba(33,150,243,.25)}
button:hover:not(:disabled){background:#1976d2;box-shadow:0 4px 8px rgba(33,150,243,.35)}
button:active:not(:disabled){transform:translateY(1px);box-shadow:0 1px 2px rgba(33,150,243,.15)}
button:disabled{cursor:not-allowed}
button.is-loading{color:transparent}
.btn-spinner{display:none}
button.is-loading .btn-spinner{display:block;position:absolute;top:50%;left:50%;width:22px;height:22px;margin:-11px 0 0 -11px;border:3px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .8s linear infinite}
.msg{margin-top:1.5rem;padding:1rem;border-radius:var(--radius);font-size:.95rem}
form .msg{margin-top:0}
.msg.error{background:#ffebee;color:#c62828;border-left:4px solid var(--err)}
.msg.success{background:#e8f5e9;color:#2e7d32;border-left:4px solid var(--success)}
.spinner{width:32px;height:32px;border:3px solid rgba(33,150,243,.2);border-top-color:var(--accent);border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.qr{max-width:320px;margin:.5rem auto 0;padding:1.5rem;background:#fff;border:1px solid var(--line);border-radius:var(--radius);text-align:center}
.qr svg{width:100%;height:auto;display:block;image-rendering:pixelated;image-rendering:crisp-edges}
.qr h2{margin-top:0}
@media (max-width:640px){
  .container{padding:1.5rem 1rem}
  h1{font-size:1.5rem}
  .msg{padding:.75rem;font-size:.9rem}
}
</style>
</head>
<body>
<main class="container">
<h1><?= Html::e($orgName) ?> Photo Release Form</h1>
<?= $content ?>
</main>
</body>
</html>

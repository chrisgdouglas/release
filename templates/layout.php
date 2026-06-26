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
:root{--fg:#1a1a1a;--err:#c0392b;--accent:#2c7;--line:#ccc}
*{box-sizing:border-box}
body{margin:0;font:16px/1.5 system-ui,sans-serif;color:var(--fg);background:#fafafa}
.container{max-width:32rem;margin:0 auto;padding:1.25rem}
h1{font-size:1.4rem;margin:.5rem 0 1rem}
h2{font-size:1.1rem;font-weight:600}
label{display:block;margin:.75rem 0 .25rem;font-weight:600}
input[type=text],input[type=email],input[type=date]{width:100%;padding:.6rem;font-size:1rem;border:1px solid var(--line);border-radius:6px}
.check{font-weight:400;display:flex;gap:.5rem;align-items:flex-start;margin-top:1rem}
.check input{margin-top:.3rem}
button{width:100%;margin-top:1.25rem;padding:.8rem;font-size:1rem;font-weight:600;color:#fff;background:var(--accent);border:0;border-radius:6px;cursor:pointer}
button:active{opacity:.85}
.msg{margin-top:1rem}
.error{color:var(--err)}
.spinner{width:36px;height:36px;border:4px solid rgba(0,0,0,.1);border-top-color:var(--accent);border-radius:50%;animation:spin 1s linear infinite;margin:.5rem 0}
@keyframes spin{to{transform:rotate(360deg)}}
.qr{max-width:300px;margin-top:1rem}
.qr svg{width:100%;height:auto}
</style>
</head>
<body>
<main class="container">
<h1><?= Html::e($orgName) ?> Photo Release Form</h1>
<?= $content ?>
</main>
</body>
</html>

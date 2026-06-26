<?php
declare(strict_types=1);

use App\Html;

/**
 * @var string $username
 * @var string $svg  trusted QR SVG markup from the QR library
 */
?>
<h2><?= Html::e($username) ?></h2>
<div class="qr"><?= $svg ?></div>

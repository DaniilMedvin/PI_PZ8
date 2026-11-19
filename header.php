<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f2f2f7">
    <title><?= e($title) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="scene" aria-hidden="true"></div>
<a class="skip" href="#content">До змісту</a>
<?php if (!empty($back)): ?>
    <header class="bar">
        <a class="back" href="<?= e($back[0]) ?>">
            <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
            <?= e($back[1]) ?>
        </a>
    </header>
<?php endif; ?>
<main id="content">
    <h1><?= e($title) ?></h1>
    <?php if ($flash): ?>
        <div class="banner <?= e($flash[0]) ?>" role="status"><?= e($flash[1]) ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="banner error" role="alert">
            <?php foreach ($errors as $error): ?>
                <p><?= e($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

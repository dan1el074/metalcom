<?php $flashes = pull_flash(); ?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= e($title) ?></title>
  <link rel="icon" href="<?= asset('assets/logo-metalcom.png') ?>">
  <script src="<?= asset('js/theme-init.js?v=20261014-1') ?>"></script>
  <link rel="stylesheet" href="<?= asset('css/styles.css?v=20261106-1') ?>">
  <link rel="stylesheet" href="<?= asset('css/app.css?v=20261105-14') ?>">
  <script defer src="<?= asset('vendor/lucide.min.js?v=1.57.0') ?>"></script>
  <script defer src="<?= asset('js/app.js?v=20261105-10') ?>"></script>
</head>
<body class="admin-login-page">
  <?php require ROOT_PATH . '/app/Views/partials/notifications.php'; ?>
  <?= $content ?>
</body>
</html>


<?php $flashes = pull_flash(); ?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= e($title) ?></title>
  <link rel="icon" href="<?= asset('assets/logo-metalcom.png') ?>">
  <script src="<?= asset('js/theme-init.js?v=20261014-1') ?>"></script>
  <link rel="stylesheet" href="<?= asset('css/styles.css?v=20261110-2') ?>">
  <link rel="stylesheet" href="<?= asset('css/app.css?v=20261110-3') ?>">
  <script defer src="<?= asset('vendor/lucide.min.js?v=1.57.0') ?>"></script>
  <script defer src="<?= asset('js/app.js?v=20261110-2') ?>"></script>
</head>
<body class="dashboard-page">
  <div class="dashboard-shell">
    <aside class="dashboard-sidebar" data-dashboard-sidebar>
      <a class="dashboard-logo" href="<?= url('/') ?>"><img src="<?= asset('assets/logo-metalcom.png') ?>" alt="Metalcom"></a>
      <nav>
        <a href="#visao-geral">Visão geral</a>
        <a href="#produtos-admin">Produtos</a>
        <a href="#categorias-admin">Categorias</a>
        <a href="#configuracoes-home">Página inicial</a>
        <a href="#login-admin">Tela de login</a>
        <a href="#contato-admin">Contato</a>
        <a href="#mensagens-admin">Mensagens <span><?= e((string) ($stats['unread'] ?? 0)) ?></span></a>
        <?php if (($user['role'] ?? '') === 'admin'): ?><a href="#usuarios-admin">Usuários</a><?php endif; ?>
      </nav>
      <div class="dashboard-user">
        <div class="avatar"><?= e(strtoupper(substr($user['name'] ?? 'MC', 0, 2))) ?></div>
        <div><strong><?= e($user['name'] ?? '') ?></strong><span><?= e($user['email'] ?? '') ?></span></div>
        <form action="<?= url('/logout') ?>" method="post"><?= csrf_field() ?><button class="link-button" type="submit">Sair</button></form>
      </div>
    </aside>
    <main class="dashboard-main">
      <header class="dashboard-topbar"><button class="dashboard-menu-toggle" type="button" data-dashboard-menu>Menu</button><div><span>Painel administrativo</span><strong>Metalcom</strong></div><a class="button button-outline-small" href="<?= url('/') ?>" target="_blank">Visualizar site</a></header>
      <?php require ROOT_PATH . '/app/Views/partials/notifications.php'; ?>
      <?= $content ?>
    </main>
  </div>
</body>
</html>

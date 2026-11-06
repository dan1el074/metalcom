<?php
use App\Models\Content;
$layoutSettings = $settings ?? Content::settings();
$layoutCategories = $categories ?? Content::categories();
$phone = $layoutSettings['contact_phone'] ?? '(54) 9 9163-4643';
$whatsapp = preg_replace('/\D+/', '', $layoutSettings['contact_whatsapp'] ?? '5554991634643');
$email = $layoutSettings['contact_email'] ?? 'contato@metalcom.com.br';
$address = $layoutSettings['contact_address'] ?? 'Rua Floresta, 139, Barão de Cotegipe, RS, 99740-000';
$flashes = pull_flash();
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Metalcom Marmentini: reboques, estruturas metálicas, fogões, fornos, lareiras e soluções sob medida.">
  <title><?= e($title ?? 'Metalcom') ?></title>
  <link rel="icon" href="<?= asset('assets/logo-metalcom.png') ?>">
  <script src="<?= asset('js/theme-init.js?v=20261014-1') ?>"></script>
  <link rel="stylesheet" href="<?= asset('css/styles.css?v=20261106-1') ?>">
  <link rel="stylesheet" href="<?= asset('css/app.css?v=20261105-16') ?>">
  <script defer src="<?= asset('vendor/lucide.min.js?v=1.57.0') ?>"></script>
  <script defer src="<?= asset('js/app.js?v=20261105-10') ?>"></script>
</head>
<body class="<?= e($bodyClass ?? '') ?>">
  <a class="skip-link" href="#conteudo">Ir para o conteúdo</a>
  <div class="site-topbar" data-topbar><div class="container"><span><i></i> Metalúrgica Metalcom Ltda · Fabricação própria · Produto 100% nacional</span><span>Orçamentos: <strong><?= e($phone) ?></strong></span></div></div>
  <header class="site-header home-header <?= ($bodyClass ?? '') === 'home-page' ? '' : 'site-header-solid' ?>" data-header>
    <div class="container header-inner">
      <a class="brand" href="<?= url('/') ?>" aria-label="Metalcom - Página inicial"><img src="<?= asset('assets/logo-metalcom.png') ?>" alt="Metalcom Marmentini"></a>
      <nav class="desktop-nav" aria-label="Navegação principal">
        <a href="<?= url('/#conteudo') ?>">Início</a><a href="<?= url('/#sobre') ?>">Sobre nós</a>
        <div class="nav-dropdown"><a class="nav-dropdown-toggle" href="<?= url('/produtos') ?>">Produtos</a><div class="nav-dropdown-menu"><?php foreach ($layoutCategories as $item): ?><a href="<?= url('/produtos?categoria=' . rawurlencode($item['slug'])) ?>"><?= e($item['name']) ?></a><?php endforeach; ?></div></div>
        <a href="<?= url('/#projetos-especiais') ?>">Projetos Especiais</a><a href="<?= url('/#contato') ?>">Contato</a>
        <a class="header-budget" href="https://wa.me/<?= e($whatsapp) ?>?text=<?= rawurlencode('Olá, gostaria de solicitar um orçamento.') ?>" target="_blank" rel="noopener">Solicitar orçamento</a>
      </nav>
      <div class="header-actions"><button class="menu-toggle" type="button" aria-label="Abrir menu" aria-controls="mobile-menu" aria-expanded="false"><span></span><span></span><span></span></button></div>
    </div>
    <div class="mobile-menu" id="mobile-menu" hidden><nav aria-label="Navegação móvel"><a href="<?= url('/') ?>">Início</a><a href="<?= url('/#sobre') ?>">Sobre nós</a><a href="<?= url('/produtos') ?>">Produtos</a><a href="<?= url('/#projetos-especiais') ?>">Projetos especiais</a><a href="<?= url('/#contato') ?>">Contato</a></nav></div>
  </header>
  <?php require ROOT_PATH . '/app/Views/partials/notifications.php'; ?>
  <?= $content ?>
  <footer class="site-footer"><div class="container footer-grid"><div class="footer-brand"><img src="<?= asset('assets/logo-metalcom.png') ?>" alt="Metalcom Marmentini"><p>Qualidade que aquece, confiança que dura. Produto 100% nacional.</p><?php $socialSettings = $layoutSettings; require ROOT_PATH . '/app/Views/partials/social-links.php'; ?></div><div><h2>Produtos</h2><?php foreach ($layoutCategories as $item): ?><a href="<?= url('/produtos?categoria=' . rawurlencode($item['slug'])) ?>"><?= e($item['name']) ?></a><?php endforeach; ?></div><div><h2>Metalcom</h2><a href="<?= url('/#sobre') ?>">Quem somos</a><a href="<?= url('/#projetos-especiais') ?>">Projetos especiais</a><a href="<?= url('/#contato') ?>">Contato</a><a href="<?= url('/login') ?>">Área administrativa</a></div><div><h2>Atendimento</h2><p><?= e($address) ?></p><a href="tel:+<?= e($whatsapp) ?>"><?= e($phone) ?></a><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></div></div><div class="footer-bottom"><div class="container"><span>Metalcom Marmentini. Todos os direitos reservados.</span><span class="footer-credit">Designed by <a href="https://wa.me/5554992539823" target="_blank" rel="noopener">Daniel Vargas</a></span></div></div></footer>
  <a class="whatsapp-float" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener" aria-label="Falar com a Metalcom pelo WhatsApp"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg></a>
</body>
</html>


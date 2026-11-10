<?php $bodyClass = 'internal-page'; ?>
<main id="conteudo" class="product-page">
  <nav class="catalog-breadcrumb"><div class="container"><a href="<?= url('/') ?>">Início</a><span>/</span><a href="<?= url('/produtos') ?>">Produtos</a><span>/</span><span><?= e($product['name']) ?></span></div></nav>
  <section class="product-page-section"><div class="container"><div class="product-detail-grid">
    <div class="product-gallery" data-product-gallery>
      <div class="product-main-image"><img src="<?= asset($images[0]['image_path']) ?>" alt="<?= e($images[0]['alt_text'] ?: $product['name']) ?>" data-gallery-main></div>
      <?php if (count($images) > 1): ?><div class="product-thumbnails" aria-label="Galeria de imagens">
        <?php foreach ($images as $index => $image): ?><button class="product-thumbnail <?= $index === 0 ? 'is-active' : '' ?>" type="button" data-gallery-thumbnail data-src="<?= asset($image['image_path']) ?>" data-alt="<?= e($image['alt_text'] ?: $product['name']) ?>" aria-label="Ver imagem <?= $index + 1 ?> de <?= e($product['name']) ?>"><img src="<?= asset($image['image_path']) ?>" alt="" loading="lazy"></button><?php endforeach; ?>
      </div><?php endif; ?>
    </div>
    <div class="product-detail-copy"><span class="eyebrow"><?= e($product['category_name']) ?></span><h1><?= e($product['name']) ?></h1><p class="product-lead"><?= nl2br(emphasized_text($product['description'])) ?></p><div class="product-highlights"><div><strong>Produto Metalcom</strong><span>Fabricação própria e atendimento especializado.</span></div><?php if ($product['code']): ?><div><strong>Código</strong><span><?= e($product['code']) ?></span></div><?php endif; ?></div><div class="product-cta-box"><p>Consulte valores, prazo e configurações disponíveis.</p><a class="button button-whatsapp" href="https://wa.me/<?= e(preg_replace('/\D+/', '', \App\Models\Content::settings()['contact_whatsapp'] ?? '5554991634643')) ?>?text=<?= rawurlencode('Olá, tenho interesse no produto ' . $product['name']) ?>" target="_blank" rel="noopener">Solicitar pelo WhatsApp</a></div></div>
  </div></div></section>
  <?php if ($related): ?><section class="section related-section"><div class="container"><div class="section-heading"><h2>Outros produtos Metalcom</h2></div><div class="related-grid"><?php foreach ($related as $product): require ROOT_PATH . '/app/Views/partials/product-card.php'; endforeach; ?></div></div></section><?php endif; ?>
</main>

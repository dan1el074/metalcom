<article class="product-card"><a class="product-card-image"
        href="<?= url('/produto?produto=' . rawurlencode($product['slug'])) ?>"><img
            src="<?= asset($product['image_path']) ?>" alt="<?= e($product['name']) ?>" loading="lazy"></a>
    <div class="product-card-body"><span><?= e($product['category_name']) ?></span>
        <h3><?= e($product['name']) ?></h3>
        <p><?= e($product['description']) ?></p><a
            href="<?= url('/produto?produto=' . rawurlencode($product['slug'])) ?>">Conhecer produto</a>
    </div>
</article>
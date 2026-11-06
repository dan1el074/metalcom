<?php $bodyClass = 'home-page'; ?>
<main id="conteudo">
  <section class="home-hero" aria-label="Destaque principal" data-home-hero>
    <div class="home-hero-slides" aria-hidden="true">
      <div class="home-hero-slide is-active"
        style="background-image:url('<?= asset($settings['hero_image_1'] ?? 'assets/hero-reboque-cavalo.png') ?>')"></div>
      <div class="home-hero-slide"
        style="background-image:url('<?= asset($settings['hero_image_2'] ?? 'uploads/9c29e64ae3fe63a24e34c493891174f5.png') ?>')"></div>
      <div class="home-hero-slide"
        style="background-image:url('<?= asset($settings['hero_image_3'] ?? 'assets/hero-calefator-ambiente-v2.png') ?>')"></div>
    </div>
    <button class="home-hero-arrow home-hero-arrow-prev" type="button" data-hero-prev aria-label="Imagem anterior">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 5-7 7 7 7" /></svg>
    </button>
    <button class="home-hero-arrow home-hero-arrow-next" type="button" data-hero-next aria-label="Próxima imagem">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 5 7 7-7 7" /></svg>
    </button>
    <div class="container home-hero-content">
      <div class="home-hero-copy reveal">
        <h1>Qualidade que <em>aquece</em>, confiança que <em>dura</em>.</h1>
        <p>Soluções em metal com excelência em cada detalhe.</p><a class="button button-primary"
          href="<?= url('/produtos') ?>">Conheça nossos produtos <span aria-hidden="true">→</span></a>
      </div>
      <div class="home-hero-dots" aria-label="Selecionar imagem do destaque"></div>
    </div>
    <div class="hero-watermark" aria-hidden="true">METALCOM</div>
  </section>
  <section class="trust-strip" aria-label="Diferenciais Metalcom">
    <div class="container trust-strip-grid">
      <div><span>Produtos 100%<strong>nacionais</strong></span></div>
      <div><span>Materiais de<strong>alta qualidade</strong></span></div>
      <div><span>Acabamento<strong>premium</strong></span></div>
      <div><span>Projetos<strong>personalizados</strong></span></div>
      <div><span>Atendimento<strong>especializado</strong></span></div>
    </div>
  </section>

  <section class="section home-products" id="produtos">
    <div class="container home-split-layout">
      <div class="home-section-intro reveal"><span
          class="eyebrow"><?= e($settings['products_eyebrow'] ?? 'Nossos produtos') ?></span>
        <h2><?= e($settings['products_title'] ?? 'Soluções robustas para quem exige mais.') ?></h2>
        <p><?= e($settings['products_description'] ?? '') ?></p>
      </div>
      <div class="carousel-shell home-category-carousel reveal" data-product-carousel><button
          class="carousel-arrow carousel-arrow-left" type="button" aria-label="Voltar" data-carousel-prev>‹</button>
        <div class="category-track" data-carousel-track><?php foreach ($categories as $category): ?>
            <article class="category-card"><a
                href="<?= url('/produtos?categoria=' . rawurlencode($category['slug'])) ?>"><img
                  src="<?= asset($category['image_path']) ?>" alt="<?= e($category['name']) ?>">
                <div>
                  <h3><?= e($category['name']) ?></h3>
                  <p><?= e($category['description']) ?></p><span>Ver mais →</span>
                </div>
              </a></article><?php endforeach; ?>
        </div><button class="carousel-arrow carousel-arrow-right" type="button" aria-label="Avançar"
          data-carousel-next>›</button>
      </div>
    </div>
  </section>

  <section class="home-about" id="sobre">
    <div class="container home-about-panel reveal">
      <div class="home-about-image"><img src="<?= asset($settings['about_image'] ?? 'assets/soldador-metalcom-sem-filtro.png') ?>"
          alt="Metalcom"></div>
      <div class="home-about-copy"><span
          class="eyebrow"><?= e($settings['about_eyebrow'] ?? 'Sobre a Metalcom') ?></span>
        <h2><?= e($settings['about_title'] ?? '') ?></h2>
        <p><?= nl2br(e($settings['about_description'] ?? '')) ?></p>
      </div>
    </div>
  </section>

  <section class="section home-projects" id="projetos-especiais">
    <div class="container home-split-layout">
      <div class="home-section-intro reveal"><span
          class="eyebrow"><?= e($settings['projects_eyebrow'] ?? 'Projetos especiais') ?></span>
        <h2><?= e($settings['projects_title'] ?? '') ?></h2>
        <p><?= nl2br(e($settings['projects_description'] ?? '')) ?></p><a class="button button-primary"
          href="https://wa.me/<?= e(preg_replace('/\D+/', '', $settings['contact_whatsapp'] ?? '5554991634643')) ?>"
          target="_blank" rel="noopener">Solicitar projeto personalizado →</a>
      </div>
      <div class="project-carousel reveal" data-project-carousel><button class="project-arrow project-arrow-left"
          type="button" aria-label="Anterior" data-project-prev>‹</button>
        <div class="project-track" data-project-track><?php foreach ($projects as $project): ?>
            <figure><img src="<?= asset($project['image_path']) ?>"
                alt="<?= e($project['alt_text'] ?: $project['title']) ?>"></figure><?php endforeach; ?>
        </div><button class="project-arrow project-arrow-right" type="button" aria-label="Próximo"
          data-project-next>›</button>
        <div class="project-dots"></div>
      </div>
    </div>
  </section>

  <section class="section contact-section" id="contato">
    <div class="container contact-grid">
      <div class="contact-copy reveal"><span class="eyebrow">Entre em contato</span>
        <h2>Vamos encontrar a solução certa para seu projeto.</h2>
        <p>Conte o que você precisa e nossa equipe entrará em contato.</p>
        <div class="contact-details"><a
            href="tel:+<?= e(preg_replace('/\D+/', '', $settings['contact_phone'] ?? '')) ?>"><span>Telefone /
              WhatsApp<strong><?= e($settings['contact_phone'] ?? '') ?></strong></span></a><a
            href="mailto:<?= e($settings['contact_email'] ?? '') ?>"><span>E-mail<strong><?= e($settings['contact_email'] ?? '') ?></strong></span></a>
          <div><span>Localização<strong><?= e($settings['contact_address'] ?? '') ?></strong></span></div>
        </div>
        <?php $socialSettings = $settings; require ROOT_PATH . '/app/Views/partials/social-links.php'; ?>
      </div>
      <form class="contact-form reveal" action="<?= url('/contato') ?>" method="post"><?= csrf_field() ?><label
          class="honeypot" aria-hidden="true">Empresa<input name="company" tabindex="-1" autocomplete="off"></label>
        <div class="form-row"><label>Nome<input type="text" name="name" value="<?= e(old('name')) ?>" maxlength="120"
              required></label><label>Telefone<input type="tel" name="phone" value="<?= e(old('phone')) ?>"
              maxlength="40" required></label></div><label>E-mail<input type="email" name="email"
            value="<?= e(old('email')) ?>" maxlength="190" required></label><label>Assunto<select name="subject">
            <option>Quero conhecer um produto</option>
            <option>Preciso de um projeto sob medida</option>
            <option>Assistência e peças</option>
            <option>Outros assuntos</option>
          </select></label><label>Mensagem<textarea name="message" rows="5"
            maxlength="5000"><?= e(old('message')) ?></textarea></label><button class="button button-primary"
          type="submit"><?= ($settings['contact_action'] ?? 'whatsapp') === 'whatsapp' ? 'Continuar no WhatsApp' : 'Enviar' ?></button>
      </form>
    </div>
  </section>
</main>
<?php unset($_SESSION['old']); ?>

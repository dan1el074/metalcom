<div class="dashboard-content">
  <section id="visao-geral" class="dashboard-heading">
    <div><span class="eyebrow">Visão geral</span>
      <h1>Olá, <?= e(explode(' ', $user['name'])[0]) ?></h1>
      <p>Gerencie o conteúdo e os contatos recebidos pelo site.</p>
    </div>
  </section>
  <section class="stats-cards">
    <article><span>Produtos ativos</span><strong><?= e($stats['products']) ?></strong></article>
    <article><span>Categorias</span><strong><?= e($stats['categories']) ?></strong></article>
    <article><span>Mensagens recebidas</span><strong><?= e($stats['messages']) ?></strong></article>
    <article><span>Não lidas</span><strong><?= e($stats['unread']) ?></strong></article>
  </section>

  <section class="dashboard-panel" id="produtos-admin">
    <div class="panel-heading">
      <div>
        <h2>Produtos</h2>
        <p>Cadastre e edite os itens exibidos no catálogo.</p>
      </div><button class="button button-primary" type="button" data-product-new>Novo produto</button>
    </div>
    <div class="responsive-table">
      <table>
        <thead>
          <tr>
            <th>Produto</th>
            <th>Categoria</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody><?php foreach ($products as $item): ?>
            <tr>
              <td>
                <div class="table-product"><img src="<?= asset($item['image_path']) ?>"
                    alt=""><strong><?= e($item['name']) ?></strong></div>
              </td>
              <td><?= e($item['category_name']) ?></td>
              <td><span
                  class="status <?= $item['active'] ? 'status-active' : '' ?>"><?= $item['active'] ? 'Ativo' : 'Inativo' ?></span>
              </td>
              <td><button class="table-action" type="button"
                  data-product-edit='<?= e(json_encode($item, JSON_UNESCAPED_UNICODE)) ?>'>Editar</button>
                <form class="inline-form" action="<?= url('/admin/products/' . $item['id'] . '/toggle') ?>" method="post">
                  <?= csrf_field() ?><button class="table-action"
                    type="submit"><?= $item['active'] ? 'Ocultar' : 'Exibir' ?></button></form>
              </td>
            </tr><?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <dialog class="admin-modal admin-modal-wide" id="product-admin-modal">
      <form action="<?= url('/admin/products') ?>" method="post" enctype="multipart/form-data"><?= csrf_field() ?><input
          type="hidden" name="id" data-product-id><input type="hidden" name="current_image" data-product-current-image>
        <div class="modal-heading">
          <h2 data-product-modal-title>Produto</h2><button class="modal-close" type="button" data-dialog-close
            aria-label="Fechar" title="Fechar"><svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M6 6l12 12M18 6L6 18" />
            </svg></button>
        </div>
        <div class="modal-grid"><label>Nome<input name="name" data-product-name required></label><label>Endereço
            amigável<input name="slug" data-product-slug></label><label>Categoria<select name="category_id"
              data-product-category required><?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>"><?= e($category['name']) ?></option><?php endforeach; ?>
            </select></label><label>Código<input name="code" data-product-code></label><label
            class="modal-span">Descrição<textarea name="description" rows="4" data-product-description
              required></textarea></label><input type="hidden" name="sort_order" value="0"
              data-product-order><label class="checkbox-label"><input type="checkbox" name="active"
              data-product-active checked> Produto ativo</label>
          <div class="modal-span product-main-image-admin">
            <div><strong>Imagem principal</strong><img data-product-image-preview alt="Imagem principal do produto">
            </div><label>Substituir imagem<input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                data-product-image><small>Deixe vazio para manter a imagem atual.</small></label>
          </div>
          <div class="modal-span product-gallery-admin">
            <div class="product-gallery-heading">
              <div><strong>Galeria do produto</strong><small>Altere a ordem ou marque uma imagem para excluir.</small>
              </div><label class="compact-upload">Adicionar imagens<input type="file" name="gallery_images[]"
                  accept="image/jpeg,image/png,image/webp" multiple></label>
            </div>
            <div class="product-gallery-existing" data-product-gallery-existing></div>
          </div>
        </div>
        <div class="modal-actions"><button class="button button-cancel" type="button" data-product-delete data-delete-base="<?= e(url('/admin/products')) ?>" hidden>Excluir produto</button><button class="button button-cancel" type="button" data-dialog-close>Cancelar</button><button class="button button-primary">Salvar produto</button></div>
      </form>
    </dialog>
    <dialog class="admin-modal" id="product-delete-modal">
      <form method="post">
        <?= csrf_field() ?>
        <div class="modal-heading"><h2>Excluir produto</h2><button class="modal-close" type="button" data-dialog-close aria-label="Fechar" title="Fechar">×</button></div>
        <p>Deseja excluir o produto <strong data-product-delete-label></strong> e sua galeria? Esta ação não pode ser desfeita.</p>
        <div class="modal-actions"><button class="button button-secondary" type="button" data-dialog-close>Cancelar</button><button class="button button-cancel" type="submit">Excluir produto</button></div>
      </form>
    </dialog>
  </section>

  <section class="dashboard-panel" id="categorias-admin">
    <div class="panel-heading">
      <div><h2>Categorias</h2><p>Crie e organize os nomes das categorias dos produtos.</p></div>
      <button class="button button-primary" type="button" data-category-new>Nova categoria</button>
    </div>
    <div class="responsive-table">
      <table>
        <thead><tr><th>Nome</th><th>Produtos vinculados</th><th>Ações</th></tr></thead>
        <tbody>
          <?php foreach ($categories as $category): ?>
            <tr>
              <td><strong><?= e($category['name']) ?></strong></td>
              <td><?= e((string) ($category['product_count'] ?? 0)) ?></td>
              <td><div class="category-name-actions">
                <button class="table-action" type="button" data-category-edit='<?= e(json_encode(['id' => $category['id'], 'name' => $category['name']], JSON_UNESCAPED_UNICODE)) ?>'>Editar</button>
                <button class="table-action category-delete-action" type="button"
                  data-category-delete-url="<?= e(url('/admin/categories/' . $category['id'] . '/delete')) ?>"
                  data-category-delete-name="<?= e($category['name']) ?>"
                  <?= (int) ($category['product_count'] ?? 0) > 0 ? 'disabled title="Esta categoria possui produtos vinculados"' : '' ?>>Excluir</button>
              </div></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$categories): ?><tr><td colspan="3">Nenhuma categoria cadastrada.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
    <p class="category-admin-help">Categorias com produtos vinculados não podem ser excluídas.</p>
    <dialog class="admin-modal" id="category-admin-modal">
      <form action="<?= url('/admin/categories') ?>" method="post">
        <?= csrf_field() ?><input type="hidden" name="id" data-category-id>
        <div class="modal-heading"><h2 data-category-modal-title>Nova categoria</h2><button class="modal-close" type="button" data-dialog-close aria-label="Fechar" title="Fechar">×</button></div>
        <div class="modal-grid"><label class="modal-span">Nome da categoria<input name="name" maxlength="100" data-category-name required></label></div>
        <div class="modal-actions"><button class="button button-cancel" type="button" data-dialog-close>Cancelar</button><button class="button button-primary" type="submit">Salvar</button></div>
      </form>
    </dialog>
    <dialog class="admin-modal" id="category-delete-modal">
      <form method="post">
        <?= csrf_field() ?>
        <div class="modal-heading"><h2>Excluir categoria</h2><button class="modal-close" type="button" data-dialog-close aria-label="Fechar" title="Fechar">×</button></div>
        <p>Deseja excluir a categoria <strong data-category-delete-label></strong>? Essa ação não pode ser desfeita.</p>
        <div class="modal-actions"><button class="button button-light" type="button" data-dialog-close>Cancelar</button><button class="button button-cancel" type="submit">Excluir</button></div>
      </form>
    </dialog>
  </section>

  <section class="dashboard-panel" id="configuracoes-home">
    <div class="panel-heading">
      <div>
        <h2>Configurações da página inicial</h2>
        <p>Edite os textos, imagens e a ordem do conteúdo exibido na home.</p>
      </div>
    </div>
    <div class="settings-form">
    <form class="settings-form" action="<?= url('/admin/home') ?>" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="home_section" value="hero">
      <fieldset class="settings-group">
        <legend>Imagens do hero</legend>
        <div class="repeatable-heading">
          <p>As imagens são exibidas nesta sequência no destaque principal da página inicial.</p>
        </div>
        <div class="hero-admin-grid">
          <?php for ($heroIndex = 1; $heroIndex <= 3; $heroIndex++): ?>  <?php $heroFallbacks = [1 => 'assets/hero-reboque-cavalo.png', 2 => 'uploads/9c29e64ae3fe63a24e34c493891174f5.png', 3 => 'assets/hero-calefator-ambiente-v2.png'];
                     $heroPath = $settings['hero_image_' . $heroIndex] ?? $heroFallbacks[$heroIndex]; ?><label
              class="stacked-image-field hero-image-field">Imagem <?= $heroIndex ?><input type="file"
                name="hero_image_<?= $heroIndex ?>" accept="image/jpeg,image/png,image/webp"><img class="admin-preview"
                src="<?= asset($heroPath) ?>" alt="Imagem <?= $heroIndex ?> atual do hero"></label><input type="hidden"
              name="current_hero_image_<?= $heroIndex ?>" value="<?= e($heroPath) ?>"><?php endfor; ?></div>
        <div class="settings-actions"><button class="button button-primary" type="submit">Salvar</button></div>
      </fieldset>
    </form>
    <form class="settings-form" action="<?= url('/admin/home') ?>" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="home_section" value="products">
      <fieldset class="settings-group">
        <legend>Nossos produtos</legend>
        <div class="settings-grid"><label>Chamada<input name="products_eyebrow"
              value="<?= e($settings['products_eyebrow'] ?? '') ?>"></label><label class="field-wide">Título<input
              name="products_title" value="<?= e($settings['products_title'] ?? '') ?>" required></label><label
            class="field-wide">Texto<textarea name="products_description"
              rows="3"><?= e($settings['products_description'] ?? '') ?></textarea></label></div>
        <div class="repeatable-heading">
          <p>Use o número de ordem para reposicionar as categorias. Números menores aparecem primeiro.</p><button
            class="button button-light" type="button" data-add-category>Adicionar categoria</button>
        </div>
        <div class="admin-edit-grid category-admin-cards" data-category-items>
          <?php foreach ($categories as $category): ?>
            <article data-repeatable-item><input type="hidden" name="categories[<?= $category['id'] ?>][current_image]"
                value="<?= e($category['image_path']) ?>"><input type="hidden"
                name="categories[<?= $category['id'] ?>][delete]" value="" data-delete-field><button
                class="repeatable-remove" type="button" data-remove-existing aria-label="Remover categoria"
                title="Remover categoria"><svg viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5" />
                </svg></button><label>Categoria<select name="categories[<?= $category['id'] ?>][name]"
                  required><?php foreach ($categories as $categoryOption): ?>
                    <option value="<?= e($categoryOption['name']) ?>" <?= (int) $categoryOption['id'] === (int) $category['id'] ? 'selected' : '' ?>><?= e($categoryOption['name']) ?></option><?php endforeach; ?>
                </select></label><label>Descrição<textarea name="categories[<?= $category['id'] ?>][description]"
                  rows="3"><?= e($category['description']) ?></textarea></label>
              <div class="category-card-fields"><label>Ordem<input type="number" min="0"
                    name="categories[<?= $category['id'] ?>][sort_order]"
                    value="<?= e($category['sort_order']) ?>"></label><label class="checkbox-label"><input type="checkbox"
                    name="categories[<?= $category['id'] ?>][active]" <?= $category['active'] ? 'checked' : '' ?>>
                  Exibir</label></div><label class="category-image-field stacked-image-field">Nova imagem<input
                  type="file" name="category_image_<?= $category['id'] ?>" accept="image/jpeg,image/png,image/webp"><img
                  class="admin-thumb" src="<?= asset($category['image_path']) ?>"
                  alt="Imagem atual de <?= e($category['name']) ?>"></label>
            </article><?php endforeach; ?>
        </div>
        <div class="settings-actions"><button class="button button-primary" type="submit">Salvar</button></div>
      </fieldset>
    </form>

    <form class="settings-form" action="<?= url('/admin/home') ?>" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="home_section" value="about">
      <fieldset class="settings-group">
        <legend>Sobre a Metalcom</legend>
        <div class="settings-grid"><label>Chamada<input name="about_eyebrow"
              value="<?= e($settings['about_eyebrow'] ?? '') ?>"></label><label class="field-wide">Título<input
              name="about_title" value="<?= e($settings['about_title'] ?? '') ?>" required></label><label
            class="field-wide">Descrição<textarea name="about_description"
              rows="5"><?= e($settings['about_description'] ?? '') ?></textarea></label><label
            class="field-wide about-image-field">Trocar imagem<input type="file" name="about_image"
              accept="image/jpeg,image/png,image/webp"><img class="admin-preview"
              src="<?= asset($settings['about_image'] ?? 'assets/soldador-metalcom-sem-filtro.png') ?>"
              alt="Imagem atual da seção Sobre a Metalcom"></label><input type="hidden" name="current_about_image"
            value="<?= e($settings['about_image'] ?? 'assets/soldador-metalcom-sem-filtro.png') ?>"></div>
        <div class="settings-actions"><button class="button button-primary" type="submit">Salvar</button></div>
      </fieldset>
    </form>

    <form class="settings-form" action="<?= url('/admin/home') ?>" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="home_section" value="projects">
      <fieldset class="settings-group">
        <legend>Projetos especiais</legend>
        <div class="settings-grid"><label>Chamada<input name="projects_eyebrow"
              value="<?= e($settings['projects_eyebrow'] ?? '') ?>"></label><label class="field-wide">Título<input
              name="projects_title" value="<?= e($settings['projects_title'] ?? '') ?>"></label><label
            class="field-wide">Texto<textarea name="projects_description"
              rows="4"><?= e($settings['projects_description'] ?? '') ?></textarea></label></div>
        <div class="repeatable-heading">
          <p>Adicione, remova e ordene as imagens exibidas no carrossel.</p><button class="button button-light"
            type="button" data-add-project>Adicionar projeto</button>
        </div>
        <div class="admin-edit-grid" data-project-items><?php foreach ($projects as $project): ?>
            <article data-repeatable-item><input type="hidden" name="projects[<?= $project['id'] ?>][current_image]"
                value="<?= e($project['image_path']) ?>"><input type="hidden"
                name="projects[<?= $project['id'] ?>][delete]" value="" data-delete-field><button
                class="repeatable-remove" type="button" data-remove-existing aria-label="Remover projeto"
                title="Remover projeto"><svg viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5" />
                </svg></button><label>Título<input name="projects[<?= $project['id'] ?>][title]"
                  value="<?= e($project['title']) ?>"></label><label>Texto alternativo<input
                  name="projects[<?= $project['id'] ?>][alt_text]"
                  value="<?= e($project['alt_text']) ?>"></label><label>Ordem<input type="number" min="0"
                  name="projects[<?= $project['id'] ?>][sort_order]"
                  value="<?= e($project['sort_order']) ?>"></label><label class="stacked-image-field">Nova imagem<input
                  type="file" name="project_image_<?= $project['id'] ?>" accept="image/jpeg,image/png,image/webp"><img
                  class="admin-thumb" src="<?= asset($project['image_path']) ?>"
                  alt="Imagem atual de <?= e($project['title']) ?>"></label><label class="checkbox-label"><input
                  type="checkbox" name="projects[<?= $project['id'] ?>][active]" <?= $project['active'] ? 'checked' : '' ?>> Exibir</label></article><?php endforeach; ?>
        </div>
        <div class="settings-actions"><button class="button button-primary" type="submit">Salvar</button></div>
      </fieldset>
    </form>
    </div>
  </section>

  <section class="dashboard-panel" id="login-admin">
    <div class="panel-heading">
      <div>
        <h2>Tela de login</h2>
        <p>Escolha a imagem exibida no painel lateral da área de acesso.</p>
      </div>
    </div>
    <form class="settings-form" action="<?= url('/admin/login-appearance') ?>" method="post"
      enctype="multipart/form-data"><?= csrf_field() ?>
      <div class="settings-grid"><label class="field-wide stacked-image-field login-image-field">Trocar imagem<input
            type="file" name="login_background_image" accept="image/jpeg,image/png,image/webp"><small>Formatos JPG, PNG
            ou WebP, com até 5 MB.</small><img class="admin-preview login-image-preview"
            src="<?= asset($settings['login_background_image'] ?? 'assets/soldador-metalcom-sem-filtro.png') ?>"
            alt="Imagem atual da tela de login"></label><input type="hidden" name="current_login_background_image"
          value="<?= e($settings['login_background_image'] ?? 'assets/soldador-metalcom-sem-filtro.png') ?>"></div>
      <div class="settings-actions"><button class="button button-primary" type="submit">Salvar imagem do login</button>
      </div>
    </form>
  </section>

  <section class="dashboard-panel" id="contato-admin">
    <div class="panel-heading">
      <div>
        <h2>Informações de contato</h2>
        <p>Dados públicos e comportamento do formulário.</p>
      </div>
    </div>
    <form class="settings-form" action="<?= url('/admin/contact') ?>" method="post"><?= csrf_field() ?>
      <div class="settings-grid"><label>Telefone<input name="contact_phone"
            value="<?= e($settings['contact_phone'] ?? '') ?>" required></label><label>WhatsApp com DDI<input
            name="contact_whatsapp" value="<?= e($settings['contact_whatsapp'] ?? '') ?>" required></label><label>E-mail
          destinatário<input type="email" name="contact_email" value="<?= e($settings['contact_email'] ?? '') ?>"
            required></label><label class="field-wide">Endereço<input name="contact_address"
            value="<?= e($settings['contact_address'] ?? '') ?>"></label><label>Instagram<input type="url"
            name="contact_instagram"
            value="<?= e($settings['contact_instagram'] ?? '') ?>"></label><label>Facebook<input type="url"
            name="contact_facebook" value="<?= e($settings['contact_facebook'] ?? '') ?>"></label><label>Após enviar o
          formulário<select name="contact_action">
            <option value="email" <?= ($settings['contact_action'] ?? '') === 'email' ? 'selected' : '' ?>>Enviar e-mail à
              Metalcom</option>
            <option value="whatsapp" <?= ($settings['contact_action'] ?? 'whatsapp') === 'whatsapp' ? 'selected' : '' ?>>
              Redirecionar ao WhatsApp</option>
          </select></label><label class="checkbox-label"><input type="checkbox" name="customer_auto_reply"
            <?= ($settings['customer_auto_reply'] ?? '0') === '1' ? 'checked' : '' ?>> Enviar confirmação automática ao
          cliente</label><label class="field-wide">Texto da confirmação<textarea name="customer_reply_text"
            rows="4"><?= e($settings['customer_reply_text'] ?? '') ?></textarea></label></div>
      <div class="settings-actions"><button class="button button-primary" type="submit">Salvar contato</button></div>
    </form>
  </section>

  <section class="dashboard-panel" id="mensagens-admin">
    <div class="panel-heading">
      <div>
        <h2>Respostas do formulário</h2>
        <p>Histórico armazenado para análise e insights futuros.</p>
      </div>
    </div>
    <div class="responsive-table">
      <table>
        <thead>
          <tr>
            <th>Contato</th>
            <th>Assunto</th>
            <th>Mensagem</th>
            <th>Destino</th>
            <th>Recebida em</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody><?php foreach ($messages as $message): ?>
            <tr>
              <td><strong><?= e($message['name']) ?></strong><br><a
                  href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a><br><?= e($message['phone']) ?>
              </td>
              <td><?= e($message['subject']) ?></td>
              <td><?= e($message['message']) ?></td>
              <td><?= $message['delivery_method'] === 'email' ? 'E-mail' : 'WhatsApp' ?></td>
              <td><?= e(date('d/m/Y H:i', strtotime($message['created_at']))) ?></td>
              <td><?php if ($message['read_at']): ?><span class="status status-active">Lida</span><?php else: ?>
                  <form action="<?= url('/admin/messages/' . $message['id'] . '/read') ?>" method="post">
                    <?= csrf_field() ?><button class="table-action">Marcar como lida</button></form><?php endif; ?>
              </td>
            </tr><?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <?php if ($user['role'] === 'admin'): ?>
    <section class="dashboard-panel" id="usuarios-admin">
      <div class="panel-heading">
        <div>
          <h2>Gestão de usuários</h2>
          <p>Crie acessos e controle quem pode entrar no painel.</p>
        </div><button class="button button-primary" type="button" data-user-new>Adicionar usuário</button>
      </div>
      <div class="responsive-table">
        <table>
          <thead>
            <tr>
              <th>Usuário</th>
              <th>Perfil</th>
              <th>Último acesso</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody><?php foreach ($users as $account): ?>
              <tr>
                <td><strong><?= e($account['name']) ?></strong><br><?= e($account['email']) ?></td>
                <td><?= $account['role'] === 'admin' ? 'Administrador' : 'Editor' ?></td>
                <td><?= $account['last_login_at'] ? e(date('d/m/Y H:i', strtotime($account['last_login_at']))) : 'Nunca' ?>
                </td>
                <td><span
                    class="status <?= $account['active'] ? 'status-active' : '' ?>"><?= $account['active'] ? 'Ativo' : 'Inativo' ?></span>
                </td>
                <td><button class="table-action" type="button"
                    data-user-edit='<?= e(json_encode($account, JSON_UNESCAPED_UNICODE)) ?>'>Editar</button><?php if ((int) $account['id'] !== (int) $user['id']): ?>
                    <form class="inline-form" action="<?= url('/admin/users/' . $account['id'] . '/toggle') ?>" method="post">
                      <?= csrf_field() ?><button class="table-action"
                        type="submit"><?= $account['active'] ? 'Desativar' : 'Ativar' ?></button></form><?php endif; ?>
                </td>
              </tr><?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <dialog class="admin-modal" id="user-modal">
        <form action="<?= url('/admin/users') ?>" method="post"><?= csrf_field() ?><input type="hidden" name="id"
            data-user-id>
          <div class="modal-heading">
            <h2 data-user-modal-title>Usuário</h2><button class="modal-close" type="button" data-dialog-close
              aria-label="Fechar" title="Fechar"><svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M6 6l12 12M18 6L6 18" />
              </svg></button>
          </div>
          <div class="modal-grid"><label>Nome<input name="name" data-user-name required></label><label>E-mail<input
                type="email" name="email" data-user-email required></label><label>Perfil<select name="role"
                data-user-role>
                <option value="editor">Editor</option>
                <option value="admin">Administrador</option>
              </select></label><label>Senha <span data-user-password-hint>(mínimo de 8 caracteres)</span><input
                type="password" name="password" minlength="8" data-user-password></label><label
              class="checkbox-label modal-span"><input type="checkbox" name="active" data-user-active checked> Usuário
              ativo</label></div>
          <div class="modal-actions"><button class="button button-primary">Salvar usuário</button></div>
        </form>
      </dialog>
    </section><?php endif; ?>
</div>

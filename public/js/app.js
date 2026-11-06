(() => {
  'use strict';
  const renderLucideIcons = (root = document) => {
    if (!window.lucide?.createIcons) return;
    window.lucide.createIcons({ root, attrs: { 'stroke-width': 1.8 } });
  };
  let savedTheme = null;
  try {
    savedTheme = window.localStorage.getItem('metalcom-theme');
  } catch (_) {}
  document.documentElement.dataset.theme = savedTheme === 'dark' ? 'dark' : 'light';

  const themeToggle = document.createElement('button');
  themeToggle.type = 'button';
  themeToggle.className = 'theme-toggle';
  themeToggle.setAttribute('aria-label', 'Alternar entre tema claro e escuro');
  themeToggle.innerHTML = '<i data-lucide="sun" class="theme-sun" aria-hidden="true"></i><i data-lucide="moon" class="theme-moon" aria-hidden="true"></i>';
  const themeMount = document.querySelector('.header-actions') || document.querySelector('.dashboard-topbar');
  if (themeMount) themeMount.prepend(themeToggle);
  else document.body.append(themeToggle);
  const updateThemeToggle = () => {
    const isDark = document.documentElement.dataset.theme === 'dark';
    themeToggle.setAttribute('aria-pressed', String(isDark));
    themeToggle.title = isDark ? 'Usar tema claro' : 'Usar tema escuro';
  };
  themeToggle.addEventListener('click', () => {
    const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.theme = nextTheme;
    try {
      window.localStorage.setItem('metalcom-theme', nextTheme);
    } catch (_) {}
    updateThemeToggle();
  });
  updateThemeToggle();

  const syncSavedTheme = () => {
    try {
      document.documentElement.dataset.theme = window.localStorage.getItem('metalcom-theme') === 'dark' ? 'dark' : 'light';
    } catch (_) {}
    updateThemeToggle();
  };
  window.addEventListener('storage', (event) => {
    if (event.key === 'metalcom-theme' || event.key === null) syncSavedTheme();
  });
  window.addEventListener('pageshow', syncSavedTheme);
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') syncSavedTheme();
  });

  document.querySelectorAll('.modal-close').forEach((button) => { button.innerHTML = '<i data-lucide="x" aria-hidden="true"></i>'; });
  document.querySelectorAll('.repeatable-remove').forEach((button) => { button.innerHTML = '<i data-lucide="trash-2" aria-hidden="true"></i>'; });
  const dashboardMenu = document.querySelector('.dashboard-menu-toggle');
  if (dashboardMenu) dashboardMenu.innerHTML = '<i data-lucide="menu" aria-hidden="true"></i><span>Menu</span>';
  renderLucideIcons();

  document.querySelectorAll('[data-toast]').forEach((toast) => {
    let remaining = toast.classList.contains('flash-error') ? 8000 : 5000;
    let startedAt;
    let timer;
    let hovered = false;
    let focused = false;
    let dismissed = false;
    toast.style.setProperty('--toast-duration', `${remaining}ms`);
    const dismiss = () => {
      if (dismissed) return;
      dismissed = true;
      window.clearTimeout(timer);
      toast.classList.add('is-leaving');
      window.setTimeout(() => {
        const container = toast.parentElement;
        toast.remove();
        if (!container.children.length) container.remove();
      }, 300);
    };
    const updateTimer = () => {
      if (dismissed) return;
      if (timer !== undefined) {
        window.clearTimeout(timer);
        remaining = Math.max(0, remaining - (performance.now() - startedAt));
        timer = undefined;
      }
      const paused = hovered || focused;
      toast.style.setProperty('--toast-play-state', paused ? 'paused' : 'running');
      if (!paused) {
        startedAt = performance.now();
        timer = window.setTimeout(dismiss, remaining);
      }
    };
    toast.querySelector('.toast-close').addEventListener('click', dismiss);
    toast.addEventListener('mouseenter', () => { hovered = true; updateTimer(); });
    toast.addEventListener('mouseleave', () => { hovered = false; updateTimer(); });
    toast.addEventListener('focusin', () => { focused = true; updateTimer(); });
    toast.addEventListener('focusout', (event) => {
      focused = toast.contains(event.relatedTarget);
      updateTimer();
    });
    window.requestAnimationFrame(() => window.requestAnimationFrame(() => {
      toast.classList.add('is-visible');
      updateTimer();
    }));
  });

  const siteTopbar = document.querySelector('[data-topbar]');
  if (siteTopbar) {
    const header = document.querySelector('[data-header]');
    let topbarHidden = false;
    const updateTopbar = () => {
      topbarHidden = window.scrollY > (topbarHidden ? 1 : 12);
      siteTopbar.classList.toggle('is-hidden', topbarHidden);
      siteTopbar.setAttribute('aria-hidden', String(topbarHidden));
      header?.classList.toggle('is-scrolled', topbarHidden);
    };
    window.addEventListener('scroll', updateTopbar, { passive: true });
    window.addEventListener('pageshow', updateTopbar);
    updateTopbar();
  }

  const menu = document.querySelector('.menu-toggle');
  const mobile = document.querySelector('.mobile-menu');
  menu?.addEventListener('click', () => {
    const open = menu.getAttribute('aria-expanded') === 'true';
    menu.setAttribute('aria-expanded', String(!open));
    mobile.hidden = open;
    mobile.classList.toggle('is-open', !open);
  });

  const dashboardButton = document.querySelector('[data-dashboard-menu]');
  const sidebar = document.querySelector('[data-dashboard-sidebar]');
  dashboardButton?.addEventListener('click', () => sidebar?.classList.toggle('is-open'));
  sidebar?.querySelectorAll('a[href^="#"]').forEach((link) => link.addEventListener('click', () => sidebar.classList.remove('is-open')));

  const revealElements = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        revealObserver.unobserve(entry.target);
      });
    }, { threshold: 0.08, rootMargin: '0px 0px -24px' });
    revealElements.forEach((element) => revealObserver.observe(element));
  } else {
    revealElements.forEach((element) => element.classList.add('is-visible'));
  }

  const productCarousel = document.querySelector('[data-product-carousel]');
  if (productCarousel) {
    const track = productCarousel.querySelector('[data-carousel-track]');
    const cards = [...track.children];
    if (cards.length > 1) {
      const cloneCards = () => cards.map((card) => {
        const clone = card.cloneNode(true);
        clone.setAttribute('aria-hidden', 'true');
        clone.querySelectorAll('a, button').forEach((control) => { control.tabIndex = -1; });
        return clone;
      });
      const leadingCards = cloneCards();
      track.prepend(...leadingCards);
      track.append(...cloneCards());
      track.classList.add('has-infinite-loop');
      let cycle = 0;
      let position = 0;
      let lastWritten = 0;
      let lastFrame;
      let dragging = false;
      let manualDistance = 0;
      const measure = () => {
        const progress = cycle ? ((position - cycle) % cycle + cycle) % cycle / cycle : 0;
        cycle = cards[0].offsetLeft - leadingCards[0].offsetLeft;
        position = cycle * (1 + progress);
        track.scrollLeft = position;
        lastWritten = track.scrollLeft;
      };
      measure();
      const frame = (now) => {
        const elapsed = lastFrame === undefined ? 0 : Math.min(now - lastFrame, 64);
        lastFrame = now;
        if (Math.abs(track.scrollLeft - lastWritten) > 1) position = track.scrollLeft;
        if (cycle > 0) {
          if (Math.abs(manualDistance) > 0.1) {
            const change = manualDistance * (1 - Math.exp(-elapsed / 100));
            position += change;
            manualDistance -= change;
          }
          position = cycle + ((position - cycle) % cycle + cycle) % cycle;
          if (!dragging) track.scrollLeft = position;
          lastWritten = track.scrollLeft;
        }
        window.requestAnimationFrame(frame);
      };
      const moveProducts = (direction) => {
        manualDistance += direction * (cards[1].offsetLeft - cards[0].offsetLeft);
      };
      productCarousel.querySelector('[data-carousel-prev]')?.addEventListener('click', () => moveProducts(-1));
      productCarousel.querySelector('[data-carousel-next]')?.addEventListener('click', () => moveProducts(1));
      track.addEventListener('pointerdown', () => { dragging = true; manualDistance = 0; });
      const endDrag = () => { dragging = false; position = track.scrollLeft; };
      window.addEventListener('pointerup', endDrag);
      window.addEventListener('pointercancel', endDrag);
      if ('ResizeObserver' in window) new ResizeObserver(measure).observe(track);
      window.requestAnimationFrame(frame);
    }
  }

  document.querySelectorAll('[data-product-gallery]').forEach((gallery) => {
    const main = gallery.querySelector('[data-gallery-main]');
    gallery.querySelectorAll('[data-gallery-thumbnail]').forEach((thumbnail) => thumbnail.addEventListener('click', () => {
      if (!main) return;
      main.src = thumbnail.dataset.src;
      main.alt = thumbnail.dataset.alt || '';
      gallery.querySelectorAll('[data-gallery-thumbnail]').forEach((item) => item.classList.toggle('is-active', item === thumbnail));
    }));
  });

  document.querySelectorAll('.product-main-image').forEach((frame) => {
    const image = frame.querySelector('[data-gallery-main]');
    if (!image) return;
    frame.addEventListener('pointermove', (event) => {
      if (!window.matchMedia('(min-width: 901px) and (hover: hover) and (pointer: fine)').matches) return;
      const bounds = frame.getBoundingClientRect();
      const x = Math.max(0, Math.min(100, ((event.clientX - bounds.left) / bounds.width) * 100));
      const y = Math.max(0, Math.min(100, ((event.clientY - bounds.top) / bounds.height) * 100));
      image.style.setProperty('--zoom-x', `${x.toFixed(2)}%`);
      image.style.setProperty('--zoom-y', `${y.toFixed(2)}%`);
    });
    frame.addEventListener('pointerleave', () => {
      image.style.removeProperty('--zoom-x');
      image.style.removeProperty('--zoom-y');
    });
  });

  const heroSlides = [...document.querySelectorAll('.home-hero-slide')];
  const heroDots = document.querySelector('.home-hero-dots');
  if (heroSlides.length) {
    const hero = document.querySelector('[data-home-hero]');
    let active = 0;
    let heroTimer;
    const show = (index) => {
      active = (index + heroSlides.length) % heroSlides.length;
      heroSlides.forEach((slide, i) => slide.classList.toggle('is-active', i === active));
      heroDots?.querySelectorAll('button').forEach((dot, i) => {
        dot.classList.toggle('is-active', i === active);
        dot.setAttribute('aria-current', String(i === active));
      });
    };
    const startHero = () => {
      window.clearInterval(heroTimer);
      if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) heroTimer = window.setInterval(() => show(active + 1), 7000);
    };
    heroSlides.forEach((_, i) => { const dot = document.createElement('button'); dot.type = 'button'; dot.ariaLabel = `Imagem ${i + 1}`; dot.addEventListener('click', () => { show(i); startHero(); }); heroDots?.append(dot); });
    const moveHero = (direction) => { show(active + direction); startHero(); };
    hero?.querySelector('[data-hero-prev]')?.addEventListener('click', () => moveHero(-1));
    hero?.querySelector('[data-hero-next]')?.addEventListener('click', () => moveHero(1));
    show(0);
    startHero();
  }

  const project = document.querySelector('[data-project-carousel]');
  if (project) {
    const track = project.querySelector('[data-project-track]');
    const dots = project.querySelector('.project-dots');
    let index = 0;
    let projectTimer;
    const slides = [...track.children];
    const dotButtons = slides.map((_, dotIndex) => {
      const dot = document.createElement('button');
      dot.type = 'button';
      dot.ariaLabel = `Ver projeto ${dotIndex + 1}`;
      dot.addEventListener('click', () => { index = dotIndex; render(); startProjects(); });
      dots?.append(dot);
      return dot;
    });
    const render = () => {
      track.style.transform = `translate3d(-${index * 100}%, 0, 0)`;
      slides.forEach((slide, slideIndex) => slide.classList.toggle('is-active', slideIndex === index));
      dotButtons.forEach((dot, dotIndex) => dot.classList.toggle('is-active', dotIndex === index));
    };
    const moveProject = (direction) => { index = (index + direction + slides.length) % slides.length; render(); };
    const startProjects = () => {
      window.clearInterval(projectTimer);
      if (slides.length > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) projectTimer = window.setInterval(() => moveProject(1), 5600);
    };
    project.querySelector('[data-project-prev]')?.addEventListener('click', () => { moveProject(-1); startProjects(); });
    project.querySelector('[data-project-next]')?.addEventListener('click', () => { moveProject(1); startProjects(); });
    project.addEventListener('mouseenter', () => window.clearInterval(projectTimer));
    project.addEventListener('mouseleave', startProjects);
    project.addEventListener('focusin', () => window.clearInterval(projectTimer));
    project.addEventListener('focusout', startProjects);
    render();
    startProjects();
  }

  const categoryModal = document.querySelector('#category-admin-modal');
  const fillCategory = (data = {}) => {
    if (!categoryModal) return;
    categoryModal.querySelector('form').reset();
    categoryModal.querySelector('[data-category-id]').value = data.id || '';
    const name = categoryModal.querySelector('[data-category-name]');
    name.value = data.name || '';
    categoryModal.querySelector('[data-category-modal-title]').textContent = data.id ? 'Editar categoria' : 'Nova categoria';
    categoryModal.showModal();
    name.focus();
  };
  document.querySelector('[data-category-new]')?.addEventListener('click', () => fillCategory());
  document.querySelectorAll('[data-category-edit]').forEach((button) => button.addEventListener('click', () => {
    fillCategory(JSON.parse(button.dataset.categoryEdit));
  }));
  const categoryDeleteModal = document.querySelector('#category-delete-modal');
  document.querySelectorAll('[data-category-delete-url]').forEach((button) => button.addEventListener('click', () => {
    if (!categoryDeleteModal || button.disabled) return;
    categoryDeleteModal.querySelector('form').action = button.dataset.categoryDeleteUrl;
    categoryDeleteModal.querySelector('[data-category-delete-label]').textContent = button.dataset.categoryDeleteName;
    categoryDeleteModal.showModal();
  }));

  const userModal = document.querySelector('#user-modal');
  const fillUser = (data = {}) => {
    if (!userModal) return;
    const editing = Boolean(data.id);
    userModal.querySelector('[data-user-id]').value = data.id || '';
    userModal.querySelector('[data-user-name]').value = data.name || '';
    userModal.querySelector('[data-user-email]').value = data.email || '';
    userModal.querySelector('[data-user-role]').value = data.role || 'editor';
    userModal.querySelector('[data-user-active]').checked = data.active === undefined || Number(data.active) === 1;
    userModal.querySelector('[data-user-password]').value = '';
    userModal.querySelector('[data-user-password]').required = !editing;
    userModal.querySelector('[data-user-password-hint]').textContent = editing ? '(opcional)' : '(mínimo de 8 caracteres)';
    userModal.querySelector('[data-user-modal-title]').textContent = editing ? 'Editar usuário' : 'Adicionar usuário';
    userModal.showModal();
  };
  document.querySelector('[data-user-new]')?.addEventListener('click', () => fillUser());
  document.querySelectorAll('[data-user-edit]').forEach((button) => button.addEventListener('click', () => {
    fillUser(JSON.parse(button.dataset.userEdit));
  }));
  document.querySelectorAll('[data-dialog-close]').forEach((button) => button.addEventListener('click', () => button.closest('dialog')?.close()));
  document.querySelectorAll('dialog.admin-modal').forEach((dialog) => {
    dialog.addEventListener('pointerdown', (event) => {
      const bounds = dialog.getBoundingClientRect();
      const clickedOutside = event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom;
      if (!clickedOutside) return;
      dialog.classList.remove('is-refusing-close');
      void dialog.offsetWidth;
      dialog.classList.add('is-refusing-close');
      window.setTimeout(() => dialog.classList.remove('is-refusing-close'), 2000);
    });
  });

  const productModal = document.querySelector('#product-admin-modal');
  const productDeleteModal = document.querySelector('#product-delete-modal');
  productModal?.querySelector('[data-product-delete]')?.addEventListener('click', (event) => {
    const id = productModal.querySelector('[data-product-id]').value;
    if (!id || !productDeleteModal) return;
    productDeleteModal.querySelector('form').action = `${event.currentTarget.dataset.deleteBase}/${encodeURIComponent(id)}/delete`;
    productDeleteModal.querySelector('[data-product-delete-label]').textContent = productModal.querySelector('[data-product-name]').value;
    productDeleteModal.showModal();
  });
  const galleryHelp = productModal?.querySelector('.product-gallery-heading small');
  if (galleryHelp) galleryHelp.textContent = 'Use as setas para alterar a ordem ou a lixeira para excluir.';
  const renderProductGallery = (data = {}) => {
    const container = productModal.querySelector('[data-product-gallery-existing]');
    container.replaceChildren();
    const images = Array.isArray(data.gallery_images) ? data.gallery_images : [];
    if (!images.length) {
      const empty = document.createElement('p');
      empty.className = 'product-gallery-empty';
      empty.textContent = 'Nenhuma imagem adicional cadastrada.';
      container.append(empty);
      return;
    }
    images.forEach((image) => {
      const item = document.createElement('article');
      item.className = 'product-gallery-item';
      const preview = document.createElement('img');
      preview.src = image.image_url || image.image_path;
      preview.alt = image.alt_text || data.name || 'Imagem do produto';
      const controls = document.createElement('div');
      const order = document.createElement('input');
      order.type = 'hidden';
      order.name = `existing_gallery[${image.id}][sort_order]`;
      order.value = image.sort_order || 0;
      const moveLeft = document.createElement('button');
      moveLeft.type = 'button';
      moveLeft.className = 'gallery-move';
      moveLeft.setAttribute('aria-label', 'Mover imagem para esquerda');
      moveLeft.setAttribute('title', 'Mover para esquerda');
      moveLeft.innerHTML = '<i data-lucide="chevron-left" aria-hidden="true"></i>';
      const moveRight = document.createElement('button');
      moveRight.type = 'button';
      moveRight.className = 'gallery-move';
      moveRight.setAttribute('aria-label', 'Mover imagem para direita');
      moveRight.setAttribute('title', 'Mover para direita');
      moveRight.innerHTML = '<i data-lucide="chevron-right" aria-hidden="true"></i>';
      const remove = document.createElement('input');
      remove.type = 'checkbox';
      remove.name = `existing_gallery[${image.id}][delete]`;
      remove.className = 'gallery-remove-input';
      remove.hidden = true;
      const removeButton = document.createElement('button');
      removeButton.type = 'button';
      removeButton.className = 'gallery-remove';
      removeButton.setAttribute('aria-label', 'Excluir imagem');
      removeButton.setAttribute('title', 'Excluir imagem');
      removeButton.setAttribute('aria-pressed', 'false');
      removeButton.innerHTML = '<i data-lucide="trash-2" aria-hidden="true"></i>';
      removeButton.addEventListener('click', () => {
        remove.checked = !remove.checked;
        item.classList.toggle('is-marked-for-deletion', remove.checked);
        removeButton.setAttribute('aria-pressed', String(remove.checked));
        removeButton.setAttribute('aria-label', remove.checked ? 'Manter imagem' : 'Excluir imagem');
        removeButton.setAttribute('title', remove.checked ? 'Manter imagem' : 'Excluir imagem');
      });
      const syncGalleryOrder = () => {
        [...container.querySelectorAll('.product-gallery-item')].forEach((galleryItem, index) => {
          const orderInput = galleryItem.querySelector('input[name*="[sort_order]"]');
          if (orderInput) orderInput.value = (index + 1) * 10;
        });
      };
      moveLeft.addEventListener('click', () => {
        const previous = item.previousElementSibling;
        if (previous) container.insertBefore(item, previous);
        syncGalleryOrder();
      });
      moveRight.addEventListener('click', () => {
        const next = item.nextElementSibling;
        if (next) container.insertBefore(next, item);
        syncGalleryOrder();
      });
      controls.append(order, moveLeft, moveRight, remove, removeButton);
      item.append(preview, controls);
      container.append(item);
    });
    renderLucideIcons(container);
  };
  const fillProduct = (data = {}) => {
    productModal.querySelectorAll('input[type="file"]').forEach((input) => {
      input.value = '';
      input.dispatchEvent(new Event('change'));
    });
    productModal.querySelector('[data-product-id]').value = data.id || '';
    productModal.querySelector('[data-product-delete]').hidden = !data.id;
    productModal.querySelector('[data-product-current-image]').value = data.image_path || '';
    productModal.querySelector('[data-product-name]').value = data.name || '';
    productModal.querySelector('[data-product-slug]').value = data.slug || '';
    productModal.querySelector('[data-product-category]').value = data.category_id || productModal.querySelector('[data-product-category]').options[0]?.value;
    productModal.querySelector('[data-product-code]').value = data.code || '';
    productModal.querySelector('[data-product-description]').value = data.description || '';
    productModal.querySelector('[data-product-order]').value = data.sort_order || 0;
    productModal.querySelector('[data-product-active]').checked = data.active === undefined || Number(data.active) === 1;
    const preview = productModal.querySelector('[data-product-image-preview]');
    const currentImageBlock = preview.closest('div');
    if (data.image_path) {
      preview.src = data.image_url || data.image_path;
      preview.hidden = false;
      currentImageBlock.hidden = false;
    } else {
      preview.removeAttribute('src');
      preview.hidden = true;
      currentImageBlock.hidden = true;
    }
    productModal.querySelector('[data-product-image]').value = '';
    const imageInput = productModal.querySelector('[data-product-image]');
    const imageLabel = imageInput.closest('label');
    imageLabel.childNodes[0].nodeValue = data.id ? 'Substituir imagem' : 'Imagem principal';
    imageInput.required = !data.id;
    const imageHint = imageInput.nextElementSibling;
    if (imageHint?.matches('small')) imageHint.textContent = data.id ? 'Deixe vazio para manter a imagem atual.' : 'Selecione a imagem principal do produto.';
    productModal.querySelector('[name="gallery_images[]"]').value = '';
    renderProductGallery(data);
    productModal.querySelector('[data-product-modal-title]').textContent = data.id ? 'Editar produto' : 'Novo produto';
    productModal.showModal();
  };
  document.querySelector('[data-product-new]')?.addEventListener('click', () => fillProduct());
  document.querySelectorAll('[data-product-edit]').forEach((button) => button.addEventListener('click', () => fillProduct(JSON.parse(button.dataset.productEdit))));

  const homeSettingsForms = document.querySelectorAll('#configuracoes-home form.settings-form');
  const categoryOrderHelp = document.querySelector('[data-add-category]')?.closest('.repeatable-heading')?.querySelector('p');
  if (categoryOrderHelp) categoryOrderHelp.textContent = 'Use as setas para alterar a ordem das categorias.';
  const visibleRepeatableItems = (container) => [...container.children].filter((item) => item.matches('[data-repeatable-item]') && !item.hidden);
  const syncRepeatableOrder = (container) => {
    visibleRepeatableItems(container).forEach((item, index) => {
      const order = item.querySelector('input[name*="[sort_order]"]');
      if (order) order.value = (index + 1) * 10;
    });
  };
  const iconButton = (icon, label, className = 'order-arrow') => {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = className;
    button.setAttribute('aria-label', label);
    button.setAttribute('title', label);
    button.innerHTML = `<i data-lucide="${icon}" aria-hidden="true"></i>`;
    return button;
  };
  const enhanceRepeatableContainer = (container) => {
    if (!container) return;
    container.querySelectorAll('[data-repeatable-item]:not([data-order-ready])').forEach((item) => {
      item.dataset.orderReady = 'true';
      const order = item.querySelector('input[name*="[sort_order]"]');
      const orderLabel = order?.closest('label');
      if (order) order.type = 'hidden';
      if (orderLabel) orderLabel.hidden = true;
      const actions = document.createElement('div');
      actions.className = 'repeatable-actions';
      const left = iconButton('chevron-left', 'Mover para esquerda');
      const right = iconButton('chevron-right', 'Mover para direita');
      const remove = item.querySelector('.repeatable-remove');
      left.addEventListener('click', () => {
        const items = visibleRepeatableItems(container);
        const index = items.indexOf(item);
        if (index > 0) container.insertBefore(item, items[index - 1]);
        syncRepeatableOrder(container);
      });
      right.addEventListener('click', () => {
        const items = visibleRepeatableItems(container);
        const index = items.indexOf(item);
        if (index >= 0 && index < items.length - 1) container.insertBefore(items[index + 1], item);
        syncRepeatableOrder(container);
      });
      actions.append(left, right);
      if (remove) actions.append(remove);
      item.append(actions);
    });
    syncRepeatableOrder(container);
    renderLucideIcons(container);
  };
  const bindExistingRemoval = (container) => {
    container?.querySelectorAll('[data-remove-existing]').forEach((button) => button.addEventListener('click', () => {
      const item = button.closest('[data-repeatable-item]');
      const deletion = item?.querySelector('[data-delete-field]');
      if (!item || !deletion) return;
      deletion.value = '1';
      item.hidden = true;
      const list = item.parentElement;
      if (list) syncRepeatableOrder(list);
    }));
  };
  const createRemoveButton = () => {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'repeatable-remove';
    button.setAttribute('aria-label', 'Remover item');
    button.setAttribute('title', 'Remover item');
    button.innerHTML = '<i data-lucide="trash-2" aria-hidden="true"></i>';
    button.addEventListener('click', () => button.closest('[data-repeatable-item]')?.remove());
    return button;
  };
  const addCategoryCard = () => {
    const container = document.querySelector('[data-category-items]');
    if (!container) return;
    const key = `new_${Date.now()}`;
    const article = document.createElement('article');
    article.dataset.repeatableItem = '';
    article.className = 'repeatable-card is-new';
    article.innerHTML = `<span class="new-item-badge">Nova categoria</span><label>Nome da categoria<input name="new_categories[${key}][name]" required></label><label>Descrição<textarea name="new_categories[${key}][description]" rows="3"></textarea></label><div class="category-card-fields"><label>Ordem<input type="number" min="0" name="new_categories[${key}][sort_order]" value="0"></label><label class="checkbox-label"><input type="checkbox" name="new_categories[${key}][active]" checked> Exibir</label></div><label class="stacked-image-field">Nova imagem<input type="file" name="new_category_image_${key}" accept="image/jpeg,image/png,image/webp" required></label>`;
    article.prepend(createRemoveButton());
    container.append(article);
    enhanceRepeatableContainer(container);
    enhanceFileInputs(article);
    article.querySelector('input[name$="[name]"]').focus();
  };
  const addProjectCard = () => {
    const container = document.querySelector('[data-project-items]');
    if (!container) return;
    const key = `new_${Date.now()}`;
    const article = document.createElement('article');
    article.dataset.repeatableItem = '';
    article.className = 'repeatable-card is-new';
    article.innerHTML = `<span class="new-item-badge">Novo projeto</span><label>Título<input name="new_projects[${key}][title]" required></label><label>Texto alternativo<input name="new_projects[${key}][alt_text]"></label><label>Ordem<input type="number" min="0" name="new_projects[${key}][sort_order]" value="0"></label><label class="stacked-image-field">Nova imagem<input type="file" name="new_project_image_${key}" accept="image/jpeg,image/png,image/webp" required></label><label class="checkbox-label"><input type="checkbox" name="new_projects[${key}][active]" checked> Exibir</label>`;
    article.prepend(createRemoveButton());
    container.append(article);
    enhanceRepeatableContainer(container);
    enhanceFileInputs(article);
    article.querySelector('input[name$="[title]"]').focus();
  };
  const filePreviewCleanup = new WeakMap();
  const previewSelectedImages = (input) => {
    filePreviewCleanup.get(input)?.();
    filePreviewCleanup.delete(input);
    const files = [...input.files].filter((file) => ['image/jpeg', 'image/png', 'image/webp'].includes(file.type));
    if (!files.length) return;
    const urls = files.map((file) => URL.createObjectURL(file));
    let restore;
    if (input.name === 'gallery_images[]') {
      const container = productModal.querySelector('[data-product-gallery-existing]');
      const empty = container.querySelector('.product-gallery-empty');
      if (empty) empty.hidden = true;
      const items = files.map((file, index) => {
        const item = document.createElement('article');
        item.className = 'product-gallery-item';
        const image = document.createElement('img');
        image.src = urls[index];
        image.alt = `Prévia de ${file.name}`;
        const caption = document.createElement('small');
        caption.textContent = 'Nova imagem';
        item.append(image, caption);
        container.append(item);
        return item;
      });
      restore = () => {
        items.forEach((item) => item.remove());
        if (empty) empty.hidden = false;
      };
    } else {
      const isProduct = input.matches('[data-product-image]');
      const label = input.closest('label');
      let image = isProduct ? productModal.querySelector('[data-product-image-preview]') : label.querySelector('img');
      const created = !image;
      if (created) {
        image = document.createElement('img');
        image.className = 'admin-thumb';
        label.append(image);
      }
      const original = { src: image.getAttribute('src'), alt: image.alt, hidden: image.hidden };
      const imageBlock = isProduct ? image.closest('div') : null;
      const blockHidden = imageBlock?.hidden;
      image.src = urls[0];
      image.alt = `Prévia de ${files[0].name}`;
      image.hidden = false;
      if (imageBlock) imageBlock.hidden = false;
      restore = () => {
        if (created) image.remove();
        else {
          if (original.src === null) image.removeAttribute('src');
          else image.setAttribute('src', original.src);
          image.alt = original.alt;
          image.hidden = original.hidden;
        }
        if (imageBlock) imageBlock.hidden = blockHidden;
      };
    }
    filePreviewCleanup.set(input, () => {
      restore();
      urls.forEach((url) => URL.revokeObjectURL(url));
    });
  };
  function enhanceFileInputs(root = document) {
    root.querySelectorAll('input[type="file"]:not([data-upload-ready])').forEach((input) => {
      input.dataset.uploadReady = 'true';
      input.classList.add('custom-file-native');
      const control = document.createElement('span');
      control.className = 'custom-file-control';
      control.innerHTML = `<i data-lucide="${input.multiple ? 'images' : 'image-up'}" aria-hidden="true"></i><span><strong>${input.multiple ? 'Selecionar imagens' : 'Selecionar imagem'}</strong><small>Nenhum arquivo selecionado</small></span>`;
      input.before(control);
      input.addEventListener('change', () => {
        const names = [...input.files].map((file) => file.name);
        control.querySelector('small').textContent = names.length ? (names.length > 2 ? `${names.length} arquivos selecionados` : names.join(', ')) : 'Nenhum arquivo selecionado';
        control.classList.toggle('has-file', names.length > 0);
        previewSelectedImages(input);
      });
      renderLucideIcons(control);
    });
  }
  if (homeSettingsForms.length) {
    homeSettingsForms.forEach(bindExistingRemoval);
    enhanceRepeatableContainer(document.querySelector('[data-category-items]'));
    enhanceRepeatableContainer(document.querySelector('[data-project-items]'));
    document.querySelector('[data-add-category]')?.addEventListener('click', addCategoryCard);
    document.querySelector('[data-add-project]')?.addEventListener('click', addProjectCard);
  }
  enhanceFileInputs();
  productModal?.addEventListener('close', () => {
    productModal.querySelectorAll('input[type="file"]').forEach((input) => {
      input.value = '';
      input.dispatchEvent(new Event('change'));
    });
  });
})();


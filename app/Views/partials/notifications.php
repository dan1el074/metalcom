<?php if ($flashes): ?>
  <div class="toast-container" aria-label="Notificações">
    <?php foreach ($flashes as $type => $message): ?>
      <div class="flash flash-<?= e($type) ?>" data-toast role="<?= $type === 'error' ? 'alert' : 'status' ?>" aria-atomic="true">
        <i data-lucide="<?= $type === 'error' ? 'triangle-alert' : 'circle-check' ?>" class="toast-icon" aria-hidden="true"></i>
        <div class="toast-message"><?= e($message) ?></div>
        <button class="toast-close" type="button" aria-label="Fechar notificação" title="Fechar notificação"><i data-lucide="x" aria-hidden="true"></i></button>
        <span class="toast-progress" aria-hidden="true"></span>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

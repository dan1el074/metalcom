<?php
$socialLinks = [];
foreach (['instagram' => 'Instagram', 'facebook' => 'Facebook'] as $network => $label) {
    $profileUrl = trim((string) ($socialSettings['contact_' . $network] ?? ''));
    if (filter_var($profileUrl, FILTER_VALIDATE_URL) && in_array(strtolower((string) parse_url($profileUrl, PHP_URL_SCHEME)), ['https', 'http'], true)) {
        $socialLinks[$network] = ['url' => $profileUrl, 'label' => $label];
    }
}
?>
<?php if ($socialLinks): ?>
  <nav class="social-links company-social-links" aria-label="Redes sociais da Metalcom">
    <?php foreach ($socialLinks as $network => $profile): ?>
      <a href="<?= e($profile['url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($profile['label']) ?> da Metalcom" title="<?= e($profile['label']) ?>">
        <?php if ($network === 'instagram'): ?>
          <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5" /><circle cx="12" cy="12" r="4" /><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none" /></svg>
        <?php else: ?>
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M14 22v-9h3l.5-4H14V7c0-1.2.4-2 2-2h2V1.4A24 24 0 0 0 15 1c-3 0-5 1.8-5 5v3H7v4h3v9z" /></svg>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>
  </nav>
<?php endif; ?>

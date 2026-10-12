<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="adm-card adm-login">
  <h1>Klart</h1>
  <p>Databasmigrationerna är körda.<?= $created ? ' Admin-användaren är skapad.' : '' ?></p>
  <?php if ($created): ?>
  <p class="adm-muted">Ta gärna bort <code>admin.setupToken</code> ur .env när sajten är uppsatt — sätt en ny token tillfälligt när migrationer behöver köras igen.</p>
  <?php endif; ?>
  <a class="adm-btn adm-btn-primary adm-btn-block" href="<?= site_url('admin/login/') ?>">Till inloggningen</a>
</div>
<?= $this->endSection() ?>

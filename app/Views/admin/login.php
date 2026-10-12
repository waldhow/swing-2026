<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="adm-card adm-login">
  <img class="adm-login-logo" src="/assets/logo.svg" alt="Swing Golf &amp; Lounge" width="140" height="108" />
  <h1>Logga in</h1>
  <?php if (! empty($error)): ?>
  <div class="adm-flash adm-flash-err" role="alert"><?= esc($error) ?></div>
  <?php endif; ?>
  <form method="post" action="<?= site_url('admin/login/') ?>" novalidate>
    <?= csrf_field() ?>
    <div class="adm-field">
      <label for="f-email">E-post</label>
      <input type="email" id="f-email" name="email" value="<?= esc($email ?? '', 'attr') ?>" autocomplete="username" required aria-required="true" />
    </div>
    <div class="adm-field">
      <label for="f-password">Lösenord</label>
      <input type="password" id="f-password" name="password" autocomplete="current-password" required aria-required="true" />
    </div>
    <button type="submit" class="adm-btn adm-btn-primary adm-btn-block">Logga in</button>
  </form>
</div>
<?= $this->endSection() ?>

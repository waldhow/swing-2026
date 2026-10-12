<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="adm-card adm-login">
  <h1>Uppsättning</h1>
  <p class="adm-muted">Kör databasmigrationerna<?= $needsUser ? ' och skapa den första admin-användaren' : '' ?>. Sidan kan köras igen efter en uppdatering av sajten för att lägga till nya tabeller.</p>
  <?= partial('admin/_errors', ['errors' => $errors]) ?>
  <form method="post" action="<?= site_url('admin/setup/') ?>?token=<?= esc(urlencode($token), 'attr') ?>" novalidate>
    <?= csrf_field() ?>
    <?php if ($needsUser): ?>
    <?= partial('admin/_field', ['name' => 'name', 'label' => 'Namn', 'value' => service('request')->getPost('name'), 'errors' => $errors, 'required' => true, 'attrs' => 'autocomplete="name" maxlength="120"']) ?>
    <?= partial('admin/_field', ['name' => 'email', 'label' => 'E-post', 'type' => 'email', 'value' => service('request')->getPost('email'), 'errors' => $errors, 'required' => true, 'attrs' => 'autocomplete="username"']) ?>
    <?= partial('admin/_field', ['name' => 'password', 'label' => 'Lösenord', 'type' => 'password', 'errors' => $errors, 'required' => true, 'help' => 'Minst ' . (int) config('Admin')->minPasswordLength . ' tecken.', 'attrs' => 'autocomplete="new-password"']) ?>
    <?= partial('admin/_field', ['name' => 'password_repeat', 'label' => 'Upprepa lösenord', 'type' => 'password', 'errors' => $errors, 'required' => true, 'attrs' => 'autocomplete="new-password"']) ?>
    <?php endif; ?>
    <button type="submit" class="adm-btn adm-btn-primary adm-btn-block"><?= $needsUser ? 'Kör migrationer och skapa användare' : 'Kör migrationer' ?></button>
  </form>
</div>
<?= $this->endSection() ?>

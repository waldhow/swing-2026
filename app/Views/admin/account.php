<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="adm-head"><h1>Konto</h1></div>
<?= partial('admin/_errors', ['errors' => $errors]) ?>
<form class="adm-card adm-form" method="post" action="<?= site_url('admin/konto/') ?>" novalidate>
  <?= csrf_field() ?>
  <p class="adm-muted">Inloggad som <strong><?= esc($user['email']) ?></strong>.</p>
  <?= partial('admin/_field', ['name' => 'name', 'label' => 'Namn', 'value' => service('request')->getPost('name') ?? $user['name'], 'errors' => $errors, 'required' => true, 'attrs' => 'autocomplete="name" maxlength="120"']) ?>
  <fieldset class="adm-fieldset">
    <legend>Byt lösenord</legend>
    <p class="adm-help">Lämna tomt för att behålla nuvarande lösenord. Minst <?= (int) config('Admin')->minPasswordLength ?> tecken.</p>
    <?= partial('admin/_field', ['name' => 'current_password', 'label' => 'Nuvarande lösenord', 'type' => 'password', 'errors' => $errors, 'attrs' => 'autocomplete="current-password"']) ?>
    <?= partial('admin/_field', ['name' => 'new_password', 'label' => 'Nytt lösenord', 'type' => 'password', 'errors' => $errors, 'attrs' => 'autocomplete="new-password"']) ?>
    <?= partial('admin/_field', ['name' => 'new_password_repeat', 'label' => 'Upprepa nytt lösenord', 'type' => 'password', 'errors' => $errors, 'attrs' => 'autocomplete="new-password"']) ?>
  </fieldset>
  <div class="adm-actions"><button type="submit" class="adm-btn adm-btn-primary">Spara</button></div>
</form>
<?= $this->endSection() ?>

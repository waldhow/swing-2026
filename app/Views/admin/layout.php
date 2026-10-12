<?php
/**
 * Admin-layout — fristående från den publika layouten. $title, $user, $navAdmin.
 */
$navAdmin ??= '';
$menu = [
    'oversikt'      => ['Översikt', 'admin/'],
    'forfragningar' => ['Förfrågningar', 'admin/forfragningar/'],
    'bestallningar' => ['Beställningar', 'admin/bestallningar/'],
    'tavlingar'     => ['Tävlingar', 'admin/tavlingar/'],
    'kampanjer'     => ['Kampanjer', 'admin/kampanjer/'],
    'konto'         => ['Konto', 'admin/konto/'],
];
$flashSuccess = session()->getFlashdata('success');
$flashError   = session()->getFlashdata('error');
?>
<!DOCTYPE html>
<html lang="sv">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="robots" content="noindex, nofollow" />
<title><?= esc($title ?? 'Admin') ?> · Swing admin</title>
<link rel="icon" href="/favicon.ico" sizes="32x32" />
<link rel="icon" type="image/svg+xml" href="<?= asset('favicon.svg') ?>" />
<link rel="stylesheet" href="/assets/fonts/fonts.css" />
<link rel="stylesheet" href="<?= asset('assets/site.css') ?>" />
<link rel="stylesheet" href="<?= asset('assets/admin.css') ?>" />
</head>
<body class="adm">
<a class="adm-skip" href="#adm-main">Hoppa till innehållet</a>
<?php if (! empty($user)): ?>
<header class="adm-top">
  <a class="adm-brand" href="<?= site_url('admin/') ?>"><img src="/assets/logo.svg" alt="Swing Golf &amp; Lounge" width="70" height="54" /><span>Admin</span></a>
  <nav class="adm-nav" aria-label="Admin">
    <?php foreach ($menu as $key => [$label, $path]): ?>
    <a href="<?= site_url($path) ?>"<?= $navAdmin === $key ? ' aria-current="page"' : '' ?>><?= esc($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <div class="adm-user">
    <span><?= esc($user['name']) ?></span>
    <form method="post" action="<?= site_url('admin/logout/') ?>">
      <?= csrf_field() ?>
      <button type="submit" class="adm-btn adm-btn-ghost adm-btn-sm">Logga ut</button>
    </form>
    <a class="adm-site" href="/" rel="noopener">Till sajten</a>
  </div>
</header>
<?php endif; ?>
<main id="adm-main" class="adm-main<?= empty($user) ? ' adm-main-narrow' : '' ?>">
  <?php if ($flashSuccess): ?>
  <div class="adm-flash adm-flash-ok" role="status"><?= esc($flashSuccess) ?></div>
  <?php endif; ?>
  <?php if ($flashError): ?>
  <div class="adm-flash adm-flash-err" role="alert"><?= esc($flashError) ?></div>
  <?php endif; ?>
  <?= $this->renderSection('content') ?>
</main>
</body>
</html>

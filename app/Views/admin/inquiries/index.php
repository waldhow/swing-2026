<?php use App\Models\InquiryModel; ?>
<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="adm-head"><h1>Förfrågningar</h1></div>
<p class="adm-muted">Förfrågningar raderas automatiskt efter <?= (int) config('Inquiries')->retentionMonths ?> månader enligt integritetspolicyn.</p>

<form class="adm-filterform" method="get" action="<?= site_url('admin/forfragningar/') ?>">
  <label for="flt-status">Status</label>
  <select id="flt-status" name="status">
    <option value="">Alla</option>
    <?php foreach (InquiryModel::STATUSES as $key => $label): ?>
    <option value="<?= esc($key, 'attr') ?>"<?= $status === $key ? ' selected' : '' ?>><?= esc($label) ?></option>
    <?php endforeach; ?>
  </select>
  <label for="flt-typ">Typ</label>
  <select id="flt-typ" name="typ">
    <option value="">Alla</option>
    <?php foreach (InquiryModel::TYPES as $key => $label): ?>
    <option value="<?= esc($key, 'attr') ?>"<?= $type === $key ? ' selected' : '' ?>><?= esc($label) ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="adm-btn adm-btn-ghost adm-btn-sm">Filtrera</button>
</form>

<?php if ($inquiries === []): ?>
<div class="adm-card adm-empty">Inga förfrågningar matchar.</div>
<?php else: ?>
<div class="adm-tablewrap">
<table class="adm-table">
  <thead>
    <tr><th scope="col">Inkom</th><th scope="col">Namn</th><th scope="col">Typ</th><th scope="col">Antal</th><th scope="col">Status</th></tr>
  </thead>
  <tbody>
    <?php foreach ($inquiries as $i): ?>
    <tr>
      <td><?= esc(date('Y-m-d H:i', strtotime((string) $i['created_at']))) ?></td>
      <td><a href="<?= site_url('admin/forfragningar/' . $i['id'] . '/') ?>"><?= esc($i['name']) ?></a><div class="adm-muted adm-small"><?= esc($i['email']) ?></div></td>
      <td><?= esc(InquiryModel::TYPES[$i['type']] ?? $i['type']) ?></td>
      <td><?= $i['participants'] ? (int) $i['participants'] : '<span class="adm-muted">—</span>' ?></td>
      <td>
        <span class="adm-tag adm-tag-<?= $i['status'] === 'new' ? 'new' : 'ok' ?>"><?= esc(InquiryModel::STATUSES[$i['status']] ?? $i['status']) ?></span>
        <?php if (! empty($i['mail_error'])): ?><span class="adm-tag adm-tag-err">Ej mejlad</span><?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?= $pager->links() ?>
<?php endif; ?>
<?= $this->endSection() ?>

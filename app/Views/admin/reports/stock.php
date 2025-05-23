<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <div class="d-flex justify-content-start align-items-center gap-2">
        <a href="/admin" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-angle-left me-2"></i>
            <?= t('Go back') ?>
        </a>
        <h3 class="display-4"><?= t($title) ?></h3>
    </div>

<?php if (empty($stockReport)): ?>
    <p>Nenhum dado de estoque disponível.</p>
<?php else: ?>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Produto</th>
            <th>Variação</th>
            <th>Quantidade em Estoque</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($stockReport as $item): ?>
            <tr>
                <td><?= esc($item['name'] ?? $item['product_name'] ?? 'N/A') ?></td>
                <td><?= esc($item['variation_name'] ?? '-') ?></td>
                <td><?= esc($item['quantity'] ?? $item['stock'] ?? 0) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?= $this->endSection() ?>
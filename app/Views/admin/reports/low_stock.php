<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <div class="d-flex justify-content-start align-items-center gap-2">
        <a href="/admin" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-angle-left me-2"></i>
            <?= t('Go back') ?>
        </a>
        <h3 class="display-4"><?= t($title) ?></h3>
    </div>

    <form action="<?= site_url('admin/low-stock-report') ?>" method="get" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="threshold" class="form-label">Limite de Estoque:</label>
                <input type="number" name="threshold" id="threshold" class="form-control" value="<?= esc($threshold) ?>" min="0" required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Gerar Relatório</button>
            </div>
        </div>
    </form>

<?php if (empty($lowStockProducts)): ?>
    <p>Nenhum produto com estoque abaixo do limite definido.</p>
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
        <?php foreach ($lowStockProducts as $item): ?>
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
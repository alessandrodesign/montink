<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <div class="d-flex justify-content-start align-items-center gap-2">
        <a href="/admin" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-angle-left me-2"></i>
            <?= t('Go back') ?>
        </a>
        <h3 class="display-4"><?= t($title) ?></h3>
    </div>

    <form action="<?= site_url('admin/product-sales-report') ?>" method="get" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Data Inicial:</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="<?= esc($startDate) ?>" required>
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">Data Final:</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="<?= esc($endDate) ?>" required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Gerar Relatório</button>
            </div>
        </div>
    </form>

<?php if (empty($productSalesReport)): ?>
    <p>Nenhum dado encontrado para o período selecionado.</p>
<?php else: ?>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Produto</th>
            <th>Quantidade Vendida</th>
            <th>Receita Total</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $totalQuantity = 0;
        $totalRevenue = 0;
        foreach ($productSalesReport as $row):
            $productName = $row['name'] ?? '';
            $quantitySold = (int) ($row['quantity_sold'] ?? 0);
            $totalSales = (float) ($row['total_sales'] ?? 0);
            $totalQuantity += $quantitySold;
            $totalRevenue += $totalSales;
            ?>
            <tr>
                <td><?= esc($productName) ?></td>
                <td><?= esc($quantitySold) ?></td>
                <td><?= money($totalSales, 'BRL', 'pt_BR') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <th>Total</th>
            <th><?= esc($totalQuantity) ?></th>
            <th><?= money($totalRevenue, 'BRL', 'pt_BR') ?></th>
        </tr>
        </tfoot>
    </table>
<?php endif; ?>

<?= $this->endSection() ?>
<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <div class="d-flex justify-content-start align-items-center gap-2">
        <a href="/admin" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-angle-left me-2"></i>
            <?= t('Go back') ?>
        </a>
        <h3 class="display-4"><?= t($title) ?></h3>
    </div>

    <form action="<?= site_url('admin/sales-report') ?>" method="get" class="mb-4">
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

<?php if (empty($salesReport)): ?>
    <p>Nenhum dado encontrado para o período selecionado.</p>
<?php else: ?>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Data</th>
            <th>Total de Vendas</th>
            <th>Total de Pedidos</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $totalSales = 0;
        $totalOrders = 0;
        foreach ($salesReport as $row):
            $date = $row['date'] ?? '';
            $orderCount = (int) ($row['order_count'] ?? 0);
            $totalSale = (float) ($row['total_sales'] ?? 0);
            $totalSales += $totalSale;
            $totalOrders += $orderCount;
            ?>
            <tr>
                <td><?= esc(date('d/m/Y', strtotime($date))) ?></td>
                <td><?= money($totalSale, 'BRL', 'pt_BR') ?></td>
                <td><?= esc($orderCount) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <th>Total</th>
            <th><?= money($totalSales, 'BRL', 'pt_BR') ?></th>
            <th><?= esc($totalOrders) ?></th>
        </tr>
        </tfoot>
    </table>
<?php endif; ?>

<?= $this->endSection() ?>
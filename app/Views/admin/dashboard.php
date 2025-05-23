<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>


    <h3 class="display-4"><?= t($title) ?></h3>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <h5 class="card-title">Relatório de Vendas (Últimos 30 dias)</h5>
                    <p class="card-text">Total de Vendas: <?= money(array_sum(array_column($salesReport, 'total_sales')), 'BRL', 'pt_BR') ?></p>
                    <p class="card-text">Total de Pedidos: <?= array_sum(array_column($salesReport, 'order_count')) ?></p>
                    <a href="<?= site_url('admin/sales-report') ?>" class="btn btn-light">Ver Detalhes</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <h5 class="card-title">Relatório de Vendas por Produto (Últimos 30 dias)</h5>
                    <?php
                    $topProduct = !empty($productSalesReport) ? $productSalesReport[0]['name'] : 'Nenhum';
                    $totalQuantity = array_sum(array_column($productSalesReport, 'quantity_sold'));
                    ?>
                    <p class="card-text">Produto mais vendido: <?= esc($topProduct) ?></p>
                    <p class="card-text">Quantidade total vendida: <?= esc($totalQuantity) ?></p>
                    <a href="<?= site_url('admin/product-sales-report') ?>" class="btn btn-light">Ver Detalhes</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-warning h-100">
                <div class="card-body">
                    <h5 class="card-title">Produtos com Estoque Baixo</h5>
                    <p class="card-text">Número de produtos: <?= count($lowStockProducts) ?></p>
                    <a href="<?= site_url('admin/low-stock-report') ?>" class="btn btn-light">Ver Detalhes</a>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>
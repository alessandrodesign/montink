<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <div class="row">
        <div class="col">
            <div class="d-flex align-items-baseline gap-3">
                <i class="fas fa-project-diagram"></i>
                <h2 class="display-4"><?= t($title) ?></h2>
                <p class="fs-4"><?= tf('Product: %s', $product->name) ?></p>
            </div>

            <form action="/products/add-variation/<?= esc($product->id) ?>" method="post" class="row g-3">
                <div class="input-group">
                    <label class="input-group-text" for="name"><?= t('Variation Name') ?></label>
                    <input type="text" name="name" id="name" class="form-control" required>


                    <label class="input-group-text" for="price_adjustment"><?= t('Price Adjustment') ?></label>
                    <input type="number" name="price_adjustment" id="price_adjustment" class="form-control" step="0.01"
                           value="0.00" required>
                </div>
                <div class="input-group">
                    <label class="input-group-text" for="initial_stock"><?= t('Initial Stock') ?></label>
                    <input type="number" name="initial_stock" id="initial_stock" class="form-control" value="0" min="0">

                    <button type="submit" class="btn btn-primary"><?= t('Add Variation') ?></button>
                </div>
            </form>
        </div>
    </div>

<?= $this->endSection() ?>
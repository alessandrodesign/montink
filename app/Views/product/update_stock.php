<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <div class="row">
        <div class="col">
            <div class="d-flex align-items-baseline gap-3">
                <i class="fas fa-boxes"></i>
                <h2 class="display-4"><?= t($title) ?></h2>
                <p class="fs-4"><?= tf('Product: %s', $product->name) ?></p>
            </div>

            <?php if (isset($variation)): ?>
                <h4><?= tf('Variation: %s', $variation->name) ?></h4>
            <?php endif; ?>

            <form action="/products/update-stock/<?= esc($product->id) ?><?= isset($variation) ? '/' . esc($variation->id) : '' ?>"
                  method="post">
                <div class="input-group">
                    <label class="input-group-text" for="quantity"><?= t('Quantity to Add/Remove') ?></label>
                    <input type="number" name="quantity" id="quantity" class="form-control" value="0">
                    <button type="submit" class="btn btn-primary"><?= t('Update Stock') ?></button>
                </div>
            </form>
        </div>
    </div>

<?= $this->endSection() ?>
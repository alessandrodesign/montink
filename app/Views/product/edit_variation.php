<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <div class="row">
        <div class="col">
            <div class="d-flex align-items-baseline gap-3">
                <i class="fas fa-project-diagram"></i>
                <h2 class="display-4"><?= t($title) ?></h2>
                <p class="fs-4"><?= tf('Product: %s', $product->name) ?></p>
            </div>

            <h3>Product: <?= esc($product->name) ?></h3>
            <h4>Edit Variation: <?= esc($variation->name) ?></h4>

            <form action="/products/edit-variation/<?= esc($variation->id) ?>" method="post">
                <div class="form-group">
                    <label class="form-label" for="name">Variation Name:</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= esc($variation->name) ?>"
                           required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="price_adjustment">Price Adjustment:</label>
                    <input type="number" name="price_adjustment" id="price_adjustment" class="form-control" step="0.01"
                           value="<?= esc($variation->price_adjustment) ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Variation</button>
            </form>
        </div>
    </div>

<?= $this->endSection() ?>
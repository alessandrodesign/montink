<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <h2 class="display-4"><?= esc($title) ?></h2>

    <div class="row">
        <div class="col-md-6">
            <img src="<?= $product->image ? image_url($product->image) : 'https://placehold.co/400' ?>"
                 class="img-fluid" alt="<?= esc($product->name) ?>">
        </div>
        <div class="col-md-6">
            <h4><?= t($product->name) ?></h4>
            <p><?= t($product->description) ?></p>
            <p><?= esc($product->price) ?></p>

            <?php if (!empty($product->variations)): ?>
                <h4><?= t('Variations') ?></h4>
                <form action="/cart/add" method="post" class="row g-2">
                    <input type="hidden" name="product_id" value="<?= esc($product->id) ?>">
                    <div class="input-group col-12">
                        <label class="input-group-text" for="variation"><?= t('Select Variation') ?></label>
                        <select name="variation_id" id="variation" class="form-select">
                            <option value=""><?= t('None') ?></option>
                            <?php foreach ($product->variations as $variation): ?>
                                <optgroup label="<?= t($variation->name) ?>">
                                    <option value="<?= esc($variation->id) ?>">
                                        +<?= esc($variation->price_adjustment) ?>
                                    </option>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-group col-12">
                        <label class="input-group-text" for="quantity"><?= t('Quantity') ?></label>
                        <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><?= t('Add to Cart') ?></button>
                    </div>
                </form>
            <?php else: ?>
                <form action="/cart/add" method="post" class="row g-3">
                    <input type="hidden" name="product_id" value="<?= esc($product->id) ?>">
                    <div class="col-12">
                        <label class="form-label" for="quantity"><?= t('Quantity') ?></label>
                        <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><?= t('Add to Cart') ?></button>
                    </div>
                </form>
            <?php endif; ?>

            <?php if (auth()->isAdmin()): ?>
                <hr>
                <p class="fs-5">
                    <?= tf("Products in stock: %s", $product->stock) ?>
                </p>
                <a href="/products/edit/<?= esc($product->id) ?>" class="btn btn-warning">
                    <i class="far fa-edit me-2"></i> <?= t('Edit Product') ?></a>
                <a href="/products/add-variation/<?= esc($product->id) ?>" class="btn btn-info">
                    <i class="fas fa-project-diagram me-2"></i> <?= t('Add Variation') ?></a>
                <a href="/products/update-stock/<?= esc($product->id) ?>" class="btn btn-secondary">
                    <i class="fas fa-boxes me-2"></i> <?= t('Update Stock') ?></a>
            <?php endif; ?>
        </div>
    </div>

<?= $this->endSection() ?>
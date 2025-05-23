<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <div class="row">
        <div class="col-8">

            <h2 class="display-4"><?= t($title) ?></h2>

            <form action="/products/edit/<?= esc($product->id) ?>" method="post" enctype="multipart/form-data"
                  class="row g-3">
                <div class="col-2">
                    <label class="form-label" for="status"><?= t('Status') ?></label>
                    <select name="status" id="status" class="form-select">
                        <option value="active" <?= $product->isActive() ? 'selected' : '' ?>>
                            <?= t('Active') ?>
                        </option>
                        <option value="inactive" <?= !$product->isActive() ? 'selected' : '' ?>>
                            <?= t('Inactive') ?>
                        </option>
                    </select>
                </div>
                <div class="col-2">
                    <label class="form-label" for="price"><?= t('Price') ?></label>
                    <input type="number" name="price" id="price" class="form-control" step="0.01"
                           value="<?= esc($product->price->getAmount()) ?>" required>
                </div>
                <div class="col-8">
                    <label class="form-label" for="name"><?= t('Name') ?></label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= esc($product->name) ?>"
                           required>
                </div>
                <div class="col-12">
                    <label class="form-label" for="description"><?= t('Description') ?></label>
                    <textarea name="description" id="description" class="form-control"
                              required><?= esc($product->description) ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label" for="image"><?= t('Image') ?></label>
                    <input type="file" name="image" id="image" class="form-control">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><?= t('Update Product') ?></button>
                </div>
            </form>
        </div>
        <div class="col-4">
            <img src="<?= $product->image ? image_url($product->image) : 'https://placehold.co/400' ?>"
                 class="img-fluid" alt="Current Image">
        </div>
    </div>

<?= $this->endSection() ?>
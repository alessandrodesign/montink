<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <h2 class="display-4"><?= esc($title) ?></h2>

    <div class="row g-3">
        <?php foreach ($products as $product): ?>
            <div class="col-md-3">
                <div class="card">
                    <img src="<?= $product->image ? image_url($product->image) : 'https://placehold.co/300' ?>"
                         class="card-img-top" alt="<?= esc($product->name) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= t($product->name) ?></h5>
                        <p class="card-text"><?= t($product->description) ?></p>
                        <p class="card-text"><?= esc($product->price) ?></p>
                        <a href="/products/view/<?= esc($product->id) ?>" class="btn btn-primary">
                            <?= t('View Product') ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?= $this->endSection() ?>
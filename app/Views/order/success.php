<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <div class="row justify-content-center">
        <div class="col-8">

            <h2 class="display-4"><?= t($title) ?></h2>

            <p><?= tf('Thank you for your order! Your order number is %s', $order->order_number) ?>.</p>

            <p><?= t('You can view your order details') ?> <a
                        href="/orders/view/<?= esc($order->order_number) ?>"><?= t('here') ?></a>.</p>

        </div>
    </div>
<?= $this->endSection() ?>
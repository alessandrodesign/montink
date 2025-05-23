<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <div class="row justify-content-center">
        <div class="col-8">

            <div class="d-flex justify-content-between align-items-center gap-2">
                <a href="/orders" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-angle-left me-2"></i>
                    <?= t('Go back') ?>
                </a>
                <h3 class="display-4"><?= t($title) ?></h3>
            </div>

            <h3><?= t('Order Details') ?></h3>

            <table class="table">
                <tr>
                    <th><?= t('Order Number') ?></th>
                    <td><?= esc($order->order_number) ?></td>
                </tr>
                <tr>
                    <th><?= t('Date') ?></th>
                    <td><?= esc($order->created_at) ?></td>
                </tr>
                <tr>
                    <th><?= t('Status') ?></th>
                    <td><?= esc($order->status->value) ?></td>
                </tr>
                <tr>
                    <th><?= t('Payment Status') ?></th>
                    <td><?= esc($order->payment_status->value) ?></td>
                </tr>
                <tr>
                    <th><?= t('Shipping Address') ?></th>
                    <td><?= esc($order->shipping_address) ?></td>
                </tr>
                <tr>
                    <th><?= t('Payment Method') ?></th>
                    <td><?= esc($order->payment_method->label()) ?></td>
                </tr>
            </table>

            <?php if (!empty($order->items)): ?>
                <h3><?= t('Order Items') ?></h3>
                <table class="table">
                    <thead>
                    <tr>
                        <th><?= t('Product') ?></th>
                        <th><?= t('Price') ?></th>
                        <th><?= t('Quantity') ?></th>
                        <th><?= t('Total') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($order->items as $item): ?>
                        <tr>
                            <td>
                                <?= t($item->Product()->name) ?>
                                <?php if ($item->variation_name): ?>
                                    <br><small><?= tf('Variation: %s', esc($item->variation_name)) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($item->price) ?></td>
                            <td><?= esc($item->quantity) ?></td>
                            <td><?= esc($item->getSubtotal()) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3" class="text-right">Total:</td>
                        <td>$<?= esc($order->final_amount) ?></td>
                    </tr>
                    </tfoot>
                </table>
            <?php endif; ?>

            <?php if ($order->isPending()): ?>
                <a href="/orders/cancel/<?= esc($order->id) ?>" class="btn btn-danger">Cancel Order</a>
            <?php endif; ?>
        </div>
    </div>
<?= $this->endSection() ?>
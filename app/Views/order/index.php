<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <div class="row">
        <div class="col-12">

            <h2 class="display-4"><?= t($title) ?></h2>

            <?php if (empty($orders)): ?>
                <p><?= t('No orders found.') ?></p>
            <?php else: ?>
                <table class="table">
                    <thead>
                    <tr>
                        <th><?= t('Order Number') ?></th>
                        <th><?= t('Date') ?></th>
                        <th><?= t('Total') ?></th>
                        <th><?= t('Status') ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= esc($order->order_number) ?></td>
                            <td><?= esc($order->created_at) ?></td>
                            <td><?= esc($order->final_amount) ?></td>
                            <td><?= t($order->status->value) ?></td>
                            <td>
                                <a href="/orders/view/<?= esc($order->order_number) ?>"
                                   class="btn btn-primary btn-sm"><?= t('View') ?></a>
                                <?php if ($order->isPending()): ?>
                                    <a href="/orders/cancel/<?= esc($order->id) ?>"
                                       class="btn btn-danger btn-sm"><?= t('Cancel') ?></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
<?= $this->endSection() ?>
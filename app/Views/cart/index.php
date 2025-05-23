<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col">
            <h2 class="display-4"><?= t($title) ?></h2>

            <?php if (empty($cart['items'])): ?>
                <p><?= t('Your cart is empty.') ?></p>
            <?php else: ?>
                <form action="/cart/update" method="post">
                    <table class="table">
                        <thead>
                        <tr>
                            <th><?= t('Product') ?></th>
                            <th><?= t('Price') ?></th>
                            <th><?= t('Quantity') ?></th>
                            <th><?= t('Total') ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($cart['items'] as $item): ?>
                            <tr>
                                <td>
                                    <?= t($item['product_name']) ?>
                                    <?php if (isset($item['variation'])): ?>
                                        <br><small><?= t('Variation') ?>: <?= t($item['variation']['name']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= money($item['price']) ?></td>
                                <td>
                                    <input type="hidden" name="row_id" value="<?= esc($item['row_id']) ?>">
                                    <input type="number" name="quantity" value="<?= esc($item['quantity']) ?>" min="1"
                                           class="form-control" style="width: 80px;" aria-label="quantity">
                                </td>
                                <td><?= money($item['subtotal']) ?></td>
                                <td>
                                    <a href="/cart/remove/<?= esc($item['row_id']) ?>"
                                       class="btn btn-danger btn-sm"><i
                                                class="fas fa-trash-alt me-2"></i> <?= t('Remove') ?></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><?= t('Subtotal') ?></td>
                            <td><?= money($cart['subtotal']) ?></td>
                            <td></td>
                        </tr>
                        <?php if ($cart['coupon']): ?>
                            <tr>
                                <td colspan="3"
                                    class="text-right"><?= tf('Coupon (%s)', esc($cart['coupon']->code)) ?></td>
                                <td>-<?= money($cart['discount']) ?></td>
                                <td><a href="/cart/remove-coupon"
                                       class="btn btn-outline-danger btn-sm"><i class="fas fa-trash-alt me-2"></i>
                                        <?= t('Remove Coupon') ?></a></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-right">Frete:</td>
                                <td><?= money($cart['shipping_fee']) ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <div class="input-group">
                                        <input type="text" name="coupon_code" class="form-control"
                                               placeholder="<?= t('Coupon Code') ?>" aria-label="Coupon Code">
                                        <button id="btnApplyCoupon" type="button"
                                                class="btn btn-primary"><?= t('Apply Coupon') ?></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="3" class="text-right"><?= t('Total') ?></td>
                            <td><?= money($cart['total']) ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sync me-2"></i> <?= t('Update Cart') ?></button>
                                <a href="/cart/clear" class="btn btn-danger">
                                    <i class="fas fa-cart-arrow-down me-2"></i> <?= t('Clear Cart') ?></a>
                                <a href="/cart/checkout" class="btn btn-success">
                                    <i class="fas fa-shopping-bag me-2"></i> <?= t('Checkout') ?></a>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </form>
            <?php endif; ?>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
    <script type="module">
        import api from './js/Api.js';

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById("formApplyCoupon"),
                btn = document.getElementById("btnApplyCoupon");

            btn.addEventListener('click', () => {
                api().create(
                    "/cart/apply-coupon",
                    {"coupon_code": document.querySelector('[name="coupon_code"]').value}
                ).then(responseData);
            });
        });
    </script>
<?= $this->endSection() ?>
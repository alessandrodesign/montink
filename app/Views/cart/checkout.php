<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <div class="row">
        <div class="col">
            <h2 class="display-4"><?= t($title) ?></h2>

            <h3 class="fs-4"><?= t('Order Summary') ?></h3>
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
                <?php foreach ($cart['items'] as $item): ?>
                    <tr>
                        <td>
                            <?= t($item['product_name']) ?>
                            <?php if (isset($item['variation'])): ?>
                                <br><small><?= t('Variation') ?>: <?= t($item['variation']['name']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= money($item['price']) ?></td>
                        <td><?= esc($item['quantity']) ?></td>
                        <td><?= money($item['subtotal']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><?= t('Subtotal') ?></td>
                    <td><?= money($cart['subtotal']) ?></td>
                </tr>
                <?php if ($cart['coupon']): ?>
                    <tr>
                        <td colspan="3" class="text-right"><?= tf('Coupon (%s)', esc($cart['coupon']->code)) ?></td>
                        <td>-<?= money($cart['discount']) ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td colspan="3" class="text-right"><?= t('Total') ?></td>
                    <td><?= money($cart['total']) ?></td>
                </tr>
                </tfoot>
            </table>

            <h3 class="fs-4"><?= t('Shipping Information') ?></h3>
            <form action="/orders/process" method="post" class="row g-3">
                <div class="input-group">
                    <label class="input-group-text" for="zip-code"><?= t('Zip code') ?></label>
                    <input type="text" id="zip-code" name="zip-code" class="form-control" maxlength="8" required>
                    <button type="button" id="btnZipCode" class="btn btn-secondary"><?=t('Find address')?></button>
                </div>

                <div id="address-fields" style="display:none;">
                    <div class="form-group">
                        <label for="street"><?= t('street') ?></label>
                        <input type="text" id="street" name="street" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="district"><?= t('District') ?></label>
                        <input type="text" id="district" name="district" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="city"><?= t('City') ?></label>
                        <input type="text" id="city" name="city" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="uf">Estado:</label>
                        <input type="text" id="uf" name="uf" class="form-control" readonly>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label" for="shipping_address"><?= t('Shipping Address') ?></label>
                    <textarea readonly name="shipping_address" id="shipping_address" class="form-control" required></textarea>
                </div>
                <div class="col-12">
                    <div class="input-group">
                        <label class="input-group-text" for="payment_method"><?= t('Payment Method') ?></label>
                        <select name="payment_method" id="payment_method" class="form-select">
                            <?php foreach (\App\Enums\PaymentMethod::cases() as $paymentMethod): ?>
                                <option value="<?= $paymentMethod->value ?>"><?= $paymentMethod->label(); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary"><?= t('Place Order') ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection() ?>
<?= $this->section('javascript') ?>
    <script type="module">
        import api from './js/Api.js';

        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById("btnZipCode");

            btn.addEventListener('click', () => {
                const zipCode = document.getElementById('zip-code').value.replace(/\D/g, '');

                if (zipCode.length !== 8) {
                    appendAlert('<?=t('Invalid zip code')?>', 'danger');
                    return;
                }

                api('https://viacep.com.br').create(`/ws/${zipCode}/json/`, null, 'get')
                    .then(data => {
                        if (data.erro) {
                            appendAlert('<?=t('Zip code not found')?>', 'danger');
                            return;
                        }

                        document.getElementById('street').value = data.logradouro || '';
                        document.getElementById('district').value = data.bairro || '';
                        document.getElementById('city').value = data.localidade || '';
                        document.getElementById('uf').value = data.uf || '';
                        document.getElementById('shipping_address').value = `${data.logradouro || ''}, `
                            + `${data.bairro || ''}, `
                            + `${data.localidade || ''} - `
                            + `${data.uf || ''}`;
                        //document.getElementById('address-fields').style.display = 'block';
                    })
                    .catch(() => appendAlert('<?=t('Error searching for zip code')?>', 'danger'));
            });
        });
    </script>
<?= $this->endSection() ?>
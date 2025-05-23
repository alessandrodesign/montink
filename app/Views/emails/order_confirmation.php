<!DOCTYPE html>
<html lang="<?= service('request')->getLocale() ?>">
<head>
    <meta charset="UTF-8"/>
    <title><?= esc(tf('Order Confirmation #%s', $order->order_number)) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #eee;
            background-color: #fafafa;
        }

        h1 {
            color: #007bff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 8px 12px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            text-align: left;
        }

        .total {
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 0.9em;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h1><?= t('Thank you for your order!') ?></h1>
    <p><?= esc(tf('Olá %s', $user->name ?? 'Cliente')) ?>,</p>
    <p>
        <?= tf(
            'Recebemos seu pedido %s realizado em %s.',
            "<strong>#" . esc($order->order_number) . "</strong>",
            date('d/m/Y H:i', strtotime($order->created_at))
        ) ?>
    </p>

    <h2><?= t('Order Details') ?></h2>
    <table>
        <thead>
        <tr>
            <th><?= t('Product') ?></th>
            <th><?= t('Variation') ?></th>
            <th><?= t('Unit Price') ?></th>
            <th><?= t('Quantity') ?></th>
            <th><?= t('Subtotal') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= esc(t($item->product_name)) ?></td>
                <td><?= esc(t($item->variation_name) ?: '-') ?></td>
                <td><?= esc($item->price) ?></td>
                <td><?= esc($item->quantity) ?></td>
                <td><?= esc($item->getSubtotal()) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="4" class="total"><?= t('Total') ?></td>
            <td class="total"><?= esc($order->final_amount) ?></td>
        </tr>
        </tfoot>
    </table>

    <h2><?= t('Shipping Information') ?></h2>
    <p><?= nl2br(esc($order->shipping_address)) ?></p>

    <h2><?= t('Payment Method') ?></h2>
    <p><?= esc(ucfirst($order->payment_method->label())) ?></p>

    <p><?= t('If you have any questions, please contact us.') ?></p>

    <div class="footer">
        <p><?= tf('Yours sincerely, %s %s', '<br/>', t('Customer Service Team')) ?></p>
    </div>
</div>
</body>
</html>
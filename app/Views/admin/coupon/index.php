<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <h2>Coupons</h2>

    <a href="/admin/coupons/create" class="btn btn-success mb-3">Create Coupon</a>

<?php if (empty($coupons)): ?>
    <p>No coupons found.</p>
<?php else: ?>
    <table class="table">
        <thead>
        <tr>
            <th>Code</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Min Purchase</th>
            <th>Status</th>
            <th>Valid From</th>
            <th>Expires At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($coupons as $coupon): ?>
            <tr>
                <td><?= esc($coupon->code) ?></td>
                <td><?= esc(ucfirst($coupon->discount_type)) ?></td>
                <td>
                    <?php if ($coupon->discount_type === 'percent'): ?>
                        <?= esc($coupon->discount_amount) ?>%
                    <?php else: ?>
                        $<?= esc($coupon->discount_amount) ?>
                    <?php endif; ?>
                </td>
                <td>$<?= esc($coupon->min_purchase) ?></td>
                <td><?= esc(ucfirst($coupon->status)) ?></td>
                <td><?= esc($coupon->starts_at) ?></td>
                <td><?= esc($coupon->expires_at) ?></td>
                <td>
                    <a href="/admin/coupons/edit/<?= esc($coupon->id) ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="/admin/coupons/delete/<?= esc($coupon->id) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this coupon?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?= $this->endSection() ?>
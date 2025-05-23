<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <h2>Create Coupon</h2>

    <form action="/admin/coupons/create" method="post">
        <div class="form-group">
            <label class="form-label" for="code">Code:</label>
            <input type="text" name="code" id="code" class="form-control" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="discount_type">Discount Type:</label>
            <select name="discount_type" id="discount_type" class="form-control">
                <option value="percent">Percent (%)</option>
                <option value="fixed">Fixed ($)</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label" for="discount_amount">Discount Amount:</label>
            <input type="number" name="discount_amount" id="discount_amount" class="form-control" step="0.01" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="min_purchase">Minimum Purchase:</label>
            <input type="number" name="min_purchase" id="min_purchase" class="form-control" step="0.01" value="0">
        </div>
        <div class="form-group">
            <label class="form-label" for="starts_at">Valid From:</label>
            <input type="date" name="starts_at" id="starts_at" class="form-control">
        </div>
        <div class="form-group">
            <label class="form-label" for="expires_at">Expires At:</label>
            <input type="date" name="expires_at" id="expires_at" class="form-control">
        </div>
        <div class="form-group">
            <label class="form-label" for="status">Status:</label>
            <select name="status" id="status" class="form-control">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Create Coupon</button>
        <a href="/admin/coupons" class="btn btn-secondary">Cancel</a>
    </form>

<?= $this->endSection() ?>
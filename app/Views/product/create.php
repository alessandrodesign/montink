<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <h2><?= esc($title) ?></h2>

    <form action="/products/create" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label class="form-label" for="name">Name:</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="description">Description:</label>
            <textarea name="description" id="description" class="form-control" required></textarea>
        </div>
        <div class="form-group">
            <label class="form-label" for="price">Price:</label>
            <input type="number" name="price" id="price" class="form-control" step="0.01" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="status">Status:</label>
            <select name="status" id="status" class="form-control">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label" for="image">Image:</label>
            <input type="file" name="image" id="image" class="form-control">
        </div>
        <div class="form-group">
            <label class="form-label" for="initial_stock">Initial Stock:</label>
            <input type="number" name="initial_stock" id="initial_stock" class="form-control" value="0" min="0">
        </div>
        <button type="submit" class="btn btn-primary">Create Product</button>
    </form>

<?= $this->endSection() ?>
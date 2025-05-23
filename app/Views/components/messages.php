<?php if (session()->has('message')): ?>
    <?php $message = session('message'); ?>
    <div class="alert alert-<?= esc($message['type']) ?> alert-dismissible fade show" role="alert">
        <?= t($message['text']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php foreach (session('errors') as $error): ?>
            <?= t($error) ?><br>
        <?php endforeach; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

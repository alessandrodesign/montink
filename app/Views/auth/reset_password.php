<?= $this->extend('layout/public') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-4">
        <h2 class="display-4"><?= esc($title) ?></h2>

        <form action="" method="post" class="row g-3">
            <input type="hidden" name="token" value="<?= esc($token) ?>">
            <div class="col-12">
                <label class="form-label" for="password"><?= t('New Password') ?></label>
                <input class="form-control" type="password" name="password" id="password" required>
            </div>
            <div class="col-12">
                <label class="form-label" for="password_confirm"><?= t('Confirm Password') ?></label>
                <input class="form-control" type="password" name="password_confirm" id="password_confirm" required>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary"><?= t('Reset Password') ?></button>
            </div>
        </form>

        <hr/>

        <p><?= tf('Remember your password? %s', '<a href="/auth/login">' . t('Login') . '</a>') ?></p>
    </div>
</div>

<?= $this->endSection() ?>
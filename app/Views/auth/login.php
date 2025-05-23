<?= $this->extend('layout/public') ?>

<?= $this->section('content') ?>

    <div class="row justify-content-center">
        <div class="col-md-4">
            <h2 class="display-4"><?= t($title) ?></h2>

            <form action="/auth/login" method="post" class="row g-3">
                <div class="col-12">
                    <label class="form-label" for="email"><?= t('Email'); ?></label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= old('email') ?>"
                           required>
                </div>
                <div class="col-12">
                    <label class="form-label" for="password"><?= t('Password'); ?></label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn btn-primary"><?= t('Login'); ?></button>
                    <a href="/auth/forgot-password"><?= t('Forgot Password?'); ?></a>
                </div>
            </form>

            <hr />

            <p><?= tf("Don't have an account? %s", '<a href="/auth/register">' . t('Register') . '</a>') ?></p>
        </div>
    </div>

<?= $this->endSection() ?>
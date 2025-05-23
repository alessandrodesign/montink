<?= $this->extend('layout/public') ?>

<?= $this->section('content') ?>

    <div class="row justify-content-center">
        <div class="col-md-4">
            <h2 class="display-4"><?= esc($title) ?></h2>

            <form action="/auth/forgot-password" method="post" class="row g-3">
                <div class="col-12">
                    <label class="form-label" for="email"><?= t('Email') ?></label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= old('email') ?>"
                           required>
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
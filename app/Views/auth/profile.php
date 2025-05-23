<?= $this->extend('layout/authenticated') ?>

<?= $this->section('content') ?>

    <div class="row justify-content-center">
        <div class="col-md-4">
            <h2 class="display-4"><?= t($title) ?></h2>

            <form action="/auth/profile" method="post" class="row g-3">
                <div class="col-12">
                    <label class="form-label" for="name"><?= t('Name') ?></label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= esc($user->name) ?>"
                           required>
                </div>
                <div class="col-12">
                    <label class="form-label" for="email"><?= t('Email') ?></label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= esc($user->email) ?>"
                           required>
                </div>
                <div class="col-12">
                    <label class="form-label" for="password"><?= t('New Password (optional)') ?></label>
                    <input type="password" name="password" id="password" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label" for="password_confirm"><?= t('Confirm New Password') ?></label>
                    <input type="password" name="password_confirm" id="password_confirm" class="form-control">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><?= t('Update Profile') ?></button>
                </div>
            </form>
        </div>
    </div>

<?= $this->endSection() ?>
<!DOCTYPE html>
<html lang="<?= service('request')->getLocale() ?>">
<head>
    <base href="<?= site_url() ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <?= $this->renderSection('stylesheet') ?>
    <style>
        body {
            padding-top: 56px; /* Height of the navbar */
        }

        .navbar {
            background-color: #343a40;
        }

        .navbar-dark .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.75);
        }

        .navbar-dark .navbar-nav .nav-link:hover {
            color: white;
        }

        .container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark fixed-top">
    <a class="navbar-brand" href="/">Mini ERP</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="/"><?= t('Home') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/products"><?= t('Products') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link position-relative" href="/cart">
                    <?= t('Cart') ?>
                    <?php if (cart()->count() > 0): ?>
                        <span class="position-absolute top-100 start-50 translate-middle badge rounded-pill bg-danger">
                            <?= cart()->count() ?>
                            </span>
                    <?php endif; ?>
                </a>
            </li>
            <?php if (auth()->isLogged()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/orders"><?= t('Orders') ?></a>
                </li>
                <?php if (auth()->isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin"><?= t('Admin') ?></a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
        <ul class="navbar-nav">
            <?php if (auth()->isLogged()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/auth/profile">
                        <i class="fas fa-user me-2"></i> <?= esc(auth()->userFirstName()) ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/auth/logout">
                        <i class="fas fa-sign-out-alt me-2"></i> <?= t('Logout') ?>
                    </a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="/auth/login">
                        <i class="fas fa-sign-in-alt me-2"></i> <?= t('Login') ?>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div class="container" id="app">
    <?= view('components/messages') ?>
    <?= $this->renderSection('content') ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
<script src="./js/global.js"></script>

<?= $this->renderSection('javascript') ?>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? e($title) . ' — ' . config('app.business.name') : config('app.business.name') ?></title>
    <meta name="description" content="Daysitech Computers — laptops, desktops, accessories, and expert computer repair services in Accra, Ghana.">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <link rel="icon" href="<?= asset('images/logo/favicon.png') ?>">

    <!-- Self-hosted fonts -->
    <link href="<?= asset('css/fonts.css') ?>" rel="stylesheet">

    <!-- Bootstrap 5 (layout grid, offcanvas, modal JS) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <!-- AOS scroll animations -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <!-- SweetAlert2 (toasts / confirm dialogs) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.5/sweetalert2.min.css" rel="stylesheet">

    <!-- App styles (compiled from /public/assets/scss) -->
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/responsive.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/storefront-enhancements.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/account-enhancements.css') ?>" rel="stylesheet">
</head>
<body>
<div class="dtc-page-loader" id="pageLoader" role="status" aria-live="polite" aria-label="Loading Daysitech Computers">
    <div class="dtc-loader-grid" aria-hidden="true"></div>
    <div class="dtc-loader-panel">
        <div class="dtc-loader-topline"><span>DT / SYSTEM BOOT</span><span class="dtc-loader-status"><i></i> LIVE</span></div>
        <div class="dtc-loader-brand"><span class="dtc-loader-mark"><b>D</b><b>T</b></span><span>Daysitech<br><em>Computers</em></span></div>
        <div class="dtc-loader-line"><span></span></div>
        <p>Preparing your workspace<span class="dtc-loader-dots" aria-hidden="true">...</span></p>
        <div class="dtc-loader-meta"><span>ACCESS / SECURE</span><span>ACCRA / GH</span></div>
    </div>
</div>

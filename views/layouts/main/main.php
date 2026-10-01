<?php require resolve_php_file(base_path('views/layouts'), 'header'); ?>

    <?php require resolve_php_file(base_path('views/layouts'), 'navbar'); ?>

<main>
    <div class="container-inner" style="padding-top:1rem;">
        <?php require resolve_php_file(base_path('views/layouts'), 'alerts'); ?>
    </div>
    <?= $content ?? '' ?>
</main>

<?php require resolve_php_file(base_path('views/layouts'), 'footer'); ?>

<!-- Bootstrap Bundle (incl. Popper) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<!-- AOS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>AOS.init({ once: true, duration: 500, offset: 60 });</script>
<!-- SweetAlert2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<!-- App JS -->
<script src="<?= asset('js/app.js') ?>"></script>
<?php if (!empty($pageScript)): ?>
<script src="<?= asset('js/' . $pageScript) ?>"></script>
<?php endif; ?>
</body>
</html>

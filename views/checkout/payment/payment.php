<section class="section text-center">
    <div class="container-inner">
        <i class="fa-solid fa-shield-halved" style="font-size:2.5rem;color:#C9793D;"></i>
        <h1 class="mt-3" style="font-size:1.75rem;">Redirecting to Secure Payment…</h1>
        <p class="text-muted-dtc">Please wait while we connect you to our payment provider. Do not close this window.</p>
        <div class="spinner-border text-secondary mt-3" role="status"><span class="visually-hidden">Loading…</span></div>
    </div>
</section>
<script>
    // Card/mobile money checkouts are redirected server-side by CheckoutController;
    // this page is shown only if that redirect could not happen automatically.
    setTimeout(() => { window.location.href = '/checkout'; }, 8000);
</script>

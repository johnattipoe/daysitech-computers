<?php $biz = config('app.business'); ?>
<div class="page-header">
    <div class="container-inner">
        <h1>Contact Us</h1>
        <p>Questions about an order, a repair, or just want to say hi?</p>
    </div>
</div>

<section class="section">
    <div class="container-inner">
        <div class="row g-5">
            <div class="col-lg-5">
                <div class="summary-card">
                    <h6 class="mb-4">Get in Touch</h6>
                    <div class="mb-3"><i class="fa-solid fa-location-dot me-2" style="color:#C9793D;"></i><?= e($biz['address']) ?></div>
                    <div class="mb-3"><i class="fa-solid fa-phone me-2" style="color:#C9793D;"></i><a href="tel:<?= e($biz['phone']) ?>"><?= e($biz['phone']) ?></a></div>
                    <div class="mb-3"><i class="fa-brands fa-whatsapp me-2" style="color:#C9793D;"></i><a href="https://wa.me/<?= preg_replace('/\D/', '', $biz['whatsapp']) ?>">Chat on WhatsApp</a></div>
                    <div class="mb-3"><i class="fa-solid fa-envelope me-2" style="color:#C9793D;"></i><a href="mailto:<?= e($biz['email']) ?>"><?= e($biz['email']) ?></a></div>
                    <div class="mb-0"><i class="fa-solid fa-clock me-2" style="color:#C9793D;"></i><?= e($biz['hours']) ?></div>
                </div>

                <div class="summary-card mt-4">
                    <h6 class="mb-3">What can we help with?</h6>
                    <ul class="list-unstyled text-muted-dtc mb-0">
                        <li class="mb-2"><i class="fa-solid fa-check me-2" style="color:#C9793D;"></i>Choosing a laptop or desktop</li>
                        <li class="mb-2"><i class="fa-solid fa-check me-2" style="color:#C9793D;"></i>Repair diagnosis and updates</li>
                        <li class="mb-2"><i class="fa-solid fa-check me-2" style="color:#C9793D;"></i>Business equipment and support</li>
                        <li><i class="fa-solid fa-check me-2" style="color:#C9793D;"></i>Orders, delivery and warranty questions</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-7">
                <form action="/contact" method="POST" class="summary-card">
                    <?= csrf_field() ?>
                    <h6 class="mb-3">Send a Message</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Message</label>
                            <textarea name="message" class="form-control" rows="5" required></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-copper mt-4">Send Message</button>
                </form>
            </div>
        </div>

        <div class="trust-strip mt-5" style="border-radius:16px;">
            <div class="container-inner d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h2 class="h5 mb-1">Need a repair instead?</h2>
                    <p class="text-muted-dtc mb-0">Book a diagnostic appointment and track your service from start to finish.</p>
                </div>
                <a href="/repairs/book" class="btn btn-copper">Book a Repair <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
        </div>

        <div class="section-head mt-5"><h2>Before You Visit</h2><p>A few details can help us respond faster.</p></div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="summary-card h-100">
                    <h3 class="h5"><i class="fa-solid fa-receipt me-2" style="color:#C9793D;"></i>For an order</h3>
                    <p class="text-muted-dtc mb-0">Include your order number and the best phone number to reach you.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card h-100">
                    <h3 class="h5"><i class="fa-solid fa-laptop-medical me-2" style="color:#C9793D;"></i>For a repair</h3>
                    <p class="text-muted-dtc mb-0">Tell us the device type, the problem, and when the issue started.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card h-100">
                    <h3 class="h5"><i class="fa-solid fa-building me-2" style="color:#C9793D;"></i>For a business</h3>
                    <p class="text-muted-dtc mb-0">Share your team size, equipment needs, and preferred support timeframe.</p>
                </div>
            </div>
        </div>

        <div class="summary-card mt-5">
            <h2 class="h4">What happens next?</h2>
            <p class="text-muted-dtc mb-0">A member of our team will review your message and respond using the contact details you provide. For urgent repair updates, calling or WhatsApp is usually the quickest option.</p>
        </div>
    </div>
</section>

<?php $biz = config('app.business'); ?>
<footer class="dtc-footer">
    <div class="container-inner">
        <div class="footer-grid">
            <div>
                <h5>Daysitech Computers</h5>
                <p style="max-width:32ch;font-size:.9rem;">Your trusted partner for computer sales, upgrades, and expert repairs across Accra — genuine parts, honest diagnostics, fast turnaround.</p>
                <div class="social-row mt-3">
                    <a href="<?= e($biz['socials']['facebook']) ?>" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="<?= e($biz['socials']['instagram']) ?>" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="<?= e($biz['socials']['twitter']) ?>" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                </div>
            </div>
            <div>
                <h5>Shop</h5>
                <ul>
                    <li><a href="/products">All Products</a></li>
                    <li><a href="/categories">Categories</a></li>
                    <li><a href="/products?sort=newest">New Arrivals</a></li>
                    <li><a href="/products?in_stock=1">In Stock</a></li>
                </ul>
            </div>
            <div>
                <h5>Services</h5>
                <ul>
                    <li><a href="/repairs/book">Book a Repair</a></li>
                    <li><a href="/repairs/track">Track a Repair</a></li>
                    <li><a href="/contact">IT Consulting</a></li>
                    <li><a href="/about">Warranty Info</a></li>
                </ul>
            </div>
            <div>
                <h5>Company</h5>
                <ul>
                    <li><a href="/about">About Us</a></li>
                    <li><a href="/contact">Contact</a></li>
                    <li><a href="/terms">Terms of Service</a></li>
                    <li><a href="/privacy">Privacy Policy</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> Daysitech Computers. All rights reserved.</span>
            <span><?= e($biz['address']) ?> &middot; <?= e($biz['phone']) ?></span>
        </div>
    </div>
</footer>

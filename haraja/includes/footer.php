<style>
    .site-footer {
        background-color: var(--footer-bg);
        /* Fallback to dark if variable is missing */
        color: var(--text-color);
        padding: 4rem 0 2rem;
        /* border-top: 1px solid var(--border-color); */
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .site-footer h3,
    .site-footer h4,
    .site-footer a {
        color: var(--text-color) !important;
    }

    .site-footer p {
        color: var(--text-color);
        opacity: 0.8;
        /* Keeps it readable */
    }

    .site-footer a:hover {
        color: var(--primary-gold);
    }
</style>

</main>
<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-col" data-aos="fade-up">
            <h3>Haraja</h3>
            <p>A tropical oasis where luxury meets nature. Relax, swim, and celebrate in style.</p>
        </div>
        <div class="footer-col" data-aos="fade-up" data-aos-delay="100">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="rooms.php">Accommodations</a></li>
                <li><a href="events.php">Event Venues</a></li>
                <li><a href="gallery.php">Gallery</a></li>
            </ul>
        </div>
        <div class="footer-col" data-aos="fade-up" data-aos-delay="200">
            <h4>Contact Us</h4>
            <p>123 Tropical Paradise Way<br>Island Cove, IC 98765</p>
            <p>Email: reserve@Haraja.com</p>
            <p>Phone: +1 (555) 123-4567</p>
        </div>
        <div class="footer-col" data-aos="fade-up" data-aos-delay="300">
            <h4>Newsletter</h4>
            <form action="#" class="newsletter-form">
                <input type="email" placeholder="Your Email Address" required>
                <button type="submit" class="btn btn-primary">Subscribe</button>
            </form>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Haraja Resort. All rights reserved. Made by <a>ryu</a></p>
    </div>
</footer>

<!-- AOS JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<!-- Custom JS -->
<script src="js/main.js"></script>
<script src="js/mobile-menu.js"></script>
<!-- made by ryu do not copy -->
<!-- contact ryujosephbalanay@gmail for more info -->
</body>

</html>
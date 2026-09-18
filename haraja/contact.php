<?php include 'includes/header.php'; ?>

<!-- Page-Specific CSS -->
<style>
    /* Page Banner */
    .page-banner {
        position: relative;
        height: 60vh;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        background-image: url('https://images.unsplash.com/photo-1561501878-aabd62634533?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }

    .banner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
    }

    .banner-content {
        position: relative;
        z-index: 2;
        color: #fff;
        padding-top: 60px;
    }

    /* Contact Info Grid */
    .contact-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-top: -80px;
        /* Overlaps the banner slightly */
        position: relative;
        z-index: 10;
        margin-bottom: 4rem;
    }

    .info-card {
        background: var(--bg-color);
        padding: 3rem 2rem;
        text-align: center;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-md);
        border-top: 4px solid var(--primary-gold);
        transition: transform var(--transition-smooth);
    }

    .info-card:hover {
        transform: translateY(-5px);
    }

    .info-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .info-title {
        font-family: var(--font-heading);
        font-size: 1.2rem;
        margin-bottom: 1rem;
        color: var(--heading-color);
    }

    .info-text {
        font-size: 0.95rem;
        color: var(--text-color);
        opacity: 0.9;
        line-height: 1.6;
    }

    .info-text a {
        color: var(--primary-gold);
        text-decoration: none;
        transition: var(--transition-smooth);
    }

    .info-text a:hover {
        opacity: 0.7;
    }

    /* Contact Layout (Form + Map) */
    .contact-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: stretch;
    }

    /* Form Styles */
    .contact-form-container {
        background: var(--bg-color);
        padding: 3rem;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        color: var(--heading-color);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-color);
        font-family: var(--font-body);
        font-size: 1rem;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-gold);
        box-shadow: 0 0 0 1px var(--primary-gold);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 150px;
    }

    /* Map Styles */
    .map-container {
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        height: 100%;
        min-height: 400px;
    }

    .map-container iframe {
        width: 100%;
        height: 100%;
        border: 0;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .contact-wrapper {
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .contact-form-container {
            padding: 2rem;
        }

        .map-container {
            height: 400px;
        }
    }
</style>

<!-- Page Banner -->
<section class="page-banner">
    <div class="banner-overlay"></div>
    <div class="banner-content container">
        <h1 class="section-title" data-aos="fade-up" style="color: #fff;">Contact Us</h1>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100" style="color: var(--primary-gold);">We are here to assist you</p>
    </div>
</section>

<!-- Contact Info & Form Section -->
<section class="section-padding" style="padding-top: 0;">
    <div class="container">

        <!-- Info Cards -->
        <div class="contact-info-grid" data-aos="fade-up">
            <div class="info-card">
                <div class="info-icon">📍</div>
                <h3 class="info-title">Our Location</h3>
                <p class="info-text">
                    Haraja Resort & Spa<br>
                    Beachfront Road, Tropical Estate<br>
                    El Nido, Palawan, Philippines
                </p>
            </div>

            <div class="info-card" data-aos="fade-up" data-aos-delay="100">
                <div class="info-icon">📞</div>
                <h3 class="info-title">Phone Numbers</h3>
                <p class="info-text">
                    Front Desk: <a href="tel:+63212345678">+63 (2) 1234 5678</a><br>
                    Reservations: <a href="tel:+639171234567">+63 917 123 4567</a><br>
                    Events: <a href="tel:+639181234567">+63 918 123 4567</a>
                </p>
            </div>

            <div class="info-card" data-aos="fade-up" data-aos-delay="200">
                <div class="info-icon">✉️</div>
                <h3 class="info-title">Email Us</h3>
                <p class="info-text">
                    Inquiries: <a href="mailto:info@Haraja.com">info@Haraja.com</a><br>
                    Bookings: <a href="mailto:reservations@Haraja.com">reservations@Haraja.com</a><br>
                    Careers: <a href="mailto:hr@Haraja.com">hr@Haraja.com</a>
                </p>
            </div>
        </div>

        <!-- Form & Map -->
        <div class="contact-wrapper">

            <!-- Contact Form -->
            <div class="contact-form-container" data-aos="fade-right">
                <h2 style="font-family: var(--font-heading); font-size: 2rem; margin-bottom: 0.5rem;">Send a Message</h2>
                <p style="font-size: 0.95rem; opacity: 0.8; margin-bottom: 2rem;">Have a question about an upcoming stay, a day pass, or an event? Fill out the form below and our concierge team will get back to you shortly.</p>

                <form action="process_contact.php" method="POST">
                    <div class="form-group">
                        <label for="name" class="form-label">Full Name *</label>
                        <input type="text" id="name" name="name" class="form-control" required placeholder="John Doe">
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-control" required placeholder="john@example.com">
                    </div>

                    <div class="form-group">
                        <label for="subject" class="form-label">Subject</label>
                        <select id="subject" name="subject" class="form-control">
                            <option value="General Inquiry">General Inquiry</option>
                            <option value="Room Reservation">Room Reservation</option>
                            <option value="Event Planning">Event Planning</option>
                            <option value="Day Pass Inquiry">Day Pass Inquiry</option>
                            <option value="Feedback">Feedback / Suggestions</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message" class="form-label">Your Message *</label>
                        <textarea id="message" name="message" class="form-control" required placeholder="How can we help you today?"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 1rem;">Send Message</button>
                </form>
            </div>

            <!-- Google Map -->
            <div class="map-container" data-aos="fade-left">
                <!-- Replace the src with your actual resort Google Maps embed link -->
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d125345.02102196656!2d119.35174416390885!3d11.17983637402636!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33b6d2740798e27f%3A0xb69db1451fbf09d7!2sEl%20Nido%2C%20Palawan!5e0!3m2!1sen!2sph!4v1700000000000!5m2!1sen!2sph" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
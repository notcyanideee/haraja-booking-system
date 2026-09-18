<?php include 'includes/header.php'; ?>

<!-- Page-Specific CSS -->
<style>
    :root {
        --footer-bg: #FFFFFF;
    }

    [data-theme="dark"] {
        --bg-color: #121212;
        --secondary-dark: #FFFFFF;
        --text-color: #E0E0E0;
        --accent-gray: #1E1E1E;
        --nav-bg: rgba(18, 18, 18, 0.98);
        --border-color: #333333;
        --card-bg: #2B2B2B;
        --footer-bg: #1a1a1a;
    }

    /* Page Banner */
    .page-banner {
        position: relative;
        height: 60vh;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        background-image: url('https://images.unsplash.com/photo-1572331165267-854da2e10ccc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
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
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0.3) 100%);
    }

    .banner-content {
        position: relative;
        z-index: 2;
        color: #fff;
        padding-top: 60px;
    }

    /* Pass Cards Layout */
    .pass-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2.5rem;
        align-items: center;
        /* Vertically center so the featured card can be taller */
        margin-top: 2rem;
    }

    .pass-card {
        background: var(--bg-color);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        padding: 3rem 2rem;
        text-align: center;
        transition: var(--transition-smooth);
        box-shadow: var(--shadow-sm);
        position: relative;
        overflow: hidden;
    }

    .pass-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-md);
    }

    /* Featured (Full Pass) Styling */
    .pass-card.featured {
        border: 2px solid var(--primary-gold);
        padding: 4rem 2rem;
        /* Taller than the others */
        box-shadow: var(--shadow-gold);
    }

    .featured-badge {
        position: absolute;
        top: 20px;
        right: -35px;
        background: var(--primary-gold);
        color: #fff;
        padding: 5px 40px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        transform: rotate(45deg);
        font-weight: 600;
    }

    /* Pass Typography & Elements */
    .pass-title {
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
    }

    .pass-hours {
        color: var(--primary-gold);
        font-weight: 500;
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
        letter-spacing: 1px;
    }

    .pass-price {
        font-family: var(--font-heading);
        font-size: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pass-price span {
        font-family: var(--font-body);
        font-size: 0.9rem;
        color: var(--text-color);
        margin-left: 8px;
        font-weight: 400;
    }

    .pass-inclusions {
        list-style: none;
        margin-bottom: 2.5rem;
        text-align: left;
    }

    .pass-inclusions li {
        margin-bottom: 1rem;
        font-size: 0.95rem;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        color: var(--text-color);
        opacity: 0.9;
    }

    .pass-inclusions li::before {
        content: '✓';
        color: var(--primary-gold);
        font-weight: bold;
        font-size: 1.1rem;
    }

    /* Guidelines Section */
    .guidelines-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        text-align: left;
        margin-top: 3rem;
    }

    .guideline-item {
        background: var(--bg-color);
        padding: 1.5rem;
        border-left: 3px solid var(--primary-gold);
        box-shadow: var(--shadow-sm);
    }

    @media (max-width: 768px) {
        .guidelines-grid {
            grid-template-columns: 1fr;
        }

        .pass-card.featured {
            padding: 3rem 2rem;
            /* Reset padding on mobile */
        }
    }
</style>

<!-- Page Banner -->
<section class="page-banner">
    <div class="banner-overlay"></div>
    <div class="banner-content container">
        <h1 class="section-title" data-aos="fade-up" style="color: #fff;">Day Passes</h1>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100" style="color: var(--primary-gold);">Experience Haraja for the Day</p>
    </div>
</section>

<!-- Passes Section -->
<section class="section-padding">
    <div class="container">
        <div class="text-center" style="margin-bottom: 3rem;">
            <h2 class="section-title" data-aos="fade-up">Choose Your Experience</h2>
            <p style="max-width: 600px; margin: 0 auto; opacity: 0.8;" data-aos="fade-up" data-aos-delay="100">
                Not staying overnight? You can still immerse yourself in our tropical oasis. Select a day pass package that best fits your schedule and relax in absolute luxury.
            </p>
        </div>

        <div class="pass-grid">

            <!-- Day Pass -->
            <div class="pass-card" data-aos="fade-up">
                <h3 class="pass-title">Day Pass</h3>
                <div class="pass-hours">8:00 AM - 5:00 PM</div>
                <div class="pass-price">₱ 1,500 <span>/ person</span></div>

                <ul class="pass-inclusions">
                    <li>Access to the main Infinity Pool and Lagoon</li>
                    <li>Complimentary towel service</li>
                    <li>Free use of shower and locker facilities</li>
                    <li>Welcome tropical fruit drink</li>
                    <li>Access to sun loungers (subject to availability)</li>
                </ul>

                <a href="pass.php?type=daypass&tier=day" class="btn btn-outline" style="width: 100%;">Reserve Day Pass</a>
            </div>

            <!-- Full Pass (Featured) -->
            <div class="pass-card featured" data-aos="fade-up" data-aos-delay="100">
                <div class="featured-badge">Best Value</div>
                <h3 class="pass-title">Full Pass</h3>
                <div class="pass-hours">8:00 AM - 10:00 PM</div>
                <div class="pass-price">₱ 3,000 <span>/ person</span></div>

                <ul class="pass-inclusions">
                    <li><strong>₱ 800 Consumable</strong> for Food & Beverage</li>
                    <li>All-day access to all swimming pools</li>
                    <li>Guaranteed premium sun lounger reservation</li>
                    <li>Complimentary towel and locker service</li>
                    <li>Welcome cocktail or mocktail</li>
                    <li>Access to evening live acoustic sessions</li>
                </ul>

                <a href="pass.php?type=daypass&tier=full" class="btn btn-primary" style="width: 100%;">Reserve Full Pass</a>
            </div>

            <!-- Night Pass -->
            <div class="pass-card" data-aos="fade-up" data-aos-delay="200">
                <h3 class="pass-title">Night Pass</h3>
                <div class="pass-hours">4:00 PM - 10:00 PM</div>
                <div class="pass-price">₱ 1,800 <span>/ person</span></div>

                <ul class="pass-inclusions">
                    <li>Access to illuminated heated pools</li>
                    <li>Complimentary towel service</li>
                    <li>Free use of shower and locker facilities</li>
                    <li>One (1) complimentary Sunset Cocktail</li>
                    <li>Access to evening live acoustic sessions</li>
                </ul>

                <a href="pass.php?type=daypass&tier=night" class="btn btn-outline" style="width: 100%;">Reserve Night Pass</a>
            </div>

        </div>
    </div>
</section>

<!-- Guidelines & Information Section -->
<section class="section-padding" style="background-color: var(--accent-gray);">
    <div class="container">
        <div class="text-center" style="margin-bottom: 3rem;">
            <h2 class="section-title" data-aos="fade-up">Guest Guidelines</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Important Information for Day Visitors</p>
        </div>

        <div class="guidelines-grid">
            <div class="guideline-item" data-aos="fade-up">
                <h4 style="margin-bottom: 0.5rem; color: var(--heading-color);">Outside Food & Drinks</h4>
                <p style="font-size: 0.9rem; opacity: 0.8;">Strictly no outside food or beverages are allowed inside the resort premises. We have two world-class restaurants and a poolside bar ready to serve you.</p>
            </div>

            <div class="guideline-item" data-aos="fade-up" data-aos-delay="100">
                <h4 style="margin-bottom: 0.5rem; color: var(--heading-color);">Proper Swimwear</h4>
                <p style="font-size: 0.9rem; opacity: 0.8;">For sanitation and safety, only proper swimming attire (swimsuits, trunks, rash guards) is permitted in the pools. Cotton shirts and denim are not allowed.</p>
            </div>

            <div class="guideline-item" data-aos="fade-up" data-aos-delay="200">
                <h4 style="margin-bottom: 0.5rem; color: var(--heading-color);">Cabanas & Gazebos</h4>
                <p style="font-size: 0.9rem; opacity: 0.8;">Day passes do not include private cabanas or gazebos unless explicitly stated. These can be rented separately upon arrival, subject to availability.</p>
            </div>

            <div class="guideline-item" data-aos="fade-up" data-aos-delay="300">
                <h4 style="margin-bottom: 0.5rem; color: var(--heading-color);">Reservations Required</h4>
                <p style="font-size: 0.9rem; opacity: 0.8;">To maintain an uncrowded and relaxing atmosphere, walk-ins are highly discouraged. Please book your day passes online in advance.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
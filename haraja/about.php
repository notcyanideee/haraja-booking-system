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
        background-image: url('https://images.unsplash.com/photo-1540541338287-41700207dee6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
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
        /* Offset for transparent navbar */
    }

    /* Split Section (Our Story) */
    .split-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
    }

    .story-image-wrapper {
        position: relative;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--shadow-md);
    }

    .story-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 1s ease;
    }

    .story-image-wrapper:hover img {
        transform: scale(1.05);
    }

    .gold-accent-box {
        position: absolute;
        bottom: -20px;
        right: -20px;
        width: 150px;
        height: 150px;
        background-color: var(--primary-gold);
        z-index: -1;
        border-radius: var(--border-radius);
    }

    /* Mission & Vision */
    .mv-section {
        background-color: var(--accent-gray);
    }

    .mv-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
    }

    .mv-card {
        background: var(--bg-color);
        padding: 3rem;
        border-radius: var(--border-radius);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        transition: var(--transition-smooth);
    }

    .mv-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-gold);
    }

    /* Timeline */
    .timeline {
        position: relative;
        max-width: 800px;
        margin: 0 auto;
    }

    .timeline::before {
        content: '';
        position: absolute;
        width: 2px;
        background-color: var(--primary-gold);
        top: 0;
        bottom: 0;
        left: 50%;
        margin-left: -1px;
    }

    .timeline-item {
        padding: 10px 40px;
        position: relative;
        background-color: inherit;
        width: 50%;
    }

    .timeline-item::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        right: -10px;
        background-color: var(--bg-color);
        border: 4px solid var(--primary-gold);
        top: 15px;
        border-radius: 50%;
        z-index: 1;
    }

    .left {
        left: 0;
        text-align: right;
    }

    .right {
        left: 50%;
    }

    .right::after {
        left: -10px;
    }

    .timeline-content {
        padding: 20px 30px;
        background-color: var(--accent-gray);
        position: relative;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
    }

    /* Team Section */
    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2.5rem;
    }

    .team-card {
        text-align: center;
    }

    .team-img {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        margin: 0 auto 1.5rem;
        overflow: hidden;
        border: 4px solid var(--accent-gray);
        transition: var(--transition-smooth);
    }

    .team-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: grayscale(20%);
        transition: var(--transition-smooth);
    }

    .team-card:hover .team-img {
        border-color: var(--primary-gold);
        box-shadow: var(--shadow-gold);
    }

    .team-card:hover .team-img img {
        filter: grayscale(0%);
        transform: scale(1.1);
    }

    /* Responsive Overrides */
    @media (max-width: 768px) {

        .split-grid,
        .mv-grid {
            grid-template-columns: 1fr;
        }

        .timeline::before {
            left: 31px;
        }

        .timeline-item {
            width: 100%;
            padding-left: 70px;
            padding-right: 25px;
            text-align: left;
        }

        .timeline-item::after {
            left: 21px;
        }

        .right {
            left: 0%;
        }
    }

    /* CTA Section Theme Adaptability */
    .cta-section {
        background: var(--card-bg);
        /* Adapts to your theme's card/container color */
        color: var(--text-color);
        border-top: 1px solid var(--border-color);
        /* border-bottom: 1px solid var(--border-color); */
    }

    .cta-section .section-title,
    .cta-section p {
        color: var(--text-color) !important;
    }

    /* Ensure paragraph has a nice muted look in both modes */
    .cta-section p {
        opacity: 0.85;
    }
</style>

<!-- Page Banner -->
<section class="page-banner">
    <div class="banner-overlay"></div>
    <div class="banner-content container">
        <h1 class="section-title" data-aos="fade-up" style="color: #fff;">Our Story</h1>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100" style="color: var(--primary-gold);">Discover the Heart of Haraja</p>
    </div>
</section>

<!-- Our Story Section -->
<section class="section-padding">
    <div class="container split-grid">
        <div data-aos="fade-right">
            <h2 class="section-title">A Legacy of Luxury</h2>
            <p class="section-subtitle">Established 2012</p>
            <p style="margin-bottom: 1.5rem;">
                Nestled on the pristine coastlines of the tropics, Haraja Resort began as a private family estate before blossoming into the world-class sanctuary it is today. Our founders envisioned a place where untouched nature and modern elegance could coexist in perfect harmony.
            </p>
            <p style="margin-bottom: 2rem;">
                Every stone laid, every garden planted, and every villa designed was done with a singular purpose: to provide our guests with an escape that feels both extraordinarily lavish and intimately like home. We believe in crafting moments that transcend the ordinary.
            </p>
            <a href="gallery.php" class="btn btn-primary">View Gallery</a>
        </div>
        <div class="story-image-wrapper" data-aos="fade-left">
            <div class="gold-accent-box"></div>
            <img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Resort Architecture">
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="section-padding mv-section">
    <div class="container">
        <div class="mv-grid">
            <div class="mv-card" data-aos="fade-up">
                <h3 style="color: var(--primary-gold); margin-bottom: 1rem; font-size: 2rem;">Our Mission</h3>
                <p>To provide an unparalleled sanctuary where impeccable service, luxurious amenities, and the breathtaking beauty of nature unite. We strive to anticipate every need, ensuring that every guest leaves with memories to cherish for a lifetime.</p>
            </div>
            <div class="mv-card" data-aos="fade-up" data-aos-delay="200">
                <h3 style="color: var(--primary-gold); margin-bottom: 1rem; font-size: 2rem;">Our Vision</h3>
                <p>To be recognized globally as the premier tropical destination, setting the gold standard in sustainable luxury hospitality. We aim to continuously innovate our guest experience while preserving the natural environment that surrounds us.</p>
            </div>
        </div>
    </div>
</section>

<!-- Timeline Section -->
<section class="section-padding">
    <div class="container">
        <div class="text-center" style="margin-bottom: 4rem;">
            <h2 class="section-title" data-aos="fade-up">Our Journey</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Milestones of Excellence</p>
        </div>

        <div class="timeline">
            <div class="timeline-item left" data-aos="fade-right">
                <div class="timeline-content">
                    <h4 style="color: var(--primary-gold);">2012</h4>
                    <h3>The Foundation</h3>
                    <p>Construction begins on the original 10-acre property, carefully designed to protect local flora.</p>
                </div>
            </div>
            <div class="timeline-item right" data-aos="fade-left">
                <div class="timeline-content">
                    <h4 style="color: var(--primary-gold);">2015</h4>
                    <h3>Grand Opening</h3>
                    <p>Haraja Resort officially opens its doors, featuring 50 luxury suites and our signature infinity pool.</p>
                </div>
            </div>
            <div class="timeline-item left" data-aos="fade-right">
                <div class="timeline-content">
                    <h4 style="color: var(--primary-gold);">2019</h4>
                    <h3>The Grand Pavilion</h3>
                    <p>Expansion of our event venues, introducing the Grand Pavilion for world-class weddings and galas.</p>
                </div>
            </div>
            <div class="timeline-item right" data-aos="fade-left">
                <div class="timeline-content">
                    <h4 style="color: var(--primary-gold);">2023</h4>
                    <h3>Global Excellence Award</h3>
                    <p>Recognized by International Travel Guide as the "Top Tropical Luxury Resort" of the year.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="section-padding" style="background-color: var(--accent-gray);">
    <div class="container">
        <div class="text-center" style="margin-bottom: 4rem;">
            <h2 class="section-title" data-aos="fade-up">Meet The Experts</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">The People Behind the Magic</p>
        </div>

        <div class="team-grid">
            <div class="team-card" data-aos="zoom-in" data-aos-delay="0">
                <div class="team-img">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="General Manager">
                </div>
                <h4>Arthur Pendelton</h4>
                <p style="color: var(--primary-gold); font-size: 0.9rem;">General Manager</p>
            </div>
            <div class="team-card" data-aos="zoom-in" data-aos-delay="150">
                <div class="team-img">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Executive Chef">
                </div>
                <h4>Elena Rostova</h4>
                <p style="color: var(--primary-gold); font-size: 0.9rem;">Executive Chef</p>
            </div>
            <div class="team-card" data-aos="zoom-in" data-aos-delay="300">
                <div class="team-img">
                    <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Head of Guest Relations">
                </div>
                <h4>Marcus Chen</h4>
                <p style="color: var(--primary-gold); font-size: 0.9rem;">Head of Guest Relations</p>
            </div>
            <div class="team-card" data-aos="zoom-in" data-aos-delay="450">
                <div class="team-img">
                    <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Events Director">
                </div>
                <h4>Sophia Laurent</h4>
                <p style="color: var(--primary-gold); font-size: 0.9rem;">Events Director</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="section-padding text-center cta-section">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Ready to Experience Haraja?</h2>
        <p data-aos="fade-up" data-aos-delay="100" style="margin: 1.5rem auto 3rem; max-width: 600px;">
            Join the thousands of guests who have made Haraja Resort their definitive getaway destination. Your private paradise awaits.
        </p>
        <div data-aos="fade-up" data-aos-delay="200">
            <a href="rooms.php" class="btn btn-outline" style="margin-right: 15px;">Explore Rooms</a>
            <a href="checkout.php" class="btn btn-primary">Book Your Stay</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
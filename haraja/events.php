<?php include 'includes/header.php'; ?>

<!-- Page-Specific CSS -->
<style>
    :root {
        --footer-bg: #FFFFFF;
        --card-bg: #F5F5F5;
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
        background-image: url('https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
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
        background: rgba(0, 0, 0, 0.4);
    }

    .banner-content {
        position: relative;
        z-index: 2;
        color: #fff;
        padding-top: 60px;
    }

    /* Event Zigzag Layout */
    .event-row {
        display: flex;
        align-items: center;
        gap: 4rem;
        margin-bottom: 6rem;
    }

    /* Alternate the flex direction for every even row */
    .event-row:nth-child(even) {
        flex-direction: row-reverse;
    }

    .event-image {
        flex: 1;
        position: relative;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        height: 450px;
    }

    .event-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 1.2s ease;
    }

    .event-image:hover img {
        transform: scale(1.05);
    }

    .event-content {
        flex: 1;
        padding: 2rem 0;
    }

    .event-title {
        font-size: 2.2rem;
        margin-bottom: 0.5rem;
    }

    .event-meta {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        font-family: var(--font-heading);
        font-size: 1.1rem;
        color: var(--primary-gold);
    }

    .event-meta span {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .event-meta span::before {
        content: '';
        display: block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: var(--primary-gold);
    }

    .event-meta span:first-child::before {
        display: none;
    }

    .event-description {
        margin-bottom: 2rem;
        font-size: 0.95rem;
        color: var(--text-color);
        opacity: 0.9;
        line-height: 1.8;
    }

    /* Responsive Overrides */
    @media (max-width: 991px) {

        .event-row,
        .event-row:nth-child(even) {
            flex-direction: column;
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .event-image {
            width: 100%;
            height: 350px;
        }

        .event-content {
            padding: 0 1rem;
            text-align: center;
        }

        .event-meta {
            justify-content: center;
        }
    }

    /* CTA Section Theme Adaptability */
    .cta-section {
        background: var(--card-bg);
        /* Adapts to your theme's card/container color */
        color: var(--text-color);
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
        <h1 class="section-title" data-aos="fade-up" style="color: #fff;">Weddings & Events</h1>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100" style="color: var(--primary-gold);">Celebrate Life's Greatest Moments</p>
    </div>
</section>

<!-- Introduction -->
<section class="section-padding" style="padding-bottom: 40px;">
    <div class="container text-center">
        <h2 class="section-title" data-aos="fade-up">Venues & Packages</h2>
        <p style="max-width: 700px; margin: 0 auto; opacity: 0.8;" data-aos="fade-up" data-aos-delay="100">
            From fairytale weddings to high-level corporate retreats, Haraja Resort offers a variety of stunning venues. Our dedicated events team will ensure every detail of your celebration is flawlessly executed.
        </p>
    </div>
</section>

<!-- Event Listings -->
<section class="section-padding" style="padding-top: 40px;">
    <div class="container">

        <!-- Wedding Venue -->
        <div class="event-row">
            <div class="event-image" data-aos="fade-right">
                <img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Wedding Venue">
            </div>
            <div class="event-content" data-aos="fade-left">
                <h3 class="event-title">The Grand Pavilion</h3>
                <div class="event-meta">
                    <span>Wedding Venue</span>
                    <span>Up to 300 Guests</span>
                </div>
                <p class="event-description">
                    Exchange vows with the ocean as your backdrop. Our Grand Pavilion features floor-to-ceiling glass doors, crystal chandeliers, and a private beachfront terrace. Our all-inclusive wedding packages cover catering, floral arrangements, and a complimentary overnight suite for the newlyweds.
                </p>
                <a href="checkout.php?type=event&package=wedding" class="btn btn-primary">Reserve Venue</a>
            </div>
        </div>

        <!-- Corporate Events -->
        <div class="event-row">
            <div class="event-image" data-aos="fade-left">
                <img src="https://images.unsplash.com/photo-1505373877841-8d25f7d46678?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Corporate Event">
            </div>
            <div class="event-content" data-aos="fade-right">
                <h3 class="event-title">Haraja Executive Hall</h3>
                <div class="event-meta">
                    <span>Corporate Events</span>
                    <span>Up to 150 Guests</span>
                </div>
                <p class="event-description">
                    Elevate your next conference, seminar, or team-building retreat. The Executive Hall is equipped with state-of-the-art audiovisual technology, high-speed Wi-Fi, and modular seating. Packages include gourmet coffee breaks, a buffet lunch, and dedicated event coordinators.
                </p>
                <a href="checkout.php?type=event&package=corporate" class="btn btn-outline">Reserve Venue</a>
            </div>
        </div>

        <!-- Pool Party -->
        <div class="event-row">
            <div class="event-image" data-aos="fade-right">
                <img src="https://images.unsplash.com/photo-1563299723-8bc653a948e9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Pool Party">
            </div>
            <div class="event-content" data-aos="fade-left">
                <h3 class="event-title">Oasis Poolside Exclusive</h3>
                <div class="event-meta">
                    <span>Pool Party</span>
                    <span>Up to 100 Guests</span>
                </div>
                <p class="event-description">
                    Host the ultimate tropical bash. Rent our exclusive Oasis Lagoon for an unforgettable private pool party. Complete with a private bar, live DJ setup area, floating cabanas, and customizable tapas and cocktail menus to keep your guests refreshed under the sun or stars.
                </p>
                <a href="checkout.php?type=event&package=poolparty" class="btn btn-primary">Reserve Venue</a>
            </div>
        </div>

        <!-- Birthday Package -->
        <div class="event-row">
            <div class="event-image" data-aos="fade-left">
                <img src="https://images.unsplash.com/photo-1530103862676-de88b7d41fdb?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Birthday Package">
            </div>
            <div class="event-content" data-aos="fade-right">
                <h3 class="event-title">Tropical Fiesta</h3>
                <div class="event-meta">
                    <span>Birthday Package</span>
                    <span>Up to 50 Guests</span>
                </div>
                <p class="event-description">
                    Celebrate another year in paradise. Our birthday packages are tailored for all ages—from vibrant kids' parties by the shallow pools to sophisticated milestone celebrations with private dining. Includes customized cakes, themed decor, and a personal event host.
                </p>
                <a href="checkout.php?type=event&package=birthday" class="btn btn-outline">Reserve Venue</a>
            </div>
        </div>

        <!-- Private Celebrations -->
        <div class="event-row">
            <div class="event-image" data-aos="fade-right">
                <img src="https://images.unsplash.com/photo-1543007630-9710e4a00a20?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Private Celebration">
            </div>
            <div class="event-content" data-aos="fade-left">
                <h3 class="event-title">Intimate Garden Gazebo</h3>
                <div class="event-meta">
                    <span>Private Celebrations</span>
                    <span>10 - 20 Guests</span>
                </div>
                <p class="event-description">
                    Perfect for anniversaries, exclusive family dinners, or intimate engagements. Tucked away in our lush botanical gardens, this private gazebo offers a serene atmosphere. Enjoy a bespoke 5-course tasting menu prepared by our Executive Chef, paired with premium wines.
                </p>
                <a href="checkout.php?type=event&package=private" class="btn btn-primary">Reserve Venue</a>
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
<?php
// Include your database connection at the very top
require_once 'db.php';
include 'includes/header.php';
?>

<style>
    /* Hero Section */
    .hero-section {
        position: relative;
        height: 100vh;
        min-height: 600px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .hero-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        z-index: 0;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        /* Keeps text readable in light/dark mode */
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        color: #ffffff;
        /* Always white on the dark image overlay */
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 60px;
    }

    /* Featured Cards */
    .featured-card {
        background: var(--bg-color);
        /* Adapts to dark mode */
        border-radius: var(--border-radius, 8px);
        overflow: hidden;
        box-shadow: var(--shadow-md, 0 4px 6px rgba(0, 0, 0, 0.1));
        border: 1px solid var(--border-color);
        transition: transform 0.4s ease;
    }

    .featured-card:hover {
        transform: translateY(-10px);
    }

    .featured-card img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        transition: transform 0.8s ease;
    }

    .featured-card:hover img {
        transform: scale(1.05);
    }

    .card-body {
        padding: 2rem;
        text-align: left;
    }

    .card-body h3 {
        color: var(--heading-color, var(--text-color));
        margin-bottom: 0.5rem;
    }

    .card-body p {
        color: var(--text-color);
        opacity: 0.8;
        font-size: 0.95rem;
    }
</style>

<main>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <h3 data-aos="fade-up" style="color: var(--primary-gold); letter-spacing: 3px; margin-bottom: 1rem; text-transform: uppercase; font-size: 3rem;">Welcome to Harajā</h3>
            <h1 data-aos="fade-up" data-aos-delay="100" style="font-size: clamp(3rem, 5vw, 5rem); margin-bottom: 1.5rem; font-family: var(--font-heading); color: #fff;">Relax. Swim. Celebrate.</h1>
            <p data-aos="fade-up" data-aos-delay="200" style="font-size: 1.2rem; font-weight: 300; margin-bottom: 3rem; max-width: 600px; color: #fff;">Where luxury meets nature. Book your unforgettable sanctuary today.</p>

            <div data-aos="fade-up" data-aos-delay="300" style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="checkout.php" class="btn btn-primary">Book Your Stay</a>
                <a href="about.php" class="btn btn-outline" style="color: #fff; border-color: #fff;">Our Story</a>
            </div>
        </div>
    </section>

    <!-- Brief Intro Section -->
    <section class="section-padding text-center">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">A Sanctuary of Serenity</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Experience the Extraordinary</p>
            <p style="max-width: 700px; margin: 0 auto; color: var(--text-color); opacity: 0.8;" data-aos="fade-up" data-aos-delay="200">
                Nestled in the heart of lush tropical landscapes, Harajā Resort offers an unparalleled retreat. Whether you are seeking a serene overnight stay, a vibrant day at our exclusive pools, or the perfect venue for your grand celebration, we curate experiences that linger in your memory forever.
            </p>
        </div>
    </section>

    <!-- Featured Rooms Teaser (DYNAMIC FROM DB) -->
    <section class="section-padding" style="background: var(--accent-gray); border-top: 1px solid var(--border-color);">
        <div class="container">
            <div class="text-center" style="margin-bottom: 3rem;">
                <h2 class="section-title" data-aos="fade-up">Luxury Accommodations</h2>
                <a href="rooms.php" data-aos="fade-up" data-aos-delay="100" style="color: var(--primary-gold); text-decoration: none; font-weight: 500;">View All Rooms &rarr;</a>
            </div>

            <!-- The Grid - Auto adjusts based on how many rooms exist -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2.5rem;">

                <?php
                // Fetch the top 3 active rooms from the database to feature on the homepage
                $sql = "SELECT id, name, capacity, base_price FROM rooms WHERE is_active = 1 ORDER BY id DESC LIMIT 3";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    $delay = 0; // Dynamic delay for the AOS scroll animation

                    while ($row = $result->fetch_assoc()) {
                        // Secure variables
                        $room_id = $row['id'];
                        $room_name = htmlspecialchars($row['name']);
                        $room_capacity = htmlspecialchars($row['capacity']);
                        $room_price = number_format($row['base_price'], 2);

                        // Default fallback image since image_url was removed from DB
                        $fallback_image = "https://images.unsplash.com/photo-1631049307264-da0ec9d70304?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80";

                        // HTML output matching your exact CSS structure
                        echo '
                        <div class="featured-card" data-aos="fade-up" data-aos-delay="' . $delay . '">
                            <img src="' . $fallback_image . '" alt="' . $room_name . '" loading="lazy">
                            <div class="card-body">
                                <h3 style="color: var(--text-color)">' . $room_name . '</h3>
                                <p style="margin-bottom: 1.5rem;">Capacity: ' . $room_capacity . '</p>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-family: var(--font-heading); font-size: 1.2rem; color: var(--primary-gold);">₱ ' . $room_price . ' / night</span>
                                    
                                    <a href="checkout.php?room_id=' . $room_id . '" class="btn btn-primary" style="padding: 8px 20px;">Book</a>
                                </div>
                            </div>
                        </div>';

                        $delay += 100; // Increment delay so cards animate in one by one (0, 100, 200...)
                    }
                } else {
                    echo '<p style="grid-column: 1 / -1; text-align: center;">No rooms are currently available. Please check back later.</p>';
                }
                ?>

            </div>
        </div>
    </section>

    <!-- Quick Links / CTA Section -->
    <section class="section-padding text-center" style="border-top: 1px solid var(--border-color);">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">More Than Just a Stay</h2>
            <p data-aos="fade-up" data-aos-delay="100" style="margin: 1.5rem auto 3rem; max-width: 600px; color: var(--text-color); opacity: 0.8;">
                Discover our world-class dining, book a day pass for our exclusive infinity pools, or start planning your dream wedding at Harajā.
            </p>
            <div data-aos="fade-up" data-aos-delay="200" style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="daypass.php" class="btn btn-outline">Day Passes</a>
                <a href="events.php" class="btn btn-outline">Weddings & Events</a>
                <a href="gallery.php" class="btn btn-primary">View Gallery</a>
            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>
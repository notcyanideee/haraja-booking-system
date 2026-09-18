<?php
include 'includes/header.php';
require_once 'db.php'; // Ensure your database connection file path is correct

// Fetch all available rooms from the database
$sql = "SELECT * FROM rooms";
$result = mysqli_query($conn, $sql);
?>

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
        background-image: url('https://images.unsplash.com/photo-1611892440504-42a792e24d32?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
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
        /* Offset for transparent navbar */
    }

    .amenities-list {
        margin: 1rem 0;
        padding: 0;
        list-style: none;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }

    .amenities-list li {
        font-size: 0.85rem;
        color: var(--text-color);
        display: flex;
        align-items: center;
        gap: 8px;
        opacity: 0.9;
    }

    .amenities-list li::before {
        content: '✦';
        color: var(--primary-gold);
        font-size: 0.7rem;
    }
</style>

<!-- Page Banner -->
<section class="page-banner">
    <div class="banner-overlay"></div>
    <div class="banner-content container">
        <h1 class="section-title" data-aos="fade-up" style="color: #fff;">Accommodations</h1>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100" style="color: var(--primary-gold);">Your Private Sanctuary</p>
    </div>
</section>

<!-- Rooms Section -->
<section class="section-padding">
    <div class="container">
        <div class="grid-cards">

            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php
                $delay = 0;
                while ($room = mysqli_fetch_assoc($result)):
                    // Support common variations in database column naming
                    $id = $room['id'] ?? $room['room_id'] ?? '';
                    $name = $room['name'] ?? 'Luxury Accommodation';
                    $description = $room['description'] ?? 'Experience ultimate luxury and comfort in our finely crafted rooms.';
                    $max_guests = $room['max_guests'] ?? $room['capacity'] ?? '2';
                    $size = $room['room_size'] ?? $room['size'] ?? '50';
                    $price = $room['base_price'] ?? $room['price'] ?? 0;
                    $image = $room['image_url'] ?? $room['image'] ?? 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=800&q=80';
                ?>
                    <div class="luxury-card" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                        <div class="card-img-wrapper">
                            <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($name) ?>">
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?= htmlspecialchars($name) ?></h3>
                            <p class="card-meta">Up to <?= htmlspecialchars($max_guests) ?> Guests • <?= htmlspecialchars($size) ?> sqm</p>
                            <p style="font-size: 0.9rem; opacity: 0.8;"><?= htmlspecialchars($description) ?></p>

                            <div class="card-footer">
                                <span class="card-price">₱ <?= number_format((float)$price, 0) ?> <span style="font-size: 0.8rem; color: var(--text-color); font-weight: 300;">/ night</span></span>
                                <a href="checkout.php?room_id=<?= urlencode($id) ?>" class="btn btn-outline" style="padding: 8px 20px;">Book</a>
                            </div>
                        </div>
                    </div>
                <?php
                    $delay = ($delay + 100) % 300; // Stagger AOS animation up to 200ms
                endwhile;
                ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem;">
                    <h3 style="color: var(--text-color); font-weight: 400;">No accommodations are currently available.</h3>
                    <p style="opacity: 0.7; margin-top: 0.5rem;">Please check back later or contact customer service for assistance.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- Include & Information Section -->
<section class="section-padding" style="background-color: var(--accent-gray);">
    <div class="container text-center">
        <h2 class="section-title" data-aos="fade-up">All Stays Include</h2>
        <div style="display: flex; justify-content: center; gap: 3rem; margin-top: 3rem; flex-wrap: wrap;" data-aos="fade-up" data-aos-delay="100">
            <div>
                <h4 style="color: var(--primary-gold); margin-bottom: 0.5rem; font-size: 1.5rem;">🍽️</h4>
                <p style="font-weight: 500;">Complimentary Breakfast</p>
            </div>
            <div>
                <h4 style="color: var(--primary-gold); margin-bottom: 0.5rem; font-size: 1.5rem;">🏊‍♂️</h4>
                <p style="font-weight: 500;">Access to All Pools</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
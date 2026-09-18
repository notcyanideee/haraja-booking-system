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
        background-image: url('https://images.unsplash.com/photo-1499793983690-e29da59ef1c2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
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

    /* Filters Section */
    .gallery-filters {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 4rem;
        flex-wrap: wrap;
    }

    .filter-btn {
        background: transparent;
        border: 1px solid var(--border-color);
        color: var(--text-color);
        padding: 8px 24px;
        border-radius: 30px;
        cursor: pointer;
        font-family: var(--font-body);
        transition: var(--transition-smooth);
        font-size: 0.9rem;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: var(--primary-gold);
        color: #fff;
        border-color: var(--primary-gold);
    }

    /* Masonry Grid Layout (CSS Only) */
    .gallery-masonry {
        column-count: 3;
        column-gap: 1.5rem;
        width: 100%;
    }

    .gallery-item {
        break-inside: avoid;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
        border-radius: var(--border-radius);
        cursor: pointer;
        box-shadow: var(--shadow-sm);
        transition: transform 0.4s ease, opacity 0.4s ease;
    }

    /* Hide animation for filtering */
    .gallery-item.hide {
        display: none;
    }

    .gallery-item img {
        width: 100%;
        display: block;
        transition: transform 0.8s ease;
        object-fit: cover;
    }

    .gallery-item:hover img {
        transform: scale(1.08);
    }

    /* Image Overlay/Hover Effect */
    .gallery-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: var(--transition-smooth);
    }

    .gallery-overlay i {
        color: #fff;
        font-size: 2rem;
        transform: scale(0.5);
        transition: transform 0.4s ease;
    }

    .gallery-item:hover .gallery-overlay {
        opacity: 1;
    }

    .gallery-item:hover .gallery-overlay i {
        transform: scale(1);
    }

    /* Lightbox Styles */
    #lightbox {
        display: none;
        /* Hidden by default */
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.95);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    #lightbox.active {
        display: flex;
        opacity: 1;
    }

    #lightbox img {
        max-width: 90%;
        max-height: 90vh;
        border-radius: 4px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        transform: scale(0.9);
        transition: transform 0.3s ease;
    }

    #lightbox.active img {
        transform: scale(1);
    }

    .lightbox-close {
        position: absolute;
        top: 30px;
        right: 40px;
        color: #fff;
        font-size: 3rem;
        cursor: pointer;
        transition: color 0.3s ease;
        line-height: 1;
    }

    .lightbox-close:hover {
        color: var(--primary-gold);
    }

    /* Responsive Masonry */
    @media (max-width: 991px) {
        .gallery-masonry {
            column-count: 2;
        }
    }

    @media (max-width: 600px) {
        .gallery-masonry {
            column-count: 1;
        }
    }
</style>

<!-- Page Banner -->
<section class="page-banner">
    <div class="banner-overlay"></div>
    <div class="banner-content container">
        <h1 class="section-title" data-aos="fade-up" style="color: #fff;">Gallery</h1>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100" style="color: var(--primary-gold);">A Glimpse Into Paradise</p>
    </div>
</section>

<!-- Gallery Section -->
<section class="section-padding">
    <div class="container">

        <!-- Category Filters -->
        <div class="gallery-filters" data-aos="fade-up">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="pools">Pools</button>
            <button class="filter-btn" data-filter="rooms">Rooms</button>
            <button class="filter-btn" data-filter="gazebos">Gazebos</button>
            <button class="filter-btn" data-filter="events">Events</button>
            <button class="filter-btn" data-filter="restaurant">Restaurant</button>
            <button class="filter-btn" data-filter="landscape">Landscape</button>
        </div>

        <!-- Masonry Grid -->
        <div class="gallery-masonry">

            <!-- Item 1 (Pools) -->
            <div class="gallery-item" data-category="pools" data-aos="fade-up">
                <img src="https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Main Infinity Pool" loading="lazy">
                <div class="gallery-overlay"><i>+</i></div>
            </div>

            <!-- Item 2 (Rooms) -->
            <div class="gallery-item" data-category="rooms" data-aos="fade-up" data-aos-delay="100">
                <img src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Ocean View Suite" loading="lazy">
                <div class="gallery-overlay"><i>+</i></div>
            </div>

            <!-- Item 3 (Landscape) -->
            <div class="gallery-item" data-category="landscape" data-aos="fade-up" data-aos-delay="200">
                <img src="https://images.unsplash.com/photo-1540541338287-41700207dee6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Resort Aerial View" loading="lazy">
                <div class="gallery-overlay"><i>+</i></div>
            </div>

            <!-- Item 4 (Events) -->
            <div class="gallery-item" data-category="events" data-aos="fade-up">
                <img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Wedding Setup" loading="lazy">
                <div class="gallery-overlay"><i>+</i></div>
            </div>

            <!-- Item 5 (Gazebos) -->
            <div class="gallery-item" data-category="gazebos" data-aos="fade-up" data-aos-delay="100">
                <img src="https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Private Garden Gazebo" loading="lazy">
                <div class="gallery-overlay"><i>+</i></div>
            </div>

            <!-- Item 6 (Restaurant) -->
            <div class="gallery-item" data-category="restaurant" data-aos="fade-up" data-aos-delay="200">
                <img src="https://images.unsplash.com/photo-1514933651103-005eec06c04b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Fine Dining Restaurant" loading="lazy">
                <div class="gallery-overlay"><i>+</i></div>
            </div>

            <!-- Item 7 (Rooms) -->
            <div class="gallery-item" data-category="rooms" data-aos="fade-up">
                <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Tropical Villa" loading="lazy">
                <div class="gallery-overlay"><i>+</i></div>
            </div>

            <!-- Item 8 (Pools) -->
            <div class="gallery-item" data-category="pools" data-aos="fade-up" data-aos-delay="100">
                <img src="https://images.unsplash.com/photo-1563299723-8bc653a948e9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Night Pool" loading="lazy">
                <div class="gallery-overlay"><i>+</i></div>
            </div>

            <!-- Item 9 (Events) -->
            <div class="gallery-item" data-category="events" data-aos="fade-up" data-aos-delay="200">
                <img src="https://images.unsplash.com/photo-1505373877841-8d25f7d46678?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Corporate Conference Hall" loading="lazy">
                <div class="gallery-overlay"><i>+</i></div>
            </div>

        </div>
    </div>
</section>

<!-- Lightbox Container -->
<div id="lightbox">
    <span class="lightbox-close">&times;</span>
    <img id="lightbox-img" src="" alt="Expanded Image">
</div>

<!-- Gallery Filtering & Lightbox Logic -->
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // --- 1. Gallery Filtering ---
        const filterBtns = document.querySelectorAll('.filter-btn');
        const galleryItems = document.querySelectorAll('.gallery-item');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active class from all buttons
                filterBtns.forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                btn.classList.add('active');

                const filterValue = btn.getAttribute('data-filter');

                galleryItems.forEach(item => {
                    const itemCategory = item.getAttribute('data-category');

                    if (filterValue === 'all' || filterValue === itemCategory) {
                        item.classList.remove('hide');
                    } else {
                        item.classList.add('hide');
                    }
                });
            });
        });

        // --- 2. Lightbox Logic ---
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        const closeBtn = document.querySelector('.lightbox-close');

        // Open Lightbox
        galleryItems.forEach(item => {
            item.addEventListener('click', () => {
                const imgSrc = item.querySelector('img').getAttribute('src');
                lightboxImg.setAttribute('src', imgSrc);
                lightbox.classList.add('active');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            });
        });

        // Close Lightbox (Clicking X)
        closeBtn.addEventListener('click', () => {
            lightbox.classList.remove('active');
            document.body.style.overflow = 'auto'; // Restore scrolling
        });

        // Close Lightbox (Clicking outside image)
        lightbox.addEventListener('click', (e) => {
            if (e.target !== lightboxImg) {
                lightbox.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });

        // Close Lightbox (Pressing Escape key)
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && lightbox.classList.contains('active')) {
                lightbox.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });

    });
</script>

<?php include 'includes/footer.php'; ?>
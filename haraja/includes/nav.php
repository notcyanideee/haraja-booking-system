<!-- Add this to the <head> section of your header.php -->
<link rel="stylesheet" href="css/mobile-menu.css">

<header class="site-header" id="navbar">
    <div class="container nav-container">
        <a href="index.php" class="brand-logo">Haraja</a>

        <!-- Desktop Navigation -->
        <nav class="desktop-nav">
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="rooms.php">Rooms</a></li>
                <li><a href="daypass.php">Day Pass</a></li>
                <li><a href="events.php">Events</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>

        <div class="nav-actions">
            <button id="theme-toggle" class="theme-btn" aria-label="Toggle Dark Mode">
                <span class="icon-moon">🌙</span>
                <span class="icon-sun" style="display:none;">☀️</span>
            </button>
            <a href="checkout.php" class="btn btn-primary btn-book">Book Now</a>

            <!-- Hamburger Button -->
            <button class="hamburger" id="mobile-menu-btn" aria-label="Open Menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </div>
    </div>
</header>

<!-- Mobile Menu Overlay (Hidden by default) -->
<div class="mobile-menu-overlay" id="mobile-menu">
    <nav class="mobile-nav">
        <ul class="mobile-nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="rooms.php">Rooms</a></li>
            <li><a href="daypass.php">Day Pass</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="gallery.php">Gallery</a></li>
            <li><a href="contact.php">Contact</a></li>
            <!-- Book button duplicated for mobile convenience -->
            <li class="mobile-book-now"><a href="checkout.php" class="btn btn-primary">Book Now</a></li>
        </ul>
    </nav>
</div>
<!-- made by ryu do not copy -->
<!-- contact ryujosephbalanay@gmail for more info -->

<!-- Add this right before the closing </body> tag, or in your footer.php -->
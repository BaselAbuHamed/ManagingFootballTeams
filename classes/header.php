<?php

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['username']);
$username = '';

if ($isLoggedIn) {
    // Retrieve the username from the session
    $username = $_SESSION['username'];
}

// Logout functionality
if (isset($_GET['logout'])) {
    // Destroy the session
    session_destroy();
    // Redirect to the login page
    header("Location: index.php");
    exit();
}
?>

<header class="menu_bar">
    <div class="logo">
        <img src="images/logo_football.png" alt="Logo" width=5% hight=5%>
    </div>
    <div class="app-name">
        <p>Football Teams
            <span>Maker</span>
        </p>
    </div>
    <nav>
        <ul>
            <?php if ($isLoggedIn) : ?>
                <li>
                    <div class="cart">
                        <a href="profile.php">
                            <?php echo $username; ?>
                            <img src="images/user_image.png" alt="User Photo" width="30" height="30">
                        </a>
                    </div>
                </li>
                <li><a href="?logout=true">Log out</a></li>
            <?php endif; ?>
            <li><a href="about.html">About Us</a></li>

        </ul>
    </nav>
</header>

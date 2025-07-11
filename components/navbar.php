<?php
session_start();
?>

<nav class="navbar">
    <div class="navbar-brand">HBN Project</div>
    <div class="navbar-menu">
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-profile">
                <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <img src="../Assets/Img/default-avatar.jpg" alt="Profile">
                <a href="../backend/controllers/AuthController.php?action=logout">Logout</a>
            </div>
        <?php else: ?>
            <a href="../login.html">Login</a>
            <a href="../register.html">Register</a>
        <?php endif; ?>
    </div>
</nav>
<?php
require_once (__DIR__."/../services/auth.php");
?>

<div class="userInfo">
    <p>Hello, <a href="user.profile.php"><?php echo htmlspecialchars($_SESSION['userName']) ?></a>!</p>
    <a class="myevents" href="my.events.php">My Events</a>
    <a class="logout" href="forms/logout.php">Logout</a>
    <?php if(Auth::checkAdmin()): ?>
        <a href="admin.php">Admin page</a>
    <?php endif; ?>
</div>

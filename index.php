<?php

require_once ("services/auth.php");
require_once ("datacontext/repositories/events.repository.php");
require_once ("datacontext/repositories/users.repository.php");
require_once ("datacontext/categories.enum.php");

#region Pagination setup

$er = new EventsRepository();

$pageLimit = 8;
$pageNumber = 1;
$eventsTotalCount = $er->getEventsCount();
$pagesCount = ceil($eventsTotalCount / $pageLimit);

if(isset($_GET["page"]))
{
    $page = intval($_GET["page"]);
    if($page > 0 && $page <= $pagesCount)
    {
        $pageNumber = $page;
    }
}

// Offset to retreive data from db corresponding to a particular page number
$offset = ($pageNumber - 1) * $pageLimit;
$events = $er->getAllEvents($pageLimit, $offset);

#endregion

$currentPage = "index.php";

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main page</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="stylesheet" href="./assets/css/events.list.css">
    <link rel="stylesheet" href="./assets/css/pagination.css">
    <script src="./assets/js/events.filter.js" defer></script>
    <link rel="icon" href="public/logo.svg">
</head>
<body>
    <div class="toolbar">
            <a href="index.php">
                <img class="logo" src="public/logo.svg" alt="logo">
            </a>
            <div class="horizontalContainer">
                <?php if(Auth::isLoggedIn()): ?>
                    <a class="nav-button" href="forms/event.form.php">CREATE EVENT</a>
                    <?php require_once("views/user.info.php"); ?>
                <?php else: ?>
                    <a class="signin" href="forms/signin.php">Sign in</a>
                <?php endif; ?>
            </div>
    </div>

    <?php 
        require_once("views/events.grid.php");
    ?>
    
</body>
</html>
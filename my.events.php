<?php 

require_once("services/validators.php");
require_once("datacontext/repositories/users.repository.php");
require_once("datacontext/entities/user.php");
require_once ("services/auth.php");
require_once ("datacontext/repositories/events.repository.php");
require_once ("datacontext/repositories/users.repository.php");
require_once ("datacontext/categories.enum.php");

Auth::checkAuth();

$er = new EventsRepository();
    
if(isset($_POST["deleteEvent"]))
{
    $eventId = $_POST["eventId"];

    if ($er->deleteEvent($eventId)){
        header("Location: my.events.php");
        exit;
    }
    else{
        die("Failed to delete event with ID: " . htmlspecialchars($eventId));
    }
}

#region Pagination setup

$pageLimit = 4;
// default
$pageNumber = 1;
$eventsTotalCount = $er->getEventsCount($_SESSION["userId"]);
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
$events = $er->getEventsByUser($_SESSION["userId"], $pageLimit, $offset);

#endregion

$currentPage = "my.events.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="stylesheet" href="./assets/css/events.list.css">
    <link rel="stylesheet" href="./assets/css/pagination.css">
    <link rel="icon" href="public/logo.svg">
    <script src="./assets/js/events.filter.js" defer></script>
</head>
<body>
    <div class="toolbar">
        <a href="index.php">
            <img class="logo" src="public/logo.svg" alt="logo">
        </a>
        <h1>My Events</h1>
        <a class="nav-button" href="forms/event.form.php">CREATE EVENT</a>
    </div>

    <?php 
        require_once("views/events.grid.php"); 
    ?>
</div>
</body>
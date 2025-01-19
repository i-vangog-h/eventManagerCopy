<?php

require_once ("services/auth.php");
require_once ("datacontext/repositories/events.repository.php");
require_once ("datacontext/repositories/users.repository.php");

if(!Auth::checkAdmin())
{
    header("Location: ./views/unauthorized.php");
}

#region Pagination setup

$ur = new UsersRepository();
$er = new EventsRepository();

$pageLimit = 3;
$usersPageNumber = 1;
$eventsPageNumber = 1;

$usersTotalCount = $ur->getUsersCount();
$eventsTotalCount = $er->getEventsCount();

$usersPagesCount = ceil($usersTotalCount / $pageLimit);
$eventsPagesCount = ceil($eventsTotalCount / $pageLimit);

if(isset($_GET["userPage"]))
{
    $page = intval($_GET["userPage"]);
    if($page > 0 && $page <= $usersPagesCount)
    {
        $usersPageNumber = $page;
    }
}

if(isset($_GET["eventPage"]))
{
    $page = intval($_GET["eventPage"]);
    if($page > 0 && $page <= $eventsPagesCount)
    {
        $eventsPageNumber = $page;
    }
}

// Offset to retreive data from db corresponding to a particular page number
$usersOffset = ($usersPageNumber - 1) * $pageLimit;
$eventsOffset = ($eventsPageNumber - 1) * $pageLimit;

$users = $ur->getUsers($pageLimit, $usersOffset);
$events = $er->getAllEvents($pageLimit, $eventsOffset);

#endregion 

if(isset($_POST["deleteUser"]))
{
    // First need to remove events, dependent on the user.
    $userId = $_POST["userId"];
    $userEvents = $er->getEventsByUser($userId);

    foreach($userEvents as $event)
    {
        !$er->deleteEvent($event["id"]);
    }

    if($ur->deleteUser($userId))
    {
        header("Location: admin.php");
    }
    else
    {
        die("Failed to delete user");
    }
}

if(isset($_POST["deleteEvent"]))
{
    $eventId = $_POST["eventId"];
    if ($er->deleteEvent($eventId))
    {
        header("Location: admin.php");
    }
    else
    {
        die("Failed to delete event");
    }
}

if(isset($_POST["makeAdmin"]))
{
    $userId = $_POST["userId"];
    $userEntity = User::getUserFromDBResponse($ur->getUserById($userId));

    $userEntity->isAdmin = 1;
    if ($ur->updateUser($userEntity))
    {
        header("Location: admin.php?userPage=".$usersPageNumber."&eventPage=".$eventsPageNumber);
    }
    else{
        die("Failed to update user");
    }
}

if(isset($_POST["reduceAdmin"]))
{
    $userId = $_POST["userId"];

    // Check if trying reduce own rights
    if($userId == $_SESSION["userId"])
    {
        $statusText = "safety issue: unable to reduce your own rights.";
    }
    else
    {
        $userEntity = User::getUserFromDBResponse($ur->getUserById($userId));

        $userEntity->isAdmin = 0;
        $ur->updateUser($userEntity);

        header("Location: admin.php?userPage=".$usersPageNumber."&eventPage=".$eventsPageNumber);
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="stylesheet" href="./assets/css/admin.css">
    <link rel="stylesheet" href="./assets/css/pagination.css">
    <link rel="icon" href="public/logo.svg">
</head>
<body>
    <div class="toolbar">
            <a href="index.php">
                <img class="logo" src="public/logo.svg" alt="logo">
            </a>
            <h3 class="title"> Admin Page</h3>
            <div id="status-text-area">
                <?php if(isset($statusText)): ?>
                    <?php echo $statusText; ?>
                <?php endif ?>
            </div>
    </div>
    <div class="container">
        <div class="section">
            <h2>Events</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Owner ID</th>
                        <th>Category ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Start At</th>
                        <th>Location</th>
                        <th>Entry Price</th>
                        <th>Image</th>
                        <th>Created At</th>
                        <th class="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event["id"]); ?></td>
                            <td><?php echo htmlspecialchars($event["ownerId"]); ?></td>
                            <td><?php echo htmlspecialchars($event["categoryId"]); ?></td>
                            <td><?php echo htmlspecialchars($event["name"]); ?></td>
                            <td><?php echo htmlspecialchars($event["description"]); ?></td>
                            <td><?php echo htmlspecialchars($event["startAt"]); ?></td>
                            <td><?php echo htmlspecialchars($event["location"]); ?></td>
                            <td><?php echo htmlspecialchars($event["entryPrice"]); ?></td>
                            <td>
                                <?php if ($event["imageUri"]): ?>
                                    <img src="public/uploads/event.images/<?php echo $event["imageUri"]; ?>" alt="Event Image">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($event["createdAt"]); ?></td>
                            <td class="actions">
                                <form method="post">
                                    <input type="hidden" name="eventId" value="<?php echo $event["id"] ?>">
                                    <button type="submit" name="deleteEvent">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination">
                <?php for ($i = 1; $i <= $eventsPagesCount; $i++): ?>
                    <a 
                        href="<?php echo("admin.php?userPage=".$usersPageNumber."&eventPage=".$i); ?>"
                        <?php if($eventsPageNumber == $i) echo("class='active'"); ?>
                    > 
                        <?php echo($i); ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
        <div class="section">
            <h2>Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Created At</th>
                        <th>Profile Image</th>
                        <th>Profile Image URI</th>
                        <th>Is Admin</th>
                        <th class="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user["id"]); ?></td>
                            <td><?php echo htmlspecialchars($user["username"]); ?></td>
                            <td><?php echo htmlspecialchars($user["email"]); ?></td>
                            <td><?php echo htmlspecialchars($user["createdAt"]); ?></td>
                            <td>
                                <?php if ($user["profileImageUri"]): ?>
                                    <img src="public/uploads/user.images/<?php echo $user["profileImageUri"]; ?>" alt="Profile Image">
                                <?php endif; ?>
                            </td>
                            <td><?php echo $user["profileImageUri"] ? (htmlspecialchars($user["profileImageUri"])) : ""; ?></td>
                            <td><?php echo $user["isAdmin"] ? "Yes" : "No"; ?></td>
                            <td class="actions">
                                <form method="post">
                                    <input type="hidden" name="userId" value="<?php echo $user["id"] ?>">
                                    <button type="submit" name="deleteUser">Delete</button>
                                </form>
                                <form method="post">
                                    <input type="hidden" name="userId" value="<?php echo $user["id"] ?>">
                                    <button type="submit" name="<?php echo $user["isAdmin"] ? 'reduceAdmin' : 'makeAdmin'; ?>">
                                        <?php echo $user["isAdmin"] ? "Reduce Admin" : "Make Admin"; ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination">
                <?php for ($i = 1; $i <= $usersPagesCount; $i++): ?>
                    <a 
                        href="<?php echo("admin.php?userPage=".$i."&eventPage=".$eventsPageNumber); ?>"
                        <?php if($usersPageNumber == $i) echo("class='active'"); ?>
                    > 
                        <?php echo($i); ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</body>
</html>
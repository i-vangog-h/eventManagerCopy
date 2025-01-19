<?php 

require_once("../services/auth.php");
require_once("../datacontext/repositories/events.repository.php");
require_once("../datacontext/entities/event.php");

Auth::checkAuth();
$uploadMaxSize = intval(ini_get('upload_max_filesize'));
$categoryId = -1;

if(isset($_POST["submit"]))
{
    $statusText;

    $imagesDir = "../public/uploads/event.images";

    $eventId = uniqid();
    $ownerId = $_SESSION["userId"];
    $name = $_POST["name"];
    $description = $_POST["description"];
    $categoryId = $_POST["categoryId"];
    $startAt = $_POST["startAt"];
    $location = $_POST["location"];
    $entryPrice = intval($_POST["entryPrice"]);
    $imageUri = null;

    $error = false;

    // image is required for the event to be created
    if(isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_NO_FILE) 
    {
        $statusText = "event image is required";
        $error = true;
    }

    if(!$error)
    {
        // do not proceed if error occured when tried upload image
        if(isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_INI_SIZE) 
        {
            $statusText = "The image is too large. Max size is ".$uploadMaxSize."MB.";
            $error = true;
        }

        if(!$error)
        {
            if(isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK)
            {
                $fileType = array_reverse(explode( '.', $_FILES['image']['name']) )[0];
                $imageUri = $eventId.'_eventimg.'.$fileType;
                $saveToUri = $imagesDir.'/'.$imageUri;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $saveToUri))
                {
                    $statusText = "Failed to upload the image.";
                }
            }

            $eventEntity = new Event($eventId, $ownerId, $categoryId, $name);
            $eventEntity->description = $description;
            $eventEntity->startAt = $startAt;
            $eventEntity->location = $location;
            $eventEntity->entryPrice = $entryPrice;
            $eventEntity->imageUri = $imageUri;

            $er = new EventsRepository();

            if($er->addEvent($eventEntity))
            {
                header("Location: ../index.php");
            }
            else{
                die("Failed to update event on DB upload");
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create event</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<div class="centeredContainer">
    <?php require_once("../views/logo.php"); ?>
    <h1>Create Event</h1>
    <form class="customForm" action="event.form.php" method="post" enctype="multipart/form-data">

            <label for="name">Title</label>
            <input type="text" id="name" name="name"
                value="<?php if (isset($name)) echo htmlspecialchars($name); ?> " required>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" required><?php if (isset($description)) echo htmlspecialchars($description); ?></textarea>

            <label for="category">Category</label>
            <select id="category" name="categoryId" required>
                <option value="1" <?php if ($categoryId == 1) echo "selected" ?>>Movie</option>
                <option value="2" <?php if ($categoryId == 2) echo "selected" ?>>Party</option>
                <option value="3" <?php if ($categoryId == 3) echo "selected" ?>>Performance</option>
                <option value="4" <?php if ($categoryId == 4) echo "selected" ?>>Rave</option>
                <option value="5" <?php if ($categoryId == 5) echo "selected" ?>>Sport</option>
            </select>
        
            <label for="startAt">Start Date and Time</label>
            <input type="datetime-local" id="startAt" name="startAt" 
                value="<?php if (isset($startAt)) echo htmlspecialchars($startAt); ?>" 
            required>
        
            <label for="location">Location</label>
            <input type="text" id="location" name="location" 
                value="<?php if (isset($location)) echo htmlspecialchars($location); ?>"
            required>

            <label for="entryPrice">Entry Price</label>
            <input type="text" pattern="\d*" maxlength="3" min="0" id="entryPrice" name="entryPrice" 
                value="<?php if (isset($entryPrice)) echo htmlspecialchars($entryPrice); ?>"
            required>

            <label for="image">Cover Image</label>
            <input type="file" id="image" name="image" accept="image/png, image/jpeg">

            <div id="status-text-area">
                <?php
                    if(isset($statusText))
                        echo $statusText;
                ?>
            </div>

            <button type="submit" name="submit" value="submit">create event</button>
    </form>
</div>
</body>
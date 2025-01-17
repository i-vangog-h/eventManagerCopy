<?php 

require_once("services/auth.php");
require_once("services/validators.php");
require_once("datacontext/repositories/users.repository.php");
require_once("datacontext/entities/user.php");

Auth::checkAuth();

$ur = new UsersRepository();
$userId = $_SESSION["userId"];

// get user from db to display data
$userEntity = $ur->getUserById($userId);
$uploadMaxSize = intval(ini_get('upload_max_filesize'));

$statusText;
$successText;

// status info on last action
if(isset($_GET["statusText"]))
{
    $statusText = htmlspecialchars($_GET["statusText"]);
}
else if(isset($_GET["successText"]))
{
    $successText = htmlspecialchars($_GET["successText"]);
}

// process changes 
if(isset($_POST["submit"]))
{
    unset($successText);
    
    $username = trim($_POST["name"]);
    $email = trim($_POST["email"]);

    if(validateEmail($email, $statusText))
    {
        $error = false;

        // do not proceed if error occured when tried uploading image
        if(isset($_FILES['user-image']) && $_FILES['user-image']['error'] == UPLOAD_ERR_INI_SIZE) 
        {
            $statusText = "The image is too large. Max size is ".$uploadMaxSize."MB.";
            $error = true;
        }
        
        if(!$error)
        {
            $imagesDir = "public/uploads/user.images";
            $profileImageUri = $userEntity["profileImageUri"];

            // Process (if) uploaded image
            if(isset($_FILES['user-image']) && $_FILES['user-image']['error'] == UPLOAD_ERR_OK )
            {
                $fileType = array_reverse(explode( '.', $_FILES['user-image']['name']) )[0];
                $profileImageUri = $userId.'_userimg.'.$fileType;
                $saveToUri = $imagesDir.'/'.$profileImageUri;

                $ur->deleteUserImages($userId);

                if (!move_uploaded_file($_FILES['user-image']['tmp_name'], $saveToUri))
                {
                    $statusText = "Failed to upload the image.";
                }
            }

            // Allow make changes without uploading image
            $updatedUser = User::getUserFromDBResponse($userEntity);

            $updatedUser->username = $username;
            $updatedUser->email = $email;
            $updatedUser->profileImageUri = $profileImageUri;

            if($ur->updateUser($updatedUser))
            {
                $_SESSION["userName"] = $updatedUser->username;
                header("Location: user.profile.php?successText=changes saved");
            }
            else{
                header("Location: user.profile.php?statusText=failed to update user");
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
    <title>User profile</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="icon" href="public/logo.svg">
</head>
<body>
<div class="centeredContainer">
    <a href="index.php">
         <img class="logo" src="public/logo.svg" alt="logo">
    </a>
    <h1>User Info</h1>
    <?php if($userEntity["profileImageUri"] != null ): ?>
        <img class="userPic" src="public/uploads/user.images/<?php echo($userEntity["profileImageUri"])?>" alt="user profile image">
    <?php endif;?>

    <form class="customForm" action="user.profile.php" method="post" enctype="multipart/form-data">

            <label for="name">username</label>
            <input type="text" id="name" name="name" required
                value="<?php if (isset($userEntity["username"])) echo htmlspecialchars($userEntity["username"]); ?>"
            >

            <label for="email">email</label>
            <input type="email" id="email" name="email" 
                   value="<?php if (isset($userEntity["email"])) echo htmlspecialchars($userEntity["email"]); ?> "
            >

            <label for="user-image">Add/Change Profile Image</label>
            <input type="file" id="user-image" name="user-image" accept="image/png, image/jpeg">

            <?php if(isset($statusText)): ?>
                <div id="status-text-area">
                    <?php echo $statusText; ?>
                </div>
            <?php elseif (isset($successText)): ?>
                <div id="success-text-area">
                    <?php echo $successText; ?>
                </div>
            <?php endif ?>

            <button type="submit" name="submit" value="submit">save</button>
    </form>
</div>
</body>

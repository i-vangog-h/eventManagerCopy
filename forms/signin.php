<?php 
require_once ("../services/validators.php");
require_once ("../services/crypto.php");
require_once ("../datacontext/repositories/users.repository.php");
require_once("../services/auth.php");

if(isset($_POST["signin"]))
{
    $email = addslashes($_POST["email"]);
    $password = addslashes($_POST["password"]);

    $statusText;

    if(validateEmail($email, $statusText))
    {
        $ur = new UsersRepository();
        $user = $ur->getUserByEmail($email);
        if(!$user){
            $statusText = "user with this email is not found";
        }
        else
        {
            if(verifyPassword($password, $user["phash"]))
            {
                Auth::logIn($user["id"], $user["username"]);
                header('Location: ../index.php');
            }
        }
    }
    $statusText = "incorrect email or password";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="../assets/js/signin.js" defer></script>

</head>
<body>
    <div class="centeredContainer">
        <?php require_once("../views/logo.php"); ?>
        <form class="customForm" action="signin.php" method="post">
            <label for="email">email</label>
            <input type="email" id="email" name="email">
            <label for="password">password</label>
            <input type="password" id="password" name="password">
            <div id="status-text-area">
                <?php
                    if(isset($statusText))
                        echo $statusText;
                ?>
            </div>
            <button type="submit" name="signin">sign in</button>
        </form>
        <a class="signUpLink" href="signup.php">dont have an account? create one</a>
    </div>

</body>
</html>
<?php

include ("../services/validators.php");
include ("../services/crypto.php");
include ("../datacontext/repositories/users.repository.php");
require_once("../services/auth.php");

// entry point

if(isset($_POST["signup"]))
{
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $pas1 = $_POST["password1"];
    $pas2 = $_POST["password2"];

    $statusText;

    if(validateEmail($email, $statusText)){
        if(validatePassword($pas1, $pas2, $statusText)){

            $userId = uniqid();

            $user = new User(
                $userId,
                $username,
                $email,
                hashPassword($pas1),
            );

            $ur = new UsersRepository();
            if($ur->addUser($user))
            {
                Auth::logIn($userId, $username);
                header('Location: ../index.php');
                $statusText = "save and redirect";
            }

            $statusText = "error on adding user";
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="../assets/js/signup.js" defer></script>
</head>
<body>
    <div class="centeredContainer">
        <?php require_once("../views/logo.php"); ?>
        <form class="customForm" action="signup.php" method="post">
            <label for="username">username</label>
            <input  type="text"
                    id="username"
                    name="username"
                    value="<?php if (isset($username)) echo htmlspecialchars($username); ?> "
            >

            <label for="email">email</label>
            <input type="email" id="email" 
                    name="email" 
                    value="<?php if (isset($email)) echo htmlspecialchars($email); ?> ">

            <label for="password">password</label>
            <input type="password" name="password1" id="password1">
            
            <label for="password">repeat password</label>
            <input type="password" name="password2" id="password2">

            <div id="status-text-area">
                <?php
                    if(isset($statusText))
                        echo $statusText;
                ?>
            </div>

            <button type="submit" name="signup" value="submit">create account</button>

        </form>
        <a class="signUpLink" href="signin.php">already have an account? sign in</a>
    </div>
</body>
</html>

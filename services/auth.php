<?php

require_once (__DIR__."/../datacontext/repositories/users.repository.php");

class Auth
{
    public static function init()
    {
        if (session_status() === PHP_SESSION_NONE)
        {
            session_start();
        }
    }

    public static function checkAuth()
    {
        if(!self::isLoggedIn()){
            header("Location: ../forms/signin.php");
        }
    }

    public static function checkAdmin()
    {
        if(isset($_SESSION["userId"]))
        {
            $ur = new UsersRepository();
            $user = $ur->getUserById($_SESSION["userId"]);
            return $user["isAdmin"];
        }

        return false;
    }

    public static function logIn($userId, $userName)
    {
        $_SESSION["userId"] = $userId;
        $_SESSION["userName"] = $userName;
    }

    public static function logOut()
    {
        session_unset();
        session_destroy();
    }

    public static function isLoggedIn()
    {
        return isset($_SESSION["userId"]);
    }
}

// is called when the file is included
Auth::init();

?>

<?php

function validatePassword(?string $pas1, ?string $pas2,  ?string &$statusText){
    
    if (empty($pas1)){
        $statusText = "empty password";
        return false;
    }

    if (empty($pas2)){
        $statusText = "please repeat the password";
        return false;
    }

    if(strlen($pas1) < 6){
        $statusText = "password must be min 6 charachters long";
        return false;
    }
    else if(strlen($pas1) > 20){
        $statusText = "password must be max 20 charachters long";
        return false;
    }

    if(!($pas1 == $pas2)){
        $statusText = "passwords do not match";
        return false;
    }

    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d\W]{6,20}$/";
    if(!preg_match($pattern, $pas1)){
        $statusText = "password must contain at least one lower, upper and number charachter [a-z;A-Z;0-9]";
        return false;
    }

    return true;
}

function validateEmail(?string $email, ?string &$statusText){
    
    if(empty($email))
    {
        $statusText = "empty email";
        return false;
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $statusText = "email in the wrong format";
        return false;
    }

    return true;
}
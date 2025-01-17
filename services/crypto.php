<?php

function hashPassword(string $password){
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword(string $password, string $hash){
    return password_verify($password, $hash);
}
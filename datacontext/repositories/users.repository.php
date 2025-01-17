<?php

require_once (__DIR__."/../data.context.php");
require_once (__DIR__."/../entities/user.php");

class UsersRepository {
    private $dataContext;

    function __construct()
    {
        $this->dataContext = new DataContext();
    }

    function addUser(User $user)
    {
        try
        {
            $qry = "INSERT INTO Users(id, username, email, phash, profileImageURI, isAdmin, createdAt) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $params = [$user->id, $user->username, $user->email, $user->phash, $user->profileImageUri, $user->isAdmin, $user->createdAt];

            $this->dataContext->executeSQL($qry, $params);

            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    function getUsersCount()
    {
        try
        {
            $qry = "SELECT COUNT(*) as count FROM Users";

            $res = $this->dataContext->executeFromSQL($qry);
            return intval($res[0]["count"]);
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    function getUsers($limit=100, $offset=0)
    {
        try
        {
            $qry = "SELECT * FROM Users 
                    ORDER BY createdAt 
                    LIMIT :limit 
                    OFFSET :offset";

            $params = [
                ':limit' => $limit,
                ':offset' => $offset 
            ];

            $res = $this->dataContext->executeFromSQL($qry, $params, true);
            return $res;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    function getUserByEmail($userEmail)
    {
        try
        {
            $qry = "SELECT * FROM Users WHERE email = ?";
            $res = $this->dataContext->executeFromSQL($qry, [$userEmail]);
            return $res[0] ?? null;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    function getUserById($userId)
    {
        try
        {
            $qry = "SELECT * FROM Users WHERE id = ?";
            $res = $this->dataContext->executeFromSQL($qry, [$userId]);
            return $res[0] ?? null;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    function deleteUserImages($userId)
    {
        $userImagesDir = __DIR__."/../../public/uploads/user.images/";
        $userImages = [
            $userId."_userimg.png",
            $userId."_userimg.jpg",
            $userId."_userimg.jpeg"
        ];

        foreach($userImages as $userImage)
        {
            $imgPath = $userImagesDir.$userImage;
            if(file_exists($imgPath))
            {
                unlink($imgPath);
            }
        }
    }

    function deleteUser($userId)
    {
        try
        {
            $qry = "DELETE FROM Users WHERE id = ?";
            $this->dataContext->executeSQL($qry, [$userId]);

            $this->deleteUserImages($userId);

            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    function updateUser(User $user)
    {
        try
        {
            $qry = "UPDATE Users
                    SET username = ?,
                        email = ?,
                        profileImageUri = ?,
                        isAdmin = ?
                    WHERE id = ?";
            $params = [$user->username, $user->email, $user->profileImageUri, $user->isAdmin, $user->id];
            $this->dataContext->executeSQL($qry, $params);

            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
}
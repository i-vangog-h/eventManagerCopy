<?php
class User
{
    function __construct(string $id, string $username, string $email, string $phash)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->phash = $phash;
        $this->createdAt = date('Y-m-d H:i:s');
    }
    
    // Convert assoc. array user received from DB to User type object
    public static function getUserFromDBResponse($userDb){
        $user = new User(
            $userDb["id"],
            $userDb["username"],
            $userDb["email"],
            $userDb["phash"]
        );

        $user->isAdmin = $userDb["isAdmin"];
        $user->profileImageUri = $userDb["profileImageUri"];
        $user->createdAt = $userDb["createdAt"];

        return $user;
    }

    public string $id;
    public string $username;
    public string $email;
    public string $phash;
    public ?string $profileImageUri = null;
    public int $isAdmin = 0;
    public string $createdAt;
}

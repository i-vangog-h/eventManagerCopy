<?php

class DataContext 
{
    private $dsn = 'mysql:host=localhost;dbname=turkoiv2';
    private $username = "turkoiv2";
    private $password = "webove aplikace";

    function __construct(){
    }

    function testConnection()
    {
        $servername = "localhost";
        $username = "admin";
        $password = "Abc_1234";
        $dbname = "eventmanagerdb";
    
        $dbc = new mysqli($servername, $username, $password, $dbname);
    
        // Check connection
        if ($dbc->connect_error) {
            die("Connection failed: " . $dbc->connect_error);
        }
        
        $dbc->close();
    }

    public function executeSQL(string $sql, ?array $params = null)
    {
        try 
        {
            $pdo = new PDO($this->dsn, $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $statement = $pdo->prepare($sql);
            $statement->execute($params);
        } 
        catch (PDOException $e) 
        {
            die('Execute Sql Failed: ' . $e->getMessage());
        }
    }
    
    public function executeFromSQL(string $sql, ?array $params = null, $isParamsAsoc = false)
    {
        try 
        {
            $pdo = new PDO($this->dsn, $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $statement = $pdo->prepare($sql);

            if(!$isParamsAsoc)
            {
                $statement->execute($params);
            }
            else
            {
                // Have to explicitly bind integer params for Limit and Offset
                foreach ($params as $key => $value) {
                    $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                    $statement->bindValue($key, $value, $type);
                }
                $statement->execute();
            }
            
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } 
        catch (PDOException $e) 
        {
            die('Execute Sql Failed: ' . $e->getMessage());
        }
    }
}
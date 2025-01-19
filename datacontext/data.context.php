<?php

class DataContext 
{
    private $dsn;
    private $username;
    private $password;

    function __construct()
    {
        $this->dsn = "mysql:host=localhost;dbname=".(getenv("php_dbname") ? getenv("php_dbname") : "turkoiv2");
        $this->username = getenv("php_dbusername") ? getenv("php_dbusername") : "turkoiv2";
        $this->password = getenv("php_dbpswd") ? getenv("php_dbpswd") : "webove aplikace";
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

    /**
         * Opens connection to the DB. Prepares the passed sql statement with the optional parameters.
         * Executes no-response sql query. Dies with the error message when error occurs.
         *
         * @param string $sql SQL query to perform.
         * @param ?array $params Array of optional query parametrs.
         * @return void;
     */
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
    
    /**
         * Opens connection to the DB. Prepares the passed sql statement with the optional parameters.
         * Executes sql query. Dies with the error message when error occurs.
         *
         * @param string $sql SQL query to perform
         * @param ?array $params
         * @param bool $isParamsAsoc Flag, indicating that parametrs are passed as an associative array
         * (is needed when Integer parametr needs to be bound).
         * @return array Returns the query result as an associative array.
     */
    public function executeFromSQL(string $sql, ?array $params = null, bool $isParamsAsoc = false)
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
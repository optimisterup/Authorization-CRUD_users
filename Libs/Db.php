<?php
require 'config.php';

class DB
{
    public $host = DB_SERVER;
    public $user = DB_USERNAME;
    public $pass = DB_PASSWORD;
    public $dbname = DB_NAME;
    public $start;
    //How many records per page
    public $rpp=5;
    public $pdo;
    public $error;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        $this->pdo = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $this->user, $this->pass);
        if (!$this->pdo) {
            $this->error = "Fatal Error: Can't connect to database";
            return false;
        }
    }

    public function authorization($user_login, $user_password)
    {
        $sql = "SELECT login, password, admin FROM users WHERE login = :login";
        if ($stmt = $this->pdo->prepare($sql)) {
            $param_login = trim($user_login);
            $stmt->bindParam(':login', $param_login, PDO::PARAM_STR);
        }
        //Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Check if login exists, if yes then verify password
            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();
                    $hashed_password = $row['password'];
                    if ($row['admin'] == 1) {
                        if (password_verify($user_password, $hashed_password)) {
                            return true;
                        } else {
                            return "no valid";
                        }
                    }else{
                        return"no access";
                    }

            }else{
                return "no account";
            }
        }else{
            return "wrong";
        }
    }

    public function list (){
        $sql="SELECT * FROM users LIMIT $this->start, $this->rpp";
        $stmt = $this->pdo->query($sql);
        $download = $stmt->fetchAll();
        return $download;
    }

    public function pagination ($choice_page){
        //Check for set page
        isset($choice_page) ? $page=$choice_page : $page=0;
        //Check for page 1
        if ($page>1){
            $this->start=($page*$this->rpp)-$this->rpp;
        }else{
            $this->start=0;
        }
        $sql = "SELECT * FROM users";
        $stmt = $this->pdo->query($sql);
        //Get total records
        $numRows=$stmt->rowCount();
        //Get total number of pages
        $totalPages=ceil($numRows/$this->rpp);
        return $totalPages;
    }

    public function detail($user_id)
    {
        $sql = "SELECT * FROM users WHERE `id`=:id;";
        $id = trim($user_id);
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        //////// excute или FETCH
        $download = $stmt->fetchAll();
        return $download;
    }

    public function delete($user_id)
    {
        $sql = "DELETE FROM users WHERE `id`=:id;";
        $stmt = $this->pdo->prepare($sql);
        $id = trim($user_id);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getElementById($user_id){
        $sql = "SELECT * FROM users WHERE `id`=:id;";
        if($stmt = $this->pdo->prepare($sql)) {
            $id = trim($user_id);
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->execute();
            $download = $stmt->fetchAll();
            return $download;
        }
    }

    public function isTheLoginBusy ($user_login)
    {
        $sql = "SELECT id FROM users WHERE login = :login";
        if ($stmt = $this->pdo->prepare($sql)) {
            // Set parameters
            $param_login = trim($user_login);
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(':login', $param_login, PDO::PARAM_STR);
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return "busy";
                } else {
                    return true;
                }
            } else {
                return "wrong";
            }
        }return false;
    }

    public function addUser($login, $password, $firstname, $surname, $sex, $birthday, $admin){
        $sql = "INSERT INTO users (login, password, firstname, surname, sex, birthday, admin ) 
VALUES (:login, :password, :firstname, :surname, :sex, :birthday, :admin)";
        if ($stmt = $this->pdo->prepare($sql)) {
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(':password', $param_password, PDO::PARAM_STR);
            $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
            $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
            $stmt->bindParam(':sex', $sex, PDO::PARAM_STR);
            $stmt->bindParam(':birthday', $birthday, PDO::PARAM_STR);
            $stmt->bindParam(':admin', $admin, PDO::PARAM_STR);
            $stmt->bindParam(':login', $login, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        }else{
            return false;
        }
    }

    public function updateUser($user_id, $password, $firstname, $surname, $sex, $birthday, $admin){
        $sql = "UPDATE `users` SET `password`=:password, `firstname`=:firstname, `surname`=:surname, `sex`=:sex, `birthday`=:birthday, `admin`=:admin  WHERE `id`=:id;";
        if ($stmt = $this->pdo->prepare($sql)) {
            $id=trim($user_id);
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(':password', $param_password, PDO::PARAM_STR);
            $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
            $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
            $stmt->bindParam(':sex', $sex, PDO::PARAM_STR);
            $stmt->bindParam(':birthday', $birthday, PDO::PARAM_STR);
            $stmt->bindParam(':admin', $admin, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        }else{
            return false;
        }
    }

}
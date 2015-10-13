<?php
session_start();
include 'database_connector.php';
$connector = new database_connector();
// make sure it's a POST request
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = $_POST["username"];
    $password = $_POST["password"];
    $result = $connector->run_query("SELECT `password` FROM `admins` WHERE `username` = ?;", array($username), PDO::FETCH_NUM)[0][0];
    $options = array('cost' => 11);
    // check the password
    if(password_verify($password, $result)){
        // see if the hashing strength has updgraded, and seamlessly update the password if yes
        if(password_needs_rehash($result, PASSWORD_DEFAULT, $options)){
            $newHash = password_hash($password, PASSWORD_DEFAULT, $options);
            $connector->run_query("UPDATE `admins` SET `password` = ? WHERE `username` = ?;", array($newHash, $username));
        }
        // login the user
        $_SESSION['auth'] = 1;
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
    }
    else{
        echo "Authentication failed";
    }
}
else{
    echo "Only can login using POST method for security";
}
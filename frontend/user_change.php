<?php
session_start();
include 'database_connector.php';
class changed_user{
	public $success;
	public $message;
	public function __construct($success = false, $message = "failure") {
		$this->success = $success;
		$this->message = $message;
	}
}
$connector = new database_connector();
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if($_POST["password"] == $_POST["repeat"] && trim($_POST["password"]) != ""){
        $username = $_POST["username"];
        $password = $_POST["curpass"];
        $newPassword = $_POST["password"];
        $result = $connector->run_query("SELECT `hashed_pass` FROM `users` WHERE `username` = ?;", array($username), PDO::FETCH_NUM);
        if(sizeof($result) == 1){
            if(password_verify($password, $result[0][0])){
                $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $connector->run_query("UPDATE `users` SET `hashed_pass` = ? WHERE `username` = ?;", array($newHash, $username));
                $resp = new changed_user(true, "Password updated successfully");
                echo(json_encode($resp, JSON_PRETTY_PRINT));
            }
            else{
                $resp = new changed_user(false, "Incorrect password for user");
                echo(json_encode($resp, JSON_PRETTY_PRINT));
            }
        }
        else{
            $resp = new changed_user(false, "User does not exist");
            echo(json_encode($resp, JSON_PRETTY_PRINT));
        }
    }
    else{
        $resp = new changed_user(false, "Password and repeated password do not match, or are empty");
        echo(json_encode($resp, JSON_PRETTY_PRINT));
    }
}
else{
    $resp = new changed_user(false, "Only POST methods allowed for security");
    echo(json_encode($resp, JSON_PRETTY_PRINT));
}
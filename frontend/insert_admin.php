<?php
session_start();
include 'database_connector.php';
class created_user{
	public $success;
	public $message;
	public function __construct($success = false, $message = "failure") {
		$this->success = $success;
		$this->message = $message;
	}
}
$connector = new database_connector();

// make sure insert is coming from logged in user
if(isset($_SESSION['auth']) && $_SESSION['auth'] === 1){
    // best practice to validate backend regardless of frontend validation
    $username = $_POST["username"];
    // make sure the user isn't spaces
    $username = trim($username);
    if($username != ""){
        $existUser = $connector->run_query("SELECT * FROM `admins` WHERE username=?;", array($username), PDO::FETCH_NUM);
        if(sizeof($existUser) == 0){
            // validate that password and repeated password are equal
            if($_POST["password"] == $_POST["repeat"] && trim($_POST["password"]) != ""){
                $password = $_POST["password"];
                $password = password_hash($password, PASSWORD_DEFAULT);
                $new_admin = array($username, $password);
                $connector->run_query("INSERT INTO `admins` (`username`, `password`) VALUES (?, ?);", $new_admin);
                $resp = new created_user(true, "User created successfully");
                echo(json_encode($resp, JSON_PRETTY_PRINT));
            }
            // throw error that passwords don't match
            else{
                $resp = new created_user(false, "Passwords do not match");
                echo(json_encode($resp, JSON_PRETTY_PRINT));
            }
        }
        else{
            $resp = new created_user(false, "Username already exists");
            echo(json_encode($resp, JSON_PRETTY_PRINT));
        }
    }
    else{
        $resp = new created_user(false, "No username");
        echo(json_encode($resp, JSON_PRETTY_PRINT));
    }
}
?>
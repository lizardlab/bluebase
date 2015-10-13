<?php
session_start();
include 'database_connector.php';
class created_user{
	public $success;
	public $message;
	public $username;
	public $userid;
	public function __construct($success = false, $message = "failure", $username = null, $userid = null) {
		$this->success = $success;
		$this->message = $message;
		$this->username = $username;
		$this->userid = $userid;
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
        // username constraints are much stricter
        // the username string must consist only of these characters: alphanumeric, underbar ('_'), dash ('-'), dot ('.'), or at ('@').
        $username = preg_replace("/[^A-Za-z0-9_@\-\.]/", "_", $username);
        // make sure that the user doesn't exist
        // database would reject duplicate username since its UNIQUE field
        // but this allows for easier discovery of any weird problems
        $existUser = $connector->run_query("SELECT `userid` FROM `users` WHERE username=?;", array($username), PDO::FETCH_NUM);
        if(sizeof($existUser) == 0){
            // validate that password and repeated password are equal
            if($_POST["password"] == $_POST["repeat"] && trim($_POST["password"]) != ""){
                $password = $_POST["password"];
                // password constraints defined by OpenVPN
                // password replace is inclusive, username is exclusive
                // The password string can consist of any printable characters except for CR or LF.
                $password = preg_replace("/[\n\r]/", "_", $password);
                $password = password_hash($password, PASSWORD_DEFAULT);
                // make sure date is valid or not set
                if(preg_match("/^\d{4}-[01]\d-[0-3]\d$/", $_POST["expire"]) == 1 || $_POST["expire"] == ""){
                    $expire;
                    if($_POST["expire"] == ""){
                        $expire = null;
                    }
                    else{
                        $expire = $_POST["expire"];
                    }
                    $new_user = array($username, $password, $_POST["fname"], $_POST["lname"], $expire, $_POST["status"]);
                    $connector->run_query("INSERT INTO `users` (`userid`, `username`, `hashed_pass`, `fname`, `lname`, `expires`, `disabled`) VALUES (NULL, ?, ?, ?, ?, ?, ?);", $new_user);
                    $userid = $connector->run_query("SELECT `userid` FROM `users` WHERE username=?;", array($username), PDO::FETCH_NUM)[0][0];
                    $resp = new created_user(true, "User created successfully", $username, $userid);
                    echo(json_encode($resp, JSON_PRETTY_PRINT));
                }
                else{
                    $resp = new created_user(false, "Invalid date received", null, null);
                    echo(json_encode($resp, JSON_PRETTY_PRINT));
                }
            }
            // throw error that passwords don't match
            else{
                $resp = new created_user(false, "Passwords do not match", null, null);
                echo(json_encode($resp, JSON_PRETTY_PRINT));
            }
        }
        else{
            $resp = new created_user(false, "Username already exists", $username, $existUser[0][0]);
            echo(json_encode($resp, JSON_PRETTY_PRINT));
        }
    }
    else{
        $resp = new created_user(false, "No username", null, null);
        echo(json_encode($resp, JSON_PRETTY_PRINT));
    }
}
?>
<?php
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
// if it's a DELETE request then they're trying to delete the user
if($_SERVER["REQUEST_METHOD"] == "DELETE"){
    $_DELETE = array();
    parse_str(file_get_contents('php://input'), $_DELETE);
    $userid = $_DELETE["userid"];
    $result = $connector->run_query("SELECT * FROM `users` WHERE `userid` = ?;", array($userid), PDO::FETCH_NUM);
    if(sizeof($result) == 0){
        $resp = new changed_user(false, "User ID does not exist");
        echo(json_encode($resp, JSON_PRETTY_PRINT));
    }
    else{
       $connector->run_query("DELETE FROM `users` WHERE `userid` = ?;", array($userid));
        $result = $connector->run_query("SELECT * FROM `users` WHERE `userid` = ?;", array($userid), PDO::FETCH_NUM);
        if(sizeof($result) == 0){
            $resp = new changed_user(true, "User ID deleted successfully");
            echo(json_encode($resp, JSON_PRETTY_PRINT));
        }
        else{
            $resp = new changed_user(false, "User ID deletion failed");
            echo(json_encode($resp, JSON_PRETTY_PRINT));
        }
    }
}
// otherwise assume it's a POST, to change a current user
else{
    // best practice to validate backend regardless of frontend validation
    $username = $_POST["username"];
    $userid = $_POST["userid"];
    // username constraints are much stricter
    // the username string must consist only of these characters: alphanumeric, underbar ('_'), dash ('-'), dot ('.'), or at ('@').
    $username = preg_replace("/[^A-Za-z0-9_@\-\.]/", "_", $username);
    // make sure that the user doesn't exist
    // database would reject duplicate username since its UNIQUE field
    // but this allows for easier discovery of any weird problems
    $existUser = $connector->run_query("SELECT * FROM `users` WHERE `userid` = ?;", array($userid), PDO::FETCH_ASSOC);
    // check if the userid we're changing exists
    if(sizeof($existUser) == 1){
        $existName = $connector->run_query("SELECT * FROM `users` WHERE `username` = ?;", array($username), PDO::FETCH_ASSOC);
        // check if username is same or if new username isn't taken
        if($existUser[0]["username"] == $username || sizeof($existName) == 0){
            // validate that password and repeated password are equal
            if($_POST["password"] == $_POST["repeat"]){
                $password = $_POST["password"];
                // if they didn't provide a password, keep the current one
                if($password == "")
                    $password = $existUser[0]["hashed_pass"];
                // otherwise santize new password and hash it
                else{
                    // password constraints defined by OpenVPN
                    // password replace is inclusive, username is exclusive
                    // The password string can consist of any printable characters except for CR or LF.
                    $password = preg_replace("/[\n\r]/", "_", $password);
                    $password = password_hash($password, PASSWORD_DEFAULT);
                }
                // make sure date is valid or not set
                if(preg_match("/^\d{4}-[01]\d-[0-3]\d$/", $_POST["expire"]) == 1 || $_POST["expire"] == ""){
                    $expire;
                    if($_POST["expire"] == ""){
                        $expire = null;
                    }
                    else
                        $expire = $_POST["expire"];
                    $changed_user = array($username, $password, $_POST["fname"], $_POST["lname"], $expire, $_POST["status"], $userid);
                    $connector->run_query("UPDATE `users` SET `username` = ?, `hashed_pass` = ?, `fname` = ?, `lname` = ?, `expires` = ?, `disabled` = ? WHERE `users`.`userid` = ?;", $changed_user);
                    $resp = new changed_user(true, "User changed successfully");
                    echo(json_encode($resp, JSON_PRETTY_PRINT));
                }
                // throw date error
                else{
                    $resp = new changed_user(false, "Invalid date received");
                    echo(json_encode($resp, JSON_PRETTY_PRINT));
                }
            }
            // throw error that passwords do not match
            else{
                $resp = new changed_user(false, "Passwords do not match");
                echo(json_encode($resp, JSON_PRETTY_PRINT));
            }
        }
        // throw that username is already taken (but not from the current userid)
        else{
            $resp = new changed_user(false, "New username already exists");
            echo(json_encode($resp, JSON_PRETTY_PRINT));
        }
    }
    // throw error that the user attempt to be changed doesn't exist at all
    else{
        $resp = new changed_user(false, "User ID does not exist");
        echo(json_encode($resp, JSON_PRETTY_PRINT));
    }
}

?>
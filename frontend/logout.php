<?php
session_start();
foreach($_SESSION as $key){
    unset($_SESSION[$key]);
}
session_destroy();
header("Location: index.php");
?>
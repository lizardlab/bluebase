<?php
session_start();
if(isset($_SESSION['auth']) && $_SESSION['auth'] === 1){
    header("Location: dashboard.php");
}
?>
<html>
<head>
<link rel="stylesheet" href="css/bootstrap.min.css" />
<title>BlueBase Login</title>
<style>
body {
  padding-top: 40px;
  padding-bottom: 40px;
}

.form-signin {
  max-width: 330px;
  padding: 15px;
  margin: 0 auto;
}
.form-signin .form-signin-heading,
.form-signin .checkbox {
  margin-bottom: 10px;
}
.form-signin .checkbox {
  font-weight: normal;
}
.form-signin .form-control {
  position: relative;
  height: auto;
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
  padding: 10px;
  font-size: 16px;
}
.form-signin .form-control:focus {
  z-index: 2;
}
.form-signin input[type="text"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}
.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
</style>
</head>
<body>
<div class="container">
<form method="POST" action="login.php" class="form-signin">
<h1 align="center">BlueBase Login</h1>
<hr />
<input class="form-control" type="text" name="username" placeholder="username"/>
<input class="form-control" type="password" name="password" placeholder="password" />
<button type="submit" class="btn btn-primary btn-block">Login</button>
</form>
</div>
</body>
</html>
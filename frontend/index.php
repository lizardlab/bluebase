<?php
session_start();
if(isset($_SESSION['auth']) && $_SESSION['auth'] === 1){
    header("Location: dashboard.php");
}
?>
<html>
<head>
<link rel="stylesheet" href="css/bootstrap.min.css" />
<script src="js/jquery.min.js"></script>
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
<h1 align="center">BlueBase Login</h1>
<div class="form-signin">
<hr />
<div class="alert alert-danger" style="display: none" role="alert" id='alertMsg'></div>
<input class="form-control" type="text" name="username" placeholder="username" id="username" />
<input class="form-control" type="password" name="password" placeholder="password" id="password" />
<button type="submit" class="btn btn-primary btn-block" id='loginBtn'>Login</button>
</div>
</div>
<script>
$('#loginBtn').click(
    function(){
        var username = $('#username').val();
        var password = $('#password').val();
        $.post('login.php', {"username": username, "password": password}, function(data){
            if(data.success == true){
                window.location = 'dashboard.php';
            }
            else{
                // set message using DOM for security reasons (XSS)
                document.getElementById('alertMsg').textContent = data.message;
                if ($("#alertMsg").is(":hidden")){
                    $('#alertMsg').slideDown();
                }
            }
        }, 'json');
    }
);
$(document).keydown(function(event){
    if (event.which == 13) {
        event.preventDefault();
        $("#loginBtn").trigger('click');
    }
});
</script>
</body>
</html>


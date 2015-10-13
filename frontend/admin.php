<?php
session_start();
// make sure that the dashboard only shows to logged in users
if(!isset($_SESSION['auth']) || $_SESSION['auth'] != 1){
    die('Not authenticated');
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>BlueBase Administrators</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<!-- <link rel="stylesheet" href="css/bootstrap-theme.min.css"> -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/admin.js"></script>
</head>
<body>
  <div class="container">
    <nav class="navbar navbar-default" style="margin-top: 20px;">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <a class="navbar-brand" href="#">BlueBase</a>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            <li><a href="dashboard.php">Dashboard<span class="sr-only">(current)</span></a></li>
            <li class="active"><a href="#">Admin<span class="sr-only">(current)</span></a></li>
          </ul>
          <p class="navbar-text navbar-right">Logged in as <a href="logout.php" class="navbar-link" style="font-weight: bold"><?php echo($_SESSION['username']) ?></a></p>
        </div>
      </div>
    </nav>
    <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Create Admin</h3>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <label>Username</label>
          <input type="text" class="form-control" id="username" placeholder="username"/>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" placeholder="password" class="form-control" id="password" />
        </div>
        <div class="form-group">
          <label>Repeat Password</label>
          <input type="password" placeholder="repeat" class="form-control" id="repeatpw" />
        </div>
        <button class="btn btn-success btn-block" onclick="createAdmin();">Submit</button>
      </div>
    </div>
    </div>
    <div class="col-md-8">
      <table class="table">
          <tr>
            <th>Username</th>
            <th>Edit</th>
          </tr>
          <tbody id="adminsTable">
          </tbody>
      </table>
    </div>
    <div class="modal fade" id="changeAdminModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Change Admin</h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <hidden type="text" id="usernameOld"></hidden>
              <label>Username</label>
              <input type="text" class="form-control" id="usernameChg" />
            </div>
            <div class="form-group">
              <label>Password</label>&nbsp;<small>(Only set if needs change)</small>
              <input type="password" placeholder="password" class="form-control" id="passwordChg" />
            </div>
            <div class="form-group">
              <label>Repeat Password</label>
              <input type="password" placeholder="repeat" class="form-control" id="repeatpwChg" />
            </div>
            <div class="form-group">
              <label>Delete Admin</label><br />
              <button type="button" class="btn btn-danger" onclick="deleteAdmin();">Delete Permanently</button>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="changeAdmin();">Save changes</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
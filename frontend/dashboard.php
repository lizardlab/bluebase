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
<title>BlueBase Dashboard</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/datepicker.css">
<!-- <link rel="stylesheet" href="css/bootstrap-theme.min.css"> -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="js/chart.min.js"></script>
<script src="js/bluebase.js"></script>
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
            <li class="active"><a href="#">Dashboard<span class="sr-only">(current)</span></a></li>
            <li><a href="admin.php">Admin</a></li>
          </ul>
          <p class="navbar-text navbar-right">Logged in as <a href="logout.php" class="navbar-link" style="font-weight: bold"><?php echo($_SESSION['username']) ?></a></p>
        </div>
      </div>
    </nav>
    <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Create User</h3>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <label>Username</label>
          <input type="text" class="form-control" id="username" placeholder="username" data-toggle="popover" title="Constraints" data-content="Valid characters: alphanumeric, _, -, ., @."/>
        </div>
        <div class="form-group">
          <label>First Name</label>
          <input type="text" class="form-control" id="fname" placeholder="first name" />
        </div>
        <div class="form-group">
          <label>Last Name</label>
          <input type="text" class="form-control" id="lname" placeholder="last name" />
        </div>
        <div class="checkbox">
        <label><input id="chkExpire" type="checkbox" checked="true" onclick="toggleExpire();">Enable Expiration</input></label>
        </div>
        <fieldset id="expireInput">
        <div class="form-group">
          <label>Expiration Date</label>
          <div class="input-group">
            <input class="datepicker form-control" data-date-format="yyyy-mm-dd" id="expire" pattern="d{4}-d{2}-d{2}" placeholder="YYYY-MM-DD" ></input>
            <span class="input-group-addon">
              <i class="glyphicon glyphicon-calendar"></i>
            </span>
          </div>
        </div>
        </fieldset>
        <label>Account status</label>
        <div class="checkbox">
          <label>
            <input type="checkbox" id="status">Disabled</input>
          </label>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" placeholder="password" class="form-control" id="password" />
        </div>
        <div class="form-group">
          <label>Repeat Password</label>
          <input type="password" placeholder="repeat" class="form-control" id="repeatpw" />
        </div>
        <button class="btn btn-success btn-block" onclick="createUser();">Submit</button>
      </div>
    </div>
    </div>
    <div class="col-md-8">
      <table class="table">
          <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Expiration</th>
            <th>Disabled</th>
            <th>Edit</th>
          </tr>
          <tbody id="usersTable">
          </tbody>
      </table>
    </div>
    <div class="col-md-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">User chart</h3>
        </div>
        <div class="panel-body">
          <canvas id="userChart" width="250" height="200"></canvas>
        </div>
      </div>
    </div>
    <div class="modal fade" id="changeUserModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Change User</h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <hidden type="text" id="useridChg"></hidden>
              <label>Username</label>
              <input type="text" class="form-control" id="usernameChg" placeholder="username"/>
            </div>
            <div class="form-group">
              <label>First Name</label>
              <input type="text" class="form-control" id="fnameChg" placeholder="first name"/>
            </div>
            <div class="form-group">
              <label>Last Name</label>
              <input type="text" class="form-control" id="lnameChg" placeholder="last name" />
            </div>
            <div class="checkbox">
            <label><input id="chkExpireChg" type="checkbox" checked="true" onclick="toggleExpireChg();" >Enable Expiration</input></label>
            </div>
            <fieldset id="expireInputChg">
            <div class="form-group">
              <label>Expiration Date</label>
              <div class="input-group">
                <input class="datepicker form-control" data-date-format="yyyy-mm-dd" id="expireChg" pattern="d{4}-d{2}-d{2}" placeholder="YYYY-MM-DD" ></input>
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-calendar"></i>
                </span>
              </div>
            </div>
            </fieldset>
            <label>Account status</label>
            <div class="checkbox">
              <label>
                <input type="checkbox" id="statusChg">Disabled</input>
              </label>
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
              <label>Delete User</label><br />
              <button type="button" class="btn btn-danger" onclick="deleteUser();">Delete Permanently</button>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="changeUser();">Save changes</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
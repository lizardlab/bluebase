<?php
 if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(trim($_POST["dbtype"]) != "" && trim($_POST["dbuser"]) != ""){
        $dbtype = $_POST["dbtype"];
        $dbname = $_POST["dbname"];
        $dbhost = $_POST["dbhost"];
        $dbuser = $_POST["dbuser"];
        $dbpass = $_POST["dbpass"];
        $dbport = $_POST["dbport"];
        $connstring = $dbtype.':host='.$dbhost.';port='.$dbport.';dbname='.$dbname;
        //echo($connstring . $dbuser . $dbpass);
        $connection = new PDO($connstring, $dbuser, $dbpass);
        $schema = fopen("schema.sql", "r");
        $query = "";
        while(!feof($schema)){
            $line = fgets($schema);
            $query = $query . $line;
        }
        $stmt = $connection->prepare($query);
        $stmt->execute();
        fclose($schema);
        echo("Installed successfully, now delete schema and install file.");
        
    }
    else{
        echo("Not enough information to establish database connection");
    }
} 
?>
<html>
<head>
<title>BlueBase Installation</title>
</head>
<body>
<h1 align="center">BlueBase Install</h1>
<p>Please input the database credentials for BlueBase, and it will install the schema.</p>
<form method="POST" action="install.php">

<input name="dbtype" type="hidden" value="mysql">
<label>Database Host<label>
<input name="dbhost" type="text"><br />
<label>Database Name<label>
<input name="dbname" type="text"><br />
<label>Database Username<label>
<input name="dbuser" type="text"><br />
<label>Database Password<label>
<input name="dbpass" type="password"><br />
<label>Database Port<label>
<input name="dbport" type="number"><br />
<button type="submit">Submit</button>
</form>
</body>
</html>

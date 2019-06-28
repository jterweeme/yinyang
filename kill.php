<!DOCTYPE html>
<?php
session_start();

if (isset($_SESSION['admin']))
{
    $handle = new SQLite3("yinyang.sqlite3");
    $query = sprintf("DELETE FROM xuser WHERE name=\"%s\";", $_GET['username']);
    $result = $handle->query($query);
}
?>
<html>
<head>
<meta http-equiv="Refresh" content="2;url=main.php"/>
<title>Kill</title>
</head>
<body>
<p>Wait while being redirected</p>
</body>
</html>



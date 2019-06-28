<!DOCTYPE html>
<?php
session_start();

if (isset($_SESSION['admin']))
{
    $handle = new SQLite3("yinyang.sqlite3");

    $query = sprintf("DELETE FROM member WHERE groupname=\"%s\" AND username=\"%s\";",
                $_GET['groupname'], $_GET['username']);

    $result = $handle->query($query);
}
?>
<html>
<head>
<meta http-equiv="Refresh" content="2;url=main.php"/>
<title>Kick</title>
</head>
<body>
<p>Wait while being redirected</p>
</body>
</html>



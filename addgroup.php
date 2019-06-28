<!DOCTYPE html>
<html>
<head>
<?php
session_start();

if (isset($_SESSION['admin']))
{
    $db = new SQLite3("yinyang.sqlite3");
    $query = sprintf("INSERT INTO xgroup(name) VALUES (\"%s\");", $_POST['groupname']);
    $result = $db->query($query);
    $db->close();
}
?>


<meta http-equiv="Refresh" content="2;url=main.php"/>
</head>
<body>
<pre>
</pre>
<p>
Wait while being redirected.
</p>
</body>
</html>


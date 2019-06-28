<!DOCTYPE html>
<?php
session_start();

if (isset($_SESSION['admin']))
{
    $db = new SQLite3("yinyang.sqlite3");

    $query = sprintf("INSERT INTO xuser(name, password) VALUES (\"%s\", \"%s\");",
            $_POST['name'], md5($_POST['name']));

    $result = $db->query($query);
    $db->close();
}
?>

<html>
<head>
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


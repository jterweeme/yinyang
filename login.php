<!DOCTYPE html>
<?php
/*
Jasper ter Weeme 2019

In dit bestand wordt het inloggen geregeld
*/
session_start();

function checkLogin($post)
{
    $handle = new SQLite3("yinyang.sqlite3");
    $md5 = md5($_POST['password']);

    // staan username en password hash in database?
    $query = sprintf("SELECT name, password FROM xuser WHERE name=\"%s\" AND password=\"%s\";",
        $_POST['user'], $md5);

    $result = $handle->query($query);
    $res = $result->fetchArray(1);
    
    if ($res == 0)
        return 2;

    $_SESSION['username'] = $res['name'];

    // is user admin?
    $query = sprintf("SELECT groupname, username FROM member ".
                    "WHERE groupname=\"admin\" AND username=\"%s\";",
                    $_SESSION['username']);

    $result = $handle->query($query);
    $res = $result->fetchArray(1);
    $admin = $res == 0 ? 0 : 1;
    
    if ($admin == 1)
        $_SESSION['admin'] = 1;

    return 1;
}

$status = 0;

if (isset($_POST['user']))
{
    $status = checkLogin($_POST);
}
?>

<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width"/>
<!--
<link rel="stylesheet" type="text/css" href="common.css"/>
-->
<style>
body {
    margin-top: 100px;
}

label {
    display: inline-block;
    width: 100px;
}

label, input
{
    margin-bottom: 10px;
}
</style>
<title>Login</title>
<?php
if ($status == 1)
    printf("<meta http-equiv=\"Refresh\" content=\"0;url=main.php\">\r\n")
?>
<link rel="Shortcut Icon" href="yinyang.svg"/>
</head>
<body>
<h1>YinYang</h1>
<form action="login.php" method="post">
<label>Username</label>
<input name="user"/>
<br/>
<label>Password</label>
<input name="password" type="password"/>
<br/>
<input type="submit" value="Login"/>
</form>
<?php
if ($status == 2)
    printf("<p>Invalid username or password</p>\r\n");
?>
</body>
</html>




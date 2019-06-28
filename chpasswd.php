<!DOCTYPE html>
<?php
session_start();

function chpasswd()
{
    if (strcmp($_POST['new'], $_POST['repeat']) != 0)
    {
        $db->close();
        return 2;   // herhaal klopt niet
    }

    $db = new SQLite3("yinyang.sqlite3");
    $query = sprintf("SELECT password FROM xuser WHERE name=\"%s\";", $_SESSION['username']);
    $result = $db->query($query);
    $res = $result->fetchArray(1);
    $md5 = md5($_POST['current']);
    
    if (strcmp($md5, $res['password']) != 0)
    {
        $db->close();
        return 3;   // het wachtwoord dat de user invult komt niet overeen met huidig ww
    }

    $md5 = md5($_POST['new']);

    $query = sprintf("UPDATE xuser SET password=\"%s\" WHERE name=\"%s\";",
        $md5, $_SESSION['username']);

    $result = $db->query($query);
    $db->close();
    return 1;   // succes!
}

$ret = 0;

if (isset($_POST['change']))
    $ret = chpasswd();

?>
<html lang="en">
<head>
<title>Verander wachtwoord</title>
<style>
label
{
    display: inline-block;
    width: 200px;
}
</style>
<?php
if ($ret == 1)
    printf("<meta http-equiv=\"Refresh\" content=\"0;url=main.php\">\r\n");
?>
</head>
<body>
<form action="chpasswd.php" method="post">
<p>
<label for="idcurrent">Huidig wachtwoord</label>
<input type="password" id="idcurrent" name="current" required/>
</p>
<p>
<label for="idnew">Nieuw wachtwoord</label>
<input type="password" id="idnew" name="new" required/>
</p>
<p>
<label for="idrepeat">Herhaal wachtwoord</label>
<input type="password" id="idrepeat" name="repeat" required/>
</p>
<input type="submit" name="change" value="Change"/>
</form>
<a href="main.php">Cancel</a>
<?php
if ($ret == 3)
    printf("<p>Incorrect wachtwoord.</p>\r\n");

if ($ret == 2)
    printf("<p>Wachtwoorden komen niet overeen.</p>\r\n");
?>
</body>
</html>



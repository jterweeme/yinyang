<!DOCTYPE html>
<?php
session_start();

if (isset($_SESSION['admin']) && isset($_GET['action']))
{
    // nieuwe gebruiker
    if (strcmp($_GET['action'], "adduser") == 0)
    {
        $db = new SQLite3("yinyang.sqlite3");

        $query = sprintf("INSERT INTO xuser(name, password) VALUES (\"%s\", \"%s\");",
            $_GET['name'], md5($_GET['name']));

        $result = $db->query($query);
        $db->close();
    }

    // nieuwe groep
    if (strcmp($_GET['action'], "mkgroup") == 0)
    {
        $db = new SQLite3("yinyang.sqlite3");
        $query = sprintf("INSERT INTO xgroup(name) VALUES (\"%s\");", $_GET['groupname']);
        $result = $db->query($query);
        $db->close();
    }

    // voeg gebruiker toe aan groep
    if (strcmp($_GET['action'], "group") == 0)
    {
        $db = new SQLite3("yinyang.sqlite3");

        $query = sprintf("INSERT INTO member(groupname, username) VALUES (\"%s\", \"%s\");",
            $_GET['groupname'], $_GET['username']);

        $result = $db->query($query);
        $db->close();
    }

    // wijs examen toe aan groep
    if (strcmp($_GET['action'], "assign") == 0)
    {
        $db = new SQLite3("yinyang.sqlite3");

        $query = sprintf("INSERT INTO assignment(groupname, examfile) VALUES (\"%s\", \"%s\");",
            $_GET['groupname'], $_GET['fn']);

        $result = $db->query($query);
        $db->close();
    }

    // schrap examen
    if (strcmp($_GET['action'], "scrape") == 0)
    {
        $db = new SQLite3("yinyang.sqlite3");

        $query = sprintf("DELETE FROM assignment WHERE groupname=\"%s\" AND examfile=\"%s\";",
            $_GET['groupname'], $_GET['fn']);

        $result = $db->query($query);
        $db->close();
    }

    // trap gebruiker uit groep
    if (strcmp($_GET['action'], "kick") == 0)
    {
        $db = new SQLite3("yinyang.sqlite3");

        $query = sprintf("DELETE FROM member WHERE groupname=\"%s\" AND username=\"%s\";",
            $_GET['groupname'], $_GET['username']);

        $result = $db->query($query);
        $db->close();
    }

    // verwijder groep
    if (strcmp($_GET['action'], "rmgroup") == 0)
    {
        $db = new SQLite3("yinyang.sqlite3");
        $query = sprintf("DELETE FROM xgroup WHERE name=\"%s\";", $_GET['groupname']);
        $result = $db->query($query);
        $db->close();
    }

    // verwijder gebruiker
    if (strcmp($_GET['action'], "kill") == 0)
    {
        $db = new SQLite3("yinyang.sqlite3");
        $query = sprintf("DELETE FROM xuser WHERE name=\"%s\";", $_GET['username']);
        $result = $db->query($query);
        $db->close();
    }
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



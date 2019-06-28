<!DOCTYPE html>
<?php
/*
Jasper ter Weeme 2019
*/

session_start();

include 'common.php';

unset($_SESSION['q']);
unset($_SESSION['qcnt']);
unset($_SESSION['map']);
unset($_SESSION['fn']);
unset($_SESSION['ans_map']);
unset($_SESSION['array']);
unset($_SESSION['answers']);

if (!isset($_SESSION['username']))
{
    session_destroy();
    printf(redirect("login.php"));
    die();
}
?>
<html lang="en">
<head>
<link rel="stylesheet" type="text/css" href="common.css"/>
<title>Main</title>
</head>
<body>
<header>
<?php
printf("\t<a href=\"chpasswd.php\">Verander wachtwoord</a>\r\n");
printf("\t<a href=\"logout.php\">Log out %s</a>\r\n", $_SESSION['username']);
?>
</header>
<table>
<caption>Exams</caption>
<?php
$ar = scandir("exams");

foreach ($ar as $fn)
{
    $path = pathinfo($fn);

    if (strcmp($path['extension'], "xml") == 0)
    {

        printf("<tr>\r\n");
        printf("\t<td>%s</td>\r\n", $fn);
        $xml = simplexml_load_file("exams/" . $fn);
        printf("\t<td>%s</td>\r\n", $xml->title[0]);
        $qcnt = $xml->exercise->count();    // aantal vragen
        printf("\t<td>%u</td>\r\n", $qcnt);
        printf("\t<td><a href=\"startp.php?fn=%s\">Practice</a></td>\r\n", $fn);
        printf("\t<td><a href=\"start.php?fn=%s\">Exam</a></td>\r\n", $fn);
        printf("</tr>\r\n");
    }
}

printf("</table>\r\n");

// open database connectie
$handle = new SQLite3("yinyang.sqlite3");

if (isset($_SESSION['admin']))
{
    // users
    $query = "SELECT name FROM xuser";
    $users = $handle->query($query);
    printf("<table>\r\n<caption>Users</caption>\r\n");

    while ($row = $users->fetchArray(1))
    {
        printf("<tr>\r\n\t<td>%s</td>\r\n", $row['name']);
        printf("\t<td><a href=\"kill.php?username=%s\">Kill</a></td>\r\n", $row['name']);
        printf("</tr>\r\n");
    }

    printf("</table>\r\n");

    // add user functie
    printf("<form method=\"post\" action=\"adduser.php\">\r\n");
    printf("\t<input name=\"name\"/>\r\n");
    printf("\t<input type=\"submit\" value=\"Add\"/>\r\n");
    printf("</form>\r\n");

    // groups
    printf("<table>\r\n<caption>Groups</caption>\r\n");
    $query = "SELECT name FROM xgroup;";
    $groupnames = $handle->query($query);
    
    while ($row = $groupnames->fetchArray(1))
    {
        printf("<tr>\r\n");
        printf("\t<td>%s</td>\r\n", $row['name']);
        printf("\t<td><a href=\"#\">Delete</a></td>\r\n");
        printf("</tr>\r\n");
    }

    printf("</table>\r\n");

    // create group
    printf("<form action=\"addgroup.php\" method=\"post\">\r\n");
    printf("\t<input name=\"groupname\"/>\r\n");
    printf("\t<input name=\"creategroup\" type=\"submit\" value=\"Create\"/>\r\n");
    printf("</form>\r\n");

    // members
    printf("<table>\r\n<caption>Members</caption>\r\n");
    $query = "SELECT groupname, username FROM member";
    $result = $handle->query($query);

    while ($row = $result->fetchArray(1))
    {
        printf("<tr>\r\n");
        printf("\t<td>%s</td>\r\n", $row['groupname']);
        printf("\t<td>%s</td>\r\n", $row['username']);

        printf("\t<td><a href=\"kick.php?groupname=%s&username=%s\">Kick</a></td>\r\n",
                $row['groupname'], $row['username']);

        printf("</tr>\r\n");
    }

    printf("</table>\r\n");

    // assign user to group
    printf("<form action=\"addmember.php\" method=\"get\">\r\n");
    printf("<select name=\"groupname\">\r\n");
    $groupnames->reset();   // terug naar eerste row

    while ($row = $groupnames->fetchArray(1))
    {
        printf("\t<option>%s</option>\r\n", $row['name']);
    }

    printf("</select>\r\n");
    printf("<select name=\"username\">\r\n");
    $users->reset();        // terug naar eeste row
    
    while ($row = $users->fetchArray(1))
    {
        printf("\t<option>%s</option>\r\n", $row['name']);
    }

    printf("</select>\r\n");
    printf("<input type=\"submit\" value=\"Add\"/>\r\n");
    printf("</form>\r\n");

    // assignments
    printf("<table>\r\n<caption>Assignments</caption>\r\n");
    $query = "SELECT groupname, examfile FROM assignment";
    $result = $handle->query($query);

    while ($row = $result->fetchArray(1))
    {
        printf("<tr>\r\n");
        printf("\t<td>%s</td>\r\n", $row['groupname']);
        printf("\t<td>%s</td>\r\n", $row['examfile']);
        printf("\t<td><a href=\"#\">Delete</a></td>\r\n");
        printf("</tr>\r\n");
    }

    printf("</table>\r\n");
    printf("<form>\r\n");
    printf("<select>\r\n");
    $groupnames->reset();
    
    while ($row = $groupnames->fetchArray(1))
    {
        printf("<option>%s</option>\r\n", $row['name']);
    }

    printf("</select>\r\n<select>\r\n");
    $users->reset();

    foreach ($ar as $fn)
    {
        if (strcmp($fn, ".") == 0)
            continue;

        if (strcmp($fn, "..") == 0)
            continue;

        printf("<option>%s</option>\r\n", $fn);
    }

    printf("</select>\r\n");
    printf("<input type=\"submit\" value=\"Assign\"/>\r\n");
    printf("</form>\r\n");
    printf("<table>\r\n<caption>Results</caption>\r\n</table>");
}

printf("</body>\r\n</html>\r\n");
?>



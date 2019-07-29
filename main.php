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
unset($_SESSION['mode']);
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

printf("<html lang=\"en\">\r\n<head>\r\n");
printf("<link rel=\"Shortcut Icon\" href=\"yinyang.svg\"/>\r\n");
printf("<link rel=\"stylesheet\" type=\"text/css\" href=\"common.css\"/>\r\n");
printf("<title>Main</title>\r\n</head>\r\n<body>\r\n<header>\r\n");
printf("\t<a href=\"chpasswd.php\">Verander wachtwoord</a>\r\n");
printf("\t<a href=\"logout.php\">Log out %s</a>\r\n", $_SESSION['username']);
printf("</header>\r\n<table>\r\n<caption>Exams</caption>\r\n");
$ar = scandir("exams");

// open database connectie
$handle = new SQLite3("yinyang.sqlite3");

$query = sprintf("SELECT groupname FROM member WHERE username=\"%s\"", $_SESSION['username']);
$resultset = $handle->query($query);
$groups = [];

while ($row = $resultset->fetchArray(1))
    array_push($groups, $row['groupname']);

function toegang($fn)
{
    if (isset($_SESSION['admin']))
        return true;

    global $handle;
    $query = sprintf("SELECT groupname FROM assignment WHERE examfile=\"%s\"", $fn);
    $resultset = $handle->query($query);
    global $groups;

    while ($row = $resultset->fetchArray(1))
        if (in_array($row['groupname'], $groups, TRUE))
            return true;

    return false;
}

foreach ($ar as $fn)
{
    $path = pathinfo($fn);

    if (strcmp($path['extension'], "xml") != 0)
        continue;

    if (toegang($fn) == false)
        continue;

    $xml2 = new DOMDocument();
    $path = sprintf("exams/%s", $fn);
    $xml2->load($path);
    $valid = $xml2->schemaValidate("exam.xsd");

    if ($valid)
        printf("<tr>\r\n");
    else
        printf("<tr class=\"strike\">\r\n");

    printf("\t<td>%s</td>\r\n", $fn);
    $titleLmnt = $xml2->getElementsByTagName('title')->item(0);
    printf("\t<td>%s</td>\r\n", $titleLmnt->textContent);
    $exercises = $xml2->getElementsByTagName('exercise');
    printf("\t<td>%u</td>\r\n", $exercises->length);
    printf("\t<td>");
    printf("<a href=\"startp.php?fn=%s\">Practice</a>", $fn);
    printf("</td>\r\n");
    printf("\t<td><a href=\"start.php?fn=%s\">Exam</a></td>\r\n", $fn);

    if (isset($_SESSION['admin']))
    {
        printf("\t<td><a href=\"edit.php?fn=%s\">Edit</a></td>\r\n", $fn);
        printf("\t<td><a href=\"view.php?fn=%s\">View</a></td>\r\n", $fn);
    }

    printf("</tr>\r\n");
}

printf("</table>\r\n");

if (isset($_SESSION['admin']))
{
    printf("<form method=\"get\">\r\n");
    printf("<input value=\"*.xml\"/>\r\n");
    printf("<input type=\"submit\" value=\"New\"/>\r\n");
    printf("</form>\r\n");

    // users
    $query = "SELECT name FROM xuser";
    $users = $handle->query($query);
    printf("<table>\r\n<caption>Users</caption>\r\n");

    while ($row = $users->fetchArray(1))
    {
        printf("<tr>\r\n\t<td>%s</td>\r\n", $row['name']);

        printf("\t<td><a href=\"admin.php?action=kill&username=%s\">Kill</a></td>\r\n",
            $row['name']);

        printf("</tr>\r\n");
    }

    printf("</table>\r\n");

    // add user functie
    printf("<form method=\"get\" action=\"admin.php\">\r\n");
    printf("\t<input name=\"action\" type=\"hidden\" value=\"adduser\"/>\r\n");
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

        printf("\t<td><a href=\"admin.php?action=rmgroup&groupname=%s\">Delete</a></td>\r\n",
            $row['name']);

        printf("</tr>\r\n");
    }

    printf("</table>\r\n");

    // create group
    printf("<form action=\"admin.php\" method=\"get\">\r\n");
    printf("\t<input name=\"action\" value=\"mkgroup\" type=\"hidden\"/>\r\n");
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
        printf("\t<td>");

        printf("<a href=\"admin.php?action=kick&groupname=%s&username=%s\">Kick</a>",
                $row['groupname'], $row['username']);

        printf("</td>\r\n");
        printf("</tr>\r\n");
    }

    printf("</table>\r\n");

    // assign user to group
    printf("<form action=\"admin.php\" method=\"get\">\r\n");
    printf("<input name=\"action\" value=\"group\" type=\"hidden\"/>\r\n");
    printf("<select name=\"groupname\">\r\n");
    $groupnames->reset();   // terug naar eerste row

    while ($row = $groupnames->fetchArray(1))
        printf("\t<option>%s</option>\r\n", $row['name']);

    printf("</select>\r\n");
    printf("<select name=\"username\">\r\n");
    $users->reset();        // terug naar eeste row
    
    while ($row = $users->fetchArray(1))
        printf("\t<option>%s</option>\r\n", $row['name']);

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
        printf("\t<td>");

        printf("<a href=\"admin.php?action=scrape&groupname=%s&fn=%s\">Delete</a>",
            $row['groupname'], $row['examfile']);

        printf("</td>\r\n");
        printf("</tr>\r\n");
    }

    printf("</table>\r\n");

    // assign
    printf("<form action=\"admin.php\" method=\"get\">\r\n");
    printf("<input name=\"action\" value=\"assign\" type=\"hidden\"/>\r\n");
    printf("<select name=\"groupname\">\r\n");
    $groupnames->reset();
    
    while ($row = $groupnames->fetchArray(1))
        printf("\t<option>%s</option>\r\n", $row['name']);

    printf("</select>\r\n<select name=\"fn\">\r\n");
    $users->reset();

    foreach ($ar as $fn)
    {
        if (strcmp($fn, ".") == 0 || strcmp($fn, "..") == 0)
            continue;

        printf("\t<option>%s</option>\r\n", $fn);
    }

    printf("</select>\r\n");
    printf("<input type=\"submit\" value=\"Assign\"/>\r\n");
    printf("</form>\r\n");
    printf("<table>\r\n<caption>Results</caption>\r\n");

    $ar = scandir("results");

    foreach ($ar as $fn)
    {
        if (strcmp($fn, ".") == 0 || strcmp($fn, "..") == 0)
            continue;

        printf("<tr>\r\n");
        printf("<td><a href=\"result.php?fn=%s\">%s</a></td>\r\n", $fn, $fn);

        $path = sprintf("results/%s", $fn);
        $xml = new DOMDocument();
        $xml->load($path);
        $exam = $xml->getElementsByTagName("exam")->item(0);
        printf("<td>%s</td>\r\n", $exam->textContent);
        $score = $xml->getElementsByTagName("score")->item(0);
        printf("<td>%s</td>\r\n", $score->textContent);

        printf("</tr>\r\n");
    }

    printf("</table>\r\n");
}

printf("</body>\r\n</html>\r\n");
?>



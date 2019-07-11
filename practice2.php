<?php
session_start();

$q = $_SESSION['q'];    // nummer van vraag

// registreer gegeven antwoord van vorige vraag in $_SESSION
if (isset($_POST['choice']))
{
    $a = $_POST['choice'];
    $_SESSION['answers'][$q] = $a;
}

if (isset($_POST['button']))
{
    if (strcmp($_POST['button'], "Next") == 0)
        $_SESSION['q']++;
    
    if (strcmp($_POST['button'], "Previous") == 0)
        $_SESSION['q']--;
}

$q = $_SESSION['q'];    // nummer van vraag
$path = sprintf("exams/%s", $_SESSION['fn']);
$xml = simplexml_load_file($path);
$qx = $_SESSION['map'][$q]; // nummer van vraag in XML
$exercise = $xml->exercise[$qx];
$qtype = $exercise['type'];

printf("<!DOCTYPE html>\r\n");
printf("<html lang=\"en\">\r\n<head>\r\n<title>Practice</title>\r\n");
printf("<link rel=\"stylesheet\" type=\"text/css\" href=\"common.css\"/>\r\n");
printf("<script type=\"text/javascript\" src=\"practice2.js\"></script>\r\n");
printf("</head>\r\n<body>\r\n");

// header
printf("<header>\r\n");
printf("<a href=\"main.php\">End practice</a>\r\n");
printf("<a href=\"logout.php\">Log out %s</a>\r\n", $_SESSION['username']);
printf("</header>\r\n");

// progress
printf("<p>%u/%u <progress max=\"%u\" value=\"%u\">%u/%u</progress></p>\r\n",
    $q + 1, $_SESSION['qcnt'], $_SESSION['qcnt'], $q + 1, $q + 1, $_SESSION['qcnt']);

printf("<h1>Practice</h1>\r\n");
printf("<div>\r\n%s\r\n</div>\r\n", $exercise->vraag->asXML());
printf("<form method=\"post\">\r\n");

if (strcmp($qtype, "single") == 0)
{
    foreach ($_SESSION['ans_map'][$q] as $n)
    {
        $item = $exercise->choice->item[$n];
        printf("<p>\r\n");
        $checked = "";

        if (isset($_SESSION['answers'][$q]))
            if ($_SESSION['answers'][$q] == $n)
                $checked = "checked";

        printf("<input type=\"radio\" value=\"%u\" name=\"choice\" %s/>\r\n", $n, $checked);
        printf("<label>%s</label>\r\n</p>\r\n", $item);
    }
}

if (strcmp($qtype, "multi") == 0)
{
    $n = 0;
    foreach ($exercise->choice->item as $item)
    {
        printf("<p>\r\n<input type=\"checkbox\" value=\"%u\" name=\"%u\"/>\r\n", $n, $n);
        printf("<label>%s</label>\r\n</p>\r\n", $item);
        $n++;
    }
}

if (strcmp($qtype, "dragdrop") == 0)
{
    // draggable items
    printf("<ul class=\"dragdrop\">\r\n");
    $n = 0;

    foreach ($exercise->ol->li as $li)
    {
        $str = sprintf("<p id=\"div%u\" ondragstart=\"drag(event)\" draggable=\"true\">%s</p>\r\n",
            $n, $li);

        printf("<li ondrop=\"drop(event)\" ondragover=\"allowDrop(event)\">\r\n%s</li>\r\n",
            $str);

        $n++;
    }
    printf("</ul>\r\n");

    // empty cells
    printf("<ol class=\"dragdrop\">\r\n");
    $n = 0;

    foreach ($exercise->ol->li as $li)
    {
        printf("<li id=\"li%u\" ondrop=\"drop(event)\" ondragover=\"allowDrop(event)\"></li>\r\n", $n);
        $n++;
    }
    printf("</ol>\r\n");
}

$disabled = "";

if ($q == 0)
    $disabled = "disabled";     // eerste vraag, disable prev button

printf("<input type=\"submit\" formaction=\"practice2.php\" ".
    "name=\"button\" value=\"Previous\" %s/>\r\n", $disabled);

printf("<input type=\"submit\" formaction=\"answer2.php\" value=\"Antwoord\"/>\r\n");
$disabled = "";

if ($q == $_SESSION['qcnt'] - 1)
    $disabled = "disabled";     // laatste vraag, disable next button

printf("<input type=\"submit\" formaction=\"practice2.php\" ".
    "name=\"button\" value=\"Next\" %s/>\r\n", $disabled);

printf("</form>\r\n");
/*
printf("<pre>\r\n");
print_r($_SESSION);
printf("</pre>\r\n");
*/
printf("</body>\r\n</html>\r\n");
?>



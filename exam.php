<!DOCTYPE html>
<?php
session_start();

include 'common.php';

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

    if (strcmp($_POST['button'], "Finish") == 0)
    {
        printf(redirect("end.php"));
        die();
    }
}

$q = $_SESSION['q'];
$path = sprintf("exams/%s", $_SESSION['fn']);
$xml = simplexml_load_file($path);
$qx = $_SESSION['map'][$q]; // nummer van vraag in XML
$exercise = $xml->exercise[$qx];
$qtype = $xml->exercise['type'];
?>
<html lang="en">
<head>
<title>Practice</title>
<link rel="stylesheet" type="text/css" href="common.css"/>
</head>
<body>
<?php
//print_r($_SESSION);

printf("<header>\r\n");
printf("<a href=\"main.php\">End exam</a>\r\n");
printf("<a href=\"logout.php\">Log out %s</a>", $_SESSION['username']);
printf("</header>\r\n");

// progress
printf("<p>%u/%u <progress max=\"%u\" value=\"%u\">%u/%u</progress></p>\r\n",
    $q + 1, $_SESSION['qcnt'], $_SESSION['qcnt'], $q + 1, $q + 1, $_SESSION['qcnt']);

printf("<h1>Exam</h1>\r\n");
printf("<p>\r\n%s\r\n</p>\r\n", $exercise->vraag->asXML());
printf("<form method=\"post\">\r\n");

if (strcmp($qtype, "single") == 0)
{
    foreach ($_SESSION['ans_map'][$q] as $n)
    {
        $item = $exercise->choice->item[$n];
        printf("<p>\r\n");
        $checked = "";

        if (isset($_SESSION['answers'][$q]))
            if ($_SESSION['answers'][$q] == $n + 1)
                $checked = "checked";

        printf("<input id=\"r%u\" type=\"radio\" value=\"%u\" name=\"choice\" %s/>\r\n",
            $n + 1, $n + 1, $checked);

        printf("<label for=\"r%u\">%s</label>\r\n</p>\r\n", $n + 1, $item);
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

$disabled = "";

if ($q == 0)
    $disabled = "disabled";     // eerste vraag, disable prev button

printf("<input type=\"submit\" formaction=\"exam.php\" ".
    "name=\"button\" value=\"Previous\" %s/>\r\n", $disabled);

$disabled = "";

if ($q == $_SESSION['qcnt'] - 1)
    $disabled = "disabled";     // laatste vraag, disable next button

printf("<input type=\"submit\" formaction=\"exam.php\" ".
    "name=\"button\" value=\"Next\" %s/>\r\n", $disabled);

printf("<input type=\"submit\" formaction=\"exam.php\" ".
    "name=\"button\" value=\"Finish\" />\r\n");

printf("</form>\r\n</body>\r\n</html>\r\n");
?>




<!DOCTYPE html>
<?php
/*
Jasper ter Weeme 2019
*/
session_start();

$q = $_SESSION['q'];    // huidig vraagnummer
$qx = $_SESSION['map'][$q]; // huidige vraagnummer in xml
$xml = simplexml_load_file("exams/" . $_SESSION['fn']);
$exercise = $xml->exercise[$qx];
$qtype = $exercise['type'];

// registreer gegeven antwoord van vorige vraag in $_SESSION
if (isset($_POST['choice']))
{
    $a = $_POST['choice'];  // ingevulde antwoord
    $_SESSION['answers'][$q] = $a;
}

printf("<html lang=\"en\">\r\n<head>\r\n");
printf("<link rel=\"stylesheet\" type=\"text/css\" href=\"common.css\"/>\r\n");
printf("</head>\r\n<body>\r\n");

// header
printf("<header>\r\n<a href=\"main.php\">End practice</a>\r\n");
printf("<a href=\"logout.php\">Log out %s</a>", $_SESSION['username']);
printf("</header>\r\n");

// progress
printf("<p>%u/%u <progress max=\"%u\" value=\"%u\">%u/%u</progress></p>\r\n",
    $q + 1, $_SESSION['qcnt'], $_SESSION['qcnt'], $q + 1, $q + 1, $_SESSION['qcnt']);

printf("<h1>Answer</h1>\r\n");
printf("<p>%s</p>\r\n", $xml->exercise[$qx]->vraag->asXML());
printf("<form method=\"post\">\r\n");

if (strcmp($qtype, "single") == 0)
{
    foreach ($_SESSION['ans_map'][$q] as $n)
    {
        $item = $exercise->choice->item[$n];
        $checked = "";

        if ($n == $a)
            $checked = "checked";

        printf("<p>\r\n<input disabled type=\"radio\" %s/>\r\n", $checked);

        if (isset($exercise->choice->item[$n]['goed']))
            printf("<label class=\"groen\">");
        else if ($n == $a)
            printf("<label class=\"rood\">");
        else
            printf("<label>");

        printf("%s</label>\r\n</p>\r\n", $item);
    }
}
if (strcmp($qtype, "multi") == 0)
{
    $n = 0;
    foreach ($exercise->choice->item as $item)
    {
        $inptype = "radio";

        if (strcmp($exercise['type'], "multi") == 0)
            $inptype = "checkbox";

        $checked = "";

        if (isset($_POST[$n]))
            $checked = "checked";

        printf("<p>\r\n<input disabled type=\"%s\" %s/>\r\n", $inptype, $checked);

        if (isset($exercise->choice->item[$n]['goed']))
            printf("<label class=\"groen\">");
        else if (isset($_POST[$n]))
            printf("<label class=\"rood\">");
        else
            printf("<label>");

        printf("%s</label>\r\n</p>\r\n", $item);
        $n++;
    }
}
printf("<div class=\"toelichting\">%s</div>\r\n", $exercise->toelichting->asXML());
$disabled = "";

if ($q == 0)
    $disabled = "disabled";     // eerste vraag, disable prev button

printf("<input type=\"submit\" formaction=\"practice2.php\" ".
    "name=\"button\" value=\"Previous\" %s/>\r\n", $disabled);

printf("<input type=\"submit\" formaction=\"answer2.php\" value=\"Antwoord\" disabled/>\r\n");
$disabled = "";

if ($q == $_SESSION['qcnt'] - 1)
    $disabled = "disabled";     // laatste vraag, disable next button

printf("<input type=\"submit\" formaction=\"practice2.php\" ".
    "name=\"button\" value=\"Next\" %s/>\r\n", $disabled);

printf("</form>\r\n</body>\r\n</html>\r\n");
?>



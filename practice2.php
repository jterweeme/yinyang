<!DOCTYPE html>
<?php
session_start();

include 'common.php';

$q = $_SESSION['q'];    // nummer van vraag
$path = sprintf("exams/%s", $_SESSION['fn']);
$xml = simplexml_load_file($path);
$qx = $_SESSION['map'][$q]; // nummer van vraag in XML
$exercise = $xml->exercise[$qx];
$qtype = $exercise['type'];

// registreer gegeven antwoord van vorige vraag in $_SESSION
if (strcmp($qtype, "multi") == 0)
{
    $n = count($_SESSION['answers'][$q]);
    
    for ($i = 0; $i < $n; $i++)
    {
        $foo = sprintf("%d", $i);
        if (isset($_POST[$foo]))
            $_SESSION['answers'][$q][$i] = 1;
    }
}

if (strcmp($qtype, "single") == 0)
{
    if (isset($_POST['choice']))
    {
        $a = $_POST['choice'];
        $_SESSION['answers'][$q] = $a;
    }
}

if (strcmp($qtype, "open") == 0)
{
    if (isset($_POST['antwoord']))
    {
        $_SESSION['answers'][$q] = $_POST['antwoord'];
    }
}

if (strcmp($qtype, "dragdrop") == 0)
{
    $keys = array();
    
    foreach ($exercise->drag->choice->item as $item)
        array_push($keys, $item['xid']->__toString());

    foreach ($_POST as $key => $value)
    {
        if (in_array($key, $keys))
        {
            $_SESSION['answers'][$q][$key] = $value;
        }
    }
}

// zet de vraagteller goed
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

$q = $_SESSION['q'];    // nummer van nieuwe vraag
$qx = $_SESSION['map'][$q]; // nummer van nieuwe vraag in XML
$exercise = $xml->exercise[$qx];
$qtype = $exercise['type'];

printf("<html lang=\"en\">\r\n<head>\r\n<title>Practice</title>\r\n");
printf("<link rel=\"stylesheet\" type=\"text/css\" href=\"common.css\"/>\r\n");

if (strcmp($qtype, "dragdrop") == 0)
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
printf("<div>\r\n%s\r\n</div>\r\n", innerCode($exercise->vraag, "vraag"));
printf("<form method=\"post\">\r\n");

// single antwoord vragen
if (strcmp($qtype, "single") == 0)
{
    printf("\t<input type=\"hidden\" name=\"xtype\" value=\"single\"/>\r\n");

    foreach ($_SESSION['ans_map'][$q] as $n)
    {
        $item = $exercise->choice->item[$n];
        printf("\t<p>\r\n");
        $checked = "";

        if (isset($_SESSION['answers'][$q]))
            if ($_SESSION['answers'][$q] == $n + 1)
                $checked = "checked";

        printf("\t<input id=\"r%u\" type=\"radio\" value=\"%u\" name=\"choice\" %s/>\r\n",
            $n + 1, $n + 1, $checked);
        // de $n + 1 maakt het mogelijk om een 0 waarde over te laten voor blanco antwoord

        printf("\t<label for=\"r%u\">%s</label>\r\n\t</p>\r\n", $n + 1, $item);
    }
}

// multiple antwoord vragen
if (strcmp($qtype, "multi") == 0)
{
    printf("\t<input type=\"hidden\" name=\"xtype\" value=\"multi\"/>\r\n");
    $n = 0;
    foreach ($exercise->choice->item as $item)
    {
        $checked = "";
        if ($_SESSION['answers'][$q][$n] == 1)
            $checked = "checked";

        printf("\t<p>\r\n");

        printf("\t<input type=\"checkbox\" id=\"c%d\" value=\"%u\" name=\"%u\" %s/>\r\n",
            $n, $n, $n, $checked);

        printf("\t<label for=\"c%d\">%s</label>\r\n\t</p>\r\n", $n, $item);
        $n++;
    }
}

// open vragen
if (strcmp($qtype, "open") == 0)
{
    printf("\t<p><input name=\"antwoord\" value=\"%s\"/></p>\r\n", $_SESSION['answers'][$q]);
}

// dragdrop vragen
function getDescFromId($id)
{
    global $exercise;
    
    foreach ($exercise->drag->choice->item as $item)
        if (strcmp($item['xid'], $id) == 0)
            return $item->__toString();
    
    return "";
}

function getItemFromId($id)
{
    global $q;

    foreach ($_SESSION['answers'][$q] as $key => $value)
    {
        if ($value == $id)
        {
            return sprintf("<p id=\"%s\" class=\"drag\">%s</p>", $key, getDescFromId($key));
        }
    }
    return "";
}

if (strcmp($qtype, "dragdrop") == 0)
{
    // "linkerkant"
    printf("\t<ul class=\"dragdrop\">\r\n");

    $n_items = $exercise->drag->choice->item->count();
    $nr_li = 0;
    
    for ($i = 0; $i < $n_items; $i++)
    {
        printf("\t\t<li id=\"li%d\" class=\"drop\">%s</li>\r\n",
            $nr_li, getItemFromId($nr_li));

        $nr_li++;
    }

    printf("\t</ul>\r\n");

    // "rechterkant"
    foreach ($exercise->drag->drop->ul as $ul)
    {
        printf("\t<ul class=\"dragdrop\">\r\n");
        
        foreach ($ul->li as $li)
        {
            printf("\t\t<li id=\"li%u\" class=\"drop\">%s</li>\r\n",
                $nr_li, getItemFromId($nr_li));

            $nr_li++;
        }

        printf("\t</ul>\r\n");
    }

    // hidden items
    foreach ($exercise->drag->choice->item as $item)
    {
        $xid = $item['xid'];
        $slot = $_SESSION['answers'][$q][$xid->__toString()];

        printf("\t<input class=\"answer\" type=\"hidden\" name=\"%s\" value=\"%d\"/>\r\n",
            $xid->__toString(), $slot);
    }
}

$disabled = "";
if ($q == 0)
    $disabled = "disabled";     // eerste vraag, disable prev button

// previous button
printf("\t<input type=\"submit\" formaction=\"practice2.php\" ".
    "name=\"button\" value=\"Previous\" %s/>\r\n", $disabled);

// antwoord button
printf("\t<input type=\"submit\" formaction=\"answer2.php\" value=\"Antwoord\"/>\r\n");

$disabled = "";
if ($q == $_SESSION['qcnt'] - 1)
    $disabled = "disabled";     // laatste vraag, disable next button

printf("\t<input type=\"submit\" formaction=\"practice2.php\" ".
    "name=\"button\" value=\"Next\" %s/>\r\n", $disabled);

$disabled = "disabled";
if (strcmp($_SESSION['mode'], "exam") == 0)
    $disabled = "";

printf("\t<input type=\"submit\" formaction=\"practice2.php\" ".
    "name=\"button\" value=\"Finish\" %s/>\r\n", $disabled);

printf("</form>\r\n");

// debug
printf("<pre>\r\n");
/*
if (isset($exercise))
    print_r($exercise);
*/
printf("\$_POST\r\n");
print_r($_POST);
print_r($_SESSION);
printf("</pre>\r\n");

printf("</body>\r\n</html>\r\n");
?>



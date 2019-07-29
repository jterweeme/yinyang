<?php
session_start();
printf("<!DOCTYPE html>\r\n");
/*
initialiseert de test
selecteerd vragen
*/

include 'common.php';

if (!isset($_GET['fn']))
{
    printf(redirect("main.php"));
    die();
}

$_SESSION['q'] = 0;                 // vraagteller, eerste vraag
$_SESSION['fn'] = $_GET['fn'];      // filename
$_SESSION['mode'] = "practice";
$path = sprintf("exams/%s", $_SESSION['fn']);
$xml = simplexml_load_file($path);
$qcnt = $xml->exercise->count();    // aantal vragen in xml file
$_SESSION['qcnt'] = $qcnt;
$map = range(0, $qcnt - 1);
shuffle($map);
$_SESSION['map'] = $map;
$_SESSION['answers'] = array_fill(0, $qcnt, 0);

$n = 0;
$tags = array();
foreach ($map as $foo)
{
    $exercise = $xml->exercise[$foo];

    if (isset($exercise->tags))
    {
        foreach ($exercise->tags->tag as $tag)
        {
            $x = $tag[0]->__toString();
    
            if (!isset($tags[$x]))
                $tags[$x] = 0;
    
            $tags[$x]++;
        }
    }

    // single antwoord vragen
    if (strcmp($exercise['type'], "single") == 0)
    {
        if (isset($exercise->choice))
        {
            $n_items = $exercise->choice->item->count();    // number of items
            $arr = range(0, $n_items - 1);
            shuffle($arr);
            $_SESSION['ans_map'][$n] = $arr;
            $_SESSION['answers'][$n] = 0;
        }
    }

    // multi antwoord vragen
    if (strcmp($exercise['type'], "multi") == 0)
    {
        $n_items = $exercise->choice->item->count();
        $arr = range(0, $n_items - 1);
        shuffle($arr);
        $_SESSION['ans_map'][$n] = $arr;
        $_SESSION['answers'][$n] = array_fill(0, $n_items, 0);
    }

    // open vragen
    if (strcmp($exercise['type'], "open") == 0)
    {
        $_SESSION['answers'][$n] = "";
    }

    // drag drop vragen
    if (strcmp($exercise['type'], "dragdrop") == 0)
    {
        $_SESSION['answers'][$n] = array();
        $p = 0;
        foreach ($exercise->drag->choice->item as $item)
        {
            $xid = $item['xid']->__toString();
            $_SESSION['answers'][$n][$xid] = $p;
            $p++;
        }
    }

    $n++;
}

printf("<html lang=\"en\">\r\n<head>\r\n<title>Start Practice</title>\r\n");
printf("<link rel=\"stylesheet\" type=\"text/css\" href=\"common.css\"/>\r\n");
printf("</head>\r\n<body>\r\n<header>\r\n<a href=\"main.php\">End exam</a>\r\n");
printf("<a href=\"logout.php\">Log out %s</a>\r\n", $_SESSION['username']);
printf("</header>\r\n<h1>Start Practice</h1>\r\n");
printf("<h2>%u vragen</h2>\r\n", $qcnt);
printf("<a href=\"practice2.php\">Start</a>\r\n");

// tags
if (!empty($tags))
{
    $maxtags = max($tags);
    printf("<table>\r\n<caption>Tags</caption>\r\n");
    
    foreach ($tags as $tag => $cnt)
    {
        printf("<tr>\r\n");
        printf("<td>%s</td>\r\n", $tag);
        printf("<td>%u</td>\r\n", $cnt);
    
        printf("<td><meter value=\"%u\" max=\"%u\">%u/%u</meter></td>\r\n",
            $cnt, $maxtags, $cnt, $maxtags);
    
        printf("</tr>\r\n");
    }
    
    printf("</table>\r\n");
}

// debug
printf("<pre>\r\n");
print_r($_SESSION);
printf("</pre>\r\n");

printf("</body>\r\n</html>\r\n");
?>



<!DOCTYPE html>
<?php
session_start();
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

$_SESSION['q'] = 0;             // vraagteller, eerste vraag
$_SESSION['fn'] = $_GET['fn'];  // filename
$path = sprintf("exams/%s", $_SESSION['fn']);
$xml = simplexml_load_file($path);
$qcnt = $xml->exercise->count();    // aantal vragen in xml file
$_SESSION['qcnt'] = $qcnt;
$map = range(0, $qcnt - 1);
shuffle($map);
$_SESSION['map'] = $map;

$n = 0;
foreach ($map as $foo)
{
    $exercise = $xml->exercise[$foo];
    $n_items = $exercise->choice->item->count();    // number of items
    $arr = range(0, $n_items - 1);
    shuffle($arr);
    $_SESSION['ans_map'][$n] = $arr;
    $n++;
}

printf("<html>\r\n<head>\r\n<title>Start</title>\r\n");
printf("<link rel=\"stylesheet\" type=\"text/css\" href=\"common.css\"/>\r\n");
printf("</head>\r\n<body>\r\n<header>\r\n<a href=\"main.php\">End exam</a>\r\n");

?>
<a href="logout.php">
<?php
printf("Log out %s", $_SESSION['username']);
?>
</a>
</header>
<h1>Exam</h1>
<?php

printf("<h2>%u vragen</h2>\r\n", $qcnt);

/*
printf("<pre>\r\n");
print_r($map);
printf("</pre>\r\n");
*/
?>
<a href="exam.php">Start</a>
</body>
</html>



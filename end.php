<!DOCTYPE html>
<?php
session_start();
$path = sprintf("exams/%s", $_SESSION['fn']);
$xml = simplexml_load_file($path);

$n_goed = 0;
$n_fout = 0;
$map = $_SESSION['map'];
$qcnt = $_SESSION['qcnt'];

// returns correct option index from xml ordering
function idxGoed($choices)
{
    $foo = 0;
    foreach ($choices->item as $choice)
    {
        if (isset($choice['goed']))
            return $foo;

        $foo++;
    }
}

$n = 0;
foreach ($map as $foo)
{
    $exercise = $xml->exercise[$foo];
    
    if ($_SESSION['answers'][$n] == idxGoed($exercise->choice))
        $n_goed++;
    else
        $n_fout++;
    
    $n++;
}

// write results file
$path = sprintf("results/%s.xml", $_SESSION['username']);
$fp = fopen($path, 'w');
fwrite($fp, "<?xml version=\"1.0\"?>\n");
fwrite($fp, "<result>\n");
$tmp = sprintf("<user>%s</user>\n", $_SESSION['username']);
fwrite($fp, $tmp);
$tmp = sprintf("<score>%u/%u</score>\n", $n_goed, $qcnt);
fwrite($fp, $tmp);

$n = 0;
foreach ($map as $foo)
{
    $exercise = $xml->exercise[$foo];
    fwrite($fp, "<exercise type=\"single\">\n");
    fwrite($fp, $exercise->vraag->asXML());
    $xidxGoed = idxGoed($exercise->choice);
    $goed = 0;

    if ($_SESSION['answers'][$n] == $xidxGoed)
    {
        $goed = 1;
        fwrite($fp, "\n<goed />\n");
    }
    else
    {
        fwrite($fp, "\n<fout />\n");
    }

    fwrite($fp, "<choice>\n");
    
    foreach ($_SESSION['ans_map'][$n] as $tmp)
    {
        $item = $exercise->choice->item[$tmp];

        if ($tmp == $xidxGoed && $goed == 1)
            fwrite($fp, "<item goed=\"ja\" checked=\"checked\">");
        else if ($tmp == $xidxGoed)
            fwrite($fp, "<item goed=\"ja\">");
        else if ($_SESSION['answers'][$n] == $tmp)
            fwrite($fp, "<item checked=\"checked\">");
        else
            fwrite($fp, "<item>");

        fwrite($fp, $item);
        fwrite($fp, "</item>\n");
    }

    fwrite($fp, "</choice>\n");
    fwrite($fp, "</exercise>\n\n");
    $n++;
}

fwrite($fp, "</result>\n");
fclose($fp);

// html
printf("<html lang=\"en\">\r\n<head>\r\n<title>Results</title>\r\n</head>\r\n");
printf("<body>\r\n");

// header
printf("<header>\r\n<a href=\"main.php\">Main menu</a>\r\n");
printf("<a href=\"logout.php\">Log out %s</a>", $_SESSION['username']);
printf("</header>\r\n");

printf("<h1>Results</h1>\r\n");
$strgoed = sprintf("<p>Goed: %s</p>\r\n", $n_goed);
$strfout = sprintf("<p>Fout: %s</p>\r\n", $n_fout);
printf($strgoed);
printf($strfout);

printf("<progress max=\"%u\" value=\"%u\">%u/%u</progress>\r\n",
    $qcnt, $n_goed, $n_goed, $qcnt);

printf("</pre>\r\n</body>\r\n</html>\r\n");
?>



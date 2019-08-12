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
    
    if (strcmp($exercise['type'], "single") == 0)
    {
        if ($_SESSION['answers'][$n] == idxGoed($exercise->choice) + 1)
            $n_goed++;
        else
            $n_fout++;
    }

    if (strcmp($exercise['type'], "multi") == 0)
    {
        $nx = 0;
        $checked_goed = 0;
        $checked_fout = 0;

        foreach ($exercise->choice->item as $item)
        {
            if (isset($item['goed']))
            {
                if ($_SESSION['answers'][$n][$nx] == 1) // door kandidaat aangevinkt
                    $checked_goed++;
                else
                    $checked_fout++;
            }
            else
            {
                if ($_SESSION['answers'][$n][$nx] == 1) // door kandidaat aangevinkt
                    $checked_fout++;
            }
            $nx++;
        }
        
        if ($checked_fout > 0)
            $n_fout++;
        else
            $n_goed++;
    }

    if (strcmp($exercise['type'], "open") == 0)
    {
        $cmp = strcmp($_SESSION['answers'][$n], $exercise->answer->__toString());

        if ($cmp == 0)
            $n_goed++;
        else
            $n_fout++;
    }

    if (strcmp($exercise['type'], "dragdrop") == 0)
    {
        $ddfout = 0;
        $n_ul = 4;

        foreach ($exercise->drag->drop->ul as $ul)
        {
            foreach ($ul->li as $li)
            {
                $answered = $_SESSION['answers'][$n][$li['ref']->__toString()];
                //printf("%u\r\n", $answered);

                if ($answered != $n_ul)
                    $ddfout = 1;
            }
            $n_ul++;
        }
        if ($ddfout == 1)
            $n_fout++;
        else
            $n_goed++;
    }

    $n++;
}

$date = date("Y-m-d");

$path = "";
$nr = 0;

while (true)
{
    $path = sprintf("results/%s-%s-%d.xml", $_SESSION['username'], $date, $nr);

    if (!file_exists($path))
        break;

    $nr++;
}

// write results file
//$path = sprintf("results/%s-%s.xml", $_SESSION['username'], $date);
$fp = fopen($path, 'w');
fwrite($fp, "<?xml version=\"1.0\"?>\n");
fwrite($fp, "<exam>\n");
$tmp = sprintf("<user>%s</user>\n", $_SESSION['username']);
fwrite($fp, $tmp);
$tmp = sprintf("<source>%s</source>\n", $_SESSION['fn']);
fwrite($fp, $tmp);
$tmp = sprintf("<date>%s</date>\n", $date);
fwrite($fp, $tmp);
$tmp = sprintf("<score>%u/%u</score>\n", $n_goed, $qcnt);
fwrite($fp, $tmp);

$n = 0;
foreach ($map as $foo)
{
    $exercise = $xml->exercise[$foo];
    $tmp = sprintf("<exercise type=\"%s\">\n", $exercise['type']->__toString());
    fwrite($fp, $tmp);
    fwrite($fp, $exercise->vraag->asXML());

    if (strcmp($exercise['type'], "single") == 0)
    {
        $xidxGoed = idxGoed($exercise->choice);

        $goed = 0;
        if (($_SESSION['answers'][$n] - 1) == $xidxGoed)
            $goed = 1;

        fwrite($fp, "<choice>\n");
    
        foreach ($_SESSION['ans_map'][$n] as $tmp)
        {
            $item = $exercise->choice->item[$tmp];

            if ($tmp == $xidxGoed && $_SESSION['answers'][$n] == $tmp + 1)
                fwrite($fp, "<item goed=\"ja\" checked=\"checked\">");
            else if ($tmp == $xidxGoed)
                fwrite($fp, "<item goed=\"ja\">");
            else if ($_SESSION['answers'][$n] == $tmp + 1)
                fwrite($fp, "<item checked=\"checked\">");
            else
                fwrite($fp, "<item>");

            fwrite($fp, htmlspecialchars($item));
            fwrite($fp, "</item>\n");
        }

        fwrite($fp, "</choice>\n");
    }

    if (strcmp($exercise['type'], "multi") == 0)
    {
        $nx = 0;
        $checked_goed = 0;
        $checked_fout = 0;
        fwrite($fp, "\n<choice>\n");
        
        foreach ($exercise->choice->item as $item)
        {
            $str_goed = "";
            $str_checked = "";

            if (isset($item['goed']))
                $str_goed = " goed=\"ja\"";

            if ($_SESSION['answers'][$n][$nx] == 1) // door kandidaat aangevinkt
                $str_checked = " checked=\"checked\"";

            $str = sprintf("<item%s%s>%s</item>\n", $str_goed, $str_checked, $item->toString());
            fwrite($fp, $str);
            $nx++;
        }

        fwrite($fp, "\n</choice>\n";
    }

/*
    if (strcmp($exercise['type'], "multi") == 0)
    {
        $nx = 0;
        $checked_goed = 0;
        $checked_fout = 0;
        fwrite($fp, "\n<choice>\n");

        foreach ($exercise->choice->item as $item)
        {
            if (isset($item['goed']))
            {
                if ($_SESSION['answers'][$n][$nx] == 1) // door kandidaat aangevinkt
                {
                    $str = sprintf("<item checked=\"checked\" goed=\"ja\">%s</item>\n",
                        $item->__toString());
                }
                else
                {
                    $str = sprintf("<item goed=\"ja\">%s</item>\n",
                        $item->__toString());
                }
            }
            else
            {
                if ($_SESSION['answers'][$n][$nx] == 1) // door kandidaat aangevinkt
                {
                    $str = sprintf("<item checked=\"checked\">%s</item>\n", $item->__toString());
                }
                else
                {
                    $str = sprintf("<item>%s</item>\n", $item->__toString());
                }
            }

            fwrite($fp, $str);
            $nx++;
        }

        fwrite($fp, "\n</choice>\n");
    }
*/

    if (strcmp($exercise['type'], "open") == 0)
    {
        $str = sprintf("<answer>%s</answer>\n", $_SESSION['answers'][$n]);
        fwrite($fp, $str);
    }

    if (strcmp($exercise['type'], "dragdrop") == 0)
    {
    }
    
    if (isset($exercise->toelichting))
    {
        fwrite($fp, $exercise->toelichting->asXML());
    }
    fwrite($fp, "</exercise>\n\n");
    $n++;
}

fwrite($fp, "</exam>\n\n\n");
fclose($fp);

// html
printf("<html lang=\"en\">\r\n<head>\r\n");
printf("<meta name=\"viewport\" content=\"width=device-width\"/>\r\n");
printf("<title>Results</title>\r\n</head>\r\n");
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

printf("</pre>\r\n");

/*
printf("<pre>\r\n");
print_r($_SESSION);
printf("</pre>\r\n");
*/

printf("</body>\r\n</html>\r\n");
?>



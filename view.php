<!DOCTYPE html>
<?php
include 'common.php';

session_start();

if (!isset($_SESSION['admin']))
{
    session_destroy();
    printf(redirect("login.php"));
    die();
}

printf("<html lang=\"en\">\r\n");
printf("<head>\r\n");
printf("<link rel=\"Shortcut Icon\" href=\"yinyang.svg\"/>\r\n");
printf("<link rel=\"stylesheet\" type=\"text/css\" href=\"common.css\"/>\r\n");
printf("<meta charset=\"UTF-8\"/>\r\n");
printf("<title>View</title>\r\n");
printf("</head>\r\n<body>\r\n");

// header
printf("<header>\r\n");
printf("<a href=\"main.php\">Main Menu</a>\r\n");
printf("<a href=\"logout.php\">Log out %s</a>\r\n", $_SESSION['username']);
printf("</header>\r\n");

$type = "view";
if (isset($_GET['type']))
    if (strcmp($_GET['type'], "result") == 0)
        $type = "result";

$dir = "exams";
if (strcmp($type, "result") == 0)
    $dir = "results";

$path = sprintf("%s/%s", $dir, $_GET['fn']);
$xml = simplexml_load_file($path);

if (strcmp($type, "result") == 0)
{
    printf("<h1>Result</h1>\r\n");
    printf("<h2>%s</h2>\r\n", $xml->user);
    printf("<h3>%s</h3>\r\n", $xml->score);
}
else
{
    printf("<h1>%s</h1>\r\n", $xml->title);
}

$tags = array();

// vul de $tags array
foreach ($xml->exercise as $exercise)
{
    if (!isset($exercise->tags))
        continue;

    foreach ($exercise->tags->tag as $tag)
    {
        $foo = $tag[0]->__toString();   //tag als string

        if (!isset($tags[$foo]))    // zit deze tag not niet in de array?
            $tags[$foo] = 0;        // voeg deze dan toe

        $tags[$foo]++;
    }
}

if (!empty($tags))
{
    $maxtags = max($tags);
    printf("<table>\r\n<caption>Tags</caption>\r\n");

    foreach ($tags as $tag => $cnt)
    {
        printf("<tr>\r\n");
        printf("<td><a href=\"view.php?fn=%s&tag=%s\">%s</a></td>\r\n", $_GET['fn'], $tag, $tag);
        printf("<td>%u</td>\r\n", $cnt);

        printf("<td><meter value=\"%u\" max=\"%u\">%u/%u</meter></td>\r\n",
            $cnt, $maxtags, $cnt, $maxtags);

        printf("</tr>\r\n");
    }

    printf("</table>\r\n");
}

function hasTag($exercise, $tag)
{
    foreach ($exercise->tags->tag as $foo)
    {
        if (strcmp($tag, $foo) == 0)
            return 1;
    }

    return 0;
}

foreach ($xml->exercise as $exercise)
{
    if (isset($_GET['tag']))
    {
        if (!hasTag($exercise, $_GET['tag']))
            continue;
    }

    printf("<div class=\"exercise\">\r\n");
    printf("<div class=\"vraag\">%s</div>\r\n", innerCode($exercise->vraag, "vraag"));

    if (strcmp($exercise['type'], "single") == 0 || strcmp($exercise['type'], "multi") == 0)
    {
        printf("<form>\r\n");
        
        foreach ($exercise->choice->item as $item)
        {
            printf("<p>\r\n");
            
            $foo = "";
            if (isset($item['checked']))
                $foo = " checked";

            if (strcmp($exercise['type'], "single") == 0)
                printf("<input type=\"radio\" disabled%s/>\r\n", $foo);

            if (strcmp($exercise['type'], "multi") == 0)
                printf("<input type=\"checkbox\" disabled%s/>\r\n", $foo);

            $foo = "";
            if (isset($item['goed']))   // item is goed, aangevinkt of niet
                $foo = " class=\"groen\"";
            else if (isset($item['checked']))   // user heeft verkeerde optie aangevinkt
                $foo = " class=\"rood\"";
            
            printf("<label%s>%s</label>\r\n", $foo, $item);
            printf("</p>\r\n");
        }

        printf("</form>\r\n");
    }

    if (strcmp($exercise['type'], "open") == 0)
    {
        printf("<form>\r\n");
        printf("<input value=\"%s\" disabled>\r\n", $exercise->answer);
        printf("</form>\r\n");
    }

    if (strcmp($exercise['type'], "dragdrop") == 0)
    {
        printf("<ul class=\"dragdrop\">\r\n");

        if (isset($exercise->dragdrop->drop))
        {
            foreach ($exercise->dragdrop->drop->ul as $ul)
            {
                $ref = $ul->li['ref'];
                $xxpath = sprintf("dragdrop/choice/item[@xid=\"%s\"]", $ref);
                printf("<li>%s</li>\r\n", $exercise->xpath($xxpath)[0]->__toString());
            }
        }

        printf("</ul>\r\n");
    }

    if (isset($exercise->toelichting))
    {
        printf("<div class=\"toelichting\">\r\n");
        printf("%s\r\n", innerCode($exercise->toelichting, "toelichting"));
        printf("</div>\r\n");
    }

    if (isset($exercise->tags))
    {
        printf("<ul class=\"tags\">\r\n");
        foreach ($exercise->tags->tag as $tag)
        {
            printf("<li>%s</li>\r\n", $tag->__toString());
        }
        printf("</ul>\r\n");
    }

    printf("</div>\r\n");
}

printf("</body>\r\n</html>\r\n");
?>



<!DOCTYPE html>
<?php
include 'common.php';

session_start();
printf("<html lang=\"en\">\r\n");
printf("<head>\r\n");
printf("<link rel=\"Shortcut Icon\" href=\"yinyang.svg\"/>\r\n");
printf("<link rel=\"stylesheet\" type=\"text/css\" href=\"common.css\"/>\r\n");
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

foreach ($xml->exercise as $exercise)
{
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
        printf("<ul>\r\n");

        if (isset($exercise->drag->drop))
        {
            foreach ($exercise->drag->drop->ul as $ul)
            {
                $ref = $ul->li['ref'];
                $xxpath = sprintf("drag/choice/item[@xid=\"%s\"]", $ref);
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

    printf("</div>\r\n");
}

printf("</body>\r\n</html>\r\n");
?>



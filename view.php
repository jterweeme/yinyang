<!DOCTYPE html>
<?php
session_start();
printf("<html lang=\"en\">\r\n<head>\r\n");
printf("<title>View</title>\r\n");
printf("<link rel=\"stylesheet\" type=\"text/css\" href=\"common.css\"/>\r\n");
printf("</head>\r\n<body>\r\n<header>\r\n");
printf("<a href=\"main.php\">Main Menu</a>\r\n");
printf("<a href=\"logout.php\">Log out %s</a>\r\n", $_SESSION['username']);
printf("</header>\r\n");

$path = sprintf("exams/%s", $_GET['fn']);
$xml = simplexml_load_file($path);

printf("<h1>%s</h1>\r\n", $xml->title);

include 'common.php';

foreach ($xml->exercise as $exercise)
{
    printf("<div class=\"exercise\">\r\n");
    printf("<div class=\"vraag\">\r\n");
    printf("%s\r\n", innerCode($exercise->vraag, "vraag"));
    printf("</div>\r\n");

    if (strcmp($exercise['type'], "single") == 0 || strcmp($exercise['type'], "multi") == 0)
    {
        printf("<form>\r\n");

        foreach ($exercise->choice->item as $item)
        {
            printf("<p>\r\n");

            if (strcmp($exercise['type'], "single") == 0)
                printf("<input type=\"radio\" disabled/>\r\n");

            if (strcmp($exercise['type'], "multi") == 0)
                printf("<input type=\"checkbox\" disabled/>\r\n");

            $foo = "";
            if (isset($item['goed']))
                $foo = " class=\"groen\"";

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

        foreach ($exercise->drag->drop->ul as $ul)
        {
            $ref = $ul->li['ref'];
            $xxpath = sprintf("drag/choice/item[@xid=\"%s\"]", $ref);
            printf("<li>%s</li>\r\n", $exercise->xpath($xxpath)[0]->__toString());
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



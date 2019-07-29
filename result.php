<?php
session_start();

printf("<!DOCTYPE html>\r\n");
printf("<html lang=\"en\">\r\n");
printf("<head>\r\n");
printf("<link rel=\"Shortcut Icon\" href=\"yinyang.svg\"/>\r\n");
printf("<link rel=\"stylesheet\" type=\"text/css\" href=\"common.css\"/>\r\n");
printf("<title>Result</title>\r\n");
printf("</head>\r\n<body>\r\n");

// header
printf("<header>\r\n");
printf("<a href=\"main.php\">Main menu</a>\r\n");
printf("<a href=\"logout.php\">Log out %s</a>\r\n", $_SESSION['username']);
printf("</header>\r\n");

$xml = new DOMDocument();
$path = sprintf("results/%s", $_GET["fn"]);
$xml->load($path);
$xsl = new DOMDocument();
$xsl->load("result.xsl");
$proc = new XSLTProcessor;
$proc->importStyleSheet($xsl);
$tree = $proc->transformToDoc($xml);

// workaround buitenste body tag verwijderen
$buf = $tree->saveHTML($tree->getElementsByTagName("body")->item(0));
$cut = substr($buf, 6, strlen($buf) - 13);
printf("%s", $cut);
//printf("%s", $tree->saveHTML($tree->getElementsByTagName("body")->item(0)));
//printf("%s", $proc->transformToXML($xml));


printf("</body>\r\n</html>\r\n");
?>



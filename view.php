<!DOCTYPE html>
<html lang="en">
<head>
<title>View</title>
<link rel="stylesheet" type="text/css" href="common.css"/>
</head>
<body>
<header>
<a href="main.php">Main Menu</a>
<?php
printf("<a href=\"logout.php\">Log out %s</a>\r\n", $_SESSION['username']);
?>
</header>
</body>
</html>



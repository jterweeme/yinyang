<?php
function redirect($target)
{
    $meta = sprintf("<meta http-equiv=\"Refresh\" content=\"2;url=%s\">\r\n", $target);
    $ret = "<html lang=\"en\">\r\n<head>\r\n<title>Redirect</title>\r\n";
    $ret .= $meta;
    $ret .= "</head>\r\n<body><p>Wait while being redirected.</p></body></html>\r\n";

    return $ret;
}

// workaround
function innerCode($xml, $tag)
{
    $ret = $xml->asXML();
    $xmllen = strlen($ret);
    $taglen = strlen($tag);
    $prelen = $taglen + 3;
    $postlen = $taglen + 4;
    $cutlen = $prelen + $postlen;
    return substr($ret, $prelen, $xmllen - $cutlen);
    return $xml->asXML();
}


?>



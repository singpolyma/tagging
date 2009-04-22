<?php

    function removeEvilTags($source) {
        // Code by tREXX [www.trexx.ch], "strip_tags", http://ca3.php.net/manual/en/function.strip-tags.php
        // [Jon Aquino 2005-10-28]
        $allowedTags = '<a><br><b><h1><h2><h3><h4><i><img><li><ol><p><strong><table><tr><td><th><u><ul>';
        $source = strip_tags($source, $allowedTags);
        return preg_replace('/<(.*?)>/ie', "'<'.removeEvilAttributes('\\1').'>'", $source);
    }
    function removeEvilAttributes($tagSource) {
        $stripAttrib = 'javascript:|onclick|ondblclick|onmousedown|onmouseup|onmouseover|'.
              'onmousemove|onmouseout|onkeypress|onkeydown|onkeyup';
        return stripslashes(preg_replace("/$stripAttrib/i", 'forbidden', $tagSource));
    }

?>
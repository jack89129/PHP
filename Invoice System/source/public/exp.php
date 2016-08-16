<?php

    //\_t\(('|")(.+?)('|")\)

    $all = array();
    $files = rglob(dirname(dirname(__FILE__)) . '/application/*.*');

    foreach( $files as $file ){
        $string = file_get_contents($file);

        $matches = array();
        if( preg_match_all('@\_t\((.+?)(\'\)|"\)|\',|"\,)@', $string, $matches) ){
            echo "<pre>";
            foreach( $matches[1] as $match ){
                $all[] = trim($match, '\'"');
            }
        }
    }

    $all = array_unique($all);
    echo count($all);
    print_r($all);
    $all = array_combine(array_values($all), array_values($all));

    file_put_contents(dirname(dirname(__FILE__)) . '/cache/z.all.tr.php', serialize($all));

    function rglob($pattern, $flags = 0, $path = '') {
        if (!$path && ($dir = dirname($pattern)) != '.') {
            if ($dir == '\\' || $dir == '/') $dir = '';
            return rglob(basename($pattern), $flags, $dir . '/');
        }
        $paths = glob($path . '*', GLOB_ONLYDIR | GLOB_NOSORT);
        $files = glob($path . $pattern, $flags);
        foreach ($paths as $p) $files = array_merge($files, rglob($pattern, $flags, $p . '/'));
        return $files;
    }
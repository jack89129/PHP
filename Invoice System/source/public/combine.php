<?php

    $files = glob(dirname(dirname(__FILE__)) . '/cache/*.all.tr.php');

//    echo "<pre>";
//    print_r($files);

    $all = array();
    foreach( $files as $file ){
        $result = unserialize(file_get_contents($file));
        $all = array_merge($all, $result);
    }

//    echo "<pre>";
//    echo "count: " , count($all), "<br/>";

//    $string = json_encode($all);
//    $pattern = array(',"', '{', '}');
//    $replacement = array(",\n\t\"", "{\n\t", "\n}");
//    $json = str_replace($pattern, $replacement, $string);
//
//    file_put_contents('translations.json', $json);

//    $fp = fopen('translations.csv', 'w');
//
//    foreach ($all as $original => $translated) {
//        fputcsv($fp, array($original, $translated));
//    }

//    fclose($fp);

    $data = array();
    $row = array();
    $row[] = 'Original';
    $row[] = 'nl';
    $row[] = 'en';
    $style = array_fill(0, 2, '-btbac');
    $data[] = implode('|', $style);
    $data[] = $row;

    foreach( $all as $original => $translation ){
        $row = array();
        $row[] = $original;
        $row[] = $translation;
        $row[] = '';
        $data[] = $row;
    }

    set_include_path( get_include_path() . PATH_SEPARATOR . dirname(dirname(__FILE__)) . "/library/");

    function __autoload($class){
        require_once str_replace('_', '/', $class . '.php');
    }

    ini_set('error_reporting', 'On');
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
    $excel = Export::excelCreate($data);
    Export::excelOutput($excel, 'translation', 'xlsx');

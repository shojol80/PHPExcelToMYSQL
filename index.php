<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "myDBPDO";

error_reporting(E_ALL);
set_time_limit(0);
date_default_timezone_set('Europe/London');


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     
}

catch (PDOException $e) {
    echo "<br>" . $e->getMessage();
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Reader Example #01</title>

</head>
<body>

<?php

/** PHPExcel_IOFactory */
include 'Classes/PHPExcel/IOFactory.php';



$files = scandir("sampleData");

//var_dump($files);

foreach($files as $file)
{
   // echo 'Checking file: "' . $file . '"...<br>';

    if(endsWith($file, ".xls") || endsWith($file, ".xlsx"))
    {
        try
        {
            operation("sampleData/".$file);
        }
        catch(Exception $e)
        {
            echo $e;
            //die($e);
        }
    }
}   

function operation ($inputFileName) {
global $conn;
$table      = true;
echo 'Loading file ', pathinfo($inputFileName, PATHINFO_BASENAME), ' using IOFactory to identify the format<br />';
$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
echo '<hr />';

$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
//var_dump($sheetData);

foreach ($sheetData[2] as $key => $value) {
    $db[$key] = $key . $value;
}
$find    = array("'s "," ","/");
$replace ="_";
$db      = str_replace($find, $replace, $db);
$sql = "CREATE TABLE `mytable` ( id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, ";
foreach ($db as $key => $dbs) {
    $sql .= "{$dbs} varchar(200) null,";
}
$sql .= "cs1 varchar(40) null";
$sql .= ");";


try {
if ($table) {
        $conn->exec($sql);
        echo "Table created successfully";
}
}

catch (PDOException $e) {
    echo "<br>" . $e->getMessage();
}


//echo $sql;
//var_dump($db);
if (is_array($sheetData)) {
    foreach ($sheetData as $key1 => $value2) {
        if ($key1 == 1 || $key1 == 2)
            continue;
        $field  = "";
        $fvalue = "";
        $i=0;
        foreach ($value2 as $key => $value) {
            
            $i++;
        
            if ($value=='') {$value='NULL';}
            $find    = array(",","'");
            $replace ="_";
            $value      = str_replace($find, $replace,$value);

            $field .= $db[$key].",";
            $fvalue .= "'".$value."',";
//echo $i.")".$fvalue."</br>";

        }
        $fvalue=rtrim($fvalue,',');
        $field=rtrim($field,',');
//echo "<pre>";
//var_dump(explode(',', $fvalue));
//var_dump(explode(',', $field));
//echo "</pre>";
//die();

        $query ="INSERT INTO mytable ($field) VALUES ($fvalue)";
        //mysqli_query($conn, $query);
         $conn->exec($query);
    }
    //var_dump($query);
}

}

function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}
$conn = null;

?>
<body>
</html> 

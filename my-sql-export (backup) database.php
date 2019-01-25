<?php

/* 
##### EXAMPLE #####
   EXPORT_DATABASE("localhost","user","pass","db_name" ); 
   
##### Notes #####
     * (optional) 5th parameter: to backup specific tables only,like: array("mytable1","mytable2",...)   
     * (optional) 6th parameter: backup filename (otherwise, it creates random name)
     * IMPORTANT NOTE ! Many people replaces strings in SQL file, which is not recommended. READ THIS:  http://itask.software/tools/wordpress-migrator
     * If you need, you can check "import.php" too
*/
EXPORT_TABLES("localhost","","","");


//https://github.com/tazotodua/useful-php-scripts
function EXPORT_TABLES($host,$user,$pass,$name,  $tables=false, $backup_name=false ){
    $mysqli = new mysqli($host,$user,$pass,$name); $mysqli->select_db($name); $mysqli->query("SET NAMES 'utf8'");
    $queryTables = $mysqli->query('SHOW TABLES'); while($row = $queryTables->fetch_row()) { $target_tables[] = $row[0]; }   if($tables !== false) { $target_tables = array_intersect( $target_tables, $tables); }
    foreach($target_tables as $table){
        $result = $mysqli->query('SELECT * FROM '.$table);  $fields_amount=$result->field_count;  $rows_num=$mysqli->affected_rows;     $res = $mysqli->query('SHOW CREATE TABLE '.$table); $TableMLine=$res->fetch_row();
        $content = (!isset($content) ?  '' : $content) . "\n\n".$TableMLine[1].";\n\n";
        for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0) {
            while($row = $result->fetch_row())  { //when started (and every after 100 command cycle):
                if ($st_counter%100 == 0 || $st_counter == 0 )  {$content .= "\nINSERT INTO ".$table." VALUES";}
                    $content .= "\n(";
                    for($j=0; $j<$fields_amount; $j++)  { $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) ); if (isset($row[$j])){$content .= '"'.$row[$j].'"' ; }else {$content .= '""';}     if ($j<($fields_amount-1)){$content.= ',';}      }
                    $content .=")";
                //every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
                if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) {$content .= ";";} else {$content .= ",";} $st_counter=$st_counter+1;
            }
        } $content .="\n\n\n";
    }
	$zaloha_db_f = '_db_zaloha';
    $backup_name = $backup_name ? $backup_name : "./".$zaloha_db_f."/".date("F")."/".date("d.m.Y")."/".$name."___(".date('H-i-s')."_".date('d-m-Y').")__rand".rand(1,11111111).".sql";
	$backup_nameecho = $name."___(".date('H-i-s')."_".date('d-m-Y').")__rand".rand(1,11111111).".sql";
	$filenamemonth = "./".$zaloha_db_f."/".date("F");
	$filenamedate = "./".$zaloha_db_f."/".date("F")."/".date("d.m.Y");
	
if (file_exists($filenamemonth)) {
   //echo "The file $filenamemonth month exists";
} else {
   $folder02 = date("F");
	mkdir ("./".$zaloha_db_f."/".$folder02, 0777);
}

if (file_exists($filenamedate)) {
    //echo "The file $filenamedate date exists";
} else {
   $folder01 = date("d.m.Y");
	mkdir ("./".$zaloha_db_f."/".date("F")."/".$folder01, 0777);
}
	
	$fp = fopen($backup_name, 'w');
fwrite($fp, $content);

fclose($fp);
  //  header('Content-Type: application/octet-stream');   header("Content-Transfer-Encoding: Binary"); header("Content-disposition: attachment; filename=\"".$backup_name."\"");  echo $content; exit;
  // name of the backup file for example - name_of_databaze___(06-25-32_24-01-2019)__rand7602027.sql

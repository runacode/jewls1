<?php
if(!function_exists('str_putcsv'))
{
    function str_putcsv($input, $delimiter = ',', $enclosure = '"')
    {
        // Open a memory "file" for read/write...
        $fp = fopen('php://temp', 'r+');
        // ... write the $input array to the "file" using fputcsv()...
        fputcsv($fp, $input, $delimiter, $enclosure);
        // ... rewind the "file" so we can read what we just wrote...
        rewind($fp);
        // ... read the entire line into a variable...
        $data = fread($fp, 1048576);
        // ... close the "file"...
        fclose($fp);
        // ... and return the $data to the caller, with the trailing newline from fgets() removed.
        return rtrim($data, "\n");
    }
}
include_once ("../config/config.php");

$tmp['Outbound'] = $_REQUEST['link'];
$tmp['Ip'] = $_SERVER['REMOTE_ADDR'];
$tmp['Now'] = date(DATE_RFC2822);
$now =getdate();
error_log(  str_putcsv($tmp) . "\n",3,$BasePath . "/logs/{$now['year']}_{$now['day']}_{$now['month']}.csv");

?>
<?php
$BasePath = dirname(dirname(__FILE__) . "../") . '/';

$ConfigFilePath = dirname(__FILE__) . '/data.json';
$ConfigTextData = file_get_contents($ConfigFilePath);
$data = json_decode($ConfigTextData);
if (isset($overwrite)) {
    $overwritedata = json_decode(file_get_contents(dirname(__FILE__) . '/Pages/' . $overwrite));
    foreach (array_keys((array)$overwritedata) as $key) {
        $data->$key = $overwritedata->$key;
    }
} else {
    $overwritedata = array();
}
if (!function_exists("GetCurrentValueByDataPosition")) {
    function GetCurrentValueByDataPosition($dp, $overwrite)
    {
        $overwritedata = json_decode(file_get_contents(dirname(__FILE__) . '/Pages/' . $overwrite), true);
        $ConfigItems = preg_split('/-/', $dp);
        $ConfigItem = $ConfigItems[0];


        if (isset($overwritedata[$ConfigItem])) {
            switch (count($ConfigItems)) {
                case 1:
                    return $overwritedata[$ConfigItem];
                    break;
                case 2:
                    $ObjectIndex = $ConfigItems[1];
                    if (is_int($ObjectIndex)) {
                        $ObjectIndex = (int)$ObjectIndex;
                    }

                    return $overwritedata[$ConfigItem][$ObjectIndex];
                    break;
                case 3:

                    $ObjectIndex = $ConfigItems[1];
                    if (is_int($ObjectIndex)) {
                        $ObjectIndex = parse_int($ObjectIndex);
                    }
                    $Property = $ConfigItems[2];
                    if (is_int($Property)) {
                        $Property = parse_int($Property);
                    }

                    return $overwritedata[$ConfigItem][$ObjectIndex][$Property];
                    break;
            }
        }
    }

    function SetCurrentValueByDataPosition($dp, $overwrite, $Value)
    {

        $overwritedata = json_decode(file_get_contents(dirname(__FILE__) . '/Pages/' . $overwrite), true);
        $ConfigItems = preg_split('/-/', $dp);
        $ConfigItem = $ConfigItems[0];


        switch (count($ConfigItems)) {
            case 1:
                $overwritedata[$ConfigItem] = $Value;




                UpdateOverWriteData($overwrite, $overwritedata);

                return true;
            case 2:

                $ObjectIndex = $ConfigItems[1];
                if (!preg_match('/\[\]$/', $dp)) {
                    $overwritedata[$ConfigItem][$ObjectIndex] = $Value;
                } else {
                    $overwritedata[$ConfigItem][] = $Value;
                }
                UpdateOverWriteData($overwrite, $overwritedata);
                break;
            case 3:

                $ObjectIndex = $ConfigItems[1];
                $Property = $ConfigItems[2];
                if (!preg_match('/\[\]$/', $dp)) {
                    $overwritedata[$ConfigItem][$ObjectIndex][$Property] = $Value;
                } else {
                    $overwritedata[$ConfigItem][$ObjectIndex][] = $Value;
                }
                UpdateOverWriteData($overwrite, $overwritedata);
                return true;

                break;
        }

    }

    function DeleteCurrentValueByDataPosition($dp, $overwrite)
    {

        $overwritedata = json_decode(file_get_contents(dirname(__FILE__) . '/Pages/' . $overwrite), true);
        $ConfigItems = preg_split('/-/', $dp);
        $ConfigItem = $ConfigItems[0];


        switch (count($ConfigItems)) {
            case 1:
                unset($overwritedata[$ConfigItem]);

                UpdateOverWriteData($overwrite, $overwritedata);
                return true;
            case 2:

                $ObjectIndex = $ConfigItems[1];
                if (is_numeric($ObjectIndex)) {
                    array_splice($overwritedata[$ConfigItem], $ObjectIndex, 1);
                } else {
                    unset($overwritedata[$ConfigItem][$ObjectIndex]);
                }
                UpdateOverWriteData($overwrite, $overwritedata);
                break;
            case 3:

                $ObjectIndex = $ConfigItems[1];
                $Property = $ConfigItems[2];
                if (is_numeric($ObjectIndex)) {
                    array_splice($overwritedata[$ConfigItem][$ObjectIndex], $Property, 1);
                } else {
                    unset($overwritedata[$ConfigItem][$ObjectIndex][$Property]);
                }
                UpdateOverWriteData($overwrite, $overwritedata);
                return true;

                break;
        }


    }

    function UpdateOverWriteData($overwrite, $overwritedata)
    {
        $result = file_put_contents(dirname(__FILE__) . '/Pages/' . $overwrite, json_encode($overwritedata));
        if ($result === false) {
            echo "File Write Failed";
            exit;
        }
    }
}
?>

<?php
if(!function_exists('http_build_url'))
{
    // Define constants
    define('HTTP_URL_REPLACE',          0x0001);    // Replace every part of the first URL when there's one of the second URL
    define('HTTP_URL_JOIN_PATH',        0x0002);    // Join relative paths
    define('HTTP_URL_JOIN_QUERY',       0x0004);    // Join query strings
    define('HTTP_URL_STRIP_USER',       0x0008);    // Strip any user authentication information
    define('HTTP_URL_STRIP_PASS',       0x0010);    // Strip any password authentication information
    define('HTTP_URL_STRIP_PORT',       0x0020);    // Strip explicit port numbers
    define('HTTP_URL_STRIP_PATH',       0x0040);    // Strip complete path
    define('HTTP_URL_STRIP_QUERY',      0x0080);    // Strip query string
    define('HTTP_URL_STRIP_FRAGMENT',   0x0100);    // Strip any fragments (#identifier)

    // Combination constants
    define('HTTP_URL_STRIP_AUTH',       HTTP_URL_STRIP_USER | HTTP_URL_STRIP_PASS);
    define('HTTP_URL_STRIP_ALL',        HTTP_URL_STRIP_AUTH | HTTP_URL_STRIP_PORT | HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT);

    /**
     * HTTP Build URL
     * Combines arrays in the form of parse_url() into a new string based on specific options
     * @name http_build_url
     * @param string|array $url     The existing URL as a string or result from parse_url
     * @param string|array $parts   Same as $url
     * @param int $flags            URLs are combined based on these
     * @param array &$new_url       If set, filled with array version of new url
     * @return string
     */
    function http_build_url(/*string|array*/ $url, /*string|array*/ $parts = array(), /*int*/ $flags = HTTP_URL_REPLACE, /*array*/ &$new_url = false)
    {
        // If the $url is a string
        if(is_string($url))
        {
            $url = parse_url($url);
        }

        // If the $parts is a string
        if(is_string($parts))
        {
            $parts  = parse_url($parts);
        }

        // Scheme and Host are always replaced
        if(isset($parts['scheme'])) $url['scheme']  = $parts['scheme'];
        if(isset($parts['host']))   $url['host']    = $parts['host'];

        // (If applicable) Replace the original URL with it's new parts
        if(HTTP_URL_REPLACE & $flags)
        {
            // Go through each possible key
            foreach(array('user','pass','port','path','query','fragment') as $key)
            {
                // If it's set in $parts, replace it in $url
                if(isset($parts[$key])) $url[$key]  = $parts[$key];
            }
        }
        else
        {
            // Join the original URL path with the new path
            if(isset($parts['path']) && (HTTP_URL_JOIN_PATH & $flags))
            {
                if(isset($url['path']) && $url['path'] != '')
                {
                    // If the URL doesn't start with a slash, we need to merge
                    if($url['path'][0] != '/')
                    {
                        // If the path ends with a slash, store as is
                        if('/' == $parts['path'][strlen($parts['path'])-1])
                        {
                            $sBasePath  = $parts['path'];
                        }
                        // Else trim off the file
                        else
                        {
                            // Get just the base directory
                            $sBasePath  = dirname($parts['path']);
                        }

                        // If it's empty
                        if('' == $sBasePath)    $sBasePath  = '/';

                        // Add the two together
                        $url['path']    = $sBasePath . $url['path'];

                        // Free memory
                        unset($sBasePath);
                    }

                    if(false !== strpos($url['path'], './'))
                    {
                        // Remove any '../' and their directories
                        while(preg_match('/\w+\/\.\.\//', $url['path'])){
                            $url['path']    = preg_replace('/\w+\/\.\.\//', '', $url['path']);
                        }

                        // Remove any './'
                        $url['path']    = str_replace('./', '', $url['path']);
                    }
                }
                else
                {
                    $url['path']    = $parts['path'];
                }
            }

            // Join the original query string with the new query string
            if(isset($parts['query']) && (HTTP_URL_JOIN_QUERY & $flags))
            {
                if (isset($url['query']))   $url['query']   .= '&' . $parts['query'];
                else                        $url['query']   = $parts['query'];
            }
        }

        // Strips all the applicable sections of the URL
        if(HTTP_URL_STRIP_USER & $flags)        unset($url['user']);
        if(HTTP_URL_STRIP_PASS & $flags)        unset($url['pass']);
        if(HTTP_URL_STRIP_PORT & $flags)        unset($url['port']);
        if(HTTP_URL_STRIP_PATH & $flags)        unset($url['path']);
        if(HTTP_URL_STRIP_QUERY & $flags)       unset($url['query']);
        if(HTTP_URL_STRIP_FRAGMENT & $flags)    unset($url['fragment']);

        // Store the new associative array in $new_url
        $new_url    = $url;

        // Combine the new elements into a string and return it
        return
            ((isset($url['scheme'])) ? $url['scheme'] . '://' : '')
            .((isset($url['user'])) ? $url['user'] . ((isset($url['pass'])) ? ':' . $url['pass'] : '') .'@' : '')
            .((isset($url['host'])) ? $url['host'] : '')
            .((isset($url['port'])) ? ':' . $url['port'] : '')
            .((isset($url['path'])) ? $url['path'] : '')
            .((isset($url['query'])) ? '?' . $url['query'] : '')
            .((isset($url['fragment'])) ? '#' . $url['fragment'] : '')
            ;
    }
}

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
if (isset($overwritedata->VoluumCpid) && strlen($overwritedata->VoluumCpid) > 0) {
    if (strpos($_SERVER['QUERY_STRING'], $overwritedata->VoluumCpid) === false) {
        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $url_parts = parse_url($actual_link);

        if (isset($url_parts['query'])) { // Avoid 'Undefined index: query'
            parse_str($url_parts['query'], $params);
        } else {
            $params = array();
        }
        $params['cpid'] = $overwritedata->VoluumCpid;
        $url_parts['query'] = http_build_query($params);
        $VoluumUrl =  http_build_url($url_parts);
        //header("location: " . http_build_url($url_parts));
        //exit;
    }
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

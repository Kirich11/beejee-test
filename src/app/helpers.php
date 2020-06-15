<?php
/** 
 * globally defined helper function
*/
if (!function_exists('env')) {
    function env($path)
    {
        return getenv($path);
    }
}

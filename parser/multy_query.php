<?php

function multyrequest($urls)
{
    $multi = curl_multi_init();
    $handles = Array();
    foreach ($urls as $key => $url) 
    {
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        
        curl_multi_add_handle($multi, $ch);
        $handles[$url] = $ch;
        
    }
    

    $active = null;
    do {
        $mrc = curl_multi_exec($multi, $active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);

    while ($active && $mrc == CURLM_OK) {
        if (curl_multi_select($multi) == -1) {
            usleep(100);
        }
        do {
            $mrc = curl_multi_exec($multi, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    }
        
    foreach ($handles as $key => $channel) {
        $tmp = curl_multi_getcontent($channel);
        
        $html[$key]['head'] = htmlspecialchars(stristr($tmp,'<!DOCTYPE html>',true));
        //$html[$key]['body'] = htmlspecialchars(stristr($tmp,'<!DOCTYPE html>'));

        curl_multi_remove_handle($multi, $channel);
    }
    curl_multi_close($multi);
    return $html;
}
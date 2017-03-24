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
        curl_setopt($ch, CURLOPT_NOBODY, true);

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
        $html[$key] = curl_multi_getcontent($channel);


        curl_multi_remove_handle($multi, $channel);
    }
    curl_multi_close($multi);
    return $html;
}
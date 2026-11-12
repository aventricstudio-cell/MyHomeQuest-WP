<?php 

function get_client_ip() {

    echo "<pre>";
    print_r($_SERVER);


    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress .= $_SERVER['HTTP_CLIENT_IP'] . ' - ';
    }    if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress .= $_SERVER['HTTP_X_FORWARDED_FOR']. ' - ';
    }    if(isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ipaddress .= $_SERVER['HTTP_X_FORWARDED']. ' - ';
    }    if(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ipaddress .= $_SERVER['HTTP_FORWARDED_FOR']. ' - ';
    }    if(isset($_SERVER['HTTP_FORWARDED'])) {
        $ipaddress .= $_SERVER['HTTP_FORWARDED']. ' - ';
    }    if(isset($_SERVER['REMOTE_ADDR'])) {
        $ipaddress .= $_SERVER['REMOTE_ADDR']. ' - ';
    }

    if($ipaddress == '') {
        $ipaddress = 'UNKNOWN';
    }

    return $ipaddress;
}

echo get_client_ip();
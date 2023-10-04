<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class WaApi
{
    function SendMessage($number, $messages)
    {
        $url = 'http://116.203.191.58/api/async_send_message';

        $key = '7d936301cb904c4ef7910925b6be142b426577bb8ab8008c';

        $data = array(
            "phone_no" => $number,
            "key"     => $key,
            "message" => $messages
        );

        $data_string = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 360);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: '.strlen($data_string)
            )
        );
        
        $responses = curl_exec($ch);
        curl_close($ch);

        $msg = json_decode($responses, true);
        return $msg;
    }

    function SendMessageFile($number, $file_path)
    {
        $url = 'http://116.203.191.58/api/send_file_url';

        $key = '7d936301cb904c4ef7910925b6be142b426577bb8ab8008c';

        $data = array(
            "phone_no"  => $number,
            "key"       => $key,
            "url"       => $file_path
          );

        $data_string = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 360);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: '.strlen($data_string)
            )
        );
        
        $responses = curl_exec($ch);
        curl_close($ch);

        $msg = json_decode($responses, true);
        return $msg;
    }
}

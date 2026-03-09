<?php
    function decryptIt($q)
    {
        $cryptKey  = 'Iloveyouallpann';
        $qDecoded = rtrim(openssl_decrypt(base64_decode($q), "AES-256-CBC", md5($cryptKey), 0, substr(md5(md5($cryptKey)), 0, 16)), "\0");
        //write_file("temp.txt","=".date("H:i:s")."=qDecoded===$qDecoded=\r\n","a");
        return ($qDecoded);
    }
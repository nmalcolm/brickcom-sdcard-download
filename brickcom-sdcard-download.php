<?php

getDir('http://127.0.0.1:9001', 'admin:admin');

function getDir($url, $auth, $dir = 'sd/')
{
    $fields = array(
      'dir' => urlencode($dir),
    );

    $fields_string = '';
    foreach ($fields as $key => $value) {
        $fields_string .= $key.'='.$value.'&';
    }
    rtrim($fields_string, '&');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url.'/cgi-bin/storage_list.cgi');
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $auth);

    $result = curl_exec($ch);

    $dom = new DOMDocument();

    @$dom->loadHTML($result);

    foreach ($dom->getElementsByTagName('a') as $link) {
        if (substr($link->getAttribute('rel'), -1) == '/') {
            getDir($url, $auth, $link->getAttribute('rel'));
        } else {
            echo 'Saving ' . $link->getAttribute('rel')."\n";
            getFile($url, $auth, $link->getAttribute('rel'));
        }
    }

    curl_close($ch);
}

function getFile($url, $auth, $file)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url.'/'.$file);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $auth);

    $result = curl_exec($ch);

    @mkdir('camera');
    file_put_contents('camera/'.basename($file), $result);

    curl_close($ch);
}

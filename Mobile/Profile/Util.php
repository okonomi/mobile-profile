<?php


class Mobile_Profile_Util
{
    /**
     *  @see http://www.ibm.com/developerworks/jp/opensource/library/os-php-multitask/index.html
     */
    public function parallelRequest($host, $urls)
    {
        $timeout = 10;
        $sockets = array();
        $result  = array();

        $id = 0;
        foreach ($urls as $url) {
            $s = stream_socket_client(
                "{$host}:80", $errno, $errstr, $timeout,
                STREAM_CLIENT_ASYNC_CONNECT | STREAM_CLIENT_CONNECT);
            if ($s) {
                $sockets[$id++] = $s;
                $http_msg = "GET {$url} HTTP 1.0\r\nHost: {$host}\r\n\r\n";
                fwrite($s, $http_msg);
            } else {
                throw new Exception("{$errno}: {$errstr}");
            }
        }

        while (count($sockets)) {
            $read = $sockets;
            stream_select($read, $w=null, $e=null, $timeout);
            if (count($read)) {
                foreach ($read as $r) {
                    $id   = array_search($r, $sockets);
                    $data = fread($r, 1024 * 8);

                    if (strlen($data) == 0) {
                        fclose($r);
                        unset($sockets[$id]);
                    } else {
                        $result[$id] .= $data;
                    }
                }
            } else {
                throw new Exception("Time-out!");
            }
        }


        return $result;
    }
}

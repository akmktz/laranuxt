<?php

$errno = null;
$errstr = null;

$fp = stream_socket_client("udp://127.0.0.1:7000", $errno, $errstr);
fwrite($fp, "TEST\n");
#!/usr/bin/php
<?php
require('fastcgi.php');

if (!isset($_SERVER['argc'])) {
    die("Command line only\n");
}
if ($_SERVER['argc']<2) {
    die("Usage: ".$_SERVER['argv'][0]."  URI\n\nEx: ".$_SERVER['argv'][0]." localhost:9000/status\n");
}

$url = parse_url($_SERVER['argv'][1]);
if (!$url || !isset($url['path'])) {
    die("Malformed URI");
}

$req = '/'.basename($url['path']);
if (isset($url['query'])) {
    $uri = $req .'?'.$url['query'];
} else {
    $url['query'] = '';
    $uri = $req;
}
$client = new FCGIClient(
    (isset($url['host']) ? $url['host'] : 'localhost'), 
    (isset($url['port']) ? $url['port'] : 9000));

$params = array(       
		'GATEWAY_INTERFACE' => 'FastCGI/1.0',
		'REQUEST_METHOD'    => 'GET',
		'SCRIPT_FILENAME'   => $url['path'],
		'SCRIPT_NAME'       => $req,
		'QUERY_STRING'      => $url['query'],
		'REQUEST_URI'       => $uri,
		'DOCUMENT_URI'      => $req,
		'SERVER_SOFTWARE'   => 'php/fcgiclient',
		'REMOTE_ADDR'       => '127.0.0.1',
		'REMOTE_PORT'       => '9985',
		'SERVER_ADDR'       => '127.0.0.1',
		'SERVER_PORT'       => '80',
		'SERVER_NAME'       => php_uname('n'),
		'SERVER_PROTOCOL'   => 'HTTP/1.1',
		'CONTENT_TYPE'      => '',
		'CONTENT_LENGTH'    => 0
);
//print_r($params);
echo "Call: $uri\n\n";
echo $client->request($params, false)."\n";


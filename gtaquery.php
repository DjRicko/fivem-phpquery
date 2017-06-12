<?php
//Spit errors
error_reporting(~E_WARNING);
 
$server = "1.2.3.4";
$port = "30120";
$query = "\xFF\xFF\xFF\xFFgetinfo xxx";

//Create socket
$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_set_option($sock,SOL_SOCKET,SO_RCVTIMEO,array("sec"=>30,"usec"=>0));

echo "Socket created!\n";

//Connect the socket to server
if (!socket_connect($sock, $server, $port))
{
	$errorcode = socket_last_error();
	$errormsg = socket_strerror($errorcode);
	
	die("Could not connect to server: [$errorcode] $errormsg \n");
}

echo "Socket connected!\n";

//Send query to server
if (!socket_write($sock, $query, strlen($query)))
{
	$errorcode = socket_last_error();
	$errormsg = socket_strerror($errorcode);
	
	die("Could not send data: [$errorcode] $errormsg \n");
}

echo "Query sent!\n";

//Now receive reply from server and print it
if (socket_recv($sock, $reply, 1024, MSG_WAITALL) === FALSE)
{
	$errorcode = socket_last_error();
	$errormsg = socket_strerror($errorcode);
	
	die("Could not receive data: [$errorcode] $errormsg \n");
}

$server = [];
$data = explode("\\", $reply);
for ($i = 0; $i < count($data); $i++) {
    if ($data[$i] == 'sv_maxclients') {
      $server['maxclients'] = $data[$i + 1];
    }

    if ($data[$i] == 'clients') {
      $server['clients'] = $data[$i + 1];
    }

    if ($data[$i] == 'challenge') {
      $server['challenge'] = $data[$i + 1];
    }

    if ($data[$i] == 'gamename') {
      $server['gamename'] = $data[$i + 1];
    }

    if ($data[$i] == 'protocol') {
      $server['protocol'] = $data[$i + 1];
    }

    if ($data[$i] == 'hostname') {
      $server['hostname'] = preg_replace("/\^[\d]/", "", $data[$i + 1]);
    }

    if ($data[$i] == 'gametype') {
      $server['gametype'] = $data[$i + 1];
    }

    if ($data[$i] == 'mapname') {
      $server['mapname'] = $data[$i + 1];
    }

    if ($data[$i] == 'iv') {
      $server['iv'] = $data[$i + 1];
    }
}

echo "Reply from server: \n";
print_r($server);

?>
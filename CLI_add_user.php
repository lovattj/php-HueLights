#!/usr/bin/php
<?php

$opts = json_decode(file_get_contents("config.json"), true);
spl_autoload_extensions(".php");
spl_autoload_register();

use jlls\Hue as Hue;

$myLights = new Hue\System($opts['ip_address'], $opts['username']);

try {
	$response = json_decode($myLights->Info()->AddUser());
	print_r($response);
} catch (Exception $e) {
	if ($e->getMessage() == "Error - link button not pressed") {
		echo $e->getMessage()." - press it and try again.";
	} else {
		echo $e->getMessage();
	}
}
echo "\n";
?>
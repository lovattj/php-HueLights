#!/usr/bin/php
<?php
$cliopts = getopt("l:");
if (@!array_key_exists('l', $cliopts)) {
	echo "You need to pass a light ID. Exiting.\n";
	exit(1);
}

$opts = json_decode(file_get_contents("config.json"), true);
spl_autoload_extensions(".php");
spl_autoload_register();

use jlls\Hue as Hue;

$myLights = new Hue\System($opts['ip_address'], $opts['username']);

try {
	$response = json_decode($myLights->Lights()->LightOn($cliopts['l'])->LightBrightness($cliopts['l'], "random")->LightHue($cliopts['l'], "random"), true);
	print_r($response);
} catch (Exception $e) {
	echo $e->getMessage();
}
echo "\n";
?>
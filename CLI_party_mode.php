#!/usr/bin/php
<?php
$cliopts = getopt("l:t:");
if (@!array_key_exists('l', $cliopts) || @!array_key_exists('t', $cliopts)) {
	echo "You need to pass a light ID on -l and time interval on -t.\n";
	echo "Example: ./CLI_party_mode.php l1 t2 - will change light 1 every 2 seconds.\n";
	exit(1);
}

$opts = json_decode(file_get_contents("config.json"), true);
spl_autoload_extensions(".php");
spl_autoload_register();

use jlls\Hue as Hue;

$myLights = new Hue\System($opts['ip_address'], $opts['username']);

printf("Changing light %d every %d seconds - CTRL+C to exit.\n", (int)$cliopts['l'], (int)$cliopts['t']);

try {
	for (;;) {
		$response = json_decode($myLights->Lights($cliopts['l'])->LightOn()->LightBrightness("random")->LightHue("random"), true);
		sleep($cliopts['t']);
	}
} catch (Exception $e) {
	echo $e->getMessage();
}
echo "\n";
?>
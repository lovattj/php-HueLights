#!/usr/bin/php
<?php
$opts = json_decode(file_get_contents("config.json"), true);
spl_autoload_extensions(".php");
spl_autoload_register();

use jlls\Hue as Hue;

$myLights = new Hue\System($opts['ip_address'], $opts['username']);

try {
	
	$response = json_decode($myLights->Lights()->DescribeAllLights(), true);
	
	echo "ID | Reachable | Status | Hue    | Brightness | Name \n";
	foreach ($response as $key=>$light) {
		$id = str_pad($key, 3);
		$name = $light['name'];
		if ($light['state']['reachable'] == 1) {
			$reachable = str_pad("true", 10);
		} else if ($light['state']['reachable'] == 0) {
			$reachable = str_pad("false", 10);
		}

		if (empty($light['state']['on'])) {
			$status = str_pad("off", 7);
		} else {
			$status = str_pad("on",7);
		}
		$hue = str_pad($light['state']['hue'], 7);
		$brightness = str_pad($light['state']['bri'], 11);
		printf("%s| %s| %s| %s| %s| %s\n", $id, $reachable, $status, $hue, $brightness, $name );

		
	}
	
} catch (Exception $e) {
	echo $e->getMessage();
}
echo "\n";
?>
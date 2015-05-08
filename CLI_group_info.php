#!/usr/bin/php
<?php
$cliopts = getopt("g:");
if (@!array_key_exists('g', $cliopts)) {
	echo "You need to pass a group ID on -g. Exiting.\n";
	echo "Example: CLI_group_info.php -g0\n";
	exit(1);
}

$opts = json_decode(file_get_contents("config.json"), true);
spl_autoload_extensions(".php");
spl_autoload_register();

use jlls\Hue as Hue;

$myLights = new Hue\System($opts['ip_address'], $opts['username']);

try {
	
	$response = json_decode($myLights->Groups($cliopts['g'])->DescribeGroup(), true);
	print_r($response);
} catch (Exception $e) {
	echo $e->getMessage();
}
echo "\n";
?>
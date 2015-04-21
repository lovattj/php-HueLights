<?php
spl_autoload_extensions(".php");
spl_autoload_register();

use jlls\Hue as Hue;

$someObject = new Hue\System("ip", "username");

try {
	print_r($someObject->Lights()->DescribeAllLights());	
} catch (Exception $e) {
	echo $e->getMessage();
}
echo "\n";
?>
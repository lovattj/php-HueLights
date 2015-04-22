# php-HueLights
This is a PHP client library for the Phillips Hue light system.<br>
Definitely a work in progress - bear with me!

- How to use:<br>
Require or autoload the `jlls/Hue/System.php` file.<br>
Create new object of the `jlls/Hue/System method`, passing your base station IP and developer ID in constructor.<br>
Call a method!

- Example:
```
spl_autoload_extensions(".php");
spl_autoload_register();
use jlls\Hue as Hue;
$myLights = new Hue\System("192.168.1.2", "newdeveloper");
print_r(json_decode($myLights->Lights()->DescribeAllLights(), true));
```

- Methods are chainable:
```
$lightId=1;
$operation = $myLights->Lights($lightId)->LightOn()->LightBrightness(100)->LightHue(20000);
```

- Randomise your light colour every few seconds with the power of automation!:
```
$lightId=1;
for ($i=1, $i<100; $i++) {
  $operation = $myLights->Lights($lightId)->LightOn()->LightBrightness('random')->LightHue('random');
  sleep(5);
}
```

- Method reference:<br>
See the Wiki.

- Example CLI files:<br>
To get these working, edit `config.json` with your Base Station IP and username.<br>
Then, call them from your command line, e.g. `php CLI_describe_lights.php`.<br>
Some need the light ID parameter passing in, e.g. `php CLI_light_on.php -l1` will turn on light ID 1.<br>

- Questions/comments:<br>
Drop me an e-mail at <a href="mailto:huelights@jlls.info">huelight@jlls.info</a>.

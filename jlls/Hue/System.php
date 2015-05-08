<?php
namespace jlls\Hue {
date_default_timezone_set("Europe/London");

	class System {
	
	private $ip_address;
	private $username;
	private $api_root;
	private $last_response;
	private $light;
	private $group;
	
		public function __construct($incoming_ip_address, $incoming_username) {
			$this->ip_address = $incoming_ip_address;
			$this->username = $incoming_username;	
			$this->api_root = "http://".$this->ip_address."/api/";
		}
		
		public function __toString() {
			return (string)json_encode($this->last_response);
		}

			
		public function Lights($light) {
			if (!empty($light) && is_numeric($light)) {
				$this->light = $light;
				return $this;
			} else {
				throw new HueException("Invalid light ID passed.");
				exit;
			}
		}
	
			public function Describe() {
				try {
					$response = $this->make_get_request($this->api_root.$this->username."/lights/".$this->light);
					$this->last_response = $response;
					return $this;
				} catch (Exception $e) {
					throw $e;
				}
			}
			
			public function LightOn() {
				try {
					$opts = Array();
					$opts['on']=true;
					$response = $this->ModifyLight($this->light, $opts);
					$this->last_response = Array("light" => $this->light, "status" => "on");
					return $this;
					
				} catch (Exception $e) {
					throw $e;
				}
			}

			public function LightOff() {
				try {
					$opts = Array();
					$opts['on']=false;
					$response = $this->ModifyLight($this->light, $opts);
					$this->last_response = Array("light" => $this->light, "status" => "off");
					return $this;
				} catch (Exception $e) {
					throw $e;
				}
			}
			
			public function LightBrightness($brightness) {
				if ($brightness == "random") {
					$brightness = mt_rand(1, 254);
				}
				
				if ($brightness < 1 || $brightness > 254) {
					throw new HueException("Specified brightness outside range of 1-254");
					exit;
				}
				
				try {
					$opts = Array();
					(int)$opts['bri'] = $brightness;
					$response = $this->ModifyLight($this->light, $opts);
					$this->last_response = $response;
					return $this;
				} catch (Exception $e) {
					throw $e;
				}
				
			}
			
			public function LightHue($hue) {
				if ($hue == "random") {
					$hue = mt_rand(0, 65535);
				}
				
				if ($hue < 0 || $hue > 65535) {
					throw new HueException("Specified hue outside range of 0-65535");
					exit;
				}
				
				try {
					$opts = Array();
					(int)$opts['hue'] = $hue;
					$response = $this->ModifyLight($this->light, $opts);
					$this->last_response = $response;
					return $this;
				} catch (Exception $e) {
					throw $e;
				}			
			}			

			public function LightSaturation($sat) {
				if ($sat == "random") {
					$sat = mt_rand(0, 254);
				}
				
				if ($sat < 0 || $sat > 254) {
					throw new HueException("Specified saturation outside range of 0-254");
					exit;
				}
				
				try {
					$opts = Array();
					(int)$opts['sat'] = $sat;
					$response = $this->ModifyLight($this->light, $opts);
					$this->last_response = $response;
					return $this;
				} catch (Exception $e) {
					throw $e;
				}			
			}	
			
			protected function ModifyLight($light, $opts) {
				try {
					$response = $this->make_put_request($this->api_root.$this->username."/lights/".$light."/state", $opts);
					return $response;
				} catch (Exception $e) {
					throw $e;
				}
				
			}

		public function Groups($groupid) {
			$this->group = $groupid;
			return $this;
		}
		
			public function DescribeGroup() {
				try {
					
					$response = $this->make_get_request($this->api_root.$this->username."/groups/".$this->group);
					$this->last_response = $response;
					return $this;				
				} catch (Exception $e) {
					throw $e;
				}
			
			}
			
			public function GroupOn() {
				try {
					$opts = Array();
					$opts['on'] = true;
					$response = $this->ModifyGroup($this->group, $opts);
					if (array_key_exists('success', $response[0])) {
						$this->last_response = Array("group" => $this->group, "status" => "on");
					} else {
						throw new HueException('Unexpected response from the base station.');
					}
					
					return $this;
				} catch (Exception $e) {
					throw $e;
				}
			}
			
			public function GroupOff() {
				try {
					$opts = Array();
					$opts['on'] = false;
					$response = $this->ModifyGroup($this->group, $opts);
					if (array_key_exists('success', $response[0])) {
						$this->last_response = Array("group" => $this->group, "status" => "off");
					} else {
						throw new HueException('Unexpected response from the base station.');
					}

					return $this;
				} catch (Exception $e) {
					throw $e;
				}
			}
					
			protected function ModifyGroup($group, $opts) {
				try {
					$response = $this->make_put_request($this->api_root.$this->username."/groups/".$group."/action", $opts);
					return $response;
				} catch (Exception $e) {
					throw $e;
				}
			}
			
			

		public function Info() {
			return $this;
			
		}
		
			public function AddUser() {
				try {
					$opts = Array();
					(string)$opts['devicetype'] = "phpHueLights";
					(string)$opts['username'] = md5(time());
					$response = $this->make_post_request($this->api_root, $opts);
					$this->last_response = $response;
					return $this;
				} catch (Exception $e) {
					throw $e;
				}	
			}
			
			public function DescribeConfiguration() {
				try {
					$response = $this->make_get_request($this->api_root.$this->username."/config");
					return $response;
				} catch (Exception $e) {
					throw $e;
				}
			}
		
			public function DescribeAllLights() {
				try {
					$response = $this->make_get_request($this->api_root.$this->username."/lights");
					$this->last_response = $response;
					return $this;
				} catch (Exception $e) {
					throw $e;
				}
			}	
					
			public function CountLights() {
				try {
					$response = $this->Lights()->DescribeAllLights();
					$this->last_response = Array('lightCount' => count($this->last_response));
					return $this;
				} catch (Exception $e) {
					throw $e;
				}
			}					
						
		
		protected function make_get_request($where) {
			$req = file_get_contents($where);
			$decoded = json_decode($req, true);
			if (@array_key_exists('error', @$decoded[0])) {
				throw new HueException('Error - '.@$decoded[0]['error']['description']);
				exit;
			} else {
				return $decoded;
			}
			
		}
		
		protected function make_put_request($where, $data) {
			$postdata = json_encode($data);
			$opts = array('http' =>
				array(
					'method'  => 'PUT',
					'header'  => 'Content-type: application/x-www-form-urlencoded',
					'content' => $postdata
				)
			);
			
			$context = stream_context_create($opts);
			$req = file_get_contents($where, false, $context);
			$decoded = json_decode($req, true);
			
			if (@array_key_exists('error', @$decoded[0])) {
				throw new HueException('Error - '.@$decoded[0]['error']['description']);
				exit;
			} else {
				return $decoded;
			}
			
		}
		
		protected function make_post_request($where, $data) {
			$postdata = json_encode($data);
			$opts = array('http' =>
				array(
					'method'  => 'POST',
					'header'  => 'Content-type: application/x-www-form-urlencoded',
					'content' => $postdata
				)
			);
			
			$context = stream_context_create($opts);
			$req = file_get_contents($where, false, $context);
			$decoded = json_decode($req, true);
			
			if (@array_key_exists('error', @$decoded[0])) {
				throw new HueException('Error - '.@$decoded[0]['error']['description']);
				exit;
			} else {
				return $decoded;
			}
			
		}		

	}
	
	class HueException extends \Exception {
	
	}
}
?>
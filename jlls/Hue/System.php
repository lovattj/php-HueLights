<?php
namespace jlls\Hue {
	class System {
	
	private $ip_address;
	private $username;
	private $api_root;
	private $last_response;
	
		public function __construct($incoming_ip_address, $incoming_username) {
			$this->ip_address = $incoming_ip_address;
			$this->username = $incoming_username;	
			$this->api_root = "http://".$this->ip_address."/api/";
		}
		
		public function __toString() {
			return (string)json_encode($this->last_response);
		}

			
		public function Lights() {
			return $this;
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
		
			public function DescribeAllLights() {
				try {
					$response = $this->make_get_request($this->api_root.$this->username."/lights");
					$this->last_response = $response;
					return $this;
				} catch (Exception $e) {
					throw $e;
				}
			}
			
			public function DescribeLight($light) {
				try {
					$response = $this->make_get_request($this->api_root.$this->username."/lights/".$light);
					return $response;
				} catch (Exception $e) {
					throw $e;
				}
			}
			
			public function LightOn($light) {
				try {
					$opts = Array();
					$opts['on']=true;
					$response = $this->Lights()->ModifyLight($light, $opts);
					$this->last_response = Array("light" => $light, "status" => "on");
					return $this;
					
				} catch (Exception $e) {
					throw $e;
				}
			}

			public function LightOff($light) {
				try {
					$opts = Array();
					$opts['on']=false;
					$response = $this->Lights()->ModifyLight($light, $opts);
					$this->last_response = Array("light" => $light, "status" => "off");
					return $this;
				} catch (Exception $e) {
					throw $e;
				}
			}
			
			public function LightBrightness($light, $brightness) {
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
					$response = $this->Lights()->ModifyLight($light, $opts);
					$this->last_response = $response;
					return $this;
				} catch (Exception $e) {
					throw $e;
				}
				
			}
			
			public function LightHue($light, $hue) {
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
					$response = $this->Lights()->ModifyLight($light, $opts);
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

		public function Info() {
			return $this;
			
		}
		
			public function DescribeIP() {
				return $this->ip_address;
			}
		
			public function DescribeUser() {
				return $this->username;			
			}
		
			public function DescribeConfiguration() {
				try {
					$response = $this->make_get_request($this->api_root."config");
					return $response;
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

	}
	
	class HueException extends \Exception {
	
	}
}
?>
<?php
require_once 'HostsFile.php';

class Domain2Ip {

	private $driver;
	private $hosts;
	
	public function __construct($driver, $hosts) {
		$this->driver = $driver;
		$this->hosts = $hosts;
	}

	public function add_all_ips($start_domain, $pattern_add) {
		$this->add_ip($start_domain);
		$this->hosts->save();

		$url = 'http://'.$start_domain;
		$raw = file_get_contents($url);
		$pattern = '/https?:\/\/([\w\.]+)/';
		
		if (preg_match_all($pattern, $raw, $m)) {
			$addresses = array_unique($m[1]);
			print_r($addresses);exit;//debug
			foreach ($addresses as $domain) {
				if (preg_match($pattern_add, $domain)) {
					$this->add_ip($domain);
				}
			}
			$this->hosts->save();
		}
	}

	private function add_ip($domain) {
		$ip = $this->driver->get_ip($domain);
		$this->hosts->delete_domain($domain);
		$this->hosts->add($ip, $domain);
		echo "$ip $domain\n";
	}
}

abstract class Ip_Driver_Abstract {
	
	public function send_post($url, $data) {
		if (strpos($url, 'http://') === false) {
			$url = 'http://' . $url;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch, CURLOPT_TIMEOUT, 150);
		curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		return curl_exec($ch);
	}

	public abstract function get_ip($domain);
}

class Ip_Driver_IpLookup extends Ip_Driver_Abstract {

	public function get_ip($domain) {
		$target = 'http://ip-lookup.net/domain.php';
		$data = array('domain' => $domain);
		$response = $this->send_post($target, $data);
		
		$pattern = '/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})<.*title="Lookup.../';

		if (preg_match($pattern, $response, $m)) {
			return $m[1];
		}
		if (strpos($domain, 'www.') === 0) {
			return $this->get_ip(substr($domain, 4));
		}
		throw new Exception('Cant find IP address:'.$domain);
	}
}

if (!isset($argv) || count($argv) < 3) {
	$help = <<<HELP

Usage: php Domain2Ip.php <domain> <pattern>

  domain: e.g. google.com
  pattern: regex to match which domain names to be added as well
           e.g. /google/


HELP;
	echo $help;
} else {
	$driver = new Ip_Driver_IpLookup();
	$hosts = new HostsFile('C:\Windows\System32\drivers\etc\hosts');
	$d2ip = new Domain2Ip($driver, $hosts);
	$d2ip->add_all_ips($argv[1], $argv[2]);
}
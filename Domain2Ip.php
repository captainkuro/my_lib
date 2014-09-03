<?php
require_once 'HostsFile.php';

class OPTION {
	public static $DEBUG = false;
	public static $HOSTS = 'C:\Windows\System32\drivers\etc\hosts';
}

class Domain2Ip {

	private $driver;
	private $hosts;
	
	public function __construct($driver, $hosts) {
		$this->driver = $driver;
		$this->hosts = $hosts;
	}

	public function add_all_ips($start_domain, $pattern_add, $start_url = null) {
		$this->add_ip($start_domain);
		$this->hosts->save();

		if (!$start_url) {
			$start_url = 'http://'.$start_domain;
		}
		$raw = file_get_contents($start_url);
		$pattern = '/https?:\/\/([\w\.]+)/';
		
		if (preg_match_all($pattern, $raw, $m)) {
			$addresses = array_unique($m[1]);

			if (OPTION::$DEBUG) {
				print_r($addresses);exit;//debug
			}

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

		$retry = 10;
		$response = curl_exec($ch);
		while (!$response && $retry--) {
			$response = curl_exec($ch);
		}
		return $response;
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

function obtain_option($code, $default = '') {
	global $argv;

	for ($i=1, $n=count($argv); $i<$n; $i++) {
		$part = "-{$code}=";
		if (strpos($argv[$i], $part) === 0) {
			return substr($argv[$i], strlen($part));
		}
	}
	return $default;
}

if (count($argv) < 3) {
	$help = <<<HELP

php Domain2Ip.php <DOMAIN> <PATTERN> [-s=<START_URL>] [-d=<0/1>] [-h=<HOST_FILE>]

Options:
  <DOMAIN>      : the first domain to be added to hosts file so we can 
                  open <START_URL>
  <PATTERN>     : regex to match which domain to be added
  -s=<START_URL>: by default <DOMAIN> will be used to grab the other domains
  -d=<0/1>      : if 1 then all domain candidates will be printed out but not 
                  added
  -h=<HOST_FILE>: where the hosts file is located

HELP;
	echo $help;
} else {
	$domain = $argv[1];
	$pattern = $argv[2];

	$start_url = obtain_option('s', null);
	OPTION::$DEBUG = (bool)obtain_option('d', false);
	OPTION::$HOSTS = obtain_option('h', OPTION::$HOSTS);

	$driver = new Ip_Driver_IpLookup();
	$hosts = new HostsFile(OPTION::$HOSTS);
	$d2ip = new Domain2Ip($driver, $hosts);
	$d2ip->add_all_ips($domain, $pattern, $start_url);

}
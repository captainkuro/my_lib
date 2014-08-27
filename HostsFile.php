<?php
/*
Parser for /etc/hosts file or equivalent
 */

class HostsFile {

	private $path = '';
	private $complete = array();
	private $ip_lines = array();
	private $domain_lines = array();

	public function __construct($file = '/etc/hosts') {
		$this->path = $file;
		$this->complete = file($file, FILE_IGNORE_NEW_LINES);
		
		$this->refresh();
	}

	public function refresh() {
		$this->ip_lines = array();
		$this->domain_lines = array();
		$pattern = '/^\s*([\d\.]+)\s+([\w\.]+)/';

		foreach ($this->complete as $i => $line) {
			if (preg_match($pattern, $line, $m)) {
				$ip = $m[1];
				$domain = $m[2];
				$this->ip_lines[$ip][] = $i;
				$this->domain_lines[$domain][] = $i;
			}
		}
	}

	public function add($ip, $domain) {
		$new_line = "$ip\t$domain";
		$i = count($this->complete);
		$this->complete[] = $new_line;
		$this->ip_lines[$ip][] = $i;
		$this->domain_lines[$domain][] = $i;
	}

	public function delete_ip($ip) {
		if (isset($this->ip_lines[$ip])) {
			foreach ($this->ip_lines[$ip] as $i) {
				unset($this->complete[$i]);
			}
			$this->refresh();
		}
	}

	public function delete_domain($domain) {
		if (isset($this->domain_lines[$domain])) {
			foreach ($this->domain_lines[$domain] as $i) {
				unset($this->complete[$i]);
			}
			$this->refresh();
		}
	}

	public function encode() {
		return implode("\n", $this->complete);
	}

	public function save() {
		file_put_contents($this->path, $this->encode());
	}
}

// $x = new HostsFile();
// $x->delete_domain('deepsea.co.id');
// $x->add('8.8.8.8', 'google.com');
// $x->delete_domain('www.deepsea.co.id');
// echo $x->encode();

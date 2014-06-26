<?php
/*
autoloader

benchmark

recursivedirectoryiterator

recusive scandir
*/

function browse_with_iterator($start_dir) {
	$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($start_dir, FilesystemIterator::SKIP_DOTS));
	$result = array();
	while ($it->valid()) {
			$filename = $it->getBasename();
			if (substr($filename, -4) === '.php') {
				$result[substr($filename, 0, -4)] = $it->key();
			}
		$it->next();
	}
	return $result;
}

class ScandirWrapper {
	protected $temp_result = array();

	public function browse($start_dir) {
		$this->temp_result = array();
		$this->recurse($start_dir);
		return $this->temp_result;
	}

	protected function recurse($start_dir) {
		$list = scandir($start_dir);
		for ($i=0, $n=count($list); $i<$n; ++$i) {
			$name = $list[$i];
			if ($name != '.' && $name != '..') {
				$path = $start_dir.'/'.$name;
				if (is_dir($path)) {
					$this->recurse($path);
				} else {
					if (substr($name, -4) === '.php') {
						$this->temp_result[substr($name, 0, -4)] = $path;
					}
				}
			}
		}
	}
}

function browse_with_scandir($start_dir) {
	$scanner = new ScandirWrapper();
	return $scanner->browse($start_dir);
}

$to_browse = '/var/www/kaspay-1.6/application/models';

$before = microtime(true);
$iterator_result = browse_with_iterator($to_browse);
$elapsed = (microtime(true) - $before);
echo "Recursive Iterator took: $elapsed seconds<br>\n";

$before = microtime(true);
$scandir_result = browse_with_scandir($to_browse);
$elapsed = (microtime(true) - $before);
echo "Recursive Scandir took: $elapsed seconds<br>\n";

echo "<pre>";
print_r($iterator_result);
print_r($scandir_result);
echo "</pre>";
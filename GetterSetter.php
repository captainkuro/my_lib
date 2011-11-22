<?php
/**
 * Menyediakan fasilitas get* dan set* untuk semua atribut private secara otomatis (pakai __call)
 *
 * Convention:
 * - protected $asdf akan mendapat getAsdf() dan setAsdf()
 * - protected $asdfJkl akan mendapat getAsdfJkl() dan setAsdfJkl()
 * - protected $_asdf akan mendapat get_asdf() dan set_asdf()
 * - protected $_asdf_jkl akan mendapat get_asdf_jkl() dan set_asdf_jkl()
 */
abstract class GetterSetter {
	public function __call($name, $params) {
		// $name nama method harus > 3 karakter "get*" atau "set*"
		if (!preg_match('/^[gs]et/', $name)) return;
		$refl = new ReflectionClass($this);
		$prop = substr($name, 3);
		$prop[0] = strtolower($prop[0]);
		if ($reflprop = $refl->getProperty($prop)) {
			if (strpos($name, 'get') === 0) {
				return $this->$prop;
			} else if (strpos($name, 'set') === 0) {
				$this->$prop = $params[0];
			}
		}
	}
}

// TEST RUN //
class A extends GetterSetter {
	protected $satuSapi;
	protected $_dua_sapi;
	
}

$a = new A();
$a->setSatuSapi('dua puluh ribu');
$a->set_dua_sapi('tiga puluh lima ribu');
print_r($a);
echo $a->getSatuSapi();
echo $a->get_dua_sapi();

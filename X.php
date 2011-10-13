<?php
/**
 * Helps you generate XML text
 *
 * $x->div(array('id' => 'isi'), $x->span('asdf'), 
 *   $x->div(
 *	   $x->span('george'),
 *	   $x->img(array('src' => 'asdf.jpg'))
 *   )
 * );
 * // Will result <div id="isi"><span>asdf</span><div><span>george</span><img src="asdf.jpg" /></div></div>
 */
class X {
	public function __call($name, $params) {
		$prex = '';
		if (is_array(current($params))) { // To specify tag attributes, assign those as array in first argument
			$args = array_shift($params);
			foreach ($args as $k => $v) {
				$prex .= ' ' . $k . '="' . str_replace('"', '&quot;', $v) . '"';
			}
		}
		if (empty($params)) { // Empty tags will be shown as <tag />
			return "<$name$prex />";
		}
		return "<$name$prex>" . implode('',$params) . "</$name>";
	}
	
	// If PHP >= 5.3.0
	/*
	public function __callStatic($name, $params) {
		$prex = '';
		if (is_array(current($params))) { // To specify tag attributes, assign those as array in first argument
			$args = array_shift($params);
			foreach ($args as $k => $v) {
				$prex .= ' ' . $k . '="' . str_replace('"', '&quot;', $v) . '"';
			}
		}
		if (empty($params)) { // Empty tags will be shown as <tag />
			return "<$name$prex />";
		}
		return "<$name$prex>" . implode('',$params) . "</$name>";
	}
	*/
}

/* Example: */
$x = new X;
echo 
$x->div(array('id' => 'isi'), 
	$x->span('asdf'), 
	$x->div(
		$x->span('george'),
		$x->img(array('src' => 'asdf.jpg'))
	)
);

/* If PHP >= 5.3.0
X::div(array('id' => 'isi'), 
	X::span('asdf'), 
	X::div(
		X::span('george'),
		X::img(array('src' => 'asdf.jpg'))
	)
);
*/
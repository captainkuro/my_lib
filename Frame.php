<?php
/**
 * Class Frame
 *
 * Menampilkan sebuah text panjang menjadi kotak
 */
 
class Frame {
	protected $_text = '';
	protected $_processed_text = null;
	protected $_width;
	protected $_height; // min_height
	protected $_margin = array(
		'top' => 0,
		'left' => 0,
		'right' => 0,
		'bottom' => 0,
	);
	protected $_pos = array(
		'x' => 0,
		'y' => 0,
	);
	protected $_border = array(
		'top' => '',
		'left' => '',
		'right' => '',
		'bottom' => '',
		'top_left' => '',
		'top_right' => '',
		'bottom_left' => '',
		'bottom_right' => '',
		'size' => 0,
	);
	protected $_canvas = array();
	protected $_children = array();
	// Other options
	protected $_opt = array();
	
	/**
	 * Constructor
	 */
	public function __construct($text, $width, $height) {
		$this->_text = $text;
		$this->_width = $width;
		$this->_height = $height;
	}
	
	/*** GETTERS AND SETTERS ***/
	public function text() {
		return $this->_text;
	}
	
	public function set_text($text) {
		$this->_text = $text;
		$this->_processed_text = null;
		return $this;
	}
	
	public function width() {
		return $this->_width;
	}
	
	public function set_width($width) {
		$this->_width = $width;
		return $this;
	}
	
	public function height() {
		return $this->_height;
	}
	
	public function set_height($height) {
		$this->_height = $height;
		return $this;
	}
	
	public function margin($pos) {
		return $this->_margin[$pos];
	}
	
	public function set_margin($key, $margin = 0) {
		if (is_array($key) && (count($key) == 4)) {
			$this->_margin['top'] = $key[0];
			$this->_margin['right'] = $key[1];
			$this->_margin['bottom'] = $key[2];
			$this->_margin['left'] = $key[3];
		} else if (is_array($key) && (count($key) == 2)) {
			$this->_margin['top'] = $key[0];
			$this->_margin['bottom'] = $key[0];
			$this->_margin['left'] = $key[1];
			$this->_margin['right'] = $key[1];
		} else {
			$this->_margin[$key] = $margin;
		}
		return $this;
	}
	
	public function border($key) {
		return $this->_border[$key];
	}
	
	public function set_border($key, $val = '') {
		// can supply array, top-right-bottom-left
		if (is_array($key) && (count($key) == 4)) {
			$this->_border['top'] = $key[0];
			$this->_border['right'] = $key[1];
			$this->_border['bottom'] = $key[2];
			$this->_border['left'] = $key[3];
		} else if (is_array($key) && (count($key) == 2)) {
			$this->_border['top'] = $key[0];
			$this->_border['bottom'] = $key[0];
			$this->_border['left'] = $key[1];
			$this->_border['right'] = $key[1];
		} else if ($key == 'corner') {
			$this->_border['top_left'] = $val;
			$this->_border['top_right'] = $val;
			$this->_border['bottom_left'] = $val;
			$this->_border['bottom_right'] = $val;
		} else if (!$val && $key != 'size') {
			$this->_border['top'] = $key;
			$this->_border['right'] = $key;
			$this->_border['bottom'] = $key;
			$this->_border['left'] = $key;
			$this->_border['top_left'] = $key;
			$this->_border['top_right'] = $key;
			$this->_border['bottom_left'] = $key;
			$this->_border['bottom_right'] = $key;
		} else {
			$this->_border[$key] = $val;
		}
		return $this;
	}
	
	public function x() {
		return $this->_pos['x'];
	}
	
	public function set_x($x) {
		$this->_pos['x'] = $x;
		return $this;
	}
	
	public function y() {
		return $this->_pos['y'];
	}
	
	public function set_y($y) {
		$this->_pos['y'] = $y;
		return $this;
	}
	
	public function option($key) {
		return $this->_opt[$key];
	}
	
	public function set_option($key, $val) {
		$this->_opt[$key] = $val;
		return $this;
	}
	
	public function children() {
		return $this->_children;
	}
	
	public function get_child($key) {
		return $this->_children[$key];
	}
	
	public function add_child($key = null, $child_frame) {
		if ($key) {
			$this->_children[$key] = $child_frame;
		} else {
			$this->_children[] = $child_frame;
		}
		return $this;
	}
	
	public function remove_child($key) {
		unset($this->_children[$key]);
		return $this;
	}
	
	public function canvas() {
		return $this->_canvas;
	}
	
	public function char($row, $col) {
		return $this->_canvas[$row][$col];
	}
	
	public function final_width() {
		return 2*$this->_border['size'] + $this->_margin['left'] + $this->_width + $this->_margin['right'];
	}
	
	public function final_height() {
		return count($this->_canvas);
	}
	
	/*** OTHER FUNCTIONS ***/
	/**
	 * Potong2 $_text sesuai width
	 */
	public function process_text() {
		// @TODO teknik memotong yg lebih baik
		$this->_processed_text = str_split($this->_text, $this->_width);
	}
	
	/**
	 * Generate the end view to $canvas
	 */
	public function render($echo = false) {
		if ($this->_processed_text === null) {
			$this->process_text();
		}
		$result = array();
		// Get max width (without border)
		$max_width = $this->_margin['left'] + $this->_width + $this->_margin['right'];
		// Append border top
		for ($i = 0; $i < $this->_border['size']; $i++) {
			$result[] = str_repeat($this->_border['top_left'], $this->_border['size']) 
				. str_repeat($this->_border['top'], $max_width) 
				. str_repeat($this->_border['top_right'], $this->_border['size']) 
				;
		}
		// Append margin top
		for ($i = 0; $i < $this->_margin['top']; $i++) {
			$result[] = str_repeat($this->_border['left'], $this->_border['size']) 
				. str_repeat(' ', $max_width) 
				. str_repeat($this->_border['right'], $this->_border['size']) 
				;
		}
		// Append text
		$n = count($this->_processed_text);
		for ($i = 0; $i < $n; $i++) {
			$result[] = str_repeat($this->_border['left'], $this->_border['size']) 
				. str_repeat(' ', $this->_margin['left']) 
				. str_pad($this->_processed_text[$i], $this->_width, ' ') 
				. str_repeat(' ', $this->_margin['right']) 
				. str_repeat($this->_border['right'], $this->_border['size']) 
				;
		}
		// Append trailing lines
		for ($i = ($this->_height - $n); $i > 0; $i--) {
			$result[] = str_repeat($this->_border['left'], $this->_border['size']) 
				. str_repeat(' ', $max_width) 
				. str_repeat($this->_border['right'], $this->_border['size']) 
				;
		}
		// Append margin bottom
		for ($i = 0; $i < $this->_margin['bottom']; $i++) {
			$result[] = str_repeat($this->_border['left'], $this->_border['size']) 
				. str_repeat(' ', $max_width) 
				. str_repeat($this->_border['right'], $this->_border['size']) 
				;
		}
		// Append border bottom
		for ($i = 0; $i < $this->_border['size']; $i++) {
			$result[] = str_repeat($this->_border['bottom_left'], $this->_border['size']) 
				. str_repeat($this->_border['bottom'], $max_width) 
				. str_repeat($this->_border['bottom_right'], $this->_border['size']) 
				;
		}
		// @TODO embedding children
		foreach ($this->_children as $key => $child) {
			$child->render();
			$ny = min($child->y() + $child->final_height(), count($result));
			for ($j = 0, $y = $child->y(); $y < $ny; $j++, $y++) {
				$nx = min($child->x() + $child->final_width(), $this->final_width());
				for ($i = 0, $x = $child->x(); $x < $nx; $i++, $x++) {
					$result[$y][$x] = $child->char($j, $i);
				}
			}
		}
		$this->_canvas = $result;
		if ($echo) return implode("\n", $result);
	}
}

/*** DRIVER ***/
/*
$f = new Frame('lakdjf lasj falsfowieu lk flasjf lwepoia lkjfa;ls a;lsjfosie lkasjfl sdfoaiweu alsjfaoiwuiaorglk dlsajflsdjfie lakjreuaosdfjslkdf lakjljeoiausf alskjfleoaueoijfs k sldkfjaslfjoeiuaoij lskfjalsfj',
	40, 20);
$f->set_margin(array(1, 2, 3, 4))
	->set_border('size', 3)
	->set_border(array('-', '|'))
	->set_border('corner', 'o')
	;
$g = new Frame('alkjdalfkjs lfajsleuoraiusflkjslfasudoiu', 9, 5);
$g->set_border('size', 2)
	->set_border(array('+', '+'))
	->set_border('corner', 'x')
	->set_x(3)
	->set_y(3);
$h = new Frame('ldkja flsdjfoajoiuoeiufaskljdfwpeifpoweflksjdflwjeoiuorigtlkjalgjlkfdjoweiuroiuaorjljdfoirutalkj', 
	35, 5);
$h->set_margin(array(3, 3))
	->set_border('size', 1)
	->set_border('a')
	->set_x(15)
	->set_y(5);
$f->add_child(null, $g)
	->add_child(null, $h);
echo '<pre>'.$f->render(true).'</pre>';
*/
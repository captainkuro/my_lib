<?php
/**
 * Class OString v 0.1a
 * object-oriented string
 *
 * Class yang membungkus string menjadi OO
 * agar dapat dilakukan method chaining terhadap string
 * 
 * Bayangkan misal kita bisa melakukan:
 * echo OString::factory("astaga naga")->ucfirst()->substr(1,5)->strtolower();
 *
 * Note: beberapa method memerlukan class OArray
 *
 * @author  captain_kuro
 */
 
class OString {
    /**
     * String asli sebenarnya
     * @type    string
     */
    protected $_string;
    
    /**
     * Factory pattern
     * @param   string $str default ''
     * @return  OString
     */
    public static function factory($str = '') {
		return new OString($str);
	}
	
    /**
     * Constructor
     * @param   string $str default ''
     * @return  void
     */
	public function __construct($str = '') {
		$this->_string = $str;
	}
    
    /**
     * Magic method jika class ini di echo
     * @return  string
     */
    public function __toString() {
		return (string)$this->_string;
	}
    
    /**
     * Mengembalikan string asli
     * @return  string
     */
    public function getString() {
        return $this->_string;
    }
    
    /**
     * Setter
     * @param   string $string
     * @return  OString
     */
    public function setString($string) {
        $this->_string = $string;
        return $this;
    }
    
    /**
     * Mengembalikan object OString baru yg identik dengan dirinya
     * @return  OString
     */
    public function copy() {
        return new OString($this->_string);
    }

    /*** STANDARD String Function BEGIN ***/
    // semua fungsi string disulap menjadi method:
    // semua method mengembalikan $this biar bisa di method-chaining
    
    /**
     * Returns a string with backslashes before characters that are listed in charlist parameter. 
     * @param   string  $charlist   A list of characters to be escaped. If charlist contains characters \n, \r etc., they are converted in C-like style, while other non-alphanumeric characters with ASCII codes lower than 32 and higher than 126 converted to octal representation
     * @return  OString
     */
    public function addcslashes($charlist) {
        $this->_string = addcslashes($this->_string, $charlist);
        return $this;
    }
    
    /**
     * Returns a string with backslashes before characters that need to be quoted in database queries etc. 
     * These characters are single quote ('), double quote ("), backslash (\) and NUL (the NULL byte). 
     * @return  OString
     */
    public function addslashes() {
        $this->_string = addslashes($this->_string);
        return $this;
    }
    
    /**
     * Returns an ASCII string containing the hexadecimal representation of str. 
     * The conversion is done byte-wise with the high-nibble first. 
     * @return  OString
     */
    public function bin2hex() {
        $this->_string  = bin2hex($this->_string);
        return $this;
    }
    
    /**
     * This function is an alias of: rtrim(). 
     */
    public function chop($charlist = ' \t\n\r\0\x0B') {
        return $this->rtrim($charlist);
    }
    
    /**
     * This function returns a string with whitespace stripped from the end of str . 
     * @param   string  $charlist   You can also specify the characters you want to strip, by means of the charlist parameter. Simply list all characters that you want to be stripped. With .. you can specify a range of characters. 
     * @return  OString
     */
    public function rtrim($charlist = ' \t\n\r\0\x0B') {
        $this->_string = rtrim($this->_string, $charlist);
        return $this;
    }
    
    /*** STANDARD String Function END ***/
    
    /*** POSIX Regex Function BEGIN ***/
    // WARNING: These functions are deprecated as of PHP 5.3.0
    /*** POSIX Regex Function END ***/
    
    /*** PCRE Function BEGIN ***/
    /*** PCRE Function END ***/
    
    /*** URL Function BEGIN ***/
    /*** URL Function END ***/
}

/* Driver, untuk mengetes class OString */
$a = new OString('alkjslkjoiu jlflaksjf');
echo $a;
echo OString::factory('asdf   ')->copy()->copy()->copy()->chop(), '\'';

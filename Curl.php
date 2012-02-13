<?php
/**
 * Wrapper for cURL functions
 * Uses cURL extension
 * @author Khandar William
 */
class Curl 
{
	/**
	 * Information about last HTTP request
	 * @var array contains 'header' and 'content' key
	 */
	protected $_lastRequest;
	/**
	 * Information about last HTTP response
	 * @var array contains 'header' and 'content' key
	 */
	protected $_lastResponse;
	/**
	 * Information about last cURL transfer
	 * @var array same as curl_getinfo()
	 */
	protected $_lastInfo;
	/**
	 * Options to be used in curl_setopt_array()
	 * @var array
	 */
	protected $_options;
	/**
	 * The cURL resource handle
	 * @var resource
	 */
	protected $_curl;
	
	/**
	 * Constructor
	 * @param array $options array of default cURL options
	 */
	public function __construct($options = null)
	{
		$this->_lastRequest = array('header' => '', 'content' => '');
		$this->_lastResponse = array('header' => '', 'content' => '');
		$this->_lastInfo = array();
		$this->_options = array(
			CURLOPT_HEADER => 1,
			CURLINFO_HEADER_OUT => 1,
			CURLOPT_RETURNTRANSFER => 1,
		);
		// merge given options with internal default options
		if (is_array($options)) {
			$this->_options = $this->_options + $options;
		}
		$this->_curl = null;
	}
	
	/**
	 * Destructor
	 */
	public function __destruct() {
		if (is_resource($this->_curl)) {
			curl_close($this->_curl);
		}
	}
	
	public function getLastRequest()
	{
		return $this->_lastRequest;
	}
	
	public function getLastRequestHeader()
	{
		return $this->_lastRequest['header'];
	}
	
	public function getLastRequestContent()
	{
		return $this->_lastRequest['content'];
	}
	
	public function getLastResponse()
	{
		return $this->_lastResponse;
	}
	
	public function getLastResponseHeader()
	{
		return $this->_lastResponse['header'];
	}
	
	public function getLastResponseContent()
	{
		return $this->_lastResponse['content'];
	}
	
	public function getOptions()
	{
		return $this->_options;
	}
	
	/**
	 * Return all or a specific information about last transfer
	 * @param string $key if NULL then return all information
	 * @return array return NULL if $key does not exist
	 */
	public function getLastInfo($key = null)
	{
		if (is_null($key)) {
			return $this->_lastInfo;
		}
		if (isset($this->_lastInfo[$key])) {
			return $this->_lastInfo[$key];
		}
		return null;
	}
	
	/**
	 * Execute a transfer to given URL (with optional options)
	 * The result can be accessed via getLastResponse()
	 * @param string $url destination
	 * @param array $options to add or override default options JUST for this transfer
	 */
	public function execute($url, $options = null)
	{
		$options = $this->_options + (array)$options;
		$options[CURLOPT_URL] = $url;
		
		if (!$this->_curl) {
			$ch = curl_init();
		} else {
			$ch = $this->_curl;
		}
		
		curl_setopt_array($ch, $options);
		$reply = curl_exec($ch);
		$info = curl_getinfo($ch);
		
		$this->_curl = $ch;
		$this->_lastInfo = $info;
		// Assign _lastRequest
		if (isset($info['request_header'])) {
			$this->_lastRequest['header'] = $this->_parseHeader($info['request_header']);
		} else {
			$this->_lastRequest['header'] = '';
		}
		if (isset($options[CURLOPT_POSTFIELDS])) {
			$this->_lastRequest['content'] = $options[CURLOPT_POSTFIELDS];
		} else {
			$this->_lastRequest['content'] = '';
		}
		// Assign _lastResponse
		if (preg_match('/^HTTP/', $reply)) {
			$pos = strpos($reply, "\r\n\r\n");
			$this->_lastResponse['header'] = $this->_parseHeader(substr($reply, 0, $pos+2));
			$this->_lastResponse['content'] = substr($reply, $pos+4);
		} else {
			$this->_lastResponse['header'] = '';
			$this->_lastResponse['content'] = $reply;
		}
	}
	
	/**
	 * Parse HTTP header text to associative array
	 * @param string $header the http header
	 * @return array
	 */
	protected function _parseHeader($header)
	{
		$parsed = array();
		$exploded = explode("\r\n", $header);
		// First line is special
		$parsed[] = array_shift($exploded);
		// parse the rest of the lines
		foreach ($exploded as $line) {
			if (preg_match('/([\w\-]+): (.*)/', $line, $m)) {
				$parsed[$m[1]] = $m[2];
			}
		}
		return $parsed;
	}
}
/*
$a = new Curl();
$a->execute('http://inception.davepedu.com/');
print_r($a);
*/
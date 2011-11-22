<?php
/**
 * A very simple implementation of a URL shortener application
 * bit.ly de.tk kom.ps tinyurl.com u.nu tk2.us pendek.in goo.gl adf.ly
 * @author captain_kuro <me@captainkuro.com>
 * The OO version
 * 
 * Database table `short`
 * 	id, UNSIGNED INT AUTO_INCREMENT PRIMARY_KEY
 * 	key, VARCHAR UNIQUE
 * 	url, TEXT
 * 	visit, UNSIGNED INT DEFAULT 0

CREATE TABLE IF NOT EXISTS `data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `url` text NOT NULL,
  `visit` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
 */
class Short {
	private $full   = 'http://localhost/my_lib/short.class.php/'; // CHANGEME
	private $base   = '/my_lib/short.class.php/'; // CHANGEME
	private $conn   = null; // Database connection
	private $row    = null; // Last row retrieved 
	private $key    = null; // Key being requested or generated
	private $url    = null; // URL being submitted or retrieved
	private $error  = null; // Error message
	private $result = null; // Result message

	// The main program is broken down to these functions

	public function startup() {
		// Database connection initiated
		$user = 'short'; // CHANGEME
		$pass = 'short'; // CHANGEME
		$db   = 'short'; // CHANGEME
		$host = 'localhost'; // CHANGEME
		
		$this->conn = mysql_connect($host, $user, $pass);
		if (!$this->conn) throw new Exception('Database connection error ' . mysql_error());
		if (!mysql_select_db($db, $this->conn)) throw new Exception('Database name error');
	}

	public function shutdown() {
		// Database connection terminated
		mysql_close($this->conn);
	}

	public function is_requesting_url() {
		// Parse the key requested
		if (strlen($_SERVER['REQUEST_URI']) > strlen($this->base)) {
			$this->key = substr($_SERVER['REQUEST_URI'], strlen($this->base));
			return true;
		} else {
			return false;
		}
	}

	public function is_submitting_url() {
		// Retrieve the submitted URL
		if (isset($_POST['url'])) {
			$this->url = $_POST['url'];
			if (get_magic_quotes_gpc()) {
				$this->url = stripslashes($this->url);
			}
			return true;
		} else {
			return false;
		}
	}

	public function is_key_exist() {
		$query = sprintf("SELECT * FROM `data` WHERE `key`='%s' LIMIT 1", mysql_real_escape_string($this->key));
		$result = mysql_query($query, $this->conn);
		if (!$result) {$this->error = 'Key not found'; return false;}
		$this->row = mysql_fetch_assoc($result);
		if (!$this->row) {$this->error = 'Key not found'; return false;}
		$this->url = $this->row['url'];
		return true;
	}

	public function redirect_to_url() {
		$query = sprintf("UPDATE `data` SET `visit`=`visit`+1 WHERE `id`=" . $this->row['id']);
		@mysql_query($query);
		header('Location: ' . $this->url);
	}

	public function is_url_valid() {
		// URL match
		if (($parsed = @parse_url($this->url)) === false) {$this->error = 'Not a URL'; return false;}
		if (!isset($parsed['scheme'])) $this->url = 'http://' . $this->url;
		// Not this app URL
		if (strpos($this->url, $this->full) === 0) {$this->error = 'Hey that\'s mine'; return false;}
		// Not another shortener (optional)
		//if (in_array($parsed['host'], array('bit.ly', 'tinyurl.com', 'u.nu', 'de.tk', 'kom.ps', 'adf.ly', 'goo.gl', 'tk2.us', 'pendek.in'))) {$this->error = 'Hey that\'s just another short URL'; return false;}
		// No bad URL (code it yourself)
		// No duplicate
		$query = sprintf("SELECT * FROM `data` WHERE `url`='%s' LIMIT 1", mysql_real_escape_string($this->url));
		if (($r = @mysql_query($query, $this->conn)) && ($ro = @mysql_fetch_assoc($r))) {$this->key = $ro['key']; return true;}
		// Valid, so now insert it
		$query = sprintf("INSERT INTO `data` (`url`) VALUES ('%s')", mysql_real_escape_string($this->url));
		if (!mysql_query($query, $this->conn)) {$this->error = 'Error inserting new URL'; return false;}
		$id = mysql_insert_id($this->conn);
		$this->key = base_convert($id, 10, 36);
		$query = sprintf("UPDATE `data` SET `key`='%s' WHERE `id`=%d", mysql_real_escape_string($this->key), $id);
		if (!mysql_query($query, $this->conn)) {$this->error = 'Error generating new key'; return false;}
		return true;
	}

	public function display_page() {
	?>
	<html>
		<head><title>Very Simple URL Shortener</title></head>
		<body>
			<?php if ($this->error) : ?>
				<div class="error"><?php echo $this->error ?></div>
			<?php endif ?>
			<form method="post" action="<?php echo $this->base ?>">
				URL:
				<input name="url" type="text" />
				<input type="submit" name="submit" value="Shorten" />
			</form>
			<?php if ($this->result) : ?>
				<div class="result"><?php echo $this->result ?></div>
			<?php endif ?>
		</body>
	</html>
	<?php
	}

	public function display_page_with_result() {
		$new = $this->full . $this->key;
		$old = $this->url;
		$this->result = "Short URL: <a href='$new' target='_blank'>$new</a> from the original <a href='$old' target='_blank'>$old</a>";
		$this->display_page();
	}

	public function display_page_with_error() {
		if (!$this->error) $this->error = 'Error occured';
		$this->display_page();
	}

	public function run() {
		// The "main program", the code literally explains itself
		$this->startup();
		if ($this->is_requesting_url()) {
			if ($this->is_key_exist()) {
				$this->redirect_to_url();
			} else {
				$this->display_page_with_error();
			}
		} else if ($this->is_submitting_url()) {
			if ($this->is_url_valid()) {
				$this->display_page_with_result();
			} else {
				$this->display_page_with_error();
			}
		} else {
			$this->display_page();
		}
		$this->shutdown();
	}

}
/* Usage: */
$a = new Short;
$a->run();

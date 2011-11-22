<?php
/**
 * A very simple implementation of a URL shortener application
 * bit.ly de.tk kom.ps tinyurl.com u.nu tk2.us pendek.in goo.gl adf.ly
 * @author captain_kuro <me@captainkuro.com>
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
// Global "variables" connecting all functions
class G {
	public static $full   = 'http://localhost/short.php/'; // CHANGEME
	public static $base   = '/short.php/'; // CHANGEME
	public static $conn   = null; // Database connection
	public static $row    = null; // Last row retrieved 
	public static $key    = null; // Key being requested or generated
	public static $url    = null; // URL being submitted or retrieved
	public static $error  = null; // Error message
	public static $result = null; // Result message
}

// The main program is broken down to these functions

function startup() {
	// Database connection initiated
	$user = 'short'; // CHANGEME
	$pass = 'short'; // CHANGEME
	$db   = 'short'; // CHANGEME
	$host = 'localhost'; // CHANGEME
	
	G::$conn = mysql_connect($host, $user, $pass);
	if (!G::$conn) throw new Exception('Database connection error ' . mysql_error());
	if (!mysql_select_db($db, G::$conn)) throw new Exception('Database name error');
}

function shutdown() {
	// Database connection terminated
	mysql_close(G::$conn);
}

function is_requesting_url() {
	// Parse the key requested
	if (strlen($_SERVER['REQUEST_URI']) > strlen(G::$base)) {
		G::$key = substr($_SERVER['REQUEST_URI'], strlen(G::$base));
		return true;
	} else {
		return false;
	}
}

function is_submitting_url() {
	// Retrieve the submitted URL
	if (isset($_POST['url'])) {
		G::$url = $_POST['url'];
		if (get_magic_quotes_gpc()) {
			G::$url = stripslashes(G::$url);
		}
		return true;
	} else {
		return false;
	}
}

function is_key_exist() {
	$query = sprintf("SELECT * FROM `data` WHERE `key`='%s' LIMIT 1", mysql_real_escape_string(G::$key));
	$result = mysql_query($query, G::$conn);
	if (!$result) {G::$error = 'Key not found'; return false;}
	G::$row = mysql_fetch_assoc($result);
	if (!G::$row) {G::$error = 'Key not found'; return false;}
	G::$url = G::$row['url'];
	return true;
}

function redirect_to_url() {
	$query = sprintf("UPDATE `data` SET `visit`=`visit`+1 WHERE `id`=" . G::$row['id']);
	@mysql_query($query);
	header('Location: ' . G::$url);
}

function is_url_valid() {
	// URL match
	if (($parsed = @parse_url(G::$url)) === false) {G::$error = 'Not a URL'; return false;}
	if (!isset($parsed['scheme'])) G::$url = 'http://' . G::$url;
	// Not this app URL
	if (strpos(G::$url, G::$full) === 0) {G::$error = 'Hey that\'s mine'; return false;}
	// Not another shortener (optional)
	//if (in_array($parsed['host'], array('bit.ly', 'tinyurl.com', 'u.nu', 'de.tk', 'kom.ps', 'adf.ly', 'goo.gl', 'tk2.us', 'pendek.in'))) {G::$error = 'Hey that\'s just another short URL'; return false;}
	// No bad URL (code it yourself)
	// No duplicate
	$query = sprintf("SELECT * FROM `data` WHERE `url`='%s' LIMIT 1", mysql_real_escape_string(G::$url));
	if (($r = @mysql_query($query, G::$conn)) && ($ro = @mysql_fetch_assoc($r))) {G::$key = $ro['key']; return true;}
	// Valid, so now insert it
	$query = sprintf("INSERT INTO `data` (`url`) VALUES ('%s')", mysql_real_escape_string(G::$url));
	if (!mysql_query($query, G::$conn)) {G::$error = 'Error inserting new URL'; return false;}
	$id = mysql_insert_id(G::$conn);
	G::$key = base_convert($id, 10, 36);
	$query = sprintf("UPDATE `data` SET `key`='%s' WHERE `id`=%d", mysql_real_escape_string(G::$key), $id);
	if (!mysql_query($query, G::$conn)) {G::$error = 'Error generating new key'; return false;}
	return true;
}

function display_page() {
?>
<html>
	<head><title>Very Simple URL Shortener</title></head>
	<body>
		<?php if (G::$error) : ?>
			<div class="error"><?php echo G::$error ?></div>
		<?php endif ?>
		<form method="post" action="<?php echo G::$base ?>">
			URL:
			<input name="url" type="text" />
			<input type="submit" name="submit" value="Shorten" />
		</form>
		<?php if (G::$result) : ?>
			<div class="result"><?php echo G::$result ?></div>
		<?php endif ?>
	</body>
</html>
<?php
}

function display_page_with_result() {
	$new = G::$full . G::$key;
	$old = G::$url;
	G::$result = "Short URL: <a href='$new' target='_blank'>$new</a> from the original <a href='$old' target='_blank'>$old</a>";
	display_page();
}

function display_page_with_error() {
	if (!G::$error) G::$error = 'Error occured';
	display_page();
}

// The "main program", the code literally explains itself
startup();
if (is_requesting_url()) {
	if (is_key_exist()) {
		redirect_to_url();
	} else {
		display_page_with_error();
	}
} else if (is_submitting_url()) {
	if (is_url_valid()) {
		display_page_with_result();
	} else {
		display_page_with_error();
	}
} else {
	display_page();
}
shutdown();
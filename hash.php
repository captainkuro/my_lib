<?php
if (isset($_GET['md5'])) {
	require 'functions.php';
	echo "<pre>".print_r(hash_all($_GET['md5']), true)."</pre><br/>";
} else {
}
//print_r($_SERVER);
?>
<html>
	<body>
		<form action="?" method="get">
			String: <input name="md5" />
		</form>
	</body>
</html>

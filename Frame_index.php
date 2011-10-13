<?php require_once 'Frame.php' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>No CSS, &lt;pre&gt; Only</title>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
	<style type="text/css">
		body {
			background-color: #000;
			color: #fff;
			font-family: Courier New;
		}
		#wrapper {
			margin: auto;
			width: 645px;
		}
	</style>
</head>
<body>
<div id="wrapper">
<pre>
<?php
$f = new Frame('C O M I N G   S O O N', 21, 1);
$f->set_margin(array(7, 20, 7, 20))
	->set_border('size', 1)
	->set_border('corner', 'x')
	->set_border(array('+', '+'));
echo $f->render(true);
?>
</pre>
</div>
</body>
</html>
<?php
/**
URL: http://www.alternateinterior.com/2007/05/multi-threading-strategies-in-php.html

PHP does not have threading anywhere in its massive core. We can, however, fake 
it by relying on the underlying operating system’s multitasking abilities instead 
of PHP. This article will show you how.

PHP has no built in support for threading. But there can still be times when you’
ve got lengthy code to run and idle CPU cyles you’d like to capitalize on. We can 
treat child processes as threads.

I created a PHP4-compatible class named Thread to abstract away the details of 
process management. That class follows:
[class Thread]

The constructor and member variables should be considered private.

$thread = Thread::create("file.php"); instanciates a thread. 
$thread->tell("command"); communicates to it, and 
$thread->listen(); captures its output. 
$thread->isActive(); exposes whether the code is done running, and 
$thread->close() releases its references.

To demonstrate it’s usefullnes, I created a simple application to calculate 
factorials. It uses a common recursive algorithm. It’s notably inefficent but 
that makes threading easier to understand.

	function fact ($n) {
		if ($n < 2) return $n;
		return fact($n - 1) + fact ($n - 2);
	}

Finally, it’s all glued together like this:

	include ("Thread.php");
	$t2 = Thread::create("t2.php");
	$t3 = Thread::create("t3.php");
	$t4 = Thread::create("t4.php");
	$t5 = Thread::create("t5.php");
	while ($t2->isActive() || $t3->isActive() || $t4->isActive() || $t5->isActive()) {
		echo $t2->listen();
		echo $t3->listen();
		echo $t4->listen();
		echo $t5->listen();
	}
	$t2->close();
	$t3->close();
	$t4->close();
	$t5->close();
	echo "Main thread done\n";
	
Each of $t2, $t3, $t4 and $t5 will often represent a discrete task. 
Therefore, more interaction with the object after listening to it will have 
more processing. Consider:

    $tHVAC = Thread::create("HVAC.php");
    $tHVAC->tell("start");
    do {
		$tHVAC->tell("get current temperature");
		$temp = $tHVAC->listen();
		if ($temp > 73) {
			$tHVAC->tell("start air conditioning");
		} else if ($temp < 65) {
			$tHVAC->tell(”start heater”);
		}
    } while (/* application loop );
	
This isn’t true multithreading. We’re just running multiple processes, and 
facilitating communication between them. As each thread is a separate instance 
or part of your application, its entry point needs to re-include all code you’d 
normally need: functions and classes established at the time you create a thread 
are not automatically visable to the new thread. Performance isn’t great, as PHP 
has to re-parse every file multiple times.

Also, my samples have relied on CLI access to PHP. It is entirely possible to 
channel your work through your webserver to leverage load balancers or other 
performance infastructure you have. You’ll have to modify the details of the 
Thread class, but it’s doable by using curl or wget as a proxy. 
*/

class Thread {
	var $pref ; // process reference
	var $pipes; // stdio
	var $buffer; // output buffer
	
	private function Thread() {
		$this->pref = 0;
		$this->buffer = "";
		$this->pipes = (array)NULL;
	}
	
	public static function Create ($file) {
		$t = new Thread;
		$descriptor = array (0 => array ("pipe", "r"), 1 => array ("pipe", "w"), 2 => array ("pipe", "w"));
		$t->pref = proc_open ("php -q $file ", $descriptor, $t->pipes);
		stream_set_blocking ($t->pipes[1], 0);
		return $t;
	}
	
	public function isActive () {
		$this->buffer .= $this->listen();
		$f = stream_get_meta_data ($this->pipes[1]);
		return !$f["eof"];
	}
	
	public function close () {
		$r = proc_close ($this->pref);
		$this->pref = NULL;
		return $r;
	}
	
	public function tell ($thought) {
		fwrite ($this->pipes[0], $thought);
	}
	
	public function listen () {
		$buffer = $this->buffer;
		$this->buffer = "";
		while ($r = fgets ($this->pipes[1], 1024)) {
			$buffer .= $r;
		}
		return $buffer;
	}
	
	public function getError () {
		$buffer = "";
		while ($r = fgets ($this->pipes[2], 1024)) {
			$buffer .= $r;
		}
		return $buffer;
	}
}

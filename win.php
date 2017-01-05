<?php
// win tools
class WinTools {
	private $active;
	private $commands;

	public function __construct() {
		$this->commands = [
			'1' => ['Test DNS', 'ping -t 210.16.120.139'],
			'2' => ['Shutdown in 2 hours', 'shutdown -s -t 7200'],
		];
	}

	public function run() {
		$this->active = true;
		while ($this->active) {
			$this->print_menu();
			$input = fgets(STDIN);
			$this->interpret_input(trim($input));
		}
	}

	private function print_menu() {
		$text = '';
		foreach ($this->commands as $key => $value) {
			list($label, $comm) = $value;
			$text .= "[$key] $label\n";
		}
		
		$text .= "[q]/[exit] Close\n\n";
		$text .= 'Choose: ';
		echo $text;
	}

	private function interpret_input($input) {
		echo "You chose [{$input}]\n\n";

		if ($input === 'exit' || $input === 'q') {
			$this->active = false;
			echo "Goodbye\n";
			return;
		} else if (isset($this->commands[$input])) {
			list($label, $comm) = $this->commands[$input];
			system($comm);
			return;
		} else {
			echo "UNRECOGNIZED\n";
		}
		echo "Press Enter...";
		fgets(STDIN);
	}
}

$p = new WinTools();
$p->run();
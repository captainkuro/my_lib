<?php
/**
 * Log Parser
 *
 * Menerima suatu file log
 * lalu memparsing per 1 baris
 * 
 * @author	captain_kuro
 */
 
class Log_Parser {
	protected $_file_name = '';
	protected $_file_handler = null;
	protected $_options = array();
    
    const STATE_WAIT_MESSAGE = 0;
    const STATE_WAIT_HEADER = 1;
    const STATE_WAIT_FOOTER = 2;
	
    /**
     * Factory pattern
     */
	public static function factory($file_path, $options = array()) {
		return new Log_Parser($file_path, $options);
	}
	
	/**
	 * Constructor
	 * @param	string	$file_path	path menuju ke file log
	 */
	public function __construct($file_path, $options = array()) {
		$this->_file_name = $file_path;
		$this->_file_handler = fopen($file_path, 'r');
		if (!$this->_file_handler) throw new Exception('File open error');
		$this->_options = $options;
	}
	
	/**
	 * Destructor
	 */
	public function __destruct() {
		@fclose($this->_file_handler);
	}
	
	/**
	 * Fungsi utama
	 */
	public function parse() {
        // constants:
		$pattern_header             = '/^([0-9]{4}-[0-9]{2}-[0-9]{2}) +([0-9]{2}:[0-9]{2}:[0-9]{2},[0-9]{3}) +\\[(main|Thread-[0-9]+)\\] +([A-Z]+) +: +(.*(top up|transfer|purchase|responding with response code).*)$/im';
		$pattern_footer             = '/^([0-9]{4}-[0-9]{2}-[0-9]{2}) +([0-9]{2}:[0-9]{2}:[0-9]{2},[0-9]{3}) +\\[(main|Thread-[0-9]+)\\] +([A-Z]+) +: +(.*(response iso message).*)$/im';
		$pattern_important          = '/^([0-9]{4}-[0-9]{2}-[0-9]{2}) +([0-9]{2}:[0-9]{2}:[0-9]{2},[0-9]{3}) +\\[(main|Thread-[0-9]+)\\] +((?!ERROR)(?!INFO)[A-Z]+) +: +(.*(Responding with response code|:).*)$/im';
        $pattern_entry              = '/^([0-9]{4}-[0-9]{2}-[0-9]{2}) +([0-9]{2}:[0-9]{2}:[0-9]{2},[0-9]{3}) +\\[(main|Thread-[0-9]+)\\] +([A-Z]+) +: +([a-z ]+) *(:|=) *(.*)$/im';
        $pattern_message            = '/^([0-9]{4}-[0-9]{2}-[0-9]{2}) +([0-9]{2}:[0-9]{2}:[0-9]{2},[0-9]{3}) +\\[(main|Thread-[0-9]+)\\] +([A-Z]+) +: +Message :(.*)$/im';
        $pattern_header_topup       = '/^.*top up.*$/im';
        $pattern_header_purchase    = '/^.*purchase.*$/im';
        $pattern_header_user        = '/^.*user transfer.*$/im';
        $pattern_header_receive     = '/^.*receive transfer.*$/im';
        $file_topup     = 'topup.output';
        $file_purchase  = 'purchase.output';
        $file_user      = 'user_transfer.output';
        $file_receive   = 'receive_transfer.output';
        $file_else      = 'else.output';
        $csv_topup      = 'topup.csv';
        $csv_purchase   = 'purchase.csv';
        $csv_user       = 'user_transfer.csv';
        $csv_receive    = 'receive_transfer.csv';
        $csv_else       = 'else.csv';
		
        // variables:
        $current_state = self::STATE_WAIT_MESSAGE;
        $buffer = '';
        $buffer_csv = array();
        $n = 0;
        $current_handler = $csv_handler = null;
        $message_no = '';
        $file_kind = '';
        
        // initiate:
        $ftemp = fopen($csv_topup, 'w');
        fwrite($ftemp, 'Merchant ID;User Card Number;EDC ID;Transaction Amt;Transmission Timestamp;Transaction Time;Transaction Date;Balance;Invoice Number;Request ISO Message;Responding with ISO code;Response ISO Message' . "\n");
        fclose($ftemp);
        $ftemp = fopen($csv_purchase, 'w');
        fwrite($ftemp, 'Merchant ID;User Card Number;EDC ID;Transaction Amt;Transmission Timestamp;Transaction Time;Transaction Date;Balance;Invoice Number;Request ISO Message;Responding with ISO code;Response ISO Message' . "\n");
        fclose($ftemp);
        $ftemp = fopen($csv_user, 'w');
        fwrite($ftemp, 'Merchant ID;User Card Number;EDC ID;Transaction Amt;Transmission Timestamp;Transaction Time;Transaction Date;Balance;Invoice Number;Request ISO Message;Responding with ISO code;Response ISO Message' . "\n");
        fclose($ftemp);
        $ftemp = fopen($csv_receive, 'w');
        fwrite($ftemp, 'Merchant ID;User Card Number;EDC ID;Transaction Amt;Transmission Timestamp;Transaction Time;Transaction Date;Balance;Invoice Number;Request ISO Message;Responding with ISO code;Response ISO Message' . "\n");
        fclose($ftemp);
        $ftemp = fopen($csv_else, 'w');
        fwrite($ftemp, 'Message;Responding with ISO code;Response ISO Message' . "\n");
        fclose($ftemp);
        
        // processing:
		while ($line = fgets($this->_file_handler)) {
            // finite state machine-like mechanism:
            switch ($current_state) {
                case self::STATE_WAIT_MESSAGE:
                    $n_match = preg_match($pattern_message, $line, $matches);
                    if ($n_match) {
                        $message_no = $matches[5];
                        $current_state = self::STATE_WAIT_HEADER;
                    }
                    break;
                case self::STATE_WAIT_HEADER:
                    $n_match = preg_match($pattern_header, $line, $matches);
                    if ($n_match) {
                        $current_state = self::STATE_WAIT_FOOTER;
                        $buffer .= $line;
                        // 5 kinds of file
                        if (preg_match($pattern_header_topup, $line)) {
                            $file_kind = 'topup';
                            $current_handler = fopen($file_topup, 'a');
                            $csv_handler = fopen($csv_topup, 'a');
                        } else if (preg_match($pattern_header_purchase, $line)) {
                            $file_kind = 'purchase';
                            $current_handler = fopen($file_purchase, 'a');
                            $csv_handler = fopen($csv_purchase, 'a');
                        } else if (preg_match($pattern_header_user, $line)) {
                            $file_kind = 'user';
                            $current_handler = fopen($file_user, 'a');
                            $csv_handler = fopen($csv_user, 'a');
                        } else if (preg_match($pattern_header_receive, $line)) {
                            $file_kind = 'receive';
                            $current_handler = fopen($file_receive, 'a');
                            $csv_handler = fopen($csv_receive, 'a');
                        } else {
                            $current_handler = fopen($file_else, 'a');
                            $file_kind = 'else';
                            $csv_handler = fopen($csv_else, 'a');
                            $n_match = preg_match($pattern_entry, $line, $matches);
                            $buffer_csv['Message'] = $message_no;
                            $buffer_csv[$matches[5]] = $matches[7];
                            $buffer .= "Message :$message_no\n";
                        }
                    }
                    break;
                case self::STATE_WAIT_FOOTER:
                    $n_match = preg_match($pattern_footer, $line, $matches);
                    if ($n_match) { // kalau sudah ketemu baris footer
                        $n++;
                        $buffer .= $line;
                        $buffer = "n: $n\n$buffer\n\n";
                        // output ke file
                        //echo $buffer;
                        fwrite($current_handler, $buffer);
                        $buffer = '';
                        fclose($current_handler);
                        
                        $n_match = preg_match($pattern_entry, $line, $matches);
                        $buffer_csv[$matches[5]] = $matches[7];
                        // output ke csv
                        // TODO
                        fwrite($csv_handler, $this->csv_line($buffer_csv));
                        $buffer_csv = array();
                        fclose($csv_handler);
                        
                        $current_state = self::STATE_WAIT_MESSAGE;
                    } else {    // baris di antara header dan footer
                        $n_match = preg_match($pattern_important, $line, $matches);
                        if ($n_match) { // termasuk baris penting
                            // buffer string
                            $buffer .= $line;
                            // buffer csv
                            // TODO
                            $n_match = preg_match($pattern_entry, $line, $matches);
                            $buffer_csv[$matches[5]] = $matches[7];
                        }
                    }
                    break;
            }
        }
	}
    
    /**
     * Mengembalikan string CSV untuk 1 entri
     * mengescape karakter kutip (")
     * @param   string  $entry  isi dari cell/entri
     * @return  string          escaped
     */
    public function csv_entry($entry) {
        return '"'.rtrim(str_replace('"','""',$entry)).'"';
    }
    
    /**
     * Mengembalikan string CSV untuk 1 baris
     * @param   array   $array      data untuk 1 baris CSV
     * @param   string  $separator  pemisah antar entri
     * @return  string              dalam format CSV
     */
    public function csv_line($array, $separator = ';') {
        foreach ($array as $key => $entry) {
            $array[$key] = $this->csv_entry($entry);
        }
        return implode($separator, $array) . "\n";
    }
}

// TEST DRIVE:
Log_Parser::factory('./alog.log')->parse();
<?php
/**
 * Class MultiPalindrom
 * 
 * solusi soal Multipalindrom
 */
 
class MultiPalindrom {
    protected $input;
    protected $xj;
    
    /**
     * Factory pattern
     */
    public function factory($input = null) {
        return new MultiPalindrom($input);
    }
    
    /**
     * Constructor
     * @param   string  $input  bisa dikasih di sini atau ntar
     */
    public function __construct($input = null) {
        $this->input = $input;
        $this->xj = array();
    }
    
    /**
     * Mengembalikan jumlah minimum palindrom
     * @param   string  $input  bisa juga dikasih di sini
     * @return  int
     */
    public function CountPalindrom($input = null) {
        if ($input) $this->input = $input;
        // return $this->JP(strlen($this->input)-1) // kalo mau langsung, dimodif juga di bawah
        echo "input:{$this->input}";
        for ($i=0; $i<(strlen($input)); $i++) {
            $this->xj[$i] = $this->JP($i);
            echo "i:$i;xj:";print_r($this->xj);
        }
        return $this->xj[strlen($this->input)-1];
    }
    
    /**
     * true jika palindrom, else false
     * @param   string
     * @return  bool
     */
    public function IsPalindrom($string) {
        for ($i=0, $j=strlen($string)-1; $i<$j; $i++, $j--) {
            if ($string[$i] != $string[$j]) return false;
        }
        return true;
    }
    
    /**
     * Mengembalikan jumlah palindrom minimal untuk substring [0,$n]\
     * @return  int
     */
    public function JP($n) {
        $xi = substr($this->input, 0, $n+1);
        echo "n:$n;xi:$xi\n";
        if ($this->IsPalindrom($xi)) {
            return 1;
        } else {
            $min = $n+1;
            for ($i=0; $i<=$n; $i++) {
                $sub = substr($this->input, $i, $n-$i+1);
                if ($this->IsPalindrom($sub)) {
                    $temp = $this->xj[$i-1] + 1;
                    // $temp = $this->JP($i-1) + 1;// kalo ga difill dulu, pake  ini
                    if ($temp < $min) $min = $temp;
                }
                echo "i:$i;sub:$sub;min:$min\n";
            }
            return $min;
        }
    }
}

// test:
echo MultiPalindrom::factory()->CountPalindrom(isset($argv[1])?$argv[1]:'minimisasi');
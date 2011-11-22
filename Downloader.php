<?php
/**
 * Class Downloader
 *
 * menerima input direktori dan nama file
 * lalu membacanya
 * setelah itu menyuruh browser untuk mendownloadnya
 *
 * @author  captain_kuro
 */
class Downloader {
    private $_dir = '';
    private $_filename = '';
    
    /**
     * Factory pattern
     */
    public static function factory($dir, $filename) {
        return new Downloader($dir, $filename);
    }
    
    /** 
     * Constructor
     */
    public function __construct($dir, $filename) {
        $this->_dir = $dir;
        $this->_filename = $filename;
    }
    
    /**
     * Getter _dir
     * @return  string
     */
    public function get_dir() {
        return $this->_dir;
    }
    
    /**
     * Getter _filename
     * @return  string
     */
    public function get_filename() {
        return $this->_filename;
    }
    
    /**
     * Setter _dir
     * @param   string
     * @return  Downloader
     */
    public function set_dir($dir) {
        $this->_dir = $dir;
        return $this;
    }
    
    /**
     * Setter _filename
     * @param   string
     * @return  Downloader
     */
    public function set_filename($filename) {
        $this->_filename = $filename;
        return $this;
    }
    
    /**
     * Melakukan proses utama
     * @param   string  $save_as    kalo ada isinya, jadi nama file ketika diterima user, default _filename
     */
    public function download($save_as = null) {
        if (!$save_as) $save_as = $this->_filename;
        $filepath = $this->_dir . $this->_filename;
        header("Content-Disposition: attachment; filename=" . urlencode($save_as));   
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Description: File Transfer");
        header("Content-Length: " . filesize($filepath));
        readfile($filepath);
    }
}

// test drive:
Downloader::factory('D:\\', 'coba.php')->download();
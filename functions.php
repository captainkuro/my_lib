<?php
/**
 * Memformat tanggal jadi indonesian
 * @param String $date in format Y-m-d H:i:s
 * @return String e.g. "Selasa, 23 Oktober 2010 | 10:55 WIB"
 */
function indonesian_date($date) {
	$hari = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis' , 'Jumat', 'Sabtu');
	$bulan = array(1=>'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
	$waktu = getdate(strtotime($date));
	$thari = $hari[$waktu['wday']];
	$tbulan = $bulan[$waktu['mon']];
	return $thari . ', ' . $waktu['mday'] . ' ' . $tbulan . ' ' . $waktu['year'] 
		. ' | ' . $waktu['hours'] . ':' . $waktu['minutes'] . ' WIB';
}

/**
 * Mengembalikan hasil hash $str dalam berbagai algoritma
 * @param String $str yang ingin dihash
 * @param Array $list daftar algoritma yg diinginkan, default semua algo
 * @return Array pasangan [hash_algo] => [hasil_hash]
 */
function hash_all($str, $list = false) {
	if (!$list) $list = hash_algos();
	$result = array();
	$n = count($list);
	for ($i=0; $i<$n; ++$i) {
		$result[$list[$i]] = hash($list[$i], $str);
	}
	return $result;
}

/**
 * Pelengkap fungsi terbilang()
 * @param Integer $x bilangan bulat positif
 * @return String teks terbilang dalam bahasa Indonesia untuk $x
 */
function kekata($x) {
    $x = abs($x);
    $angka = array("", "satu", "dua", "tiga", "empat", "lima",
    "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    if ($x < 12) {
        $temp = " ". $angka[$x];
    } else if ($x < 20) {
        $temp = kekata($x - 10). " belas";
    } else if ($x < 100) {
        $temp = kekata($x / 10)." puluh". kekata($x % 10);
    } else if ($x < 200) {
        $temp = " seratus" . kekata($x - 100);
    } else if ($x < 1000) {
        $temp = kekata($x / 100) . " ratus" . kekata($x % 100);
    } else if ($x < 2000) {
        $temp = " seribu" . kekata($x - 1000);
    } else if ($x < 1000000) {
        $temp = kekata($x / 1000) . " ribu" . kekata($x % 1000);
    } else if ($x < 1000000000) {
        $temp = kekata($x / 1000000) . " juta" . kekata($x % 1000000);
    } else if ($x < 1000000000000) {
        $temp = kekata($x / 1000000000) . " milyar" . kekata(fmod($x, 1000000000));
    } else if ($x < 1000000000000000) {
        $temp = kekata($x / 1000000000000) . " trilyun" . kekata(fmod($x, 1000000000000));
    }      
	return $temp;
}

/**
 * Mengembalikan teks terbilang untuk suatu angka dalam bahasa Indonesia
 * @param Integer $x bilangan bulat
 * @param Integer $style [1=UPPERCASE; 2=lowercase; 3=Upper Case Each Word; else=Upper case]
 * @return String teks terbilang untuk $x
 */
function terbilang($x, $style = 4) {
    if ($x < 0) {
        $hasil = "minus ". trim(kekata($x));
    } else {
        $hasil = trim(kekata($x));
    }
    switch ($style) {
        case 1: $hasil = strtoupper($hasil); break;
        case 2: $hasil = strtolower($hasil); break;
        case 3: $hasil = ucwords($hasil); break;
        default: $hasil = ucfirst($hasil);
    }
    return $hasil;
}

/**
 * @return String $string dilimit sampe $word_limit words
 */
function limit_words($string, $word_limit) {
    $words = explode(" ",$string);
    return implode(" ",array_splice($words,0,$word_limit));
}

/**
 * @param String $uri misal 'http://www.ancol.com/asdf lala/bubu[bibi]'
 * @return String $uri setelah diescape karakter2 unsafe
 */
function full_url_encode($uri) {
	$parsed = parse_url($uri);
	if ($parsed) {
		// process the [path]
		$pathplode = explode('/', $parsed['path']);
		$pathplode2 = array_map('urlencode', $pathplode);
		$parsed['path'] = implode('/', $pathplode2);
		if (function_exists('http_build_url')) {
			return http_build_url($parsed);
		} else {	// manual build
			$result = $parsed['scheme'] . '://' . $parsed['hosted'] . $parsed['path'];
			if ($parsed['query']) $result .= '?' . $parsed['query'];
			if ($parsed['fragment']) $result .= '#' . $parsed['fragment'];
			return $result;
		}
	} else {	// gagal parse_url
		return $uri;
	}
}

// Test drive
// echo full_url_encode('http://www.ancol.com/asdf lala/bubu[bibi]');

/**
 * Count interval from two datetime
 * @param string $interval can be: 
	yyyy - Number of full years 
	q - Number of full quarters 
	m - Number of full months 
	y - Difference between day numbers 
		(eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".) 
	d - Number of full days 
	w - Number of full weekdays 
	ww - Number of full weeks 
	h - Number of full hours 
	n - Number of full minutes 
	s - Number of full seconds (default) 
 */
function datediff($interval, $datefrom, $dateto, $using_timestamps = false) { 
	if (!$using_timestamps) { 
		$datefrom = strtotime($datefrom, 0); 
		$dateto = strtotime($dateto, 0); 
	} 
	$difference = $dateto - $datefrom; // Difference in seconds 
	switch($interval) { 
		case 'yyyy': // Number of full years 
			$years_difference = floor($difference / 31536000); 
			if (mktime(
					date("H", $datefrom), 
					date("i", $datefrom), 
					date("s", $datefrom), 
					date("n", $datefrom), 
					date("j", $datefrom), 
					date("Y", $datefrom)+$years_difference
				) > $dateto) 
			{ 
				$years_difference--; 
			} 
			if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) { 
				$years_difference++; 
			} 
			$datediff = $years_difference; 
			break; 
			
		case "q": // Number of full quarters 
			$quarters_difference = floor($difference / 8035200); 
			while (mktime(
					date("H", $datefrom), 
					date("i", $datefrom), 
					date("s", $datefrom), 
					date("n", $datefrom)+($quarters_difference*3), 
					date("j", $dateto), 
					date("Y", $datefrom)
				) < $dateto) 
			{ 
				$months_difference++; 
			} 
			$quarters_difference--; 
			$datediff = $quarters_difference; 
			break; 
			
		case "m": // Number of full months 
			$months_difference = floor($difference / 2678400); 
			while (mktime(
					date("H", $datefrom), 
					date("i", $datefrom), 
					date("s", $datefrom), 
					date("n", $datefrom)+($months_difference), 
					date("j", $dateto), 
					date("Y", $datefrom)
				) < $dateto) 
			{ 
				$months_difference++; 
			} 
			$months_difference--; 
			$datediff = $months_difference; 
			break; 
			
		case 'y': // Difference between day numbers 
			$datediff = date("z", $dateto) - date("z", $datefrom); 
			break; 
			
		case "d": // Number of full days 
			$datediff = floor($difference / 86400); 
			break; 
			
		case "w": // Number of full weekdays 
			$days_difference = floor($difference / 86400); 
			$weeks_difference = floor($days_difference / 7); // Complete weeks 
			$first_day = date("w", $datefrom); 
			$days_remainder = floor($days_difference % 7); 
			$odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder? 
			if ($odd_days > 7) { // Sunday 
				$days_remainder--; 
			} 
			if ($odd_days > 6) { // Saturday 
				$days_remainder--; 
			}
			$datediff = ($weeks_difference * 5) + $days_remainder; 
			break; 
			
		case "ww": // Number of full weeks 
			$datediff = floor($difference / 604800); 
			break; 
			
		case "h": // Number of full hours 
			$datediff = floor($difference / 3600); 
			break; 
			
		case "n": // Number of full minutes 
			$datediff = floor($difference / 60); 
			break; 
			
		default: // Number of full seconds (default) 
			$datediff = $difference; 
	} 
	return $datediff; 
} 

/**
 * Transform the rows from a database query (array) into a Tree [] => array('node' => ROW, 'children' => array())
 * @param array  $raw        the database query result, an associative array
 * @param string $id_key     the key pointing to the row's primary key
 * @param string $parent_key the key pointing to the row's parent's primary key
 */
function rows_to_tree($raw, $id_key = 'id', $parent_key = 'parent_id') {
	// First, transform $raw to $rows so that array key == id
	$rows = array();
	foreach ($raw as $row) {
		$rows[$row[$id_key]] = $row;
	}
	$tree = array();
	$tree_index = array(); // Storing the reference to each node
 
	while (count($rows)) {
		foreach ($rows as $id => $row) {
			if ($row[$parent_key]) { // If it has parent
				// Abnormal case: has parent id but no such id exists
				if (!array_key_exists($row[$parent_key], $rows) AND !array_key_exists($row[$parent_key], $tree_index)) {
					unset($rows[$id]);
				}
				// If the parent id exists in $tree_index, insert itself
				else if (array_key_exists($row[$parent_key], $tree_index)) {
					$parent = &$tree_index[$row[$parent_key]];
					$parent['children'][$id] = array('node' => $row, 'children' => array());
					$tree_index[$id] = &$parent['children'][$id];
					unset($rows[$id]);
				}
			} else { // Top parent
				$tree[$id] = array('node' => $row, 'children' => array());
				$tree_index[$id] = &$tree[$id];
				unset($rows[$id]);
			}
		}
	}
	return $tree;
}

/**
 * Return true if $array is an associative array
 */
function is_assoc($array) {
  return is_array($array) && (bool)count(array_filter(array_keys($array), 'is_string'));
}

/*
Javascript code reveal page current source code
javascript:h=document.getElementsByTagName('html')[0].innerHTML;function%20disp(h){h=h.replace(/</g,'\n&lt;');h=h.replace(/>/g,'&gt;');document.getElementsByTagName('body')[0].innerHTML='<pre>&lt;html&gt;'+h.replace(/(\n|\r)+/g,'\n')+'&lt;/html&gt;</pre>';}void(disp(h));
*/
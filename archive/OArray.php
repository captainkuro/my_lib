<?php
/**
 * Class OArray v 0.1a
 * object-oriented array
 *
 * Bayangkan kalo kita bisa melakukan ini:
 * OArray::factory(array(1,10,12))->sort()->array_flip()->implode(',');
 *
 * Note: beberapa method memerlukan class OString
 *
 * @author  captain_kuro
 */
 
class OArray implements ArrayAccess, IteratorAggregate, Countable {
    /**
     * Array asli sebenarnya
     * @type    array
     */
    protected $_array;
    
    /**
     * Factory pattern
     * @param   string $arr default array kosong
     * @return  OArray
     */
    public static function factory($arr = array()) {
		return new OArray($arr);
	}
	
    /**
     * Constructor
     * @param   string $arr default array kosong
     * @return  void
     */
	public function __construct($arr = array()) {
		$this->_array = $arr;
	}
    
    /**
     * Getter
     * @return  array
     */
    public function getArray() {
        return $this->_array;
    }
    
    /**
     * Setter
     * @param   array $array
     * @return  OArray
     */
    public function setArray($array) {
        $this->_array = $array;
        return $this;
    }
    
    /**
     * Mengembalikan object OArray baru yg identik dengan dirinya
     * @return  OArray
     */
    public function copy() {
        return new OArray($this->_array);
    }
    
    /*** INTERFACE ArrayAccess BEGIN ***/
    /**
     * This method is executed when using isset() or empty() on objects implementing ArrayAccess.
     * @param   mixed $offset key name 
     * @return  bool
     */
    public function offsetExists($offset) {
        return isset($this->_array[$offset]);
    }
    
    /** 
     * Returns the value at specified offset. 
     * This method is executed when checking if offset is empty().
     * @param   mixed $offset key name
     * @return  mixed
     */
    public function offsetGet($offset) {
        return $this->_array[$offset];
    }
    
    /**
     * Assigns a value to the specified offset.
     * @param   mixed $offset key name
     * @param   mixed $value value to be set
     * @return  void
     */
    public function offsetSet($offset, $value) {
        $this->_array[$offset] = $value;
    }
    
    /**
     * Unsets an offset.
     * @param   mixed $offset key name
     * @return  void
     */
    public function offsetUnset($offset) {
        unset($this->_array[$offset]);
    }
    /*** INTERFACE ArrayAccess END ***/
    
    /*** INTERFACE IteratorAggregate BEGIN ***/
    /**
     * Returns an external iterator
     * @return  Traversable
     */
    public function getIterator() {
        return new ArrayIterator($this->_array);
    }
    /*** INTERFACE IteratorAggregate END ***/
    
    /*** INTERFACE Countable BEGIN ***/
    /**
     * This method is executed when using the count() function on an object implementing Countable.
     * @return  int
     */
    public function count() {
        return count($this->_array);
    }
    /*** INTERFACE Countable END ***/
    
    /*** STANDARD ARRAY FUNCTION BEGIN ***/
    // semua fungsi array disulap menjadi method
    // semua method mengembalikan $this agar bisa di-method-chaining
    // komen2 nya hasil copas dari PHP Manual dengan modif sedikit (banget)
    
    /**
     * Makes all keys lowercased or uppercased. 
     * Numbered indices are left as is.
     * @param   int     $case   Either CASE_UPPER or CASE_LOWER (default)
     * @return  OArray
     */
    public function array_change_key_case($case = CASE_LOWER) {
        $this->_array = array_change_key_case($this->_array, $case);
        return $this;
    }
    
    /**
     * Chunks this array into size large chunks. 
     * The last chunk may contain less than size elements.
     * @param   int     $size           The size of each chunk 
     * @param   bool    $preserve_keys  When set to TRUE keys will be preserved. Default is FALSE which will reindex the chunk numerically
     * @return  OArray
     */
    public function array_chunk($size, $preserve_keys = false) {
        $this->_array = array_chunk($this->_array, $size, $preserve_keys);
        return $this;
    }
    
    /**
     * Creates an array by using the values from this array as keys 
     * and the values from the values array as the corresponding values.
     * @param   array   $values Array of values to be used 
     * @return  OArray
     */
    public function array_combine_as_keys($values) {
        $this->_array = array_combine($this->_array, $values);
        return $this;
    }
    
    /**
     * Creates an array by using the values from the keys array as keys 
     * and the values from this array as the corresponding values.
     * @param   array   $keys   Array of keys to be used. Illegal values for key will be converted to string.
     * @return  OArray
     */
    public function array_combine_as_values($keys) {
        $this->_array = array_combine($keys, $this->_array);
        return $this;
    }
    
    /**
     * Returns an array using the values of the input array as keys 
     * and their frequency in input as values. 
     * @return  OArray
     */
    public function array_count_values() {
        $this->_array = array_count_values($this->_array);
        return $this;
    }
    
    /**
     * Compares this array against array2 and returns the difference. 
     * Unlike array_diff() the array keys are used in the comparison. 
     * @param   array   $array2 An array to compare against
     * @return  OArray
     */
    public function array_diff_assoc($array2) {
        $this->_array = array_diff_assoc($this->_array, $array2);
        return $this;
    }
    
    /**
     * Compares the keys from this array against the keys from array2 and returns the difference. 
     * This function is like array_diff() except the comparison is done on the keys instead of the values.
     * @param   array   $array2 An array to compare against
     * @return  OArray
     */
    public function array_diff_key($array2) {
        $this->_array = array_diff_key($this->_array, $array2);
        return $this;
    }
    
    /**
     * Compares this array against array2 and returns the difference. 
     * Unlike array_diff() the array keys are used in the comparison. 
     * Unlike array_diff_assoc() a user supplied callback function is 
     * used for the indices comparison, not internal function.
     * @param   array       $array2             An array to compare against 
     * @param   callback    $key_compare_func   callback function to use. The callback function must return an integer less than, equal to, or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second. 
     * @return  OArray
     */
    public function array_diff_uassoc($array2, $key_compare_func) {
        $this->_array = array_diff_uassoc($this->_array, $array2, $key_compare_func);
        return $this;
    }
    
    /**
     * Compares the keys from this array against the keys from array2 and returns the difference. 
     * This function is like array_diff() except the comparison is done on the keys instead of the values.
     * Unlike array_diff_key() a user supplied callback function is used for the indices comparison, not internal function.
     * @param   array       $array2             An array to compare against 
     * @param   callback    $key_compare_func   callback function to use. The callback function must return an integer less than, equal to, or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second. 
     * @return  OArray
     */
    public function array_diff_ukey($array2, $key_compare_func) {
        $this->_array = array_diff_ukey($this->_array, $array2, $key_compare_func);
        return $this;
    }
    
    /**
     * Compares this array against array2 and returns the difference.
     * @param   array   $array2 An array to compare against 
     * @return  OArray
     */
    public function array_diff($array2) {
        $this->_array = array_diff($this->_array, $array2);
        return $this;
    }
    
    /**
     * Fills an array with the value of the value parameter, 
     * using the values of the keys array as keys.
     * @param   mixed   $value  Value to use for filling 
     * @return  OArray
     */
    public function array_fill_keys($value) {
        $this->_array = array_fill_keys($this->_array, $value);
        return $this;
    }
    
    /**
     * Fills an array with num entries of the value of the value parameter, 
     * keys starting at the start_index parameter. 
     * @param   int     $start_index    The first index of the returned array. Supports non-negative indexes only. 
     * @param   int     $sum            Number of elements to insert 
     * @param   mixed   $value          Value to use for filling 
     * @return  OArray
     */
    public function array_fill($start_index, $num, $value) {
        $this->_array = array_fill($start_index, $num, $value);
        return $this;
    }
    
    /**
     * Iterates over each value in the input array passing them to the callback function. 
     * If the callback function returns true, the current value from input is returned into the result array. 
     * Array keys are preserved.
     * @param   callback    $callback   The callback function to use 
     * @return  OArray
     */
    public function array_filter($callback) {
        $this->_array = array_filter($this->_array, $callback);
        return $this;
    }
    
    /**
     * array_flip() returns an array in flip order, i.e. keys from trans become values and values from trans become keys. 
     * Note that the values of trans need to be valid keys, i.e. they need to be either integer or string. 
     * A warning will be emitted if a value has the wrong type, and the key/value pair in question will not be flipped. 
     * If a value has several occurrences, the latest key will be used as its values, and all others will be lost.
     * @return  OArray
     */
    public function array_flip() {
        $this->_array = array_flip($this->_array);
        return $this;
    }
    
    /**
     * array_intersect_assoc() returns an array containing all the values of array1 that are present in all the arguments. 
     * Note that the keys are used in the comparison unlike in array_intersect().
     * @param   array   $array2 An array to compare values against. 
     * @return  OArray
     */
    public function array_intersect_assoc($array2) {
        $this->_array = array_intersect_assoc($this->_array, $array2);
        return $this;
    }
    
    /**
     * array_intersect_key() returns an array containing all the entries of array1 which have keys that are present in all the arguments.
     * @param   array   $array2 An array to compare keys against. 
     * @return  OArray
     */
    public function array_intersect_key($array2) {
        $this->_array = array_intersect_key($this->_array, $array2);
        return $this;
    }
    
    /**
     * array_intersect_uassoc() returns an array containing all the values of array1 that are present in all the arguments. 
     * Note that the keys are used in the comparison unlike in array_intersect(). 
     * @param   array       $array2             First array to compare keys against. 
     * @param   callback    $key_compare_func   User supplied callback function to do the comparison. 
     * @return  OArray
     */
    public function array_intersect_uassoc($array2, $key_compare_func) {
        $this->_array = array_intersect_uassoc($this->_array, $array2, $key_compare_func);
        return $this;
    }
    
    /**
     * array_intersect_ukey() returns an array containing all the values of array1 which have matching keys that are present in all the arguments.
     * @param   array       $array2             First array to compare keys against.
     * @param   callback    $key_compare_func   User supplied callback function to do the comparison.
     * @return  OArray
     * 
     */
    public function array_intersect_ukey($array2, $key_compare_func) {
        $this->_array = array_intersect_ukey($this->_array, $array2, $key_compare_func);
        return $this;
    }
    
    /**
     * array_intersect() returns an array containing all the values of array1 that are present in all the arguments. 
     * Note that keys are preserved.
     * @param   array   $array2 An array to compare values against.
     * @return  OArray
     */
    public function array_intersect($array2) {
        $this->_array = array_intersect($this->_array, $array2);
        return $this;
    }
    
    /**
     * array_key_exists() returns TRUE if the given key is set in the array. 
     * key can be any value possible for an array index.
     * @param   mixed   $key    Value to check.
     * @return  bool
     */
    public function array_key_exists($key) {
        return array_key_exists($key, $this->_array);
    }
    
    /**
     * array_keys() returns the keys, numeric and string, from the input array. 
     * If the optional search_value is specified, then only the keys for that value are returned. 
     * Otherwise, all the keys from the input are returned. 
     * @param   mixed   $search_value   If specified, then only keys containing these values are returned.
     * @param   bool    $strict         Determines if strict comparison (===) should be used during the search.
     * @return  OArray
     */
    public function array_keys($search_value = null, $strict = false) {
        $this->_array = array_keys($this->_array, $search_value, $strict);
        return $this;
    }
    
    /**
     * array_map() returns an array containing all the elements of arr1 after applying the callback function to each one. 
     * The number of parameters that the callback function accepts should match the number of arrays passed to the array_map().
     * @param   callback    $callback   Callback function to run for each element in each array.
     * @return  OArray
     */
    public function array_map($callback) {
        $this->_array = array_map($callback, $this->_array);
        return $this;
    }
    
    /**
     * array_merge_recursive() merges the elements of one or more arrays together so that the values of one are appended to the end of the previous one. 
     * It returns the resulting array. 
     * @param   array   $array2 Array to be recursively merged.
     * @return  OArray
     */
    public function array_merge_recursive($array2) {
        $this->_array = array_merge_recursive($this->_array, $array2);
        return $this;
    }
    
    /**
     * Merges the elements of one or more arrays together so that the values of one are appended to the end of the previous one. 
     * It returns the resulting array.
     * @param   array   $array2 Array to be merged.
     * @return  OArray
     */
    public function array_merge($array2) {
        $this->_array = array_merge($this->_array, $array2);
        return $this;
    }
    
    /**
     * array_multisort() can be used to sort several arrays at once, 
     * or a multi-dimensional array by one or more dimensions.
     * @param   mixed   $arg    sort options: SORT_ASC, SORT_DESC, SORT_REGULAR, SORT_NUMERIC, SORT_STRING
     * @return  OArray
     */
    public function array_multisort($arg = SORT_ASC) {
        array_multisort($this->_array, $arg);
        return $this;
    }
    
    /**
     * array_pad() returns a copy of the input padded to size specified by pad_size with value pad_value. 
     * If pad_size is positive then the array is padded on the right, if it's negative then on the left. 
     * If the absolute value of pad_size is less than or equal to the length of the input then no padding takes place. 
     * It is possible to add most 1048576 elements at a time.
     * @param   int     $pad_size   New size of the array.
     * @param   mixed   $pad_value  Value to pad if input is less than pad_size.
     * @return  OArray
     */
    public function array_pad($pad_size, $pad_value) {
        $this->_array = array_pad($this->_array, $pad_size, $pad_value);
        return $this;
    }
    
    /**
     * array_pop() pops and returns the last value of the array , shortening the array by one element. 
     * If array is empty (or is not an array), NULL will be returned. 
     * Will additionally produce a Warning when called on a non-array.
     * @return  mixed
     */
    public function array_pop() {
        return array_pop($this->_array);
    }
    
    /**
     * Calculate the product of values in an array.
     * array_product() returns the product of values in an array. 
     * @return  number
     */
    public function array_product() {
        return array_product($this->_array);
    }
    
    /**
     * array_push() treats array as a stack, and pushes the passed variables onto the end of array . 
     * The length of array increases by the number of variables pushed.
     * @param   mixed   $var    The pushed value.
     * @return  OArray
     */
    public function array_push($var) {
        array_push($this->_array, $var);
        return $this;
    }
    
    /**
     * Pick one or more random entries out of an array. The key(s) got returned.
     * array_rand() is rather useful when you want to pick one or more random entries out of an array.
     * @param   int $num_req    Specifies how many entries you want to pick.
     * @return  mixed
     */
    public function array_rand($num_req = 1) {
        return array_rand($this->_array, $num_req);
    }
    
    /**
     * array_reduce() applies iteratively the function function to the elements of the array input, 
     * so as to reduce the array to a single value.
     * @param   callback    $function   The callback function. 
     * @param   mixed       $initial    If the optional initial is available, it will be used at the beginning of the process, or as a final result in case the array is empty.
     * @return  mixed
     */
    public function array_reduce($function, $initial = null) {
        return array_reduce($this->_array, $function, $initial);
    }
    
    /**
     * Takes an input array and returns a new array with the order of the elements reversed.
     * @param   bool    $preserve_keys  If set to TRUE keys are preserved.
     * @return  OArray
     */
    public function array_reverse($preserve_keys = false) {
        $this->_array = array_reverse($this->_array, $preserve_keys);
        return $this;
    }
    
    /**
     * Searches the array for a given value and returns the corresponding key if successful
     * @param   mixed   $needle The searched value.
     * @param   bool    $strict If set to TRUE then the array_search() function will also check the types of the needle in the haystack.
     * @return  mixed
     */
    public function array_search($needle, $strict = null) {
        return array_search($needle, $this->_array, $strict);
    }
    
    /**
     * array_shift() shifts the first value of the array off and returns it, 
     * shortening the array by one element and moving everything down. 
     * All numerical array keys will be modified to start counting from zero while literal keys won't be touched.
     * @return  mixed
     */
    public function array_shift() {
        return array_shift($this->_array);
    }
    
    /**
     * array_slice() returns the sequence of elements from the array array as specified by the offset and length parameters.
     * @param   int     $offset         If offset is non-negative, the sequence will start at that offset in the array . If offset is negative, the sequence will start that far from the end of the array.
     * @param   int     $length         If length is given and is positive, then the sequence will have that many elements in it. If length is given and is negative then the sequence will stop that many elements from the end of the array. If it is omitted, then the sequence will have everything from offset up until the end of the array.
     * @param   bool    $preserve_keys  Note that array_slice() will reorder and reset the array indices by default. You can change this behaviour by setting preserve_keys to TRUE.
     * @return  OArray
     */
    public function array_slice($offset, $length = null, $preserve_keys = false) {
        $this->_array = array_slice($this->_array, $offset, $length, $preserve_keys);
        return $this;
    }
    /*** STANDARD ARRAY FUNCTION END ***/
    
    /*** MATH Function BEGIN ***/
    /**
     * Returns the highest value in that array
     * @return  int
     */
    public function max() {
        return max($this->_array);
    }
    
    /**
     * Returns the lowest value in that array
     * @return  int
     */
    public function min() {
        return min($this->_array);
    }
    /*** MATH Function END ***/
}

/* Driver, untuk mengetes class */
$a = new OArray(array(1,10,12,3));
echo "Tes pake foreach \n";
foreach ($a as $key => $value) {
    echo "Key:'$key'\nValue:'$value'\n";
}
echo "Tes pake for count \n";
for ($i=0; $i<count($a); $i++) {
    echo "a[$i] = {$a[$i]}\n";
}
echo "Tes array access\n";
echo "a[0] = {$a[0]}\n";
echo "a[1] = {$a[1]}\n";
echo "a[2] = {$a[2]}\n";
echo "a[3] = {$a[3]}\n";
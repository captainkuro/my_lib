<?php
/**
 * Simple Validator class
 */
class Value {
	public static function is($validator, $value, $opts = null) {
		switch ($validator) {
			case 'required':
				if (trim($value) == '') return false;
				break;
			case 'numeric':
				return is_numeric($value);
			case 'between':
				return ($opts[0] <= $value) && ($value <= $opts[1]);
			case 'integer':
				return is_numeric($value) && ((int)$value == (float)$value);
			case 'greater_than':
				return $value > $opts[0];
			case 'less_than':
				return $value < $opts[0];
			case 'min_length':
				return isset($value[$opts[0]-1]);
			case 'max_length':
				return !isset($value[$opts[0]]);
			case 'between_length':
				return isset($value[$opts[0]-1]) && !isset($value[$opts[1]]);
			default:
		}
		return true;
	}

}
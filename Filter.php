<?php
/**
 * Simple Filter class
 */
class Filter {
	public static function with($filter, $value, $opts = null) {
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				$value[$k] = Filter::with($filter, $v, $opts);
			}
			return $value;
		}
		switch ($filter) {
			case 'trim':
				$value = trim($value);
				break;
			case 'integer':
				$value = (int)$value;
				break;
			case 'boolean':
				$value = (bool)$value;
				break;
			default:
		}
		return $value;
	}
}
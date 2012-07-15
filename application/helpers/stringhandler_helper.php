<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function search_char($str, $start, $end, $char) {
	mb_internal_encoding('UTF-8');
	$count = 0;
	for ($i = $start; $i < $end; $i++) {
		if (mb_substr($str, $i, 1) == $char) {
			$count++;
		}
	}
	return $count;
}
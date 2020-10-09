<?php

function array_dot_reverse($data)
{
	$returns = [];
	if (!empty($data)) {
		foreach ($data as $key => $value) {
			array_set($returns, $key, $value);
		}
	}
	
	return $returns;
}

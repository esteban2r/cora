<?php 

function pre(){
	$values = func_get_args();
	foreach($values as $val){
		echo "<pre>";
		print_r($val);
		echo "</pre>";
	}
}
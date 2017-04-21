<?php 

	$d = dir(dirname(__file__));
	//print_r($d);
	while(false !== ($entry=$d->read())){
		echo $entry."<br />";
	}
	$d -> close();
?>
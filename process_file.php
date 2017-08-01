<?php
	if ( 0 < $_FILES['file']['error'] ) {
		echo 'Error: ' . $_FILES['file']['error'] . '<br>';
	} else {
		// move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);
		$w_file = $_FILES['file']['tmp_name'];
		$lines = file(urldecode($w_file));
		foreach ($lines as $line) {
			$line = str_replace(" ",",",$line);
			$new_str[] = str_getcsv($line, ",", '"');
		}
		echo "success|".implode(",",$new_str[0])."|".implode(",",$new_str[1])."|".implode("",$new_str[2]);
	}
?>

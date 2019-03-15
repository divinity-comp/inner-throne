<?php
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
?>

/* optimizeMember-only mode. Do NOT load theme functions, exclude all themes. */
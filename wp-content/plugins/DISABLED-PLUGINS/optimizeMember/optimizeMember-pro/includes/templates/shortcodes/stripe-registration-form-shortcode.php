<?php
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
?>

[optimizeMember-Pro-Stripe-Form register="1" level="%%level%%" ccaps="" desc="<?php echo _x ("Signup now, it's Free!", "s2member-front", "s2member"); ?>" custom="%%custom%%" tp="0" tt="D" captcha="clean" /]
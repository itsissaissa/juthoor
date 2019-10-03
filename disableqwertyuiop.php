<?php session_start();

unset($_SESSION["enableediting"]);

if (!isset($_SESSION["enableediting"]))
	header("Location: ./");
?>
<!DOCTYPE html>
<html>
<body>
</body>
</html>
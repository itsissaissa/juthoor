<?php session_start();

$_SESSION["enableediting"] = true;

if (isset($_SESSION["enableediting"]))
	header("Location: ./");
?>
<!DOCTYPE html>
<html>
<body>
</body>
</html>
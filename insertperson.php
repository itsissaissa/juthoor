<?php
	session_start(); 
	require 'include/settings.php';
	require 'include/connectmysql.php';

$name_ar 	= "'" . $conn->real_escape_string($_POST["name_ar"]) 	. "'";
$name_en 	= "'" . $conn->real_escape_string($_POST["name_en"]) 	. "'";
$father 	= "'" . $conn->real_escape_string($_POST["father"]) 	. "'";
$mother 	= "'" . $conn->real_escape_string($_POST["mother"]) 	. "'";
$family 	= "'" . $conn->real_escape_string($_POST["family"]) 	. "'";
$gender 	= "'" . $conn->real_escape_string($_POST["gender"]) 	. "'";
$alive 		= "'" . $conn->real_escape_string($_POST["alive"]) 		. "'";
if ($alive == "'on'")
	$alive = "'1'";
else $alive = "'0'";
$dob 		= "'" . $conn->real_escape_string($_POST["dob"]) 		. "'";
$dod 		= "'" . $conn->real_escape_string($_POST["dod"]) 		. "'";
$unknowndob = "'" . $conn->real_escape_string($_POST["unknowndob"]) 		. "'";
if ($unknowndob == "'on'")
	$unknowndob = "'1'";
else $unknowndob = "'0'";
$prefix_ar 	= "'" . $conn->real_escape_string($_POST["prefix_ar"]) 	. "'";
$prefix_en 	= "'" . $conn->real_escape_string($_POST["prefix_en"]) 	. "'";
$suffix_ar 	= "'" . $conn->real_escape_string($_POST["suffix_ar"]) 	. "'";
$suffix_en 	= "'" . $conn->real_escape_string($_POST["suffix_en"]) 	. "'";
$altname_ar = "'" . $conn->real_escape_string($_POST["altname_ar"])	. "'";
$altname_en = "'" . $conn->real_escape_string($_POST["altname_en"])	. "'";
$gentitle 	= "'" . $conn->real_escape_string($_POST["gentitle"])	. "'";
$spouse		= "'" . $conn->real_escape_string($_POST["spouse"])		. "'";
$notcurrent	= "'" . $conn->real_escape_string($_POST["notcurrent"])	. "'";
if ($notcurrent == "'on'")
	$current = "'0'";
else $current = "'1'";
if ($father == "''")		$father = "NULL";
if ($mother == "''")		$mother = "NULL";
if ($prefix_ar == "''")	$prefix_ar = "NULL";
if ($prefix_en == "''")	$prefix_en = "NULL";
if ($suffix_ar == "''")	$suffix_ar = "NULL";
if ($suffix_en == "''")	$suffix_en = "NULL";
if ($altname_ar == "''")	$altname_ar = "NULL";
if ($altname_en == "''")	$altname_en = "NULL";
if ($gentitle == "''")	$gentitle = "NULL";
if ($dob == "''")	$dob = "NULL";
if ($dod == "''")	$dod = "NULL";
if ($spouse == "'-1'")	$spouse = "NULL";

if ($family == "'-1'")
{
	$family_ar = "'" . $conn->real_escape_string($_POST["family_ar"]) 	. "'";
	$family_en = "'" . $conn->real_escape_string($_POST["family_en"]) 	. "'";
	
	$sql = "INSERT INTO `family` (name_ar,name_en) VALUES (" . 
	$family_ar	. "," .
	$family_en	. ")";
	$result = mysqli_query($conn, $sql);	
	
	$family = mysqli_insert_id($conn);
}

$sql = "INSERT INTO `person` (name_ar,name_en,father,mother,family,gender,alive,prefix_ar,prefix_en,suffix_ar,suffix_en,altname_ar,altname_en,gentitle,dob,unknowndob,dod) VALUES (" . 
$name_ar	. "," .
$name_en	. "," .
$father		. "," .
$mother		. "," .
$family		. "," .
$gender		. "," .
$alive		. "," .
$prefix_ar	. "," .
$prefix_en	. "," .
$suffix_ar	. "," .
$suffix_en	. "," .
$altname_ar	. "," .
$altname_en	. "," .
$gentitle	. "," .
$dob	. "," .
$unknowndob	. "," .
$dod	. ")";
$result = mysqli_query($conn, $sql);

if ($result)
{
	$id = mysqli_insert_id($conn);
	
	if ($spouse != "NULL")
	{
		if ($gender == "'male'")
		{
			$husband = $id;
			$wife = $spouse;
		}
		else if ($gender == "'female'")
		{
			$husband = $spouse;
			$wife = $id;
		}
		
		$sql = "INSERT INTO `marriage` (husband,wife,current) VALUES (" . 
		$husband	. "," .
		$wife	. "," .
		$current	. ")";
		$result = mysqli_query($conn, $sql);	
		$error = mysqli_error($conn);
	}
	$errorphrase = '';
	if(!empty($error))
		$errorphrase = "&error=" . $error;
	header("Location: person.php?p=" . $id . $errorphrase);
}
else
{
	$error = mysqli_error($conn);
	header("Location: newperson.php?error=" . $error);
}
?>
<?php
	session_start(); 
	require('include/settings.php');
	require('include/localization.php');
	require('include/connectmysql.php');
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	
	<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
	<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="include/bootstrap.css">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

	<?php
	if ($pagedirection == 'rtl')
		echo '<!-- Load Bootstrap RTL theme from RawGit -->
		<link rel="stylesheet" href="//cdn.rawgit.com/morteza/bootstrap-rtl/v3.3.4/dist/css/bootstrap-rtl.min.css">';
	?>

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

	<!-- Include jQuery ScrollTo -->
	<script src="jquery.scrollTo.min.js"></script>
	
	<link rel="stylesheet" media="screen" href="https://fontlibrary.org/face/droid-arabic-kufi" type="text/css"/>
	<link href='https://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'>
	
	<link rel="stylesheet" href="include/style.css">
	
	<link href="include/prettify.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="include/prettify.js"></script>
	
	<link rel="stylesheet" href="include/jquery.jOrgChart.css"/>
    <link rel="stylesheet" href="include/custom.css"/>
    
    <script src="include/jquery.jOrgChart.js"></script>
	
	<?php
	date_default_timezone_set($timezone);
	
	function getage($dob)
	{
		$years = date_diff(date_create($dob), date_create('today'))->y;
		if ($years < 1)
		{
			$years = date_diff(date_create($dob), date_create('today'))->m . ' ش';
		}
		return $years;
	}
	
	function getageupondeath($dob,$dod, $yearsoldstring, $afewmonthsoldstring)
	{
		$years = date_diff(date_create($dob), date_create($dod))->y;
		if ($years < 1)
		{
			$years = date_diff(date_create($dob), date_create($dod))->m;
			if ($years < 1)
			{
				$years = $afewmonthsoldstring;
			}
		}
		else $years = $years . ' ' . $yearsoldstring;
		return $years;
	}
	
	function toroman($integer, $upcase = true) 
	{ 
		$table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1); 
		$return = ''; 
		while($integer > 0) 
		{ 
			foreach($table as $rom=>$arb) 
			{ 
				if($integer >= $arb) 
				{ 
					$integer -= $arb; 
					$return .= $rom; 
					break; 
				} 
			} 
		} 

		return $return; 
	} 
	
	$id = $conn->real_escape_string($_GET["p"]);
	
	if (empty($id))
		header("Location: ./");
	
	$sql = "SELECT p.name_".$pagelanguage." as firstname, p.unknowndob as unknowndob, f.name_".$pagelanguage." as lastname, p.* FROM person p JOIN family f ON p.family = f.id WHERE p.id = '" . $id . "'";
	$result = mysqli_query($conn, $sql);
	
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		
		$firstname = $row["firstname"];
		$family = $row["family"];
		$lastname = $row["lastname"];
		$gender = $row["gender"];
		$alive = $row["alive"];
		$dob = $row["dob"];
		if ($row["unknowndob"] == 1)
			$dob = '';
		$dod = $row["dod"];
		$prefix_ar = $row["prefix_".$pagelanguage];
		$suffix_ar = $row["suffix_".$pagelanguage];
		$altname_ar = $row["altname_ar"];
		$altname_en = $row["altname_".$pagelanguage];
		$gentitle = $row["gentitle"];
		if (!empty($gentitle))
			$gentitle = toroman($gentitle);
		
		$agephrase = '';
		$bornpronoun = $gender == 'male'? $locstring_bornonmale:$locstring_bornonfemale;
		$agepronoun = $gender == 'male'? $locstring_hisage:$locstring_herage;
		$diedpronoun = '';
		$eulogy = '';
		$separator = '';
		if ($alive == '0' && !empty($dob) && !empty($dod))
		{
			$separator = ' '.$locstring_and;
			$age = getageupondeath($dob,$dod,$locstring_yearsold,$locstring_afewmonthsold);
			$agephrase = ' ('.$locstring_and . $agepronoun . $age . ')';
		}
		else if ($alive == '1' && !empty($dob))
		{
			$age = getage($dob);
			$agephrase = ' (' . $agepronoun . ' ' . $age . ')';
		}
		
		$dobphrase = '';
		if (!empty($dob))
		{
			if (date_parse($dob)["day"] == '1' && date_parse($dob)["month"] == '1')
				$dobphrase = $bornpronoun . ' ‏' . date_parse($dob)["year"];
			else $dobphrase = $bornpronoun . ' ‏' . $dob;
		}
		else $dob = '؟؟؟؟';
		
		$dobphrase = $dobphrase . $separator;
		
		$dodphrase = '';
		if (!empty($dod))
		{
			$diedpronoun = $gender == 'male'? $locstring_diedonmale:$locstring_diedonبثmale;
			if (date_parse($dod)["day"] == '1' && date_parse($dod)["month"] == '1')
				$dodphrase = $diedpronoun . ' ‏' . date_parse($dod)["year"];
			else $dodphrase = $diedpronoun . ' ‏' . $dod;
		}
		else $dod = '؟؟؟؟';
		
		if ($alive == '0')
			$eulogy = $gender == 'male'? $locstring_godblesshim:$locstring_godblessher;
		
	}
	else
	{
		header("Location: ./");
	}
	
	?>
	
	<script type="text/javascript">
		$(document).ready(function() {
			var hash = window.location.hash.substring(1);
			if (hash == 'tree')
			{
				$('#table').removeClass('active');
				$('#tree').addClass('active');
				$('#tableli').removeClass('active');
				$('#treeli').addClass('active');
			}
			/*if ($(".fatherlabel").length)
				$("#fatherlabel").removeClass("hidden");
			if ($(".motherlabel").length)
				$("#motherlabel").removeClass("hidden");*/
		});
	</script>
	
	<title><?php echo $firstname . ' ' . $lastname; if (!empty($gentitle)) echo ' ' .  $gentitle; ?></title>
	
</head>

<body onload="prettify();" dir='<?php echo $pagedirection;?>'>
	<div class="container">
	
	<br/><br/>
	
	<div class="row" id="header">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<h3><a href="./"><?php echo $locstring_mainpage; ?></a></h3><h1><?php echo '<span class="' . $gender . '">'; if($gender == 'male') echo '👨 '; else echo '👩 '; if (!empty($prefix_ar)) echo $prefix_ar . ' '; echo $firstname; if (!empty($altname_ar)) echo ' "' . $altname_ar . '"'; if (!empty($suffix_ar)) echo ' ' .  $suffix_ar; echo ' ' . $lastname;?> </span><small><?php if (!empty($gentitle)) echo '(' . $gentitle . ') '; echo $eulogy; ?></small></h1><h2><small><?php echo $dobphrase . $dodphrase . $agephrase ?></small></h2>

			<br/>
			<ol class="breadcrumb">
				<?php
				echo $firstname . ' ';
				
				$ancestors = [];
				$currentid = $id;
				
				do {
					$sql = "select id, name_".$pagelanguage.", father from person where id='" . $currentid . "'";
					$result = mysqli_query($conn, $sql);
					$numrows = mysqli_num_rows($result);
					
					if (mysqli_num_rows($result) > 0) {
						$row = mysqli_fetch_assoc($result);
					
						$currentid = $row["father"];
						array_push($ancestors, $currentid);
					}	
				} while ($numrows!=0);
				
				foreach ($ancestors as &$currentid)
				{	
					$sql = "select id, name_".$pagelanguage.", father from person where id='" . $currentid . "'";
					$result = mysqli_query($conn, $sql);
					$numrows = mysqli_num_rows($result);
					
					if (mysqli_num_rows($result) > 0) {
						$row = mysqli_fetch_assoc($result);
						
						$ancestorfirstname = $row["name_".$pagelanguage];
						$ancestorid = $row["id"];
						$ancestorurl = '"person.php?p=' . $ancestorid . '"#table>';
						$ancestorurlbefore = '<a href=';
						$ancestorurlafter = '</a> ';
				
						$activity = '';
						if ($ancestorid == $id)
						{
							$activity = ' class="active"';
							$ancestorurl = '';
							$ancestorurlbefore = '';
							$ancestorurlafter = ' ';
						}
						
						echo $ancestorurlbefore . $ancestorurl . $ancestorfirstname . $ancestorurlafter;
					}
				}
				
				echo $lastname;
				
				if($pagedirection == 'rtl')
					$float = 'left';
				else $float = 'right';
				
				
				if (isset($_SESSION["enableediting"]))
					echo '<span style="float:'.$float.'"><a href="editperson.php?p=' . $id . '"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> ' . $locstring_editperson . '</a></span>';
				?>
			</ol>
		</div>
	</div>
	
	<ul class="nav nav-pills" role="tablist">
    <li id="tableli" role="presentation" class="active"><a href="#table" aria-controls="table" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-th-large" aria-hidden="true"></span> <?php echo $locstring_viewastable; ?></a></li>
    <li id="treeli" role="presentation"><a href="#tree" aria-controls="tree" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-tree-deciduous" aria-hidden="true"></span> <?php echo $locstring_viewastree2; ?></a></li>
	</ul>
	<br/>
	</div>
		
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active container" id="table">
		<div class="row">
			<?php
			$sql = "SELECT father.unknowndob as fatherunknowndob, mother.unknowndob as motherunknowndob, fatherfather.unknowndob as fatherfatherunknowndob, fathermother.unknowndob as fathermotherunknowndob, motherfather.unknowndob as motherfatherunknowndob, mothermother.unknowndob as mothermotherunknowndob, father.id as fatherid, CONCAT_WS(' ',CONCAT_WS(' ',father.name_".$pagelanguage.",CONCAT('\"',father.altname_".$pagelanguage.",'\"')),father.suffix_".$pagelanguage.") as fathername_".$pagelanguage.", father.alive as fatheralive, father.dob as fatherdob, father.dod as fatherdod, fatherfamily.name_".$pagelanguage." as fatherfamily_".$pagelanguage.", mother.id as motherid, CONCAT_WS(' ',CONCAT_WS(' ',mother.name_".$pagelanguage.",CONCAT('\"',mother.altname_".$pagelanguage.",'\"')),mother.suffix_".$pagelanguage.") as mothername_".$pagelanguage.", mother.alive as motheralive, mother.dob as motherdob, mother.dod as motherdod, motherfamily.name_".$pagelanguage." as motherfamily_".$pagelanguage.", fatherfather.id as fatherfatherid, CONCAT_WS(' ',CONCAT_WS(' ',fatherfather.name_".$pagelanguage.",CONCAT('\"',fatherfather.altname_".$pagelanguage.",'\"')),fatherfather.suffix_".$pagelanguage.") as fatherfathername_".$pagelanguage.", fatherfather.alive as fatherfatheralive, fatherfather.dob as fatherfatherdob, fatherfather.dod as fatherfatherdod, fatherfatherfamily.name_".$pagelanguage." as fatherfatherfamily_".$pagelanguage.", fathermother.id as fathermotherid, CONCAT_WS(' ',CONCAT_WS(' ',fathermother.name_".$pagelanguage.",CONCAT('\"',fathermother.altname_".$pagelanguage.",'\"')),fathermother.suffix_".$pagelanguage.") as fathermothername_".$pagelanguage.", fathermother.alive as fathermotheralive, fathermother.dob as fathermotherdob, fathermother.dod as fathermotherdod, fathermotherfamily.name_".$pagelanguage." as fathermotherfamily_".$pagelanguage.", motherfather.id as motherfatherid, CONCAT_WS(' ',CONCAT_WS(' ',motherfather.name_".$pagelanguage.",CONCAT('\"',motherfather.altname_".$pagelanguage.",'\"')),motherfather.suffix_".$pagelanguage.") as motherfathername_".$pagelanguage.", motherfather.alive as motherfatheralive, motherfather.dob as motherfatherdob, motherfather.dod as motherfatherdod, motherfatherfamily.name_".$pagelanguage." as motherfatherfamily_".$pagelanguage.", mothermother.id as mothermotherid, CONCAT_WS(' ',CONCAT_WS(' ',mothermother.name_".$pagelanguage.",CONCAT('\"',mothermother.altname_".$pagelanguage.",'\"')),mothermother.suffix_".$pagelanguage.") as mothermothername_".$pagelanguage.", mothermother.alive as mothermotheralive, mothermother.dob as mothermotherdob, mothermother.dod as mothermotherdod, mothermotherfamily.name_".$pagelanguage." as mothermotherfamily_".$pagelanguage." FROM `person` p LEFT JOIN `person` father ON p.father = father.id LEFT JOIN `person` mother ON p.mother = mother.id LEFT JOIN `person` fatherfather ON father.father = fatherfather.id LEFT JOIN `person` fathermother ON father.mother = fathermother.id LEFT JOIN `person` motherfather ON mother.father = motherfather.id LEFT JOIN `person` mothermother ON mother.mother = mothermother.id LEFT JOIN `family` fatherfamily ON father.family = fatherfamily.id LEFT JOIN `family` motherfamily ON mother.family = motherfamily.id LEFT JOIN `family` fatherfatherfamily ON fatherfather.family = fatherfatherfamily.id LEFT JOIN `family` fathermotherfamily ON fathermother.family = fathermotherfamily.id LEFT JOIN `family` motherfatherfamily ON motherfather.family = motherfatherfamily.id LEFT JOIN `family` mothermotherfamily ON mothermother.family = mothermotherfamily.id WHERE p.id = '" . $id . "'";
			$result = mysqli_query($conn, $sql);
		
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_assoc($result);
				
				if ($row["fatherunknowndob"] == 1)
					$row["fatherdob"] = '';
				if ($row["motherunknowndob"] == 1)
					$row["motherdob"] = '';
				
				if ($row["fatherfatherunknowndob"] == 1)
					$row["fatherfatherdob"] = '';
				if ($row["fathermotherunknowndob"] == 1)
					$row["fathermotherdob"] = '';
				
				if ($row["motherfatherunknowndob"] == 1)
					$row["motherfatherdob"] = '';
				if ($row["mothermotherunknowndob"] == 1)
					$row["mothermotherdob"] = '';
			
				$fatherfirstname = empty($row["fathername_".$pagelanguage])? '<small>👨</small></span>':$row["fathername_".$pagelanguage];
				$fatherlastname = $row["fatherfamily_".$pagelanguage];
				$fatherid = $row["fatherid"];
				$fatherurl = empty($fatherid)? '':'person.php?p=' . $fatherid . '#table';
				if ($row["fatheralive"] == '0')
					$fatherage = (empty($row["fatherdob"])? '؟؟؟؟':date_parse($row["fatherdob"])["year"]) . '-‏' . (empty($row["fatherdod"])? '؟؟؟؟':date_parse($row["fatherdod"])["year"]);
				else if(!empty($row["fatherdob"]))$fatherage = getAge($row["fatherdob"]);
				else $fatherage = '&nbsp;';
				$motherfirstname = empty($row["mothername_".$pagelanguage])? '<small>👩</small></span>':$row["mothername_".$pagelanguage];
				$motherlastname = $row["motherfamily_".$pagelanguage];
				$motherid = $row["motherid"];
				$motherurl = empty($motherid)? '':'person.php?p=' . $motherid . '#table';
				if ($row["motheralive"] == '0')
					$motherage = (empty($row["motherdob"])? '؟؟؟؟':date_parse($row["motherdob"])["year"]) . '-‏' . (empty($row["motherdod"])? '؟؟؟؟':date_parse($row["motherdod"])["year"]);
				else if(!empty($row["motherdob"])) $motherage = getAge($row["motherdob"]);
				else $motherage = '&nbsp;';
				
				$fatherfatherfirstname = empty($row["fatherfathername_".$pagelanguage])? '<small>👨</small></span>':$row["fatherfathername_".$pagelanguage];
				$fatherfatherlastname = $row["fatherfatherfamily_".$pagelanguage];
				$fatherfatherid = $row["fatherfatherid"];
				$fatherfatherurl = empty($fatherfatherid)? '':'person.php?p=' . $fatherfatherid . '#table';
				if ($row["fatherfatheralive"] == '0')
					$fatherfatherage = (empty($row["fatherfatherdob"])? '؟؟؟؟':date_parse($row["fatherfatherdob"])["year"]) . '-‏' . (empty($row["fatherfatherdod"])? '؟؟؟؟':date_parse($row["fatherfatherdod"])["year"]);
				else if(!empty($row["fatherfatherdob"]))$fatherfatherage = getAge($row["fatherfatherdob"]);
				else $fatherfatherage = '&nbsp;';
				$fathermotherfirstname = empty($row["fathermothername_".$pagelanguage])? '<small>👩</small></span>':$row["fathermothername_".$pagelanguage];
				$fathermotherlastname = $row["fathermotherfamily_".$pagelanguage];
				$fathermotherid = $row["fathermotherid"];
				$fathermotherurl = empty($fathermotherid)? '':'person.php?p=' . $fathermotherid . '#table';
				if ($row["fathermotheralive"] == '0')
					$fathermotherage = (empty($row["fathermotherdob"])? '؟؟؟؟':date_parse($row["fathermotherdob"])["year"]) . '-‏' . (empty($row["fathermotherdod"])? '؟؟؟؟':date_parse($row["fathermotherdod"])["year"]);
				else if(!empty($row["fathermotherdob"]))$fathermotherage = getAge($row["fathermotherdob"]);
				else $fathermotherage = '&nbsp;';
				
				$motherfatherfirstname = empty($row["motherfathername_".$pagelanguage])? '<small>👨</small></span>':$row["motherfathername_".$pagelanguage];
				$motherfatherlastname = $row["motherfatherfamily_".$pagelanguage];
				$motherfatherid = $row["motherfatherid"];
				$motherfatherurl = empty($motherfatherid)? '':'person.php?p=' . $motherfatherid . '#table';
				if ($row["motherfatheralive"] == '0')
					$motherfatherage = (empty($row["motherfatherdob"])? '؟؟؟؟':date_parse($row["motherfatherdob"])["year"]) . '-‏' . (empty($row["motherfatherdod"])? '؟؟؟؟':date_parse($row["motherfatherdod"])["year"]);
				else if(!empty($row["motherfatherdob"]))$motherfatherage = getAge($row["motherfatherdob"]);
				else $motherfatherage = '&nbsp;';
				$mothermotherfirstname = empty($row["mothermothername_".$pagelanguage])? '<small>👩</small></span>':$row["mothermothername_".$pagelanguage];
				$mothermotherlastname = $row["mothermotherfamily_".$pagelanguage];
				$mothermotherid = $row["mothermotherid"];
				$mothermotherurl = empty($mothermotherid)? '':'person.php?p=' . $mothermotherid . '#table';
				if ($row["mothermotheralive"] == '0')
					$mothermotherage = (empty($row["mothermotherdob"])? '؟؟؟؟':date_parse($row["mothermotherdob"])["year"]) . '-‏' . (empty($row["mothermotherdod"])? '؟؟؟؟':date_parse($row["mothermotherdod"])["year"]);
				else if(!empty($row["mothermotherdob"]))$mothermotherage = getAge($row["mothermotherdob"]);
				else $mothermotherage = '&nbsp;';
				
			}
			?>
		
			<div class="col-md-12 col-sm-12 col-xs-12 text-center padded">
				<ul class="col-md-12 col-sm-12 col-xs-12 nav nav-justified">
					<ul class="col-md-3 col-sm-6 col-xs-6 nav fatherfather">
						<li role="presentation" class="h4 male"><a href="<?php echo $fatherfatherurl; ?>"><?php echo $fatherfatherfirstname; echo ' ' . $fatherfatherlastname; ?><br/><small><?php echo $fatherfatherage; ?></small></a></li>
					</ul>
					<ul class="col-md-push-3 col-md-3 col-sm-6 col-xs-6 nav motherfather">
						<li role="presentation" class="h4 male"><a href="<?php echo $motherfatherurl; ?>"><?php echo $motherfatherfirstname; echo ' ' . $motherfatherlastname; ?><br/><small><?php echo $motherfatherage; ?></small></a></li>
					</ul>
					<ul class="col-md-pull-3 col-md-3 col-sm-6 col-xs-6 nav fathermother">
						<li role="presentation" class="h4 female"><a href="<?php echo $fathermotherurl; ?>"><?php echo $fathermotherfirstname; echo ' ' . $fathermotherlastname; ?><br/><small><?php echo $fathermotherage; ?></small></a></li>
					</ul>
					<ul class="col-md-3 col-sm-6 col-xs-6 nav mothermother">
						<li role="presentation" class="h4 female"><a href="<?php echo $mothermotherurl; ?>"><?php echo $mothermotherfirstname; echo ' ' . $mothermotherlastname; ?> <br/><small><?php echo $mothermotherage; ?></small></a></li>
					</ul>
				</ul>
				<ul class="col-md-12 col-sm-12 col-xs-12 nav nav-justified">
					<ul class="col-md-6 col-sm-6 col-xs-6 nav father">
					<li role="presentation" class="h3 male"><a href="<?php echo $fatherurl; ?>"><?php echo $fatherfirstname; echo ' ' . $fatherlastname; ?> <small><span id="fatherlabel" class="label fatherfather hidden">أ</span></small> <small><?php echo $fatherage; ?></small></a></li>
					</ul>
					<ul class="col-md-6 col-sm-6 col-xs-6 nav mother">
						<li role="presentation" class="h3 female"><a href="<?php echo $motherurl; ?>"><?php echo $motherfirstname; echo ' ' . $motherlastname; ?> <small><span id="motherlabel" class="label mothermother hidden">ب</span></small> <small><?php echo $motherage; ?></small></a></li>
					</ul>
				</ul>
			
			<ul class="nav" style="border:1px solid #ddd;padding-bottom:50px;">
				<?php
				$sql = "SELECT p.*, f.name_".$pagelanguage." as lastname_".$pagelanguage.", CONCAT_WS(' ',CONCAT_WS(' ',p.name_".$pagelanguage.", CONCAT('\"',p.altname_".$pagelanguage.",'\"')),p.suffix_".$pagelanguage.") as name_".$pagelanguage.", p.unknowndob as unknowndob, s.id as spouseid,
				GROUP_CONCAT(CONCAT_WS(' ',s.name_".$pagelanguage.",CONCAT('\"',s.altname_".$pagelanguage.",'\"'),s.suffix_".$pagelanguage.",sf.name_".$pagelanguage.") ORDER BY s.dob SEPARATOR '<br/><span class=\'glyphicon glyphicon-heart\' aria-hidden=\'true\'></span> ') as spousename_".$pagelanguage." FROM `person` p LEFT JOIN `family` f ON p.family = f.id LEFT JOIN `marriage` m ON p.gender = 'male' AND m.husband = p.id AND m.current = '1' OR p.gender = 'female' AND m.wife = p.id AND m.current = '1' LEFT JOIN `person` s ON p.gender = 'male' AND m.wife = s.id OR p.gender = 'female' AND m.husband = s.id LEFT JOIN `family` sf ON s.family = sf.id WHERE (p.father = (SELECT father as thisfather FROM `person` WHERE id = '" . $id . "') AND p.father IS NOT NULL) OR (p.mother = (SELECT mother as thismother FROM `person` WHERE id = '" . $id . "') AND p.mother IS NOT NULL) OR p.id = '" . $id . "' GROUP BY p.id ORDER BY -p.dob DESC, p.id ASC";
				$result = mysqli_query($conn, $sql);
			
				if (mysqli_num_rows($result) > 0) {
					while ($row = mysqli_fetch_assoc($result)) {
						
						$siblingfirstname = $row["name_".$pagelanguage];
						$siblinglastname = $row["lastname_".$pagelanguage];
						$siblingfamily = $row["family"];
						$siblingfather = $row["father"];
						$siblingmother = $row["mother"];
						
						$siblingid = $row["id"];
						$siblingurl = 'person.php?p=' . $siblingid . '#table';
						$siblinggender = $row["gender"];
						
						$siblingname = $siblingfirstname;				
						if($siblinglastname != $fatherlastname)
						{
							$siblingname = $siblingfirstname . ' ' . $siblinglastname;
						}
						
						if ($siblingfather != $fatherid || $siblingfamily != $family)
						{
							$siblingname = $siblingname . ' <small class="motherlabel"><span class="label mothermother">' . $locstring_maternal . '</span></small>';
						}
						else if ($siblingmother != $motherid)
						{
							$siblingname = $siblingname . ' <small class="fatherlabel"><span class="label fatherfather" >' . $locstring_paternal . '</span></small>';
						}
						
						$siblingspousefirstname = $row["spousename_".$pagelanguage];
						if(!empty($siblingspousefirstname))
						{
							$siblingspousefirstname = '<span class="glyphicon glyphicon-heart" aria-hidden="true"></span> ' . $siblingspousefirstname . '<br/>';
						}
						else $siblingspousefirstname = '<br/>';
						
						if ($row["unknowndob"] == 1)
							$row["dob"] = '';
						
						if ($siblingid == $id)
							$siblinggender = 'selected';
						
						if ($row["alive"] == '0')
							$siblingage = (empty($row["dob"])? '؟؟؟؟':date_parse($row["dob"])["year"]) . '-‏' . (empty($row["dod"])? '؟؟؟؟':date_parse($row["dod"])["year"]);
						else if(!empty($row["dob"])) $siblingage = getAge($row["dob"]);
						else $siblingage = '&nbsp;';
						
						echo '<li style="height:100px" role="presentation" class="col-md-2 col-sm-2 col-xs-6 h3 ' . $siblinggender . '"><a href="' . $siblingurl . '">' . $siblingname . '<br/><small>' . $siblingage . '</small><br/><small>' . $siblingspousefirstname . '</small></a></li>';
					}
				}
				if (isset($_SESSION["enableediting"]))
				{
					$getfather = '';
					if (!empty($fatherid))
						$getfather = 'father=' . $fatherid;
					$getmother = '';
					if (!empty($motherid))
						$getmother = 'mother=' . $motherid;
					
					if (!empty($getfather) || !empty($getmother))
						echo '<li role="presentation" class="col-md-2 col-sm-2 col-xs-6 h3"><a href="newperson.php?' . $getfather . '&' . $getmother . '"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> ' . $locstring_newsibling . '</a></li>';
				}
				?>
			</ul>
		</div>
		
		<div class="col-md-12 col-sm-12 col-xs-12 text-center padded">		
		<?php
		$addedaddchild = false;
		
		$sql = "SELECT s.*, CONCAT_WS(' ',CONCAT_WS(' ',s.name_".$pagelanguage.",CONCAT('\"',s.altname_".$pagelanguage.",'\"')),s.suffix_".$pagelanguage.") as name_".$pagelanguage.", s.unknowndob as unknowndob, f.name_".$pagelanguage." as family_".$pagelanguage.", m.current as current FROM `person` s INNER JOIN marriage m  ON (m.wife = s.id AND m.husband = '" . $id . "') OR (m.husband = s.id AND m.wife = '" . $id . "') LEFT JOIN `family` f ON s.family = f.id ORDER BY s.dob, m.current DESC";
		$result2 = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result2) > 0) {
			while ($row2 = mysqli_fetch_assoc($result2)) {
				
				$spousefirstname = $row2["name_".$pagelanguage];
				$spouselastname = $row2["family_".$pagelanguage];
				$spouseid = $row2["id"];
				$spouseurl = 'person.php?p=' . $spouseid . '#table';
				$spousegender = $row2["gender"];
				
				$spousealive = $row2["alive"];
				$spousecurrent = $row2["current"];
				
				if ($row2["unknowndob"] == 1)
					$row2["dob"] = '';
				
				if ($spousealive == 0)
					$spouseage = (empty($row2["dob"])? '؟؟؟؟':date_parse($row2["dob"])["year"]) . '-‏' . (empty($row2["dod"])? '؟؟؟؟':date_parse($row2["dod"])["year"]);
				else if(!empty($row2["dob"])) $spouseage = getAge($row2["dob"]);
				else $spouseage = '&nbsp;';
				
				if ($spousecurrent == 0)
					$relation = $spousegender == 'male'? $locstring_herex:$locstring_hisex;
				else $relation = $spousegender == 'male'? $locstring_herhusband:$locstring_hiswife;
					
				$parent = $spousegender == 'male'? 'father':'mother';
				
					echo '<ul class="nav nav-justified">
				<li role="presentation" class="h3 ' . $spousegender . ' ' . $parent . '"><a href="' . $spouseurl . '"><small>' . $relation . ' </small>'. $spousefirstname . ' ' . $spouselastname . ' <small>' . $spouseage . '</small></a></li>
				</ul>';
				
				$sql = "SELECT p.*, CONCAT_WS(' ',CONCAT_WS(' ',p.name_".$pagelanguage.", CONCAT('\"',p.altname_".$pagelanguage.",'\"')),p.suffix_".$pagelanguage.") as name_".$pagelanguage.", f.name_".$pagelanguage." as lastname_".$pagelanguage.", p.unknowndob as unknowndob, s.id as spouseid, GROUP_CONCAT(CONCAT_WS(' ',s.name_".$pagelanguage.",CONCAT('\"',s.altname_".$pagelanguage.",'\"'),s.suffix_".$pagelanguage.",sf.name_".$pagelanguage.") ORDER BY s.dob SEPARATOR '<br/><span class=\'glyphicon glyphicon-heart\' aria-hidden=\'true\'></span> ') as spousename_".$pagelanguage.", sf.name_".$pagelanguage." as spousefamily_".$pagelanguage." FROM `person` p LEFT JOIN `family` f ON p.family = f.id LEFT JOIN `marriage` m ON p.gender = 'male' AND m.husband = p.id AND m.current = '1' OR p.gender = 'female' AND m.wife = p.id AND m.current = '1' LEFT JOIN `person` s ON p.gender = 'male' AND m.wife = s.id OR p.gender = 'female' AND m.husband = s.id LEFT JOIN `family` sf ON s.family = sf.id WHERE (p.father = '" . $id . "' AND p.mother = '" . $spouseid . "') OR (p.father = '" . $spouseid . "' AND p.mother = '" . $id . "') GROUP BY p.id ORDER BY -p.dob DESC, p.id ASC";
				$result = mysqli_query($conn, $sql);
			
				if (mysqli_num_rows($result) > 0) {
					echo '<ul class="nav" style="border:1px solid #ddd;padding-bottom:50px;">';
					while ($row = mysqli_fetch_assoc($result)) {
						
						$childfirstname = $row["name_".$pagelanguage];
						$childlastname = $row["lastname_".$pagelanguage];
						$childid = $row["id"];
						$childurl = 'person.php?p=' . $childid . '#table';
						$childgender = $row["gender"];
				
						$childname = $childfirstname;				
						if($lastname != $childlastname && $spouselastname != $childlastname)
						{
							$childname = $childfirstname . ' ' . $childlastname;
						}
						
						$childspousefirstname = $row["spousename_".$pagelanguage];
						if(!empty($childspousefirstname))
							$childspousefirstname = '<span class="glyphicon glyphicon-heart" aria-hidden="true"></span> ' . $childspousefirstname;
						
						if ($row["unknowndob"] == 1)
							$row["dob"] = '';
						
						if ($row["alive"] == '0')
							$childage = (empty($row["dob"])? '؟؟؟؟':date_parse($row["dob"])["year"]) . '-‏' . (empty($row["dod"])? '؟؟؟؟':date_parse($row["dod"])["year"]);
						else if(!empty($row["dob"])) $childage = getAge($row["dob"]);
						else $childage = '&nbsp;';
						
						echo '<li style="height:100px" role="presentation" class="col-md-2 col-sm-2 col-xs-6 h3 ' . $childgender . '"><a href="' . $childurl . '">' . $childname . '<br/><small>' . $childage . '</small><br/><small>' . $childspousefirstname . '<br/>' . '</small></a></li>';
					}
					if (isset($_SESSION["enableediting"]))
					{
						$addedaddchild = true;
						$getfather = '';
						$getmother = '';
						if ($gender=='male')
						{
							$getfather = 'father=' . $id;
							$getmother = 'mother=' . $spouseid;
						}
						else if ($gender=='female')
						{
							$getfather = 'father=' . $spouseid;
							$getmother = 'mother=' . $id;
						}
						
						if (!empty($getfather) || !empty($getmother))
							echo '<li role="presentation" class="col-md-2 col-sm-2 col-xs-6 h3"><a href="newperson.php?' . $getfather . '&' . $getmother . '"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> ' . $locstring_newchild . '</a></li>';
					}
					echo '</ul>';
				}
				else
				{
					if (isset($_SESSION["enableediting"]))
					{
						$addedaddchild = true;
						$getfather = '';
						$getmother = '';
						if ($gender=='male')
						{
							$getfather = 'father=' . $id;
							$getmother = 'mother=' . $spouseid;
						}
						else if ($gender=='female')
						{
							$getfather = 'father=' . $spouseid;
							$getmother = 'mother=' . $id;
						}
						
						if (!empty($getfather) || !empty($getmother))
						{
							echo '<ul class="nav" style="border:1px solid #ddd;padding-bottom:50px;">';
							echo '<li role="presentation" class="col-md-2 col-sm-2 col-xs-6 h3"><a href="newperson.php?' . $getfather . '&' . $getmother . '"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> ابن جديد</a></li>';
							echo '</ul>';
						}
					}
				}
				echo '<br/>';
			}
		}
		if (isset($_SESSION["enableediting"]))
		{
			$getspouse = '';
			if ($gender == 'male')
			{
				$getspouse = 'husband=' . $id . '&gender=female';
				$newspousephrase = $locstring_newwife;
			}
			else if ($gender == 'female')
			{
				$getspouse = 'wife=' . $id . '&gender=male';
				$newspousephrase = $locstring_newhusband;
			}
			
			if (!empty($getspouse))
			{
				echo '<ul class="nav">';
				echo '<li style="height:100px" role="presentation" class="col-md-2 col-sm-2 col-xs-6 h3"><a href="newperson.php?' . $getspouse . '"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> ' . $newspousephrase . '</a></li>';
				echo '</ul>';
			}
		}
		
		$sql = "SELECT p.*, CONCAT_WS(' ',CONCAT_WS(' ',p.name_".$pagelanguage.",CONCAT('\"',p.altname_".$pagelanguage.",'\"')),p.suffix_".$pagelanguage.") as name_".$pagelanguage.", pf.name_".$pagelanguage." as lastname_".$pagelanguage.", p.unknowndob as unknowndob, s.id as spouseid, GROUP_CONCAT(CONCAT_WS(' ',s.name_".$pagelanguage.",CONCAT('\"',s.altname_".$pagelanguage.",'\"'),s.suffix_".$pagelanguage.",sf.name_".$pagelanguage.") ORDER BY s.dob SEPARATOR '<br/><span class=\'glyphicon glyphicon-heart\' aria-hidden=\'true\'></span> ') as spousename_".$pagelanguage.", sf.name_".$pagelanguage." as spousefamily_".$pagelanguage." FROM `person` p LEFT JOIN `marriage` m ON p.gender = 'male' AND m.husband = p.id AND m.current = '1' OR p.gender = 'female' AND m.wife = p.id AND m.current = '1' LEFT JOIN `person` s ON p.gender = 'male' AND m.wife = s.id OR p.gender = 'female' AND m.husband = s.id INNER JOIN family pf ON p.family = pf.id LEFT JOIN `family` sf ON s.family = sf.id WHERE (p.father = '" . $id . "' AND p.mother IS NULL) OR (p.father IS NULL AND p.mother = '" . $id . "') GROUP BY p.id ORDER BY -p.dob DESC";
		$result = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result) > 0) {
			$relation = $gender == 'female'? $locstring_herchildren.'<br/></small>':$locstring_hischildren.'<br/></small>';
				
			$parent = 'neutral';
			
				echo '<ul class="nav nav-justified">
			<li role="presentation" class="h3 ' . $parent . '"><a href=""><small>' . $relation . ' </small></a></li>
			</ul>';

			echo '<ul class="nav" style="border:1px solid #ddd;padding-bottom:50px;">';
		
			while ($row = mysqli_fetch_assoc($result)) {
				
				$childfirstname = $row["name_".$pagelanguage.""];
				$childlastname = $row["lastname_".$pagelanguage.""];
				$childid = $row["id"];
				$childurl = 'person.php?p=' . $childid . '#table';
				$childgender = $row["gender"];
				
				$childspousefirstname = $row["spousename_".$pagelanguage.""];
				if(!empty($childspousefirstname))
					$childspousefirstname = '<span class="glyphicon glyphicon-heart" aria-hidden="true"></span> ' . $childspousefirstname;
				
				$childname = $childfirstname;
				if($lastname != $childlastname)
				{
					$childname = $childfirstname . ' ' . $childlastname;
				}
				
				if ($row["unknowndob"] == 1)
					$row["dob"] = '';
				
				if ($row["alive"] == '0')
					$childage = (empty($row["dob"])? '؟؟؟؟':date_parse($row["dob"])["year"]) . '-‏' . (empty($row["dod"])? '؟؟؟؟':date_parse($row["dod"])["year"]);
				else if(!empty($row["dob"])) $childage = getAge($row["dob"]);
				else $childage = '&nbsp;';

				
				echo '<li role="presentation" class="col-md-2 col-sm-2 col-xs-6 h3 ' . $childgender . '"><a href="' . $childurl . '">' . $childname . '<br/><small>' . $childage . '</small><br/><small>' . $childspousefirstname . '<br/>' . '</small></a></li>';
			}
			if (isset($_SESSION["enableediting"]))
			{
				$addedaddchild = true;
				$getfather = '';
				$getmother = '';
				if ($gender=='male')
				{
					$getfather = 'father=' . $id;
				}
				else if ($gender=='female')
				{
					$getmother = 'mother=' . $id;
				}
				
				if (!empty($getfather) || !empty($getmother))
					echo '<li role="presentation" class="col-md-2 col-sm-2 col-xs-6 h3"><a href="newperson.php?' . $getfather . '&' . $getmother . '"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> ابن جديد</a></li>';
			}
			echo '</ul>';
		}
		if (isset($_SESSION["enableediting"]) && !$addedaddchild)
		{
			$addedaddchild = true;
			$getfather = '';
			$getmother = '';
			if ($gender=='male')
			{
				$getfather = 'father=' . $id;
				$getmother = 'mother=' . $spouseid;
			}
			else if ($gender=='female')
			{
				$getfather = 'father=' . $spouseid;
				$getmother = 'mother=' . $id;
			}
			
			if (!empty($getfather) || !empty($getmother))
			{
				echo '<ul class="nav">';
				echo '<li role="presentation" class="col-md-2 col-sm-2 col-xs-6 h3"><a href="newperson.php?' . $getfather . '&' . $getmother . '"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> ابن جديد</a></li>';
				echo '</ul>';
			}
		}
		?>
		</div>
		</div>
		</div>
		<div role="tabpanel" class="tab-pane col-md-12 col-sm-12 col-xs-12" id="tree" >
			<?php require "subtree.php"; ?>
		</div>
	</div>
</body>

</html>
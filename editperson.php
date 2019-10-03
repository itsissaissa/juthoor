<?php
	session_start(); 
	require 'include/settings.php';
	require 'include/localization.php';
	require 'include/connectmysql.php';
?>
<!-- empty hidden fields when fields are emptied -->
<!-- validate values -->
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
	
	<?php
	if ($pagedirection == 'rtl')
		echo '<!-- Load Bootstrap RTL theme from RawGit -->
		<link rel="stylesheet" href="//cdn.rawgit.com/morteza/bootstrap-rtl/v3.3.4/dist/css/bootstrap-rtl.min.css">';
	?>

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	
	<link rel="stylesheet" media="screen" href="https://fontlibrary.org/face/droid-arabic-kufi" type="text/css"/>
	<link href='https://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'>
	
	<!-- jQuery UI theme -->
	<link rel="stylesheet" href="include/jquery-ui-1.10.3.custom.css">
	
	<link rel="stylesheet" href="include/style.css">
	
	<?php
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
	
	$sql = "SELECT p.*, f.id as family, f.name_ar as family_ar, f.name_en as family_en, father.id as father, CONCAT_WS(' ',father.name_ar,fatherf.name_ar) as father_ar, CONCAT_WS(' ',father.name_en,fatherf.name_en) as father_en, mother.id as mother, CONCAT_WS(' ',mother.name_ar,motherf.name_ar) as mother_ar, CONCAT_WS(' ',mother.name_en,motherf.name_en) as mother_en FROM `person` p JOIN family f ON p.family = f.id LEFT JOIN `person` father ON p.father = father.id LEFT JOIN `family` fatherf ON father.family = fatherf.id LEFT JOIN `person` mother ON p.mother = mother.id LEFT JOIN `family` motherf ON mother.family = motherf.id WHERE p.id = '" . $id . "'";
	$result = mysqli_query($conn, $sql);
	
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		
		$name_ar = $row["name_ar"];
		$name_en = $row["name_en"];
		$family = $row["family"];
		$family_ar = $row["family_ar"];
		$family_en = $row["family_en"];
		$gender = $row["gender"];
		$alive = $row["alive"];
		$prefix_ar = $row["prefix_ar"];
		$prefix_en = $row["prefix_en"];
		$suffix_ar = $row["suffix_ar"];
		$suffix_en = $row["suffix_en"];
		$altname_ar = $row["altname_ar"];
		$altname_en = $row["altname_en"];
		$gentitle = $row["gentitle"];
		$dob = $row["dob"];
		$unknowndob = $row["unknowndob"];
		$dod = $row["dod"];
		
		$father = $row["father"];
		$father_ar = $row["father_ar"];
		$father_en = $row["father_en"];
		$mother = $row["mother"];
		$mother_ar = $row["mother_ar"];
		$mother_en = $row["mother_en"];
	}
	else
	{
		header("Location: ./");
	}
	
	?>
	
	<script>
		$.widget( "custom.catcomplete", $.ui.autocomplete, {
		_create: function() {
		  this._super();
		  this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
		},
		_renderMenu: function( ul, items ) {
		  var that = this,
			notcurrentCategory = "";
		  $.each( items, function( index, item ) {
			var li;
			if ( item.category && item.category != notcurrentCategory ) {
			  ul.append( "<li class='ui-autocomplete-category h4'>" + item.category + "</li>" );
			  notcurrentCategory = item.category;
			}
			li = that._renderItemData( ul, item );
			li.attr( "style", "font-size:0;" );
			li.append( "<span class='" + item.class + "'>" + item.label_short + " <small>" + item.desc + "</small></span>" );
			if ( item.category ) {
			  li.attr( "aria-label", item.category + " : " + item.label );
			}
		  });
		}
		});
	</script>
	<title><?php echo $locstring_editpersondetails; ?></title>
	
</head>

<body class="container" dir='<?php echo $pagedirection;?>'>

	<br/><br/>
	
	<div class="row" id="header">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<h3><a href="./"><?php echo $locstring_mainpage; ?></a></h3><h1><span class="glyphicon glyphicon-tree-deciduous" aria-hidden="true"></span> <?php echo $locstring_editpersondetails; ?></h1>
			<br/>
			<ol class="breadcrumb">
				<li class="active"><a href="person.php?p=<?php echo $id; ?>"><?php if($pagelanguage == "ar") echo $name_ar; else if($pagelanguage == "en") echo $name_en; ?></a></li>
			</ol>
			<form class="padded" method="post" action="updateperson.php">
				<input type="hidden" name="id" value="<?php echo $id; ?>">
				<ul class="nav nav-pills" role="tablist">
					<li id="simpleli" role="presentation" class="active"><a href="#simple" aria-controls="simple" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-file" aria-hidden="true"></span> <?php echo $locstring_simpleview; ?></a></li>
					<li id="detailedli" role="presentation"><a href="#detailed" aria-controls="detailed" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span> <?php echo $locstring_detailedview; ?></a></li>
				</ul>
				<div class="form-group form-inline">
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active container" id="simple" style="padding:0;">
							<label class="h4"><?php echo $locstring_name; ?></label>
							<br/>
							<div class="input-group form-inline" style="width:49%">
								<div>
									<input type="text" class="form-control" name="name_ar" required placeholder="<?php echo $locstring_firstname_ar; ?>" style="width:50%" value="<?php echo $name_ar; ?>">
									<input type="text" class="form-control" name="name_en" required dir="ltr" placeholder="<?php echo $locstring_firstname_en; ?>" style="width:50%" value="<?php echo $name_en; ?>">
								</div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane container" id="detailed" style="padding:0;">
							<label class="h4"><?php echo $locstring_name; ?></label>
							<br/>
							<div class="input-group" style="width:50%">
								<div>
									<?php
									if ($pagedirection == 'rtl')
										echo '<input type="text" class="form-control" name="prefix_ar" placeholder="'.$locstring_prefix_ar.'" style="width:21%" value="'.$prefix_ar.'">
										<input type="text" class="form-control" name="name_ar" required placeholder="'.$locstring_name_ar.'"  style="width:21%" value="'.$name_ar.'">
										<input type="text" class="form-control" name="suffix_ar" placeholder="'.$locstring_suffix_ar.'" style="width:21%" value="'.$suffix_ar.'">';
									else if ($pagedirection == 'ltr')
									echo '<input type="text" class="form-control" name="suffix_ar" placeholder="'.$locstring_suffix_ar.'" style="width:21%" value="'.$suffix_ar.'">
										<input type="text" class="form-control" name="name_ar" required placeholder="'.$locstring_name_ar.'"  style="width:21%" value="'.$name_ar.'">
										<input type="text" class="form-control" name="prefix_ar" placeholder="'.$locstring_prefix_ar.'" style="width:21%" value="'.$prefix_ar.'">';
									?>
								</div>
								<div class="input-group" style="padding-right:5%;width:35%">
									<div class="input-group-addon">"</div>
									<input type="text" class="form-control" name="altname_ar" placeholder="<?php echo $locstring_altname_ar; ?>" value="<?php echo $altname_ar; ?>">
									<div class="input-group-addon">"</div>
								</div>
								<br/>
								<?php
								if ($pagedirection == 'rtl')
									echo '<p class="help-block">
										<span style="padding-right:0%">'.$locstring_example_ar.' '.$locstring_prefix_arexample.'</span>
										<span style="padding-right:8%">'.$locstring_example_ar.' '.$locstring_name_arexample.'</span>
										<span style="padding-right:8%">'.$locstring_example_ar.' '.$locstring_suffix_arexample.'</span>
										<span style="padding-right:18%">'.$locstring_example_ar.' '.$locstring_altname_arexample.'</span>
										<br/>
									</p>';
								else if ($pagedirection == 'ltr')
									echo '<p class="help-block">
										<span style="padding-left:7.5%">'.$locstring_example_ar.' '.$locstring_altname_arexample.'</span>
										<span style="padding-left:6.5%">'.$locstring_example_ar.' '.$locstring_prefix_arexample.'</span>
										<span style="padding-left:6.5%">'.$locstring_example_ar.' '.$locstring_name_arexample.'</span>
										<span style="padding-left:9.5%">'.$locstring_example_ar.' '.$locstring_suffix_arexample.'</span>
										<br/>
									</p>';
								?>
								<div>
									<div>
										<?php
										if ($pagedirection == 'rtl')
											echo '<input type="text" class="form-control" name="suffix_en" placeholder="'.$locstring_suffix_en.'" style="width:21%" dir="ltr" value="'.$suffix_en.'">
											<input type="text" class="form-control" name="name_en" required dir="ltr" placeholder="'.$locstring_name_en.'"  style="width:21%" dir="ltr" value="'.$name_en.'">
											<input type="text" class="form-control" name="prefix_en" placeholder="'.$locstring_prefix_en.'" style="width:21%" dir="ltr" value="'.$prefix_en.'">';
										else if ($pagedirection == 'ltr')
											echo '<input type="text" class="form-control" name="prefix_en" placeholder="'.$locstring_prefix_en.'" style="width:21%" dir="ltr" value="'.$prefix_en.'">
											<input type="text" class="form-control" name="name_en" required dir="ltr" placeholder="'.$locstring_name_en.'"  style="width:21%" dir="ltr" value="'.$name_en.'">
											<input type="text" class="form-control" name="suffix_en" placeholder="'.$locstring_suffix_en.'" style="width:21%" dir="ltr" value="'.$suffix_en.'">';
										?>
									</div>
									<div class="input-group" style="padding-right:5%;width:35%">
										<div class="input-group-addon">"</div>
										<input type="text" class="form-control" name="altname_en" placeholder="Alt. name" dir="ltr" value="<?php echo $altname_en; ?>">
										<div class="input-group-addon">"</div>
									</div>
									
									<?php
									if ($pagedirection == 'rtl')
										echo '<p class="help-block" dir="ltr">
											<span style="padding-left:7.5%">'.$locstring_example_en.' '.$locstring_altname_enexample.'</span>
											<span style="padding-left:18.5%">'.$locstring_example_en.' '.$locstring_prefix_enexample.'</span>
											<span style="padding-left:6.5%">'.$locstring_example_en.' '.$locstring_name_enexample.'</span>
											<span style="padding-left:9.5%">'.$locstring_example_en.' '.$locstring_suffix_enexample.'</span>
										</p>';
									else if ($pagedirection == 'ltr')
										echo '<p class="help-block" dir="ltr">
											<span style="padding-right:8.5%">'.$locstring_example_en.' '.$locstring_prefix_enexample.'</span>
											<span style="padding-right:6.5%">'.$locstring_example_en.' '.$locstring_name_enexample.'</span>
											<span style="padding-right:18.5%">'.$locstring_example_en.' '.$locstring_suffix_enexample.'</span>
											<span style="padding-right:7.5%">'.$locstring_example_en.' '.$locstring_altname_enexample.'</span>
										</p>';
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<input type="text" class="form-control" name="gentitle" placeholder="<?php echo $locstring_order; ?>" style="width:12%" value="<?php echo $gentitle; ?>">
				<br/><br/>
				<div class="form-group">
					<div class="input-group form-inline">
						<input type="radio" name="gender" value="male" onclick="if(!$('#husband').hasClass('hidden')) $('#husband').addClass('hidden');$('#wife').removeClass('hidden');$('input[name=spouse]').val('-1');" <?php if($gender == 'male') echo 'checked'; ?>> <?php echo $locstring_male; ?>
						<input type="radio" name="gender" value="female" onclick="if(!$('#wife').hasClass('hidden')) $('#wife').addClass('hidden');$('#husband').removeClass('hidden');$('input[name=spouse]').val('-1');" <?php if($gender == 'female') echo 'checked'; ?>> <?php echo $locstring_female; ?>
					<div class="input-group form-inline" style="padding-right:25px;">
						<input type="checkbox" name="alive" <?php if($alive == '1') echo 'checked'; ?>> <?php echo $locstring_alive; ?>
					</div>
					<br/><br/>
					<label class="h4"><?php echo $locstring_lastname; ?></label>
					<br/>
					<div class="input-group form-inline" style="width:49%">
						<div>
							<input type="text" class="form-control" name="family_ar" placeholder="<?php echo $locstring_lastname_ar; ?>" style="width:50%" value="<?php echo $family_ar; ?>" <?php if (!empty($father_ar)) echo 'disabled'; ?>>
							<input type="text" class="form-control" name="family_en" dir="ltr" placeholder="<?php echo $locstring_lastname_en; ?>" style="width:50%" value="<?php echo $family_en; ?>" <?php if (!empty($father_ar)) echo 'disabled'; ?>>
							<input class="hidden" name="family" required value="<?php echo $family; ?>">
							<div class="input-group form-inline">
								<input type="checkbox" onclick="if ($(this).attr('checked')){ $('input[name=family_ar]').removeAttr('disabled');$('input[name=family_en]').removeAttr('disabled');$('input[name=family]').val('-1')}" name="newfamily"> ليست في القائمة
							</div>
						</div>
					</div>
					<br/><br/>
					<label class="h4"><?php echo $locstring_birthdate; ?></label><label class="h4" style="margin-<?php if ($pagedirection=='rtl') echo 'right'; else echo 'left'?>:160px"><?php echo $locstring_deathdate; ?></label>
					<br/>
					<div class="input-group form-inline" style="width:49%">
						<input type="text" class="form-control datepicker" name="dob" placeholder="<?php echo $locstring_dateformat; ?>" value="<?php echo $dob; ?>">
					</div>
					<div class="input-group form-inline" style="width:49%">
						 <input type="text" class="form-control datepicker" name="dod" placeholder="<?php echo $locstring_dateformat; ?>" value="<?php echo $dod; ?>">
					</div>
					<div class="input-group form-inline">
						<div class="input-group form-inline">
							<input type="checkbox" name="unknowndob" <?php if ($unknowndob == '1') echo 'checked'; ?>> <?php echo $locstring_fakedate; ?>
						</div>
					</div>
				</div>
				<br/>
				<div class="form-group">
					<label class="h4"><?php echo $locstring_father; ?></label>
					<br/>
					<div class="input-group form-inline" style="width:49%">
						<div>
							<input type="text" class="form-control" name="father_ar" placeholder="<?php echo $locstring_fathername_ar; ?>" style="width:50%" value="<?php echo $father_ar; ?>">
							<input type="text" class="form-control" name="father_en" dir="ltr" placeholder="<?php echo $locstring_fathername_en; ?>" style="width:50%" value="<?php echo $father_en; ?>">
							<input class="hidden" name="father" value="<?php echo $father; ?>">
						</div>
					</div>
					<br/>
					<label class="h4"><?php echo $locstring_mother; ?></label>
					<br/>
					<div class="input-group form-inline" style="width:49%">
						<div>
							<input type="text" class="form-control" name="mother_ar" placeholder="<?php echo $locstring_mothername_ar; ?>" style="width:50%" value="<?php echo $mother_ar; ?>">
							<input type="text" class="form-control" name="mother_en" dir="ltr" placeholder="<?php echo $locstring_mothername_en; ?>" style="width:50%" value="<?php echo $mother_en; ?>">
							<input class="hidden" name="mother" value="<?php echo $mother; ?>">
						</div>
					</div>
					<br/>
					<div id="husband" <?php if($gender=='male') echo 'class="hidden"'; ?>>
						<label class="h4"><?php echo $locstring_addhusband; ?></label>
						<br/>
						<div class="input-group form-inline" style="width:49%">
							<div>
								<input type="text" class="form-control" name="husband_ar" placeholder="<?php echo $locstring_husbandname_ar; ?>" style="width:50%">
								<input type="text" class="form-control" name="husband_en" dir="ltr" placeholder="<?php echo $locstring_husbandname_en; ?>" style="width:50%">
								<input type="checkbox" id='notcurrent1' name="notcurrent" onchange="if ($(this).attr('checked')) $('#notcurrent2').attr('checked','checked); else $('#notcurrent2').removeAttr('checked')"> <?php echo $locstring_divorcedmale; ?>
							</div>
						</div>
					</div>
					<div id="wife" <?php if($gender=='female') echo 'class="hidden"'; ?>>
						<label class="h4"><?php echo $locstring_addwife; ?></label>
						<br/>
						<div class="input-group form-inline" style="width:49%">
							<div>
								<input type="text" class="form-control" name="wife_ar" placeholder="<?php echo $locstring_wifename_ar; ?>" style="width:50%">
								<input type="text" class="form-control" name="wife_en" dir="ltr" placeholder="<?php echo $locstring_wifename_en; ?>" style="width:50%">
								<input type="checkbox" id='notcurrent2' name="notcurrent" onchange="if ($(this).attr('checked')) $('#notcurrent1').attr('checked','checked); else $('#notcurrent1').removeAttr('checked')"> <?php echo $locstring_divorcedfemale; ?>
							</div>
						</div>
					</div>
					<input class="hidden" name="spouse" value="-1">
				</div>
				<br/>
				<button type="submit" class="btn btn-default"><?php echo $locstring_update; ?></button>
				<button type="reset" onclick="if ('<?php echo $father?>' === '') {$('input[name=family_ar]').removeAttr('disabled');$('input[name=family_en]').removeAttr('disabled');} else {$('input[name=family_ar]').attr('disabled','disabled');$('input[name=family_en]').attr('disabled','disabled';}" class="btn btn-default"><?php echo $locstring_cancelchanges; ?></button>
			</form>
			<script type="text/javascript">
				$(function() {
					// Overrides the default autocomplete filter function to 
					// search only from the beginning of the string
					$.ui.autocomplete.filter = function (array, term) {
					  var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex(term), "i");
					  return $.grep(array, function (value) {
						return matcher.test(value.label || value.value || value);
					  });
					};

					var males_ar = [];
					var males_en = [];
					var females_ar = [];
					var females_en = [];
					var family_ar = [];
					var family_en = [];
					<?php
					$sql = "SELECT p.id as id, p.name_ar as label_ar, CONCAT_WS(' ', father.name_ar, fatherfather.name_ar, fatherfatherfather.name_ar) as desc_ar, family.name_ar as category_ar, p.name_en as label_en, CONCAT_WS(' ', father.name_en, fatherfather.name_en, fatherfatherfather.name_en) as desc_en, family.name_en as category_en, family.id as category_id, p.gentitle as gentitle, p.gender as gender FROM `person` p INNER JOIN `family` family ON p.family = family.id LEFT JOIN `person` father ON p.father = father.id LEFT JOIN `person` fatherfather ON father.father = fatherfather.id LEFT JOIN `person` fatherfatherfather ON fatherfather.father = fatherfatherfather.id ORDER BY family.name_ar, p.name_ar, father.name_ar, fatherfather.name_ar, fatherfatherfather.name_ar, p.gentitle";
					$result = mysqli_query($conn, $sql);
					
					if (mysqli_num_rows($result) > 0) {
						while ($row = mysqli_fetch_assoc($result)) {					
							$id = $row["id"];
							$label_ar = $row["label_ar"];
							$label_en = $row["label_en"];
							$desc_ar = $row["desc_ar"];
							$desc_en = $row["desc_en"];
							$category_ar = $row["category_ar"];
							$category_en = $row["category_en"];
							$category_id = $row["category_id"];
							$gender = $row["gender"];
							$gentitle = $row["gentitle"];
							if (!empty($gentitle))
								$gentitle = ' (' . toroman($gentitle) . ')';
							$array = "";
							
							if ($gender == "male")
								$array = "males";
							else if ($gender == "female")
								$array = "females";
							
							$space = ' ';
							if (empty($desc_ar) || $desc_ar == '')
								$space = '';

							echo $array . '_ar.push({ id : "' . $id . '", label: "' . $label_ar . $space . $desc_ar . ' ' . $category_ar . '", label_short: "' . $label_ar . '", label_otherlanguage : "' . $label_en . $space . $desc_en . ' ' . $category_en . '", desc: "' . $desc_ar . $gentitle . '", category: "' . $category_ar .  '", category_otherlanguage: "' . $category_en .  '", category_id: "' . $category_id .  '", class: "' . $gender . ' h4" });
							';
							echo $array . '_en.push({ id : "' . $id . '", label: "' . $label_en . $space . $desc_en . ' ' . $category_en . '", label_short: "' . $label_en . '", label_otherlanguage : "' . $label_ar . $space . $desc_ar . ' ' . $category_ar . '", desc: "' . $desc_en . $gentitle . '", category: "' . $category_en .  '", category_otherlanguage: "' . $category_ar .  '", category_id: "' . $category_id .  '", class: "' . $gender . ' h4" });
							';
						}
					}
					
					$sql = "SELECT f.id as id, f.name_ar as label_ar, f.name_en as label_en, remarks as desc_ FROM `family` f ORDER BY f.name_ar";
					$result = mysqli_query($conn, $sql);
					
					if (mysqli_num_rows($result) > 0) {
						while ($row = mysqli_fetch_assoc($result)) {					
							$id = $row["id"];
							$label_ar = $row["label_ar"];
							$label_en = $row["label_en"];
							$desc = $row["desc_"];
							
							echo 'family_ar.push({ id : "' . $id . '", label: "' . $label_ar . '", label_short: "' . $label_ar . '", label_otherlanguage: "' . $label_en . '", desc: "' . $desc . '", class: "h4" });
							';
							echo 'family_en.push({ id : "' . $id . '", label: "' . $label_en . '", label_short: "' . $label_en . '", label_otherlanguage: "' . $label_ar . '", desc: "' . $desc . '", class: "h4" });
							';
						}
					}
					?>
					var husbands_ar = males_ar;
					var husbands_en = males_en;
					var wives_ar = females_ar;
					var wives_en = females_en;
					$( "input[name=father_ar]").catcomplete({
					  delay: 0,
					  source: males_ar,
					  select: function (event, ui) {
						$("input[name=father]").val(ui.item.id);
						$("input[name=father_en]").val(ui.item.label_otherlanguage);
						$('input[name=newfamily]').removeAttr('checked')
						$("input[name=family]").val(ui.item.category_id);
						$("input[name=family_ar]").val(ui.item.category);
						$("input[name=family_en]").val(ui.item.category_otherlanguage);
					  }
					});
					$( "input[name=father_en]").catcomplete({
					  delay: 0,
					  source: males_en,
					  select: function (event, ui) {
						$("input[name=father]").val(ui.item.id);
						$("input[name=father_ar]").val(ui.item.label_otherlanguage);
						$("input[name=family]").val(ui.item.category_id);
						$("input[name=family_en]").val(ui.item.category);
						$("input[name=family_ar]").val(ui.item.category_otherlanguage);
					  }
					});
					$( "input[name=mother_ar]").catcomplete({
					  delay: 0,
					  source: females_ar,
					  select: function (event, ui) {
						$("input[name=mother]").val(ui.item.id);
						$("input[name=mother_en]").val(ui.item.label_otherlanguage);
					  }
					});
					$( "input[name=mother_en]").catcomplete({
					  delay: 0,
					  source: females_en,
					  select: function (event, ui) {
						$("input[name=mother]").val(ui.item.id);
						$("input[name=mother_ar]").val(ui.item.label_otherlanguage);
					  }
					});
					$( "input[name=family_ar]").catcomplete({
					  delay: 0,
					  source: family_ar,
					  select: function (event, ui) {
						$("input[name=family]").val(ui.item.id);
						$("input[name=family_en]").val(ui.item.label_otherlanguage);
					  }
					});
					$( "input[name=family_en]").catcomplete({
					  delay: 0,
					  source: family_en,
					  select: function (event, ui) {
						$("input[name=family]").val(ui.item.id);
						$("input[name=family_ar]").val(ui.item.label_otherlanguage);
					  }
					});
					$( "input[name=husband_ar]").catcomplete({
					  delay: 0,
					  source: husbands_ar,
					  select: function (event, ui) {
						$("input[name=spouse]").val(ui.item.id);
						$("input[name=husband_en]").val(ui.item.label_otherlanguage);
					  }
					});
					$( "input[name=husband_en]").catcomplete({
					  delay: 0,
					  source: husbands_en,
					  select: function (event, ui) {
						$("input[name=spouse]").val(ui.item.id);
						$("input[name=husband_ar]").val(ui.item.label_otherlanguage);
					  }
					});
					$( "input[name=wife_ar]").catcomplete({
					  delay: 0,
					  source: wives_ar,
					  select: function (event, ui) {
						$("input[name=spouse]").val(ui.item.id);
						$("input[name=wife_en]").val(ui.item.label_otherlanguage);
					  }
					});
					$( "input[name=wife_en]").catcomplete({
					  delay: 0,
					  source: wives_en,
					  select: function (event, ui) {
						$("input[name=spouse]").val(ui.item.id);
						$("input[name=wife_ar]").val(ui.item.label_otherlanguage);
					  }
					});
					if ($("input[name=gender]:first:checked").val())
						$('#wife').removeClass('hidden');
					else $('#husband').removeClass('hidden');
				});
				$("input[name=father_ar]").change(function(){
					if ($("input[name=father_ar]").val())
					{
						if ($("input[name=father]").val())
						{
							$("input[name=family_ar]").attr("disabled","disabled");
							$("input[name=family_en]").attr("disabled","disabled");
						}
						else
						{
							$("input[name=family_ar]").removeAttr("disabled");
							$("input[name=family_en]").removeAttr("disabled");
						}
					}
					else
					{
						$("input[name=father]").val('');
						$("input[name=father_en]").val('');
						$("input[name=family_ar]").removeAttr("disabled");
						$("input[name=family_en]").removeAttr("disabled");
					}
				});
				$("input[name=father_ar]").keyup(function(){
					if ($("input[name=father_ar]").val())
					{
						if ($("input[name=father]").val())
						{
							$("input[name=family_ar]").attr("disabled","disabled");
							$("input[name=family_en]").attr("disabled","disabled");
						}
						else
						{
							$("input[name=family_ar]").removeAttr("disabled");
							$("input[name=family_en]").removeAttr("disabled");
						}
					}
					else
					{
						$("input[name=father]").val('');
						$("input[name=father_en]").val('');
						$("input[name=family_ar]").removeAttr("disabled");
						$("input[name=family_en]").removeAttr("disabled");
					}
				});
				$("input[name=father_en]").change(function(){
					if ($("input[name=father_en]").val())
					{
						if ($("input[name=father]").val())
						{
							$("input[name=family_ar]").attr("disabled","disabled");
							$("input[name=family_en]").attr("disabled","disabled");
						}
						else
						{
							$("input[name=family_ar]").removeAttr("disabled");
							$("input[name=family_en]").removeAttr("disabled");
						}
					}
					else
					{
						$("input[name=father]").val('');
						$("input[name=father_ar]").val('');
						$("input[name=family_ar]").removeAttr("disabled");
						$("input[name=family_en]").removeAttr("disabled");
					}
				});
				$("input[name=father_en]").keyup(function(){
					if ($("input[name=father_en]").val())
					{
						if ($("input[name=father]").val())
						{
							$("input[name=family_ar]").attr("disabled","disabled");
							$("input[name=family_en]").attr("disabled","disabled");
						}
						else
						{
							$("input[name=family_ar]").removeAttr("disabled");
							$("input[name=family_en]").removeAttr("disabled");
						}
					}
					else
					{
						$("input[name=father]").val('');
						$("input[name=father_ar]").val('');
						$("input[name=family_ar]").removeAttr("disabled");
						$("input[name=family_en]").removeAttr("disabled");
					}
				});
				$("input[name=mother_ar]").change(function(){
					if (!$("input[name=mother_ar]").val())
					{
						$("input[name=mother]").val('');
						$("input[name=mother_en]").val('');
					}
				});
				$("input[name=mother_ar]").keyup(function(){
					if (!$("input[name=mother_ar]").val())
					{
						$("input[name=mother]").val('');
						$("input[name=mother_en]").val('');
					}
				});
				$("input[name=mother_en]").change(function(){
					if (!$("input[name=mother_en]").val())
					{
						$("input[name=mother]").val('');
						$("input[name=mother_ar]").val('');
					}
				});
				$("input[name=mother_en]").keyup(function(){
					if (!$("input[name=mother_en]").val())
					{
						$("input[name=mother]").val('');
						$("input[name=mother_ar]").val('');
					}
				});
				$("input[name=husband_ar]").change(function(){
					if (!$("input[name=husband_ar]").val())
					{
						$("input[name=spouse]").val('-1');
						$("input[name=husband_en]").val('');
					}
				});
				$("input[name=husband_ar]").keyup(function(){
					if (!$("input[name=husband_ar]").val())
					{
						$("input[name=spouse]").val('-1');
						$("input[name=husband_en]").val('');
					}
				});
				$("input[name=husband_en]").change(function(){
					if (!$("input[name=husband_en]").val())
					{
						$("input[name=spouse]").val('-1');
						$("input[name=husband_ar]").val('');
					}
				});
				$("input[name=husband_en]").keyup(function(){
					if (!$("input[name=husband_en]").val())
					{
						$("input[name=spouse]").val('-1');
						$("input[name=husband_ar]").val('');
					}
				});
				$("input[name=wife_ar]").change(function(){
					if (!$("input[name=wife_ar]").val())
					{
						$("input[name=spouse]").val('-1');
						$("input[name=wife_en]").val('');
					}
				});
				$("input[name=wife_ar]").keyup(function(){
					if (!$("input[name=wife_ar]").val())
					{
						$("input[name=spouse]").val('-1');
						$("input[name=wife_en]").val('');
					}
				});
				$("input[name=wife_en]").change(function(){
					if (!$("input[name=wife_en]").val())
					{
						$("input[name=spouse]").val('-1');
						$("input[name=wife_ar]").val('');
					}
				});
				$("input[name=wife_en]").keyup(function(){
					if (!$("input[name=wife_en]").val())
					{
						$("input[name=spouse]").val('-1');
						$("input[name=wife_ar]").val('');
					}
				});
				$( "input[name=name_ar]" ).change(function() {
				  var newval = $(this).val();
				  $( "input[name=name_ar]" ).each(function() {
					$(this).val(newval);
				  });
				});
				$( "input[name=name_ar]" ).keyup(function() {
				  var newval = $(this).val();
				  $( "input[name=name_ar]" ).each(function() {
					$(this).val(newval);
				  });
				});
				$( "input[name=name_en]" ).change(function() {
				  var newval = $(this).val();
				  $( "input[name=name_en]" ).each(function() {
					$(this).val(newval);
				  });
				});
				$( "input[name=name_en]" ).keyup(function() {
				  var newval = $(this).val();
				  $( "input[name=name_en]" ).each(function() {
					$(this).val(newval);
				  });
				});
			</script>
		</div>
		</div>
	</div>

</body>

</html>
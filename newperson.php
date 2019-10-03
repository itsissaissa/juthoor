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
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css">
	<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/js/bootstrap-datepicker.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/locales/bootstrap-datepicker.ar.min.js"></script>
	
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
	
	if (!empty($_GET["father"]))
	{
		$father = $conn->real_escape_string($_GET["father"]);
		$sql = "SELECT p.id as id, CONCAT_WS(' ',p.name_ar,f.name_ar) as name_ar, CONCAT_WS(' ',p.name_en,f.name_en) as name_en, f.name_ar as family_ar, f.name_en as family_en, f.id as family FROM `person` p JOIN family f ON p.family = f.id WHERE p.id = '" . $father . "'";
		$result = mysqli_query($conn, $sql);
		
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			
			$father_ar = $row["name_ar"];
			$father_en = $row["name_en"];
			$father = $row["id"];
			$family_ar = $row["family_ar"];
			$family_en = $row["family_en"];
			$family = $row["family"];
		}
	}
	if (!empty($_GET["mother"]))
	{
		$mother = $conn->real_escape_string($_GET["mother"]);
		$sql = "SELECT p.id as id, CONCAT_WS(' ',p.name_ar,f.name_ar) as name_ar, CONCAT_WS(' ',p.name_en,f.name_en) as name_en FROM `person` p JOIN family f ON p.family = f.id WHERE p.id = '" . $mother . "'";
		$result = mysqli_query($conn, $sql);
		
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			
			$mother_ar = $row["name_ar"];
			$mother_en = $row["name_en"];
			$mother = $row["id"];
		}
	}
	if (!empty($_GET["husband"]))
	{
		$husband = $conn->real_escape_string($_GET["husband"]);
		$sql = "SELECT p.id as id, CONCAT_WS(' ',p.name_ar,f.name_ar) as name_ar, CONCAT_WS(' ',p.name_en,f.name_en) as name_en FROM `person` p JOIN family f ON p.family = f.id WHERE p.id = '" . $husband . "'";
		$result = mysqli_query($conn, $sql);
		
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			
			$husband_ar = $row["name_ar"];
			$husband_en = $row["name_en"];
			$spouse = $row["id"];
			$gender = 'female';
		}
	}
	if (!empty($_GET["wife"]))
	{
		$wife = $conn->real_escape_string($_GET["wife"]);
		$sql = "SELECT p.id as id, CONCAT_WS(' ',p.name_ar,f.name_ar) as name_ar, CONCAT_WS(' ',p.name_en,f.name_en) as name_en FROM `person` p JOIN family f ON p.family = f.id WHERE p.id = '" . $wife . "'";
		$result = mysqli_query($conn, $sql);
		
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			
			$wife_ar = $row["name_ar"];
			$wife_en = $row["name_en"];
			$spouse = $row["id"];
			$gender = 'male';
		}
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
	<title>إضافة فرد جديد</title>
	
</head>

<body class="container" dir='<?php echo $pagedirection;?>'>

	<br/><br/>
	
	<div class="row" id="header">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<h3><a href="./">الرئيسية</a></h3><h1><span class="glyphicon glyphicon-tree-deciduous" aria-hidden="true"></span> إضافة فرد جديد</h1>
			<br/>
			<ol class="breadcrumb">
				<li class="active">إضافة فرد جديد</li>
			</ol>
			<form class="padded" method="post" action="insertperson.php">
				<ul class="nav nav-pills" role="tablist">
					<li id="simpleli" role="presentation" class="active"><a href="#simple" aria-controls="simple" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-file" aria-hidden="true"></span> بسيط</a></li>
					<li id="detailedli" role="presentation"><a href="#detailed" aria-controls="detailed" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span> مفصل</a></li>
				</ul>
				<div class="form-group form-inline">
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active container" id="simple" style="padding:0;">
							<label class="h4">الاسم</label>
							<br/>
							<div class="input-group form-inline" style="width:49%">
								<div>
									<input type="text" class="form-control" name="name_ar" required placeholder="الاسم الأول" style="width:50%">
									<input type="text" class="form-control" name="name_en" required dir="ltr" placeholder="First Name" style="width:50%">
								</div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane container" id="detailed" style="padding:0;">
							<label class="h4">الاسم</label>
							<br/>
							<div class="input-group" style="width:50%">
								<div>
									<input type="text" class="form-control" name="prefix_ar" placeholder="لقب" style="width:21%">
									<input type="text" class="form-control" name="name_ar" required placeholder="الاسم"  style="width:21%">
									<input type="text" class="form-control" name="suffix_ar" placeholder="لقب مضاف إليه" style="width:21%">
								</div>
								<div class="input-group" style="padding-right:5%;width:35%">
									<div class="input-group-addon">"</div>
									<input type="text" class="form-control" name="altname_ar" placeholder="الاسم الدارج">
									<div class="input-group-addon">"</div>
								</div>
								<br/>
								<p class="help-block">
									<span style="padding-right:0%">مثال: السلطان</span>
									<span style="padding-right:8%">مثال: محمد</span>
									<span style="padding-right:8%">مثال: الثاني</span>
									<span style="padding-right:18%">مثال: الفاتح</span>
									<br/>
								</p>
								<div>
									<div>
										<input type="text" class="form-control" name="suffix_en" placeholder="Suffix" style="width:21%" dir="ltr">
										<input type="text" class="form-control" name="name_en" required dir="ltr" placeholder="Name"  style="width:21%" dir="ltr">
										<input type="text" class="form-control" name="prefix_en" placeholder="Prefix" style="width:21%" dir="ltr">
									</div>
									<div class="input-group" style="padding-right:5%;width:35%">
										<div class="input-group-addon">"</div>
										<input type="text" class="form-control" name="altname_en" placeholder="Alt. name" dir="ltr">
										<div class="input-group-addon">"</div>
									</div>
									
									<p class="help-block" dir="ltr">
										<span style="padding-left:7.5%">e.g. Jimmy</span>
										<span style="padding-left:18.5%">e.g. President</span>
										<span style="padding-left:6.5%">e.g. James</span>
										<span style="padding-left:9.5%">e.g. Jr.</span>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<input type="text" class="form-control" name="gentitle" placeholder="الترتيب" style="width:12%">
				<br/><br/>
				<div class="form-group">
					<div class="input-group form-inline">
						<input type="radio" name="gender" value="male" onclick="if(!$('#husband').hasClass('hidden')) $('#husband').addClass('hidden');$('#wife').removeClass('hidden');$('input[name=spouse]').val('-1');" <?php if(!empty($gender) && $gender == 'male') echo 'checked'; else if (empty($gender)) echo 'checked'; ?>> ذكر
						<input type="radio" name="gender" value="female" onclick="if(!$('#wife').hasClass('hidden')) $('#wife').addClass('hidden');$('#husband').removeClass('hidden');$('input[name=spouse]').val('-1');" <?php if(!empty($gender) && $gender == 'female') echo 'checked'; ?>> أنثى
					<div class="input-group form-inline" style="padding-right:25px;">
						<input type="checkbox" name="alive" checked> على قيد الحياة
					</div>
					<br/><br/>
					<label class="h4">العائلة</label>
					<br/>
					<div class="input-group form-inline" style="width:49%">
						<div>
							<input type="text" class="form-control" name="family_ar" placeholder="اسم العائلة" style="width:50%" value="<?php if(!empty($family_ar)) echo $family_ar; ?>" <?php if (!empty($father_ar)) echo 'disabled'; ?>>
							<input type="text" class="form-control" name="family_en" dir="ltr" placeholder="Last name" style="width:50%" value="<?php echo $family_en; ?>" <?php if (!empty($father_ar)) echo 'disabled'; ?>>
							<input class="hidden" name="family" required value="<?php if(!empty($family)) echo $family; else echo '-1' ?>">
							<div class="input-group form-inline">
								<input type="checkbox" onclick="if ($(this).attr('checked')){ $('input[name=family_ar]').removeAttr('disabled');$('input[name=family_en]').removeAttr('disabled');$('input[name=family]').val('-1')}" name="newfamily"> ليست في القائمة
							</div>
						</div>
					</div>
					<br/><br/>
					<label class="h4">تاريخ الميلاد</label><label class="h4" style="margin-right:160px">تاريخ الوفاة</label>
					<br/>
					<div class="input-group form-inline" style="width:49%">
						<input type="text" class="form-control datepicker" name="dob" placeholder="يوم-شهر-سنة">
					</div>
					<div class="input-group form-inline" style="width:49%">
						 <input type="text" class="form-control datepicker" name="dod" placeholder="يوم-شهر-سنة">
					</div>
					<div class="input-group form-inline">
						<div class="input-group form-inline">
							<input type="checkbox" name="unknowndob"> تاريخ وهمي
						</div>
					</div>
				</div>
				<br/>
				<div class="form-group">
					<label class="h4">الأب</label>
					<br/>
					<div class="input-group form-inline" style="width:49%">
						<div>
							<input type="text" class="form-control" name="father_ar" placeholder="اسم الأب" style="width:50%" value="<?php  if(!empty($father_ar)) echo $father_ar; ?>">
							<input type="text" class="form-control" name="father_en" dir="ltr" placeholder="Father name" style="width:50%" value="<?php  if(!empty($father_en)) echo $father_en; ?>">
							<input class="hidden" name="father" value="<?php  if(!empty($father)) echo $father; ?>">
						</div>
					</div>
					<br/>
					<label class="h4">الأم</label>
					<br/>
					<div class="input-group form-inline" style="width:49%">
						<div>
							<input type="text" class="form-control" name="mother_ar" placeholder="اسم الأم" style="width:50%" value="<?php  if(!empty($mother_ar)) echo $mother_ar; ?>">
							<input type="text" class="form-control" name="mother_en" dir="ltr" placeholder="Mother name" style="width:50%" value="<?php  if(!empty($mother_en)) echo $mother_en; ?>">
							<input class="hidden" name="mother" value="<?php  if(!empty($mother)) echo $mother; ?>">
						</div>
					</div>
					<br/>
					<div id="husband" <?php if(empty($gender) || $gender=='male') echo 'class="hidden"'; ?>>
						<label class="h4">إضافة زوج</label>
						<br/>
						<div class="input-group form-inline" style="width:49%">
							<div>
								<input type="text" class="form-control" name="husband_ar" placeholder="اسم الزوج" style="width:50%" value="<?php if(!empty($husband_ar)) echo $husband_ar; ?>">
								<input type="text" class="form-control" name="husband_en" dir="ltr" placeholder="Husband name" style="width:50%" value="<?php if(!empty($husband_en)) echo $husband_en; ?>">
								<input type="checkbox" id='notcurrent1' name="notcurrent" onchange="if ($(this).attr('checked')) $('#notcurrent2').attr('checked','checked); else $('#notcurrent2').removeAttr('checked')"> مطلّق
							</div>
						</div>
					</div>
					<div id="wife" <?php if(empty($gender) || $gender=='female') echo 'class="hidden"'; ?>>
						<label class="h4">إضافة زوجة</label>
						<br/>
						<div class="input-group form-inline" style="width:49%">
							<div>
								<input type="text" class="form-control" name="wife_ar" placeholder="اسم الزوجة" style="width:50%" value="<?php if(!empty($wife_ar)) echo $wife_ar; ?>">
								<input type="text" class="form-control" name="wife_en" dir="ltr" placeholder="Wife name" style="width:50%" value="<?php if(!empty($wife_en)) echo $wife_en; ?>">
								<input type="checkbox" id='notcurrent2' name="notcurrent" onchange="if ($(this).attr('checked')) $('#notcurrent1').attr('checked','checked); else $('#notcurrent1').removeAttr('checked')"> مطلّقة
							</div>
						</div>
					</div>
					<input class="hidden" name="spouse" value="<?php if (!empty($spouse)) echo $spouse; else echo '-1'; ?>">
				</div>
				<br/>
				<button type="submit" class="btn btn-default">إضافة</button>
				<button type="reset" onclick="$('input[name=family_ar]').removeAttr('disabled');$('input[name=family_en]').removeAttr('disabled');" class="btn btn-default">مسح</button>
			</form>
			<script type="text/javascript">
				$('.datepicker').datepicker({
					format: "yyyy-mm-dd",
					startView: 2,
					clearBtn: true,
					orientation: "top right"
				});
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
				$( "input[name=name_ar]" ).blur(function() {
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
				$( "input[name=name_en]" ).blur(function() {
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
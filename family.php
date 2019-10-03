<?php
	session_start(); 
	require 'include/settings.php';
	require 'include/localization.php';
	require 'include/connectmysql.php';
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
	
	@$id = $conn->real_escape_string($_GET["f"]);
	
	if (empty($id))
		header("Location: ./");
	
	$sql = "SELECT f.name_ar, f.name_en FROM family f WHERE f.id = '" . $id . "'";
	$result2 = mysqli_query($conn, $sql);			
	$row = mysqli_fetch_assoc($result2);
	
	$thisfamily = $row["name_".$pagelanguage];
	
	$namephrase = $thisfamily;
	
	$header = 'عائلة ' . $thisfamily;

	?>
	
	<title>شجرة <?php echo $header; ?></title>
	
	<style>
		html {
  position: relative;
  min-height: 100%;
}
body {
  /* Margin bottom by footer height */
  margin-bottom: 60px;
}
#header {
  text-align: center;
  position: absolute;
  bottom: 0;
  width: 100%;
  /* Set the fixed height of the footer here */
  height: 60px;
  background-color: #f5f5f5;
}
	</style>
	
</head>

<body onload="prettify();">
	
	<nav class="navbar-fixed-top" dir='<?php echo $pagedirection;?>' style="text-align:center;background-color:inherit; border-bottom: 1px solid #ddd;"
		<br/>
        <h3><a href="./"><?php echo $locstring_mainpage; ?></a></h3><h1><span class="glyphicon glyphicon-tree-deciduous" aria-hidden="true"></span> <?php echo $locstring_familytreeprefamilyname." ".$thisfamily." ".$locstring_familytreepostfamilyname; ?></h1>
    </nav>
	
	<div class="row">
		<div id="treecontainer" class="col-md-12 col-sm-12">
			<br/><br/><br/><br/><br/><br/>
			<?php

			$dom = new DOMdocument();  

			$sql = "SELECT p.id as rootfather FROM person p JOIN family f ON p.family = f.id WHERE f.id = '" . $id . "' AND p.father IS NULL";
			$rootresult = mysqli_query($conn, $sql);
			
			$rootindex = 1;
			$numroots = mysqli_num_rows($rootresult);
			
			if (mysqli_num_rows($rootresult) > 0) {
				while($rootrow = mysqli_fetch_assoc($rootresult)) {
					
					$html = '<ul id="tree' . $rootindex . '" style="display:none">
					</ul>';
			  
					@$dom->loadHTML($html);    
					$xpath = new DOMXPath($dom);    
					$tree = $dom->getElementById('tree' . $rootindex);
					
					$rootfather = $rootrow["rootfather"];
					$nextfathers = new SplQueue();
					$children = [];
					$grandchildren = [];
					
					$firsttime = true;
					
					do {
						if ($firsttime)
						{
							$sql = "SELECT p.id as personid, p.name_ar as firstname_ar, p.name_en as firstname_en, f.name_ar as lastname_ar, f.name_en as lastname_en, p.gender as gender, s.id as spouseid, GROUP_CONCAT(CONCAT_WS(' ', s.name_ar, sf.name_ar) ORDER BY s.dob SEPARATOR '،') as spousename_ar, s.suffix_ar as spousesuffix_ar, sf.name_ar as spousefamily_ar, GROUP_CONCAT(CONCAT_WS(' ', s.name_en, sf.name_en) ORDER BY s.dob SEPARATOR '،') as spousename_en, s.suffix_en as spousesuffix_en, sf.name_en as spousefamily_en FROM person p LEFT JOIN `marriage` m ON p.gender = 'male' AND m.husband = p.id OR p.gender = 'female' AND m.wife = p.id LEFT JOIN `person` s ON p.gender = 'male' AND m.wife = s.id OR p.gender = 'female' AND m.husband = s.id LEFT JOIN `family` sf ON s.family = sf.id JOIN family f ON p.family = f.id WHERE f.id = '" . $id . "' AND p.id = '" . $rootfather . "' GROUP BY p.id";
							$father = 'tree' . $rootindex;
						}
						else
						{
							$father = $nextfathers->dequeue();
							
							$sql = "SELECT p.id as personid, p.name_ar as firstname_ar, p.name_en as firstname_en, f.name_ar as lastname_ar, f.name_en as lastname_en, p.gender as gender, s.id as spouseid, GROUP_CONCAT(CONCAT_WS(' ', s.name_ar, sf.name_ar) ORDER BY s.dob SEPARATOR '،') as spousename_ar, s.suffix_ar as spousesuffix_ar, sf.name_ar as spousefamily_ar, GROUP_CONCAT(CONCAT_WS(' ', s.name_en, sf.name_en) ORDER BY s.dob SEPARATOR '،') as spousename_en, s.suffix_en as spousesuffix_en, sf.name_en as spousefamily_en FROM person p LEFT JOIN `marriage` m ON p.gender = 'male' AND m.husband = p.id OR p.gender = 'female' AND m.wife = p.id LEFT JOIN `person` s ON p.gender = 'male' AND m.wife = s.id OR p.gender = 'female' AND m.husband = s.id LEFT JOIN `family` sf ON s.family = sf.id JOIN family f ON p.family = f.id WHERE p.father = '" . $father . "' OR p.mother = '" . $father . "' GROUP BY p.id ORDER BY -p.dob ASC, p.id DESC";
						}
						$result = mysqli_query($conn, $sql);
						
						$count =1;
						$first = true;

						if (mysqli_num_rows($result) > 0) {
							while($row = mysqli_fetch_assoc($result)) {
								
								$personid = $row["personid"];
								
								if (empty($highlight) || in_array($personid,$ancestors) || $personid == $highlight || $father == $highlightfather || in_array($father,$children) || in_array($father,$grandchildren))
								{
									$name_ar = $row["firstname_".$pagelanguage];
									$lastname_ar = $row["lastname_".$pagelanguage];
									if ($lastname_ar != $thisfamily || $firsttime)
										$name_ar .= ' ' . $lastname_ar;
									
									$firsttime = false;
									
									$gender = $row["gender"];
									$parentnode = $dom->getElementById($father);
									
									$thisa = $dom->createElement('a',$name_ar);
									$thisa->setAttribute('href','person.php?p=' . $personid);
									$spousename_ar = $row["spousename_".$pagelanguage];
									
									$thisli = $dom->createElement('li','');
									$thisli->setAttribute('class', $gender);
									$thisli->appendChild($thisa);
									$spousesnames = explode('،',$row["spousename_".$pagelanguage]);
									if(!empty($spousesnames[0]))
									{
										for($i = 0; $i < count($spousesnames); $i++)
										{
											$thisli->appendChild($dom->createElement('br',''));
											$thisli->appendChild(
											$spouse = $dom->createElement('small','‏(' . $spousesnames[$i] . '‏)'));
										}
									}
									if ($gender == 'male')
									{
										$nextfathers->enqueue($personid);
										
										
										$thisul = $dom->createElement('ul','');
										@$thisul->setAttribute('id',$personid);
										$thisli->appendChild($thisul);
									}
									$parentnode->appendChild($thisli);
								}
							}
						}
					} while (!$nextfathers->isempty());

					echo $dom->saveHTML($tree) . '
					';
					$rootindex++;
				}
			}
			?>

			<?php
			for ($i = 1; $i <= $numroots; $i++)
				echo '<div id="chart' . $i . '" class="orgChart"></div>'
			?>
			
			<script>
				jQuery(document).ready(function() {
					/* Custom jQuery for the example */
					$("#show-list").click(function(e){
						e.preventDefault();
						
						$("#list-html").toggle("fast", function(){
							if($(this).is(":visible")){
								$("#show-list").text("Hide underlying list.");
								$(".topbar").fadeTo("fast",0.9);
							}else{
								$("#show-list").text("Show underlying list.");
								$(".topbar").fadeTo("fast",1);                  
							}
						});
					});
					<?php
					for ($i = 1; $i <= $numroots; $i++)
						echo '$("#tree' . $i . '").jOrgChart({
							chartElement : "#chart' . $i . '"
						});
						
						$("#list-html").text($("#tree1").html());
						
						$("#tree' . $i . '").bind("DOMSubtreeModified", function() {
							$("#list-html").text("");
							
							$("#list-html").text($("#tree' . $i . '").html());
							
							prettyPrint();                
						});';
					?>
					$('body').scrollTo($('tr.node-cells > td.node-cell:first > div'),{offset:(-window.innerWidth/2)+($('tr.node-cells > td.node-cell:first > div').width()/2)});
				});
			</script>
			
		</div>
	</div>

</body>

</html>
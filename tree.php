<?php

$highlight = $id;

if (empty($highlight))
	header("Location: ./");

if (!empty($highlight))
{
	$sql = "SELECT p.name_ar as firstname_ar, p.name_en as firstname_en, p.father as father, f.name_ar as lastname_ar, f.name_en as lastname_en, f.id as familyid, p.gentitle as gentitle FROM person p INNER JOIN family f ON p.family = f.id WHERE p.id = '" . $highlight . "'";
	$result2 = mysqli_query($conn, $sql);			
	$row = mysqli_fetch_assoc($result2);
	
	$id = $row["familyid"];
	$highlightfather = $row["father"];
	
	$thisperson = $row["firstname_".$pagelanguage];
	$thisfamily = $row["lastname_".$pagelanguage];
	$gentitle = $row["gentitle"];
	
	if (!empty($gentitle))
	{
		$gentitle = toroman($gentitle);
	$namephrase = $thisperson . ' ' . $thisfamily . ' ' . $gentitle;
	}
	else $namephrase = $thisperson . ' ' . $thisfamily;
	
	$header = 'عائلة ' . $thisfamily;
	
	$ancestors = [];
	$currentid = $highlight;
	
	do {
		$sql = "select id, name_ar, name_en, father from person where id='" . $currentid . "'";
		$result = mysqli_query($conn, $sql);
		$numrows = mysqli_num_rows($result);
		
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
		
			$currentid = $row["father"];
			array_push($ancestors, $currentid);
		}	
	} while ($numrows!=0);
}
else
{
	$sql = "SELECT f.name_ar, f.name_en FROM family f WHERE f.id = '" . $id . "'";
	$result2 = mysqli_query($conn, $sql);			
	$row = mysqli_fetch_assoc($result2);
	
	$thisfamily = $row["name_".$pagelanguage];
	
	$namephrase = $thisfamily;
	
	$header = 'عائلة ' . $thisfamily;

}

$dom = new DOMdocument();  

$sql = "SELECT p.id as rootfather FROM person p JOIN family f ON p.family = f.id WHERE f.id = '" . $id . "' AND p.father IS NULL";
if(!empty($highlight))
	$sql = "SELECT p.id as rootfather FROM person p JOIN family f ON p.family = f.id WHERE f.id = '" . $id . "' AND p.father IS NULL AND p.id ='".$ancestors[count($ancestors)-2]."'";
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
			if (empty($highlight) || in_array($rootfather,$ancestors) || $rootfather==$highlight)
			{
				if ($firsttime)
				{
					$sql = "SELECT p.id as personid, CONCAT_WS(' ',CONCAT_WS(' ',p.name_ar,CONCAT('\"',p.altname_ar,'\"')),p.suffix_ar) as firstname_ar, CONCAT_WS(' ',CONCAT_WS(' ',p.name_en,CONCAT('\"',p.altname_en,'\"')),p.suffix_en) as firstname_en, f.name_ar as lastname_ar, f.name_en as lastname_en, p.gender as gender, GROUP_CONCAT(s.id ORDER BY s.dob SEPARATOR '،') as spouseid, GROUP_CONCAT(CONCAT_WS(' ',CONCAT_WS(' ',CONCAT_WS(' ',s.name_ar,CONCAT('\"',s.altname_ar,'\"')),s.suffix_ar),sf.name_ar) ORDER BY s.dob SEPARATOR '، ') as spousename_ar, s.suffix_ar as spousesuffix_ar, sf.name_ar as spousefamily_ar FROM person p LEFT JOIN `marriage` m ON p.gender = 'male' AND m.husband = p.id OR p.gender = 'female' AND m.wife = p.id LEFT JOIN `person` s ON p.gender = 'male' AND m.wife = s.id OR p.gender = 'female' AND m.husband = s.id LEFT JOIN `family` sf ON s.family = sf.id JOIN family f ON p.family = f.id WHERE f.id = '" . $id . "' AND p.id = '" . $rootfather . "' GROUP BY p.id";
					$father = 'tree' . $rootindex;
				}
				else
				{
					$father = $nextfathers->dequeue();
					$sql = "SELECT p.id as personid, CONCAT_WS(' ',CONCAT_WS(' ',p.name_ar,CONCAT('\"',p.altname_ar,'\"')),p.suffix_ar) as firstname_ar, CONCAT_WS(' ',CONCAT_WS(' ',p.name_en,CONCAT('\"',p.altname_en,'\"')),p.suffix_en) as firstname_en, f.name_ar as lastname_ar, f.name_en as lastname_en, p.gender as gender, GROUP_CONCAT(s.id ORDER BY s.dob SEPARATOR '،') as spouseid, GROUP_CONCAT(CONCAT_WS(' ',CONCAT_WS(' ',CONCAT_WS(' ',s.name_ar,CONCAT('\"',s.altname_ar,'\"')),s.suffix_ar),sf.name_ar) ORDER BY s.dob SEPARATOR '،') as spousename_ar, s.suffix_ar as spousesuffix_ar, sf.name_ar as spousefamily_ar FROM person p LEFT JOIN `marriage` m ON p.gender = 'male' AND m.husband = p.id OR p.gender = 'female' AND m.wife = p.id LEFT JOIN `person` s ON p.gender = 'male' AND m.wife = s.id OR p.gender = 'female' AND m.husband = s.id LEFT JOIN `family` sf ON s.family = sf.id JOIN family f ON p.family = f.id WHERE p.father = '" . $father . "' OR p.mother = '" . $father . "' GROUP BY p.id ORDER BY -p.dob DESC, p.id";
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
							$thisa->setAttribute('href','person.php?p=' . $personid . '#tree');
							$spousesnames = explode('،',$row["spousename_".$pagelanguage]);
							$spousesids = explode('،',$row["spouseid"]);
							
							$thisli = $dom->createElement('li','');
							if ($personid == $highlight)
								$thisli->setAttribute('class', 'selected');
							else $thisli->setAttribute('class', $gender);
							$thisli->appendChild($thisa);
							if(!empty($spousesnames[0]))
							{
								$thisli->appendChild($dom->createElement('br',''));
								$spousesmall = $dom->createElement('small','');
								for($i = 0; $i < count($spousesnames); $i++)
								{
									$spousename_ar = $spousesnames[$i];
									$spouseid = $spousesids[$i];
									$spousea = $dom->createElement('a','');
									$spousea->setAttribute('href', 'person.php?p=' . $spouseid . '#tree');
									//$spousea->setAttribute('class', $gender=='male'? 'female':'male');
									/*$hearticon = $dom->createElement('span','');
									$hearticon->setAttribute('class','glyphicon glyphicon-heart');
									$hearticon->setAttribute('arie-hidden','true');
									$spousea->appendChild($hearticon);*/
									$spousea->appendChild($dom->createTextNode('(' . $spousename_ar. ')'));
									$spousesmall->appendChild($spousea);
									if ($i != count($spousesnames)-1)
										$spousesmall->appendChild($dom->createElement('br','‏'));
									$thisli->appendChild($spousesmall);
								}
							}
							//if ($gender == 'male')
							{
								if (empty($highlight) || in_array($personid,$ancestors))
								{
									$nextfathers->enqueue($personid);
								}
								else if ($personid == $highlight)
								{
									array_push($children,$personid);
									$nextfathers->enqueue($personid);
								}										else if (in_array($father,$children))
								{
									array_push($grandchildren,$personid);
									$nextfathers->enqueue($personid);
								}
								
								$thisul = $dom->createElement('ul','');
								@$thisul->setAttribute('id',$personid);
								$thisli->appendChild($thisul);
							}
							$parentnode->appendChild($thisli);
						}
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
echo '<div id="chart' . $numroots . '" class="orgChart"></div>'
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
		echo '$("#tree' . $numroots . '").jOrgChart({
			chartElement : "#chart' . $numroots . '"
		});
		
		$("#list-html").text($("#tree1").html());
		
		$("#tree' . $numroots . '").bind("DOMSubtreeModified", function() {
			$("#list-html").text("");
			
			$("#list-html").text($("#tree' . $numroots . '").html());
			
			prettyPrint();                
		});';
		?>
		
		$('#tree').scrollTo($('tr.node-cells > td.node-cell:first > div'));
	});
</script>
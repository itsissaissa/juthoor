<?php

$highlight = $id;

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

//draw parents
$fathersql = "SELECT p.id as id, p.name_ar as firstname_ar, p.name_en as firstname_en, p.father as father, f.name_ar as lastname_ar, f.name_en as lastname_en, f.id as familyid, p.gentitle as gentitle FROM person p INNER JOIN family f ON p.family = f.id JOIN person x ON x.father = p.id WHERE x.id = '" . $highlight . "'";
$result = mysqli_query($conn, $fathersql);
$row = mysqli_fetch_assoc($result);

$fatherid = $row["id"];
$fathername_ar = $row["firstname_".$pagelanguage];
$fatherlastname_ar = $row["lastname_".$pagelanguage];
if ($fatherlastname_ar != $thisfamily)
	$fathername_ar .= ' ' . $fatherlastname_ar;

$mothersql = "SELECT p.id as id, p.name_ar as firstname_ar, p.name_en as firstname_en, p.father as father, f.name_ar as lastname_ar, f.name_en as lastname_en, f.id as familyid, p.gentitle as gentitle FROM person p INNER JOIN family f ON p.family = f.id JOIN person x ON x.mother = p.id WHERE x.id = '" . $highlight . "'";
$result = mysqli_query($conn, $mothersql);
$row = mysqli_fetch_assoc($result);

$motherid = $row["id"];
$mothername_ar = $row["firstname_".$pagelanguage];
$motherlastname_ar = $row["lastname_".$pagelanguage];
if ($motherlastname_ar != $thisfamily)
	$mothername_ar .= ' ' . $motherlastname_ar;
	
$fatherfathersql = "SELECT p.id as id, p.name_ar as firstname_ar, p.name_en as firstname_en, p.father as father, f.name_ar as lastname_ar, f.name_en as lastname_en, f.id as familyid, p.gentitle as gentitle FROM person p INNER JOIN family f ON p.family = f.id JOIN person x ON x.father = p.id WHERE x.id = '" . $fatherid . "'";
$result = mysqli_query($conn, $fatherfathersql);
$row = mysqli_fetch_assoc($result);

$fatherfatherid = $row["id"];
$fatherfathername_ar = $row["firstname_".$pagelanguage];
$fatherfatherlastname_ar = $row["lastname_".$pagelanguage];
if ($fatherfatherlastname_ar != $thisfamily)
	$fatherfathername_ar .= ' ' . $fatherfatherlastname_ar;

$fathermothersql = "SELECT p.id as id, p.name_ar as firstname_ar, p.name_en as firstname_en, p.father as father, f.name_ar as lastname_ar, f.name_en as lastname_en, f.id as familyid, p.gentitle as gentitle FROM person p INNER JOIN family f ON p.family = f.id JOIN person x ON x.mother = p.id WHERE x.id = '" . $fatherid . "'";
$result = mysqli_query($conn, $fathermothersql);
$row = mysqli_fetch_assoc($result);

$fathermotherid = $row["id"];
$fathermothername_ar = $row["firstname_".$pagelanguage];
$fathermotherlastname_ar = $row["lastname_".$pagelanguage];
if ($fathermotherlastname_ar != $thisfamily)
	$fathermothername_ar .= ' ' . $fathermotherlastname_ar;
	
$motherfathersql = "SELECT p.id as id, p.name_ar as firstname_ar, p.name_en as firstname_en, p.father as father, f.name_ar as lastname_ar, f.name_en as lastname_en, f.id as familyid, p.gentitle as gentitle FROM person p INNER JOIN family f ON p.family = f.id JOIN person x ON x.father = p.id WHERE x.id = '" . $motherid . "'";
$result = mysqli_query($conn, $motherfathersql);
$row = mysqli_fetch_assoc($result);

$motherfatherid = $row["id"];
$motherfathername_ar = $row["firstname_".$pagelanguage];
$motherfatherlastname_ar = $row["lastname_".$pagelanguage];
if ($motherfatherlastname_ar != $thisfamily)
	$motherfathername_ar .= ' ' . $motherfatherlastname_ar;

$mothermothersql = "SELECT p.id as id, p.name_ar as firstname_ar, p.name_en as firstname_en, p.father as father, f.name_ar as lastname_ar, f.name_en as lastname_en, f.id as familyid, p.gentitle as gentitle FROM person p INNER JOIN family f ON p.family = f.id JOIN person x ON x.mother = p.id WHERE x.id = '" . $motherid . "'";
$result = mysqli_query($conn, $mothermothersql);
$row = mysqli_fetch_assoc($result);

$mothermotherid = $row["id"];
$mothermothername_ar = $row["firstname_".$pagelanguage];
$mothermotherlastname_ar = $row["lastname_".$pagelanguage];
if ($mothermotherlastname_ar != $thisfamily)
	$mothermothername_ar .= ' ' . $mothermotherlastname_ar;

?>
<div class="jOrgChart" style='margin-bottom:-3.6%'>
    <table cellspacing="0" cellpadding="0" border="0">
        <tbody>
            <tr>
                <td class="node-container" colspan="2">
                    <table cellspacing="0" cellpadding="0" border="0">
                        <tbody>
							<tr>
                                <td class="node-container" colspan="2">
                                    <table cellspacing="0" cellpadding="0" border="0">
                                        <tbody>
                                            <tr class="node-cells">
                                                <td class="node-cell" colspan="2">
                                                    <div class="node male">
                                                        <a href="person.php?p=<?php echo $fatherfatherid; ?>#tree"><?php echo $fatherfathername_ar; ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td class="node-container" colspan="2">
                                    <table cellspacing="0" cellpadding="0" border="0">
                                        <tbody>
                                            <tr class="node-cells">
                                                <td class="node-cell" colspan="2">
                                                    <div class="node female">
                                                        <a href="person.php?p=<?php echo $fathermotherid; ?>#tree"><?php echo $fathermothername_ar; ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td class="line left"><div class="line down" style="margin-left:-3.6%"></td>
                                <td class="line right bottom">&nbsp;</td>
                                <td class="line left bottom">&nbsp;</td>
                                <td class="line right"><div class="line down" style="margin-right:-3.6%"></div></td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <div class="line down"></div>
                                </td>
                            </tr>
                            
							<tr class="node-cells">
                                <td class="node-cell" colspan="4">
                                    <div class="node male">
                                        <a href="person.php?p=<?php echo $fatherid; ?>#tree"><?php echo $fathername_ar; ?></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
				<td class="node-container" colspan="2">
				</td>
				<td class="node-container" colspan="2">
				</td>
                <td class="node-container" colspan="2">
                    <table cellspacing="0" cellpadding="0" border="0">
                        <tbody>
							<tr>
                                <td class="node-container" colspan="2">
                                    <table cellspacing="0" cellpadding="0" border="0">
                                        <tbody>
                                            <tr class="node-cells">
                                                <td class="node-cell" colspan="2">
                                                    <div class="node male">
                                                        <a href="person.php?p=<?php echo $motherfatherid; ?>#tree"><?php echo $motherfathername_ar; ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td class="node-container" colspan="2">
                                    <table cellspacing="0" cellpadding="0" border="0">
                                        <tbody>
                                            <tr class="node-cells">
                                                <td class="node-cell" colspan="2">
                                                    <div class="node female">
                                                        <a href="person.php?p=<?php echo $mothermotherid; ?>#tree"><?php echo $mothermothername_ar; ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
							<tr>
                                <td class="line left"><div class="line down" style="margin-left:-3.6%"></td>
                                <td class="line right bottom">&nbsp;</td>
                                <td class="line right bottom">&nbsp;</td>
                                <td class="line left bottom">&nbsp;</td>
                                <td class="line right"><div class="line down" style="margin-right:-3.6%"></td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <div class="line down"></div>
                                </td>
                            </tr>
							<tr class="node-cells">
                                <td class="node-cell" colspan="4">
                                    <div class="node female">
                                        <a href="person.php?p=<?php echo $motherid; ?>#tree"><?php echo $mothername_ar; ?></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="line left"><div class="line down" style="margin-left:-1.5%"></td>
                <td class="line right bottom">&nbsp;</td>
                <td class="line right bottom">&nbsp;</td>
                <td class="line left bottom">&nbsp;</td>
                <td class="line left bottom">&nbsp;</td>
                <td class="line left bottom">&nbsp;</td>
                <td class="line left bottom">&nbsp;</td>
                <td class="line right"><div class="line down" style="margin-right:-1.5%"></td>
            </tr>
        </tbody>
    </table>
</div>
<?php

//draw children
$rootindex = 1;
$numroots = 1;
$dom = new DOMdocument();  

$html = '<ul id="tree' . $rootindex . '" style="display:none">
</ul>';

@$dom->loadHTML($html);    
$xpath = new DOMXPath($dom);    
$tree = $dom->getElementById('tree' . $rootindex);

$rootfather = $fatherid;
$nextfathers = new SplQueue();
$children = [];
$grandchildren = [];

$firsttime = true;

do {
	if (empty($highlight) || in_array($rootfather,$ancestors) || $rootfather==$highlight)
	{
		if ($firsttime)
		{
			$sql = "SELECT p.id as personid, CONCAT_WS(' ',CONCAT_WS(' ',p.name_ar,CONCAT('\"',p.altname_ar,'\"')),p.suffix_ar) as firstname_ar, CONCAT_WS(' ',CONCAT_WS(' ',p.name_en,CONCAT('\"',p.altname_en,'\"')),p.suffix_en) as firstname_en, f.name_ar as lastname_ar, f.name_en as lastname_en, p.gender as gender, GROUP_CONCAT(s.id ORDER BY s.dob SEPARATOR '،') as spouseid, GROUP_CONCAT(CONCAT_WS(' ',CONCAT_WS(' ',CONCAT_WS(' ',s.name_ar,CONCAT('\"',s.altname_ar,'\"')),s.suffix_ar),sf.name_ar) ORDER BY s.dob SEPARATOR '، ') as spousename_ar, s.suffix_ar as spousesuffix_ar, sf.name_ar as spousefamily_ar, GROUP_CONCAT(CONCAT_WS(' ',CONCAT_WS(' ',CONCAT_WS(' ',s.name_en,CONCAT('\"',s.altname_en,'\"')),s.suffix_en),sf.name_en) ORDER BY s.dob SEPARATOR '،') as spousename_en, s.suffix_en as spousesuffix_en, sf.name_en as spousefamily_en FROM person p LEFT JOIN `marriage` m ON p.gender = 'male' AND m.husband = p.id OR p.gender = 'female' AND m.wife = p.id LEFT JOIN `person` s ON p.gender = 'male' AND m.wife = s.id OR p.gender = 'female' AND m.husband = s.id LEFT JOIN `family` sf ON s.family = sf.id JOIN family f ON p.family = f.id WHERE f.id = '" . $id . "' AND p.id = '" . $rootfather . "' GROUP BY p.id";
			$father = 'tree' . $rootindex;
		}
		else
		{
			$father = $nextfathers->dequeue();
			$sql = "SELECT p.id as personid, CONCAT_WS(' ',CONCAT_WS(' ',p.name_ar,CONCAT('\"',p.altname_ar,'\"')),p.suffix_ar) as firstname_ar, CONCAT_WS(' ',CONCAT_WS(' ',p.name_en,CONCAT('\"',p.altname_en,'\"')),p.suffix_en) as firstname_en, f.name_ar as lastname_ar, f.name_en as lastname_en, p.gender as gender, GROUP_CONCAT(s.id ORDER BY s.dob SEPARATOR '،') as spouseid, GROUP_CONCAT(CONCAT_WS(' ',CONCAT_WS(' ',CONCAT_WS(' ',s.name_ar,CONCAT('\"',s.altname_ar,'\"')),s.suffix_ar),sf.name_ar) ORDER BY s.dob SEPARATOR '،') as spousename_ar, s.suffix_ar as spousesuffix_ar, sf.name_ar as spousefamily_ar, GROUP_CONCAT(CONCAT_WS(' ',CONCAT_WS(' ',CONCAT_WS(' ',s.name_en,CONCAT('\"',s.altname_en,'\"')),s.suffix_en),sf.name_en) ORDER BY s.dob SEPARATOR '،') as spousename_en, s.suffix_en as spousesuffix_en, sf.name_en as spousefamily_en FROM person p LEFT JOIN `marriage` m ON p.gender = 'male' AND m.husband = p.id OR p.gender = 'female' AND m.wife = p.id LEFT JOIN `person` s ON p.gender = 'male' AND m.wife = s.id OR p.gender = 'female' AND m.husband = s.id LEFT JOIN `family` sf ON s.family = sf.id JOIN family f ON p.family = f.id WHERE p.father = '" . $father . "' OR p.mother = '" . $father . "' GROUP BY p.id ORDER BY -p.dob DESC, p.id";
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
					
					if ($personid == $fatherid)
						$thisli->setAttribute('class', 'hidden');

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
						}
						else if (in_array($father,$children))
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
?>

<?php
echo '<div id="chart' . $numroots . '" class="orgChart"></div>';
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
		
		echo '$("#tree0").jOrgChart({
			chartElement : "#chart0"
		});
		
		$("#list-html").text($("#tree-1").html());
		
		$("#tree0").bind("DOMSubtreeModified", function() {
			$("#list-html").text("");
			
			$("#list-html").text($("#tree0").html());
			
			prettyPrint();                
		});';
		?>
		
		$('#tree').scrollTo($('tr.node-cells > td.node-cell:first > div'));
	});
</script>
<?php
require_once "ServerParameters.php";

$columns = array();
$priorities = array();

foreach($_POST as $key => $value) {
	if (startsWith($key, "q")) {
		$columns[substr($key, -1)] = $value;
	} else if (startsWith($key, "care")) {
		$priorities[substr($key, -1)] = $value;
	} else {
		// invalid parameter, ignore
	}
}

print ServerParameters::header();

// Create connection
$conn = ServerParameters::mysqli();
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}
  
$champion_id = 0;
$url = "/img/urf.png";
$name = "Urf";
$title = "The manatee";
$explanation = "You didn't answer any questions properly. You got urfed.";
$totalPriority = 0;
$image = NULL;
$address = NULL;

foreach($columns as $i => $value) {
	$totalPriority = $totalPriority + $priorities[$i];
}

if (!empty($columns) && $totalPriority > 0) {
	$questions = ServerParameters::getQuestions();
	$sql = "SELECT * FROM max_average_stat";
	$results = $conn->query($sql) or die ('Error getting max stats.');
	$max_stats = $results->fetch_assoc();
	
	$orderby = "";
	$explanation = "";
	foreach($columns as $i => $value) {
		$question = $questions[$i];
		$answer = $question['answers'][$value];
		$column = $answer['column'];
		
		$column_sign = substr($column, 0, 1);
		$column_name = substr($column, 1);
		$orderby = $orderby . "(" . $column_sign . $column_name . "/" . $max_stats[$column_name] . "*" . $priorities[$i] . ")" . "+";
		
		$explanation = $explanation . "<p>When asked \"$question[name]\" you answered: \"$answer[name]\" and used priority: $priorities[$i].</p>";
	}
	
	$orderby = substr($orderby, 0, -1);
	
	$sql = "SELECT champion_id, address, champion_version, name, image, title 
	FROM average_stat a
	JOIN cdn cdn ON a.champion_id = cdn.champion_id
	JOIN champion_data cd ON a.champion_id = cd.champion_id
	ORDER BY ($orderby) DESC LIMIT 1";
		
	$results = $conn->query($sql) or die ('Error getting result');
	$row = $results->fetch_assoc();

	$champion_id = $row['champion_id'];
	$address = $row['address'];
	$champion_version = $row['champion_version'];
	$image = $row['image'];
	$name = $row['name'];
	$title = $row['title'];
	
	$url = $address . "/" . $champion_version . "/img/champion/" . $image;
}

print "<h3>Your champion has been picked</h3>";

$gameInfoUrl = "<a href=\"http://gameinfo.na.leagueoflegends.com/en/game-info/champions/";
$namelower = NULL;
if ($image != NULL) {
	$namelower = strtolower(substr($image, 0, -4));
	print "<a href=\"" . $gameInfoUrl . $namelower . "/\">";
}

print "<div id=\"result\">";
print "<img src=\"$url\" alt=\"$name, $title\" title=\"$name, $title\"/>";
print "<h2>$name</h2></a>";
if ($namelower != NULL) {
	print "</a>";
}
print "<p id=\"explanation\">$explanation</p>";
print "</div>";

$client_ip = $_SERVER['REMOTE_ADDR'];

if ($image != NULL && $address != NULL) {
	$sql = "SELECT DISTINCT p.champion_id, p.pick_count, p.latest_picked_date, p.client_ip, s.image, s.name, s.title
	FROM picked_champion p
	JOIN champion_stat s ON p.champion_id = s.champion_id
	ORDER BY latest_picked_date DESC LIMIT 5";
	$results = $conn->query($sql) or die ('Error getting recent picks: ' . $sql);
	
	print "<div id=\"recent\">";
	print "<h4>Recent URF picker results</h4>";
	
	while($row = $results->fetch_assoc()) {
		$image = $row['image'];
		$name = $row['name'];
		$title = $row['title'];
	
		$url = $address . "/" . $champion_version . "/img/champion/" . $image;
		$namelower = strtolower(substr($image, 0, -4));
		print "<a href=\"" . $gameInfoUrl . $namelower . "/\">";
		print "<img src=\"$url\" alt=\"$name, $title\" title=\"$name, $title\"/></a>";
	}
	print "</div>";
	
	$sql = "insert into picked_champion (champion_id, pick_count, latest_picked_date, client_ip) VALUES ($champion_id, 1, now(), '$client_ip')
			ON DUPLICATE KEY UPDATE
			latest_picked_date=now(), pick_count=pick_count+1, client_ip='$client_ip'";		
	$results = $conn->query($sql) or die ('Error inserting pick:' . $sql);
}

print "<div id=\"footer\"></div>";
print "</body>";
print "</html>";

$conn->close();

?>
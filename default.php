<?php

require_once "ServerParameters.php";

$questions = ServerParameters::getQuestions();

print ServerParameters::header();

print "<form Method=\"POST\" ACTION\"result.php\">";
		
$i = 0;
foreach($questions as $n => $question) {
	$j = 0;
	$name = $question['name'];
	$answers = $question['answers'];

	print "<div><fieldset><legend>$name</legend>";
	
	foreach($answers as $m => $answer) {
		$column = $answer['column'];
		$title = $answer['title'];
		$name = $answer['name'];
		print "<span><input type=\"radio\" id=\"a$i$j\" name=\"q$i\" value=\"$j\"/><label for=\"a$i$j\">$name</label><div>$title</div></span>";
		$j = $j+1;
	}
	
	print "<p><span title\"A high priority means that this question will take precedence over other questions when finding a champion.\">Priority <input type=\"range\" min=\"0\" max=\"100\" name=\"care$i\"/></span>";
	print "</fieldset></div>";
	$i = $i + 1;
}

print "<p><button type=\"submit\" class=\"myButton\">Urf me!</button></p>";

print "</form>"; 
print "</body>"
print "</html>";

?>
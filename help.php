<?php

require_once "ServerParameters.php";

$questions = ServerParameters::getQuestions();

print ServerParameters::header();

print "<div id=\"help\">";
print "<h3>What is URF Champion Picker?</h3>";
print "<p class=\"helpText\">URF Champion Picker finds a compatible champion for playing URF in the game League of Legends based on a series of questions.</p>";
print "<h3>How does it work?</h3>";
print "<p class\"helpText\">Over 2000 Random URF match statistics (and some static spell data) have been fetched by using Riot's API. These statistics are matched against questions and priorities to find a closest match.</p>";
print "<h3>What are priorities?</h3>";
print "<p class=\"helpText\">They are numbers between 0 and 100, used to sort results on importance. A high priority means a higher importance.</p>";
print "<h3>Can I contact you about questions or bugs?</h3>";
print "<p class=\"helpText\">Yes, email: urf.champion.picker 'at' gmail.com</p>";
print "</div>";
print "<div class=\"footer\"><h3>Legal</h3>URF Champion Picker isn't endorsed by Riot Games and doesn't reflect the views or opinions of Riot Games or anyone officially involved in producing or managing League of Legends. League of Legends and Riot Games are trademarks or registered trademarks of Riot Games, Inc. League of Legends © Riot Games, Inc.</div>";
print "</body></html>";

?>
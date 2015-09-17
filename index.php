<!DOCTYPE html>
<?php
	include 'GetFeed.php';
	$feedUrl = 'http://sportsfeeds.bovada.lv/basic/NFL.xml';
	$currentTime = time();
	$doc = getXMLDoc($feedUrl);
	$dateArray = currentDate($doc, $currentTime);
	$gameArray = currentGames($dateArray, $currentTime);
?>
<head>
<link rel="stylesheet" type="text/css" href="betStyle.css">
</head>
<body>
	<header></header>
	<div id="mainBettingTable">
		<?php foreach ($gameArray as $oneGame) {  ?>
		<div class="matchup">
			<div class="DateTime">
				<div class="Time">
					<?php print $oneGame->Time['TTEXT']; ?>
				</div>
				<div class="Date">
					<?php 
						$parent_div = $oneGame->xpath("parent::*");
						$Date = $parent_div[0]['DTEXT'];
						print $Date;
					?>
				</div>	
			</div>
			<div class="clear"></div>
			<div class="team1">	
				<div class="name"><?php print $oneGame->Competitor[0]['NAME']; ?></div>
				<div class="spread"><?php if(isset($oneGame->Competitor[0]->Line[0]->Choice['NUMBER'])) print $oneGame->Competitor[0]->Line[0]->Choice['NUMBER']; ?></div>
				<div class="moneyline"><?php if(isset($oneGame->Competitor[0]->Line[1]->Choice['VALUE'])) print $oneGame->Competitor[0]->Line[1]->Choice['VALUE']; ?></div>
			</div>
			<div class="team2">
			 	<div class="name"><?php print $oneGame->Competitor[1]['NAME']; ?></div>
			 	<div class="spread"><?php if(isset($oneGame->Competitor[1]->Line[0]->Choice['NUMBER'])) print $oneGame->Competitor[1]->Line[0]->Choice['NUMBER']; ?></div>
				<div class="moneyline"><?php if(isset($oneGame->Competitor[1]->Line[1]->Choice['VALUE'])) print $oneGame->Competitor[1]->Line[1]->Choice['VALUE']; ?></div>
			</div>
		</div>
		<div class="clear"></clear>
		<?php } ?>
	</div>
	<footer></footer>
</body>

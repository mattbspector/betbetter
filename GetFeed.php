<?php
//Set to Michigan TimeZone
date_default_timezone_set('America/Detroit');

//Grab the XML Doc
$feedUrl = 'http://sportsfeeds.bovada.lv/basic/NFL.xml';
$httpChannel = curl_init();
curl_setopt($httpChannel, CURLOPT_URL, $feedUrl);
curl_setopt($httpChannel, CURLOPT_RETURNTRANSFER, true);
curl_setopt($httpChannel, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($httpChannel, CURLOPT_HEADER, 0);
$initialFeed = curl_exec($httpChannel);
curl_close($httpChannel);
$doc = new SimpleXmlElement($initialFeed, LIBXML_NOCDATA);
//End grabbing XML Doc (Now in object $doc)


//Get current date/week
$dateArray = getdate();
$dateString = substr($dateArray['weekday'],0,3) . ', ' .  substr($dateArray['month'],0,3) . ' ' . $dateArray['mday'] . ', ' . substr($dateArray['year'],2,3);
$currentTime = time();

$currWeek = '';
$getNextThree = false;
$dateArray = array();



//BUG (WORKS ONLY IF BEFORE THURSDAY, IF AFTER THURSDAY OR AFTER GAME STARTS IT WILL BREAK)
foreach ($doc->EventType->Date as $Date) {

	$Timestamp = intval(substr($Date['TS'],0,10));
	if($currentTime < $Timestamp || $currentTime < $Timestamp + 86400){
		$getNextThree = true;
	}
	if($getNextThree){
		if(sizeof($dateArray) < 3){
			$dateArray[] = $Date;
		}
		else{
			break;
		}
	}
}
//ENDBUG

//PRINT OUT DATE AND GAMES ON THAT DATE WITH A LINE
foreach ($dateArray as $Event) {
	echo $Event['DTEXT'] . '<br/>';
	foreach ($Event as $oneGame) {
		$Timestamp = intval(substr($oneGame->Time['TS'],0,10));
		if($currentTime > $Timestamp){
			continue;
		}
		else{
					$line = $oneGame->Competitor[0]->Line->Choice['NUMBER'];
			if(strpos($line, '½')){
				$pos = strpos($line, '½');
				$line = substr($line, 0, $pos);
				$line = $line . ".5";
			}	
			if($line > 0){
				$line = '+' . $line;
			}
			$line = ' (' . $line . ') ';
			echo $oneGame->Competitor[0]['NAME'] .$line . ' vs. ';
			echo $oneGame->Competitor[1]['NAME'] . ' <br/> ';
		}
	}

	echo "<br>";
}
//END PRINT

?>
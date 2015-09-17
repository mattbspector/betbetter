<?php
//Set to Michigan TimeZone
date_default_timezone_set('America/Detroit');

//Grab the XML Doc
function getXMLDoc($feedUrl){

	$httpChannel = curl_init();
	curl_setopt($httpChannel, CURLOPT_URL, $feedUrl);
	curl_setopt($httpChannel, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($httpChannel, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($httpChannel, CURLOPT_HEADER, 0);
	$initialFeed = curl_exec($httpChannel);
	curl_close($httpChannel);
	$doc = new SimpleXmlElement($initialFeed, LIBXML_NOCDATA);

	return $doc;
}

//Get current date/week
function currentDate($doc, $currentTime){

	$dateArray = getdate();
	$dateString = substr($dateArray['weekday'],0,3) . ', ' .  substr($dateArray['month'],0,3) . ' ' . $dateArray['mday'] . ', ' . substr($dateArray['year'],2,3);
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

	return $dateArray;
}

function currentGames($dateArray, $currentTime){

	$gameArray = array();
	foreach ($dateArray as $Event) {
		foreach ($Event as $oneGame) {
			$Timestamp = intval(substr($oneGame->Time['TS'],0,10));
			if($currentTime > $Timestamp){
				continue;
			}
			else{
				foreach ($oneGame->Competitor as $Competitor) {
					$line = $Competitor->Line->Choice['NUMBER'];
					if(strpos($line, '½')){
						$pos = strpos($line, '½');
						$line = substr($line, 0, $pos);
						$line = $line . ".5";
						$Competitor->Line->Choice['NUMBER'] = $line;
					}
					if(intval($line) > 0){
						$Competitor->Line->Choice['NUMBER'] = "+" . $Competitor->Line->Choice['NUMBER'];
					}
				}
				$gameArray[] = $oneGame;
			}
		}
	}

	return $gameArray;
}
//PRINT OUT DATE AND GAMES ON THAT DATE WITH A LINE
//END PRINT
?>
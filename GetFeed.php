<?php
// This fetches the initial feed from the Pinnacle Sports API
$feedUrl = 'http://sportsfeeds.bovada.lv/basic/NFL.xml';
// Set up a CURL channel.
$httpChannel = curl_init();
// Prime the channel
curl_setopt($httpChannel, CURLOPT_URL, $feedUrl);
curl_setopt($httpChannel, CURLOPT_RETURNTRANSFER, true);
curl_setopt($httpChannel, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($httpChannel, CURLOPT_HEADER, 0);


$initialFeed = curl_exec($httpChannel);
curl_close($httpChannel);
$doc = new SimpleXmlElement($initialFeed, LIBXML_NOCDATA);

foreach ($doc->EventType->Date[3]->Event as $Event) {
	$line = $Event->Competitor[0]->Line->Choice['NUMBER'];
	$line = intval($line);
	if($line > 0){
		$line = '+' . $line;
	}

	$line = ' (' . $line . ') ';
	echo $Event->Competitor[0]['NAME'] .$line . ' vs. ';
	echo $Event->Competitor[1]['NAME'] . ' <br/> ';

}
?>
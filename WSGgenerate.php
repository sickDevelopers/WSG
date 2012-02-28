<?php

	include "WSG.php";

	$wsg = new WSG();
	//$wsg->getWebsite('http://www.chainsport.it');
	//echo 'Log generato!<br/>';
	if(isset($_POST['websiteUrl'])) {
		$websiteUrl = $_POST['websiteUrl'];
		
		$wsg->getWebsite($websiteUrl);
		//$wsg->getXmlSitemap();
		
	} 
	
	if(isset($_POST['sitemap'])) {
		
		$xmlData = $wsg->arrayToXML( $_POST['sitemap'] );
		$wsg->writeXMLSitemap($xmlData);
		echo $xmlData;
	}
	

	//echo $wsg->scanLogToXML();

?>
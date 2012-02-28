<?php
	
	require "Serializer.php";

	class WSG {
	
		private $logName = './output.txt';
		private $host = '';
	
		public function __construct() {
			
		}
		
		
		public function getWebsite($host) {
			$host = preg_match('/http:\/\//i', $host) ? $host : 'http://'.$host;
			$this->host = $host;
			exec('wget --spider -r -l inf --no-parent  -o output.txt ' . $this->host);
		}
		
		
		public function scanLogToXML() {
			
			$sitemap = array();
			
			$content = file_get_contents($this->logName);
			$lines = explode("\n", $content);
			
			$options = array( "addDecl" => true,
			   "defaultTagName" => "url",
			   "indent" => "    ",
			   "rootName" => "employees");
			
			$serializer = new XML_Serializer($options);
			$xml = array();
			
			for($i=0; $i<count($lines); $i++){
				$lineContent = $lines[$i];
				if (preg_match('/^(--[0 9]*)/i', $lineContent)) { // linea della data e url
					
					$dateAndUrl = explode('-- ', $lineContent); // ottengo l'url
					$url = $dateAndUrl[1];
					$hostAndPath = explode($this->host, $url);
					$path = $hostAndPath[1];	
					
					//controllo se è già stato inserito il nodo
					$saved = false;
					foreach($xml as $node) {
						if ($node['loc'] == $url && !$saved) {
							$saved = true;
						}
					}
					
					if(!$saved) {
						
						// Creazione dell'oggetto pagina
						$page = array(
								"loc" => $url,
								"lastmod" => date("Y-m-d", time()),
								"changefreq" => 'weekly',
								"priority" => '0.5'
						);
						
						array_push($xml, $page);
					}
					//echo "AGGIUNTO :  " .$path . "<br>";
				}
				
			}
			
			$serializer->setOption("addDecl", true);
			$serializer->setOption('indent', '    ');
			$serializer->setOption('rootName', 'urlset');
			
			$finalXml = $serializer->serialize($xml);
			return $serializer->getSerializedData();	
			
		}
		
		
		public function arrayToXML($plainArray) {
			
			$sitemap = array();
			
			$options = array( "addDecl" => true,
			   "defaultTagName" => "url",
			   "indent" => "    ",
			   "rootName" => "employees");
			
			$serializer = new XML_Serializer($options);
			$xml = array();
			
			foreach($plainArray as $key => $value) {
				
				$page = array(
						"loc" => $key,
						"lastmod" => date("Y-m-d", time()),
						"changefreq" => 'weekly',
						"priority" => '0.5'
				);
				
				array_push($xml, $page);
			}
			
			
			$serializer->setOption("addDecl", true);
			$serializer->setOption('indent', '    ');
			$serializer->setOption('rootName', 'urlset');
			
			$finalXml = $serializer->serialize($xml);
			return $serializer->getSerializedData();
			
		}
		
		
		public function writeXMLSitemap($xmlData) {
			// open file
			if (!$handle = fopen('sitemap.xml', 'w')) 
			{ 
			 print "Cannot open file";
			 exit;
			}

			// write XML to file
			if (!fwrite($handle, $xmlData)) 
			{
			 print "Cannot write to file";
			 exit;
			}

			// close file    
			fclose($handle);
			//echo "Sitemap scritta correttamente";
		}
		
	}


?>
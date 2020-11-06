<?php
define('red',"\e[31m");
define("green","\e[32m");
error_reporting(0);
	class Site_Scan{
		public function cURL($web){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $web);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			$exe = curl_exec($curl);
			curl_close($curl);
			return $exe;
		}
		public function Save($save,$name){
			$result = fopen($name, "a+");
			fwrite($result,"$save\n");
			fclose($result);
		}
		public function Scan($site){
			if (!preg_match('#^http(s)?://#',$site)) {
				$site = "http://".$site;
			}
			else {
				$site = $site;
			}
			$web = $this->cURL($site);
			for ($i=0; $i < count($web); $i++) { 
				if (preg_match("/html|head|body/", $web)) {
					echo green."[ Live ] > $site\n";
					$this->Save($site,"live.txt");
				}
				else if (preg_match("/domain has expired|Domain Expired|Under Construction/", $web)) {
					echo "[ Expired ] > $site\n";
					$this->Save($site,"expired.txt");
				}
				else {
					echo red."[ Not Live ] > $site\n";
				}
			}	
		}
		public function Mass($list){
			$web = file_get_contents($list);
			if (!file_exists($list)) die("File List ".$list."Not Found");
			$exp = explode("\n", $web);
			echo "Total Site > ".count($exp)."\n";
			foreach ($exp as $key) {
				$this->Scan($key);
			}
		}
		public function headerr(){
			echo "\n\t1. Mass Scan\n\t2.Not Mass Scann\n\n";
		}
		public function Chose($ch){
			switch ($ch) {
				case '1':
					echo "\tYour List (yourfile.txt) > ";
					$txt = trim(fgets(STDIN));
					$this->Mass($txt);
					break;
				case '2':
					echo "\tYour Site (example.com) > ";
					$site = trim(fgets(STDIN));
					$this->Scan($site);
					break;
				default:
					echo "CHOSE 1 OR 2 YET!!";
					break;
			}
		}
	}
	$scan = new Site_Scan();
	if (!isset($argv[1])) {
		$scan->headerr();
		echo "Use : php live.php chose 1 or 2";
		exit(1);
	}
	else {
		$link = $argv[1];
	}
	$scan->Chose($link);
?>

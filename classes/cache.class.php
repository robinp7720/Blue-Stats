<?php
class cache{
	private $config;
	private $appPath;

	public function __construct($mysqli,$appPath)
	{
		$this->config = new config($mysqli,"cache");
		$this->config->setDefault("expiry-time", 36000);
		$this->appPath = $appPath;
	}

	public function cache($content,$name)
	{
		$file_name = md5($name).'.html';
		$time = time();

		$toSave = array("time"=>$time,"content"=>$content);

		file_put_contents($this->appPath."/cache/$file_name", json_encode($toSave));
	}

	public function reCache($name)
	{	
		$file_name = md5($name).'.html';
		if (file_exists($this->appPath."/cache/$file_name")){
			$file = file_get_contents($this->appPath."/cache/$file_name");
			$cache = json_decode($file,true);
			if ($cache["time"]<time()-$this->config->get("expiry-time")){
				return true;
			}else{
				return false;
			}
		}else{
			return true;
		}
	}

	public function getCache($name)
	{
		$file_name = md5($name).'.html';
		return json_decode(file_get_contents($this->appPath."/cache/$file_name"),true)["content"];
	}
}
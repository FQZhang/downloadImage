<?php
  define('URL', 'https://specialistsdirectory.com.au');
	
	class getImage {	

		private $file;
		private $path;
		private $type = ['photo', 'logo', 'gmap'];

		function __construct($input){
			$pathInfo = pathinfo($input);
			$this->file = $pathInfo['basename'];
			$this->path = $pathInfo['dirname'];
		}

		function createDir () {
			foreach ($this->type as $value) {
				if(!file_exists($this->path.'/'.$value))
					@mkdir($this->path.'/hsd_'.$value, 0777);
			}
		}

		function download () {
			$array = $fields = array(); $i = 0;
			$handle = @fopen($this->path.'/'.$this->file, "r");
			if ($handle) {
		    while (($row = fgetcsv($handle, 4096)) !== false) {
		        if (empty($fields)) {
		            $fields = $row;
		            continue;
		        }
		        foreach ($row as $k=>$value) {
		            $array[$i][$fields[$k]] = $value;
		        }

		        foreach ($this->type as $value) {
		        	if($value == 'gmap' && !empty($array[$i]['gmap'])){
		        		$content = file_get_contents($array[$i][$value]);
								file_put_contents($this->path.'/hsd_'.$value.'/'.$array[$i]['locId'].'.png', $content);
		        	}else{
		        		if(!empty($array[$i][$value])){
			        		$content = file_get_contents(URL.'/'.$array[$i][$value]);
									$imgType = explode(';', explode('/',$http_response_header[24])[1])[0];
									file_put_contents($this->path.'/hsd_'.$value.'/'.$array[$i][$value].'.'.$imgType, $content);
								}
		        	}
		        }
		        $i++;
		    }
		    if (!feof($handle)) {
		        echo "Error: unexpected fgetcsv() fail\n";
		    }
    		fclose($handle);
			}
		}
	}

	$downloadImg = new getImage($argv[1]);
	$downloadImg->createDir();
	$downloadImg->download();
?>

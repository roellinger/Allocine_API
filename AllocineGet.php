<?php

namespace App\Http\Factory;

include(app_path() . '\Api\api-allocine-helper.php');

class AllocineGet{

	private static $tab = array(

		'title' => '',
		'productionYear' => '',
		'release' => array('releaseDate' => ''),
		'synopsisShort' => '',
		'castingShort' => array('directors' => '', 'actors' => '')
	);

	private static function createTab($data, $i){
		$tab = array();
		$tabdata = array();

    	try{

	    	foreach (self::$tab as $key => $value) {
	        
		        if(is_array(self::$tab[$key])){

		        	foreach (self::$tab[$key] as $k => $v) {

		        		if(is_object($data->$key)){
		        			if($data->$key->$k){
		        					self::$tab[$key][$k] = $data->$key->$k;
		        			}
		        		}
		
		        	}

		        }else{

		        	if($data->$key){
		        			self::$tab[$key] = $data->$key;
		        	}
		        }

	        }
	        		
		    $tabdata[$i] = self::$tab;
		    $tab = self::array_flatten($tabdata[$i]);
		    return $tab;

    	}catch(\ErrorException $error ){

	        // En cas d'erreur
	        echo "Erreur n°", $error->getCode(), ": ", $error->getMessage(), PHP_EOL;
    	}

	} 

	public static function doublonTab($arr){
		
		if(!is_array($arr)){
			echo 'ce n\'est pas un tableau';
			return false;
		}		
		$arr = array_map('implode', $arr, array_fill(0, count($arr), '|'));
		 
		$arr = array_unique($arr);
		 
		$arr = array_map('explode', array_fill(0, count($arr), '|'), $arr);
		 
		return $arr;
	}

	public static function array_flatten($array) {
		//echo $i;
	  	if (!is_array($array)) { 
	  		
	    	return FALSE; 
	  	}

	  	$result = array(); 

	  	foreach ($array as $key => $value) { 

		    if (is_array($value)) { 

		      $result = array_merge($result, self::array_flatten($value)); 
		    } 

		    else { 

		      $result[$key] = $value; 

		    }
	  	}

	  	return $result; 
	}

	public function searchMovie($q, $list=10){
		$p=0;
		$codeList = array();
		$helper = new AlloHelper;
    	$profile = 'small';
    	$r = array();
		$movie = $helper->search($q, 1, $list, false, array('movie'));
		//var_dump($movie['movie'][4]['code']);

		if(!$movie['results']['movie']){
			echo 'film introuvable';
			return false;
		}

		for($i=0;$i!=count($movie['movie']);$i++){
			//var_dump($movie['movie'][$i]);

			if(!$movie['movie']){
				echo 'Le film est introuvable';
				return false;
			}else{
				$code = $movie['movie'][$i]['code'];
				if($code){
					array_push($codeList, $code);
				}	
			}
		}

		//var_dump($codeList);

		for($a=0;$a!=count($codeList);$a++){
			$movie = $helper->movie($codeList[$a]);
			//var_dump($movie);
			array_push($r, self::createTab($movie, $p++));
		}

		$t = self::doublonTab($r);

		return $t;
	}

}
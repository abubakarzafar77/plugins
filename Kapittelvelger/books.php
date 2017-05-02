<?php
@include_once("../../../wp-config.php");
require_once 'sinus1t.php';
require_once 'matematikk1t.php';
require_once 'matematikk1p.php';
require_once 'sinus1p.php';

require_once 'sinusR1.php';
require_once 'sinusR2.php';
require_once 'matematikkR1.php';
require_once 'matematikkR2.php';
require_once 'sinusS1.php';

switch($_POST['book']){
	
	case "matematikk1t":
		$keys = array_keys($matematikk1t);
		break;
		
	case "sinus1t":
		$keys = array_keys($sinus1t);
		break;		
	
	case "matematikk1p":
		$keys = array_keys($matematikk1p);
		break;

	case "sinus1p":
		$keys = array_keys($sinus1p);
		break;
    
    case "sinusR1":
		$keys = array_keys($sinusR1);
		break;
    case "matematikkR1":
		$keys = array_keys($matematikkR1);
		break;
    
    case "sinusR2":
		$keys = array_keys($sinusR2);
		break;
    case "matematikkR2":
		$keys = array_keys($matematikkR2);
		break;
	case "sinusS1":
		$keys = array_keys($sinusS1);
		break;	
}

switch($_POST['pensum']){
	case "matematikk1t":	
		
		if(isset($_POST['subchapter'])){
			$keys = $matematikk1t[$_POST['chapter']][$_POST['subchapter']];
		
		}else{
			echo 'chapter:'.$_POST['chapter'].' - mattematikk1t: '.$matematikk1t[$_POST['chapter']];
			$keys = array_keys( $matematikk1t[$_POST['chapter']]);
			
						
		}
		break;
		
	case "sinus1t":
	
		if(isset($_POST['subchapter'])){
			$keys = $sinus1t[$_POST['chapter']][$_POST['subchapter']];
		}else{
			$keys = array_keys( $sinus1t[ $_POST['chapter']]);
		}
		break;	
	
	case "matematikk1p":
	
		if(isset($_POST['subchapter'])){
			$keys = $matematikk1p[$_POST['chapter']][$_POST['subchapter']];
		}else{
			$keys = array_keys( $matematikk1p[ $_POST['chapter']]);
		}
		break;
	case "sinus1p":
	
		if(isset($_POST['subchapter'])){
			$keys = $sinus1p[$_POST['chapter']][$_POST['subchapter']];
		}else{
			$keys = array_keys( $sinus1p[ $_POST['chapter']]);
		}
		break;
        
    case "sinusR1":
		if(isset($_POST['subchapter'])){
			$keys = $sinusR1[$_POST['chapter']][$_POST['subchapter']];
		}else{
			$keys = array_keys( $sinusR1[ $_POST['chapter']]);
		}
		break;
    case "matematikkR1":
		if(isset($_POST['subchapter'])){
			$keys = $matematikkR1[$_POST['chapter']][$_POST['subchapter']];
		}else{
			$keys = array_keys( $matematikkR1[ $_POST['chapter']]);
		}
		break;
        
    case "sinusR2":
		if(isset($_POST['subchapter'])){
			$keys = $sinusR2[$_POST['chapter']][$_POST['subchapter']];
		}else{
			$keys = array_keys( $sinusR2[ $_POST['chapter']]);
		}
		break;
    case "matematikkR2":
		if(isset($_POST['subchapter'])){
			$keys = $matematikkR2[$_POST['chapter']][$_POST['subchapter']];
		}else{
			$keys = array_keys( $matematikkR2[ $_POST['chapter']]);
		}
		break;
   case "sinusS1":
		if(isset($_POST['subchapter'])){
			$keys = $sinusS1[$_POST['chapter']][$_POST['subchapter']];
		}else{
			$keys = array_keys( $sinusS1[ $_POST['chapter']]);
		}
		break;	

}

if(isset($_POST['subchapter'])){
	printLinks($keys);
}else{
	printOptions($keys);
}

function printOptions($keys){
	echo '<option>Kapittel</option>';
	foreach($keys as $key){
		echo '<option value="'.$key.'">';
		echo $key; 
		echo '</option>';
	} 
}

function printLinks($keys){
	foreach($keys as $key){
		$link = '#';
		if(isset($key['id']) && $key['id'] > 0){
			$link = get_permalink($key['id']);
		}elseif(isset($key['url'])){
			$link = $key['url'];
		}
		echo '<a class="veiviser" target="_blank" href="'.$link.'">'.$key['title'].'</a>';
	}
}

?>
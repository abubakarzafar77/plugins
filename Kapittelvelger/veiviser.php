

<?php

$matteboker = array("Matematikk 1T", "Sinus 1T");


require_once 'mattematikk1t.php';
require_once 'sinus1t.php';


echo '
		';

if($_POST["book"] == "Matematikk 1T"){
	$mattebok = $mattematikk1t;
}else if($_POST["book"] == "Sinus 1T"){
	$mattebok = $sinus1t;
}
		

switch($_POST['todo']){
	
	
	case "showLink":
	
		for($a = 0; $a < count($mattebok[$_POST['chapter']][$_POST['subchapter']]); $a++){
			$url = $mattebok[$_POST['chapter']][$_POST['subchapter']][$a]['url'];
			$title = $mattebok[$_POST['chapter']][$_POST['subchapter']][$a]['title'];
			echo '<div class="link">
					
					<img src="play.png">
					<p class="link_box">
						<a href="'.$url.'" target="_top">'.$title.'</a>	
					</p>
				</div>';
			}
		break;
			
	case "Kapittel":
		
	
		echo '
			<select onchange="getSubChaptersFromBook(this.options[this.selectedIndex].value, \''.$_POST['book'].'\')">
				<option>Kapittel</option>';
				
			$keys = array_keys($mattebok);
	
			foreach ($keys as $key){
				echo '<option value="'.$key.'">';
				echo $key;//[$a]; 
				echo '</option>';
			}	
		echo'</select>

		';
		
		break;
		
	case "UnderKapittel":
		
		echo '
			<select onchange="showLink(this.options[this.selectedIndex].value, \''.$_POST['kapittel'].'\', \''.$_POST['book'].'\')">
				<option>Underkapittel</option>';
				
			$kapittel = $_POST['kapittel'];
			$chapters = array_keys($mattebok[$kapittel]);
		
			foreach ($chapters as $chapter){
				echo '<option value="'.$chapter.'">';
				echo $chapter;
				echo '</option>';
			}	
		echo'
				
			</select>
		';
		break;
		
	default:
		echo '<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
				<script type="text/javascript" src="classics.js"></script>
			<link rel="stylesheet"  href="veiviser_style.css">
			<div class="firstBox">
			<select onchange="getChaptersFromBook(this.options[this.selectedIndex].value)">
			<option>Din mattebok</option>';
	
			for($a = 0; $a < count($matteboker); $a++){
				echo '<option value="'.$matteboker[$a].'">'.$matteboker[$a].'</option>';
			}

		echo '
			</select>
			<span id="Kapittel">
				<select><option>Velg kapittel</option></select>
			</span>
			<span id="UnderKapittel">
				<select><option>Velg underkapittel</option></select>
			</span><br/>
			<br/>
			<span id="Resultat"></span>
			</form>
			</div>
		';
		break;
		
}

?>



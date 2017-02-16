<?php
    header('Content-type: text/html; charset=utf-8');
    echo "<center><br>\n";
	
	echo "<form action='ffmpeg_create.php' method='GET' onsubmit='return validateForm()'>\n";
	echo "<button type='button' onclick='location.href = \"../index.php\";'>Cancel</button>\n";
	echo "<input type='submit' value='Create'>\n";
	echo "<hr width=300>\n";
	echo "<input type='text' id='word' name='word' value='" . htmlspecialchars($_GET["word"]) . "' placeholder='Input word'><br>\n";
	echo "<select required id='movie' name='movie' onchange='refreshMoviesList(this.value)'>\n";
	echo "<option value='' selected disabled>Select movie</option>\n";
	$moviesPath  = '/Users/Apple/Sites/ffmpeg/video';
	$movies = scandir($moviesPath);
	foreach ($movies as $key => $movie)  {
		if (!in_array($movie, array(".","..")))  { 
			if (is_dir($moviesPath . DIRECTORY_SEPARATOR . $movie))  { 
				echo "<option value='" . $movie . "' style='font-size:50px'>" . $movie . "</option>\n";
			}
		} 
	}
    echo "</select><br>\n";
	echo "<select id='time' name='time'>\n";
	echo "<option value='' selected disabled>Select time</option>\n";
	echo "</select><br>\n";
    echo "<input type='checkbox' name='isSubs' value='y'> Subtitles<br>\n";
    
	
	echo "</form>\n";
	echo "<script src='script.js' type='text/javascript'></script>\n";
?>
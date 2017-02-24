<?php
    header('Content-type: text/html; charset=utf-8');
    echo "<center><br>\n";
	
	echo "<form action='ffmpeg_create.php' method='GET' onsubmit='return validateForm()'>\n";
// 	echo "<button type='button' onclick='location.href = \"../index.php\";'>Cancel</button>\n";
	echo "<input type='submit' value='Create'>\n";
	echo "<hr width=300>\n";
	echo "<input type='text' id='word' name='word' value='' placeholder='Input word'><br>\n";
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		$moviesPath  = 'E:/git_workspace/Video-Vocabulary-Php/ffmpeg/video';
	} else {
		$moviesPath  = '/Users/Apple/Sites/ffmpeg/video';
	}
	echo "<select required id='movie' name='movie' onchange='refreshMoviesList(\"$moviesPath\", this.value)'>\n";
	echo "<option value='' selected disabled>Select movie</option>\n";
	$movies = scandir($moviesPath);
	foreach ($movies as $key => $movie)  {
		if (!in_array($movie, array(".","..")))  { 
			if (is_dir($moviesPath . DIRECTORY_SEPARATOR . $movie))  { 
				if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
					$movieUtf = iconv("gb2312", "UTF-8", $movie);
				} else {
					$movieUtf = $movie;
				}
				echo "<option value='" . $movieUtf . "'>" . $movieUtf . "</option>\n";
			}
		} 
	}
    echo "</select><br>\n";
	echo "<select id='time' name='time'>\n";
	echo "<option value='' selected disabled>Select time</option>\n";
	echo "</select><br>\n";
    echo "<input type='checkbox' name='isSubs' id='isSubs' value='Yes'> Subtitles \n";
    echo "<input type='hidden' name='isSubs' id='isSubsHidden' value='No'>\n";
    echo "<input type='checkbox' name='isThumb' id='isThumb' value='Yes'> Thumbnail<br>\n";
    echo "<input type='hidden' name='isThumb' id='isThumbHidden' value='No'>\n";
	
	echo "</form>\n";
	echo "<script src='script.js' type='text/javascript'></script>\n";
?>
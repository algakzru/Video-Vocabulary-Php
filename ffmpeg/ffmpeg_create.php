<?php
    header('Content-type: text/html; charset=utf-8');
    echo "<center>\n";
	
	echo "<button type='button' onclick='location.href = \"ffmpeg_set.php\";'>Back</button>\n";
	echo "<hr width=300>\n";

	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		$movie = iconv("UTF-8", "gb2312", $_GET["movie"]);
		$moviesPath  = 'E:/git_workspace/Video-Vocabulary-Php/ffmpeg/video';
	} else {
		$movie = $_GET["movie"];
		$moviesPath  = '/Users/Apple/Sites/ffmpeg/video';
	}
	$ffmpeg = getcwd() . "/ffmpeg";
	$ffprobe = getcwd() . "/ffprobe";
	$input = str_replace("\\", "/", getcwd()) . "/video/" . $movie . "/original.mp4";
	$subtitleFile = str_replace("\\", "/", getcwd()) . "/video/" . $movie . "/edited.ass";
	$output_end = ($_GET["isSubs"] == "Yes" ? "_" . urldecode($_GET["word"]) : "");
	$output_ext = ($_GET["isThumb"] == "Yes" ? ".png" : ".mp4");
	$output = getcwd() . "/video/" . $movie . "/" . urldecode($_GET["time"]) . $output_end . $output_ext;
	$fileBlack = getcwd() . "/video/" . $movie . "/black.mp4";
	$fileEdited = getcwd() . "/video/" . $movie . "/edited.mp4";
	$fileConcat = getcwd() . "/video/" . $movie . "/concat.txt";
	
	$time = "";
	$timeFrom = substr(htmlspecialchars($_GET["time"]), 0, strpos(htmlspecialchars($_GET["time"]), "_"));
	$timeFrom = substr($timeFrom, 0, 2) . ":" . substr($timeFrom, 2, 2) . ":" . substr($timeFrom, 4, 2) . "." . substr($timeFrom, 6);
	$timeTo = substr(htmlspecialchars($_GET["time"]), strpos(htmlspecialchars($_GET["time"]), "_")+1);
	$timeTo = substr($timeTo, 0, 2) . ":" . substr($timeTo, 2, 2) . ":" . substr($timeTo, 4, 2) . "." . substr($timeTo, 6);
	$time = "-ss $timeFrom -to $timeTo";
	
	// Thumbnails
	if ($_GET["isThumb"] == "Yes") {
		echo $cmd = "$ffmpeg -y -i \"$input\" -ss $timeFrom -vframes 1 $output 2>&1";
		echo "<br>";
		$output = shell_exec($cmd);
		echo "<br><img src='video/" . $_GET["movie"] . "/" . $_GET["time"] . $output_end . $output_ext. "'><br>\n";
	} else {
		
		putenv("FC_CONFIG_DIR=".getcwd());
		putenv("FONTCONFIG_PATH=".getcwd());
		putenv("FONTCONFIG_FILE=".getcwd()."/fonts.conf");
		
		$filterComplexContent = "";
		if (file_exists("video/" . $movie . "/filter_complex.txt")) {
			$filterComplexFile = fopen("video/" . $movie . "/filter_complex.txt", "r") or die("Unable to open file!");
			while(!feof($filterComplexFile)) {
				$filterComplexContent = fgets($filterComplexFile);
			}
			fclose($filterComplexFile);
		}
	
		if (htmlspecialchars($_GET["isSubs"]) == "Yes") {
			$subtitleFileOriginal = fopen("video/" . $movie . "/original.ass", "r") or die("Unable to open file!");
			$subtitleFileEdited = fopen("video/" . $movie . "/edited.ass", "w") or die("Unable to open file!");
			while(!feof($subtitleFileOriginal)) {
				$string = fgets($subtitleFileOriginal);
				$string = str_replace(htmlspecialchars($_GET["word"]), '{\c&H00FFFF&}' . htmlspecialchars($_GET["word"]) . '{\c&HFFFFFF&}', $string);
				fwrite($subtitleFileEdited, $string);
			}
			fclose($subtitleFileEdited);
			fclose($subtitleFileOriginal);
			if ($filterComplexContent == "") {
				$filterComplexContent .= "[0:v] ass=$subtitleFile";
			} else {
				$filterComplexContent .= " [withoutSubs]; [withoutSubs] ass=$subtitleFile";
			}
		}
	
		$filterComplex = "";
		if ($filterComplexContent != "") {
			$filterComplex .= "-filter_complex \"$filterComplexContent\"";
		}
	
		echo $cmdBlack = "$ffmpeg -y -f lavfi -i color=color=black:s=480x270:d=1 \"$fileBlack\" 2>&1";
		echo $cmdEdited = "$ffmpeg -y -i \"$input\" $time $filterComplex -c:a copy \"$fileEdited\" 2>&1";
		$cmdConcat = "$ffmpeg -f concat -i \"$fileConcat\" -codec copy \"$output\" 2>&1";
	
		$output = shell_exec($cmdBlack." && ".$cmdEdited." && ".$cmdConcat);
		echo "<br><video id='videoPlayer' src='video/" . $_GET["movie"] . "/" . $_GET["time"] . $output_end . $output_ext. "'  controls></video><br>\n";
	}
	echo "</center>\n";
	echo "<pre>$output</pre>\n";
	echo "<script src='notifications.js' type='text/javascript'></script>\n";
?>
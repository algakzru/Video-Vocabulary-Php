<?php
    header('Content-type: text/html; charset=utf-8');
    echo "<center>\n";
	
	echo "<button type='button' onclick='location.href = \"ffmpeg_set.php?word=" . urldecode($_GET["word"]) . "&time=" . htmlspecialchars($_GET["time"]) . "\";'>Back</button>\n";
	echo "<hr width=300>\n";

	$ffmpeg = getcwd() . "/ffmpeg";
	$ffprobe = getcwd() . "/ffprobe";
	$input = getcwd() . "/video/" . urldecode($_GET["movie"]) . "/original.mp4";
	$subtitleFile = getcwd() . "/video/" . urldecode($_GET["movie"]) . "/edited.ass";
	$output_end = ($_GET["isSubs"] != "" ? "_" . urldecode($_GET["word"]) : "");
	$output = getcwd() . "/video/" . urldecode($_GET["movie"]) . "/" . urldecode($_GET["time"]) . $output_end . ".mp4";
	$fileBlack = getcwd() . "/video/" . urldecode($_GET["movie"]) . "/black.mp4";
	$fileEdited = getcwd() . "/video/" . urldecode($_GET["movie"]) . "/edited.mp4";
	$fileConcat = getcwd() . "/video/" . urldecode($_GET["movie"]) . "/concat.txt";
		
	putenv("FC_CONFIG_DIR=".getcwd());
	putenv("FONTCONFIG_PATH=".getcwd());
	putenv("FONTCONFIG_FILE=".getcwd()."/fonts.conf");
	
	$time = "";
	$timeFrom = substr(htmlspecialchars($_GET["time"]), 0, strpos(htmlspecialchars($_GET["time"]), "_"));
	$timeFrom = substr($timeFrom, 0, 2) . ":" . substr($timeFrom, 2, 2) . ":" . substr($timeFrom, 4, 2) . "." . substr($timeFrom, 6);
	$timeTo = substr(htmlspecialchars($_GET["time"]), strpos(htmlspecialchars($_GET["time"]), "_")+1);
	$timeTo = substr($timeTo, 0, 2) . ":" . substr($timeTo, 2, 2) . ":" . substr($timeTo, 4, 2) . "." . substr($timeTo, 6);
	$time = "-ss $timeFrom -to $timeTo";
	
	
	$filterComplexContent = "";
	if (file_exists("video/" . htmlspecialchars($_GET["movie"]) . "/filter_complex.txt")) {
		$filterComplexFile = fopen("video/" . htmlspecialchars($_GET["movie"]) . "/filter_complex.txt", "r") or die("Unable to open file!");
		while(!feof($filterComplexFile)) {
			$filterComplexContent = fgets($filterComplexFile);
		}
		fclose($filterComplexFile);
	}
	
	if (htmlspecialchars($_GET["isSubs"]) != "") {
		$subtitleFileOriginal = fopen("video/" . htmlspecialchars($_GET["movie"]) . "/original.ass", "r") or die("Unable to open file!");
		$subtitleFileEdited = fopen("video/" . htmlspecialchars($_GET["movie"]) . "/edited.ass", "w") or die("Unable to open file!");
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
	echo $cmdEdited = "$ffmpeg -y -i \"/Users/Apple/Sites/ffmpeg/video/12 angry men/original.mp4\" $time $filterComplex -c:a copy \"$fileEdited\" 2>&1";
	$cmdConcat = "$ffmpeg -f concat -i \"$fileConcat\" -codec copy \"$output\" 2>&1";
	
	$output = shell_exec($cmdBlack." && ".$cmdEdited." && ".$cmdConcat);
		
	echo "<br><video id='videoPlayer' src='video/" . htmlspecialchars($_GET["movie"]) . "/" . htmlspecialchars($_GET["time"]) . $output_end . ".mp4'  controls></video><br>\n";
	echo "</center>\n";
	echo "<pre>$output</pre>\n";
	echo "<script src='notifications.js' type='text/javascript'></script>\n";
?>
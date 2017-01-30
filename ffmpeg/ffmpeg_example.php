<?php
	header('Content-type: text/html; charset=utf-8');
	echo "<center>\n";	
	
	$ffmpeg = "/Users/Apple/Sites/ffmpeg/ffmpeg";
	$ffprobe = "/Users/Apple/Sites/ffmpeg/ffprobe";
	$input = "/Users/Apple/Sites/video/地三鲜/地三鲜.mp4";
	$output = "/Users/Apple/Sites/video/地三鲜/output.mp4";
	$subtitleFile = "/Users/Apple/Sites/video/地三鲜/地三鲜_edited.ass";
		
	$subtitleFileOriginal = fopen("../video/地三鲜/地三鲜.ass", "r") or die("Unable to open file!");
	$subtitleFileEdited = fopen("../video/地三鲜/地三鲜_edited.ass", "w") or die("Unable to open file!");
	while(!feof($subtitleFileOriginal)) {
		$string = fgets($subtitleFileOriginal);
		$string = str_replace(htmlspecialchars($_GET["word"]), '{\c&H00FFFF&}' . htmlspecialchars($_GET["word"]) . '{\c&HFFFFFF&}', $string);
		fwrite($subtitleFileEdited, $string);
	}
	fclose($subtitleFileEdited);
	fclose($subtitleFileOriginal);
	
	
	$timeFrom = "00:00:00.000";
	$timeTo = "00:00:23.480";
				
	putenv("FC_CONFIG_DIR=/Users/Apple/Sites/ffmpeg");
	putenv("FONTCONFIG_PATH=/Users/Apple/Sites/ffmpeg");
	putenv("FONTCONFIG_FILE=/Users/Apple/Sites/ffmpeg/fonts.conf");
	
	echo $cmd = "$ffmpeg -y -i \"$input\" -ss $timeFrom -to $timeTo -filter_complex \"[0:v] delogo=x=96:y=227:w=220:h=16 [delogo1]; [delogo1] delogo=x=30:y=20:w=61:h=26 [delogo2]; [delogo2] delogo=x=375:y=24:w=75:h=16 [delogo3]; [delogo3] ass=$subtitleFile \" -c:a copy $output 2>&1";
	echo $cmd = "$ffmpeg -y -i \"$input\" -ss $timeFrom -to $timeTo -filter_complex \"[0:v] delogo=x=96:y=227:w=220:h=16 [delogo1]; [delogo1] delogo=x=30:y=20:w=61:h=26 [delogo2]; [delogo2] delogo=x=375:y=24:w=75:h=16 \" -c:a copy $output 2>&1";
		
	echo "<br>";
		
	$output = shell_exec($cmd);
		
	echo "<video id='videoPlayer' src='../video/地三鲜/output.mp4'  controls></video><br>\n";
	echo "</center>\n";
	echo "<pre>$output</pre>\n";
	echo "<script src='notifications.js' type='text/javascript'></script>\n";
?>
<?php

/*****YoukuUpload SDK*****/
	header('Content-type: text/html; charset=utf-8');
	include("include/YoukuUploader.class.php");
	echo "<center><br>\n";
    
	$youkuUrl = "https://openapi.youku.com/v2/oauth2/authorize?client_id=f9e962f0927cea7d&response_type=code&state=xyz&redirect_uri=http://localhost/~Apple/checkYouku.php";
    echo "<button type='button' style='font-size: 12pt' onclick='location.href = \"index.php\";'>Cancel</button>\n";
	echo "<button type='button' style='font-size: 12pt' onclick='location.href = \"$youkuUrl\";'>Next</button><hr>\n";

$client_id = "f9e962f0927cea7d"; // Youku OpenAPI client_id
$client_secret = "2973e87ba421ad746cb4236fd9952874"; //Youku OpenAPI client_secret

	$url = 'https://openapi.youku.com/v2/oauth2/token';
    $postdata = array(
                      'client_id' => $client_id,
                      'client_secret' => $client_secret,
                      'grant_type' => 'authorization_code',
                      'code' => htmlspecialchars($_GET["code"]),
                      'redirect_uri' => 'http://localhost/~Apple/checkYouku.php'
                      );
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $array = json_decode(trim($response), TRUE);
    curl_close($ch);
    
	echo 'Code ' . htmlspecialchars($_GET["code"]) . "<br>\n";
    echo 'Response ' . $response . "<br>\n";
    echo 'Access Token ' . $array['access_token'] . "<br>\n";
    echo 'Refresh Token ' . $array['refresh_token'] . "<hr>\n";

/*
**The way with username and password applys to the partner level clients!
**Others may use access_token to upload the video file.
**In addition, refresh_token is to refresh expired access_token. 
**If it is null, the access_token would not be refreshed.
**You may refresh it by yourself.
**Like "http://open.youku.com/docs/api/uploads/client/english" for reference.
*/

$params['access_token'] = $array['access_token'];
$params['refresh_token'] = $array['refresh_token'];
$params['username'] = ""; //Youku username or email
$params['password'] = md5(""); //Youku password

set_time_limit(0);
ini_set('memory_limit', '128M');
$youkuUploader = new YoukuUploader($client_id, $client_secret);

	$db = new SQLite3('DatabaseVocabulary.db');
	$results = $db->query('SELECT * FROM video WHERE youku_id = ""');
	$count = 0;
    while ($row = $results->fetchArray()) {
		$video_id = $row['video_id'];
		$movie_name = $row['movie_name'];
		$file_name = $row['file_name'];
		$count++;
    }
	$db->close();
	
if ($count > 0) {
	
	echo "video_id: " . $video_id . " from " . $count . "<br>\n";
	echo "movie_name: " . $movie_name . "<br>\n";
	echo "file_name: " . $file_name . "<br>\n";
	$file_path = "ffmpeg/video/" . $movie_name . '/' . $file_name; //video file
	echo "<video id='videoPlayer' src='" . $file_path . "' autoplay controls></video><br><br>\n";
	
	// Title and Description
	$db = new SQLite3('DatabaseVocabulary.db');
	$results = $db->query('SELECT * FROM word_video INNER JOIN video ON word_video.video_id = video.video_id INNER JOIN word ON word_video.word_id = word.id where video.video_id =' . $video_id);
    while ($row = $results->fetchArray()) $word = $row['word'];
    $db->close();
	$title = "The word \"" . $word . "\" in the movie \"" . $movie_name . "\" (" . $video_id . ")";
	$title = "A movieclip from the movie \"" . $movie_name . "\" (" . $video_id . ")";
	$title = $movie_name . " (" . $video_id . ")";
	$description = "\"" . $movie_name . "\"的做法。";
	//$description = "This movieclip is uploaded only for nonprofit educational purposes and it demonstrates the proper way of using the word \"" . $word . "\" in the movie \"" . $movie_name . "\" by native speakers.";
	//$description = "This video shows how the word \"" . $word . "\" is used in the movie \"" . $movie_name . "\" as an example how native speakers use this word in daily life, and it is uploaded only for nonprofit educational purposes.";
    //$description .= "\n\n这段视频出自电影《" . $movie_name . "》，示范了词语 “" . $word . "” 在日常生活中的使用方法。本视频仅供学习交流，严禁用于商业用途。";
    
	
try {
    $file_md5 = @md5_file($file_path);
    if (!$file_md5) {
        throw new Exception("Could not open the file!\n");
    }
}catch (Exception $e) {
    echo "(File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage();
    return;
}
$file_size = filesize($file_path);
$uploadInfo = array(
		"title" => $title, //video title
		"description" => $description,  //video description
		"tags" => "视频词汇 Video Vocabulary", //tags, split by space
		"file_name" => $file_path, //video file name
		"file_md5" => $file_md5, //video file's md5sum
		"file_size" => $file_size //video file size
);
$progress = true; //if true,show the uploading progress
$youkuUploader->upload($progress, $params,$uploadInfo, $video_id);

} else {
	echo "No video to upload :)<br>\n";
}

?>


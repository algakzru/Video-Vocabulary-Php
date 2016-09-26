<?php
    include "vv/fragment_top.html";
	
    //Open the database mydb
	$db = new SQLite3('DatabaseVocabulary.db');
	$youkuUrl = "https://openapi.youku.com/v2/oauth2/authorize?client_id=f9e962f0927cea7d&response_type=code&state=xyz&redirect_uri=http://localhost/~Apple/checkYouku.php";
	$youtubeUrl = "https://accounts.google.com/o/oauth2/auth?client_id=576834641464-9mnbgljlgukatshfb2dnu8j99qinjhrb.apps.googleusercontent.com&redirect_uri=http%3A%2F%2Flocalhost%2F%7Ealgakzru%2FcheckYoutube.php&scope=https://www.googleapis.com/auth/youtube&response_type=code&access_type=offline";
    
	$results = $db->query('SELECT * FROM wordlist WHERE is_skip = 0');
    $selected_categories = "";
	$wordlist_names = "<select>";
    while ($row = $results->fetchArray()) {
        if ($selected_categories == "") {
        	$selected_categories .= "category_id in (";
        } else {
        	$selected_categories .= ", ";
        }
        $selected_categories .= $row['id'];
		$wordlist_names .= "<option>" . $row['wordlist'] . "</option>";
    }
    $selected_categories .= ($selected_categories == "" ? "" : ")");
	$wordlist_names .= "</select>";
    
	$file_words = fopen("vv/words.js", "w") or die("Unable to open file!");
    fwrite($file_words, "var words = [];\n");
	$sum=0;
	//$results = $db->query('SELECT id, word, sentence FROM word INNER JOIN word_video ON word_video.word_id = word.id  WHERE ' . $selected_categories . ' GROUP BY word_video.word_id ORDER BY COUNT(word_video.video_id) DESC');
    $results = $db->query('SELECT id, word, pronunciation FROM word WHERE ' . $selected_categories . ' ORDER BY pronunciation');
    while ($row = $results->fetchArray()) {
		$results2 = $db->query('SELECT * FROM word_video INNER JOIN video ON word_video.video_id = video.video_id where video.movie_name="锦绣华南" and word_video.word_id='. $row['id']);
		$youtubeIds = ""; $time = ""; $sentence = "";
		while ($row2 = $results2->fetchArray()) {
			if ($youtubeIds != "") $youtubeIds .= ",";
			if ($time != "") $time .= ",";
			$youtubeIds .= "\"" . $row2['youku_id'] . "\"";
			$time .= "\"" . explode("_", $row2['file_name'])[0] . "_" . explode("_", $row2['file_name'])[1] . "\"";
		}
		fwrite($file_words, "words.push( {word:'" . $row['word'] . "', pinyin:'" . $row['pronunciation'] . "', id:" . $row['id'] . ", videos:[");
		fwrite($file_words, $youtubeIds . "], times:[");
		fwrite($file_words, $time . "]} );\n");
    }
	fwrite($file_words, "document.getElementById('divWordlist').innerHTML = '" . $wordlist_names ."';\n");
    fclose($file_words);
	
	echo "<script src='vv/words.js' type='text/javascript'></script>\n";
	echo "<script src='vv/script.js' type='text/javascript'></script>\n";
	
	include "vv/fragment_bottom.html";
?>

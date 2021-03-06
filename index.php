<?php
    include "vv/fragment_top.html";
	
    //Open the database mydb
	$db = new SQLite3('DatabaseVocabulary.db');
	$youkuUrl = "https://openapi.youku.com/v2/oauth2/authorize?client_id=f9e962f0927cea7d&response_type=code&state=xyz&redirect_uri=http://localhost/~Apple/checkYouku.php";
	$youtubeUrl = "https://accounts.google.com/o/oauth2/auth?client_id=576834641464-9mnbgljlgukatshfb2dnu8j99qinjhrb.apps.googleusercontent.com&redirect_uri=http%3A%2F%2Flocalhost%2F%7Ealgakzru%2FcheckYoutube.php&scope=https://www.googleapis.com/auth/youtube&response_type=code&access_type=offline";
    
	$results = $db->query('SELECT * FROM wordlist WHERE is_skip = 0');
    $selected_categories = "";
	$wordlist_names = "<select onchange='wordListOnChange(this.value)'>";
	$wordlist_names2 = "<select id='selectWordlist'>";
	$wordlist_names .= "<option value='ALL'>ALL</option>";
	$wordlist_names2 .= "<option value='' selected disabled>Select Wordlist</option>";
    while ($row = $results->fetchArray()) {
        if ($selected_categories == "") {
        	$selected_categories .= "wordlist_id in (";
        } else {
        	$selected_categories .= ", ";
        }
        $selected_categories .= $row['id'];
		$wordlist_names .= "<option value='" . $row['id'] . "'>" . $row['wordlist'] . "</option>";
		$wordlist_names2 .= "<option value='" . $row['id'] . "'>" . $row['wordlist'] . "</option>";
    }
    $selected_categories .= ($selected_categories == "" ? "" : ")");
	$wordlist_names .= "</select>";
	$wordlist_names2 .= "</select>";
    
	$file_words = fopen("vv/words.js", "w") or die("Unable to open file!");
    fwrite($file_words, "var words = [];\n");
	$sum=0;
    $results = $db->query('SELECT id, word, pronunciation, wordlist_id FROM word WHERE ' . $selected_categories . ' ORDER BY pronunciation');
    while ($row = $results->fetchArray()) {
		$results2 = $db->query('SELECT * FROM word_video INNER JOIN video ON word_video.video_id = video.video_id where word_video.word_id='. $row['id']);
		$youtubeIds = ""; $withoutSubs = "";
		while ($row2 = $results2->fetchArray()) {
			if ($youtubeIds != "") $youtubeIds .= ",";
			$youtubeIds .= "\"" . $row2['youku_id'] . "\"";
			$time = explode("_", $row2['file_name'])[0] . "_" . explode("_", $row2['file_name'])[1];
			$results3 = $db->query("SELECT * FROM video where movie_name='". $row2['movie_name'] . "' and file_name='". $time. ".mp4'");
			while ($row3 = $results3->fetchArray()) {
				if ($withoutSubs != "") $withoutSubs .= ",";
				$withoutSubs .= "\"" . $row3['youku_id'] . "\"";
			}
		}
		fwrite($file_words, "words.push( {word:'" . $row['word'] . "', pinyin:'" . $row['pronunciation'] . "', id:" . $row['id'] . ", wordlist_id:'" . $row['wordlist_id'] . "', withSubs:[");
		fwrite($file_words, $youtubeIds . "], withoutSubs:[");
		fwrite($file_words, $withoutSubs . "]} );\n");
    }
	fwrite($file_words, "document.getElementById('divWordlist').innerHTML = \"" . $wordlist_names ."\";\n");
	fwrite($file_words, "document.getElementById('spanWordlist').innerHTML = \"" . $wordlist_names2 ."\";\n");
    fclose($file_words);
	
	echo "<script src='vv/words.js' type='text/javascript'></script>\n";
	echo "<script src='vv/script.js' type='text/javascript'></script>\n";
	
	include "vv/fragment_bottom.html";
?>

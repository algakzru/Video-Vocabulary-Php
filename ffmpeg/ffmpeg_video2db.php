<?php	//Open the database mydb	$db = new SQLite3('../DatabaseVocabulary.db');	$result = $db->exec('INSERT INTO video (movie_name, file_name, youtube_id, youku_id) VALUES ("' . $_GET["movieName"] . '", "' . $_GET["fileName"] . '", "", "")');    	$returnObj = new stdClass();	$returnObj->result = $result;	$returnJSON = json_encode($returnObj);	echo $returnJSON;?>
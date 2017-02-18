function refreshMoviesList(moviePath, movieName) {
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            returnArray = JSON.parse(this.responseText);
            var select = document.getElementById("time");
            while (select.length > 1) {
                select.remove(1);
            }
            for (i=0; i<returnArray.length; i++) {
                var option = document.createElement("option");
                option.text = returnArray[i];
                select.add(option);
            }
        }
    };
    xmlhttp.open("GET", "ffmpeg_refresh.php?path=" + moviePath + "/" + movieName + "/videos.txt", true);
    xmlhttp.send();
}

function video2Db(movieName, fileName) {
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
        	alert(this.responseText);
        	returnObj = JSON.parse(this.responseText);
//        	alert(returnObj.result);
        }
    };
    xmlhttp.open("GET", "ffmpeg_video2db.php?movieName=" + movieName + "&fileName=" + fileName, true);
    xmlhttp.send();
}

function validateForm() {
    var selectedMovie = document.getElementById("movie").value;
    if (selectedMovie == "") {
        alert("Select movie");
        return false;
    }
    
    var selectedTime = document.getElementById("time").value;
    if (selectedTime == "") {
        alert("Select time");
        return false;
    }
	
	if (document.getElementById("isSubs").checked) {
		document.getElementById('isSubsHidden').disabled = true;
	}
	
	if (document.getElementById("isThumb").checked) {
		document.getElementById('isThumbHidden').disabled = true;
	}
}
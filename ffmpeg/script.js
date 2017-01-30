function refreshMoviesList(movieName) {
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
    xmlhttp.open("GET", "ffmpeg_refresh.php?path=/Users/Apple/Sites/ffmpeg/video/" + movieName + "/videos.txt", true);
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
}
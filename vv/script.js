// Conditional CSS for IEMobile (http://blogs.msdn.com/b/iemobile/archive/2010/12/08/targeting-mobile-optimized-css-at-windows-phone-7.aspx)
// doesn't seem to work on WP7.5 (Mango) emulator, so use useragent-based detection instead
if (navigator.userAgent.indexOf("IEMobile") >= 0) {
    document.body.className += " iemobile";
    window.onscroll = function () { window.scroll(0, 0) }
}

// IE does not know about the target attribute. It looks for srcElement
// This function will get the event target in a browser-compatible way
function getEventTarget(e) {
    e = e || window.event;
    return e.target || e.srcElement; 
}

var ul = document.getElementById('words');
ul.onclick = function(event) {
    var target = getEventTarget(event);
    if (target.parentElement.nodeName == "LI") target = target.parentElement;
    if (selected_li != null) {
        selected_li.className = "";
    }
    selected_li = target;
    selected_li.className = "selected";
	onClick(target.id.substring(3), true);
};

function onClick(index, hanzi) {
	//document.getElementById('divTitle').innerHTML = words[index].word;
	var text = "<select onchange='selectVideo(" + index + ", this.value)'>";
	for (i=0; i<words[index].withSubs.length; i++) {
		text += "<option value=" + i + " " + (i==0?"selected":"") + ">" +  words[index].withSubs[ i ] + "</option>";
	}
	text += "</select>";
	document.getElementById('selectVideos').innerHTML = text;
    document.getElementById("editbox").style.display = "none";
	document.getElementById("videobox").style.display = "block";
    if (document.getElementById('cbSubtitles').checked) {
        document.getElementById("player").src = "http://player.youku.com/embed/" + words[index].withSubs[ 0 ];
    } else {
        document.getElementById("player").src = "http://player.youku.com/embed/" + words[index].withoutSubs[ 0 ];
    }
	document.getElementById("btnDelete").onclick = function() { deleteWordConfirmation( words[index].word, words[index].id ) } ;
	document.getElementById("btnEdit").onclick = function() { requestTempRefresh(words[index].id); }
}

function requestTempRefresh(wordId) {
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            //alert(this.responseText);
            returnObj = JSON.parse(this.responseText);
            //alert(returnObj.location);
            
            document.getElementById("videobox").style.display = "none";
            document.getElementById("editbox").style.display = "block";
            document.getElementById("btnCancel").onclick = function() { document.getElementById("editbox").style.display = "none"; }
            document.getElementById("btnSave").onclick = function() {
                saveWord(wordId, document.getElementById("inputWord").value, document.getElementById("selectWordlist").value); }
        }
    };
    xmlhttp.open("GET", "tempRefresh.php?wordId=" + wordId, true);
    xmlhttp.send();
}

function checkYouku() {
    location.href = "https://openapi.youku.com/v2/oauth2/authorize?client_id=f9e962f0927cea7d&response_type=code&state=xyz&redirect_uri=http://localhost/~Apple/checkYouku.php";
}

function saveWord(wordId, word, wordlistId) {
    if (wordlistId == "") {
        alert('Select Wordlist');
        return;
    }
    if (word == "") {
        alert('Input Word');
        return;
    }
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            alert(this.responseText);
            returnObj = JSON.parse(this.responseText);
            //alert(returnObj.location);
        }
    };
    xmlhttp.open("GET", "tempSaveWord.php?wordId=" + wordId + "&word=" + word + "&wordlistId=" + wordlistId, true);
    xmlhttp.send();
}

function wordListOnChange(value) {
	setList(value);
}

function selectVideo(index, videoPosition) {
	document.getElementById("player").src = "http://player.youku.com/embed/" + words[index].videos[ videoPosition ];
}

function deleteWordConfirmation(word, word_id) {
	var r = confirm('Delete "' + word + '" ?\nword_id = ' + word_id);
	if (r == true) {
		location.href = "deleteWord.php?word_id=" + word_id;
	}
}

var selected_li = null;

function setList(wordlistId) {
	while (ul.firstChild) {
		ul.removeChild(ul.firstChild);
	}
	for (i = 0; i < words.length; i++) {
		if (wordlistId == "ALL" || words[i].wordlist_id == wordlistId) {
			var li = document.createElement("LI");
			li.id = "itm" + i;
			var textnode = document.createTextNode(words[i].word);
			li.appendChild(textnode);
			ul.appendChild(li);
		}
	}
}
setList("ALL");
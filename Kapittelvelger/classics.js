function makeRequest() {
	
 	if (window.XMLHttpRequest) { // Mozilla, Safari, ...
		httpRequest = new XMLHttpRequest();
        if (httpRequest.overrideMimeType) {
            httpRequest.overrideMimeType('text/xml; charset=UTF-8');
        }
    } else if (window.ActiveXObject) { // IE
        try {
			httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
        }catch (e){
            try {
				httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
			}catch (e) {alert("IE BUG");}
        }
    }

    if (!httpRequest) {
        alert('Error: Cannot create an XMLHTTP instance');
		return false;
    }else{
		return httpRequest;
	}

}

function getChaptersFromBook(book){
	
	var zalo = makeRequest();
	var url = 'veiviser.php';
	var params = 'todo=Kapittel&book='+book;
		
	zalo.open('POST', url, true);
	
	zalo.onreadystatechange = function(){
		if(zalo.readyState == 4){
			document.getElementById("Kapittel").innerHTML = zalo.responseText; 
		}
	}
	
	zalo.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
	zalo.setRequestHeader("Content-length", params.length);
	zalo.setRequestHeader("Connection", "close");

	zalo.send(params);
}

function getSubChaptersFromBook(chapter, book){
	
	var zalo = makeRequest();
	var url = 'veiviser.php';
	var params = 'todo=UnderKapittel&kapittel='+chapter+'&book='+book;
		
	zalo.open('POST', url, true);
	
	zalo.onreadystatechange = function(){
		if(zalo.readyState == 4){
			document.getElementById("UnderKapittel").innerHTML = zalo.responseText; 
		}
	}
	
	zalo.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
	zalo.setRequestHeader("Content-length", params.length);
	zalo.setRequestHeader("Connection", "close");

	zalo.send(params);
}

function showLink(subChapter, chapter, book){
	var zalo = makeRequest();
	var url = "veiviser.php";
	var params = "todo=showLink&subchapter="+subChapter+"&chapter="+chapter+"&book="+book;
	
	zalo.open('POST', url, true);
	
	zalo.onreadystatechange = function(){
		if(zalo.readyState == 4){
			document.getElementById("Resultat").innerHTML = zalo.responseText;
		}
	}
	
	zalo.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
	zalo.setRequestHeader("Content-length", params.length);
	zalo.setRequestHeader("Connection", "close");

	zalo.send(params);
}

function goToThisUrl(link, title){
	
	var zalo = makeRequest();
	var url = 'veiviser.php';
	
	var params = 'todo=Link&link='+link+'&title='+title;
		
	zalo.open('POST', url, true);
	
	zalo.onreadystatechange = function(){
		if(zalo.readyState == 4){
			document.getElementById("Resultat").innerHTML = zalo.responseText; 
		}
	}
	
	zalo.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
	zalo.setRequestHeader("Content-length", params.length);
	zalo.setRequestHeader("Connection", "close");

	zalo.send(params);
	
}
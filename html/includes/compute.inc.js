
function computeGNN(id,key) {
	var fd = new FormData();
        var xhr = new XMLHttpRequest();
	xhr.upload.addEventListener("progress", gnnProgress, false);
	xhr.addEventListener("load", gnnComplete, false);
	xhr.addEventListener("error", gnnFailed, false);
	xhr.addEventListener("abort", gnnCanceled, false);
	fd.append('id',id);
	fd.append('key',key);
        xhr.open("POST", "compute.php",true);
        xhr.send(fd);
	xhr.onreadystatechange  = function(){
		if (xhr.readyState == 4  ) {

			// Javascript function JSON.parse to parse JSON data
			var jsonObj = JSON.parse(xhr.responseText);

			// jsonObj variable now contains the data structure and can
			// be accessed as jsonObj.name and jsonObj.country.
			if (jsonObj.valid) {
				window.location.replace("stepc.php?id=" + jsonObj.id + "&key=" + jsonObj.key);
			}
			else {
				var bar = document.getElementById('progress_bar');
				bar.value = 100;
				//document.getElementById('progress_bar').disabled = true;
				document.getElementById('message').innerHTML =  jsonObj.message;
			}
			
		}
	}

}

function gnnProgress(evt) {
        /*if (evt.lengthComputable) {
          var percentComplete = Math.round(evt.loaded * 100 / evt.total);
          document.getElementById('progressNumber').innerHTML = percentComplete.toString() + '%';
	  var bar = document.getElementById('progress_bar');
	  bar.value = percentComplete;
        }
        else {
          document.getElementById('progressNumber').innerHTML = 'unable to compute';
        }*/
}

function gnnComplete(evt) {
        /* This event is raised when the server send back a response */
        //alert(evt.target.responseText);
      }

function gnnFailed(evt) {
        alert("There was an error processing GNN.");
      }

function gnnCanceled(evt) {
        alert("The upload has been canceled by the user or the browser dropped the connection.");
      }

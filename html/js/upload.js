
function uploadFile(fileInputId, formId, progressNumId, progressBarId, messageId, emailId, submitId, isSsn) {
    var fd = new FormData();
    fd.append("file", document.getElementById(fileInputId).files[0]);
    var xhr = new XMLHttpRequest();
    xhr.upload.addEventListener("progress", function(evt) { uploadProgress(evt, progressNumId, progressBarId);}, false);
    xhr.addEventListener("load", uploadComplete, false);
    xhr.addEventListener("error", uploadFailed, false);
    xhr.addEventListener("abort", uploadCanceled, false);
    fd.append('email',document.getElementById(emailId).value);
    fd.append('submit',document.getElementById(submitId).value);
    if (isSsn) {
        fd.append('neighbor_size',document.getElementById('neighbor_size').value);
        fd.append('MAX_FILE_SIZE',document.getElementById('MAX_FILE_SIZE').value);
        fd.append('cooccurrence',document.getElementById('cooccurrence').value);
    }
    disableForm(formId);
    var script = isSsn ? "upload_ssn.php" : "upload_diagram.php";
    xhr.open("POST", script, true);
    xhr.send(fd);
    xhr.onreadystatechange  = function(){
        if (xhr.readyState == 4  ) {

            // Javascript function JSON.parse to parse JSON data
            var jsonObj = JSON.parse(xhr.responseText);

            // jsonObj variable now contains the data structure and can
            // be accessed as jsonObj.name and jsonObj.country.
            if (jsonObj.valid) {
                var nextStepScript = "stepb.php";
                var diagUpload = isSsn ? "" : "&upload=1";
                window.location.href = nextStepScript + "?id=" + jsonObj.id + "&key=" + jsonObj.key + diagUpload;
                console.log(jsonObj.cookieInfo);
                if (jsonObj.cookieInfo)
                    document.cookie = jsonObj.cookieInfo;
            }
            if (jsonObj.message) {
                enableForm(formId);
                document.getElementById(messageId).innerHTML = jsonObj.message;
            }

        }
    }

}

function uploadProgress(evt, progressTextId, progressBarId) {
    if (evt.lengthComputable) {
        var percentComplete = Math.round(evt.loaded * 100 / evt.total);
        document.getElementById(progressTextId).innerHTML = "Uploading File: " + percentComplete.toString() + '%';
        var bar = document.getElementById(progressBarId);
        bar.value = percentComplete;
    }
    else {
        document.getElementById(progressTextId).innerHTML = 'unable to compute';
    }
}

function uploadComplete(evt) {
    /* This event is raised when the server send back a response */
    //alert(evt.target.responseText);
}

function uploadFailed(evt) {
    alert("There was an error attempting to upload the file.");
}

function uploadCanceled(evt) {
    alert("The upload has been canceled by the user or the browser dropped the connection.");
}

function disableForm(formId) {
    document.getElementById(formId).disabled = true;
//    document.getElementById('ssn_file').disabled = true;
//    document.getElementById('neighbor_size').disabled = true;
//    document.getElementById('email').disabled = true;
//    document.getElementById('submit').disabled = true;
}

function enableForm(formId) {
    document.getElementById(formId).disabled = false;
//    document.getElementById('ssn_file').disabled = false;
//    document.getElementById('neighbor_size').disabled = false;
//    document.getElementById('email').disabled = false;
//    document.getElementById('submit').disabled = false;
}



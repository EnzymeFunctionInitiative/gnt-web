
var DIAGRAM_UPLOAD = 0;
var SSN_UPLOAD = 1;

function uploadFile(fileInputId, formId, progressNumId, progressBarId, messageId, emailId, submitId, isSsn) {
    var fd = new FormData();
    addParam(fd, "email", emailId);
    addParam(fd, "submit", submitId);
    if (isSsn) {
        addParam(fd, "neighbor_size", "neighbor_size");
//        addParam(fd, "MAX_FILE_SIZE", "MAX_FILE_SIZE");
        addParam(fd, "cooccurrence", "cooccurrence");
    }

    var files = document.getElementById(fileInputId).files;
    var completionHandler = function() { enableForm(formId); };
    fd.append("file", files[0]);
    var fileHandler = function(xhr) {
        addUploadStuff(xhr, progressNumId, progressBarId);
    };

    disableForm(formId);
    var script = isSsn ? "upload_ssn.php" : "upload_diagram.php";

    var uploadType = isSsn ? SSN_UPLOAD : DIAGRAM_UPLOAD;
    doFormPost(script, fd, messageId, fileHandler, uploadType, completionHandler);
    
//    var xhr = new XMLHttpRequest();
//    addUploadStuff(xhr, progressNumId, progressBarId);
//    xhr.open("POST", script, true);
//    xhr.send(fd);
//    xhr.onreadystatechange  = function(){
//        if (xhr.readyState == 4  ) {
//
//            // Javascript function JSON.parse to parse JSON data
//            var jsonObj = JSON.parse(xhr.responseText);
//
//            // jsonObj variable now contains the data structure and can
//            // be accessed as jsonObj.name and jsonObj.country.
//            if (jsonObj.valid) {
//                var nextStepScript = "stepb.php";
//                var diagUpload = isSsn ? "" : "&diagram=1";
//                if (jsonObj.cookieInfo)
//                    document.cookie = jsonObj.cookieInfo;
//                window.location.href = nextStepScript + "?id=" + jsonObj.id + "&key=" + jsonObj.key + diagUpload;
//            }
//            if (jsonObj.message) {
//                enableForm(formId);
//                document.getElementById(messageId).innerHTML = jsonObj.message;
//            }
//
//        }
//    }

}

function addUploadStuff(xhr, progressNumId, progressBarId) {
    xhr.upload.addEventListener("progress", function(evt) { uploadProgress(evt, progressNumId, progressBarId);}, false);
    xhr.addEventListener("load", uploadComplete, false);
    xhr.addEventListener("error", uploadFailed, false);
    xhr.addEventListener("abort", uploadCanceled, false);
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

function addParam(fd, param, id) {
    console.log(param + "   " + id);
    fd.append(param, document.getElementById(id).value);
}

function submitOptionAForm(formAction, optionId, inputId, titleId, evalueId, maxSeqId, emailId, nbSizeId, messageId) {

    var fd = new FormData();
    addParam(fd, "option", optionId);
    addParam(fd, "title", titleId);
    addParam(fd, "sequence", inputId);
    addParam(fd, "evalue", evalueId);
    addParam(fd, "max-seqs", maxSeqId);
    addParam(fd, "nb-size", nbSizeId);
    addParam(fd, "email", emailId);
    var fileHandler = function(xhr) {};
    var completionHandler = function() {};

    doFormPost(formAction, fd, messageId, fileHandler, DIAGRAM_UPLOAD, completionHandler);
}


function submitOptionDForm(formAction, optionId, inputId, titleId, emailId, nbSizeId, fileId, progressNumId, progressBarId, messageId) {
    submitOptionForm(formAction, optionId, "ids", inputId, titleId, emailId, nbSizeId, fileId, progressNumId, progressBarId, messageId);

//    var fd = new FormData();
//    addParam(fd, "option", optionId);
//    addParam(fd, "title", titleId);
//    addParam(fd, "ids", inputId);
//    addParam(fd, "nb-size", nbSizeId);
//    addParam(fd, "email", emailId);
//    var files = document.getElementById(fileId).files;
//    var fileHandler = function(xhr) {};
//    var completionHandler = function() {};
//    if (files.length > 0) {
//        fd.append("file", files[0]);
//        fileHandler = function(xhr) {
//            addUploadStuff(xhr, progressNumId, progressBarId);
//        };
//    }
//
//    doFormPost(formAction, fd, messageId, fileHandler, DIAGRAM_UPLOAD, completionHandler);
}


function submitOptionCForm(formAction, optionId, inputId, titleId, emailId, nbSizeId, fileId, progressNumId, progressBarId, messageId) {
    submitOptionForm(formAction, optionId, "fasta", inputId, titleId, emailId, nbSizeId, fileId, progressNumId, progressBarId, messageId);
}

function submitOptionForm(formAction, optionId, inputField, inputId, titleId, emailId, nbSizeId, fileId, progressNumId, progressBarId, messageId) {
    var fd = new FormData();
    addParam(fd, "option", optionId);
    addParam(fd, "title", titleId);
    addParam(fd, inputField, inputId);
    addParam(fd, "nb-size", nbSizeId);
    addParam(fd, "email", emailId);
    var files = document.getElementById(fileId).files;
    var fileHandler = function(xhr) {};
    var completionHandler = function() {};
    if (files.length > 0) {
        fd.append("file", files[0]);
        fileHandler = function(xhr) {
            addUploadStuff(xhr, progressNumId, progressBarId);
        };
    }

    doFormPost(formAction, fd, messageId, fileHandler, DIAGRAM_UPLOAD, completionHandler);
}


function doFormPost(formAction, formData, messageId, fileHandler, uploadType, completionHandler) {
    var xhr = new XMLHttpRequest();
    if (typeof fileHandler === "function")
        fileHandler(xhr);
    xhr.open("POST", formAction, true);
    xhr.send(formData);
    xhr.onreadystatechange  = function(){
        if (xhr.readyState == 4  ) {

            // Javascript function JSON.parse to parse JSON data
            var jsonObj = JSON.parse(xhr.responseText);

            // jsonObj variable now contains the data structure and can
            // be accessed as jsonObj.name and jsonObj.country.
            if (jsonObj.valid) {
                var nextStepScript = "stepb.php";
                var diagUpload = uploadType == SSN_UPLOAD ? "" : "&diagram=1";
                if (jsonObj.cookieInfo)
                    document.cookie = jsonObj.cookieInfo;
                window.location.href = nextStepScript + "?id=" + jsonObj.id + "&key=" + jsonObj.key + diagUpload;
            }
            if (jsonObj.message) {
                document.getElementById(messageId).innerHTML = jsonObj.message;
            } else {
                completionHandler();
                document.getElementById(messageId).innerHTML = "";
            }
        }
    }
}


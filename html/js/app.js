
function ArrowApp(arrows, popupIds) {

    this.arrows = arrows;
    this.popupIds = popupIds;
    this.inputObj = document.getElementById("search-input");
    this.advancedInputObj = document.getElementById("advanced-search-input");
    this.progressObj = $("#progress-loader");
    this.showMoreObj = $("#show-more-arrows-button");
    this.showAllObj = $("#show-all-arrows-button");
    this.filterListObj = $("#filter-container");
    this.filterContainerToggleObj = $("#filter-container-toggle");
    this.filterLegendObj = $("#active-filter-list");
    this.diagramsDisplayed = $("#diagrams-displayed-count");
    this.diagramsTotal = $("#diagrams-total-count");
    this.firstRun = true;
    this.showFamsById = false;
    this.idKeyQueryString = "";
    this.showAll = false;

    var that = this;
    this.filterLegendObj.empty();
    $("#search-cluster-button").click(function() {
            that.advancedInputObj.value = that.inputObj.value;
            that.search(that.inputObj);
        });
    $("input").on('keypress', function(e) {
            if (e.which == 13) {
                that.advancedInputObj.value = that.inputObj.value;
                that.search(that.inputObj);
                e.preventDefault();
            }
        });
    $("#advanced-search-cluster-button").click(function() {
            that.showAll = false;
            that.search(that.advancedInputObj);
        });

    $("#filter-cb-toggle-dashes").click(function() { $(".an-arrow").toggleClass("dashed-arrow"); });

    this.showMoreObj.click(function() {
        that.startProgressBar();
        that.arrows.nextPage(function(isEod) {
            that.nextPageCallback(isEod);
            that.populateFilterList();
            that.stopProgressBar();
            that.updateCountFields();
        });
    });

    $("#refresh-window").click(function(e) {
        var nbSize = $("#window-size").val();
        that.setNeighborhoodWindow(nbSize);
        that.startProgressBar();
        that.refreshCanvas(!that.showAll, function(isEod) {
            that.stopProgressBar();
        });
    });
    
    this.showAllObj.click(function() {
        that.startProgressBar();
        that.showAll = true;
        
        var finishCb = function(isEod) {
            that.updateMoreButtonStatus(isEod);
            that.populateFilterList();
            that.stopProgressBar();
            that.updateCountFields();
        };

        that.arrows.searchArrows(!that.showAll, finishCb);
//        that.arrows.retrieveArrowData(undefined, false, ARROW_RESET_REFRESH, function(isEod) {
//            that.updateMoreButtonStatus(isEod);
//            that.populateFilterList();
//            that.stopProgressBar();
//            that.updateCountFields();
////            $('html,body').animate({scrollTop: document.body.scrollHeight},{duration:800});
//        });
    });

    $("#filter-clear").click(function() {
            that.clearFilter();
        });

    $("#download-data").tooltip({delay: {show: 50}, placement: 'top', trigger: 'hover'});

    this.enableSaveButton();
}

ArrowApp.prototype.refreshCanvas = function(pageResults, callback) {
    this.arrows.refreshCanvas(pageResults, callback);
}

ArrowApp.prototype.setNeighborhoodWindow = function(nbSize) {
    this.arrows.setNeighborhoodWindow(nbSize);
}

ArrowApp.prototype.setQueryString = function(idKeyQueryString) {
    this.idKeyQueryString = idKeyQueryString;
}

ArrowApp.prototype.showDefaultDiagrams = function() {
    this.startProgressBar();
    this.doSearch("1");
}

ArrowApp.prototype.search = function(inputObj) {
    this.startProgressBar();
    var idList = this.getIdList(inputObj);
    this.doSearch(idList);
}

ArrowApp.prototype.doSearch = function(idList) {
    this.arrows.clearFamilyData();
    this.clearFilter();
    
    var that = this;
    var finishCb = function(isEod ) {
        that.populateFilterList();
        that.updateMoreButtonStatus(isEod);
        that.stopProgressBar();
        that.updateCountFields();
        $("#start-info").hide();
    };
    
    this.arrows.setIdList(idList);
    this.showAll = false;
    this.arrows.searchArrows(!this.showAll, finishCb);

//    this.arrows.retrieveArrowData(idList, true, ARROW_RESET_REFRESH, function(isEod) {
//        that.populateFilterList();
//        that.updateMoreButtonStatus(isEod);
//        that.stopProgressBar();
//        that.updateCountFields();
//        $("#start-info").hide();
//    });
}

ArrowApp.prototype.populateFilterList = function() {
    this.fams = this.arrows.getFamilies(this.showFamsById);
    this.filterListObj.empty();
    this.filterLegendObj.empty();

    var that = this;
    $.each(this.fams, function(i, fam) {
        var famText = that.showFamsById ? fam.id : fam.name;
        //var ttText = that.showFamsById ? fam.name : fam.id;
        var ttText = famText;
        var isChecked = fam.checked ? "checked" : "";

        var entry = $("<div class='filter-cb-div' title='" + ttText + "'></div>").appendTo(that.filterListObj);
        $("<input id='filter-cb-" + i + "' class='filter-cb' type='checkbox' value='" + i + "' " + isChecked + "/>")
            .appendTo(entry)
            .click(function(e) {
                if (this.checked) {
                    that.arrows.addPfamFilter(fam.id);
                    that.addLegendItem(i, fam.id, famText);
//    var color = that.arrows.getPfamColor(id);
//                    var activeFilter = $("<div id='legend-" + this.value + "'>" +
//                            "<span class='active-filter-icon' style='background-color:" + color + "'> </span> " + famText + "</div>")
//                        .appendTo("#active-filter-list");
                } else {
                    that.arrows.removePfamFilter(fam.id);
                    $("#legend-" + i).remove();
                }
            });
         //$(" <span id='filter-cb-text-" + i + "' class='filter-cb-name'>" + fam.name + "</span>").
         //   appendTo(entry);
         $("<span id='filter-cb-text-" + i + "' class='filter-cb-number'><label for='filter-cb-" + i + "'>" + famText + "</label></span>").
            appendTo(entry);
         
         if (fam.checked)
             that.addLegendItem(i, fam.id, famText);
    });

    if (this.firstRun) {
        $(".initial-hidden").removeClass("initial-hidden");
        this.firstRun = false;
    }

    $('.filter-cb-div').tooltip({delay: {show: 50}, placement: 'top', trigger: 'hover'});

}

ArrowApp.prototype.addLegendItem = function(index, id, text) {
    var color = this.arrows.getPfamColor(id);
    var activeFilter = $("<div id='legend-" + index + "'>" +
            "<span class='active-filter-icon' style='background-color:" + color + "'> </span> " + text + "</div>")
        .appendTo(this.filterLegendObj);
}

ArrowApp.prototype.clearFilter = function() {
    $("input.filter-cb:checked").removeAttr("checked");
    this.filterLegendObj.empty();
    this.arrows.clearPfamFilters();
}

ArrowApp.prototype.togglePfamNamesNumbers = function(isChecked) {
    this.showFamsById = isChecked; // true == sort by ID
    this.populateFilterList();
    //$(".filter-cb-name").toggleClass("hidden");
    //$(".filter-cb-number").toggleClass("hidden"); 
}

ArrowApp.prototype.startProgressBar = function() {
    this.progressObj.removeClass("hidden-placeholder");
}

ArrowApp.prototype.stopProgressBar = function() {
    this.progressObj.addClass("hidden-placeholder");
}

ArrowApp.prototype.getIdList = function(inputObj) {
    var idList = inputObj.value;
    return idList;
}

ArrowApp.prototype.updateMoreButtonStatus = function (isEod) {
    if (isEod) {
        this.showMoreObj.prop('disabled', true).addClass("disabled");
        this.showAllObj.prop('disabled', true).addClass("disabled");
    } else {
        this.showMoreObj.prop('disabled', false).removeClass("disabled");
        this.showAllObj.prop('disabled', false).removeClass("disabled");
    }
}

ArrowApp.prototype.nextPageCallback = function (isEod) {
    this.populateFilterList();
    this.updateMoreButtonStatus(isEod);
    //$('html,body').animate({scrollTop: document.body.scrollHeight},{duration:800});
}

ArrowApp.prototype.enableSaveButton = function() {
    
    var saveSupported = (navigator.userAgent.search("Chrome") >= 0 ||
                         navigator.userAgent.search("Firefox") >= 0 ||
                         (navigator.userAgent.search("Safari") >= 0 && navigator.userAgent.search("Chrome") < 0)) &&
                        navigator.userAgent.search("Edge") < 0;

    if (saveSupported) {
        $("#save-canvas-button").show();

//        $("#save-canvas-button").canvas(function(e) {
//                
//            });
    } else {
        $("#save-canvas-button").hide();
    }
}

ArrowApp.prototype.getMatchedUniProtIds = function(callback) {
   var xmlhttp = new XMLHttpRequest();
   xmlhttp.open("GET", "get_direct_ids.php?" + this.idKeyQueryString + "&type=matched", true);
   xmlhttp.onload = function() {
       if (this.readyState == 4 && this.status == 200) {
           var data = JSON.parse(this.responseText);
           typeof callback === 'function' && callback(data);
       }
   };
   xmlhttp.send(null);
}

ArrowApp.prototype.getUnmatchedUniProtIds = function(callback) {
   var xmlhttp = new XMLHttpRequest();
   xmlhttp.open("GET", "get_direct_ids.php?" + this.idKeyQueryString + "&type=unmatched", true);
   xmlhttp.onload = function() {
       if (this.readyState == 4 && this.status == 200) {
           var data = JSON.parse(this.responseText);
           typeof callback === 'function' && callback(data);
       }
   };
   xmlhttp.send(null);
}

ArrowApp.prototype.formatIdList = function(idList) {
    var text = $("<div></div>");
    for (var i = 0; i < idList.length; i++) {
        text.append("<div>" + idList[i] + "</div>");
    }

    return text;
}

ArrowApp.prototype.updateCountFields = function() {
    var c = this.arrows.getDiagramCounts();
    this.diagramsDisplayed.text(c[0]);
    this.diagramsTotal.text(c[1]);
}

ArrowApp.prototype.downloadSvg = function(svg, gnnName) {
    var dlForm = $("<form></form>");
    dlForm.attr("method", "POST");
    dlForm.attr("action", "download_diagram_image.php");
    dlForm.append('<input type="hidden" name="type" value="svg">');
    dlForm.append('<input type="hidden" name="name" value="' + gnnName + '">');
    dlForm.append('<input type="hidden" name="svg" value="' + svg + '">');
    $("#download-forms").append(dlForm);
    dlForm.submit();
}










////////////////////////////////////////////////////////////////////////////////////////////////////////////

function PopupIds() {
    this.ParentId = "info-popup";
    this.IdId = "info-popup-id";
    this.FamilyId = "info-popup-fam";
    this.FamilyDescId = "info-popup-fam-desc";
    this.SpTrId = "info-popup-sptr";
    this.SeqLenId = "info-popup-seqlen";
    this.DescId = "info-popup-desc";
}





function saveDataFn(filename, dataId) {
    var data = getDataFromDivs(dataId);
    var blob = new Blob([data], {type: 'text/plain'});
    if(window.navigator.msSaveOrOpenBlob) {
        window.navigator.msSaveBlob(blob, filename);
    }
    else{
        var elem = window.document.createElement('a');
        elem.href = window.URL.createObjectURL(blob);
        elem.download = filename;        
        document.body.appendChild(elem);
        elem.click();        
        document.body.removeChild(elem);
    }
}
function getDataFromDivs(parentId) {
    var data = "";
    var parent = document.getElementById(parentId);
    for (var i = 0; i < parent.children.length; i++) {
        data += parent.children[i].innerHTML + "\n";
    }
    return data;
}



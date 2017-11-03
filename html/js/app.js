
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
    this.firstRun = true;
    this.showFamsById = false;

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
    $("#advanced-search-cluster-button").click(function() { that.search(that.advancedInputObj); });

    $("#filter-cb-toggle-dashes").click(function() { $(".an-arrow").toggleClass("dashed-arrow"); });

    this.showMoreObj.click(function() {
        that.startProgressBar();
        that.arrows.nextPage(function(isEod) {
            that.nextPageCallback(isEod);
            that.populateFilterList();
            that.stopProgressBar();
        });
    });
    
    this.showAllObj.click(function() {
        that.startProgressBar();
        that.arrows.retrieveArrowData(undefined, false, true, function(isEod) {
            that.updateMoreButtonStatus(isEod);
            that.populateFilterList();
            that.stopProgressBar();
//            $('html,body').animate({scrollTop: document.body.scrollHeight},{duration:800});
        });
    });

    $("#filter-clear").click(function() {
            that.clearFilter();
        });

    $("#download-data").tooltip({delay: {show: 50}, placement: 'top', trigger: 'hover'});

    this.enableSaveButton();
}

ArrowApp.prototype.search = function(inputObj) {
    this.startProgressBar();
    var idList = this.getIdList(inputObj);
    this.arrows.clearFamilyData();
    this.clearFilter();
    var that = this;
    this.arrows.retrieveArrowData(idList, true, true, function(isEod) {
        that.populateFilterList();
        that.updateMoreButtonStatus(isEod);
        that.stopProgressBar();
        $("#start-info").hide();
    });
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
    console.log("Starting");
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



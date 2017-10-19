
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
    this.firstRun = true;
    this.showFamsById = false;

    var that = this;
    $("#search-cluster-button").click(function() { that.search(that.inputObj); });
    $("#advanced-search-cluster-button").click(function() { that.search(that.advancedInputObj); });

    this.showMoreObj.click(function() {
        that.arrows.nextPage(function(isEod) {
            that.nextPageCallback(isEod);
            that.populateFilterList();
        });
    });
    
    this.showAllObj.click(function() {
        that.startProgressBar();
        that.arrows.retrieveArrowData(undefined, false, true, function(isEod) {
            that.updateMoreButtonStatus(isEod);
            that.populateFilterList();
            that.stopProgressBar();
            $('html,body').animate({scrollTop: document.body.scrollHeight},{duration:800});
        });
    });

    $("#filter-clear").click(function() {
            that.clearFilter();
        });
}

ArrowApp.prototype.search = function(inputObj) {
    this.startProgressBar();
    var idList = this.getIdList(inputObj);
    this.arrows.clearFamilyData();
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

    var that = this;
    $.each(this.fams, function(i, fam) {
        var ttText = that.showFamsById ? fam.name : fam.id;
        var famText = that.showFamsById ? fam.id : fam.name;
        var isChecked = fam.checked ? "checked" : "";

        var entry = $("<div class='filter-cb-div' data-toggle='tooltip' title='" + ttText + "'></div>").appendTo(that.filterListObj);
        $("<input id='filter-cb-" + i + "' class='filter-cb' type='checkbox' value='" + fam.id + "' " + isChecked + "/>")
            .appendTo(entry)
            .click(function(e) {
                    if (this.checked)
                        that.arrows.addPfamFilter(fam.id);
                    else
                        that.arrows.removePfamFilter(fam.id);
                });
         //$(" <span id='filter-cb-text-" + i + "' class='filter-cb-name'>" + fam.name + "</span>").
         //   appendTo(entry);
         $("<span id='filter-cb-text-" + i + "' class='filter-cb-number'><label for='filter-cb-" + i + "'>" + famText + "</label></span>").
            appendTo(entry);
    });

    if (this.firstRun) {
        $(".initial-hidden").removeClass("initial-hidden");
        this.firstRun = false;
    }
}

ArrowApp.prototype.clearFilter = function() {
    $("input.filter-cb:checked").removeAttr("checked");
    this.arrows.clearPfamFilters();
}

ArrowApp.prototype.togglePfamNamesNumbers = function(isChecked) {
    this.showFamsById = isChecked; // true == sort by ID
    this.populateFilterList();
    //$(".filter-cb-name").toggleClass("hidden");
    //$(".filter-cb-number").toggleClass("hidden"); 
}

ArrowApp.prototype.startProgressBar = function() {
    this.progressObj.removeClass("hidden");
}

ArrowApp.prototype.stopProgressBar = function() {
    this.progressObj.addClass("hidden");
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
    $('html,body').animate({scrollTop: document.body.scrollHeight},{duration:800});
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



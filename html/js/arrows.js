
var DM_COORDS = 1;
var DM_INDEX = 2;


function ArrowDiagram(canvasId, displayModeCbId, canvasContainerId, popupIds) {

    this.canvasId = canvasId;
    this.canvasContainerId = canvasContainerId;
    this.popupIds = popupIds;

    this.displayMode = DM_COORDS;
    this.diagramHeight = 70;
    this.padding = 10;
    this.S = Snap("#" + this.canvasId);
    this.fontHeight = 15;
    this.arrowHeight = 15;
    this.pointerWidth = 5;
    this.axisThickness = 1;
    this.axisBuffer = 2;
    this.colors = getColors();
    this.axisColor = "black";
    this.selectedGeneColor = "red";
    this.pfamColorMap = {};
    this.pfamColorCount = 0;

    this.diagramPage = 0;
    this.diagramCount = 0;

    this.popupElement = $("#" + this.popupIds.ParentId);

    this.arrowMap = {};
    this.pfamFilter = {};
    this.pfamList = {};

    this.idKeyQueryString = ""; 
}

ArrowDiagram.prototype.nextPage = function(callback) {
    this.diagramPage++;
    this.retrieveArrowData(this.idList, true, false, callback);
}

ArrowDiagram.prototype.retrieveArrowData = function(idList, usePaging, resetCanvas, callback) {
    if (typeof idList !== 'undefined')
        this.idList = idList;

    if (typeof this.idList !== 'undefined' && this.idList.length > 0) {
        var idListQuery = this.idList.replace(/\n/g, " ").replace(/\r/g, " ");
        var that = this;

        usePaging = typeof usePaging === 'undefined' ? false : usePaging;
        var pageString = "";
        if (usePaging)
            pageString = "&page=" + this.diagramPage;
        resetCanvas = typeof resetCanvas === 'undefined' ? false : resetCanvas;

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("GET", "get_neighbor_data.php?" + this.idKeyQueryString + "&query=" + idListQuery + pageString, true);
        xmlhttp.onload = function() {
            if (this.readyState == 4 && this.status == 200) {
                var data = JSON.parse(this.responseText);
                that.makeArrowDiagram(data, usePaging, resetCanvas);
                that.data = data;
                typeof callback === 'function' && callback(data.eod);
            }
        };
        xmlhttp.send(null);
    }
}

ArrowDiagram.prototype.retrieveFamilyData = function(callback) {
    var that = this;
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", "get_neighbor_data.php?" + this.idKeyQueryString + "&fams=1", true);
    xmlhttp.onload = function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(this.responseText);
            that.families = data.families;
            typeof callback === 'function' && callback(that.families);
        }
    };
    xmlhttp.send(null);
}

ArrowDiagram.prototype.clearFamilyData = function() {
    this.pfamList = {};
}

ArrowDiagram.prototype.hasFamilyData = function() {
    return typeof this.families !== 'undefined';
}

ArrowDiagram.prototype.getFamilies = function(sortById) {
    var fams = [];
    var keys = Object.keys(this.pfamList);
    for (var fi = 0; fi < keys.length; fi++) {
        var famId = keys[fi];
        if (famId == "none")
            continue;

        var isChecked = typeof this.pfamFilter[famId] !== 'undefined';
        fams.push({'id': famId, 'name': this.pfamList[famId], 'checked': isChecked});
    }

    if (sortById)
        fams.sort(function(a, b) { return a.id.localeCompare(b.id); });
    else
        fams.sort(function(a, b) { return a.name.localeCompare(b.name); });

    return fams;
}

ArrowDiagram.prototype.getPfamColor = function(pfam) {
    if (pfam in this.pfamColorMap) {
        return this.pfamColorMap[pfam];
    } else {
        return "transparent";
    }
}

ArrowDiagram.prototype.makeArrowDiagram = function(data, usePaging, resetCanvas) {
    canvas = document.getElementById(this.canvasId);
    
    var drawingWidth = canvas.getBoundingClientRect().width - this.padding * 2;
    if (!usePaging || resetCanvas) {
        while (canvas.hasChildNodes()) {
            canvas.removeChild(canvas.lastChild);
        }
        document.getElementById(this.canvasContainerId).setAttribute("style","width:100%;height:0px");
    }

    var i = usePaging && !resetCanvas ? this.diagramCount : 0;
//    var order = [];
////    if (data.hasOwnProperty("order")) {
////        
////    } else {
//        order = getDefaultOrder(i, data.data.length);
////    }

    //for (seqId in data.data) {
    for (var oi = 0; oi < data.data.length; oi++) {
        this.drawDiagram(canvas, i, data.data[oi], drawingWidth);
        i++;
    }
    this.diagramCount = i;
}

// Draw a diagram for a single arrow
ArrowDiagram.prototype.drawDiagram = function(canvas, index, data, drawingWidth) {
    var canvasHeight = canvas.getBoundingClientRect().height;
    var ypos = index * this.diagramHeight + this.padding * 2 + this.fontHeight;
    var extraPadding = 60; // for popup for last one
    if (ypos + this.diagramHeight + this.padding > canvasHeight)
        document.getElementById(this.canvasContainerId).setAttribute("style","width:100%;height:" + (ypos + this.diagramHeight + this.padding + extraPadding) + "px");

    // Orient the query arrows in the same direction (flip the diagram)
    var orientSameDir = true;

    var indexGeneWidth = 1 / 21;
    var geneXpos = 0,
        geneWidth = indexGeneWidth,
        geneXoffset = 0;
    var isComplement = data.attributes.direction == "complement";
    if (this.displayMode == DM_COORDS) {
        geneXpos = parseFloat(data.attributes.rel_start);
        geneWidth = parseFloat(data.attributes.rel_width);
    } else {
        var refGeneXpos = (isComplement ? 11 : 10);
        geneXpos = refGeneXpos * indexGeneWidth;
        geneXoffset = data.attributes.num - 10; 
    }

    var minXpct = 1.1, maxXpct = -0.1;

    var attrData = makeStruct(data.attributes);
    // Always face to the right which is why isComplement isn't provided, rather false is given in this call.
    var arrow = this.drawArrow(geneXpos, ypos, geneWidth, orientSameDir ? false : isComplement,
                               drawingWidth, this.selectedGeneColor, attrData);
    if (!(attrData.family in this.arrowMap))
        this.arrowMap[attrData.family] = [];
    this.arrowMap[attrData.family].push(arrow);
    this.pfamList[attrData.family] = attrData.family_desc;
    this.pfamColorMap[attrData.family] = this.selectedGeneColor;

    var isBound = attrData.is_bound;
    minXpct = (geneXpos < minXpct) ? geneXpos : minXpct;
    maxXpct = (geneXpos+geneWidth > maxXpct) ? geneXpos+geneWidth : maxXpct;

    var max_start = 0;
    for (var i = 0; i < data.neighbors.length; i++) {
        var N = data.neighbors[i];
        var neighborXpos = 0;
        var neighborWidth = indexGeneWidth;
        var nIsComplement = N.direction == "complement";

        if (this.displayMode == DM_COORDS) {
            neighborXpos = parseFloat(N.rel_start);
            neighborWidth = parseFloat(N.rel_width);
            if (orientSameDir && isComplement) {
                nIsComplement = !nIsComplement;
                neighborXpos = 1.0 - neighborXpos - neighborWidth + geneWidth;
            }
        } else {
            neighborXpos = (parseInt(N.num) - geneXoffset) * indexGeneWidth;
            if (nIsComplement)
                neighborXpos += indexGeneWidth;
        }
        minXpct = (neighborXpos < minXpct) ? neighborXpos : minXpct;
        maxXpct = (neighborXpos+neighborWidth > maxXpct) ? neighborXpos+neighborWidth : maxXpct;

        var attrData = makeStruct(N);

        var color = "grey";
        var pfam = N.family;
        if (attrData.color.length > 0) {
            color = this.pfamColorMap[pfam] = attrData.color;
        } else if (pfam.length > 0) {
            if (pfam in this.pfamColorMap) {
                color = this.pfamColorMap[pfam];
            } else {
                var colorIndex = Math.floor(Math.random() * this.colors.length);
                var colorIndex = this.pfamColorCount++ % this.colors.length;
                color = this.pfamColorMap[pfam] = this.colors[colorIndex];
            }
        }

        arrow = this.drawArrow(neighborXpos, ypos, neighborWidth, nIsComplement, drawingWidth, color, attrData);

        if (!(attrData.family in this.arrowMap))
            this.arrowMap[attrData.family] = [];
        this.arrowMap[attrData.family].push(arrow);
        this.pfamList[attrData.family] = attrData.family_desc;
        
        if (i == 0)
            max_start = N.start;
        else
            max_stop = N.stop;
    }
    
    this.drawAxis(ypos, drawingWidth, minXpct, maxXpct, isBound);

    data.attributes.max_start = max_start;
//    data.attributes.max_stop = max_stop;
    this.drawTitle(index * this.diagramHeight + this.fontHeight - 2, data.attributes);
}

function getDefaultOrder(start, numElements) {
    var order = [];
    for (var i = start; i < start+numElements; i++) {
        order.push(i);
    }
    return order;
}

function makeStruct(data) {
    struct = {
        "accession": data.accession,
        "family": data.family,
        "id": data.id,
        "gene_direction": data.direction,
        "seq_len": data.seq_len,
        "start": data.start,
        "stop": data.stop,
        "num": data.num,
        "anno_status": data.anno_status,
        "family_desc": data.family_desc,
        "desc": data.desc,
    };
    
    if (data.hasOwnProperty("cluster_num"))
        struct.cluster_num = data.cluster_num;
    
    if (data.hasOwnProperty("is_bound"))
        struct.is_bound = data.is_bound;
    else
        struct.is_bound = 0;

    if (data.hasOwnProperty("strain"))
        struct.strain = data.strain;

    if (data.hasOwnProperty("color") && data.color != null)
        struct.color = data.color;
    else
        struct.color = "";

    return struct;
}

ArrowDiagram.prototype.drawAxis = function(ypos, drawingWidth, minXpct, maxXpct, isBound) {
    ypos = ypos + this.axisThickness - 1;
    // A single line, full width:
    //this.S.paper.line(this.padding, ypos, this.padding + drawingWidth, ypos).attr({ 'stroke': this.axisColor, 'strokeWidth': this.axisThickness });
    this.S.paper.line(this.padding + minXpct * drawingWidth - 3, ypos,
                      this.padding + maxXpct * drawingWidth + 3, ypos)
        .attr({ 'stroke': this.axisColor, 'strokeWidth': this.axisThickness });
    
    if (isBound & 1) { // end of contig on left
        this.S.paper.line(this.padding + minXpct * drawingWidth - 3, ypos - 5,
                          this.padding + minXpct * drawingWidth - 3, ypos + 5)
            .attr({ 'stroke': this.axisColor, 'strokeWidth': this.axisThickness });
    } else if (minXpct > 0) {
        this.S.paper.line(this.padding, ypos,
                          this.padding + minXpct * drawingWidth - 3, ypos)
            .attr({ 'stroke': this.axisColor, 'strokeWidth': this.axisThickness, 'stroke-dasharray': '2px, 4px' });
    }
    
    if (isBound & 2) { // end of contig on right
        this.S.paper.line(this.padding + maxXpct * drawingWidth + 3, ypos - 5,
                          this.padding + maxXpct * drawingWidth + 3, ypos + 5)
            .attr({ 'stroke': this.axisColor, 'strokeWidth': this.axisThickness });
    } else if (minXpct > 0) {
        this.S.paper.line(this.padding + maxXpct * drawingWidth + 3, ypos,
                          this.padding + drawingWidth, ypos)
            .attr({ 'stroke': this.axisColor, 'strokeWidth': this.axisThickness, 'stroke-dasharray': '2px, 4px' });
    }
}

ArrowDiagram.prototype.drawTitle = function(ypos, data) {
    var title = "";
    if (data.hasOwnProperty("organism"))
        title = data.organism + "; ";
    if (data.hasOwnProperty("taxon_id"))
        title = title + "NCBI Taxon ID: " + data.taxon_id;
    if (data.hasOwnProperty("id"))
        title = title + "; ENA ID: " + data.id;
    if (data.hasOwnProperty("cluster_num"))
        title = title + "; Cluster: " + data.cluster_num;
//    if (title.length == 0)
//        title = data.strain + " [global width = " + (data.max_stop - data.max_start) + "]";
//    if (data.hasOwnProperty("evalue") && data.evalue >= 0)
//        title = title + "; e-value: " + data.evalue;
//    if (data.hasOwnProperty("pid") && data.pid >= 0)
//        title = title + "; %ID: " + data.pid;
    if (title.length > 0) {
        var textObj = this.S.paper.text(this.padding, ypos, title);
        //textObj.attr({'font-size':12});
        textObj.attr({'style':'diagram-title'});
    }
}

// xpos is in percent (0-1)
// ypos is in pixels from top
// width is in percent (0-1)
// drawingWidth is in pixels and is the area of the canvas in which we can draw (need to add padding to it to get proper coordinate)
ArrowDiagram.prototype.drawArrow = function(xpos, ypos, width, isComplement, drawingWidth, color, attrData) {
    // Upper left and right of rect. portion of arrow
    coords = [];
    llx = lrx = urx = ulx = px = 0;
    lly = lry = ury = uly = py = 0;
    if (!isComplement) {
        // lower left of rect. portion
        llx = this.padding + xpos * drawingWidth;
        lly = ypos - this.axisThickness - this.axisBuffer;
        // lower right of rect. portion
        lrx = this.padding + (xpos + width) * drawingWidth - this.pointerWidth;
        lry = lly;
        // pointer of arrow, facing right
        px = this.padding + (xpos + width) * drawingWidth;
        py = ypos - this.axisThickness - this.axisBuffer - this.arrowHeight / 2;
        // upper right of rect. portion
        urx = lrx; //this.padding + (xpos + width) * drawingWidth - this.pointerWidth;
        ury = ypos - this.arrowHeight - this.axisThickness - this.axisBuffer; 
        // upper left of rect. portion
        ulx = llx; //this.padding + xpos * drawingWidth;
        uly = ury;

        if (llx > lrx) {
            lrx = llx;
            urx = ulx;
        }

        coords = [llx, lly, lrx, lry, px, py, urx, ury, ulx, uly];
    } else { // pointing left
        // pointer of arrow, facing left
        //px = this.padding + (xpos - width) * drawingWidth;
        px = this.padding + xpos * drawingWidth;
        py = ypos + this.axisThickness + this.axisBuffer + this.arrowHeight / 2;
        // lower left of rect. portion
        //llx = this.padding + (xpos - width) * drawingWidth + this.pointerWidth;
        llx = this.padding + xpos * drawingWidth + this.pointerWidth;
        lly = ypos + this.axisThickness + this.axisBuffer + this.arrowHeight;
        // lower right of rect. portion
        //lrx = this.padding + xpos * drawingWidth;
        lrx = this.padding + (xpos + width) * drawingWidth;
        lry = lly;
        // upper right of rect. portion
        urx = lrx; //this.padding + (xpos + width) * drawingWidth - this.pointerWidth;
        ury = ypos + this.axisThickness + this.axisBuffer;
        // upper left of rect. portion
        ulx = llx; //this.padding + xpos * drawingWidth + this.pointerWidth;
        uly = ury;

        if (llx > lrx) {
            llx = lrx;
            ulx = urx;
        }

        coords = [px, py, llx, lly, lrx, lry, urx, ury, ulx, uly];
    }

    attrData.fill = color;
    attrData.fillColor = color;
    attrData.cx = ulx + (urx - ulx) / 2;
    attrData.cy = lly; //py;
    attrData.class = "an-arrow";
    var arrow = this.S.paper.polygon(coords).attr(attrData);

    arrow.click(function(event) {
        window.open("http://uniprot.org/uniprot/" + this.attr("accession"));
    });

    var that = this;
    var pos = $("#" + this.canvasId).offset();

    arrow.mouseover(function(e) {
        var cx = parseInt(this.attr("cx"));
        var cy = parseInt(this.attr("cy"));
        that.doPopup(pos.left + cx, pos.top + cy + 1, true, this);
    });
    arrow.mouseout(function(e) {
        that.doPopup(e.pageX, e.pageY, false, null);
    });

    // There are filters applied
    if (Object.keys(this.pfamFilter).length > 0) {
        if (attrData.family in this.pfamFilter) {
            arrow.addClass("an-arrow-selected");
        } else {
            arrow.addClass("an-arrow-mute");
        }
    }

    return arrow;
}

ArrowDiagram.prototype.addPfamFilter = function(pfam) {
    this.pfamFilter[pfam] = 1;

    $(".an-arrow").addClass("an-arrow-mute");

    for (pf in this.pfamFilter) {
        for (idx in this.arrowMap[pf]) {
            var arrow = this.arrowMap[pf][idx];
            arrow.addClass("an-arrow-selected");
        }
    }
}

ArrowDiagram.prototype.removePfamFilter = function(pfam) {
    delete this.pfamFilter[pfam];

    for (idx in this.arrowMap[pfam]) {
        var arrow = this.arrowMap[pfam][idx];
        //var fillColor = arrow.attr("fillColor");
        //arrow.attr({fill: fillColor, stroke: "none", strokeWidth: 0, class: "an-arrow-mute"});
        arrow.removeClass("an-arrow-selected");
    }
    
    if (Object.keys(this.pfamFilter).length == 0) {
        $(".an-arrow").removeClass("an-arrow-mute");
    }
}

ArrowDiagram.prototype.clearPfamFilters = function() {
    var pfams = Object.keys(this.pfamFilter);
    for (var i = 0; i < pfams.length; i++) {
        this.removePfamFilter(pfams[i]);
    }
    $(".an-arrow-mute").toggleClass("an-arrow-mute");
}

ArrowDiagram.prototype.doPopup = function(xPos, yPos, doShow, data) {
    
    if (doShow) {
        family = data.attr("family");
        this.popupElement.css({top: yPos, left: xPos});
        if (!family || family.length == 0) family = "none";
        $("#" + this.popupIds.IdId + " span").text(data.attr("accession"));
        $("#" + this.popupIds.DescId + " span").text(data.attr("desc"));
        $("#" + this.popupIds.FamilyId + " span").text(data.attr("family"));
        $("#" + this.popupIds.FamilyDescId + " span").text(data.attr("family_desc"));
        $("#" + this.popupIds.SpTrId + " span").text(data.attr("anno_status"));
        $("#" + this.popupIds.SeqLenId + " span").text(data.attr("seq_len"));
        //this.popupElement.show();
        this.popupElement.removeClass("hidden");
    } else {
        //this.popupElement.hide();
        this.popupElement.addClass("hidden");
    }
}

function getColors() {
    var colors = [
/*        //"crimson",
        "bisque",
        "blue",
        "blueviolet",
        "brown",
        "cadetblue",
        "chartreuse",
        "chocolate",
        "coral",
        "cornflowerblue",
        "cyan",
        "darkblue",
        "darkgreen",
        "darkorange",
        "darkslateblue",
        "gold",
        "hotpink",
        "greenyellow",
        "lightskyblue",
        "maroon",
        "olive",
        "purple",
        "sienna",
        "teal",
        "turquoise",
        "violet"
        */
        "Pink",
        "HotPink",
        "DeepPink",
        "PaleVioletRed",
        "Salmon",
        "DarkSalmon",
        "LightCoral",
        "IndianRed",
        "DarkRed",
        "OrangeRed",
        "Tomato",
        "Coral",
        "DarkOrange",
        "Orange",
        "DarkKhaki",
        "Gold",
        "BurlyWood",
        "Tan",
        "RosyBrown",
        "SandyBrown",
        "Goldenrod",
        "DarkGoldenrod",
        "Peru",
        "Chocolate",
        "SaddleBrown",
        "Sienna",
        "Brown",
        "Maroon",
        "DarkOliveGreen",
        "Olive",
        "OliveDrab",
        "YellowGreen",
        "LimeGreen",
        "Lime",
        "LightGreen",
        "DarkSeaGreen",
        "MediumAquamarine",
        "MediumSeaGreen",
        "SeaGreen",
        "Green",
        "DarkGreen",
        "Cyan",
        "Turquoise",
        "LightSeaGreen",
        "CadetBlue",
        "Teal",
        "LightSteelBlue",
        "SkyBlue",
        "DeepSkyBlue",
        "DodgerBlue",
        "CornflowerBlue",
        "SteelBlue",
        "RoyalBlue",
        "Blue",
        "MediumBlue",
        "DarkBlue",
        "Navy",
        "MidnightBlue",
        "Thistle",
        "Plum",
        "Violet",
        "Orchid",
        "Fuchsia",
        "MediumOrchid",
        "MediumPurple",
        "BlueViolet",
        "DarkViolet",
        "DarkOrchid",
        "Purple",
        "Indigo",
        "DarkSlateBlue",
        "SlateBlue",
        "LightSlateGray",
        "DarkSlateGray",
    ];
    return colors;
}

ArrowDiagram.prototype.setJobInfo = function(idKeyQueryString) {
    this.idKeyQueryString = idKeyQueryString; 
}


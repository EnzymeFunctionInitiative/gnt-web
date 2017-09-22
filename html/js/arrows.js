
var DM_COORDS = 1;
var DM_INDEX = 2;


function PopupIds(parentId, idId, familyId, coordsId, seqLenId, directionId, indexId) {
    this.ParentId = parentId;
    this.IdId = idId;
    this.FamilyId = familyId;
    this.CoordsId = coordsId;
    this.DirectionId = directionId;
    this.SeqLenId = seqLenId;
    this.IndexId = indexId;
}

function ArrowDiagram(canvasId, displayModeCbId, canvasContainerId, popupIds, inputId, controlsId, progressId) {

    this.inputId = inputId;
    this.canvasId = canvasId;
    this.displayModeCbId = displayModeCbId;
    this.canvasContainerId = canvasContainerId;
    this.popupIds = popupIds;
    this.controlsId = controlsId;
    this.progressId = progressId;

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

    this.popupElement = $("#" + this.popupIds.ParentId);
    this.popupElement.css({position:"absolute"});
    this.popupElement.hide();

    this.updateDisplayMode();
}

ArrowDiagram.prototype.toggleDisplayMode = function() {
    this.updateDisplayMode();
    this.makeArrowDiagram(this.data);
}

ArrowDiagram.prototype.updateDisplayMode = function() {
    if (document.getElementById(this.displayModeCbId).checked) {
        this.displayMode = DM_COORDS;
    } else {
        this.displayMode = DM_INDEX;
    }
}

ArrowDiagram.prototype.getArrowData = function() {
    var idList = document.getElementById(this.inputId).value;

    if (idList.length > 0) {
        var idListQuery = idList.replace(/\n/g, " ").replace(/\r/g, " ");
        var that = this;

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("GET", "get_neighbor_data.php?id=" + this.jobId + "&key=" + this.jobKey + "&query=" + idListQuery, true);
        xmlhttp.onload = function() {
            if (this.readyState == 4 && this.status == 200) {
                var data = JSON.parse(this.responseText);
                that.makeArrowDiagram(data);
                that.data = data;
                $("#" + that.controlsId + " button,textarea,input").prop("disabled", false);
                $("#" + that.progressId).css({visibility: "hidden"});
            }
        };
        $("#" + this.controlsId + " button,textarea,input").prop("disabled", true);
        $("#" + this.progressId).css({visibility: "visible"});
        xmlhttp.send(null);
    }
}

ArrowDiagram.prototype.makeArrowDiagram = function(data) {
    canvas = document.getElementById(this.canvasId);
    var drawingWidth = canvas.getBoundingClientRect().width - this.padding * 2;
    while (canvas.hasChildNodes()) {
        canvas.removeChild(canvas.lastChild);
    }
    var i = 0;
    for (seqId in data.data) {
        this.drawDiagram(canvas, i, data.data[seqId], drawingWidth);
        i++;
    }
}

// Draw a diagram for a single arrow
ArrowDiagram.prototype.drawDiagram = function(canvas, index, data, drawingWidth) {
    var canvasHeight = canvas.getBoundingClientRect().height;
    var ypos = index * this.diagramHeight + this.padding + this.fontHeight;
    if (ypos + this.diagramHeight + this.padding > canvasHeight)
        document.getElementById(this.canvasContainerId).setAttribute("style","width:900px;height:" + (ypos + this.diagramHeight + this.padding) + "px");
    this.drawAxis(ypos, drawingWidth);

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

    var attrData = makeStruct(data.attributes);
    var arrow = this.drawArrow(geneXpos, ypos, geneWidth, isComplement, drawingWidth, this.selectedGeneColor, attrData);

    for (var i = 0; i < data.neighbors.length; i++) {
        var N = data.neighbors[i];
        var neighborXpos = 0;
        var neighborWidth = indexGeneWidth;
        var isComplement = N.direction == "complement";

        if (this.displayMode == DM_COORDS) {
            neighborXpos = parseFloat(N.rel_start);
            neighborWidth = parseFloat(N.rel_width);
        } else {
            neighborXpos = (parseInt(N.num) - geneXoffset) * indexGeneWidth;
            if (isComplement)
                neighborXpos += indexGeneWidth;
        }

        //var color = colors[i % colors.length];
        var color = "grey";
        var pfam = N.family;
        if (pfam.length > 0) {
            if (pfam in this.pfamColorMap)
                color = this.pfamColorMap[pfam];
            else
                color = this.pfamColorMap[pfam] = this.colors[this.pfamColorCount++ % this.colors.length];
        }
        var attrData = makeStruct(N);
        arrow = this.drawArrow(neighborXpos, ypos, neighborWidth, isComplement, drawingWidth, color, attrData);
    }
}

function makeStruct(data) {
    return {
        "accession": data.accession,
        "family": data.family,
        "id": data.id,
        "gene_direction": data.direction,
        "seq_len": data.seq_len,
        "start": data.start,
        "stop": data.stop,
        "strain": data.strain,
        "num": data.num,
    };
}

ArrowDiagram.prototype.drawAxis = function(ypos, drawingWidth) {
    ypos = ypos + this.axisThickness - 1;
    this.S.paper.line(this.padding, ypos, this.padding + drawingWidth, ypos).attr({ 'stroke': this.axisColor, 'strokeWidth': this.axisThickness });
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
        px = this.padding + (xpos - width) * drawingWidth;
        py = ypos + this.axisThickness + this.axisBuffer + this.arrowHeight / 2;
        // lower left of rect. portion
        llx = this.padding + (xpos - width) * drawingWidth + this.pointerWidth;
        lly = ypos + this.axisThickness + this.axisBuffer + this.arrowHeight;
        // lower right of rect. portion
        lrx = this.padding + xpos * drawingWidth;
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
    attrData.cx = ulx + (urx - ulx) / 2;
    attrData.cy = lly; //py;
    var arrow = this.S.paper.polygon(coords).attr(attrData);

    arrow.click(function(event) {
        window.location.href = "http://uniprot.org/uniprot/" + this.attr("id");
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

    return arrow;
}

ArrowDiagram.prototype.doPopup = function(xPos, yPos, doShow, data) {
    
    if (doShow) {
        family = data.attr("family");
        this.popupElement.css({top: yPos, left: xPos});
        if (!family || family.length == 0) family = "none";
        $("#" + this.popupIds.IdId + " span").text(data.attr("accession"));
        $("#" + this.popupIds.FamilyId + " span").text(data.attr("family"));
        $("#" + this.popupIds.CoordsId + " span").text(data.attr("start") + "-" + data.attr("stop"));
        $("#" + this.popupIds.SeqLenId + " span").text(data.attr("seq_len"));
        $("#" + this.popupIds.DirectionId + " span").text(data.attr("gene_direction"));
        $("#" + this.popupIds.IndexId + " span").text(data.attr("num"));
        this.popupElement.show();
    } else {
        this.popupElement.hide();
    }
}

function getColors() {
    var colors = [
        "crimson",
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
    ];
    return colors;
}

ArrowDiagram.prototype.setJobInfo = function(jobId, jobKey) {
    this.jobId = jobId;
    this.jobKey = jobKey;
}


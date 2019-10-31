define(['jquery'], function ($) {
    var mousePressed;
    var lastX, lastY;
    var ctx;
    var canvas;
    return {
        init: function () {
            initialize();
        },
        save: function () {
            initialize();
        }

    };
});



function initialize() {

    canvas = document.getElementById('canvas');
    ctx = canvas.getContext('2d');
var rect = canvas.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = rect.height;
// setup lines styles .. 
ctx.strokeStyle = "#000";
ctx.lineWidth = 1;

// some variables we'll need .. 
var drawing = false;
var mousePos = {x:0, y:0};
var lastPos = mousePos;
var isMobile = ('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch;

$('#clearCanvas').bind('click', function () {
    clearCanvas(canvas, ctx);
});
$('#id_submitbutton').click(function () {
        var data = $('#canvas')[0].toDataURL(); // Change here
        $('[name="signing"]').val(data);
    });
// mouse/touch events ..
canvas.addEventListener((isMobile ? 'touchstart' : 'mousedown'), function(e) {
    e.preventDefault();
    e.stopPropagation();
    drawing = true;
    lastPos = getMousePos(canvas, e);
    mousePos = lastPos;
});
canvas.addEventListener((isMobile ? 'touchmove' : 'mousemove'), function(e) {
    e.preventDefault();
   e.stopPropagation();
    mousePos = getMousePos(canvas, e);
});
canvas.addEventListener((isMobile ? 'touchend' : 'mouseup'), function(e) {
    e.preventDefault();
    e.stopPropagation();
    drawing = false;
});

document.body.addEventListener("touchstart", function (e) {
  if (e.target == canvas) {
    e.preventDefault();
    e.stopPropagation();
  }
}, false);
document.body.addEventListener("touchend", function (e) {
  if (e.target == canvas) {
    e.preventDefault();
    e.stopPropagation();
  }
}, false);
document.body.addEventListener("touchmove", function (e) {
  if (e.target == canvas) {
    e.preventDefault();
    e.stopPropagation();
  }
}, false);



// helper functions .. 
function getMousePos(canvasDom, touchOrMouseEvent) {
    var rect = canvasDom.getBoundingClientRect();
    return {
        x: (isMobile ? touchOrMouseEvent.touches[0].clientX : touchOrMouseEvent.clientX) - rect.left,
        y: (isMobile ? touchOrMouseEvent.touches[0].clientY : touchOrMouseEvent.clientY) - rect.top
    };
};

// drawing .. 
window.requestAnimFrame = (function(callback) {
    return  window.requestAnimationFrame ||
            window.webkitRequestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            window.oRequestAnimationFrame ||
            window.msRequestAnimationFrame ||
            function(callback) {
                window.setTimeout(callback, 1000/60);
            };
})();

function renderCanvas() {
    if (drawing) {
        ctx.moveTo(lastPos.x, lastPos.y);
        ctx.lineTo(mousePos.x, mousePos.y);
        ctx.stroke();
        lastPos = mousePos;
    }
};

(function drawLoop() {
    requestAnimFrame(drawLoop);
    renderCanvas();
})();

$(window).resize(function () {
    var W = canvas.width, H = canvas.height;
    var temp = ctx.getImageData(0,0,W,H);
    var rect = canvas.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = rect.height;
     W = canvas.width, H = canvas.height
    ctx.putImageData(temp,0,0);
});



function clearCanvas(canvas, ctx) {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    canvas.width = canvas.width;
}

function downloadCanvas(canvas) {
    this.href = canvas.toDataURL();

}

function save() {
    this.href = canvas.toDataURL();
    
    if(this.href == document.getElementById('blank').toDataURL()){

    $('#signing_text').val("");
    }
    else{
    $('#signing_text').val("this.href");
    }
}
}
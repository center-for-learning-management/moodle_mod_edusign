define(['jquery'], function($) {
    var mousePressed;
    var lastX, lastY;
    var ctx;
    var canvas;
    return {
        init: function() {
            initialize();
        },
        save: function() {
            initialize();
        }

    };
});


function initialize() {

let canvas = document.getElementById('canvas');
let ctx = canvas.getContext('2d');
let rect = canvas.getBoundingClientRect();
canvas.width = rect.width;
canvas.height = rect.height;
// Setup lines styles .. 
ctx.strokeStyle = "#000";
ctx.lineWidth = 1;
    clearCanvas(canvas, ctx);

// Some variables we'll need .. 
let drawing = false;
let mousePos = {x: 0, y: 0};
let lastPos = mousePos;
let isMobile = ('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch;


$('#clearCanvas').bind('click', function() {
    clearCanvas(canvas, ctx);
});
$('#id_submitbutton').click(function() {
        var data = $('#canvas')[0].toDataURL(); // Change here
        $('[name="signing"]').val(data);
    });
// Mouse/touch events ..
    canvas.addEventListener('pointerdown', function(e) {
        e.preventDefault();
        e.stopPropagation();
        drawing = true;
        console.log(e.clientX);
        lastPos = getMousePos(canvas, e);
        mousePos = lastPos;
    }, false);
    canvas.addEventListener('pointermove', function(e) {
        e.preventDefault();
        e.stopPropagation();
        mousePos = getMousePos(canvas, e);
    }, false);
    canvas.addEventListener('pointerup', function(e) {
        e.preventDefault();
        e.stopPropagation();
        drawing = false;
    }, false);
 
    document.body.addEventListener("pointerdown", function(e) {
      if (e.target == canvas) {
        e.preventDefault();
        e.stopPropagation();
      }
    }, false);
    document.body.addEventListener("pointermove", function(e) {
      if (e.target == canvas) {
        e.preventDefault();
        e.stopPropagation();
      }
    }, false);
    document.body.addEventListener("pointerup", function(e) {
      if (e.target == canvas) {
        e.preventDefault();
        e.stopPropagation();
      }
    }, false);
/*
    canvas.addEventListener((isMobile ? 'touchstart' : 'mousedown'), function(e) {
        e.preventDefault();
        e.stopPropagation();
        drawing = true;
        lastPos = getMousePos(canvas, e);
        mousePos = lastPos;
    }, false);
    canvas.addEventListener((isMobile ? 'touchmove' : 'mousemove'), function(e) {
        e.preventDefault();
       e.stopPropagation();
        mousePos = getMousePos(canvas, e);
    }, false);
    canvas.addEventListener((isMobile ? 'touchend' : 'mouseup'), function(e) {
        e.preventDefault();
        e.stopPropagation();
        drawing = false;
    }, false);

    document.body.addEventListener("touchstart", function(e) {
      if (e.target == canvas) {
        e.preventDefault();
      }
    }, false);
    document.body.addEventListener("touchend", function(e) {
      if (e.target == canvas) {
        e.preventDefault();
      }
    }, false);
    document.body.addEventListener("touchmove", function(e) {
      if (e.target == canvas) {
        e.preventDefault();
      }
    }, false);
*/

// Helper functions .. 
function getMousePos(canvasDom, touchOrMouseEvent) {
    var rect = canvasDom.getBoundingClientRect();
        console.log(touchOrMouseEvent.clientX);
    return {   
        x: touchOrMouseEvent.clientX - rect.left,
        y: touchOrMouseEvent.clientY - rect.top
    };
}
/*
function getTouchPos(canvasDom, touchOrMouseEvent) {
    var rect = canvasDom.getBoundingClientRect();
        console.log(touchOrMouseEvent.touches[0].clientX);
    return {   
        x: touchOrMouseEvent.touches[0].clientX - rect.left,
        y: touchOrMouseEvent.touches[0].clientY - rect.top
    };
}*/

// Drawing .. 
window.requestAnimFrame = (function(callback) {
    return window.requestAnimationFrame ||
            window.webkitRequestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            window.oRequestAnimationFrame ||
            window.msRequestAnimationFrame ||
            function(callback) {
                window.setTimeout(callback, 1000 / 60);
            };
})();

function renderCanvas() {
    if (drawing) {
        ctx.moveTo(lastPos.x, lastPos.y);
        ctx.lineTo(mousePos.x, mousePos.y);
        ctx.stroke();
        if ((lastPos.x == mousePos.x) && (lastPos.y == mousePos.y)) {
            ctx.moveTo(lastPos.x, lastPos.y);
            ctx.arc(lastPos.x, lastPos.y, 0.5, 0, 2 * Math.PI, false);
        }
        lastPos = mousePos;

    }
}

(function drawLoop() {
    requestAnimFrame(drawLoop);
    renderCanvas();
})();

$(window).resize(function() {
    var W = canvas.width,
H = canvas.height;
    var temp = ctx.getImageData(0, 0, W, H);
    var rect = canvas.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = rect.height;
     W = canvas.width, H = canvas.height;
    ctx.putImageData(temp, 0, 0);
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

    if (this.href == document.getElementById('blank').toDataURL()) {

    $('#signing_text').val("");
    } else {
    $('#signing_text').val("this.href");
    }
}
}
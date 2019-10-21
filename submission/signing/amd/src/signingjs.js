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
    ctx = canvas.getContext("2d");
    var rect = canvas.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = rect.height;
    mousePressed = false;
    canvas.addEventListener("touchstart", function (e) {
        mousePos = getTouchPos(canvas, e);
        var touch = e.touches[0];
        var mouseEvent = new MouseEvent("mousedown", {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
    }, false);
    canvas.addEventListener("touchend", function (e) {
      var mouseEvent = new MouseEvent("mouseup", {});
      canvas.dispatchEvent(mouseEvent);
  }, false);
    canvas.addEventListener("touchmove", function (e) {
      var touch = e.touches[0];
      var mouseEvent = new MouseEvent("mousemove", {
        clientX: touch.clientX,
        clientY: touch.clientY
    });
      canvas.dispatchEvent(mouseEvent);
  }, false);

// Get the position of a touch relative to the canvas
function getTouchPos(canvasDom, touchEvent) {
  var rect = canvasDom.getBoundingClientRect();
  return {
    x: touchEvent.touches[0].clientX - rect.left,
    y: touchEvent.touches[0].clientY - rect.top
};
}

$('#clearCanvas').bind('click', function () {
    clearCanvas(canvas, ctx);
});
$('#id_submitbutton').click(function () {
        var data = $('#canvas')[0].toDataURL(); // Change here
        $('[name="signing"]').val(data);
    });
$('#canvas').mousedown(function (e) {
    mousePressed = true;
    Draw(e.pageX - $(this).offset().left, e.pageY - $(this).offset().top, false);
});

$('#canvas').mousemove(function (e) {
    if (mousePressed) {
        Draw(e.pageX - $(this).offset().left, e.pageY - $(this).offset().top, true);
    }
});

$('#canvas').mouseup(function (e) {
    mousePressed = false;
});
$('#canvas').mouseleave(function (e) {
    mousePressed = false;
});

$(window).resize(function () {
    var dataURL = canvas.toDataURL();
    console.log(dataURL);
    var rect = canvas.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = rect.height;
        /*var image = new Image
image.src = dataURL;
image.onload = function(){
   ctx.drawImage(image,10,10)
}*/

});
}

function Draw(x, y, isDown) {
    if (isDown) {
        ctx.beginPath();
        ctx.lineWidth = 2;
        ctx.lineJoin = "round";
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(x, y);
        ctx.closePath();
        ctx.stroke();
    }
    lastX = x;
    lastY = y;
}

function clearCanvas(canvas, ctx) {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

function downloadCanvas(canvas) {
    this.href = canvas.toDataURL();

}

function save() {
    this.href = canvas.toDataURL();
    if(isCanvasBlank(canvas)){
      console.log("true");
    }
    $('#signing_text').val("this.href");
}

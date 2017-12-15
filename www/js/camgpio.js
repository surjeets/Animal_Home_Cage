var mjpeg_img;
function reload_img () {
  mjpeg_img.src = "cam_pic.php?time=" + new Date().getTime();
}
function error_img () {
  setTimeout("mjpeg_img.src = 'cam_pic.php?time=' + new Date().getTime();", 100);
}
function init() {
  mjpeg_img = document.getElementById("mjpeg_dest");
  mjpeg_img.onload = reload_img;
  mjpeg_img.onerror = error_img;
  reload_img();
}

var pin;
    var request = new XMLHttpRequest();
    request.onreadystatechange = function() {
        if (request.readyState === 4 && request.status === 200);
    };

function load(){
        setTimeout('init();', 100);
        request.open("GET","out.php", true);
        request.send(null);
        return false;
}

function tog(pin){
        request.open("GET","toggle.php?call="+pin, true);
        request.send(null);
        return false;
}


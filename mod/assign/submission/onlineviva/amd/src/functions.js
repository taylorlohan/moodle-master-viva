window.onload=function (){
    /*let constraintObj = {
        audio: true,
        video: {
            facingMode: "user",
            width: { min: 640, ideal: 1280, max: 1920 },
            height: { min: 480, ideal: 720, max: 1080 }
        }
    };*/
//startRecording();
    var canvas=document.getElementById('canvas');
    let chunks = [];
    var width = 320;
    var height = 0;
    let c = 10;
    //let recorder;
    //let t;
    let photo = document.getElementById('photo');
    var options = {audio: true, video: {
            facingMode: "user",
            width: { min: 640, ideal: 1280, max: 1280 },
            height: { min: 480, ideal: 720, max: 720 }
        }};
    init();

    function init() {
        // eslint-disable-next-line no-undef
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        navigator.mediaDevices.getUserMedia(options)
            .then(function(mediaStream) {
                var srcvideo = document.getElementById("srcvideo")
                srcvideo.srcObject = mediaStream;
                srcvideo.play();
                //playCanvas(srcvideo, ctx)
            });

        setRecorder();
        setFormatSelect('video/webm;codecs=vp9');
    }

    let audio = document.getElementById('aud');
//let mediaRecorder = new MediaRecorder(mediaStreamObj);


    function setFormatSelect(format){
        if(!MediaRecorder.isTypeSupported(format)){
            alert(format);
            alert("当前浏览器不支持该编码类型");
            return;
        }
        chunks = [];
        setRecorder(format);
    }

    function setRecorder(format) {
        let start = document.getElementById('btnstart');
        let stop = document.getElementById('btnstart');
        const stream = canvas.captureStream(60); // 60 FPS recording
        const recorder = new MediaRecorder(stream, {
            mimeType: format
        });
        recorder.ondataavailable = e => {
            chunks.push(
                e.data
            );
        };

        start.disabled = false;
        start.onclick = e => {
            recorder.start(10);
            start.disabled = true;
            stop.disabled = false;
        };
        stop.onclick = e => {
            recorder.stop();
            blobDownload(format);
            start.disabled = false;
            stop.disabled = true;
        };


    }

    function blobDownload(format) {
        const link = document.createElement('a');
        link.style.display = 'none';
        const fullBlob = new Blob(chunks);
        const downloadUrl = window.URL.createObjectURL(fullBlob);
        link.href = downloadUrl;
        link.download = 'media - '+format+'.mp4';
        document.body.appendChild(link);
        link.click();
        link.remove();
    }
    /*function timedCount() {
        document.querySelector(".count").innerHTML ='time left is'+ c;
        c = c - 1;
        if(c>0){
            let t = setTimeout(timedCount, 1000);
        }else {
            if (recorder != null) {
                //设置后不会崩
                recorder.setOnErrorListener(null);
                recorder.setPreviewDisplay(null);
                try {
                    recorder.stop();
                } catch (IllegalStateException e) {
                    Log.w("Yixia", "stopRecord", e);
                } catch (RuntimeException e) {
                    Log.w("Yixia", "stopRecord", e);
                } catch (Exception e) {
                    Log.w("Yixia", "stopRecord", e);
                }
            }
            //recorder.stop();
            document.querySelector(".count").innerHTML = "Time is up!";
            return;
        }
    }
    function timedPic() {
        setTimeout(takepicture, 10000);
    }
    function takepicture() {
        var context = canvas.getContext('2d');
        if (width && height) {
            canvas.width = width;
            canvas.height = height;
            context.drawImage(video, 0, 0, width, height);

            var data = canvas.toDataURL('image/png');
            photo.setAttribute('src', data);
        } else {
            clearphoto();
        }
    }

    function clearphoto() {
        var context = canvas.getContext('2d');
        context.fillStyle = "#AAA";
        context.fillRect(0, 0, canvas.width, canvas.height);

        var data = canvas.toDataURL('image/png');
        photo.setAttribute('src', data);
    }
*/



};
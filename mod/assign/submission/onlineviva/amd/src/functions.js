window.onload=function (){
    let constraintObj = {
        audio: true,
        video: {
            facingMode: "user",
            width: { min: 640, ideal: 1280, max: 1920 },
            height: { min: 480, ideal: 720, max: 1080 }
        }
    };
//startRecording();
    var canvas=$('#canvas');
    var width = 320;
    var height = 0;
    let c = 10;
    let recorder;
    //let t;
    let photo = document.getElementById('photo');
    var recordmethods = {
        //let recorder = null;
        startRecording: function () {
            if (navigator.mediaDevices === undefined) {
                navigator.mediaDevices = {};
                navigator.mediaDevices.getUserMedia = function (constraintObj) {
                    let getUserMedia = navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
                    if (!getUserMedia) {
                        return Promise.reject(new Error('getUserMedia is not implemented in this browser'));
                    }
                    return new Promise(function (resolve, reject) {
                        getUserMedia.call(navigator, constraintObj, resolve, reject);
                    });
                }
            } else {
                navigator.mediaDevices.enumerateDevices()
                    .then(devices => {
                        devices.forEach(device => {
                            console.log(device.kind.toUpperCase(), device.label);
                            //, device.deviceId
                        })
                    })
                    .catch(err => {
                        console.log(err.name, err.message);
                    })
            }

            navigator.mediaDevices.getUserMedia(constraintObj)
                .then(function (mediaStreamObj) {
                    //connect the media stream to the first video element
                    recorder = new MediaRecorder(mediaStreamObj);
                    let video = document.querySelector('video');
                    if ("srcObject" in video) {
                        video.srcObject = mediaStreamObj;
                    } else {
                        //old version
                        video.src = window.URL.createObjectURL(mediaStreamObj);
                    }
                    timedCount();
                    console.log(c);
                    video.onloadedmetadata = function (ev) {
                        //show in the video element what is being captured by the webcam
                        video.play();
                        //timedCount();
                    };
                    recorder.onstart=function (ev) {
                        timedPic();
                    };
                    recorder.ondataavailable = function (ev) {
                        chunks.push(ev.data);
                    };
                    stop.addEventListener('click', (ev)=>{
                        recorder.stop();
                    });

                    recorder.onstop=function (ev) {
                        let blob = new Blob(chunks, {'type': 'audio/wav;'});
                        chunks = [];
                        let audioURL = window.URL.createObjectURL(blob);
                        audio.src = audioURL;
                    };



                })
                .catch(function (err) {
                    console.log(err.name, err.message);
                });
        },
    };


    let start = document.getElementById('btnstart');
    let stop = document.getElementById('btnstart');
    let audio = document.getElementById('aud');
//let mediaRecorder = new MediaRecorder(mediaStreamObj);
    let chunks = [];


    start.addEventListener('click', (ev)=>{
        //console.log(start);
        recordmethods.startRecording();
        console.log('recorder starts now');
    });
    function timedCount() {
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




}
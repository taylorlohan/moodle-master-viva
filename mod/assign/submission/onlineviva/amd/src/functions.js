//require(['core/first', 'jquery', 'jqueryui', 'core/ajax'], function(core, $, bootstrap, ajax) {

var $ = jQuery;
// eslint-disable-next-line no-undef
jQuery(document).ready(function () {

       //var opts;
        var canvas=$('#canvas');
        var width = 320;
        var height = 0;
        let chunks = [];
        var myRecorder = {
            objects: {
                context: null,
                stream: null,
                recorder: null
            },
            start: function () {
                var options = {audio: true, video: {
                        facingMode: "user",
                        width: { min: 640, ideal: 1280, max: 1920 },
                        height: { min: 480, ideal: 720, max: 1080 }
                    }};
                navigator.mediaDevices.getUserMedia(options).then(function (stream) {
                    myRecorder.objects.stream = stream;
                    myRecorder.objects.recorder = new MediaRecorder(stream);
                    myRecorder.objects.recorder.start();
                    if ("srcObject" in $('#vidcon')) {
                        $('#vidcon').srcObject = stream;
                    } else {
                        //old version
                        $('#vidcon').src = window.URL.createObjectURL(stream);
                    }
                    $('#vidcon').onloadedmetadata = function(ev) {
                        //show in the video element what is being captured by the webcam
                        $('#vidcon').play();
                    };
                }).catch(function (err) {});
            },
            stop: function () {
                let blob = new Blob(chunks, { 'type' : 'audio/wav;' });
                chunks = [];

                //vidSave.src = videoURL;
                if (null !== myRecorder.objects.stream) {
                    myRecorder.objects.stream.getAudioTracks()[0].stop();
                    myRecorder.objects.stream.getVideoTracks()[0].stop();
                }
                //if (myRecorder.objects.recorder=='recording') {
                    myRecorder.objects.recorder.stop();
                    let audioURL = window.URL.createObjectURL(blob);
                    $('#aud').src=audioURL;

                    /*}else{
                    console.log('not recording with curent state is'+ myRecorder.objects.recorder.state);
                }*/
             }
            };
        $('#btnstart').click(function() {
            myRecorder.start();
        }),
            $('#btnstop').click(function() {
                myRecorder.stop();
            }),

        /*setTimeout(() => {

        }, 5000);*/
            function takepicture() {
                var context = canvas.getContext('2d');
                if (width && height) {
                    canvas.width = width;
                    canvas.height = height;
                    context.drawImage($('#vidcon'), 0, 0, width, height);

                    var data = canvas.toDataURL('image/png');
                    $('#photo').setAttribute('src', data);
                } else {
                    clearphoto();
                }
            },
            function clearphoto() {
                var context = canvas.getContext('2d');
                context.fillStyle = "#AAA";
                context.fillRect(0, 0, canvas.width, canvas.height);

                var data = canvas.toDataURL('image/png');
                $('#photo').setAttribute('src', data);
            };

        });
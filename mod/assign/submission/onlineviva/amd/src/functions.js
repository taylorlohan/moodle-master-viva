define(['jquery','core/log','core/str'], function($,log,str) {
    "use strict"; // jshint ;_;

    log.debug('submission helper: initialising');

    return {

        allChunks :[],
        c:10,
//init(obj);
        /*let opts= '<?php echo json_encode($obj); ?>';
        console.log('recording begin');
        console.log(opts);*/
        assignment:0,
        submission:0,

        init:  function(obj) {//可以放到return的大括号里
            const canvas=document.getElementById('canvas');
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            this.assignment=obj['assignment'];
            this.submission=obj['submission'];
            console.log(obj);

            navigator.mediaDevices.getUserMedia({
                audio:true,
                video: true
            })
                .then(function(mediaStream) {
                    var srcvideo = document.getElementById("srcvideo")
                    srcvideo.srcObject = mediaStream;
                    srcvideo.play();
                    this.playCanvas(srcvideo, ctx);
                });

            this.setRecorder();
            this.setFormatSelect('video/webm;codecs=vp8')//找到合适的视频格式并导入数据库
        },

        playCanvas:function (srcvideo, ctx) {
        ctx.drawImage(srcvideo, 0, 0, 640, 460);
        requestAnimationFrame(() => {
            this.playCanvas(srcvideo, ctx);
        })
    },

        setFormatSelect:function(format){
        if(!MediaRecorder.isTypeSupported(format)){
            alert(format);
            alert("当前浏览器不支持该编码类型");//最后可以去掉
            return;
        }
        this.allChunks = [];
            this.setRecorder(format);
    },


    setRecorder:function (format) {
        const stream = canvas.captureStream(60); // 60 FPS recording
        const audioTracks = stream.getAudioTracks();//怎么只得到音频
        const videoTracks=stream.getVideoTracks();
        const startBtn=document.getElementById('startBtn');
        const stopBtn=document.getElementById('stopBtn');

        const recorder = new MediaRecorder(stream, {
            mimeType: format
        });
        recorder.ondataavailable = e => {
            this.allChunks.push(
                e.data
            );
        };

        startBtn.disabled = false;
        startBtn.onclick = e => {
            recorder.start(10);
            this.timedCount();
            startBtn.disabled = true;
            stopBtn.disabled = false;
        },
        stopBtn.onclick = e => {
            recorder.stop();
            /*audioTracks.stop();//停止不了，没有用
            videoTracks.stop();*/
            stream.getTracks().forEach( track => track.stop() ); // stop each of them
            console.log('recorder has stopped')
            this.saveAudio();
            //blobDownload(format);
            startBtn.disabled = false;
            stopBtn.disabled = true;
        },
        recorder.onstop=event => {
            this.allChunks.push(
                e.data
            );
        };


    },
    timedCount:function () {
        document.querySelector(".count").innerHTML = 'time left is' + this.c;
        this.c = this.c - 1;
        if (this.c > 0) {
            let t = setTimeout(this.timedCount, 1000);
        } else {
            //recorder.stop();
            document.querySelector(".count").innerHTML = "Time is up!";
            this.stopBtn.click();
            console.log('stop is clicked');
            return;

            //else console.log('recorder is null');
        }
    },

    saveAudio:function () {


        const fullBlob = new Blob(this.allChunks, { type: 'video/mp4' });//
        //const uploadUrl = window.URL.createObjectURL(fullBlob);
        var myFormData = new FormData();
        myFormData.append('file',fullBlob);
        myFormData.append('assignment', this.assignment);
        myFormData.append('submission', this.submission);

        $.ajax({
            type: "POST",
            url: 'upload.php',
            data : myFormData,
            contentType: false,
            processData: false,
            cache: false,
            success: function(data)
            {
                alert("pass data to php success!");
                console.log(data);
            }
        });
    }
};
});
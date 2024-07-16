<!DOCTYPE html>
<html>
<head>
    <title>Live Meeting</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
    <div id="app">
        <h1>Live Meeting</h1>
        <video id="localVideo" autoplay muted></video>
        <video id="remoteVideo" autoplay></video>
        <button onclick="startCall()">Start Call</button>
    </div>

    <script>
        const localVideo = document.getElementById('localVideo');
        const remoteVideo = document.getElementById('remoteVideo');
        let localStream;
        let peerConnection;
        const userId = Math.floor(Math.random() * 1000);

        window.Echo.channel(`webrtc.${userId}`)
            .listen('WebRTCSignal', (e) => {
                if (e.type === 'offer') {
                    handleOffer(e.data);
                } else if (e.type === 'answer') {
                    handleAnswer(e.data);
                } else if (e.type === 'candidate') {
                    handleCandidate(e.data);
                }
            });

        async function startCall() {
            localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            localVideo.srcObject = localStream;

            peerConnection = new RTCPeerConnection();
            localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

            peerConnection.onicecandidate = (event) => {
                if (event.candidate) {
                    sendSignal('candidate', event.candidate);
                }
            };

            peerConnection.ontrack = (event) => {
                remoteVideo.srcObject = event.streams[0];
            };

            const offer = await peerConnection.createOffer();
            await peerConnection.setLocalDescription(offer);
            sendSignal('offer', offer);
        }

        async function handleOffer(offer) {
            peerConnection = new RTCPeerConnection();
            localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

            peerConnection.onicecandidate = (event) => {
                if (event.candidate) {
                    sendSignal('candidate', event.candidate);
                }
            };

            peerConnection.ontrack = (event) => {
                remoteVideo.srcObject = event.streams[0];
            };

            await peerConnection.setRemoteDescription(new RTCSessionDescription(offer));
            const answer = await peerConnection.createAnswer();
            await peerConnection.setLocalDescription(answer);
            sendSignal('answer', answer);
        }

        async function handleAnswer(answer) {
            await peerConnection.setRemoteDescription(new RTCSessionDescription(answer));
        }

        function handleCandidate(candidate) {
            peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
        }

        function sendSignal(type, data) {
            fetch('/send-signal', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ type, data, userId })
            });
        }
    </script>
</body>
</html>

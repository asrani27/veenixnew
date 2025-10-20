<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sdf</title>

    {{-- ✅ Plyr CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/plyr@3.7.8/dist/plyr.css" />

    <style>
        body {
            background: #f8f9fa;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .video-container {
            width: 90%;
            max-width: 900px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        video {
            width: 100%;
            border-radius: 16px 16px 0 0;
        }

        .title {
            padding: 1rem;
            text-align: center;
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }
    </style>
</head>

<body>

    <div class="video-container">
        <video id="player" playsinline controls>
            <source src="{{ $urlVideo }}" type="video/mp4">
        </video>

    </div>

    {{-- ✅ Plyr JS --}}
    <script src="https://cdn.jsdelivr.net/npm/plyr@3.7.8/dist/plyr.polyfilled.js"></script>

    <script>
        const player = new Plyr('#player', {
            ratio: '16:9',
            controls: [
                'play-large', 'play', 'progress', 'current-time',
                'mute', 'volume', 'settings', 'pip', 'fullscreen'
            ],
            settings: ['quality', 'speed'],
        });

        // Optional: auto pause all other Plyr instances
        window.player = player;
    </script>

</body>

</html>
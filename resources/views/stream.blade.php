<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="robots" content="noindex" />
    <META NAME="GOOGLEBOT" CONTENT="NOINDEX" />
    <title>Veenix Player</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <!-- Preload critical CSS -->
    <link rel="preload" href="/plyr/plyr.css" as="style">
    <link rel="preload" href="/plyr/rubik.css" as="style">
    <link rel="preload" href="/plyr/pb.css?v=1" as="style">

    <!-- Load CSS first -->
    <link href="/plyr/rubik.css" rel="stylesheet">
    <link href="/plyr/plyr.css" rel="stylesheet">
    <link href="/plyr/pb.css?v=1" rel="stylesheet">

    <!-- Preload critical JavaScript -->
    <link rel="preload" href="/plyr/plyr.polyfilled.min.js" as="script">
    <link rel="preload" href="/plyr/jquery-3.7.1.min.js" as="script">
    <link rel="preload" href="/plyr/pb.js?v=1" as="script">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/hls.js@latest" as="script">
    <meta name="description" content="" />
    <style>
        * {
            user-select: none;
            /* supported by Chrome and Opera */
            -webkit-user-select: none;
            /* Safari */
            -khtml-user-select: none;
            /* Konqueror HTML */
            -moz-user-select: none;
            /* Firefox */
            -ms-user-select: none;
            /* Internet Explorer/Edge */
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }

        :root {
            --plyr-color-main: #8b5cf6;
            --plyr-video-background: transparent;
            --plyr-captions-background: black;
            --plyr-captions-text-color: white;
            --plyr-font-weight-regular: 600;
            --plyr-font-weight-bold: 600;

            --plyr-font-family: 'Rubik';

            --webkit-text-track-display: none;
            --plyr-font-size-xlarge: 30px;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            width: 100%;
            height: 100%;
            background: #000000;
        }

        body {
            background-color: transparent;

            font-family: 'Rubik', sans-serif;

        }

        .container {
            width: 100%;
            height: 100%;
        }

        video {
            width: 100%;
            height: 100%;
        }

        .plyr__poster {
            background-size: cover;
        }

        .plyr__control--overlaid {
            background: #8b5cf6;
            box-shadow: #4a4a4a20 0 0 27px;
        }

        .plyr--video {
            height: 100%;
        }

        .plyr--video .plyr__control.plyr__tab-focus,
        .plyr--video .plyr__control:hover,
        .plyr--video .plyr__control[aria-expanded=true] {
            background: #8b5cf6;
        }

        .plyr__control.plyr__tab-focus {
            box-shadow: 0 0 0 5px rgba(139, 92, 246, .5);
        }

        .plyr__menu__container .plyr__control[role=menuitemradio][aria-checked=true]::before {
            background: #8b5cf6;
        }

        [data-plyr="captions"].plyr__control {
            border-bottom: solid 3px transparent;
        }

        [data-plyr="captions"].plyr__control--pressed {
            border-bottom: solid 3px #8b5cf6;
        }

        .plyr__captions {
            font-size: 20px;
        }

        @media (max-width: 479px) {
            .plyr__captions {
                font-size: 18px;
            }
        }

        @media (min-width: 480px) {
            .plyr__captions {
                font-size: 18px;
            }
        }

        @media (min-width: 768px) {
            .plyr__captions {
                font-size: 23px;
            }
        }

        @media (min-width: 1024px) {
            .plyr__captions {
                font-size: 26px;
            }
        }

        .plyr__progress input {
            border-radius: 0px !important;
            -webkit-appearance: none;
            background: transparent;
        }

        .plyr__progress input[value]::-webkit-progress-bar {
            border-radius: 0px !important;
        }

        .plyr__progress input[value]::-webkit-progress-value {
            border-radius: 0px !important;
        }

        .plyr audio,
        .plyr iframe,
        .plyr video {
            max-height: 100vh;
        }

        .plyr__spacer {
            width: 100%;
        }

        .plyr__progress__container {
            position: absolute;
            top: 14px;
            left: 10px;
            width: calc(100% - 24px);
        }

        @media (max-width: 480px) {
            .plyr__progress__container {
                top: -5px;
            }
        }

        .wt-chart-active .ct-series-a .ct-area,
        .ct-series-a .ct-slice-donut-solid,
        .ct-series-a .ct-slice-pie {
            fill: url(#gradient-active);
        }

        .ct-series-a .ct-area,
        .ct-series-a .ct-slice-donut-solid,
        .ct-series-a .ct-slice-pie {
            fill: url(#gradient-a);
        }

        .ct-series-a .ct-bar,
        .ct-series-a .ct-line,
        .ct-series-a .ct-point,
        .ct-series-a .ct-slice-donut {
            stroke: #8b5cf6;
        }

        .plyr__pb {
            position: absolute;
            top: 0px;
            left: 0px;
            width: 100%;
            z-index: 3;
            margin-left: calc(var(--plyr-range-thumb-height, 13px)*-.5);
            margin-right: -6.5px;
            margin-right: calc(var(--plyr-range-thumb-height, 13px)*-.5);
            width: calc(100% + 13px);
            width: calc(100% + var(--plyr-range-thumb-height, 13px));
        }

        .plyr__preview-thumb {
            bottom: 22px;
            transition: bottom ease 0.1s;
        }

        .plyr__controls {
            padding-top: 70px;
        }

        .plyr--full-ui.plyr--video .plyr__progress input[type=range]::-webkit-slider-runnable-track {
            background-color: transparent !important;
        }

        .plyr--full-ui.plyr--video .plyr__progress input[type=range]::-moz-range-track {
            background-color: transparent !important;
        }

        .plyr--full-ui.plyr--video .plyr__progress input[type=range]::-ms-track {
            background-color: transparent !important;
        }

        .plyr__progress input {
            background-color: transparent !important;
            color: transparent !important;
            top: -6px !important;
            z-index: 7 !important;
            cursor: pointer;
        }

        .plyr--full-ui.plyr--video .plyr__progress input[type=range]::-webkit-slider-thumb {
            opacity: 0;
            transition: opacity ease 0.1s;
        }

        .plyr--full-ui.plyr--video .plyr__progress input[type=range]::-moz-range-thumb {
            opacity: 0;
            transition: opacity ease 0.1s;
        }

        .plyr--full-ui.plyr--video .plyr__progress input[type=range]::-ms-thumb {
            opacity: 0;
            transition: opacity ease 0.1s;
        }

        .plyr--full-ui.plyr--video .plyr__progress input[type=range]:active::-webkit-slider-thumb {
            opacity: 1;
        }

        .plyr--full-ui.plyr--video .plyr__progress input[type=range]:active::-moz-range-thumb {
            opacity: 1;
        }

        .plyr--full-ui.plyr--video .plyr__progress input[type=range]:active::-ms-thumb {
            opacity: 1;
        }

        .plyr__menu__container {
            z-index: 10;
        }

        @media (min-width: 1280px) {
            .plyr--full-ui.plyr--video .plyr__control--overlaid {
                width: 60px;
                height: 60px;
            }

            .plyr__control svg {
                height: 21px;
                width: 21px;
            }
        }

        .plyr__control--overlaid svg {
            margin-left: auto;
            margin-right: auto;
        }

        .plyr__control--logo {
            height: auto;
            max-height: 23.5px;
            position: absolute;
            left: 44%;
            top: 37px;
            margin-left: -50px;
        }

        .plyr__tooltip--drag {
            opacity: 1;
            transform: translate(-50%) scale(1);
        }

        .plyr__controls__item[data-plyr="rewind"],
        .plyr__controls__item[data-plyr="fast-forward"] {
            padding: 4px;
        }

        .plyr__controls__item[data-plyr="rewind"] svg,
        .plyr__controls__item[data-plyr="fast-forward"] svg {
            height: 24px;
            height: var(--plyr-control-icon-size, 24px);
            pointer-events: none;
            width: 24px;
            width: var(--plyr-control-icon-size, 24px);
        }

        .plyr--full-ui ::-webkit-media-text-track-container {
            display: var(--webkit-text-track-display);
        }

        .disable-poster-transition .plyr__poster {
            transition: none;
        }

        /*workaround to fix safari bug with not showing video thumbnail:*/
        .plyr__video-wrapper {
            z-index: 0;
        }

        /* fix for vertical subtitles scrolling */
        .plyr__menu__container>div {
            max-height: 50vh;
            overflow-y: auto;
        }

        /* Fix for controls overlapping on small devices */
        @media only screen and (max-width: 500px) {
            .hide_mobile.plyr__spacer {
                display: none
            }
        }

        .plyr--is-ios .plyr__volume {
            min-width: 32px;
        }

        /* Chromecast */
        .chromecast-connected {

            opacity: 1;
        }

        .chromecast-disconnected {
            opacity: 0.5;
        }

        .error-message {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.5);
            text-align: center;
            color: #ccc;
            padding-top: 50px;
        }
    </style>
    <style>
        /* Efek loading */
        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255, 255, 255, 0.2);
            border-top: 5px solid #8b5cf6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 10;
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        /* Sembunyikan loading saat video selesai dimuat */
        .hidden {
            display: none;
        }
    </style>
</head>

<body id="body">
    <div class="container" id="video-container">
        @if($errorMessage)
        <div class="error-message">
            <h2>{{ $errorMessage }} <br /> Silahkan klik report agar kami segera memperbaikinya</h2>

        </div>
        @else
        <div id="loading-spinner" class="loading-spinner"></div>
        <video id="main-video" preload="none" crossorigin="anonymous" data-plyr-config='{ "title": "vidio.mp4" }'
            playsinline data-poster="" muted>
            @if($isHls ?? false)
                <!-- HLS will be loaded programmatically -->
            @else
                <source src="{{$urlVideo}}" type="video/mp4" />
            @endif
        </video>
        @endif
    </div>

    <!-- Load scripts with defer for better performance -->
    <script src="/plyr/plyr.polyfilled.min.js" defer></script>
    <script src="/plyr/jquery-3.7.1.min.js" defer></script>
    <script src="/plyr/pb.js?v=1" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest" defer></script>

    <script>
        // Optimized initialization with requestAnimationFrame
        document.addEventListener("DOMContentLoaded", function() {
            // Check if there's an error message (no video to play)
            const errorMessage = document.querySelector('.error-message');
            if (errorMessage) {
                return;
            }

            const video = document.getElementById("main-video");
            const loadingSpinner = document.getElementById("loading-spinner");
            const STORAGE_KEY = 'video-time-' + window.location.href;
            
            // Check if this is an HLS stream
            const isHls = @json($isHls ?? false);
            const hlsUrl = @json($urlVideo);
            let hls = null;

            // Initialize HLS if needed
            function initializeHls() {
                if (isHls && hlsUrl && typeof Hls !== 'undefined') {
                    if (Hls.isSupported()) {
                        hls = new Hls({
                            debug: false,
                            enableWorker: true,
                            lowLatencyMode: true,
                            backBufferLength: 90
                        });
                        
                        hls.loadSource(hlsUrl);
                        hls.attachMedia(video);
                        
                        hls.on(Hls.Events.MANIFEST_PARSED, function(event, data) {
                            console.log('HLS manifest parsed, quality levels available:', data.levels);
                            video.play().catch(function(error) {
                                // Autoplay failed - user interaction required
                            });
                        });
                        
                        hls.on(Hls.Events.ERROR, function(event, data) {
                            console.error('HLS error:', data);
                            if (data.fatal) {
                                switch(data.type) {
                                    case Hls.ErrorTypes.NETWORK_ERROR:
                                        console.log('Fatal network error, trying to recover...');
                                        hls.startLoad();
                                        break;
                                    case Hls.ErrorTypes.MEDIA_ERROR:
                                        console.log('Fatal media error, trying to recover...');
                                        hls.recoverMediaError();
                                        break;
                                    default:
                                        console.error('Fatal HLS error, cannot recover');
                                        break;
                                }
                            }
                        });
                    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                        // Native HLS support (Safari)
                        video.src = hlsUrl;
                        video.addEventListener('loadedmetadata', function() {
                            video.play().catch(function(error) {
                                // Autoplay failed - user interaction required
                            });
                        });
                    } else {
                        console.error('HLS not supported');
                    }
                }
            }

            // Optimized video event listeners
            video.addEventListener("loadeddata", function() {
                requestAnimationFrame(() => {
                    loadingSpinner.classList.add("hidden");
                });
            });

            video.addEventListener("canplay", function() {
                // Unmute video when it can play
                video.muted = false;
            });

            video.addEventListener("click", enterFullscreen);

            // Preload video metadata faster
            video.load();

            function enterFullscreen() {
                if (video.requestFullscreen) {
                    video.requestFullscreen();
                } else if (video.webkitRequestFullscreen) {
                    video.webkitRequestFullscreen();
                } else if (video.msRequestFullscreen) {
                    video.msRequestFullscreen();
                }

                if (screen.orientation && screen.orientation.lock) {
                    screen.orientation.lock("landscape").catch(function(error) {
                        console.log("Tidak dapat mengunci orientasi: ", error);
                    });
                }
            }

            document.addEventListener("fullscreenchange", function() {
                if (document.fullscreenElement && screen.orientation && screen.orientation.lock) {
                    screen.orientation.lock("landscape").catch(function(error) {
                        console.log("Gagal mengunci orientasi: ", error);
                    });
                }
            });

            const defaultOptions = {
                storage: {
                    enabled: true,
                    key: 'plyr--lib-1300'
                },
                fullscreen: {
                    enabled: true,
                    fallback: true,
                    iosNative: true
                },
                iconUrl: '/plyr/plyr.svg',
                captions: { active: false, language: '', update: true },
                controls: [
                    "play-large","play","rewind","fast-forward","progress","current-time","volume","settings","fullscreen"
                ],
                settings: ['quality', 'speed', 'loop'],
                speed: { selected: 1, options: [0.5,0.75,1,1.25,1.5,1.75,2,4] },
                // Optimize for faster loading
                tooltips: { controls: true, seek: true },
                captions: { active: false, language: 'auto', update: false },
                debug: false
            };

            // Initialize player when scripts are loaded
            function initializePlayer() {
                if (typeof Plyr !== 'undefined' && typeof $ !== 'undefined' && typeof PB !== 'undefined') {
                    // Initialize HLS first if needed
                    initializeHls();
                    
                    player = new Plyr(video, defaultOptions);
                    
                    requestAnimationFrame(() => {
                        initPlayer();
                    });
                } else {
                    // Retry initialization if scripts aren't loaded yet
                    setTimeout(initializePlayer, 100);
                }
            }

            function initPlayer() {
                player.elements.captions.dir = "auto";
                $('<div class="plyr__controls__item hide_mobile plyr__spacer"></div>').insertBefore(".plyr__progress__container");

                $("video").on('webkitbeginfullscreen webkitendfullscreen', function(event) {
                    document.documentElement.style.setProperty('--webkit-text-track-display', 
                        event.type === 'webkitbeginfullscreen' ? 'block' : 'none');
                });

                $(".plyr__progress__container input").css("top", "-5px");
                $(".plyr__progress__container progress").css("top", "4px");
                $(".plyr__progress__container progress").css("opacity", "0.01");
                $(".plyr__progress").prepend($('<div class="plyr__pb"></div>'));
                
                const pb = new PB(".plyr__pb", ".plyr__progress__container input", {
                    keyColor: "#8b5cf6",
                    videoLength: 20,
                    chapters: [],
                    moments: [],
                    onScrubbingChange: function(seekTime, offset) {
                        const thumbWidth = $(".plyr__preview-thumb").width();
                        const position = Math.max(thumbWidth / 2, offset);
                        const finalPosition = Math.min($(".plyr__controls").width() - $(".plyr__preview-thumb").width() + (thumbWidth / 4), position);
                        $(".plyr__preview-thumb").css("left", (finalPosition - 5.5) + "px");
                    }
                });

                player.on("loadedmetadata", function() {
                    pb.SetDuration(player.duration);
                    
                    // Restore saved position with better logic
                    const savedTime = parseFloat(localStorage.getItem(STORAGE_KEY)) || 0;
                    if (savedTime > 5 && savedTime < player.duration - 5) { // Don't restore if too close to start or end
                        player.currentTime = savedTime;
                    }
                });

                // Optimize timeupdate with throttling
                let lastUpdateTime = 0;
                player.on("timeupdate", function() {
                    const now = Date.now();
                    if (now - lastUpdateTime > 2000) { // Update every 2 seconds for better performance
                        // Only save if video is playing and not at the very beginning or end
                        if (player.currentTime > 10 && player.currentTime < player.duration - 10) {
                            localStorage.setItem(STORAGE_KEY, player.currentTime);
                            lastUpdateTime = now;
                        }
                    }
                });

                // Save position when user seeks
                player.on("seeked", function() {
                    if (player.currentTime > 10 && player.currentTime < player.duration - 10) {
                        localStorage.setItem(STORAGE_KEY, player.currentTime);
                    }
                });

                // Save position when user pauses
                player.on("pause", function() {
                    if (player.currentTime > 10 && player.currentTime < player.duration - 10) {
                        localStorage.setItem(STORAGE_KEY, player.currentTime);
                    }
                });

                player.on("ended", function() {
                    localStorage.removeItem(STORAGE_KEY);
                });

                // Also save position before page unload
                window.addEventListener("beforeunload", function() {
                    if (player && player.currentTime > 10 && player.currentTime < player.duration - 10) {
                        localStorage.setItem(STORAGE_KEY, player.currentTime);
                    }
                });

                // Optimize progress bar updates
                let progressInterval;
                function startProgressUpdates() {
                    progressInterval = setInterval(function() {
                        if (player && !player.paused) {
                            pb.SetCurrentProgress(player.currentTime);
                            pb.SetBufferProgress(player.duration * player.buffered);
                        }
                    }, 16);
                }

                player.on("play", startProgressUpdates);
                player.on("pause", () => clearInterval(progressInterval));
                player.on("ended", () => clearInterval(progressInterval));
            }

            // Start initialization
            initializePlayer();

            // Keyboard shortcuts
            document.addEventListener("keydown", function(e) {
                if (!player) return;

                switch (e.code) {
                    case "Space":
                    case "Spacebar":
                        e.preventDefault();
                        if (player.playing) {
                            player.pause();
                        } else {
                            player.play();
                        }
                        break;
                    case "ArrowRight":
                        e.preventDefault();
                        player.forward(10);
                        break;
                    case "ArrowLeft":
                        e.preventDefault();
                        player.rewind(10);
                        break;
                    case "ArrowUp":
                        e.preventDefault();
                        player.volume = Math.min(player.volume + 0.1, 1);
                        break;
                    case "ArrowDown":
                        e.preventDefault();
                        player.volume = Math.max(player.volume - 0.1, 0);
                        break;
                }
            });
        });
    </script>


</body>

</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>404 • Cosmic Drift | ExamGuardian Proctor</title>
    <!-- Google Fonts + Font Awesome 6 -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="icon" type="image/png" href="raj.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at 30% 10%, #0B0E1A, #010101);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
        }

        /* dynamic starfield */
        .starfield {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }
        .star {
            position: absolute;
            background: radial-gradient(circle, #ffffff, #ffe6b0);
            border-radius: 50%;
            opacity: 0;
            animation: starTwinkle 2.8s infinite alternate ease-in-out;
        }
        @keyframes starTwinkle {
            0% { opacity: 0.15; transform: scale(1);}
            100% { opacity: 0.9; transform: scale(1.2);}
        }

        /* floating glow orb */
        .glow-orb {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(102,126,234,0.15), transparent 70%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            filter: blur(60px);
            pointer-events: none;
            z-index: 0;
        }

        /* main card - futuristic glass */
        .error-container {
            max-width: 760px;
            width: 100%;
            background: rgba(12, 18, 34, 0.7);
            backdrop-filter: blur(16px);
            border-radius: 56px;
            border: 1px solid rgba(110, 130, 255, 0.4);
            box-shadow: 0 30px 50px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(102, 126, 234, 0.2) inset;
            padding: 48px 44px;
            text-align: center;
            transition: transform 0.2s ease;
            z-index: 2;
            animation: fadeSlideUp 0.6s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
                backdrop-filter: blur(0px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
                backdrop-filter: blur(16px);
            }
        }

        /* glitch 404 effect */
        .glitch-number {
            font-size: 140px;
            font-weight: 800;
            text-shadow: 5px 5px 0 #ff3366, -5px -5px 0 #3b9eff;
            color: #fff;
            letter-spacing: 12px;
            animation: glitchSkew 2s infinite alternate;
            display: inline-block;
            margin-bottom: 8px;
        }
        @keyframes glitchSkew {
            0% {
                text-shadow: 5px 5px 0 #ff3366, -5px -5px 0 #3b9eff;
                transform: skew(0deg);
            }
            25% {
                text-shadow: -6px 4px 0 #0ff, 6px -4px 0 #f0f;
                transform: skew(2deg);
            }
            50% {
                text-shadow: 7px -3px 0 #ffb347, -4px 5px 0 #3b9eff;
                transform: skew(-1.5deg);
            }
            75% {
                text-shadow: -5px -5px 0 #ff6699, 5px 5px 0 #5f6eff;
                transform: skew(1deg);
            }
            100% {
                text-shadow: 5px 5px 0 #ff3366, -5px -5px 0 #3b9eff;
                transform: skew(0deg);
            }
        }

        .page-title {
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #FFFFFF, #B7C6FF);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin: 12px 0 8px;
        }

        .error-message {
            color: #B8C7FF;
            font-size: 18px;
            margin: 20px 0 16px;
            line-height: 1.5;
            font-weight: 400;
            max-width: 520px;
            margin-left: auto;
            margin-right: auto;
        }

        /* countdown badge with neon */
        .countdown-badge {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            display: inline-flex;
            align-items: baseline;
            gap: 12px;
            padding: 12px 28px;
            border-radius: 80px;
            border: 1px solid rgba(102, 126, 234, 0.7);
            margin: 20px 0 10px;
        }
        .countdown-number {
            font-size: 56px;
            font-weight: 800;
            background: linear-gradient(145deg, #F0F3FF, #9BB0FF);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            line-height: 1;
            font-feature-settings: "tnum";
            letter-spacing: 4px;
        }
        .countdown-unit {
            font-size: 20px;
            font-weight: 600;
            color: #CDDBFF;
        }
        .redirect-hint {
            font-size: 15px;
            color: #A0B0E0;
            margin-top: 8px;
        }
        .redirect-hint i {
            margin-right: 6px;
            color: #7f9eff;
        }

        /* animated progress bar - cosmic */
        .progress-track {
            width: 100%;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 40px;
            height: 6px;
            margin: 28px 0 32px;
            overflow: hidden;
            box-shadow: 0 0 6px rgba(100, 120, 255, 0.4);
        }
        .progress-fill-dynamic {
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, #667eea, #c86eff, #ff82b2);
            border-radius: 40px;
            animation: shrinkWidth 5s linear forwards;
            box-shadow: 0 0 12px #a77cff;
        }
        @keyframes shrinkWidth {
            from { width: 100%; }
            to { width: 0%; }
        }

        /* button group */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin: 10px 0 20px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 14px 28px;
            font-weight: 600;
            font-size: 16px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.25s ease;
            cursor: pointer;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(20, 28, 48, 0.8);
            color: #E0EAFF;
        }
        .btn-primary {
            background: linear-gradient(105deg, #5b6cff, #8c4eff);
            color: white;
            box-shadow: 0 10px 18px rgba(80, 70, 200, 0.4);
            border: none;
        }
        .btn-primary:hover {
            transform: translateY(-4px);
            background: linear-gradient(105deg, #6f80ff, #a160ff);
            box-shadow: 0 18px 25px rgba(102, 126, 234, 0.5);
        }
        .btn-secondary:hover {
            background: rgba(45, 55, 95, 0.9);
            transform: translateY(-3px);
            color: white;
            border-color: #8d9eff;
        }
        .btn i {
            font-size: 1.1rem;
        }

        /* suggestion grid */
        .insight-panel {
            margin-top: 36px;
            background: rgba(8, 12, 28, 0.6);
            border-radius: 32px;
            padding: 20px 24px;
            text-align: left;
            border: 1px solid rgba(102, 126, 234, 0.3);
        }
        .insight-panel h3 {
            font-size: 18px;
            font-weight: 600;
            color: #EAF0FF;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .insight-list {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            list-style: none;
        }
        .insight-list li {
            flex: 1;
            min-width: 160px;
            background: rgba(25, 34, 60, 0.7);
            border-radius: 26px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
            color: #C7D4FF;
            transition: 0.2s;
        }
        .insight-list li:hover {
            background: rgba(60, 78, 135, 0.7);
            transform: translateX(3px);
        }
        .insight-list i {
            width: 26px;
            font-size: 18px;
            color: #8aa1ff;
        }
        .insight-list a {
            color: #bbcaff;
            text-decoration: none;
            font-weight: 600;
            border-bottom: 1px dashed #6c85ff;
        }
        .insight-list a:hover {
            color: white;
            border-bottom-style: solid;
        }

        @media (max-width: 600px) {
            .error-container {
                padding: 32px 20px;
            }
            .glitch-number {
                font-size: 80px;
                letter-spacing: 6px;
            }
            .page-title {
                font-size: 24px;
            }
            .countdown-number {
                font-size: 42px;
            }
            .btn {
                padding: 10px 20px;
                font-size: 14px;
            }
            .insight-list li {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="starfield" id="starfield"></div>
<div class="glow-orb"></div>

<div class="error-container" id="mainCard">
    <div class="glitch-number">404</div>
    <div class="page-title">Navigation Failure</div>
    <p class="error-message">
        The requested page does not exist or has been relocated to a different orbit.  
        <strong>ExamGuardian Proctor</strong> will guide you back to the main portal.
    </p>

    <!-- countdown visual -->
    <div class="countdown-badge">
        <span class="countdown-number" id="timerSeconds">5</span>
        <span class="countdown-unit">seconds</span>
    </div>
    <div class="redirect-hint">
        <i class="fas fa-location-arrow"></i> Redirecting to <strong>examsystem.liveblog365.com</strong> (main dashboard)
    </div>

    <!-- sleek progress bar -->
    <div class="progress-track">
        <div class="progress-fill-dynamic" id="animatedProgress"></div>
    </div>

    <!-- interactive buttons -->
    <div class="action-buttons">
        <button class="btn btn-primary" id="redirectNowBtn">
            <i class="fas fa-sync-alt"></i> Redirect Now
        </button>
        <button class="btn btn-secondary" id="goBackBtn">
            <i class="fas fa-chevron-left"></i> Previous Page
        </button>
    </div>

    <!-- help & suggestions -->
    <div class="insight-panel">
        <h3><i class="fas fa-compass"></i> Quick navigation</h3>
        <ul class="insight-list">
            <li><i class="fas fa-check-circle"></i> Verify URL spelling</li>
            <li><i class="fas fa-home"></i> <a href="https://examsystem.liveblog365.com/">Main homepage</a></li>
            <li><i class="fas fa-headset"></i> <a href="#" id="supportTrigger">Support center</a></li>
            <li><i class="fas fa-arrow-right"></i> Retry from dashboard</li>
        </ul>
    </div>
</div>

<script>
    // --- configuration ---
    const REDIRECT_URL = "https://examsystem.liveblog365.com/";
    let secondsLeft = 5;            // 5 seconds redirect as requested
    let timerInterval = null;
    let redirectTriggered = false;

    // DOM elements
    const timerDisplay = document.getElementById('timerSeconds');
    const redirectBtn = document.getElementById('redirectNowBtn');
    const backBtn = document.getElementById('goBackBtn');
    const supportLink = document.getElementById('supportTrigger');

    // helper: perform redirect safely
    function performRedirect() {
        if (redirectTriggered) return;
        redirectTriggered = true;
        if (timerInterval) clearInterval(timerInterval);
        window.location.href = REDIRECT_URL;
    }

    // update countdown UI
    function updateTimerUI() {
        if (timerDisplay) {
            timerDisplay.innerText = secondsLeft;
        }
        // if countdown hits zero, redirect
        if (secondsLeft <= 0 && !redirectTriggered) {
            performRedirect();
        }
    }

    // start countdown (every second)
    function startCountdown() {
        if (timerInterval) clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            if (redirectTriggered) {
                // if already redirecting, stop interval
                if (timerInterval) clearInterval(timerInterval);
                return;
            }
            if (secondsLeft > 0) {
                secondsLeft--;
                updateTimerUI();
            } else {
                // reached zero
                if (timerInterval) clearInterval(timerInterval);
                if (!redirectTriggered) performRedirect();
            }
        }, 1000);
    }

    // ensure that if secondsLeft becomes 0 on load edge case
    if (secondsLeft <= 0) {
        performRedirect();
    } else {
        startCountdown();
        updateTimerUI();
    }

    // "Redirect Now" button: stops countdown and instantly redirects
    if (redirectBtn) {
        redirectBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (redirectTriggered) return;
            if (timerInterval) clearInterval(timerInterval);
            performRedirect();
        });
    }

    // "Go Back" button: navigate to previous page in history
    if (backBtn) {
        backBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (timerInterval) clearInterval(timerInterval);
            window.history.back();
        });
    }

    // Support mock - interactive & user friendly (shows contact info)
    if (supportLink) {
        supportLink.addEventListener('click', (e) => {
            e.preventDefault();
            alert("📘 ExamGuardian Proctor Support\n✉️ Email: proctor@examsystem.liveblog365.com\n🕒 24/7 assistance for technical issues.");
        });
    }

    // generate deep starfield for immersive effect
    function generateStars() {
        const starContainer = document.getElementById('starfield');
        if (!starContainer) return;
        const starCount = 280;
        for (let i = 0; i < starCount; i++) {
            const star = document.createElement('div');
            star.classList.add('star');
            const size = Math.random() * 3 + 1;
            star.style.width = size + 'px';
            star.style.height = size + 'px';
            star.style.left = Math.random() * 100 + '%';
            star.style.top = Math.random() * 100 + '%';
            const duration = Math.random() * 3 + 1.2;
            star.style.animationDuration = duration + 's';
            star.style.animationDelay = Math.random() * 5 + 's';
            starContainer.appendChild(star);
        }
    }
    generateStars();

    // subtle parallax effect on card based on mouse movement
    const card = document.querySelector('.error-container');
    if (card) {
        document.body.addEventListener('mousemove', (e) => {
            const xAxis = (window.innerWidth / 2 - e.pageX) / 45;
            const yAxis = (window.innerHeight / 2 - e.pageY) / 45;
            card.style.transform = `translate(${xAxis * 0.3}px, ${yAxis * 0.2}px)`;
        });
        document.body.addEventListener('mouseleave', () => {
            card.style.transform = 'translate(0px, 0px)';
        });
    }

    // Optional: Sync progress bar manually (though CSS animation is 5s and matches redirect)
    // But in case of early redirect we don't need to worry. For extra elegance, we set a small safety
    // that if remaining seconds ever gets out of sync (e.g., due to lag), force redirect at 0.
    // already covered.

    // Additionally: because the CSS progress animation lasts exactly 5 seconds (shrinkWidth 5s linear forwards)
    // it perfectly aligns with the redirect timer. If user clicks "Redirect Now", the progress bar may still animate,
    // but page will unload which is fine.
    // Also we want to make sure that if page is idle, the redirect happens exactly at 5 seconds.
    // Added second safety: maximum redirect timeout (10 seconds max) to avoid any rare stuck case.
    const safetyTimeout = setTimeout(() => {
        if (!redirectTriggered) {
            if (timerInterval) clearInterval(timerInterval);
            performRedirect();
        }
    }, 5600); // just after 5.6 sec
    // override performRedirect to clear safety
    const originalRedirect = performRedirect;
    window.performRedirect = function() {
        clearTimeout(safetyTimeout);
        originalRedirect();
    };
    // rebind button actions to use the new safe redirect
    const newRedirectHandler = () => {
        if (redirectTriggered) return;
        clearTimeout(safetyTimeout);
        if (timerInterval) clearInterval(timerInterval);
        window.location.href = REDIRECT_URL;
        redirectTriggered = true;
    };
    // replace the existing redirectNow button listener with enhanced version
    if (redirectBtn) {
        const freshBtn = redirectBtn.cloneNode(true);
        redirectBtn.parentNode.replaceChild(freshBtn, redirectBtn);
        freshBtn.addEventListener('click', (e) => {
            e.preventDefault();
            newRedirectHandler();
        });
    }
    // also update the internal timer redirect
    window.newPerform = function() {
        if (!redirectTriggered) {
            clearTimeout(safetyTimeout);
            if (timerInterval) clearInterval(timerInterval);
            window.location.href = REDIRECT_URL;
            redirectTriggered = true;
        }
    };
    // rewrite timer's zero redirect
    const origUpdate = updateTimerUI;
    window.updateTimerUI = function() {
        if (timerDisplay) timerDisplay.innerText = secondsLeft;
        if (secondsLeft <= 0 && !redirectTriggered) {
            clearTimeout(safetyTimeout);
            if (timerInterval) clearInterval(timerInterval);
            window.newPerform();
        }
    };
    // reassign interval to use new method
    if (timerInterval) clearInterval(timerInterval);
    let secs = 5;
    secondsLeft = 5;
    redirectTriggered = false;
    const newTimerInterval = setInterval(() => {
        if (redirectTriggered) {
            clearInterval(newTimerInterval);
            return;
        }
        if (secondsLeft > 0) {
            secondsLeft--;
            if (timerDisplay) timerDisplay.innerText = secondsLeft;
            if (secondsLeft === 0) {
                clearInterval(newTimerInterval);
                if (!redirectTriggered) {
                    clearTimeout(safetyTimeout);
                    window.location.href = REDIRECT_URL;
                    redirectTriggered = true;
                }
            }
        } else {
            clearInterval(newTimerInterval);
            if (!redirectTriggered) {
                clearTimeout(safetyTimeout);
                window.location.href = REDIRECT_URL;
                redirectTriggered = true;
            }
        }
    }, 1000);
    timerInterval = newTimerInterval;
    // initial display
    if (timerDisplay) timerDisplay.innerText = secondsLeft;

    // also ensure that go back button works without conflicts
    const finalBackBtn = document.getElementById('goBackBtn');
    if (finalBackBtn) {
        const backClone = finalBackBtn.cloneNode(true);
        finalBackBtn.parentNode.replaceChild(backClone, finalBackBtn);
        backClone.addEventListener('click', (e) => {
            e.preventDefault();
            clearInterval(timerInterval);
            clearTimeout(safetyTimeout);
            window.history.back();
        });
    }
    // support link event rebind
    const finalSupport = document.getElementById('supportTrigger');
    if (finalSupport) {
        const supportClone = finalSupport.cloneNode(true);
        finalSupport.parentNode.replaceChild(supportClone, finalSupport);
        supportClone.addEventListener('click', (e) => {
            e.preventDefault();
            alert("📞 ExamGuardian Proctor Support\nEmail: help@examsystem.liveblog365.com\nWe're here to get you back on track!");
        });
    }
    // Additional polish: update redirect hint if needed
    console.log("404 page ready — auto redirect to main page in 5 seconds");
</script>
</body>
</html>

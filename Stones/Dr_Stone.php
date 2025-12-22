<?php

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. Stone - Ancient Gateway</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700;900&family=Cinzel:wght@600;700&family=IM+Fell+English:ital@0;1&display=swap" rel="stylesheet">

    <style>
        :root {
            --ancient-gold: #d4af37;
            --ancient-bronze: #8b6914;
            --glow-gold: rgba(212, 175, 55, 0.6);
        }

        body {
            font-family: 'IM Fell English', serif;
            background: linear-gradient(135deg, #0f0c0a, #1a1714, #0f0c0a);
            height: 100vh;
            overflow: hidden;
        }

        .particle-bg, .fog {
            position: fixed;
            inset: 0;
            pointer-events: none;
        }

        .particle-bg {
            background-image:
                radial-gradient(2px 2px at 20% 30%, rgba(212,175,55,.3), transparent),
                radial-gradient(2px 2px at 80% 70%, rgba(139,105,20,.3), transparent);
            animation: float 20s infinite alternate;
        }

        @keyframes float {
            to { background-position: 100% 100%; }
        }

        .fog {
            background: radial-gradient(ellipse, transparent, rgba(0,0,0,.8));
        }

        .landing-container {
            position: relative;
            z-index: 5;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .main-title {
            font-family: 'Cinzel Decorative', cursive;
            font-size: 4rem;
            color: var(--ancient-gold);
            text-shadow: 0 0 40px var(--glow-gold);
            margin-bottom: 10px;
        }

        .subtitle {
            color: #c9b896;
            font-style: italic;
        }

        /* ================= BUTTON ================= */
        .stone-button-container {
            position: relative;
            margin: 60px 0;
        }

        .stone-button {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            border: none;
            background: none;
            cursor: pointer;
            position: relative;
            transition: transform .3s ease;
        }

        .stone-button:hover {
            transform: scale(1.05);
        }

        .stone-button:active {
            transform: scale(0.95);
        }

        .stone-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            pointer-events: none;
        }

        .stone-button::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            box-shadow: 0 0 50px var(--glow-gold);
            opacity: 0;
            transition: opacity .3s;
        }

        .stone-button:hover::after {
            opacity: 1;
        }

        /* ================= EFFECTS ================= */
        .ripple-wave {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 3px solid var(--ancient-gold);
            opacity: 0;
        }

        .ripple-wave.active {
            animation: ripple 1.5s ease-out;
        }

        @keyframes ripple {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(2); }
        }

        .crack {
            position: absolute;
            width: 2px;
            height: 0;
            background: linear-gradient(var(--ancient-gold), transparent);
            top: 50%;
            left: 50%;
            opacity: 0;
        }

        .crack.active {
            animation: crack 0.5s forwards;
        }

        @keyframes crack {
            to { height: 150px; opacity: 0; }
        }

        .click-counter {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 6rem;
            color: var(--ancient-gold);
            opacity: 0;
        }

        .click-counter.show {
            animation: pop .6s;
        }

        @keyframes pop {
            50% { opacity: 1; transform: translate(-50%, -50%) scale(1.3); }
            100% { opacity: 0; }
        }

        .portal-effect {
            position: fixed;
            inset: 50%;
            border-radius: 50%;
            background: radial-gradient(var(--ancient-gold), transparent);
            opacity: 0;
            pointer-events: none;
        }

        .portal-effect.activate {
            animation: portal 2s forwards;
        }

        @keyframes portal {
            to {
                inset: -150%;
                opacity: 1;
            }
        }
    </style>
</head>

<body>

<div class="particle-bg"></div>
<div class="fog"></div>

<div class="landing-container">

    <h1 class="main-title">Dr. Stone</h1>
    <p class="subtitle">Gateway to the Ancient Emporium</p>
    <!-- Admin Login Button -->
<div style="position:absolute; top:20px; right:20px; z-index:10;">
    <button id="adminLoginBtn" 
            style="
                padding:10px 20px; 
                background: var(--ancient-gold); 
                border:none; 
                border-radius:5px; 
                font-weight:bold; 
                cursor:pointer;
                color:#1a1714;
                box-shadow: 0 0 10px var(--glow-gold);
            ">
        Admin Login
    </button>
</div>

    <div class="stone-button-container">
        <div class="ripple-wave"></div>
        <div class="ripple-wave"></div>
        <div class="ripple-wave"></div>

        <div class="crack" style="transform:translate(-50%,-50%) rotate(45deg)"></div>
        <div class="crack" style="transform:translate(-50%,-50%) rotate(-45deg)"></div>

        <button class="stone-button" id="stoneButton">
            <img src="assets/stone-button.png" class="stone-image" alt="Sacred Stone">
        </button>

        <div class="click-counter" id="clickCounter">0</div>
    </div>
</div>

<div class="portal-effect" id="portalEffect"></div>

<audio id="stoneSound">
    <source src="assets/stoneblockdragwoodgrind-82327.mp3" type="audio/mpeg">
</audio>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const button = document.getElementById('stoneButton');
    const counter = document.getElementById('clickCounter');
    const portal = document.getElementById('portalEffect');
    const ripples = document.querySelectorAll('.ripple-wave');
    const cracks = document.querySelectorAll('.crack');
    const sound = document.getElementById('stoneSound');

    let clicks = 0;

    button.addEventListener('click', () => {
        clicks++;
        counter.textContent = clicks;
        counter.classList.remove('show');
        void counter.offsetWidth;
        counter.classList.add('show');

        sound.currentTime = 0;
        sound.play().catch(()=>{});

        ripples.forEach(r => {
            r.classList.remove('active');
            void r.offsetWidth;
            r.classList.add('active');
        });

        if (clicks === 2) {
            cracks.forEach(c => {
                c.classList.remove('active');
                void c.offsetWidth;
                c.classList.add('active');
            });
        }

        if (clicks === 3) {
            portal.classList.add('activate');
            button.style.opacity = '0';
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 2000);
        }
    });
});
// Admin Login button click
document.getElementById('adminLoginBtn').addEventListener('click', () => {
    window.location.href = 'admin-login.php'; // redirect to admin login
});

</script>


</body>
</html>

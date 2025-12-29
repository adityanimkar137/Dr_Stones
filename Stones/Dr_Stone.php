<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. Stones - Ancient Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700&family=Cinzel:wght@400;600;700&family=IM+Fell+English:ital@0;1&display=swap" rel="stylesheet">
    <style>
        :root {
            --ancient-parchment: #f4ede1;
            --ancient-sepia: #e8dcc4;
            --ancient-bronze: #8b6914;
            --ancient-gold: #d4af37;
            --ancient-copper: #b87333;
            --ancient-stone: #5a5550;
            --ancient-obsidian: #1a1714;
            --ancient-aged: #c9b896;
            --shadow-soft: 0 6px 20px rgba(0,0,0,0.45);
            --shadow-subtle: 0 2px 6px rgba(0,0,0,0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'IM Fell English', 'Crimson Text', serif;
            background-color: #0f0f0f;
            color: var(--ancient-parchment);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Ancient texture overlay */
        .ancient-texture {
            position: fixed;
            inset: 0;
            background-image:
                repeating-linear-gradient(
                    0deg,
                    rgba(255,255,255,0.01),
                    rgba(255,255,255,0.01) 4px,
                    transparent 4px,
                    transparent 30px
                );
            pointer-events: none;
            opacity: 0.12;
            z-index: 1;
        }

        /* Main container */
        .portal-container {
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            z-index: 2;
        }

        /* Ancient portal frame */
        .portal-frame {
            position: relative;
            max-width: 900px;
            width: 100%;
            background: linear-gradient(180deg, rgba(20,20,20,0.98), rgba(35,35,35,0.98));
            border: 2px solid rgba(139,105,20,0.25);
            border-radius: 12px;
            padding: 3rem 2rem;
            box-shadow: var(--shadow-soft);
        }

        /* Corner decorations */
        .portal-frame::before,
        .portal-frame::after {
            content: '◆';
            position: absolute;
            font-size: 1.5rem;
            color: var(--ancient-gold);
            opacity: 0.4;
        }

        .portal-frame::before {
            top: 1rem;
            left: 1rem;
        }

        .portal-frame::after {
            bottom: 1rem;
            right: 1rem;
        }

        /* Title section */
        .portal-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .portal-title {
            font-family: 'Cinzel Decorative', cursive;
            font-size: 4rem;
            color: var(--ancient-gold);
            letter-spacing: 10px;
            margin-bottom: 1rem;
            font-weight: 700;
            text-shadow: 0 2px 8px rgba(212,175,55,0.3);
        }

        .portal-subtitle {
            font-family: 'Cinzel', serif;
            font-size: 1.4rem;
            color: var(--ancient-aged);
            font-style: italic;
            letter-spacing: 4px;
            font-weight: 600;
        }

        /* Divider */
        .ancient-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 2rem 0;
            gap: 1rem;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(212,175,55,0.2), transparent);
        }

        .divider-symbol {
            font-size: 1.2rem;
            color: var(--ancient-gold);
            opacity: 0.6;
        }

        /* Button container */
        .portal-choices {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        /* Ancient button card */
        .ancient-portal-card {
            position: relative;
            background: #1e1e1d;
            border: 1px solid rgba(139,105,20,0.09);
            border-radius: 8px;
            padding: 2.5rem 2rem;
            transition: all 0.25s ease;
            cursor: pointer;
            overflow: hidden;
            text-decoration: none;
            display: block;
            box-shadow: var(--shadow-subtle);
        }

        .ancient-portal-card:hover {
            transform: translateY(-3px);
            border-color: rgba(139,105,20,0.25);
            box-shadow: var(--shadow-soft);
            background: #232322;
        }

        .card-icon {
            font-size: 3.5rem;
            text-align: center;
            margin-bottom: 1.5rem;
            opacity: 0.9;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }

        .card-title {
            font-family: 'Cinzel', serif;
            font-size: 1.9rem;
            color: var(--ancient-gold);
            text-align: center;
            margin-bottom: 1rem;
            letter-spacing: 3px;
            font-weight: 700;
            text-shadow: 0 1px 4px rgba(212,175,55,0.2);
        }

        .card-description {
            font-family: 'Cinzel', serif;
            font-size: 1.05rem;
            color: var(--ancient-aged);
            text-align: center;
            line-height: 1.7;
            font-style: normal;
            font-weight: 400;
        }

        /* Badge for role */
        .role-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(139,105,20,0.9);
            color: var(--ancient-parchment);
            padding: 0.4rem 1rem;
            border-radius: 3px;
            font-size: 0.8rem;
            font-family: 'Cinzel', serif;
            letter-spacing: 1.5px;
            border: 1px solid rgba(212,175,55,0.3);
            font-weight: 600;
        }

        /* Footer */
        .portal-footer {
            margin-top: 3rem;
            text-align: center;
            color: var(--ancient-aged);
            font-size: 1.05rem;
            font-style: italic;
            font-family: 'Cinzel', serif;
            font-weight: 400;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .portal-frame {
                padding: 2rem 1.5rem;
            }

            .portal-title {
                font-size: 3rem;
                letter-spacing: 6px;
            }

            .portal-subtitle {
                font-size: 1.2rem;
                letter-spacing: 3px;
            }

            .portal-choices {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .card-title {
                font-size: 1.6rem;
            }

            .card-description {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .portal-title {
                font-size: 2.2rem;
                letter-spacing: 4px;
            }

            .portal-subtitle {
                font-size: 1rem;
            }

            .card-icon {
                font-size: 2.8rem;
            }

            .ancient-portal-card {
                padding: 2rem 1.5rem;
            }

            .card-title {
                font-size: 1.4rem;
            }

            .card-description {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <div class="ancient-texture"></div>

    <div class="portal-container">
        <div class="portal-frame">
            <div class="portal-header">
                <h1 class="portal-title">DR. STONES</h1>
                <p class="portal-subtitle">Ancient Treasures Await</p>
            </div>

            <div class="ancient-divider">
                <div class="divider-line"></div>
                <span class="divider-symbol">✦</span>
                <div class="divider-line"></div>
            </div>

            <div class="portal-choices">
                <a href="index.php" class="ancient-portal-card">
                    <span class="role-badge">EXPLORER</span>
                    <div class="card-icon">👤</div>
                    <h2 class="card-title">User Login</h2>
                    <p class="card-description">
                        Enter the marketplace and discover ancient stones of power
                    </p>
                </a>

                <a href="admin-login.php" class="ancient-portal-card">
                    <span class="role-badge">GUARDIAN</span>
                    <div class="card-icon">⚔️</div>
                    <h2 class="card-title">Admin Login</h2>
                    <p class="card-description">
                        Access the sacred chamber and manage the ancient collection
                    </p>
                </a>
            </div>

            <div class="portal-footer">
                <p>"In every stone lies a story, in every story lies power"</p>
            </div>
        </div>
    </div>
</body>
</html>
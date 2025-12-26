<?php
session_start();
require_once 'config.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ancient Stone Emporium - Relics of Eternity</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts - Ancient Style -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700;900&family=Cinzel:wght@400;600;700&family=IM+Fell+English:ital@0;1&family=Uncial+Antiqua&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">

    <!-- Google Identity Services -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>

    <!-- Ancient Texture Overlay -->
    <div class="ancient-texture"></div>

    <!-- ====================== NAVBAR ====================== -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top ancient-nav">
        <div class="container">
            <a class="navbar-brand ancient-brand" href="#home">
                <i class="fas fa-ankh"></i>
                <span class="brand-rune">⟡</span>
                <span class="brand-text">Ancient Stone Emporium</span>
                <span class="brand-rune">⟡</span>
            </a>
            <button class="navbar-toggler ancient-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto align-items-center">

                    <li class="nav-item active">
                        <a class="nav-link ancient-link" href="#home">
                            <i class="fas fa-home"></i> 
                            <span>Home</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link ancient-link" href="#vault">
                            <i class="fas fa-store"></i> 
                            <span>Vault</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link ancient-link" href="#about">
                            <i class="fas fa-book-dead"></i> 
                            <span>Chronicles</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link ancient-link" href="#contact">
                            <i class="fas fa-scroll"></i> 
                            <span>Sell Stone</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link cart-link ancient-link" href="#" data-toggle="modal" data-target="#cartModal">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-badge" id="cartCount">0</span>
                        </a>
                    </li>

                    <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown ml-2">
                        <a class="nav-link dropdown-toggle ancient-link" href="#" id="userMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user"></i>
                                <span><?php echo htmlspecialchars(getCurrentUserName() ?? ''); ?></span>
                            </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userMenu">
                            <?php if (isAdmin()): ?>
                                <a class="dropdown-item" href="admin.php"><i class="fas fa-crown mr-2"></i>Admin Panel</a>
                            <?php endif; ?>
                            <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
                        </div>
                    </li>
                    <?php else: ?>
                    <li class="nav-item ml-2">
                        <button class="btn btn-ancient-outline" data-toggle="modal" data-target="#authModal">
                            <i class="fas fa-scroll"></i> Join / Enter
                        </button>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash messages -->
    <?php if (!empty($_SESSION['success']) || !empty($_SESSION['error'])): ?>
    <div class="container mt-5 pt-4">
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['success']); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['success']); endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['error']); endif; ?>
    </div>
    <?php endif; ?>

    <!-- ====================== AUTH MODAL (combined login/register) ====================== -->
    <div class="modal fade" id="authModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content ancient-modal">
                <div class="modal-header ancient-modal-header">
                    <h5 class="modal-title-ancient">
                        <i class="fas fa-user-circle mr-2"></i>
                        <span id="authTitle">Enter the Sacred Vault</span>
                    </h5>
                    <div class="auth-toggle" role="tablist" aria-label="Auth toggle">
                        <button id="toggleLogin" class="active" type="button">Login</button>
                        <button id="toggleRegister" type="button">Register</button>
                    </div>
                    <button type="button" class="close ancient-close" data-dismiss="modal">
                        <span>✕</span>
                    </button>
                </div>

                <div class="modal-body p-4">

                    <!-- Social / Google sign-in area -->
                    <div class="mb-3 auth-socials">
                        <div id="googleSignInLogin"></div>
                        <div id="googleSignInRegister" style="display:none;"></div>

                        <!-- fallback visible button -->
                        <button id="googleFallback" class="btn btn-google btn-block mt-2" type="button">
                            <i class="fab fa-google mr-2"></i> Continue with Google
                        </button>
                    </div>

                    <div class="ancient-or"><span>⟡ or ⟡</span></div>

                    <!-- LOGIN FORM -->
                    <form id="loginForm" class="auth-form" method="POST" action="login.php" style="display:block;" aria-live="polite">
                        <div id="loginError" class="form-error" style="display:none;"></div>

                        <div class="form-group">
                            <label for="loginEmail" class="ancient-label">
                                <i class="fas fa-envelope mr-2" aria-hidden="true"></i> Sacred Seal (Email)
                            </label>
                            <input id="loginEmail" name="email" type="email" class="form-control ancient-input" placeholder="you@realm.example" required aria-required="true">
                        </div>

                        <div class="form-group">
                            <label for="loginPassword" class="ancient-label">
                                <i class="fas fa-lock mr-2" aria-hidden="true"></i> Secret Rune (Password)
                            </label>
                            <input id="loginPassword" name="password" type="password" class="form-control ancient-input" placeholder="Enter your secret rune" required aria-required="true">
                        </div>

                        <button type="submit" class="btn btn-ancient-primary btn-block btn-lg mt-2" aria-label="Log in">
                            <i class="fas fa-door-open mr-2" aria-hidden="true"></i> Unlock
                        </button>
                        <small class="form-text helper-center mt-2">Forgot your rune? <a href="forgot.php">Recover it</a></small>
                    </form>

                    <!-- REGISTER FORM -->
                    <form id="registerForm" class="auth-form" method="POST" action="register.php" style="display:none;">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="regFirstName" class="ancient-label">First Name</label>
                                <input id="regFirstName" name="first_name" type="text" class="form-control ancient-input" placeholder="Marcus" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="regLastName" class="ancient-label">Last Name</label>
                                <input id="regLastName" name="last_name" type="text" class="form-control ancient-input" placeholder="Aurelius" required>
                            </div>
                        </div>

                        <div id="registerError" class="form-error" style="display:none;"></div>

                        <div class="form-group">
                            <label for="regEmail" class="ancient-label">
                                <i class="fas fa-envelope mr-2" aria-hidden="true"></i> Sacred Seal
                            </label>
                            <input id="regEmail" name="email" type="email" class="form-control ancient-input" placeholder="you@realm.example" required>
                        </div>

                        <div class="form-group">
                            <label for="regPassword" class="ancient-label">
                                <i class="fas fa-lock mr-2" aria-hidden="true"></i> Create Secret Rune
                            </label>
                            <input id="regPassword" name="password" type="password" class="form-control ancient-input" placeholder="Minimum 8 characters" minlength="8" required>
                        </div>

                        <div class="form-group">
                            <label for="regConfirm" class="ancient-label">Confirm Secret Rune</label>
                            <input id="regConfirm" name="confirm_password" type="password" class="form-control ancient-input" placeholder="Re-enter your secret rune" required>
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input ancient-checkbox" id="agreeTerms" name="agree_terms" required>
                            <label class="form-check-label ancient-label small" for="agreeTerms">
                                I agree to the <a href="terms.php">Ancient Covenant & Terms</a>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-ancient-primary btn-block btn-lg mt-2" aria-label="Register">
                            <i class="fas fa-scroll mr-2" aria-hidden="true"></i> Create Account
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- ====================== CART MODAL ====================== -->
    <div class="modal fade" id="cartModal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content ancient-modal">
                <div class="modal-header ancient-modal-header">
                    <h5 class="modal-title-ancient">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        <span>Your Treasure Chest</span>
                    </h5>
                    <button type="button" class="close ancient-close" data-dismiss="modal">
                        <span>✕</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <div id="cartItems"></div>
                    <div class="cart-total mt-4 pt-4 border-top border-ancient">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="text-ancient-gold">Total Investment:</h4>
                            <h3 class="text-ancient-gold"><span id="cartTotal">0</span> Gold Coins</h3>
                        </div>
                    </div>
                    <button class="btn btn-ancient-primary btn-block btn-lg mt-4" id="checkoutBtn">
                        <i class="fas fa-gem mr-2"></i>Proceed to Sacred Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================== HERO SECTION ====================== -->
    <div class="ancient-hero" id="home">
        <div class="hero-ancient-overlay"></div>
        <div class="hero-vignette"></div>
        
        <div class="container hero-content">
            <div class="text-center col-lg-10 mx-auto">
                <div class="hero-ornament-top">⟡ ◈ ⟡</div>
                
                <h1 class="hero-title-ancient mb-4">
                    Relics of the<br>
                    <span class="hero-highlight">Eternal Earth</span>
                </h1>
                
                <div class="hero-rune-divider">
                    <span>✦</span>
                    <span class="rune-line"></span>
                    <span>◈</span>
                    <span class="rune-line"></span>
                    <span>✦</span>
                </div>
                
                <p class="hero-subtitle-ancient mb-5">
                    Each stone whispers tales of forgotten civilizations,<br>
                    bearing the weight of millennia within its sacred essence
                </p>

                <div class="hero-buttons">
                    <a href="#vault" class="btn btn-ancient-hero btn-lg mr-3">
                        <i class="fas fa-gem mr-2"></i>Discover Treasures
                    </a>
                    <a href="#contact" class="btn btn-ancient-hero-outline btn-lg">
                        <i class="fas fa-scroll mr-2"></i>Offer Your Relic
                    </a>
                </div>
                
                <div class="hero-ornament-bottom">⟡ ◈ ⟡</div>
            </div>
        </div>

        <div class="hero-scroll-indicator">
            <i class="fas fa-angle-double-down"></i>
        </div>
    </div>

    <!-- ====================== STONE VAULT SECTION ====================== -->
    <div class="product-section-ancient py-5" id="vault">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-ornament-top">◈</div>
                <h2 class="section-title-ancient">The Sacred Vault</h2>
                <div class="title-underline-ancient">
                    <span>✦</span>
                    <span class="line"></span>
                    <span>◈</span>
                    <span class="line"></span>
                    <span>✦</span>
                </div>
                <p class="section-subtitle-ancient">Browse our collection of ancient relics</p>
            </div>

            <div class="row" id="stoneGrid">
                <?php
                // Load stones from the database and render as cards
                $conn = getDBConnection();
                $sql = "SELECT id, name, subtitle, description, price, weight, origin, era, image FROM items ORDER BY id ASC";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Resolve image(s) and fallback to placeholder if file(s) missing
                        $rawImg = $row['image'] ?? '';
                        $images = [];

                        // Accept JSON arrays, or comma/pipe separated lists, or single filenames/paths
                        $candidates = [];
                        $r = trim($rawImg);
                        if ($r !== '') {
                            // try JSON decode first
                            $decoded = json_decode($r, true);
                            if (is_array($decoded)) {
                                $candidates = $decoded;
                            } else {
                                // split by common separators
                                if (strpos($r, '|') !== false) $parts = explode('|', $r);
                                elseif (strpos($r, ',') !== false) $parts = explode(',', $r);
                                else $parts = [$r];
                                foreach ($parts as $p) $candidates[] = trim($p);
                            }
                        }

                        foreach ($candidates as $cand) {
                            if (!$cand) continue;
                            $cand = ltrim($cand, "/\\");
                            // try a few likely locations: as-stored, in uploads/, in assets/
                            $tries = [$cand, 'uploads/' . $cand, 'assets/' . $cand];
                            $found = null;
                            foreach ($tries as $t) {
                                if (file_exists(__DIR__ . '/' . $t)) { $found = $t; break; }
                            }
                            if ($found) $images[] = $found;
                        }

                        if (empty($images)) {
                            $images[] = 'assets/placeholder.png';
                        }

                        // build HTML for either single image or carousel for multiple images
                        $carouselId = 'carousel-' . ((int)$row['id']);
                        if (count($images) === 1) {
                            $img = htmlspecialchars($images[0] ?? '');
                            $imgHtml = '<img src="' . $img . '" alt="' . htmlspecialchars($row['name'] ?? '') . '" class="ancient-product-image">';
                        } else {
                            $indicators = '';
                            $itemsHtml = '';
                            foreach ($images as $idx => $ip) {
                                $active = $idx === 0 ? ' active' : '';
                                $indicators .= '<li data-target="#' . $carouselId . '" data-slide-to="' . $idx . '"' . ($idx===0? ' class="active"': '') . '></li>';
                                $itemsHtml .= '<div class="carousel-item' . $active . '">'
                                            . '<img src="' . htmlspecialchars($ip ?? '') . '" alt="' . htmlspecialchars($row['name'] ?? '') . '" class="d-block w-100 ancient-product-image">'
                                            . '</div>';
                            }
                            $imgHtml = '<div id="' . $carouselId . '" class="carousel slide" data-ride="carousel">'
                                     . '<ol class="carousel-indicators">' . $indicators . '</ol>'
                                     . '<div class="carousel-inner">' . $itemsHtml . '</div>'
                                     . '<a class="carousel-control-prev" href="#' . $carouselId . '" role="button" data-slide="prev">'
                                     . '<span class="carousel-control-prev-icon" aria-hidden="true"></span>'
                                     . '<span class="sr-only">Previous</span>'
                                     . '</a>'
                                     . '<a class="carousel-control-next" href="#' . $carouselId . '" role="button" data-slide="next">'
                                     . '<span class="carousel-control-next-icon" aria-hidden="true"></span>'
                                     . '<span class="sr-only">Next</span>'
                                     . '</a>'
                                     . '</div>';
                        }
                        $name = htmlspecialchars($row['name'] ?? '');
                        $subtitle = htmlspecialchars($row['subtitle'] ?? '');
                        $desc = htmlspecialchars($row['description'] ?? '');
                        $price = htmlspecialchars($row['price'] ?? '0');
                        $weight = htmlspecialchars($row['weight'] ?? '');
                        $origin = htmlspecialchars($row['origin'] ?? '');
                        $era = htmlspecialchars($row['era'] ?? '');
                        $id = (int)$row['id'];

                        echo "<div class=\"col-md-4 mb-4\">\n" .
                            "  <div class=\"card ancient-product-card h-100\">\n" .
                            "    <div class=\"product-image-frame\">\n" .
                            "      {$imgHtml}\n" .
                            "    </div>\n" .
                            "    <div class=\"card-body d-flex flex-column\">\n" .
                            "      <div class=\"d-flex justify-content-between align-items-start\">\n" .
                            "        <div>\n" .
                            "          <h5 class=\"product-title-ancient\">{$name}</h5>\n" .
                            "          <div class=\"product-subtitle\">{$subtitle}</div>\n" .
                            "        </div>\n" .
                            "        <div class=\"product-badge-ancient\">{$price} Gold</div>\n" .
                            "      </div>\n" .
                            "      <p class=\"product-description-ancient\">{$desc}</p>\n" .
                            "      <ul class=\"list-unstyled small text-muted mb-3\">\n" .
                            "        <li><strong>Origin:</strong> {$origin}</li>\n" .
                            "        <li><strong>Era:</strong> {$era}</li>\n" .
                            "        <li><strong>Weight:</strong> {$weight}</li>\n" .
                            "      </ul>\n" .
                            "      <div class=\"mt-auto\">\n" .
                            "        <button class=\"btn btn-ancient-primary btn-block add-to-cart\" data-id=\"{$id}\" data-name=\"{$name}\" data-price=\"{$price}\">\n" .
                            "          <i class=\"fas fa-cart-plus mr-2\"></i>Add to Chest\n" .
                            "        </button>\n" .
                            "      </div>\n" .
                            "    </div>\n" .
                            "  </div>\n" .
                            "</div>\n";
                    }
                } else {
                    echo '<div class="col-12"><p class="text-center text-muted">No stones available right now.</p></div>';
                }

                $conn->close();
                ?>
            </div>
        </div>
    </div>

    <!-- ====================== ABOUT SECTION ====================== -->
    <div class="about-section-ancient py-5" id="about">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-ornament-top">◈</div>
                <h2 class="section-title-ancient">The Chronicles</h2>
                <div class="title-underline-ancient">
                    <span>✦</span>
                    <span class="line"></span>
                    <span>◈</span>
                    <span class="line"></span>
                    <span>✦</span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card ancient-product-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-shield-alt fa-3x text-ancient-gold mb-3"></i>
                            <h4 class="text-ancient-gold">Authentic Relics</h4>
                            <p class="ancient-subtext">Every stone is verified by our guild of ancient experts</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card ancient-product-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-history fa-3x text-ancient-gold mb-3"></i>
                            <h4 class="text-ancient-gold">Timeless History</h4>
                            <p class="ancient-subtext">Each piece carries millennia of stories and power</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card ancient-product-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-hand-holding-heart fa-3x text-ancient-gold mb-3"></i>
                            <h4 class="text-ancient-gold">Sacred Covenant</h4>
                            <p class="ancient-subtext">Protected by ancient guarantees and eternal promises</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================== CONTACT SECTION (Sell Stone) ====================== -->
    <div class="contact-section-ancient py-5" id="contact">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-ornament-top">◈</div>
                <h2 class="section-title-ancient">Present Your Sacred Treasure</h2>
                <div class="title-underline-ancient">
                    <span>✦</span>
                    <span class="line"></span>
                    <span>◈</span>
                    <span class="line"></span>
                    <span>✦</span>
                </div>
                <p class="section-subtitle-ancient">Share your extraordinary relic with the Guild of Keepers</p>
            </div>

            <div class="col-lg-8 mx-auto">
                <div class="card ancient-contact-card">
                    <div class="card-body p-5">
                        <div class="contact-intro text-center mb-4">
                            <i class="fas fa-scroll contact-icon"></i>
                            <p class="ancient-subtext">
                                Do you possess a stone of remarkable origin? The Guild seeks artifacts 
                                worthy of preservation in our eternal vault.
                            </p>
                        </div>

                        <form id="sellStoneForm" method="POST" action="submit_proposals.php" enctype="multipart/form-data">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="ancient-label">
                                        <i class="fas fa-user mr-2"></i>Your Name
                                    </label>
                                    <input type="text" class="form-control ancient-input" name="sellerName" placeholder="Full name of the bearer" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="ancient-label">
                                        <i class="fas fa-envelope mr-2"></i>Sacred Seal
                                    </label>
                                    <input type="email" class="form-control ancient-input" name="sellerEmail" placeholder="your.seal@realm.ancient" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="ancient-label">
                                    <i class="fas fa-gem mr-2"></i>Stone Name
                                </label>
                                <input type="text" class="form-control ancient-input" name="stoneName" placeholder="Ancient Obsidian" required>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="ancient-label">
                                        <i class="fas fa-coins mr-2"></i>Desired Price (Gold Coins)
                                    </label>
                                    <input type="number" class="form-control ancient-input" name="stonePrice" placeholder="299" min="1" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="ancient-label">
                                        <i class="fas fa-weight mr-2"></i>Weight
                                    </label>
                                    <input type="text" class="form-control ancient-input" name="stoneWeight" placeholder="2.4 lbs" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="ancient-label">
                                        <i class="fas fa-map-marked-alt mr-2"></i>Origin
                                    </label>
                                    <input type="text" class="form-control ancient-input" name="stoneOrigin" placeholder="Mediterranean Depths" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="ancient-label">
                                        <i class="fas fa-calendar-alt mr-2"></i>Era/Age
                                    </label>
                                    <input type="text" class="form-control ancient-input" name="stoneEra" placeholder="3000 BCE" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="ancient-label">
                                    <i class="fas fa-feather-alt mr-2"></i>Chronicle of Your Relic
                                </label>
                                <textarea class="form-control ancient-input ancient-textarea" name="stoneDescription" rows="5" 
                                    placeholder="Describe the stone's origin, appearance, and any legends associated with it..." required></textarea>
                            </div>

                            <div class="form-group">
                                <label class="ancient-label">
                                    <i class="fas fa-images mr-2"></i>Sacred Depictions
                                </label>
                                <div class="custom-file ancient-file">
                                    <input type="file" class="custom-file-input" id="stoneImages" name="stoneImages" accept="image/*" required>
                                    <label class="custom-file-label" for="stoneImages">
                                        <i class="fas fa-cloud-upload-alt mr-2"></i>
                                        Upload image of your treasure
                                    </label>
                                </div>
                            </div>

                            <div class="form-group form-check mt-4">
                                <input type="checkbox" class="form-check-input ancient-checkbox" id="authenticCheck" required>
                                <label class="form-check-label ancient-label small" for="authenticCheck">
                                    <i class="fas fa-certificate mr-1"></i>
                                    I affirm this relic is authentic and obtained through righteous means
                                </label>
                            </div>

                            <button type="submit" class="btn btn-ancient-primary btn-lg btn-block mt-4">
                                <i class="fas fa-paper-plane mr-2"></i>Dispatch Proposal to Guild Masters
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================== FOOTER ====================== -->
    <footer class="ancient-footer">
        <div class="container">
            <div class="footer-ornament-top">
                <span>✦</span>
                <span class="line"></span>
                <span>◈</span>
                <span class="line"></span>
                <span>✦</span>
            </div>
            
            <div class="text-center py-4">
                <div class="footer-brand mb-3">
                    <i class="fas fa-ankh"></i>
                    <span class="brand-rune">⟡</span>
                    <span>Ancient Stone Emporium</span>
                    <span class="brand-rune">⟡</span>
                </div>
                
                <p class="footer-text mb-2">
                    Guardians of Earth's Eternal Treasures since Time Immemorial
                </p>
                
                <div class="footer-rune">◈</div>
                
                <p class="footer-copyright mb-0">
                    © MMXXIV Ancient Stone Emporium. All relics preserved under sacred covenant.
                </p>
            </div>
        </div>
    </footer>

    <!-- ====================== JAVASCRIPT ====================== -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // ====================== GLOBAL VARIABLES ====================== 
    var cart = [];
    const GOOGLE_CLIENT_ID = <?php echo json_encode(defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : 'YOUR_GOOGLE_CLIENT_ID_HERE.apps.googleusercontent.com'); ?>;
    const TAX_RATE = 0.05; // 5% VAT

    // Export safe user object from server-side
    const APP_USER = <?php echo json_encode([
        'logged' => isLoggedIn(),
        'role' => (isAdmin() ? 'ADMIN' : (isLoggedIn() ? 'USER' : null)),
        'name' => getCurrentUserName()
    ]); ?>;

    // ====================== INITIALIZATION ====================== 
    document.addEventListener("DOMContentLoaded", function() {
        loadCart();
        updateCartUI();
        initializeForms();
        initAuthToggle();
        initGoogleSignIn();
        initSmoothScroll();
        initFileUpload();
    });

    // ====================== CART FUNCTIONS ====================== 
    function loadCart() {
        try {
            const raw = localStorage.getItem('ancient_cart');
            if (raw) {
                cart = JSON.parse(raw) || [];
            }
        } catch (e) {
            console.error('Failed to load cart:', e);
            cart = [];
        }
    }

    function saveCart() {
        try {
            localStorage.setItem('ancient_cart', JSON.stringify(cart));
        } catch (e) {
            console.error('Failed to save cart:', e);
        }
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        const div = document.createElement('div');
        div.textContent = String(str);
        return div.innerHTML;
    }

    function formatGold(n) {
        const num = Number(n) || 0;
        return num.toFixed(2);
    }

    function calculateCartTotals() {
        const subtotal = cart.reduce((sum, item) => {
            const price = Number(item.price) || 0;
            const quantity = Number(item.quantity) || 1;
            return sum + (price * quantity);
        }, 0);

        const tax = Math.round(subtotal * TAX_RATE * 100) / 100;
        const grandTotal = Math.round((subtotal + tax) * 100) / 100;

        return { subtotal, tax, grandTotal };
    }

    function updateCartUI() {
        const countEl = document.getElementById('cartCount');
        const totalEl = document.getElementById('cartTotal');
        const itemsEl = document.getElementById('cartItems');

        const count = cart.reduce((sum, item) => sum + (Number(item.quantity) || 1), 0);
        const { subtotal, tax, grandTotal } = calculateCartTotals();

        if (countEl) countEl.textContent = count;
        if (totalEl) totalEl.textContent = formatGold(grandTotal);

        if (itemsEl) {
            if (!cart.length) {
                itemsEl.innerHTML = '<p class="text-center text-muted py-4">Your treasure chest is empty. Browse our vault to discover ancient relics!</p>';
            } else {
                const rows = cart.map(item => {
                    const qty = Number(item.quantity) || 1;
                    const price = Number(item.price) || 0;
                    const lineTotal = Math.round(price * qty * 100) / 100;
                    return `
                        <tr>
                            <td><strong>${escapeHtml(item.name)}</strong></td>
                            <td class="text-right">${formatGold(price)}</td>
                            <td class="text-center">${escapeHtml(String(qty))}</td>
                            <td class="text-right">${formatGold(lineTotal)}</td>
                            <td class="text-right">
                                <button class="btn btn-sm btn-outline-danger remove-from-cart" data-id="${escapeHtml(String(item.id))}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>`;
                }).join('');

                itemsEl.innerHTML = `
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="text-right">Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rows}
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex justify-content-between mb-2">
                            <div class="text-muted">Subtotal:</div>
                            <div>${formatGold(subtotal)} Gold</div>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="text-muted">Tax (5%):</div>
                            <div>${formatGold(tax)} Gold</div>
                        </div>
                        <div class="d-flex justify-content-between font-weight-bold mt-2 pt-2 border-top">
                            <div>Grand Total:</div>
                            <div class="text-ancient-gold">${formatGold(grandTotal)} Gold</div>
                        </div>
                    </div>
                `;
            }
        }
    }

    // ====================== CART EVENT HANDLERS ====================== 
    document.addEventListener('click', function(e) {
        const addBtn = e.target.closest('.add-to-cart');
        if (addBtn) {
            e.preventDefault();
            
            // Check if user is logged in
            if (!APP_USER || !APP_USER.logged) {
                $('#authModal').modal('show');
                const toggleLogin = document.getElementById('toggleLogin');
                if (toggleLogin) toggleLogin.click();
                return;
            }

            const id = addBtn.getAttribute('data-id');
            const name = addBtn.getAttribute('data-name');
            const price = addBtn.getAttribute('data-price');

            if (!id) return;

            // Find existing item or add new
            const existing = cart.find(item => String(item.id) === String(id));
            if (existing) {
                existing.quantity = (Number(existing.quantity) || 1) + 1;
            } else {
                cart.push({
                    id: id,
                    name: name || 'Item',
                    price: price || 0,
                    quantity: 1
                });
            }

            saveCart();
            updateCartUI();

            // Visual feedback
            addBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Added!';
            setTimeout(() => {
                addBtn.innerHTML = '<i class="fas fa-cart-plus mr-2"></i>Add to Chest';
            }, 1500);
        }

        const removeBtn = e.target.closest('.remove-from-cart');
        if (removeBtn) {
            e.preventDefault();
            const id = removeBtn.getAttribute('data-id');
            if (!id) return;

            cart = cart.filter(item => String(item.id) !== String(id));
            saveCart();
            updateCartUI();
        }
    });

    // ====================== AUTH TOGGLE ====================== 
    function initAuthToggle() {
        const tLogin = document.getElementById('toggleLogin');
        const tRegister = document.getElementById('toggleRegister');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const authTitle = document.getElementById('authTitle');
        const gLogin = document.getElementById('googleSignInLogin');
        const gRegister = document.getElementById('googleSignInRegister');

        if (!tLogin || !tRegister || !loginForm || !registerForm) return;

        function showLogin() {
            tLogin.classList.add('active');
            tRegister.classList.remove('active');
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
            if (authTitle) authTitle.textContent = 'Enter the Sacred Vault';
            if (gLogin) gLogin.style.display = '';
            if (gRegister) gRegister.style.display = 'none';
        }

        function showRegister() {
            tRegister.classList.add('active');
            tLogin.classList.remove('active');
            loginForm.style.display = 'none';
            registerForm.style.display = 'block';
            if (authTitle) authTitle.textContent = 'Inscribe Your Name in the Tome';
            if (gLogin) gLogin.style.display = 'none';
            if (gRegister) gRegister.style.display = '';
        }

        tLogin.addEventListener('click', showLogin);
        tRegister.addEventListener('click', showRegister);

        // Default to login
        showLogin();

        // Maintain state when modal shown
        $('#authModal').on('shown.bs.modal', function() {
            if (registerForm.style.display === 'block') {
                showRegister();
            } else {
                showLogin();
            }
        });
    }

    // ====================== FORM HANDLERS ====================== 
    function initializeForms() {
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const checkoutBtn = document.getElementById('checkoutBtn');

        // Login form
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(loginForm);
                const submitBtn = loginForm.querySelector('button[type="submit"]');
                
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Unlocking...';
                }

                fetch('login.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data && data.success) {
                        $('#authModal').modal('hide');
                        location.reload();
                    } else {
                        alert(data && data.message ? data.message : 'Login failed. Please try again.');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fas fa-door-open mr-2"></i>Unlock Gateway';
                        }
                    }
                })
                .catch(error => {
                    console.error('Login error:', error);
                    alert('An error occurred during login. Please try again.');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-door-open mr-2"></i>Unlock Gateway';
                    }
                });
            });
        }

        // Register form
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const password = document.getElementById('regPassword');
                const confirm = document.getElementById('regConfirm');

                if (password && confirm && password.value !== confirm.value) {
                    alert('Secret runes do not align. Please ensure both passwords match.');
                    confirm.focus();
                    return;
                }

                if (password && password.value.length < 8) {
                    alert('Your secret rune must contain at least 8 sacred symbols.');
                    password.focus();
                    return;
                }

                const formData = new FormData(registerForm);
                const submitBtn = registerForm.querySelector('button[type="submit"]');
                
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Inscribing...';
                }

                fetch('register.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data && data.success) {
                        alert('Registration complete! You may now enter the vault.');
                        registerForm.reset();
                        const toggleLogin = document.getElementById('toggleLogin');
                        if (toggleLogin) toggleLogin.click();
                    } else {
                        alert(data && data.message ? data.message : 'Registration failed. Please try again.');
                    }
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-scroll mr-2"></i>Inscribe in Eternal Record';
                    }
                })
                .catch(error => {
                    console.error('Registration error:', error);
                    alert('An error occurred during registration. Please try again.');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-scroll mr-2"></i>Inscribe in Eternal Record';
                    }
                });
            });
        }

        // Checkout button
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', function() {
                if (!cart.length) {
                    alert('Your treasure chest is empty. Browse our vault to discover ancient relics!');
                    return;
                }

                const { grandTotal } = calculateCartTotals();

                if (!confirm(`Proceed with checkout? Total: ${formatGold(grandTotal)} Gold Coins`)) {
                    return;
                }

                checkoutBtn.disabled = true;
                checkoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

                fetch('controls.php?action=placeOrder', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ items: cart })
                })
                .then(async response => {
                    const text = await response.text();
                    if (text.trim().startsWith('<')) {
                        throw new Error('Received HTML response instead of JSON');
                    }
                    return JSON.parse(text);
                })
                .then(data => {
                    if (data && data.success) {
                        alert(`Order placed successfully! Total: ${formatGold(grandTotal)} Gold Coins`);
                        cart = [];
                        saveCart();
                        updateCartUI();
                        $('#cartModal').modal('hide');
                    } else {
                        alert(data && data.message ? data.message : 'Order failed. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Checkout error:', error);
                    alert('An error occurred during checkout. Please try again.');
                })
                .finally(() => {
                    checkoutBtn.disabled = false;
                    checkoutBtn.innerHTML = '<i class="fas fa-gem mr-2"></i>Proceed to Sacred Checkout';
                });
            });
        }
    }

    // ====================== GOOGLE SIGN-IN ====================== 
    function initGoogleSignIn() {
        window.handleCredentialResponse = function(response) {
            if (!response || !response.credential) {
                alert('Invalid Google response. Please try again.');
                return;
            }

            fetch('google_auth.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_token: response.credential })
            })
            .then(response => response.json())
            .then(data => {
                if (data && data.success) {
                    location.reload();
                } else {
                    alert(data && data.message ? data.message : 'Google sign-in failed. Please try again.');
                }
            })
            .catch(error => {
                console.error('Google auth error:', error);
                alert('An error occurred during Google authentication. Please try again.');
            });
        };

        // Initialize Google Sign-In
        function tryInitGoogle() {
            if (window.google && google.accounts && google.accounts.id) {
                try {
                    google.accounts.id.initialize({
                        client_id: GOOGLE_CLIENT_ID,
                        callback: handleCredentialResponse
                    });

                    const gLogin = document.getElementById('googleSignInLogin');
                    const gRegister = document.getElementById('googleSignInRegister');

                    if (gLogin) {
                        google.accounts.id.renderButton(gLogin, {
                            theme: 'outline',
                            size: 'large',
                            width: '100%',
                            text: 'continue_with'
                        });
                    }

                    if (gRegister) {
                        google.accounts.id.renderButton(gRegister, {
                            theme: 'outline',
                            size: 'large',
                            width: '100%',
                            text: 'signup_with'
                        });
                    }

                    const fallback = document.getElementById('googleFallback');
                    if (fallback) {
                        // Hide the manual fallback button if Google's button was rendered
                        fallback.style.display = 'none';
                        // still wire fallback to prompt if needed (in case of dynamic UI changes)
                        fallback.addEventListener('click', () => {
                            google.accounts.id.prompt();
                        });
                    }
                } catch (error) {
                    console.warn('Google Sign-In initialization error:', error);
                }
            } else {
                setTimeout(tryInitGoogle, 300);
            }
        }

        tryInitGoogle();
    }

    // ====================== SMOOTH SCROLL ====================== 
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#' || href === '#cartModal' || href === '#authModal') return;

                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const offsetTop = target.offsetTop - 80;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    // ====================== FILE UPLOAD LABEL ====================== 
    function initFileUpload() {
        const stoneImages = document.getElementById('stoneImages');
        if (stoneImages) {
            stoneImages.addEventListener('change', function() {
                const label = this.nextElementSibling;
                if (label) {
                    if (this.files.length > 0) {
                        label.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + escapeHtml(this.files[0].name);
                    } else {
                        label.innerHTML = '<i class="fas fa-cloud-upload-alt mr-2"></i>Upload image of your treasure';
                    }
                }
            });
        }
    }
    </script>

</body>
</html>
<?php
session_start();
require_once 'config.php';

if (!isLoggedIn()) {
    $forceLogin = true;
} else {
    $forceLogin = false;
    $user_name = getCurrentUserName();
}
?>
<!DOCTYPE html>
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

                <!-- <li class="nav-item ml-2">
                    <a href="admin-login.php" class="btn btn-ancient-danger">
                        <i class="fas fa-user-shield"></i> Admin
                    </a>
                </li>                     -->
                    <li class="nav-item ml-2">
                        <button class="btn btn-ancient-outline" data-toggle="modal" data-target="#registerModal">
                            <i class="fas fa-scroll"></i> Join Guild
                        </button>
                    </li>

                    <li class="nav-item ml-2">
                        <button class="btn btn-ancient-primary" data-toggle="modal" data-target="#loginModal">
                            <i class="fas fa-door-open"></i> Enter
                        </button>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <!-- ====================== LOGIN MODAL ====================== -->
    <div class="modal fade" id="loginModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ancient-modal">
                <div class="modal-header ancient-modal-header">
                    <h5 class="modal-title-ancient">
                        <i class="fas fa-key mr-2"></i>
                        <span>Enter the Sacred Vault</span>
                    </h5>
                    <button type="button" class="close ancient-close" data-dismiss="modal">
                        <span>✕</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="relic-divider">
                            <span class="relic-symbol">◈</span>
                        </div>
                        <p class="ancient-subtext">Access your treasured collection</p>
                    </div>

                    <div class="social-login mb-3">
                        <button class="btn btn-block btn-ancient-social">
                            <i class="fab fa-google mr-2"></i> Continue with Google
                        </button>
                    </div>

                    <div class="ancient-or"><span>⟡ or ⟡</span></div>

                    <form id="loginForm" method="POST" action="login.php">
                        <div class="form-group">
                            <label class="ancient-label">
                                <i class="fas fa-envelope mr-2"></i>Sacred Seal (Email)
                            </label>
                            <input type="email" class="form-control ancient-input" placeholder="your.seal@realm.ancient" required name="email">
                        </div>

                        <div class="form-group">
                            <label class="ancient-label">
                                <i class="fas fa-lock mr-2"></i>Secret Rune (Password)
                            </label>
                            <input type="password" class="form-control ancient-input" placeholder="Enter your secret rune" required name="password">
                        </div>

                        <button type="submit" class="btn btn-ancient-primary btn-block btn-lg mt-4">
                            <i class="fas fa-door-open mr-2"></i>Unlock Gateway
                        </button>
                    </form>
                    <div class="text-center mt-3">
    <span>Not yet a Keeper? </span>
    <a href="#" id="showRegisterModal">Join the Guild</a>
</div>

                </div>
            </div>
        </div>
    </div>

    <!-- ====================== REGISTER MODAL ====================== -->
    <div class="modal fade" id="registerModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ancient-modal">
                <div class="modal-header ancient-modal-header">
                    <h5 class="modal-title-ancient">
                        <i class="fas fa-feather-alt mr-2"></i>
                        <span>Inscribe Your Name in the Tome</span>
                    </h5>
                    <button type="button" class="close ancient-close" data-dismiss="modal">
                        <span>✕</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="relic-divider">
                            <span class="relic-symbol">◈</span>
                        </div>
                        <p class="ancient-subtext">Join the order of stone keepers</p>
                    </div>

                    <div class="social-login mb-3">
                        <button class="btn btn-block btn-ancient-social">
                            <i class="fab fa-google mr-2"></i> Sign with Google
                        </button>
                    </div>

                    <div class="ancient-or"><span>⟡ or ⟡</span></div>

                    <form id="registerForm" method="POST" action="register.php">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="ancient-label">First Name</label>
                                <input type="text" class="form-control ancient-input" placeholder="Marcus" required name="first_name">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="ancient-label">Last Name</label>
                                <input type="text" class="form-control ancient-input" placeholder="Aurelius" required name="last_name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="ancient-label">
                                <i class="fas fa-envelope mr-2"></i>Sacred Seal
                            </label>
                            <input type="email" class="form-control ancient-input" placeholder="your.seal@realm.ancient" required name="email">
                        </div>

                        <div class="form-group">
                            <label class="ancient-label">
                                <i class="fas fa-lock mr-2"></i>Create Secret Rune
                            </label>
                            <input type="password" class="form-control ancient-input" id="regPassword" placeholder="Minimum 8 sacred symbols" minlength="8" required name="password">
                        </div>

                        <div class="form-group">
                            <label class="ancient-label">Confirm Secret Rune</label>
                            <input type="password" class="form-control ancient-input" id="regConfirm" placeholder="Re-inscribe your rune" required name="confirm_password">
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input ancient-checkbox" id="agreeTerms" required>
                            <label class="form-check-label ancient-label small">
                                I swear by the ancient covenant to uphold the sacred terms
                            </label>
                        </div>

                        <button type="submit" class="btn btn-ancient-primary btn-block btn-lg mt-4">
                            <i class="fas fa-scroll mr-2"></i>Inscribe in Eternal Record
                        </button>
                    </form>

                    <div class="text-center mt-3">
    <span>Already a Keeper? </span>
    <a href="#" id="showLoginModal">Enter Vault</a>
</div>

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
                <!-- Stones will be dynamically loaded here -->
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

                        <form id="sellStoneForm" method="POST"action="submit_proposals.php" enctype="multipart/form-data">
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
                                <label class="form-check-label ancient-label small">
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
    // Stone Database
    let stones = [
        {
            id: 1,
            name: "Ancient Obsidian",
            subtitle: "Forged in Dragon's Fire",
            price: 299,
            originalPrice: 399,
            origin: "Mediterranean Depths",
            era: "3000 BCE",
            weight: "2.4 lbs",
            description: "Born from molten depths of primordial volcanoes, this obsidian relic bears witness to Earth's violent genesis.",
            image: "assets/image-removebg-preview.png"

        },
        {
            id: 2,
            name: "Sacred Jade",
            subtitle: "Emperor's Treasure",
            price: 450,
            originalPrice: 600,
            origin: "Ancient China",
            era: "2000 BCE",
            weight: "1.8 lbs",
            description: "This jade was once part of an imperial seal, radiating with the authority of forgotten dynasties.",
            image: "assets/jade.png"

        },
        {
            id: 3,
            name: "Mystic Amethyst",
            subtitle: "Seer's Vision Stone",
            price: 350,
            originalPrice: 475,
            origin: "Egyptian Temples",
            era: "1500 BCE",
            weight: "3.2 lbs",
            description: "Used by ancient oracles to peer into realms beyond mortal sight, this amethyst pulses with otherworldly energy.",
            image: "assets/mythelyst.png"

        },
        {
            id: 4,
            name: "Eternal Marble",
            subtitle: "Temple Guardian",
            price: 275,
            originalPrice: 350,
            origin: "Greek Acropolis",
            era: "500 BCE",
            weight: "5.6 lbs",
            description: "Carved from the sacred quarries that built the Parthenon, this marble echoes with hymns of ancient gods.",
            image: "assets/marble.png"

        },
        {
            id: 5,
            name: "Crimson Jasper",
            subtitle: "Warrior's Amulet",
            price: 320,
            originalPrice: 420,
            origin: "Roman Battlefields",
            era: "100 CE",
            weight: "2.1 lbs",
            description: "Carried by legionnaires into battle, this jasper is said to grant courage and protection to its bearer.Wore by Kings in the Past",
            image: "assets/crimson.png"

        },
        {
            id: 6,
            name: "Golden Topaz",
            subtitle: "Pharaoh's Crown Jewel",
            price: 550,
            originalPrice: 725,
            origin: "Valley of Kings",
            era: "1200 BCE",
            weight: "1.5 lbs",
            description: "Once adorning a pharaoh's death mask, this topaz shimmers with the golden light of Ra himself.",
            image: "assets/gold.png"

        }
    ];

    let cart = [];

    // Initialize page
    document.addEventListener("DOMContentLoaded", function() {
        loadStones();
        updateCartUI();
        initializeForms();
        initializeSmoothScroll();
    });

    // Load stones into grid
    function loadStones() {
        const grid = document.getElementById('stoneGrid');
        grid.innerHTML = stones.map(stone => `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card ancient-product-card h-100">
                    <div class="product-image-frame">
                        <div class="frame-corner frame-tl"></div>
                        <div class="frame-corner frame-tr"></div>
                        <div class="frame-corner frame-bl"></div>
                        <div class="frame-corner frame-br"></div>
                        <img src="${stone.image}" class="img-fluid ancient-product-image" alt="${stone.name}">
                    </div>
                    <div class="card-body p-4">
                        <div class="product-badge-ancient">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <h4 class="product-title-ancient mb-2" style="font-size: 1.5rem;">
                            ${stone.name}<br>
                            <span class="product-subtitle" style="font-size: 1rem;">${stone.subtitle}</span>
                        </h4>
                        <div class="product-rune-divider">◈</div>
                        <p class="product-description-ancient" style="font-size: 0.9rem; max-height: 80px; overflow: hidden;">
                            ${stone.description}
                        </p>
                        <div class="product-attributes mb-3">
                            <div class="attribute"><i class="fas fa-map-marked-alt"></i><span>${stone.origin}</span></div>
                            <div class="attribute"><i class="fas fa-calendar-alt"></i><span>${stone.era}</span></div>
                            <div class="attribute"><i class="fas fa-weight"></i><span>${stone.weight}</span></div>
                        </div>
                        <div class="product-price-ancient mb-3">
                            <span class="current-price-ancient" style="font-size: 2rem;">${stone.price}</span>
                            <span class="currency-ancient">Gold Coins</span>
                            ${stone.originalPrice ? `<div class="original-price-ancient"><span>${stone.originalPrice}</span></div>` : ''}
                        </div>
                        <button class="btn btn-ancient-primary btn-block" onclick="addToCart(${stone.id})">
                            <i class="fas fa-cart-plus mr-2"></i>Claim Treasure
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    // Add to cart
function addToCart(stoneId) {
<?php if (!isLoggedIn()): ?>
        $('#loginModal').modal({backdrop: 'static',keyboard: false });
        return;
<?php endif; ?>

    const stone = stones.find(s => s.id === stoneId);
    const existingItem = cart.find(item => item.id === stoneId);
    
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({ ...stone, quantity: 1 });
    }
    
    updateCartUI();
    showNotification(`${stone.name} added to your treasure chest!`);
}


    // Remove from cart
    function removeFromCart(stoneId) {
        cart = cart.filter(item => item.id !== stoneId);
        updateCartUI();
    }

    // Update quantity
    function updateQuantity(stoneId, change) {
        const item = cart.find(item => item.id === stoneId);
        if (item) {
            item.quantity += change;
            if (item.quantity <= 0) {
                removeFromCart(stoneId);
            } else {
                updateCartUI();
            }
        }
    }

    // Update cart UI
    function updateCartUI() {
        const cartCount = document.getElementById('cartCount');
        const cartItems = document.getElementById('cartItems');
        const cartTotal = document.getElementById('cartTotal');
        
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        
        cartCount.textContent = totalItems;
        cartTotal.textContent = totalPrice;
        
        if (cart.length === 0) {
            cartItems.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-gem fa-3x text-ancient-bronze mb-3"></i>
                    <p class="ancient-subtext">Your treasure chest is empty</p>
                    <p class="text-ancient-aged">Begin your collection by claiming ancient relics</p>
                </div>
            `;
        } else {
            cartItems.innerHTML = cart.map(item => `
                <div class="cart-item mb-3 p-3 border-ancient">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <img src="${item.image}" class="img-fluid" alt="${item.name}">
                        </div>
                        <div class="col-5">
                            <h5 class="text-ancient-gold mb-1">${item.name}</h5>
                            <p class="text-ancient-aged mb-0" style="font-size: 0.9rem;">${item.subtitle}</p>
                            <p class="text-ancient-bronze mb-0">${item.price} Gold Coins</p>
                        </div>
                        <div class="col-2">
                            <div class="d-flex align-items-center justify-content-center">
                                <button class="btn btn-sm btn-ancient-outline" onclick="updateQuantity(${item.id}, -1)">-</button>
                                <span class="mx-2 text-ancient-gold">${item.quantity}</span>
                                <button class="btn btn-sm btn-ancient-outline" onclick="updateQuantity(${item.id}, 1)">+</button>
                            </div>
                        </div>
                        <div class="col-2 text-right">
                            <button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }
    }


    // Show notification
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'ancient-notification';
        notification.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${message}`;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

// Initialize forms
function initializeForms() {
    // Login Form
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault(); // prevent default form submission

        const form = this;
        const formData = new FormData(form);

        // Send data to login.php
        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Gateway unlocked! Welcome, keeper of stones.');
                form.reset();
                $('#loginModal').modal('hide');
                location.reload(); // optional: refresh page to reflect logged-in state
            } else {
                alert('Login failed: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred. Please try again.');
        });
    });
}


        // Register Form
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('regPassword').value;
    const confirm = document.getElementById('regConfirm').value;

    if (password !== confirm) {
        e.preventDefault(); // stop submit only on error
        alert('Secret runes do not align.');
        return;
    }


});

const IS_LOGGED_IN = <?= isLoggedIn() ? 'true' : 'false' ?>;
// CHECKOUT (USER ONLY)
document.getElementById('checkoutBtn').addEventListener('click', function () {

    if (!cart.length) {
        alert('Your treasure chest is empty.');
        return;
    }

    fetch('controls.php?action=placeOrder', {
        method: 'POST',
        credentials: 'same-origin', // REQUIRED for PHP session
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ items: cart })
    })
.then(async res => {
    const text = await res.text();
    console.log('SERVER RAW RESPONSE:', text);

    // Check if response looks like HTML
    if (text.trim().startsWith('<')) {
        throw new Error('Server returned HTML instead of JSON');
    }

    try {
        return JSON.parse(text);
    } catch {
        throw new Error('Invalid JSON from server');
    }
})

    .then(data => {

        if (!data.success) {
            alert(data.message || 'Order failed');
            return;
        }

        const total = cart.reduce(
            (sum, item) => sum + (item.price * item.quantity),
            0
        );

        alert(
            `Sacred transaction complete!\n\n` +
            `Total: ${total} Gold Coins\n\n` +
            `Your relics will arrive in 3–5 mystical days.`
        );

        cart = [];
        updateCartUI();
        $('#cartModal').modal('hide');
    })
    .catch(err => {
        console.error('CHECKOUT ERROR:', err);
        alert('Checkout failed. Please check console logs.');
    });

});


// ======================
// LOGIN FORM
// // ======================
// document.getElementById('loginForm')?.addEventListener('submit', function (e) {
//     e.preventDefault();

    const formData = new FormData(this);

    fetch('login.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Gateway unlocked! Welcome, keeper of stones.');
            this.reset();
            $('#loginModal').modal('hide');
            location.reload();
        } else {
            alert('Login failed: ' + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('An error occurred.');
    });

// ======================
// REGISTER FORM (PASSWORD CHECK ONLY)
// ======================
document.getElementById('registerForm')?.addEventListener('submit', function (e) {
    const password = document.getElementById('regPassword').value;
    const confirm  = document.getElementById('regConfirm').value;

    if (password !== confirm) {
        e.preventDefault();
        alert('Secret runes do not align.');
    }
});

// ======================
// SELL STONE (PROPOSAL ONLY — NO AUTO ADD)
// ======================
// ======================
// SELL STONE (PROPOSAL ONLY — NO AUTO ADD)
// ======================
document.getElementById('sellStoneForm')?.addEventListener('submit', function (e) {
    e.preventDefault();

    if (!IS_LOGGED_IN) {
        $('#loginModal').modal({ backdrop: 'static', keyboard: false });
        return;
    }

    const formData = new FormData(this);

    fetch('submit_proposal.php', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(async res => {
        const text = await res.text();
        console.log('RAW PROPOSAL RESPONSE:', text);

        if (text.trim().startsWith('<')) {
            throw new Error('HTML response received instead of JSON');
        }

        return JSON.parse(text);
    })
    .then(data => {
        if (data.success) {
            alert(
                'Your proposal has been dispatched to the Guild Masters for review.\n' +
                'You will be notified once it is approved.'
            );

            this.reset();

            const fileLabel = document.querySelector('#sellStoneForm .custom-file-label');
            if (fileLabel) {
                fileLabel.innerHTML =
                    '<i class="fas fa-cloud-upload-alt mr-2"></i>Upload image of your treasure';
            }
        } else {
            alert('Failed to submit proposal: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(err => {
        console.error('Submission error:', err);
        alert('An error occurred while submitting your proposal. Please check console logs.');
    });
});

// ======================
// FILE INPUT LABEL UPDATE
// ======================
document.getElementById('stoneImages')?.addEventListener('change', function () {
    const label = this.nextElementSibling;
    if (this.files.length > 0) {
        label.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + this.files[0].name;
    } else {
        label.innerHTML = '<i class="fas fa-cloud-upload-alt mr-2"></i>Upload image of your treasure';
    }
});
// ======================
// SMOOTH SCROLL
// ======================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#' && !href.includes('Modal')) {
            e.preventDefault();
            document.querySelector(href)?.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// ======================
// MODAL TOGGLE (LOGIN ↔ REGISTER)
// ======================
document.addEventListener('DOMContentLoaded', function () {
    const loginModal    = $('#loginModal');
    const registerModal = $('#registerModal');

    document.getElementById('showRegisterModal')?.addEventListener('click', e => {
        e.preventDefault();
        loginModal.modal('hide');
        registerModal.modal('show');
    });

    document.getElementById('showLoginModal')?.addEventListener('click', e => {
        e.preventDefault();
        registerModal.modal('hide');
        loginModal.modal('show');
    });
});
</script>
<?php if ($forceLogin): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Force login modal to stay
    $('#loginModal').modal({
        backdrop: 'static',
        keyboard: false
    });

    // Hide the close button
    $('#loginModal .close').hide();

    // Disable all page interactions until login
    document.body.style.pointerEvents = 'none';
    $('#loginModal').on('shown.bs.modal', function () {
        document.body.style.pointerEvents = 'auto';
    });
});
</script>
<?php endif; ?>


</body>
</html>

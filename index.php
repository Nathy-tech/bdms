<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donation System</title>

    <style>
      /* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    background: linear-gradient(to right, #e0f7fa, #b2ebf2); /* Light blue gradient background */
    color: #333;
}

a {
    text-decoration: none;
}

/* Navigation Bar */
.navbar {
    position: sticky;
    top: 0;
    background-color: #00acc1; /* Vibrant cyan */
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    z-index: 1000;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.navbar h1 {
    font-size: 1.8em;
    margin: 0;
}

.navbar .nav-links {
    display: flex;
    gap: 15px;
}

.navbar .nav-links a {
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 1.1em;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.navbar .nav-links a:hover {
    background-color: #0097a7; /* Darker cyan */
    transform: scale(1.05);
}

/* Hero Section */
.hero {
    position: relative;
    background: url('images/blood-donation-bg.jpg') no-repeat center center/cover;
    height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: black;
    overflow: hidden;
    padding: 20px;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(173, 216, 230); /* Darker overlay for better text readability */
    z-index: 1;
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 800px;
}

.hero h1 {
    font-size: 4em;
    margin-bottom: 20px;
    letter-spacing: 2px;
    animation: fadeInUp 1s ease-out;
}

.hero p {
    font-size: 1.5em;
    margin-bottom: 30px;
    animation: fadeIn 2s ease;
}

.hero .stats {
    display: flex;
    justify-content: space-around;
    margin-top: 20px;
    flex-wrap: wrap;
}

.hero .stats div {
    background: rgba(255, 255, 255); /* Semi-transparent white */
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.2);
    flex: 1;
    margin: 10px;
    min-width: 180px;
}

.hero .stats h3 {
    margin-bottom: 10px;
}

.hero .stats p {
    font-size: 1.1em;
}

.hero .btn-group {
    margin-top: 30px;
}

.hero .btn {
    background-color: #add8e6; /* Light blue */
    color: black;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 1.2em;
    margin: 5px;
    text-decoration: none;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.hero .btn-alt {
    background-color: #fff;
    color: #add8e6; /* Light blue for alternative button */
    border: 2px solid #add8e6;
}

.hero .btn:hover, .hero .btn-alt:hover {
    background-color: #87ceeb; /* Slightly darker light blue on hover */
    color: #fff;
    transform: scale(1.05);
}

/* Keyframe Animations */
@keyframes fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Section Styling */
.info-section, .heroes-section, .facts-section {
    padding: 40px 20px;
    text-align: center;
    background-color: #ffffff; /* White background for sections */
    margin-bottom: 20px;
    border-radius: 10px;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
}

.info-section {
    background: linear-gradient(to right, #b2ebf2, #e0f7fa); /* Light blue gradient */
}

.info-cards, .heroes-cards {
    display: flex;
    justify-content: space-around;
    gap: 20px;
    flex-wrap: wrap;
}

.info-card, .hero-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
    width: 250px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.info-card img, .hero-card img {
    width: 100%; /* Make the images responsive */
    height: auto;
    border-radius: 10px;
    object-fit: cover;
}

.hero-card img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #00acc1; /* Vibrant cyan border for heroes */
}

.info-card:hover, .hero-card:hover {
    transform: translateY(-10px);
    box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
}

/* Footer */
footer {
    background-color: #00acc1; /* Vibrant cyan */
    color: white;
    text-align: center;
    padding: 10px 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .info-cards, .heroes-cards {
        flex-direction: column;
        align-items: center;
    }

    .hero h1 {
        font-size: 2em;
    }

    .hero p {
        font-size: 1.2em;
    }

    .hero {
        height: 80vh;
    }

    .info-card, .hero-card {
        width: 100%;
    }

    .hero .stats {
        flex-direction: column;
        align-items: center;
    }
}

/* Google Translate Widget */
.google-translate {
    position: absolute;
    top: 20px;
    right: 20px;
    z-index: 1000;
}

    </style>
     <!-- Google Translate Widget Script -->
     <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'am,tig,en',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</head>
<body>
    

    <!-- Navigation Bar -->
    <nav class="navbar">
        <h1>Blood Donation System</h1>
        <div class="nav-links">
            <a href="pages/login.php" class="btn">Login</a>
            <a href="pages/donor/register.php" class="btn">Register as Donor</a>
        </div>
    </nav>
     <!-- Google Translate Widget -->
     <div id="google_translate_element" class="google-translate"></div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Donate Blood, Save Lives</h1>
            <p>Join our blood donation system and make a difference today.</p>
            <div class="stats">
                <div>
                    <h3>Every 2 Seconds</h3>
                    <p>Someone in the world needs blood.</p>
                </div>
                <div>
                    <h3>1 Donation</h3>
                    <p>Can save up to 3 lives.</p>
                </div>
                <div>
                    <h3>Get Involved</h3>
                    <p>Be a hero. Donate today.</p>
                </div>
            </div>
            <div class="btn-group">
                <a href="pages/donor/register.php" class="btn">Donate Now</a>
            </div>
        </div>
    </section>

    <!-- Information Section -->
    <section class="info-section">
        <h2>Why Donate Blood?</h2>
        <div class="info-cards">
            <div class="info-card">
                <img src="pictures/blood-test-5601437_1280.jpg" alt="Benefit 1">
                <h3>Save Lives</h3>
                <p>Each donation can save multiple lives, making you a hero to those in need.</p>
            </div>
            <div class="info-card">
                <img src="pictures/E8AzAe7WYAExZ0I.jpg" alt="Benefit 2">
                <h3>Health Benefits</h3>
                <p>Donating blood can help improve your health and reduce the risk of certain diseases.</p>
            </div>
            <div class="info-card">
                <img src="pictures/earth-4861456_1280.jpg" alt="Benefit 3">
                <h3>Community Support</h3>
                <p>Your donation helps ensure a stable blood supply for emergencies and routine needs.</p>
            </div>
        </div>
    </section>

    <!-- Heroes Section -->
    <section class="heroes-section">
        <h2>Meet Our Heroes</h2>
        <div class="heroes-cards">
            <div class="hero-card">
                <img src="pictures/marc_satalof_02.png" alt="Hero 1">
                <h3>Mark Satalof</h3>
                <p>Regular donor who has saved numerous lives.</p>
            </div>
            <div class="hero-card">
                <img src="pictures/14xp-blooddonor1-superJumbo.jpg" alt="Hero 2">
                <h3>James Harrison</h3>
                <p>Dedicated to helping others through frequent donations.</p>
            </div>
            <div class="hero-card">
                <img src="pictures/images.jpg" alt="Hero 3">
                <h3>Fresenius Kabi</h3>
                <p>Committed to making a difference in her community.</p>
            </div>
        </div>
    </section>

    <!-- Facts Section -->
    <section class="facts-section">
        <h2>Interesting Facts</h2>
        <ul class="facts-list">
            <li>Blood is made up of plasma, red cells, white cells, and platelets.</li>
            <li>Donors can give blood every 56 days.</li>
            <li>Blood donations are crucial in emergencies and surgeries.</li>
            <li>One donation can help up to three people in need.</li>
        </ul>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Blood Donation System. All rights reserved.</p>
    </footer>

</body>
</html>

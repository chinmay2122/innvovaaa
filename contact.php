<?php
require_once 'config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $subject = sanitizeInput($_POST['subject']);
    $message = sanitizeInput($_POST['message']);
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO contact_queries (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
        
        if ($stmt->execute()) {
            $success = 'Your message has been sent successfully! We will get back to you soon.';
            // Clear form
            $_POST = array();
        } else {
            $error = 'Failed to send message. Please try again.';
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | InnovateX</title>
    <link rel="icon" type="image/svg+xml" href="public/favicon.svg" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        zentry: ["zentry", "sans-serif"],
                        general: ["general", "sans-serif"],
                        "robert-medium": ["robert-medium", "sans-serif"],
                        "robert-regular": ["robert-regular", "sans-serif"],
                    },
                    colors: {
                        blue: {
                            50: "#dfdff0",
                            75: "#dfdff2",
                            100: "#f0f2fa",
                            200: "#101010",
                            300: "#4fb7dd",
                        },
                        violet: {
                            300: "#5724ff",
                        },
                        yellow: {
                            100: "#8e983f",
                            300: "#edff66",
                        },
                    },
                },
            },
        }
    </script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-blue-50 text-blue-50 overflow-x-hidden">
    <!-- Navigation -->
    <header id="nav-container" class="fixed inset-x-0 top-4 z-50 h-16 border-none transition-all duration-700 sm:inset-x-6">
        <div class="absolute top-1/2 w-full -translate-y-1/2">
            <nav class="flex size-full items-center justify-between p-4">
                <div class="flex items-center gap-7">
                    <a href="index.php" class="hover:opacity-75 transition-opacity">
                        <img src="public/img/Inno.png" alt="InnovateX Logo" class="h-16 w-16 object-contain" />
                    </a>
                </div>
                <div class="flex h-full items-center">
                    <div class="hidden md:block">
                        <a href="index.php" class="nav-hover-btn">Home</a>
                        <a href="events.php" class="nav-hover-btn">Events</a>
                        <a href="about.php" class="nav-hover-btn">About</a>
                        <a href="contact.php" class="nav-hover-btn">Contact</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative h-screen w-full overflow-hidden">
        <div class="absolute inset-0">
            <img src="public/img/about.png" alt="Contact Us" class="w-full h-full object-cover brightness-50" />
        </div>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center z-10">
                <h1 class="font-zentry text-7xl md:text-9xl text-white mb-4 animate-fade-in">
                    CONTACT US
                </h1>
                <p class="font-robert-regular text-xl md:text-2xl text-white/80 max-w-2xl mx-auto px-4">
                    Get in touch with us for any queries or support
                </p>
            </div>
        </div>
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </section>

    <!-- Contact Info & Form Section -->
    <section class="bg-white py-20 px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Left - Contact Information -->
                <div>
                    <h2 class="font-zentry text-5xl text-blue-200 mb-8">Get In Touch</h2>
                    <p class="font-robert-regular text-lg text-blue-200/70 mb-8">
                        Have questions about InnovateX? We're here to help! Reach out to us through any of the following channels.
                    </p>

                    <!-- Contact Details -->
                    <div class="space-y-6 mb-10">
                        <div class="flex items-start gap-4">
                            <div class="bg-blue-300 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-robert-medium text-blue-200 mb-1">Email</h3>
                                <a href="mailto:contact@innovatex.com" class="font-robert-regular text-blue-300 hover:underline">contact@innovatex.com</a>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="bg-blue-300 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-robert-medium text-blue-200 mb-1">Phone</h3>
                                <a href="tel:+911234567890" class="font-robert-regular text-blue-300 hover:underline">+91 1234567890</a>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="bg-blue-300 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-robert-medium text-blue-200 mb-1">Location</h3>
                                <p class="font-robert-regular text-blue-200/70">Presidency University,<br>Yelahanka,Banglore, India</p>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div>
                        <h3 class="font-robert-medium text-blue-200 mb-4">Follow Us</h3>
                        <div class="flex gap-4">
                            <a href="#" class="bg-blue-50 p-3 rounded-lg text-blue-200 hover:bg-blue-300 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                                </svg>
                            </a>
                            <a href="#" class="bg-blue-50 p-3 rounded-lg text-blue-200 hover:bg-blue-300 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right - Contact Form -->
                <div class="bg-blue-50 p-8 rounded-2xl shadow-xl">
                    <h3 class="font-zentry text-3xl text-blue-200 mb-6">Send us a Message</h3>

                    <?php if ($error): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 font-robert-regular">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 font-robert-regular">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="space-y-6">
                        <div>
                            <label for="name" class="block text-blue-200 font-robert-medium mb-2">Name *</label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                required
                                value="<?php echo isset($_POST['name']) && !$success ? htmlspecialchars($_POST['name']) : ''; ?>"
                                class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular bg-white"
                                placeholder="Your full name"
                            >
                        </div>

                        <div>
                            <label for="email" class="block text-blue-200 font-robert-medium mb-2">Email *</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                required
                                value="<?php echo isset($_POST['email']) && !$success ? htmlspecialchars($_POST['email']) : ''; ?>"
                                class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular bg-white"
                                placeholder="your.email@example.com"
                            >
                        </div>

                        <div>
                            <label for="phone" class="block text-blue-200 font-robert-medium mb-2">Phone</label>
                            <input 
                                type="tel" 
                                id="phone" 
                                name="phone"
                                value="<?php echo isset($_POST['phone']) && !$success ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular bg-white"
                                placeholder="+91 1234567890"
                            >
                        </div>

                        <div>
                            <label for="subject" class="block text-blue-200 font-robert-medium mb-2">Subject *</label>
                            <input 
                                type="text" 
                                id="subject" 
                                name="subject" 
                                required
                                value="<?php echo isset($_POST['subject']) && !$success ? htmlspecialchars($_POST['subject']) : ''; ?>"
                                class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular bg-white"
                                placeholder="What is this about?"
                            >
                        </div>

                        <div>
                            <label for="message" class="block text-blue-200 font-robert-medium mb-2">Message *</label>
                            <textarea 
                                id="message" 
                                name="message" 
                                required
                                rows="5"
                                class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular bg-white"
                                placeholder="Your message..."
                            ><?php echo isset($_POST['message']) && !$success ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        </div>

                        <button 
                            type="submit"
                            class="w-full bg-blue-200 text-white font-robert-medium py-3 rounded-lg hover:bg-blue-200/90 transition-all duration-300 transform hover:scale-105"
                        >
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="bg-blue-50 py-20 px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="text-center mb-12">
                <h2 class="font-zentry text-5xl text-blue-200 mb-4">Find Us</h2>
                <p class="font-robert-regular text-lg text-blue-200/70">Visit us at our campus location</p>
            </div>
            
            <!-- Embedded Google Map -->
            <div class="rounded-2xl overflow-hidden shadow-2xl h-96">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3502.2564037866956!2d77.36719931508079!3d28.629008882422937!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390ce5456ef36d9f%3A0x3b7191b1286136c8!2sDelhi%20Technological%20University!5e0!3m2!1sen!2sin!4v1635759820456!5m2!1sen!2sin" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-200 text-white py-12">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- About -->
                <div>
                    <h3 class="font-zentry text-2xl mb-4">InnovateX</h3>
                    <p class="font-robert-regular text-sm text-white/80">
                        The ultimate technical festival bringing innovation and excellence together.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-robert-medium text-lg mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="font-robert-regular text-sm text-white/80 hover:text-yellow-300 transition-colors">Home</a></li>
                        <li><a href="events.php" class="font-robert-regular text-sm text-white/80 hover:text-yellow-300 transition-colors">Events</a></li>
                        <li><a href="about.php" class="font-robert-regular text-sm text-white/80 hover:text-yellow-300 transition-colors">About</a></li>
                        <li><a href="contact.php" class="font-robert-regular text-sm text-white/80 hover:text-yellow-300 transition-colors">Contact</a></li>
                    </ul>
                </div>

                <!-- Events -->
                <div>
                    <h4 class="font-robert-medium text-lg mb-4">Categories</h4>
                    <ul class="space-y-2">
                        <li><a href="events.php" class="font-robert-regular text-sm text-white/80 hover:text-yellow-300 transition-colors">Technical</a></li>
                        <li><a href="events.php" class="font-robert-regular text-sm text-white/80 hover:text-yellow-300 transition-colors">Non-Technical</a></li>
                        <li><a href="events.php" class="font-robert-regular text-sm text-white/80 hover:text-yellow-300 transition-colors">Workshops</a></li>
                        <li><a href="events.php" class="font-robert-regular text-sm text-white/80 hover:text-yellow-300 transition-colors">Competitions</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="font-robert-medium text-lg mb-4">Get In Touch</h4>
                    <ul class="space-y-2">
                        <li class="font-robert-regular text-sm text-white/80">contact@innovatex.com</li>
                        <li class="font-robert-regular text-sm text-white/80">+91 1234567890</li>
                        <li><a href="admin_login.php" class="font-robert-regular text-sm text-yellow-300 hover:text-yellow-200 transition-colors">Admin Portal</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-white/20 pt-8 text-center">
                <p class="font-robert-regular text-sm text-white/80">
                    &copy; 2026 InnovateX. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Navbar background on scroll
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('nav-container');
            if (window.scrollY > 100) {
                nav.classList.add('floating-nav');
            } else {
                nav.classList.remove('floating-nav');
            }
        });
    </script>
</body>
</html>

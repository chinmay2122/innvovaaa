<?php
require_once 'config.php';

// Get all events
$conn = getDBConnection();
$events = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events | InnovateX</title>
    <link rel="icon" type="image/svg+xml" href="public/favicon.svg" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        zentry: ["zentry", "sans-serif"],
                        general: ["general", "sans-serif"],
                        "circular-web": ["circular-web", "sans-serif"],
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
    <style>
        .event-card {
            transition: all 0.3s ease;
        }
        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
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
                        <a href="index.php#about" class="nav-hover-btn">About</a>
                        <a href="index.php#contact" class="nav-hover-btn">Contact</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative h-screen w-full overflow-hidden">
        <div class="absolute inset-0">
            <img src="public/img/about.webp" alt="Events" class="w-full h-full object-cover brightness-50" />
        </div>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center z-10">
                <h1 class="font-zentry text-7xl md:text-9xl text-white mb-4 animate-fade-in">
                    EVENTS
                </h1>
                <p class="font-robert-regular text-xl md:text-2xl text-white/80 max-w-2xl mx-auto px-4">
                    Discover exciting competitions and challenges at InnovateX
                </p>
            </div>
        </div>
        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </section>

    <!-- Events Grid Section -->
    <section class="bg-white py-20 px-4">
        <div class="container mx-auto max-w-7xl">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <h2 class="font-zentry text-5xl md:text-6xl text-blue-200 mb-4">
                    All Events
                </h2>
                <p class="font-robert-regular text-lg text-blue-200/70 max-w-2xl mx-auto">
                    Explore our diverse range of technical and non-technical events designed to challenge and inspire
                </p>
            </div>

            <!-- Events Grid -->
            <?php if ($events->num_rows > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php while ($event = $events->fetch_assoc()): ?>
                        <div class="event-card bg-blue-50 rounded-2xl overflow-hidden shadow-lg border-2 border-blue-200/10">
                            <!-- Cover Image -->
                            <div class="relative h-64 overflow-hidden">
                                <img 
                                    src="<?php echo htmlspecialchars($event['cover_image']); ?>" 
                                    alt="<?php echo htmlspecialchars($event['title']); ?>"
                                    class="w-full h-full object-cover"
                                />
                                <?php if (!empty($event['prize_money'])): ?>
                                    <div class="absolute top-4 right-4 bg-yellow-300 text-blue-200 font-robert-medium px-4 py-2 rounded-full text-sm">
                                        <?php echo htmlspecialchars($event['prize_money']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Card Content -->
                            <div class="p-6">
                                <!-- Title -->
                                <h3 class="font-zentry text-2xl text-blue-200 mb-3">
                                    <?php echo htmlspecialchars($event['title']); ?>
                                </h3>

                                <!-- Description -->
                                <p class="font-robert-regular text-blue-200/70 mb-4 line-clamp-3">
                                    <?php echo htmlspecialchars($event['description']); ?>
                                </p>

                                <!-- Event Details -->
                                <div class="space-y-2 mb-6">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-robert-regular text-sm text-blue-200/70">
                                            <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span class="font-robert-regular text-sm text-blue-200/70">
                                            <?php echo htmlspecialchars($event['event_head_name']); ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-3">
                                    <?php if (!empty($event['registration_link']) && $event['registration_link'] !== '#'): ?>
                                        <a 
                                            href="<?php echo htmlspecialchars($event['registration_link']); ?>" 
                                            target="_blank"
                                            class="flex-1 bg-blue-200 text-white font-robert-medium py-3 rounded-lg hover:bg-blue-200/90 transition-all duration-300 text-center transform hover:scale-105"
                                        >
                                            Register
                                        </a>
                                    <?php else: ?>
                                        <a 
                                            href="register.php?event_id=<?php echo $event['id']; ?>"
                                            class="flex-1 bg-blue-200 text-white font-robert-medium py-3 rounded-lg hover:bg-blue-200/90 transition-all duration-300 text-center transform hover:scale-105"
                                        >
                                            Register
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($event['rule_book'])): ?>
                                        <a 
                                            href="<?php echo htmlspecialchars($event['rule_book']); ?>" 
                                            download
                                            class="flex-1 bg-blue-300 text-white font-robert-medium py-3 rounded-lg hover:bg-blue-300/90 transition-all duration-300 text-center transform hover:scale-105"
                                        >
                                            Rules
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <!-- No Events -->
                <div class="text-center py-20">
                    <svg class="w-24 h-24 mx-auto text-blue-200/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="font-zentry text-3xl text-blue-200 mb-2">No Events Yet</h3>
                    <p class="font-robert-regular text-blue-200/70">Check back soon for exciting events!</p>
                </div>
            <?php endif; ?>
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
                        <li><a href="index.php#contact" class="font-robert-regular text-sm text-white/80 hover:text-yellow-300 transition-colors">Contact</a></li>
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

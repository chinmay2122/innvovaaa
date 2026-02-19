<?php
require_once 'config.php';

// Get all team members grouped by category
$conn = getDBConnection();
$teamCategories = ['Faculty', 'Organisers', 'Event Heads', 'Design Team', 'Website Team', 'Decor Team', 'Volunteers'];
$teamMembers = [];

foreach ($teamCategories as $category) {
    $stmt = $conn->prepare("SELECT * FROM team_members WHERE team_category = ? ORDER BY display_order ASC, name ASC");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    $teamMembers[$category] = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Get blog posts (latest 3)
$blogs = $conn->query("SELECT * FROM blogs ORDER BY publish_date DESC LIMIT 3");

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | InnovateX</title>
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
        .team-card {
            transition: all 0.3s ease;
        }
        .team-card:hover {
            transform: translateY(-10px);
        }
        .blog-card {
            transition: all 0.3s ease;
        }
        .blog-card:hover {
            transform: translateY(-5px);
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
                        <a href="about.php" class="nav-hover-btn">About</a>
                        <a href="index.php#contact" class="nav-hover-btn">Contact</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative h-screen w-full overflow-hidden">
        <div class="absolute inset-0">
            <img src="public/img/about.png" alt="About Us" class="w-full h-full object-cover brightness-50" />
        </div>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center z-10">
                <h1 class="font-zentry text-7xl md:text-9xl text-white mb-4 animate-fade-in">
                    ABOUT US
                </h1>
                <p class="font-robert-regular text-xl md:text-2xl text-white/80 max-w-2xl mx-auto px-4">
                    Discover the vision and team behind InnovateX
                </p>
            </div>
        </div>
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </section>

    <!-- About Club Section -->
    <section class="bg-white py-20 px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div>
                    <h2 class="font-zentry text-5xl md:text-6xl text-blue-200 mb-6">
                        Our Story
                    </h2>
                    <div class="space-y-4 font-robert-regular text-lg text-blue-200/80 leading-relaxed">
                        <p>
                            InnovateX is a premier college technical festival that brings together brilliant minds, cutting-edge technology, and innovative ideas. Founded with the vision of fostering creativity and technical excellence, we have been inspiring students since our inception.
                        </p>
                        <p>
                            Our festival features a diverse range of events including coding competitions, hackathons, robotics challenges, technical workshops, and much more. We provide a platform for students to showcase their talents, learn from industry experts, and connect with like-minded peers.
                        </p>
                        <p>
                            With over 50+ events and participation from colleges across the country, InnovateX has become one of the most anticipated technical festivals. Join us in this journey of innovation, learning, and excellence.
                        </p>
                    </div>
                    <div class="mt-8 grid grid-cols-3 gap-6">
                        <div class="text-center">
                            <p class="font-zentry text-4xl text-blue-300 mb-2">50+</p>
                            <p class="font-robert-regular text-sm text-blue-200/70">Events</p>
                        </div>
                        <div class="text-center">
                            <p class="font-zentry text-4xl text-blue-300 mb-2">5000+</p>
                            <p class="font-robert-regular text-sm text-blue-200/70">Participants</p>
                        </div>
                        <div class="text-center">
                            <p class="font-zentry text-4xl text-blue-300 mb-2">100+</p>
                            <p class="font-robert-regular text-sm text-blue-200/70">Colleges</p>
                        </div>
                    </div>
                </div>

                <!-- Right Image -->
                <div class="relative">
                    <img 
                        src="public/img/buildclub.png" 
                        alt="InnovateX Club" 
                        class="rounded-2xl shadow-2xl w-full"
                    />
                    <div class="absolute -bottom-6 -right-6 w-48 h-48 bg-yellow-300 rounded-2xl -z-10"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="bg-blue-50 py-20 px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="text-center mb-16">
                <h2 class="font-zentry text-5xl md:text-6xl text-blue-200 mb-4">
                    Our Team
                </h2>
                <p class="font-robert-regular text-lg text-blue-200/70 max-w-2xl mx-auto">
                    Meet the passionate individuals who make InnovateX possible
                </p>
            </div>

            <!-- Team Category Tabs -->
            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <?php foreach ($teamCategories as $index => $category): ?>
                    <?php if (!empty($teamMembers[$category])): ?>
                        <button 
                            onclick="showTeamCategory('<?php echo str_replace(' ', '_', $category); ?>')"
                            class="team-tab font-robert-medium px-6 py-3 rounded-lg transition-all duration-300 <?php echo $index === 0 ? 'bg-blue-200 text-white' : 'bg-white text-blue-200 hover:bg-blue-200/10'; ?>"
                            data-category="<?php echo str_replace(' ', '_', $category); ?>"
                        >
                            <?php echo $category; ?>
                        </button>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <!-- Team Members by Category -->
            <?php foreach ($teamCategories as $index => $category): ?>
                <?php if (!empty($teamMembers[$category])): ?>
                    <div 
                        id="team-<?php echo str_replace(' ', '_', $category); ?>" 
                        class="team-content <?php echo $index === 0 ? '' : 'hidden'; ?>"
                    >
                        <div class="text-center mb-10">
                            <div class="inline-block">
                                <h3 class="font-zentry text-3xl md:text-4xl text-blue-200 mb-2"><?php echo $category; ?></h3>
                                <div class="h-1 bg-blue-300 rounded"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
                            <?php foreach ($teamMembers[$category] as $member): ?>
                                <div class="team-card bg-white rounded-xl overflow-hidden shadow-lg">
                                    <div class="p-6">
                                        <img 
                                            src="<?php echo htmlspecialchars($member['photo']); ?>" 
                                            alt="<?php echo htmlspecialchars($member['name']); ?>"
                                            class="w-32 h-32 rounded-full mx-auto mb-4 object-cover border-4 border-blue-50"
                                        />
                                        <h4 class="font-robert-medium text-xl text-blue-200 text-center mb-1">
                                            <?php echo htmlspecialchars($member['name']); ?>
                                        </h4>
                                        <p class="font-robert-regular text-sm text-blue-200/70 text-center">
                                            <?php echo htmlspecialchars($member['designation']); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="bg-white py-20 px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="text-center mb-16">
                <h2 class="font-zentry text-5xl md:text-6xl text-blue-200 mb-4">
                    Latest Updates
                </h2>
                <p class="font-robert-regular text-lg text-blue-200/70 max-w-2xl mx-auto">
                    Stay informed with our latest news and announcements
                </p>
            </div>

            <?php if ($blogs->num_rows > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php while ($blog = $blogs->fetch_assoc()): ?>
                        <div class="blog-card bg-blue-50 rounded-2xl overflow-hidden shadow-lg">
                            <img 
                                src="<?php echo htmlspecialchars($blog['cover_image']); ?>" 
                                alt="<?php echo htmlspecialchars($blog['title']); ?>"
                                class="w-full h-56 object-cover"
                            />
                            <div class="p-6">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="font-robert-regular text-xs text-blue-200/60">
                                        <?php echo date('F j, Y', strtotime($blog['publish_date'])); ?>
                                    </span>
                                    <span class="text-blue-200/40">•</span>
                                    <span class="font-robert-regular text-xs text-blue-300">
                                        <?php echo htmlspecialchars($blog['author']); ?>
                                    </span>
                                </div>
                                <h3 class="font-zentry text-2xl text-blue-200 mb-3">
                                    <?php echo htmlspecialchars($blog['title']); ?>
                                </h3>
                                <p class="font-robert-regular text-blue-200/70 mb-4">
                                    <?php echo htmlspecialchars($blog['excerpt']); ?>
                                </p>
                                <a href="#" class="font-robert-medium text-blue-300 hover:text-blue-200 transition-colors">
                                    Read More →
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <p class="font-robert-regular text-blue-200/50">No updates yet. Check back soon!</p>
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

        // Team category switching
        function showTeamCategory(category) {
            // Hide all team content sections
            const allContent = document.querySelectorAll('.team-content');
            allContent.forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active state from all tabs
            const allTabs = document.querySelectorAll('.team-tab');
            allTabs.forEach(tab => {
                tab.classList.remove('bg-blue-200', 'text-white');
                tab.classList.add('bg-white', 'text-blue-200');
            });

            // Show selected team content
            const selectedContent = document.getElementById('team-' + category);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
            }

            // Activate selected tab
            const selectedTab = document.querySelector(`[data-category="${category}"]`);
            if (selectedTab) {
                selectedTab.classList.remove('bg-white', 'text-blue-200');
                selectedTab.classList.add('bg-blue-200', 'text-white');
            }
        }
    </script>
</body>
</html>

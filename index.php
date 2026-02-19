<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnovateX | Official Website</title>
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
</head>

<body class="bg-blue-50 text-blue-50 overflow-x-hidden min-h-screen w-screen">
    <div id="root" class="relative min-h-screen w-screen overflow-x-hidden">
        <header id="nav-container"
            class="fixed inset-x-0 top-4 z-50 h-16 border-none transition-all duration-700 sm:inset-x-6">
            <div class="absolute top-1/2 w-full -translate-y-1/2">
                <nav class="flex size-full items-center justify-between p-4">
                    <div class="flex items-center gap-7">
                        <a href="index.php" class="transition hover:opacity-75">
                            <img src="public/img/Inno.png" alt="Logo" class="w-20" />
                        </a>
                        <button id="product-button"
                            class="group relative z-10 w-fit cursor-pointer overflow-hidden rounded-full bg-violet-50 px-7 py-3 text-black transition hover:opacity-75 bg-blue-50 md:flex hidden items-center justify-center gap-1">
                            <div class="relative inline-flex overflow-hidden font-general text-xs uppercase">Brochure
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-4">
                                <path
                                    d="M21.426 11.095l-17-8c-.35-.164-.763-.072-1.018.223-.255.295-.295.716-.1.972l6.217 8.184-6.217 8.185c-.195.256-.155.677.1.972.16.185.396.283.636.283.13 0 .26-.03.382-.087l17-8c.29-.136.474-.43.474-.75s-.184-.614-.474-.75z" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex h-full items-center">
                        <div class="hidden md:block">
                            <a href="events.php" class="nav-hover-btn">Events</a>
                            <a href="about.php" class="nav-hover-btn">Team</a>
                            <a href="about.php" class="nav-hover-btn">About</a>
                            <a href="contact.php" class="nav-hover-btn">Contact</a>
                        </div>
                        <div class="flex items-center gap-4">
                            <button id="audio-btn"
                                class="ml-10 flex items-center space-x-0.5 p-2 transition hover:opacity-75"
                                title="Play Audio">
                                <audio id="audio-element" src="public/audio/loop.mp3" class="hidden" loop></audio>
                                <div class="indicator-line" style="--animation-order: 1"></div>
                                <div class="indicator-line" style="--animation-order: 2"></div>
                                <div class="indicator-line" style="--animation-order: 3"></div>
                                <div class="indicator-line" style="--animation-order: 4"></div>
                            </button>
                        </div>
                    </div>
                </nav>
            </div>
        </header>
        <main>
            <section id="hero" class="relative h-dvh w-screen overflow-x-hidden">
                <div id="loading-screen"
                    class="flex-center absolute z-[100] h-dvh w-screen overflow-hidden bg-violet-50">
                    <div class="three-body">
                        <div class="three-body__dot"></div>
                        <div class="three-body__dot"></div>
                        <div class="three-body__dot"></div>
                    </div>
                </div>
                <div id="video-frame" class="relative z-10 h-dvh w-screen overflow-hidden rounded-lg bg-blue-75">
                    <div>
                        <div id="mini-video-wrapper"
                            class="mask-clip-path absolute-center absolute z-50 size-64 cursor-pointer overflow-hidden rounded-lg">
                            <div id="mini-video-container"
                                class="origin-center scale-50 opacity-0 transition-all duration-500 ease-in hover:scale-100 hover:opacity-100">
                                <video id="current-video" src="public/videos/hero-1.mp4" loop muted preload="metadata" poster="public/img/about.webp"
                                    class="size-64 origin-center scale-150 object-cover object-center"></video>
                            </div>
                        </div>
                        <video id="next-video" src="public/videos/hero-1.mp4" loop muted preload="none"
                            class="absolute-center invisible absolute z-20 size-64 object-cover object-center"></video>
                        <video id="bg-video" src="public/videos/webm.webm" autoplay loop muted preload="auto" poster="public/img/about.webp" playsinline
                            class="absolute left-0 top-0 size-full object-cover object-center"></video>
                    </div>
                </div>
                <h1 class="special-font hero-heading absolute bottom-5 right-5 z-40 text-blue-75">4.0</h1>
                <div class="absolute left-0 top-0 z-40 size-full">
                    <div class="mt-24 px-5 sm:px-10">
                        <h1 class="special-font hero-heading text-blue-100">Inno<b>vateX</b></h1>
                        <p class="mb-5 max-w-64 font-robert-regular text-blue-100">Our Flagship National Tech
                            Fest<br />at Presidency University, Banglore.</p>
                        <button id="watch-trailer"
                            class="group relative z-10 w-fit cursor-pointer overflow-hidden rounded-full bg-violet-50 px-7 py-3 text-black transition hover:opacity-75 bg-yellow-300 flex-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-4">
                                <path
                                    d="M21.426 11.095l-17-8c-.35-.164-.763-.072-1.018.223-.255.295-.295.716-.1.972l6.217 8.184-6.217 8.185c-.195.256-.155.677.1.972.16.185.396.283.636.283.13 0 .26-.03.382-.087l17-8c.29-.136.474-.43.474-.75s-.184-.614-.474-.75z" />
                            </svg>
                            <div class="relative inline-flex overflow-hidden font-general text-xs uppercase">Register
                                Now</div>
                        </button>
                    </div>
                </div>
                <h1 class="special-font hero-heading absolute bottom-5 right-5 text-black">Innovate<b>X</b> 4.0</h1>
            </section>
            <section id="about" class="min-h-screen w-screen">
                <div class="relative mb-8 mt-36 flex flex-col items-center gap-5">
                    <p class="font-general text-sm uppercase md:text-[10px]">
                        Welcome to Zentry
                    </p>
                    <div class="animated-title mt-5 !text-black text-center">
                        <div class="flex-center max-w-full flex-wrap gap-2 px-10 md:gap-3">
                            <span class="animated-word">The</span>
                            <span class="animated-word">Biggest</span>
                            <span class="animated-word">National Level tech Fest</span>
                        </div>
                        <div class="flex-center max-w-full flex-wrap gap-2 px-10 md:gap-3">
                            <span class="animated-word">Of</span>
                            <span class="animated-word">2026</span>
                        </div>
                    </div>
                    <div class="about-subtext">
                        <p>The Game of Games begins-your life, now an epic MMORPG</p>
                        <p>Zentry unites every player from countless games and platforms</p>
                    </div>
                </div>
                <div class="h-dvh w-screen" id="clip">
                    <div class="mask-clip-path about-image">
                        <img src="public/img/about.webp" alt="Background"
                            class="absolute left-0 top-0 size-full object-cover" loading="lazy" />
                    </div>
                </div>
            </section>
            <section id="features" class="bg-black pb-52">
                <div class="container mx-auto px-3 md:px-10">
                    <div class="px-5 py-32">
                        <p class="font-circular-web text-lg text-blue-50">A Techno-Cultural Phenomena</p>
                        <p class="max-w-md font-circular-web text-lg text-blue-50 opacity-50">this
A Three-day extravaganza designed to nurture the
brightest minds and shape the tech maestros of
tomorrow. Hosted by Build Club.</p>
                    </div>
                    <div
                        class="bento-tilt border-hsla relative mb-7 h-96 w-full overflow-hidden rounded-md md:h-[65vh]">
                        <article class="relative size-full">
                            <video src="https://93w95scdts.ufs.sh/f/AOfILeWJzqCc56aV03LYRyJDZsOPGdFTt0lQuHLkeqjKCao1"
                                loop muted autoplay
                                class="absolute left-0 top-0 size-full object-cover object-center"></video>
                            <div class="relative z-10 flex size-full flex-col justify-between p-5 text-blue-50">
                                <div>
                                    <h1 class="bento-title special-font">Eve<b>nt</b>s</h1>
                                    <p class="tetx-xl mt-3 max-w-64 md:text-base">with over 150+ events we aim to go bigger and beyond this time with InnovateX 3.0 indulging events across genre's and tenure's making it the Biggest Technical Fest.</p>
                                </div>
                            </div>
                        </article>
                    </div>
                    <div id="nexus" class="grid h-[135vh] grid-cols-2 grid-rows-3 gap-7">
                        <div class="bento-tilt bento-tilt_1 row-span-1 md:col-span-1 md:row-span-2">
                            <article class="relative size-full">
                                <video
                                    src="https://93w95scdts.ufs.sh/f/AOfILeWJzqCclcn5JiTo8NUtBfpgkOmXZ2CT3DjMr19Yqlac"
                                    loop muted autoplay
                                    class="absolute left-0 top-0 size-full object-cover object-center"></video>
                                <div class="relative z-10 flex size-full flex-col justify-between p-5 text-blue-50">
                                    <div>
                                        <h1 class="bento-title special-font">Spon<b>Sor</b>S</h1>
                                        <p class="tetx-xl mt-3 max-w-64 md:text-base">For the Organisations and the Teams that believed in us and are willing to take their leap of faith.</p>
                                    </div>
                                </div>
                            </article>
                        </div>
                        <div class="bento-tilt bento-tilt_1 row-span-1 ms-32 md:col-span-1 md:ms-0">
    <article class="relative size-full">
        <img 
            src="public/img/team.png"
            alt="InnovateX Team"
            class="absolute left-0 top-0 size-full object-cover object-center"
            loading="lazy"
        />
        <div class="relative z-10 flex size-full flex-col justify-between p-5 text-blue-50">
            <div>
                <h1 class="bento-title special-font">Our <b>Team</b></h1>
                <p class="tetx-xl mt-3 max-w-64 md:text-base">
                    The core tech force of Presidency University — the go-to crew for 
                    innovation, execution, and everything that powers the spirit of InnovateX.
                </p>
            </div>
        </div>
    </article>
</div>

                        <div class="bento-tilt bento-tilt_1 me-14 md:col-span-1 md:me-0">
    <article class="relative size-full">
        <img 
            src="public/img/buildclub.png" 
            alt="Build Club" 
            class="absolute left-0 top-0 size-full object-cover object-center"
            loading="lazy"
        />

        <div class="relative z-10 flex size-full flex-col justify-between p-5 text-blue-50">
            <div>
                <h1 class="bento-title special-font">Our <b>Club</b></h1>
                <p class="text-xl mt-3 max-w-64 md:text-base">
                    Build Club is Presidency University’s innovation engine where ideas becomes to life
                </p>
            </div>
        </div>
    </article>
</div>

                        <div class="bento-tilt bento-tilt_2">
                            <div class="flex size-full flex-col justify-between p-5" style="background-color: #1733DD;">
                                <h1 class="bento-title special-font max-w-64 text-white">Check<b>Out</b>
                                    Inn<b>ovat</b>ex
                                    3.0</h1>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="m-5 scale-[5] self-end">
                                    <path
                                        d="M21.426 11.095l-17-8c-.35-.164-.763-.072-1.018.223-.255.295-.295.716-.1.972l6.217 8.184-6.217 8.185c-.195.256-.155.677.1.972.16.185.396.283.636.283.13 0 .26-.03.382-.087l17-8c.29-.136.474-.43.474-.75s-.184-.614-.474-.75z" />
                                </svg>
                            </div>
                        </div>
                        <div class="bento-tilt bento-tilt_2">
                            <video src="public/videos/inno-3.0.mp4"
                                loop muted autoplay class="size-full object-cover object-center"></video>
                        </div>
                    </div>
                </div>
            </section>
            <!-- FIXED STORY SECTION -->
            <section id="story" class="min-h-dvh w-screen bg-black text-blue-50">
                <div class="flex size-full flex-col items-center py-10 pb-24">

                    <!-- Top Small Title -->
                    <p class="font-general text-sm uppercase md:text-[10px]">
                        The Realm Behind InnovateX
                    </p>

                    <!-- Main Animated Title -->
                    <div class="relative size-full">
                        <div class="animated-title mt-5 pointer-events-none mix-blend-difference relative z-10 text-center">

                            <!-- LINE 1 -->
                            <div class="flex-center max-w-full flex-wrap gap-2 px-10 md:gap-3">
                                <span class="animated-word">The</span>
                                <span class="animated-word">Flagship</span>
                                <span class="animated-word">Event</span>
                            </div>

                            <!-- LINE 2 -->
                            <div class="flex-center max-w-full flex-wrap gap-2 px-10 md:gap-3">
                                <span class="animated-word">Of</span>
                                <span class="animated-word">Presidency</span>
                            </div>

                        </div>

                        <!-- IMAGE SECTION -->
                        <div class="story-img-container">
                            <div class="story-img-mask">
                                <div class="story-img-content">
                                    <img id="story-img" src="public/img/presidency.png" alt="Entrance"
                                         class="object-contain" loading="lazy" />
                                </div>
                            </div>

                            <!-- Shadow Filter -->
                            <svg class="invisible absolute size-0" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <filter id="flt_tag">
                                        <feGaussianBlur in="SourceGraphic" stdDeviation="8" result="blur" />
                                        <feColorMatrix in="blur" mode="matrix"
                                            values="1 0 0 0 0  
                                                    0 1 0 0 0  
                                                    0 0 1 0 0  
                                                    0 0 0 19 -9" result="flt_tag" />
                                        <feComposite in="SourceGraphic" in2="flt_tag" operator="atop" />
                                    </filter>
                                </defs>
                            </svg>
                        </div>
                    </div>

                    <!-- TEXT + BUTTON SECTION -->
                    <div class="-mt-80 flex w-full justify-center md:-mt-64 md:me-44 md:justify-end">
                        <div class="flex h-full w-fit flex-col items-center md:items-start">

                            <p class="mt-3 max-w-sm text-center font-circular-web text-violet-50 md:text-start">
                                Founded in 2015, Presidency University is built on a culture of innovation and hands-on learning. With industry-ready courses and an interdisciplinary approach, the university blends theory, applied science, and real-world experience to shape future-focused talent.
                            </p>

                            <a href="https://www.presidencyuniversity.in" target="_blank" rel="noreferrer noopener">
                        <button id="realm-button"
                         class="group relative z-10 w-fit cursor-pointer overflow-hidden rounded-full bg-violet-50 px-7 py-3 text-black transition hover:opacity-75 mt-5">
                         <div class="relative inline-flex overflow-hidden font-general text-xs uppercase">
                        Discover Presidency
                        </div>
                        </button>
                        </a>    

                        </div>
                    </div>

                </div>
            </section>

            <section id="contact" class="my-20 min-h-96 w-screen px-10">
                <div class="relative rounded-lg bg-black py-24 text-blue-50 sm:overflow-hidden">
                    <div class="absolute -left-20 top-0 hidden h-full w-72 overflow-hidden sm:block lg:left-20 lg:w-96">
                        <div class="contact-clip-path-1">
                            <img src="public/img/contact-1.webp" alt="Contact bg 1" loading="lazy" />
                        </div>
                        <div class="contact-clip-path-2 lg:translate-y-40 translate-y-60">
                            <img src="public/img/contact-2.webp" alt="Contact bg 2" loading="lazy" />
                        </div>
                    </div>
                    <div class="absolute -top-40 left-20 w-60 sm:top-1/2 md:left-auto md:right-10 lg:top-20 lg:w-80">
                        <div class="absolute md:scale-125">
                            <img src="public/img/swordman-partial.webp" alt="Swordman partial" loading="lazy" />
                        </div>
                        <div class="sword-man-clip-path md:scale-125">
                            <img src="public/img/swordman.webp" alt="Swordman" loading="lazy" />
                        </div>
                    </div>
                    <div class="flex flex-col items-center text-center">
                        <p class="font-general text-[10px] uppercase">Contact Us For Any Query</p>
                        <p class="special-font mt-10 w-full font-zentry text-5xl leading-[0.9] md:text-[6rem]">
                            It's Your <b>Apex</b> Time, <br /> Are <b>You</b> re<b>ad</b>y?
                        </p>
                        <a href="contact.php"
                            class="group relative z-10 w-fit cursor-pointer overflow-hidden rounded-full bg-violet-50 px-7 py-3 text-black transition hover:opacity-75 mt-10">
                            <div class="relative inline-flex overflow-hidden font-general text-xs uppercase">
                                Contact Us</div>
                        </a>
                    </div>
                </div>
            </section>
        </main>
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
    </div>
    <!-- GSAP -->
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <!-- Custom JS -->
    <script src="script.js"></script>
</body>

</html>

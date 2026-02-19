// Register GSAP ScrollTrigger
gsap.registerPlugin(ScrollTrigger);

console.log("Script loaded");

// Main initialization function
document.addEventListener("DOMContentLoaded", () => {
    console.log("DOM loaded");

    // Initialize components
    initNavbar();
    initHero();
    initAbout();
    initFeatures();
    initStory();
    // initContact();
});

function initStory() {
    const storyImg = document.getElementById('story-img');

    if (!storyImg) return;

    const handleMouseMove = (e) => {
        const { clientX, clientY } = e;
        const rect = storyImg.getBoundingClientRect();

        const x = clientX - rect.left;
        const y = clientY - rect.top;

        const centerX = rect.width / 2;
        const centerY = rect.height / 2;

        const rotateX = ((y - centerY) / centerY) * -10;
        const rotateY = ((x - centerX) / centerX) * 10;

        gsap.to(storyImg, {
            duration: 0.3,
            rotateX,
            rotateY,
            transformPerspective: 500,
            ease: "power1.inOut",
        });
    };

    const handleMouseLeave = () => {
        gsap.to(storyImg, {
            duration: 0.3,
            rotateX: 0,
            rotateY: 0,
            ease: "power1.inOut",
        });
    };

    storyImg.addEventListener('mousemove', handleMouseMove);
    storyImg.addEventListener('mouseleave', handleMouseLeave);
    storyImg.addEventListener('mouseenter', handleMouseLeave);
    storyImg.addEventListener('mouseup', handleMouseLeave);
}

function initFeatures() {
    const tilts = document.querySelectorAll('.bento-tilt');

    tilts.forEach(item => {
        item.addEventListener('mousemove', (e) => {
            const { left, top, width, height } = item.getBoundingClientRect();

            const relativeX = (e.clientX - left) / width;
            const relativeY = (e.clientY - top) / height;

            const tiltX = (relativeY - 0.5) * 5;
            const tiltY = (relativeX - 0.5) * -5;

            item.style.transform = `perspective(700px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) scale3d(0.98, 0.98, 0.98)`;
        });

        item.addEventListener('mouseleave', () => {
            item.style.transform = '';
        });
    });
}

function initAbout() {
    // Clip Animation
    const clipAnimation = gsap.timeline({
        scrollTrigger: {
            trigger: "#clip",
            start: "center center",
            end: "+=800 center",
            scrub: 0.5,
            pin: true,
            pinSpacing: true,
        },
    });

    clipAnimation.to(".mask-clip-path", {
        width: "100vw",
        height: "100vh",
        borderRadius: 0,
    });

    // Animated Title
    const animatedTitles = document.querySelectorAll('.animated-title');

    animatedTitles.forEach((title) => {
        const words = title.querySelectorAll('.animated-word');

        gsap.to(words, {
            opacity: 1,
            transform: "translate3d(0, 0, 0) rotateY(0deg) rotateX(0deg)",
            ease: "power2.inOut",
            stagger: 0.02,
            scrollTrigger: {
                trigger: title,
                start: "100 bottom",
                end: "center bottom",
                toggleActions: "play none none reverse",
            },
        });
    });
}

function initHero() {
    const videoLinks = [
        "https://93w95scdts.ufs.sh/f/AOfILeWJzqCc5wEKtxLYRyJDZsOPGdFTt0lQuHLkeqjKCao1",
        "https://93w95scdts.ufs.sh/f/AOfILeWJzqCcLjP2Y7QEQuN5THDwzeBx4OvmaFZjP6ysCKk3",
        "https://93w95scdts.ufs.sh/f/AOfILeWJzqCcpmpmzmuj1IHWSEokgRuN2hMcUpBq0xQery3i",
        "https://93w95scdts.ufs.sh/f/AOfILeWJzqCcpB0GHsouj1IHWSEokgRuN2hMcUpBq0xQery3"
    ];

    let currentIndex = 1; // Starting with index 1 (hero1 is index 0)
    let hasClicked = false;
    const totalVideos = 4;

    const miniVideoWrapper = document.getElementById('mini-video-wrapper');
    const miniVideoContainer = document.getElementById('mini-video-container');
    const currentVideo = document.getElementById('current-video');
    const nextVideo = document.getElementById('next-video');
    const bgVideo = document.getElementById('bg-video');
    const loadingScreen = document.getElementById('loading-screen');

    const handleVideoLoad = () => {
        // Smooth fade out
        if (loadingScreen && loadingScreen.style.display !== 'none') {
            gsap.to(loadingScreen, {
                opacity: 0,
                duration: 0.5,
                onError: () => { loadingScreen.style.display = 'none'; },
                onComplete: () => {
                    loadingScreen.style.display = 'none';
                }
            });
        }
    };

    // IMMEDIATE LOAD: Don't wait for the video. 
    // The user will see the poster image (which loads fast) and then the video will play.
    // We add a tiny 100ms delay just to ensure the DOM is fully settled and the transition is smooth.
    setTimeout(handleVideoLoad, 100);

    // Click Handler
    miniVideoWrapper.addEventListener('click', () => {
        hasClicked = true;
        const upcomingIndex = (currentIndex % totalVideos) + 1;

        // Update next video src
        nextVideo.src = videoLinks[upcomingIndex - 1]; // Array is 0-indexed
        nextVideo.load(); // Ensure it loads

        gsap.set(nextVideo, { visibility: "visible" });

        gsap.to(nextVideo, {
            transformOrigin: "center center",
            scale: 1,
            width: "100%",
            height: "100%",
            duration: 1,
            ease: "power1.inOut",
            onStart: () => {
                nextVideo.play();
            },
            onComplete: () => {
                // Swap videos
                bgVideo.src = videoLinks[upcomingIndex - 1];
                bgVideo.play();

                // Reset next video
                gsap.set(nextVideo, { visibility: "hidden", scale: 0, width: "16rem", height: "16rem" }); // Reset size-64

                // Update current mini video
                const nextUpcomingIndex = (upcomingIndex % totalVideos) + 1;
                currentVideo.src = videoLinks[nextUpcomingIndex - 1];
                currentVideo.load();

                currentIndex = upcomingIndex;
            }
        });

        gsap.from(currentVideo, {
            transformOrigin: "center center",
            scale: 0,
            duration: 1.5,
            ease: "power1.inOut",
        });
    });

    // Scroll Animation
    gsap.set("#video-frame", {
        clipPath: "polygon(14% 0%, 72% 0%, 90% 90%, 0% 100%)",
        borderRadius: "0 0 40% 10%",
    });

    gsap.from("#video-frame", {
        clipPath: "polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%)",
        borderRadius: "0 0 0 0",
        ease: "power1.inOut",
        scrollTrigger: {
            trigger: "#video-frame",
            start: "center center",
            end: "bottom center",
            scrub: true,
        },
    });
}

function initNavbar() {
    const navContainer = document.getElementById('nav-container');
    const audioBtn = document.getElementById('audio-btn');
    const audioElement = document.getElementById('audio-element');
    const indicatorLines = document.querySelectorAll('.indicator-line');

    let isAudioPlaying = false;
    let lastScrollY = 0;
    let isNavVisible = true;

    // Audio Toggle
    audioBtn.addEventListener('click', () => {
        isAudioPlaying = !isAudioPlaying;

        if (isAudioPlaying) {
            audioElement.play();
            indicatorLines.forEach(line => line.classList.add('active'));
        } else {
            audioElement.pause();
            indicatorLines.forEach(line => line.classList.remove('active'));
        }
    });

    // Scroll Effect
    window.addEventListener('scroll', () => {
        const currentScrollY = window.scrollY;

        if (currentScrollY === 0) {
            isNavVisible = true;
            navContainer.classList.remove('floating-nav');
        } else if (currentScrollY > lastScrollY) {
            isNavVisible = false;
            navContainer.classList.add('floating-nav');
        } else if (currentScrollY < lastScrollY) {
            isNavVisible = true;
            navContainer.classList.add('floating-nav');
        }

        lastScrollY = currentScrollY;

        gsap.to(navContainer, {
            y: isNavVisible ? 0 : -100,
            opacity: isNavVisible ? 1 : 0,
            duration: 0.2,
        });
    });
}

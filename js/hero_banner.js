document.addEventListener("DOMContentLoaded", () => {
    const banner = document.getElementById("heroBanner");
    const jsonPath = banner.getAttribute("data-json") || "/home/hero_banner.json";
    const layerA = document.getElementById("layerA");
    const layerB = document.getElementById("layerB");
    const controls = banner.querySelector(".hero-controls");
    const content = banner.querySelector(".hero-content");
    const arrowLeft = banner.querySelector(".hero-arrow-left");
    const arrowRight = banner.querySelector(".hero-arrow-right");
    let media = [];
    let index = 0;
    let currentLayer = 'A';
    let isTransitioning = false;
    let autoTimeout = null;
    const delayBetween = 7000;
    const fadeDuration = 600;
    let currentVideos = { A: null, B: null }; // Track current video elements

    // Fetch media JSON
    fetch(jsonPath)
        .then(res => {
            if (!res.ok) {
                console.error('Failed to fetch hero banner JSON:', res.status, res.statusText);
                return [];
            }
            return res.json();
        })
        .then(json => {
            console.log('Loaded hero banner JSON:', json);
            media = json;
            if (!media.length || !media[0] || !media[0].src) {
                console.error('No valid media entries in hero banner JSON:', media);
                return;
            }
            console.log('First media entry:', media[0]);
            renderControls();
            showMedia(index, currentLayer, true);
            scheduleNext();
        })
        .catch(err => {
            console.error('Error loading hero banner JSON:', err);
        });

    function getLayer(id) {
        return id === 'A' ? layerA : layerB;
    }

    // Clean up video element properly
    function cleanupVideo(layerId) {
        if (currentVideos[layerId]) {
            const video = currentVideos[layerId];
            try {
                video.pause();
                video.removeAttribute('src');
                video.load(); // This helps clear the video buffer
                video.remove();
            } catch (e) {
                console.warn('Error cleaning up video:', e);
            }
            currentVideos[layerId] = null;
        }
    }

    function showMedia(idx, targetLayer, instant = false) {
        const item = media[idx];
        const layer = getLayer(targetLayer);
        
        // Clean up existing content
        layer.innerHTML = '';
        
        if (item.type === 'image') {
            const img = document.createElement('img');
            img.src = item.src;
            img.alt = item.title || '';
            img.className = 'hero-img';
            layer.appendChild(img);
        } else if (item.type === 'video') {
            // Clean up any existing video in this layer
            cleanupVideo(targetLayer);
            
            const video = document.createElement('video');
            video.src = item.src;
            video.muted = true;
            video.playsInline = true;
            video.loop = true;
            video.autoplay = false; // Don't autoplay immediately
            video.preload = 'metadata'; // Only preload metadata, not the full video
            if (item.poster) video.poster = item.poster;
            video.className = 'hero-video';
            
            // Store reference to current video
            currentVideos[targetLayer] = video;
            
            // Add error handling
            video.addEventListener('error', (e) => {
                console.error('Video error:', e);
                // Fallback to poster image if available
                if (item.poster) {
                    const fallbackImg = document.createElement('img');
                    fallbackImg.src = item.poster;
                    fallbackImg.alt = item.title || '';
                    fallbackImg.className = 'hero-img';
                    layer.innerHTML = '';
                    layer.appendChild(fallbackImg);
                }
            });
            
            // Add load event to start playing when ready
            video.addEventListener('loadedmetadata', () => {
                // Use requestAnimationFrame to ensure DOM is ready
                requestAnimationFrame(() => {
                    if (video.parentNode) { // Check if video is still in DOM
                        video.play().catch(err => {
                            console.warn('Autoplay failed:', err);
                            // Video will show poster image
                        });
                    }
                });
            });
            
            layer.appendChild(video);
        }
        renderContent(item, instant);
    }

    function renderContent(item, instant = false) {
        // Fade out, change content, then fade in
        if (!instant) {
            content.classList.remove('fade-in');
            content.classList.add('fade-out');
            setTimeout(() => {
                setContentHTML(item);
                content.classList.remove('fade-out');
                content.classList.add('fade-in');
            }, fadeDuration / 2);
        } else {
            setContentHTML(item);
            content.classList.add('fade-in');
        }
    }

    function setContentHTML(item) {
        let html = '';
        if (item.title) html += `<h1 class="hero-title">${escapeHTML(item.title)}</h1>`;
        if (item.subtitle) html += `<p class="hero-subtitle">${escapeHTML(item.subtitle)}</p>`;
        if (item.buttons && Array.isArray(item.buttons) && item.buttons.length) {
            html += '<div class="hero-buttons">';
            for (const btn of item.buttons) {
                const isExternal = /^https?:\/\//.test(btn.link);
                const rel = isExternal ? ' rel="noopener noreferrer"' : '';
                html += `<a class="cta-button" href="${escapeHTML(btn.link)}"${rel}>${escapeHTML(btn.text)}</a>`;
            }
            html += '</div>';
        }
        content.innerHTML = html;
    }

    function escapeHTML(str) {
        return str.replace(/[&<>"]|'/g, function (c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c];
        });
    }

    function fadeTo(nextIdx) {
        if (isTransitioning || nextIdx === index) return;
        isTransitioning = true;
        
        // Fade out text
        content.classList.remove('fade-in');
        content.classList.add('fade-out');
        
        const nextLayer = currentLayer === 'A' ? 'B' : 'A';
        
        setTimeout(() => {
            showMedia(nextIdx, nextLayer);
            getLayer(nextLayer).classList.add('visible');
            getLayer(currentLayer).classList.remove('visible');
            
            setTimeout(() => {
                // Clean up the old layer's video after transition
                cleanupVideo(currentLayer);
                
                currentLayer = nextLayer;
                index = nextIdx;
                updateControls();
                isTransitioning = false;
                scheduleNext();
            }, fadeDuration);
        }, fadeDuration / 2);
    }

    function next() {
        fadeTo((index + 1) % media.length);
    }
    function prev() {
        fadeTo((index - 1 + media.length) % media.length);
    }
    function jumpTo(i) {
        fadeTo(i);
    }
    function scheduleNext() {
        clearTimeout(autoTimeout);
        autoTimeout = setTimeout(next, delayBetween);
    }

    // Controls
    function renderControls() {
        controls.innerHTML = '';
        media.forEach((_, i) => {
            const dot = document.createElement('button');
            dot.className = 'hero-dot';
            dot.type = 'button';
            dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
            dot.addEventListener('click', () => jumpTo(i));
            controls.appendChild(dot);
        });
        updateControls();
    }
    function updateControls() {
        Array.from(controls.children).forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
    }

    // Arrow controls (desktop only, show on hover)
    function showArrows(show) {
        arrowLeft.style.display = show ? '' : 'none';
        arrowRight.style.display = show ? '' : 'none';
    }
    banner.addEventListener('mouseenter', () => {
        if (!isMobile()) showArrows(true);
    });
    banner.addEventListener('mouseleave', () => {
        if (!isMobile()) showArrows(false);
    });
    arrowLeft.addEventListener('click', prev);
    arrowRight.addEventListener('click', next);

    // Mobile swipe
    let touchStartX = null;
    let touchOnControls = false;
    banner.addEventListener('touchstart', e => {
        // If touch is on controls, do not trigger swipe
        if (e.touches.length === 1) {
            const target = e.target;
            if (target.closest('.hero-controls')) {
                touchOnControls = true;
                return;
            }
            touchOnControls = false;
            touchStartX = e.touches[0].clientX;
        }
    });
    banner.addEventListener('touchend', e => {
        if (touchOnControls) {
            touchOnControls = false;
            return;
        }
        if (touchStartX === null) return;
        const dx = e.changedTouches[0].clientX - touchStartX;
        if (Math.abs(dx) > 40) {
            if (dx > 0) prev();
            else next();
        }
        touchStartX = null;
    });

    function isMobile() {
        return window.matchMedia("(max-width: 768px)").matches;
    }
    // Hide arrows on mobile
    function handleResize() {
        if (isMobile()) showArrows(false);
    }
    window.addEventListener('resize', handleResize);
    handleResize();
    
    // Clean up on page unload
    window.addEventListener('beforeunload', () => {
        clearTimeout(autoTimeout);
        cleanupVideo('A');
        cleanupVideo('B');
    });
    
    // Pause videos when page becomes hidden (tab switch, minimize, etc.)
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            // Pause all videos when page is hidden
            Object.values(currentVideos).forEach(video => {
                if (video) {
                    try {
                        video.pause();
                    } catch (e) {
                        console.warn('Error pausing video:', e);
                    }
                }
            });
        } else {
            // Resume current video when page becomes visible
            const currentVideo = currentVideos[currentLayer];
            if (currentVideo && media[index] && media[index].type === 'video') {
                try {
                    currentVideo.play().catch(err => {
                        console.warn('Error resuming video:', err);
                    });
                } catch (e) {
                    console.warn('Error resuming video:', e);
                }
            }
        }
    });
}); 
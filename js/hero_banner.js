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
    let isVisible = true; // Track if banner is visible
    let preloadedImages = new Set(); // Track preloaded images
    let preloadedVideos = new Map(); // Track preloaded videos

    // Browser detection for specific optimizations
    const isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
    const isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor);
    const isFirefox = /Firefox/.test(navigator.userAgent);
    const isEdge = /Edg/.test(navigator.userAgent);

    // Intersection Observer for performance optimization
    let intersectionObserver;
    let resumeTimeout = null;
    console.log('IntersectionObserver available:', 'IntersectionObserver' in window);
    if ('IntersectionObserver' in window) {
        intersectionObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const wasVisible = isVisible;
                isVisible = entry.isIntersecting;
                console.log('Intersection observer:', { wasVisible, isVisible, isIntersecting: entry.isIntersecting });
                
                if (!isVisible && wasVisible) {
                    // Pause videos when going out of view
                    Object.values(currentVideos).forEach(video => {
                        if (video) {
                            try {
                                video.pause();
                            } catch (e) {
                                console.warn('Error pausing video:', e);
                            }
                        }
                    });
                    clearTimeout(resumeTimeout);
                    // Pause auto-advance when not visible
                    clearTimeout(autoTimeout);
                } else if (isVisible && !wasVisible) {
                    // Resume current video when coming into view with a small delay
                    clearTimeout(resumeTimeout);
                    resumeTimeout = setTimeout(() => {
                        const currentVideo = currentVideos[currentLayer];
                        if (currentVideo && media[index] && media[index].type === 'video') {
                            try {
                                // For Firefox, we need to be more careful about resuming
                                if (isFirefox) {
                                    // Check if video is ready to play
                                    if (currentVideo.readyState >= 2) { // HAVE_CURRENT_DATA
                                        currentVideo.play().catch(err => {
                                            console.warn('Error resuming video in Firefox:', err);
                                        });
                                    } else {
                                        // Wait a bit more for Firefox to be ready
                                        setTimeout(() => {
                                            if (currentVideo.parentNode && isVisible) {
                                                currentVideo.play().catch(err => {
                                                    console.warn('Error resuming video in Firefox (delayed):', err);
                                                });
                                            }
                                        }, 100);
                                    }
                                } else {
                                    currentVideo.play().catch(err => {
                                        console.warn('Error resuming video:', err);
                                    });
                                }
                            } catch (e) {
                                console.warn('Error resuming video:', e);
                            }
                        }
                        // Resume auto-advance when visible
                        scheduleNext();
                    }, 150); // Small delay to prevent stuttering
                }
            });
        }, {
            threshold: 0.1, // Trigger when 10% of banner is visible
            rootMargin: '50px' // Start loading 50px before banner comes into view
        });
        intersectionObserver.observe(banner);
    } else {
        console.log('IntersectionObserver not available, using fallback');
        // Fallback: assume banner is always visible
        isVisible = true;
    }

    // Preload next media items for smoother transitions
    function preloadNextMedia() {
        const nextIndex = (index + 1) % media.length;
        const nextItem = media[nextIndex];
        
        if (!nextItem) return;
        
        if (nextItem.type === 'image' && !preloadedImages.has(nextItem.src)) {
            const img = new Image();
            img.onload = () => preloadedImages.add(nextItem.src);
            img.src = nextItem.src;
        } else if (nextItem.type === 'video' && !preloadedVideos.has(nextItem.src)) {
            // For videos, we'll preload metadata only
            const video = document.createElement('video');
            video.preload = 'metadata';
            video.muted = true;
            video.src = nextItem.src;
            video.addEventListener('loadedmetadata', () => {
                preloadedVideos.set(nextItem.src, true);
                video.remove(); // Clean up preload element
            });
            video.addEventListener('error', () => {
                video.remove(); // Clean up on error
            });
        }
    }

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
            console.log('Initial isVisible:', isVisible, 'isMobile:', isMobile());
            renderControls();
            showMedia(index, currentLayer, true);
            scheduleNext();
            preloadNextMedia(); // Start preloading
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
            
            // Add loading optimization
            if (preloadedImages.has(item.src)) {
                img.style.opacity = '1'; // Already loaded
            } else {
                img.style.opacity = '0';
                img.style.transition = 'opacity 0.3s ease-in-out';
                img.onload = () => {
                    img.style.opacity = '1';
                    preloadedImages.add(item.src);
                };
            }
            
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
            
            // Browser-specific optimizations
            if (isChrome) {
                video.preload = 'metadata'; // Chrome works well with metadata preload
            } else if (isSafari) {
                video.preload = 'auto'; // Safari prefers auto preload
                video.setAttribute('webkit-playsinline', 'true');
            } else if (isFirefox) {
                video.preload = 'metadata'; // Firefox works well with metadata
            } else {
                video.preload = 'metadata'; // Default to metadata
            }
            
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
                    if (video.parentNode && isVisible) { // Check if video is still in DOM and visible
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
                preloadNextMedia(); // Preload next item
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
        // Always auto-advance, regardless of device size
        console.log('Scheduling next auto-advance in', delayBetween, 'ms');
        autoTimeout = setTimeout(() => {
            console.log('Auto-advancing to next slide');
            next();
        }, delayBetween);
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

    // Mobile swipe with improved touch handling
    let touchStartX = null;
    let touchStartY = null;
    let touchOnControls = false;
    let isScrolling = false;
    
    banner.addEventListener('touchstart', e => {
        if (e.touches.length === 1) {
            const target = e.target;
            if (target.closest('.hero-controls')) {
                touchOnControls = true;
                return;
            }
            touchOnControls = false;
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            isScrolling = false;
        }
    });
    
    banner.addEventListener('touchmove', e => {
        if (touchStartX === null || touchOnControls) return;
        
        const touchX = e.touches[0].clientX;
        const touchY = e.touches[0].clientY;
        const deltaX = Math.abs(touchX - touchStartX);
        const deltaY = Math.abs(touchY - touchStartY);
        
        // If vertical scrolling is more than horizontal, don't trigger swipe
        if (deltaY > deltaX && deltaY > 10) {
            isScrolling = true;
        }
    });
    
    banner.addEventListener('touchend', e => {
        if (touchOnControls || isScrolling) {
            touchOnControls = false;
            isScrolling = false;
            touchStartX = null;
            touchStartY = null;
            return;
        }
        
        if (touchStartX === null) return;
        const dx = e.changedTouches[0].clientX - touchStartX;
        const dy = Math.abs(e.changedTouches[0].clientY - touchStartY);
        
        // Only trigger swipe if horizontal movement is significant and vertical is minimal
        if (Math.abs(dx) > 40 && dy < 100) {
            if (dx > 0) prev();
            else next();
        }
        
        touchStartX = null;
        touchStartY = null;
        isScrolling = false;
    });

    function isMobile() {
        return window.matchMedia("(max-width: 768px)").matches;
    }
    
    // Hide arrows on mobile
    function handleResize() {
        if (isMobile()) {
            showArrows(false);
            // On mobile, pause auto-advance and resume when desktop
            clearTimeout(autoTimeout);
        } else {
            // Resume auto-advance on desktop if visible
            if (isVisible) {
                scheduleNext();
            }
        }
    }
    window.addEventListener('resize', handleResize);
    handleResize();
    
    // Clean up on page unload
    window.addEventListener('beforeunload', () => {
        clearTimeout(autoTimeout);
        clearTimeout(resumeTimeout);
        cleanupVideo('A');
        cleanupVideo('B');
        if (intersectionObserver) {
            intersectionObserver.disconnect();
        }
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
            clearTimeout(autoTimeout); // Stop auto-advance
        } else {
            // Resume current video when page becomes visible
            const currentVideo = currentVideos[currentLayer];
            if (currentVideo && media[index] && media[index].type === 'video' && isVisible) {
                try {
                    currentVideo.play().catch(err => {
                        console.warn('Error resuming video:', err);
                    });
                } catch (e) {
                    console.warn('Error resuming video:', e);
                }
            }
            // Resume auto-advance if not on mobile
            if (!isMobile()) {
                scheduleNext();
            }
        }
    });
}); 
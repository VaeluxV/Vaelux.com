<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects</title>
    <link rel="icon" type="image/x-icon" href="/images/favicons/main/favicon.ico">
    <link rel="stylesheet" href="/css/main_stylesheet.css">
    <link rel="stylesheet" href="/css/projects_stylesheet.css">
</head>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/headers/main_header.php'; ?>

    <!-- Pre-load images -->
    <?php
    $directory = '/images/irl_trains';
    $images = glob($_SERVER['DOCUMENT_ROOT'] . $directory . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    foreach ($images as $image) {
        $imagePath = $directory . '/' . basename($image);
        echo '<img src="' . htmlspecialchars($imagePath) . '" style="display: none;" alt="">';
    }
    ?>

    <main>
        <!-- Hero Section -->
        <section class="hero-container">
            <div class="hero" id="hero-section"></div>
            <div class="hero" id="hero-overlay"></div>
            <div class="hero-content">
                <h1>My Projects</h1>
                <p>Explore some of the projects I've worked on.</p>
            </div>
        </section>

        <!-- Projects Section -->
        <section class="projects-container">
        <div class="project-card">
                <img src="/images/irl_trains/ultra_low_res/02_NMBS-SNCB-HLE18-M6-1860.jpg" alt="Project 1">
                <div class="project-card-content">
                    <h3>Project 1</h3>
                    <p>Placeholder text about the project.</p>
                    <p>This page is still a WIP, check back soon!</p>
                    <a href="#" class="cta-button">Learn More</a>
                </div>
            </div>
            <div class="project-card">
                <img src="/images/irl_trains/ultra_low_res/06_NS-TRAXX-E186-011.jpg" alt="Project 2">
                <div class="project-card-content">
                    <h3>Project 2</h3>
                    <p>Placeholder text about the project.</p>
                    <p>This page is still a WIP, check back soon!</p>
                    <a href="#" class="cta-button">Learn More</a>
                </div>
            </div>
            <div class="project-card">
                <img src="/images/irl_trains/ultra_low_res/04_NMBS-SNCB-M6-HLE18-1839.jpg" alt="Project 3">
                <div class="project-card-content">
                    <h3>Project 3</h3>
                    <p>Placeholder text about the project.</p>
                    <p>This page is still a WIP, check back soon!</p>
                    <a href="#" class="cta-button">Learn More</a>
                </div>
            </div>
            <div class="project-card">
                <img src="/images/irl_trains/ultra_low_res/08_Eurostar-PBKA-4341.jpg" alt="Project 4">
                <div class="project-card-content">
                    <h3>Project 4</h3>
                    <p>Placeholder text about the project.</p>
                    <p>This page is still a WIP, check back soon!</p>
                    <a href="#" class="cta-button">Learn More</a>
                </div>
            </div>
            <div class="project-card">
                <img src="/images/irl_trains/ultra_low_res/10_Eurostar-E320-4014.jpg" alt="Project 5">
                <div class="project-card-content">
                    <h3>Project 5</h3>
                    <p>Placeholder text about the project.</p>
                    <p>This page is still a WIP, check back soon!</p>
                    <a href="#" class="cta-button">Learn More</a>
                </div>
            </div>
            <div class="project-card">
                <img src="/images/irl_trains/ultra_low_res/09_NMBS-SNCB-TRAXX-E186-207.jpg" alt="Project 6">
                <div class="project-card-content">
                    <h3>Project 6</h3>
                    <p>Placeholder text about the project.</p>
                    <p>This page is still a WIP, check back soon!</p>
                    <a href="#" class="cta-button">Learn More</a>
                </div>
            </div>
            <div class="project-card">
                <img src="/images/irl_trains/ultra_low_res/03_NS-ICNG-B-331.jpg" alt="Project 7">
                <div class="project-card-content">
                    <h3>Project 7</h3>
                    <p>Placeholder text about the project.</p>
                    <p>This page is still a WIP, check back soon!</p>
                    <a href="#" class="cta-button">Learn More</a>
                </div>
            </div>
        </section>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/footers/main_footer.php'; ?>

    <!-- Hero Banner Script -->
    <?php
    $imageDirectory = $_SERVER['DOCUMENT_ROOT'] . '/images/irl_trains';
    $images = array_filter(
        glob("{$imageDirectory}/*.{jpg,jpeg,png,gif}", GLOB_BRACE),
        'file_exists'
    );

    echo "<script>
            const images = [";
    foreach ($images as $image) {
        $imagePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $image);
        echo "'{$imagePath}',";
    }
    echo "];
            const heroSection = document.getElementById('hero-section');
            const heroOverlay = document.getElementById('hero-overlay');
            heroSection.style.backgroundImage = 'url(' + images[0] + ')';
            heroOverlay.style.backgroundImage = 'url(' + images[0] + ')';
            let currentIndex = 0;
            function changeImage() {
                currentIndex = (currentIndex + 1) % images.length;
                heroOverlay.style.backgroundImage = 'url(' + images[currentIndex] + ')';
                heroOverlay.style.opacity = 1;
                setTimeout(() => {
                    heroSection.style.backgroundImage = 'url(' + images[currentIndex] + ')';
                    heroOverlay.style.opacity = 0;
                }, 1500);
            }
            setInterval(changeImage, 7500);
          </script>";
    ?>
</body>

</html>

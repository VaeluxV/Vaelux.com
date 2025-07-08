<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects</title>
    <link rel="icon" type="image/x-icon" href="/images/favicons/main/favicon.ico">
    <link rel="stylesheet" href="/css/main_stylesheet.css">
    <link rel="stylesheet" href="/css/projects_stylesheet_old.css">
</head>

<body>
    <?php
    // Safe function to get server variables with validation
    function get_server_var(string $key, $default = '') {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
    }

    // Safe function to escape output
    function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    include __DIR__ . '/../headers/main_header.php';
    ?>

    <!-- Pre-load images -->
    <?php
    $document_root = get_server_var('DOCUMENT_ROOT');
    if (!empty($document_root) && is_dir($document_root)) {
        $directory = '/images/irl_trains';
        $images = glob($document_root . $directory . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        if ($images !== false) {
            foreach ($images as $image) {
                $imagePath = $directory . '/' . basename($image);
                echo '<img src="' . e($imagePath) . '" style="display: none;" alt="">';
            }
        }
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
                <img src="/images/irl_trains/ultra_low_res/06_NS-TRAXX-E186-011.jpg" alt="Project Cover Image">
                <div class="project-card-content">
                    <h3>This website!</h3>
                    <p>This website is a little project I like to work on from time to time.</p>
                    <p>Feel free to look around!</p>
                    <a href="https://github.com/VaeluxV/Vaelux.com" class="cta-button" target="_blank" rel="noopener noreferrer">Website GitHub repo</a>
                </div>
            </div>

            <div class="project-card">
                <img src="/images/projects/modular_train_sim/renders/MTS-v0.9.8-Render-2024-12-14-17-46-28.png" alt="Modular Train Simulator Project Cover Image">
                <div class="project-card-content">
                    <h3>MTS Project</h3>
                    <p>The MTS (Modular Train Simulator) Project is my take on a homemade, modular controller for train simulators.</p>
                    <p>This project is in an unfinished state.</p>
                    <a href="#" class="cta-button">Learn More</a>
                </div>
            </div>

            <div class="project-card">
                <img src="/images/projects/vertexarmor_enclosure/Render-V2.3_2024-06-20_1.jpg" alt="VertexArmor-Enclosure Cover Image">
                <div class="project-card-content">
                    <h3>VertexArmor</h3>
                    <p>VertexArmor is an enclosure designed for my custom SlimeVR PCB</p>
                    <a href="https://github.com/VaeluxV/VertexArmor-Enclosure/" class="cta-button" target="_blank" rel="noopener noreferrer">GitHub repo</a>
                    <br><br>
                    <a href="https://slimevr.dev/" class="cta-button" target="_blank" rel="noopener noreferrer">SlimeVR Website</a>
                </div>
            </div>

        </section>
    </main>

    <?php include __DIR__ . '/../footers/main_footer.php'; ?>

    <!-- Hero Banner Script -->
    <?php
    $document_root = get_server_var('DOCUMENT_ROOT');
    if (!empty($document_root) && is_dir($document_root)) {
        $imageDirectory = $document_root . '/images/irl_trains';
        $images = array_filter(
            glob("{$imageDirectory}/*.{jpg,jpeg,png,gif}", GLOB_BRACE),
            'file_exists'
        );

        if (!empty($images)) {
            echo "<script>
                    const images = [";
            
            foreach ($images as $image) {
                $imagePath = str_replace($document_root, '', $image);
                echo json_encode($imagePath) . ',';
            }
            
            echo "];
                    const heroSection = document.getElementById('hero-section');
                    const heroOverlay = document.getElementById('hero-overlay');
                    if (images.length > 0) {
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
                    }
                  </script>";
        }
    }
    ?>
</body>

</html>

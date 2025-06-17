<?php
$projectsPath = __DIR__ . '/project';
$folders = glob($projectsPath . '/*', GLOB_ONLYDIR);
?>

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

    <?php include __DIR__ . '/../headers/main_header.php'; ?>

    <main>
        <section class="projects-title">
            <h1>All of my projects</h1>
        </section>

        <div class="projects-container">
            <?php foreach ($folders as $folder): ?>
                <?php
                $infoPath = $folder . '/info.json';
                if (!file_exists($infoPath)) continue;

                $info = json_decode(file_get_contents($infoPath), true);
                if (!$info) continue;

                $title = $info['title'] ?? 'Untitled Project';
                $desc = $info['description'] ?? 'No description available.';
                $image = $info['image'] ?? null;
                $link  = $info['link'] ?? '#';

                $imagePath = $image
                    ? '/projects/project/' . basename($folder) . '/' . $image
                    : '/projects/placeholder.jpg';

                $linkPath = '/projects/project/' . basename($folder) . '/' . $link;
                ?>
                <div class="project-card">
                    <div class="aspect-ratio-16-9">
                        <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($title) ?>">
                    </div>
                    <div class="project-card-content">
                        <h3><?= htmlspecialchars($title) ?></h3>
                        <p><?= htmlspecialchars($desc) ?></p>
                        <a href="<?= htmlspecialchars($linkPath) ?>" class="cta-button">View Project</a>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>

    </main>

    <?php include __DIR__ . '/../footers/main_footer.php'; ?>

</body>

</html>
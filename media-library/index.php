<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Gallery</title>
    <link rel="icon" type="image/x-icon" href="/images/favicons/main/favicon.ico">
    <link rel="stylesheet" href="/css/main_stylesheet.css">
    <link rel="stylesheet" href="/css/media_library_stylesheet.css">
</head>

<body>
    <?php
    function server_var(string $key, $default = '') { // Function to get server variable(s) to prevent direct use of $_SERVER as much as possible
        return $_SERVER[$key] ?? $default;
    }

    include server_var('DOCUMENT_ROOT', __DIR__) . '/headers/main_header.php';
    ?>

    <header-title>
        <h1>Media Library</h1>
    </header-title>
    <main>
        <!-- Sidebar with filters -->
        <aside id="sidebar" class="sidebar">
            <div class="sidebar-content">
                <h3>Filter</h3>
                <h4>General Filters</h4>
                <button class="clear-filters">Clear Filters</button>
                <button class="tag-filter" data-tag="train">Trains</button>
                <button class="tag-filter" data-tag="tram">Trams</button>
                <button class="tag-filter" data-tag="bus">Busses</button>
                <button class="tag-filter" data-tag="other-vehicle">Other vehicles</button>
                <button class="tag-filter" data-tag="building">Buildings</button>
                <button class="tag-filter" data-tag="station">Stations</button>

                <h4>Media Types</h4>
                <button class="type-filter" data-type="all">All</button>
                <button class="type-filter" data-type="image">Pictures</button>
                <button class="type-filter" data-type="video">Videos</button>
                <button class="type-filter" data-type="other">Other</button>

                <h4>Train Types - NMBS/SNCB</h4>
                <!-- <button class="tag-filter" data-tag="nmbs-sncb-hle11">HLE11</button>
                <button class="tag-filter" data-tag="nmbs-sncb-hle12">HLE12</button> -->
                <button class="tag-filter" data-tag="nmbs-sncb-hle13">HLE13</button>
                <button class="tag-filter" data-tag="nmbs-sncb-hle18">HLE18</button>
                <button class="tag-filter" data-tag="nmbs-sncb-hle19">HLE19</button>
                <button class="tag-filter" data-tag="nmbs-sncb-hle27">HLE27</button>
                <button class="tag-filter" data-tag="nmbs-sncb-hle28">HLE28</button>
                <button class="tag-filter" data-tag="nmbs-sncb-hle29">HLE29</button>
                <button class="tag-filter" data-tag="nmbs-sncb-hlr-hld-77-78">HLR/HLD77/78</button>
                <button class="tag-filter" data-tag="nmbs-sncb-i11">I11</button>
                <button class="tag-filter" data-tag="nmbs-sncb-m5">M5</button>
                <button class="tag-filter" data-tag="nmbs-sncb-m6">M6</button>
                <button class="tag-filter" data-tag="nmbs-sncb-hle18">HLR77</button>
                <button class="tag-filter" data-tag="nmbs-sncb-mr08">MR08</button>
                <button class="tag-filter" data-tag="nmbs-sncb-mr75">MR75</button>
                <button class="tag-filter" data-tag="nmbs-sncb-mr80">MR80</button>
                <button class="tag-filter" data-tag="nmbs-sncb-mr86">MR86</button>
                <button class="tag-filter" data-tag="nmbs-sncb-mr96">MR96</button>
                <button class="tag-filter" data-tag="nmbs-sncb-mr96">MW41</button>

                <h4>Train Types - NS</h4>
                <!-- <button class="tag-filter" data-tag="ns-ddar">DDAR</button> -->
                <button class="tag-filter" data-tag="ns-ddz">DDZ</button>
                <button class="tag-filter" data-tag="ns-flirt">Flirt</button>
                <button class="tag-filter" data-tag="ns-icm">ICM</button>
                <button class="tag-filter" data-tag="ns-icng">ICNG</button>
                <button class="tag-filter" data-tag="ns-icng-b">ICNG-B</button>
                <button class="tag-filter" data-tag="ns-ic-direct">IC Direct</button>
                <button class="tag-filter" data-tag="ns-slt">SLT</button>
                <button class="tag-filter" data-tag="ns-sng">SNG</button>
                <button class="tag-filter" data-tag="ns-virm">VIRM</button>

                <h4>Train Types - Eurostar</h4>
                <button class="tag-filter" data-tag="eurostar-e300">Eurostar E300</button>
                <button class="tag-filter" data-tag="eurostar-e320">Eurostar E320</button>
                <button class="tag-filter" data-tag="eurostar-pba">Eurostar PBA</button>
                <button class="tag-filter" data-tag="eurostar-pbka">Eurostar PBKA</button>
                <button class="tag-filter" data-tag="thalys-pba">Thalys PBA</button>
                <button class="tag-filter" data-tag="thalys-pbka">Thalys PBKA</button>

                <h4>Train Types - SNCF</h4>
                <button class="tag-filter" data-tag="sncf-tgv-reseau">TGV Atlantique</button>
                <button class="tag-filter" data-tag="sncf-tgv-duplex">TGV Duplex</button>
                <button class="tag-filter" data-tag="sncf-tgv-avelia">TGV M</button>
                <button class="tag-filter" data-tag="sncf-tgv-pos">TGV POS</button>
                <button class="tag-filter" data-tag="sncf-tgv-reseau">TGV Reseau</button>

                <h4>Train Types - DB</h4>
                <button class="tag-filter" data-tag="db-vectron">DB Vectron</button>

                <!-- <h4>Train Types - JR - Shinkansen</h4>
                <button class="tag-filter" data-tag="shinkansen-0">0 Series</button>
                <button class="tag-filter" data-tag="shinkansen-100">100 Series</button>
                <button class="tag-filter" data-tag="shinkansen-200">200 Series</button>
                <button class="tag-filter" data-tag="shinkansen-300">300 Series</button>
                <button class="tag-filter" data-tag="shinkansen-400">400 Series</button>
                <button class="tag-filter" data-tag="shinkansen-500">500 Series</button>
                <button class="tag-filter" data-tag="shinkansen-700">700 Series</button>
                <button class="tag-filter" data-tag="shinkansen-e1">E1 Series</button>
                <button class="tag-filter" data-tag="shinkansen-e2">E2 Series</button>
                <button class="tag-filter" data-tag="shinkansen-e3">E3 Series</button>
                <button class="tag-filter" data-tag="shinkansen-e4">E4 Series</button>
                <button class="tag-filter" data-tag="shinkansen-e5">E5 Series</button>
                <button class="tag-filter" data-tag="shinkansen-e6">E6 Series</button>
                <button class="tag-filter" data-tag="shinkansen-e7">E7 Series</button>
                <button class="tag-filter" data-tag="shinkansen-e8">E8 Series</button>
                <button class="tag-filter" data-tag="shinkansen-e5">E5 Series</button>
                <button class="tag-filter" data-tag="shinkansen-doctor-yellow">Doctor Yellow</button>
                -->

                <h4>Train Types - Other</h4>

                <button class="tag-filter" data-tag="trains-other">Other trains</button>

                <h4>Media Categories</h4>
                <button class="tag-filter" data-tag="event">Event</button>
                <button class="tag-filter" data-tag="informative">Informative</button>
                <button class="tag-filter" data-tag="news">News</button>
                <button class="tag-filter" data-tag="spotting">Spotting</button>

                <h4>All Tags</h4>
                <?php
                // Parse this page and extract tag-filter buttons
                $dom = new DOMDocument();
                libxml_use_internal_errors(true); // Suppress HTML parsing warnings
                
                // Load the current file content
                $currentFile = __FILE__;
                $html = file_get_contents($currentFile);
                $dom->loadHTML($html);
                libxml_clear_errors();

                // Get all buttons with the class 'tag-filter'
                $buttons = $dom->getElementsByTagName('button');
                $tags = [];

                foreach ($buttons as $button) {
                    if ($button->getAttribute('class') === 'tag-filter') {
                        $dataTag = $button->getAttribute('data-tag');
                        $label = trim($button->textContent); // Ensure clean label text
                        $tags[$dataTag] = $label; // Store as key-value pair
                    }
                }

                // Sort tags alphabetically by label
                asort($tags);

                // Render sorted tags, skipping the first iteration
                $first = true;
                foreach ($tags as $key => $label) {
                    if ($first) {
                        $first = false;
                        continue; // Skip the first iteration
                    }
                    echo '<button class="tag-filter" data-tag="' . htmlspecialchars($key) . '">' . htmlspecialchars($label) . '</button>' . "\n";
                }
                ?>

            </div>
        </aside>
        <!-- Sidebar toggle button -->
        <button id="toggle-sidebar" class="sidebar-toggle">&#9654;</button>

        <!-- Media Grid -->
        <section id="media-grid"></section>
    </main>
    <script src="/js/media_library.js"></script>
    <?php include server_var('DOCUMENT_ROOT', __DIR__) . '/footers/main_footer.php'; ?>
</body>

</html>
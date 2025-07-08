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
    require_once __DIR__ . '/../utils.php';
    include __DIR__ . '/../headers/main_header.php';
    ?>

    <header-title style="display: flex; align-items: center; justify-content: center; padding: 48px 32px 0 32px; gap: 16px;">
        <h1 style="flex: 1; margin: 0; text-align: center;">Media Library</h1>
        <button id="toggle-sidebar" class="sidebar-toggle">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="search-icon">
                <path d="M21 21L16.514 16.506L21 21ZM19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </header-title>
    <main>
        <!-- Sidebar with filters -->
        <aside id="sidebar" class="sidebar">
            <div class="sidebar-content">
                <!-- Mobile Close Button -->
                <button id="mobile-close-sidebar" class="mobile-close-sidebar">×</button>
                
                <h3>Filters & Search</h3>
                
                <!-- Search Box -->
                <div class="search-container">
                    <div class="search-input-wrapper">
                        <input type="text" id="search-input" placeholder="Search titles, descriptions, or tags..." class="search-input">
                        <button id="search-button" class="search-button">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="search-icon">
                                <path d="M21 21L16.514 16.506L21 21ZM19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Search Suggestions -->
                    <div id="search-suggestions" class="search-suggestions"></div>
                </div>
                
                <h4>Active Filters</h4>
                <div class="filter-status">
                    <span id="selected-tags-count">No tags selected</span>
                </div>
                
                <!-- Tag Matching Mode Toggle -->
                <div class="matching-mode-container">
                    <label class="matching-mode-label">Tag Matching:</label>
                    <div class="matching-mode-toggle">
                        <button id="any-tags-mode" class="mode-button active" data-mode="any">
                            <span class="mode-text">ANY Tag</span>
                            <span class="mode-description">Show items with any of the selected tags</span>
                        </button>
                        <button id="all-tags-mode" class="mode-button" data-mode="all">
                            <span class="mode-text">ALL Tags</span>
                            <span class="mode-description">Show items with all selected tags only</span>
                        </button>
                    </div>
                </div>
                
                <button class="clear-filters">Clear All Filters</button>
                
                <!-- Media Types (expanded by default) -->
                <div class="collapsible-section">
                    <button class="collapsible-toggle" id="media-types-toggle">
                        <span class="toggle-text">Media Types</span>
                        <span class="toggle-icon">▼</span>
                    </button>
                    <div class="collapsible-content" id="media-types-content">
                        <button class="type-filter" data-type="image">Pictures</button>
                        <button class="type-filter" data-type="video">Videos</button>
                        <button class="type-filter" data-type="other">Other</button>
                    </div>
                </div>

                <!-- Locations (collapsed by default, with more options) -->
                <div class="collapsible-section">
                    <button class="collapsible-toggle" id="locations-toggle">
                        <span class="toggle-text">Locations</span>
                        <span class="toggle-icon">▼</span>
                    </button>
                    <div class="collapsible-content" id="locations-content">
                        <button class="tag-filter" data-tag="station">Stations</button>
                        <button class="tag-filter" data-tag="building">Buildings</button>
                        <button class="tag-filter" data-tag="bridge">Bridges</button>
                        <button class="tag-filter" data-tag="viaduct">Viaducts</button>
                        <button class="tag-filter" data-tag="tunnel">Tunnels</button>
                        <button class="tag-filter" data-tag="yard">Yards</button>
                        <button class="tag-filter" data-tag="depot">Depots</button>
                        <button class="tag-filter" data-tag="crossing">Crossings</button>
                    </div>
                </div>

                <!-- Vehicle Types (collapsed by default) -->
                <div class="collapsible-section">
                    <button class="collapsible-toggle" id="vehicle-types-toggle">
                        <span class="toggle-text">Vehicle Types</span>
                        <span class="toggle-icon">▼</span>
                    </button>
                    <div class="collapsible-content" id="vehicle-types-content">
                        <button class="tag-filter" data-tag="train">Trains</button>
                        <button class="tag-filter" data-tag="tram">Trams</button>
                        <button class="tag-filter" data-tag="bus">Busses</button>
                        <button class="tag-filter" data-tag="other-vehicle">Other vehicles</button>
                    </div>
                </div>

                <!-- Train Types (collapsed by default, with nested operators) -->
                <div class="collapsible-section">
                    <button class="collapsible-toggle" id="train-types-toggle">
                        <span class="toggle-text">Train Types</span>
                        <span class="toggle-icon">▼</span>
                    </button>
                    <div class="collapsible-content" id="train-types-content">
                        <!-- Nested operator sections as before -->
                        <div class="collapsible-section nested">
                            <button class="collapsible-toggle nested-toggle" id="nmbs-sncb-toggle">
                                <span class="toggle-text">NMBS/SNCB</span>
                                <span class="toggle-icon">▼</span>
                            </button>
                            <div class="collapsible-content nested-content" id="nmbs-sncb-content">
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
                                <button class="tag-filter" data-tag="nmbs-sncb-mr08">MR08</button>
                                <button class="tag-filter" data-tag="nmbs-sncb-mr75">MR75</button>
                                <button class="tag-filter" data-tag="nmbs-sncb-mr80">MR80</button>
                                <button class="tag-filter" data-tag="nmbs-sncb-mr86">MR86</button>
                                <button class="tag-filter" data-tag="nmbs-sncb-mr96">MR96</button>
                                <button class="tag-filter" data-tag="nmbs-sncb-mr96">MW41</button>
                            </div>
                        </div>
                        <div class="collapsible-section nested">
                            <button class="collapsible-toggle nested-toggle" id="ns-toggle">
                                <span class="toggle-text">NS</span>
                                <span class="toggle-icon">▼</span>
                            </button>
                            <div class="collapsible-content nested-content" id="ns-content">
                                <button class="tag-filter" data-tag="ns-ddz">DDZ</button>
                                <button class="tag-filter" data-tag="ns-flirt">Flirt</button>
                                <button class="tag-filter" data-tag="ns-icm">ICM</button>
                                <button class="tag-filter" data-tag="ns-icng">ICNG</button>
                                <button class="tag-filter" data-tag="ns-icng-b">ICNG-B</button>
                                <button class="tag-filter" data-tag="ns-ic-direct">IC Direct</button>
                                <button class="tag-filter" data-tag="ns-slt">SLT</button>
                                <button class="tag-filter" data-tag="ns-sng">SNG</button>
                                <button class="tag-filter" data-tag="ns-virm">VIRM</button>
                            </div>
                        </div>
                        <div class="collapsible-section nested">
                            <button class="collapsible-toggle nested-toggle" id="eurostar-toggle">
                                <span class="toggle-text">Eurostar</span>
                                <span class="toggle-icon">▼</span>
                            </button>
                            <div class="collapsible-content nested-content" id="eurostar-content">
                                <button class="tag-filter" data-tag="eurostar-e300">Eurostar E300</button>
                                <button class="tag-filter" data-tag="eurostar-e320">Eurostar E320</button>
                                <button class="tag-filter" data-tag="eurostar-pba">Eurostar PBA</button>
                                <button class="tag-filter" data-tag="eurostar-pbka">Eurostar PBKA</button>
                                <button class="tag-filter" data-tag="thalys-pba">Thalys PBA</button>
                                <button class="tag-filter" data-tag="thalys-pbka">Thalys PBKA</button>
                            </div>
                        </div>
                        <div class="collapsible-section nested">
                            <button class="collapsible-toggle nested-toggle" id="sncf-toggle">
                                <span class="toggle-text">SNCF</span>
                                <span class="toggle-icon">▼</span>
                            </button>
                            <div class="collapsible-content nested-content" id="sncf-content">
                                <button class="tag-filter" data-tag="sncf-tgv-reseau">TGV Atlantique</button>
                                <button class="tag-filter" data-tag="sncf-tgv-duplex">TGV Duplex</button>
                                <button class="tag-filter" data-tag="sncf-tgv-avelia">TGV M</button>
                                <button class="tag-filter" data-tag="sncf-tgv-pos">TGV POS</button>
                                <button class="tag-filter" data-tag="sncf-tgv-reseau">TGV Reseau</button>
                            </div>
                        </div>
                        <div class="collapsible-section nested">
                            <button class="collapsible-toggle nested-toggle" id="db-toggle">
                                <span class="toggle-text">DB</span>
                                <span class="toggle-icon">▼</span>
                            </button>
                            <div class="collapsible-content nested-content" id="db-content">
                                <button class="tag-filter" data-tag="db-ice">ICE</button>
                                <button class="tag-filter" data-tag="db-ic">IC</button>
                                <button class="tag-filter" data-tag="db-regional">Regional</button>
                            </div>
                        </div>
                        <div class="collapsible-section nested">
                            <button class="collapsible-toggle nested-toggle" id="other-trains-toggle">
                                <span class="toggle-text">Other Trains</span>
                                <span class="toggle-icon">▼</span>
                            </button>
                            <div class="collapsible-content nested-content" id="other-trains-content">
                                <button class="tag-filter" data-tag="other-train">Other</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Categories (collapsed by default) -->
                <div class="collapsible-section">
                    <button class="collapsible-toggle" id="content-categories-toggle">
                        <span class="toggle-text">Content Categories</span>
                        <span class="toggle-icon">▼</span>
                    </button>
                    <div class="collapsible-content" id="content-categories-content">
                        <button class="tag-filter" data-tag="spotting">Spotting</button>
                        <button class="tag-filter" data-tag="departure">Departure</button>
                        <button class="tag-filter" data-tag="arrival">Arrival</button>
                        <button class="tag-filter" data-tag="passing">Passing</button>
                        <button class="tag-filter" data-tag="stationary">Stationary</button>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content area -->
        <div class="main-content">
            <div id="media-grid" class="media-grid">
                <!-- Media items will be loaded here -->
            </div>
        </div>
    </main>

    <!-- Fullscreen Modal -->
    <div id="fullscreen-modal" class="fullscreen-modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="modal-media">
                <img id="modal-image" src="" alt="">
                <iframe id="modal-video" src="" frameborder="0" allowfullscreen></iframe>
            </div>
            <div class="modal-info">
                <h3 id="modal-title"></h3>
                <p id="modal-description"></p>
                <div id="modal-tags"></div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../footers/main_footer.php'; ?>
    <script src="/js/media_library.js"></script>
</body>

</html>
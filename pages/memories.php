<?php
global $db;
require_once '../db/database.php';

$id = $_GET['id'];
$query = 'SELECT * FROM `year_data` WHERE id = $id';

$result = mysqli_query($db, $query)
or die('Error ' . mysqli_error($db) . ' with query ' . $query);

$years = mysqli_fetch_assoc($result);


mysqli_close($db);
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BlossomStep</title>
    <link rel="stylesheet" href="../css/memories.css">
</head>
<body>
<div class="container">
    <header class="header">
        <div class="header-content">
            <svg class="header-icon icon-clock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12,6 12,12 16,14"></polyline>
            </svg>
            <div class="header-text">
                <h1 class="header-title">Time Machine</h1>
                <p class="header-subtitle">Unlock nostalgic memories</p>
            </div>
        </div>
    </header>

    <div class="year-input-card">
        <h2 class="card-title">Enter a Year to Explore</h2>
        <div class="input-row">
            <input
                    type="number"
                    class="year-input"
                    placeholder="e.g., 1995, 2000, 2010..."
                    id="yearInput"
                    min="1990"
                    max="2020"
            >
            <button class="submit-button" onclick="exploreYear()">
                <svg class="icon icon-calendar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </button>
        </div>
    </div>

    <section class="section">
        <h2 class="section-title">Your Memory Collection</h2>

        <!-- 1995 - Unlocked -->
        <div class="memory-card">
            <div class="memory-header">
                <img src="https://images.pexels.com/photos/1029604/pexels-photo-1029604.jpeg?auto=compress&cs=tinysrgb&w=400" alt="1995" class="memory-image">
                <div class="memory-info">
                    <h3 class="memory-year">1995</h3>
                    <div class="unlocked-badge">
                        <svg class="icon icon-unlock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 9.9-1"></path>
                        </svg>
                        <span class="unlocked-text">Unlocked</span>
                    </div>
                </div>
            </div>

            <div class="memory-content">
                <div class="memory-section">
                    <svg class="icon icon-music" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18V5l12-2v13"></path>
                        <circle cx="6" cy="18" r="3"></circle>
                        <circle cx="18" cy="16" r="3"></circle>
                    </svg>
                    <h4 class="memory-section-title">Popular Music</h4>
                </div>
                <p class="memory-item">• Waterfalls - TLC</p>
                <p class="memory-item">• Kiss from a Rose - Seal</p>
                <p class="memory-item">• Fantasy - Mariah Carey</p>

                <div class="memory-section">
                    <svg class="icon icon-film" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                    <h4 class="memory-section-title">Hit Movies</h4>
                </div>
                <p class="memory-item">• Toy Story</p>
                <p class="memory-item">• Apollo 13</p>
                <p class="memory-item">• Babe</p>

                <div class="memory-section">
                    <svg class="icon icon-camera" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                        <circle cx="12" cy="13" r="4"></circle>
                    </svg>
                    <h4 class="memory-section-title">Cultural Moments</h4>
                </div>
                <p class="memory-item">• Windows 95 Launch</p>
                <p class="memory-item">• PlayStation Release</p>
                <p class="memory-item">• Internet becomes mainstream</p>

                <div class="memory-section">
                    <svg class="icon icon-heart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                    <h4 class="memory-section-title">Fashion Trends</h4>
                </div>
                <p class="fashion-text">Grunge and minimalism dominated</p>
            </div>
        </div>

        <!-- 2000 - Unlocked -->
        <div class="memory-card">
            <div class="memory-header">
                <img src="https://images.pexels.com/photos/1083822/pexels-photo-1083822.jpeg?auto=compress&cs=tinysrgb&w=400" alt="2000" class="memory-image">
                <div class="memory-info">
                    <h3 class="memory-year">2000</h3>
                    <div class="unlocked-badge">
                        <svg class="icon icon-unlock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 9.9-1"></path>
                        </svg>
                        <span class="unlocked-text">Unlocked</span>
                    </div>
                </div>
            </div>

            <div class="memory-content">
                <div class="memory-section">
                    <svg class="icon icon-music" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18V5l12-2v13"></path>
                        <circle cx="6" cy="18" r="3"></circle>
                        <circle cx="18" cy="16" r="3"></circle>
                    </svg>
                    <h4 class="memory-section-title">Popular Music</h4>
                </div>
                <p class="memory-item">• Oops!... I Did It Again - Britney Spears</p>
                <p class="memory-item">• Say My Name - Destiny's Child</p>
                <p class="memory-item">• Music - Madonna</p>

                <div class="memory-section">
                    <svg class="icon icon-film" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                    <h4 class="memory-section-title">Hit Movies</h4>
                </div>
                <p class="memory-item">• Gladiator</p>
                <p class="memory-item">• X-Men</p>
                <p class="memory-item">• Almost Famous</p>

                <div class="memory-section">
                    <svg class="icon icon-camera" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                        <circle cx="12" cy="13" r="4"></circle>
                    </svg>
                    <h4 class="memory-section-title">Cultural Moments</h4>
                </div>
                <p class="memory-item">• Y2K Bug fears</p>
                <p class="memory-item">• Dot-com boom</p>
                <p class="memory-item">• Nokia 3310 launch</p>

                <div class="memory-section">
                    <svg class="icon icon-heart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                    <h4 class="memory-section-title">Fashion Trends</h4>
                </div>
                <p class="fashion-text">Low-rise jeans and butterfly clips</p>
            </div>
        </div>

        <!-- 2010 - Locked -->
        <div class="memory-card">
            <div class="locked-memory">
                <h3 class="locked-year">2010</h3>
                <p class="locked-title">🔒 Locked Memory</p>
                <p class="unlock-requirement">Unlock with 50,000 steps and 25 plants</p>
                <div class="progress-info">
                    <p class="progress-text">Steps: 42,400 / 50,000</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 84.8%;"></div>
                    </div>
                    <p class="progress-text">Plants: 18 / 25</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 72%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2015 - Locked -->
        <div class="memory-card">
            <div class="locked-memory">
                <h3 class="locked-year">2015</h3>
                <p class="locked-title">🔒 Locked Memory</p>
                <p class="unlock-requirement">Unlock with 75,000 steps and 40 plants</p>
                <div class="progress-info">
                    <p class="progress-text">Steps: 42,400 / 75,000</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 56.5%;"></div>
                    </div>
                    <p class="progress-text">Plants: 18 / 40</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 45%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
</body>
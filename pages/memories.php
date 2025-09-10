<?php
global $db;
require_once '../db/database.php';

// Alle data ophalen
$query = 'SELECT * FROM `year_data` ORDER BY year ASC';
$result = mysqli_query($db, $query) or die('Error ' . mysqli_error($db) . ' with query ' . $query);

$years = [];
while ($row = mysqli_fetch_assoc($result)) {
    $years[] = $row;
}

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
<!--        <h2 class="card-title">Enter a Year to Explore</h2>-->
        <h2 class="section-title">Your Memory Collection</h2>
    </div>

    <section class="section">


        <?php foreach ($years as $year) { ?>
            <div class="memory-card">
                <div class="memory-header">
                    <!-- Je kan evt. een kolom 'image' in je DB toevoegen voor dynamische foto's -->
                    <img src="https://images.pexels.com/photos/1029604/pexels-photo-1029604.jpeg?auto=compress&cs=tinysrgb&w=400"
                         alt="<?= $year['year'] ?>" class="memory-image">
                    <div class="memory-info">
                        <h3 class="memory-year"><?= $year['year'] ?></h3>
                        <div class="unlocked-badge">
                            <svg class="icon icon-unlock" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 9.9-1"></path>
                            </svg>
                            <span class="unlocked-text">Unlocked</span>
                        </div>
                    </div>
                </div>

                <div class="memory-content">
                    <div class="memory-section">
                        <h4 class="memory-section-title">Popular Music</h4>
                        <p class="memory-item">• <?= $year['music'] ?></p>
                    </div>

                    <div class="memory-section">
                        <h4 class="memory-section-title">Hit Movies</h4>
                        <p class="memory-item">• <?= $year['movies'] ?></p>
                    </div>

                    <div class="memory-section">
                        <h4 class="memory-section-title">Cultural Moments</h4>
                        <p class="memory-item">• <?= $year['events'] ?></p>
                    </div>

                    <div class="memory-section">
                        <h4 class="memory-section-title">Fashion Trends</h4>
                        <p class="fashion-text"><?= $year['fashion'] ?></p>
                    </div>
                </div>
            </div>
        <?php } ?>
    </section>
    <footer>
        <a href="index.html">Steps</a>
        <a href="scan.html">Scan</a>
        <a href="rewards.html">Rewards</a>
        <a href="memories.php">Memories</a>
        <a href="">Profile</a>
    </footer>
</div>
</body>

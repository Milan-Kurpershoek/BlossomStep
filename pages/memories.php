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

// Define point requirements for each memory
$memoryPoints = [
    1990 => 150,
    1995 => 120,
    2000 => 100,
    2005 => 80,
    2010 => 50
];
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BlossomStep</title>
    <link rel="stylesheet" href="../css/memories.css">
    <script src="../js/main.js" defer></script>
    <style>
        .memory-card.locked {
            position: relative;
            opacity: 0.7;
        }

        .locked-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            border-radius: 12px;
            flex-direction: column;
            padding: 20px;
            text-align: center;
            z-index: 10;
        }

        .locked-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .unlocked-badge .icon-lock,
        .unlocked-badge .icon-unlock {
            width: 16px;
            height: 16px;
            margin-right: 5px;
        }

        .points-progress {
            margin-top: 10px;
            width: 100%;
            background-color: #e0e0e0;
            border-radius: 5px;
            height: 8px;
        }

        极        .points-progress-fill {
            height: 100%;
            border-radius: 5px;
            background-color: #4CAF50;
            transition: width 0.3s ease;
        }

        .demo-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px 0;
            display: inline-block;
        }

        .reset-button {
            background-color: #ff4757;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px 0;
            display: inline-block;
        }
    </style>
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
                <p class="header-subtitle">Unlock nostalgic memories with points</p>
            </div>
        </div>
        <div class="points-card">
            <svg class="points-icon" viewBox="0 0 24 24" fill="currentColor">
                <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"></polygon>
            </svg>
            <span class="points-text" id="userPoints">0 points</span>
        </div>
    </header>

    <div class="year-input-card">
        <h2 class="section-title">Your Memory Collection</h2>
        <p>Earn points by walking and scanning plants to unlock memories</p>
        <div class="points-progress">
            <div class="points-progress-fill" id="pointsProgress" style="width: 0%"></div>
        </div>
        <p id="progressText">0% of memories unlocked</p>
        <button class="demo-button" onclick="addDemoPoints()">Demo: +100 Points</button>
        <button class="reset-button" onclick="resetAllProgress()">Reset All Progress</button>
    </div>

    <section class="section">
        <?php foreach ($years as $year) {
            $yearValue = $year['year'];
            $pointsRequired = $memoryPoints[$yearValue] ?? 50;
            ?>
            <div class="memory-card locked" data-year="<?= $yearValue ?>" data-required-points="<?= $pointsRequired ?>">
                <div class="memory-header">
                    <img src="https://images.pexels.com/photos/1029604/pexels-photo-1029604.jpeg?auto=compress&cs=tinysrgb&w=400"
                         alt="<?= $year['year'] ?>" class="memory-image">
                    <div class="memory-info">
                        <h3 class="memory-year"><?= $year['year'] ?></h3>
                        <div class="unlocked-badge">
                            <svg class="icon icon-lock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <span class="unlocked-text"><?= $pointsRequired ?> points needed</span>
                        </div>
                    </div>
                </div>

                <div class="locked-overlay">
                    <div class="locked-icon">🔒</div>
                    <p>Reach <?= $pointsRequired ?> points to unlock this memory</p>
                    <p>Walk more steps or scan plants to earn points!</p>
                </div>

                <div class="memory-content" style="display: none;">
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

<script>
    // JavaScript to check memory unlocks based on points
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize with 0 points and no unlocked memories
        if (!localStorage.getItem('userPoints')) {
            localStorage.setItem('userPoints', '0');
        }
        if (!localStorage.getItem('unlockedMemories')) {
            localStorage.setItem('unlockedMemories', '[]');
        }

        // Load points from localStorage
        const points = parseFloat(localStorage.getItem('userPoints') || 0);
        document.getElementById('userPoints').textContent = Math.floor(points) + ' points';

        // Check each memory card and update its status
        updateMemoryCards();
    });

    function updateMemoryCards() {
        const points = parseFloat(localStorage.getItem('userPoints') || 0);
        const memoryCards = document.querySelectorAll('.memory-card');
        const unlockedMemories = JSON.parse(localStorage.getItem('unlockedMemories') || '[]');

        let unlockedCount = 0;
        let totalMemories = memoryCards.length;

        memoryCards.forEach(card => {
            const year = parseInt(card.getAttribute('data-year'));
            const pointsRequired = parseInt(card.getAttribute('data-required-points'));

            // Check if user has enough points AND if the memory is not already unlocked
            if (points >= pointsRequired) {
                // Memory can be unlocked
                card.classList.remove('locked');
                unlockedCount++;

                const badge = card.querySelector('.unlocked-badge');
                if (badge) {
                    badge.querySelector('.unlocked-text').textContent = 'Unlocked';
                    // Change lock icon to unlock icon
                    const icon = badge.querySelector('.icon');
                    if (icon) {
                        icon.classList.remove('icon-lock');
                        icon.classList.add('icon-unlock');
                        // Update SVG path for unlock icon
                        const path = icon.querySelector('path');
                        if (path) {
                            path.setAttribute('d', 'M7 11V7a5 5 0 0 1 9.9-1');
                        }
                    }
                    // Remove any progress bars
                    const progressBar = badge.querySelector('.points-progress');
                    if (progressBar) {
                        progressBar.remove();
                    }
                }
                // Show memory content
                const lockedOverlay = card.querySelector('.locked-overlay');
                if (lockedOverlay) {
                    lockedOverlay.style.display = 'none';
                }
                const memoryContent = card.querySelector('.memory-content');
                if (memoryContent) {
                    memoryContent.style.display = 'block';
                }

                // Add to unlocked memories if not already there
                if (!unlockedMemories.includes(year)) {
                    unlockedMemories.push(year);
                    localStorage.setItem('unlockedMemories', JSON.stringify(unlockedMemories));
                }
            } else {
                // Memory is locked
                card.classList.add('locked');
                const badge = card.querySelector('.unlocked-badge');
                if (badge) {
                    badge.querySelector('.unlocked-text').textContent = pointsRequired + ' points needed';

                    // Show progress toward unlocking
                    const progressPercent = Math.min(Math.floor((points / pointsRequired) * 100), 100);
                    const existingProgress = badge.querySelector('.points-progress');
                    if (!existingProgress) {
                        badge.innerHTML += `<div class="points-progress">
                        <div class="points-progress-fill" style="width: ${progressPercent}%"></div>
                    </div>`;
                    } else {
                        existingProgress.querySelector('.points-progress-fill').style.width = progressPercent + '%';
                    }
                }
                // Show locked overlay
                const lockedOverlay = card.querySelector('.locked-overlay');
                if (lockedOverlay) {
                    lockedOverlay.style.display = 'flex';
                }
                // Hide memory content
                const memoryContent = card.querySelector('.memory-content');
                if (memoryContent) {
                    memoryContent.style.display = 'none';
                }

                // Remove from unlocked memories if it was previously unlocked
                const index = unlockedMemories.indexOf(year);
                if (index > -1) {
                    unlockedMemories.splice(index, 1);
                    localStorage.setItem('unlockedMemories', JSON.stringify(unlockedMemories));
                }
            }
        });

        // Update progress text
        const progressPercent = Math.floor((unlockedCount / totalMemories) * 100);
        document.getElementById('pointsProgress').style.width = progressPercent + '%';
        document.getElementById('progressText').textContent = `${unlockedCount}/${极totalMemories} memories unlocked (${progressPercent}%)`;
    }

    function addDemoPoints() {
        // Add points for demonstration
        let currentPoints = parseFloat(localStorage.getItem('userPoints') || 0);
        currentPoints += 100;
        localStorage.setItem('userPoints', currentPoints.toString());

        // Update points display
        document.getElementById('userPoints').textContent = Math.floor(currentPoints) + ' points';

        // Update memory cards
        updateMemoryCards();

        alert('Added 100 points for demonstration!');
    }

    function resetAllProgress() {
        if (confirm('Are you sure you want to reset all progress? This will set your points to 0 and lock all memories.')) {
            localStorage.setItem('userPoints', '0');
            localStorage.setItem('unlockedMemories', '[]');
            localStorage.setItem('claimedRewards', '[]');
            localStorage.setItem('stepCount', '0');

            document.getElementById('userPoints').textContent = '0 points';
            updateMemoryCards();
            alert('All progress has been reset!');
        }
    }

    // Listen for storage events to update when points change in other tabs
    window.addEventListener('storage', function(e) {
        if (e.key === 'userPoints') {
            updateMemoryCards();
        }
    });
</script>
</body>
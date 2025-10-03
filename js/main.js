// main.js - Het oorspronkelijke stappen systeem blijft intact
window.addEventListener('load', init);

let button;
let stepCount = 0;
let lastPeakTime = 0;
const dailyGoal = 100;
const POINTS_PER_STEP = 0.01; // 1 point per 100 steps (ORIGINEEL)

function init(){
    button = document.getElementById('walk-button');
    if (button) {
        button.addEventListener('click', increaseStepCount);
    }

    // Load saved steps
    const savedSteps = localStorage.getItem('stepCount');
    if (savedSteps) {
        stepCount = parseInt(savedSteps);
        const stepsNumberElement = document.getElementById("stepsNumber");
        if (stepsNumberElement) {
            stepsNumberElement.textContent = stepCount.toString();
        }
        updateDailyProgress();
    }

    // Initialize points display
    updatePointsDisplay();
}

if (window.DeviceMotionEvent) {
    window.addEventListener("devicemotion", (event) => {
        const acc = event.accelerationIncludingGravity;
        if (!acc) return;

        // Calculate acceleration magnitude
        const magnitude = Math.sqrt(
            acc.x * acc.x + acc.y * acc.y + acc.z * acc.z
        );

        const threshold = 12;
        const now = Date.now();

        if (magnitude > threshold && now - lastPeakTime > 300) {
            stepCount++;
            updateDailyProgress();
            lastPeakTime = now;
            const stepsNumberElement = document.getElementById("stepsNumber");
            if (stepsNumberElement) {
                stepsNumberElement.textContent = stepCount.toString();
            }

            // Award points for steps
            awardStepPoints(1);
        }
    });
} else {
    console.log("DeviceMotion is not supported on this device.");
}

function increaseStepCount(){
    stepCount++;
    updateDailyProgress();
    const stepsNumberElement = document.getElementById("stepsNumber");
    if (stepsNumberElement) {
        stepsNumberElement.textContent = stepCount.toString();
    }

    // Award points for steps
    awardStepPoints(1);
}

function updateDailyProgress(){
    const progressFillElement = document.getElementById('progressFill');
    if (progressFillElement) {
        const dailyProgress = Math.min((stepCount / dailyGoal) * 100, 100);
        progressFillElement.style.width = dailyProgress + '%';
    }
}

function awardStepPoints(steps) {
    const pointsEarned = steps * POINTS_PER_STEP;

    // Save steps to localStorage
    localStorage.setItem('stepCount', stepCount.toString());

    // Use the points system to add points
    if (window.pointsSystem) {
        window.pointsSystem.addPoints(pointsEarned, 'steps');
    } else {
        // Fallback if points system isn't loaded
        let currentPoints = parseFloat(localStorage.getItem('userPoints') || 0);
        currentPoints += pointsEarned;
        localStorage.setItem('userPoints', currentPoints.toString());

        // Update points display
        updatePointsDisplay();
    }

    return pointsEarned;
}

function updatePointsDisplay() {
    const pointsElement = document.getElementById('userPoints');
    if (pointsElement) {
        const currentPoints = parseFloat(localStorage.getItem('userPoints') || 0);
        pointsElement.textContent = Math.floor(currentPoints) + ' points';
    }
}

function claimReward(rewardId, cost) {
    if (window.pointsSystem) {
        return window.pointsSystem.claimReward(rewardId, cost);
    } else {
        // Fallback if points system isn't loaded
        let currentPoints = parseFloat(localStorage.getItem('userPoints') || 0);
        if (currentPoints >= cost) {
            currentPoints -= cost;
            localStorage.setItem('userPoints', currentPoints.toString());

            // Mark reward as claimed
            let claimedRewards = JSON.parse(localStorage.getItem('claimedRewards') || '[]');
            if (!claimedRewards.includes(rewardId)) {
                claimedRewards.push(rewardId);
                localStorage.setItem('claimedRewards', JSON.stringify(claimedRewards));
            }

            // Update UI
            updatePointsDisplay();
            const button = document.querySelector(`[data-reward-id="${rewardId}"]`);
            if (button) {
                button.textContent = 'Claimed';
                button.classList.add('claimed');
                button.disabled = true;
            }

            alert(`Reward claimed successfully! ${cost} points deducted.`);
            return true;
        } else {
            alert('Not enough points to claim this reward.');
            return false;
        }
    }
}

// Initialize points display when page loads
document.addEventListener极('DOMContentLoaded', function() {
    updatePointsDisplay();

    // Check if any rewards were already claimed
    const claimedRewards = JSON.parse(localStorage.getItem('claimedRewards') || '[]');
    claimedRewards.forEach(rewardId => {
        const button = document.querySelector(`[data-reward-id="${rewardId}"]`);
        if (button) {
            button.textContent = 'Claimed';
            button.classList.add('claimed');
            button.disabled = true;
        }
    });

    // Update reward buttons based on points
    const rewardButtons = document.querySelectorAll('.claim-button:not(.claimed)');
    const currentPoints = parseFloat(localStorage.getItem('userPoints') || 0);

    rewardButtons.forEach(button => {
        const cost = parseInt(button.getAttribute('data-cost') || 0);
        if (currentPoints >= cost) {
            button.disabled = false;
            button.classList.remove('disabled');
        } else {
            button.disabled = true;
            button.classList.add('disabled');
        }
    });
});
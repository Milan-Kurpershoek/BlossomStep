// points-system.js
class PointsSystem {
    constructor() {
        this.points = 0;
        this.claimedRewards = [];
        this.unlockedMemories = [];
        this.loadData();
    }

    loadData() {
        // Load points
        const savedPoints = localStorage.getItem('userPoints');
        if (savedPoints) {
            this.points = parseFloat(savedPoints);
        }

        // Load claimed rewards
        const savedRewards = localStorage.getItem('claimedRewards');
        if (savedRewards) {
            this.claimedRewards = JSON.parse(savedRewards);
        }

        // Load unlocked memories
        const savedMemories = localStorage.getItem('unlockedMemories');
        if (savedMemories) {
            this.unlockedMemories = JSON.parse(savedMemories);
        }
    }

    saveData() {
        localStorage.setItem('userPoints', this.points.toString());
        localStorage.setItem('claimedRewards', JSON.stringify(this.claimedRewards));
        localStorage.setItem('unlockedMemories', JSON.stringify(this.unlockedMemories));
    }

    addPoints(amount, source = 'unknown') {
        this.points += amount;
        this.saveData();
        this.updateAllDisplays();
        this.checkMemoryUnlocks();
        return amount;
    }

    deductPoints(amount) {
        if (this.points >= amount) {
            this.points -= amount;
            this.saveData();
            this.updateAllDisplays();
            return true;
        }
        return false;
    }

    updateAllDisplays() {
        // Update points display in all pages
        const pointsElements = document.querySelectorAll('.points-text, #userPoints');
        pointsElements.forEach(element => {
            element.textContent = Math.floor(this.points) + ' points';
        });

        // Update reward buttons
        this.updateRewardButtons();
    }

    updateRewardButtons() {
        const rewardButtons = document.querySelectorAll('.claim-button');
        rewardButtons.forEach(button => {
            const pointsCost = parseInt(button.getAttribute('data-cost') || 0);
            const rewardId = button.getAttribute('data-reward-id');

            if (this.claimedRewards.includes(rewardId)) {
                button.textContent = 'Claimed';
                button.classList.add('claimed');
                button.disabled = true;
            } else if (this.points >= pointsCost) {
                button.disabled = false;
                button.classList.remove('disabled');
                button.textContent = 'Claim';
            } else {
                button.disabled = true;
                button.classList.add('disabled');
                button.textContent = 'Need more points';
            }
        });
    }

    claimReward(rewardId, cost) {
        if (this.deductPoints(cost)) {
            this.claimedRewards.push(rewardId);
            this.saveData();
            this.updateRewardButtons();
            alert(`Reward claimed successfully! ${cost} points deducted.`);
            return true;
        } else {
            alert('Not enough points to claim this reward.');
            return false;
        }
    }

    checkMemoryUnlocks() {
        // Define point thresholds for memory unlocks
        const memoryThresholds = {
            10: [2010],
            25: [2005],
            50: [2000],
            75: [1995],
            100: [1990]
        };

        // Check if points reach any threshold
        let newUnlocks = [];
        for (const [threshold, memories] of Object.entries(memoryThresholds)) {
            if (this.points >= parseInt(threshold)) {
                memories.forEach(memory => {
                    if (!this.unlockedMemories.includes(memory)) {
                        this.unlockedMemories.push(memory);
                        newUnlocks.push(memory);
                    }
                });
            }
        }

        if (newUnlocks.length > 0) {
            this.saveData();
            console.log(`Unlocked new memories: ${newUnlocks.join(', ')}`);

            // Show notification if on memories page
            if (window.location.pathname.includes('memories')) {
                alert(`Congratulations! You've unlocked ${newUnlocks.length} new memories!`);
            }
        }
    }

    getPoints() {
        return this.points;
    }

    getClaimedRewards() {
        return this.claimedRewards;
    }

    getUnlockedMemories() {
        return this.unlockedMemories;
    }

    isMemoryUnlocked(memoryId) {
        return this.unlockedMemories.includes(memoryId);
    }
}

// Initialize the points system
const pointsSystem = new PointsSystem();

// Make it available globally
window.pointsSystem = pointsSystem;
window.claimReward = function(rewardId, cost) {
    return pointsSystem.claimReward(rewardId, cost);
};

// Initialize points display when page loads
document.addEventListener('DOMContentLoaded', function() {
    pointsSystem.updateAllDisplays();
});
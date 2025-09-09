window.addEventListener('load', init);

let button
let stepCount = 0;
let lastPeakTime = 0;
const dailyGoal = 10000;

function init(){
button = document.getElementById('walk-button')
button.addEventListener('click', increaseStepCount)
}

if (window.DeviceMotionEvent) {
    window.addEventListener("devicemotion", (event) => {
        const acc = event.accelerationIncludingGravity;
        if (!acc) return;

        // Calculate acceleration magnitude
        const magnitude = Math.sqrt(
            acc.x * acc.x + acc.y * acc.y + acc.z * acc.z
        );

        const threshold = 12; // adjust if too sensitive or not sensitive enough
        const now = Date.now();

        if (magnitude > threshold && now - lastPeakTime > 300) {
            stepCount++;
            updateDailyProgress()
            lastPeakTime = now;
            document.getElementById("stepsNumber").textContent = stepCount.toString();
        }
    });
} else {
    alert("DeviceMotion is not supported on this device.");
}

function updateDailyProgress(){
 const dailyProgress = Math.min((stepCount / dailyGoal) * 100, 100)

    document.getElementById('progressFill').style.width = dailyProgress + '%';
}

function increaseStepCount(){
    stepCount++;
    updateDailyProgress()
    console.log(stepCount)
    document.getElementById("stepsNumber").textContent = stepCount.toString();
}

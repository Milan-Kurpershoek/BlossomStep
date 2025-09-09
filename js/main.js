let stepCount = 0;
let lastPeakTime = 0;

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
            lastPeakTime = now;
            document.getElementById("stepsNumber").textContent = stepCount.toString();
        }
    });
} else {
    alert("DeviceMotion is not supported on this device.");
}
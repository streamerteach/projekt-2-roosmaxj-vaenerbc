const output = document.getElementById("countdown");
let intervalId = null;

// Delete old time
if (intervalId) {
    clearInterval(intervalId);
}

if (!window.targetTimestampSeconds) {
    output.innerText = "No active countdown.";
} else {
    const targetMs = window.targetTimestampSeconds * 1000;

    function updateCountdown() {
        const now = Date.now();
        let diff = targetMs - now;

        if (diff <= 0) {
            output.innerText = "⏰ Let's se that amazing build!";
            clearInterval(intervalId);
            return;
        }

        const totalSeconds = Math.floor(diff / 1000);
        const days = Math.floor(totalSeconds / 86400);
        const hours = Math.floor((totalSeconds % 86400) / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        output.innerText =
            `${days} days ${hours} hours ${minutes} minutes ${seconds} seconds to your next build`;
    }

    updateCountdown();
    intervalId = setInterval(updateCountdown, 1000);
}

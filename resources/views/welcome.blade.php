<!DOCTYPE html>
<html>
<head>
    <title>GitHub Merge</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: Arial;
            padding: 40px;
        }

        input {
            width: 400px;
            padding: 10px;
        }

        button {
            padding: 10px 20px;
            margin-left: 10px;
        }

        .profile {
            margin-top: 20px;
        }

        .profile img {
            width: 50px;
            border-radius: 50%;
        }
    </style>
</head>
<body>

<h2>GitHub Contribution Merger</h2>

<input type="text" id="usernames" placeholder="username1, username2">
<button onclick="merge()">Submit</button>

<div id="profiles"></div>

<canvas id="chart" height="100"></canvas>

<script>
async function merge() {
    const usernames = document.getElementById('usernames').value;

    const res = await fetch('/merge', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ usernames })
    });

    const data = await res.json();

    showProfiles(data.profiles);
    showChart(data.merged);
}

/**
 * Show profiles
 */
function showProfiles(profiles) {
    let html = '';

    profiles.forEach(p => {
        html += `
            <div class="profile">
                <img src="${p.avatar}">
                <a href="${p.url}" target="_blank">${p.username}</a>
            </div>
        `;
    });

    document.getElementById('profiles').innerHTML = html;
}

/**
 * Show merged contributions (chart)
 */
let chartInstance = null;

function showChart(merged) {

    // Convert object → array
    const entries = Object.entries(merged);

    // Sort by date (VERY IMPORTANT)
    entries.sort((a, b) => new Date(a[0]) - new Date(b[0]));

    // Take last 30 days
    const last = entries.slice(-30);

    const labels = last.map(e => e[0]);
    const values = last.map(e => e[1]);

    const ctx = document.getElementById('chart').getContext('2d');

    // Destroy old chart (IMPORTANT)
    if (chartInstance) {
        chartInstance.destroy();
    }

    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Merged Contributions',
                data: values,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}
</script>

</body>
</html>
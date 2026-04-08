<!DOCTYPE html>
<html>
<head>
    <title>GitHub Contribution Merger</title>

    <style>
        body {
            font-family: Arial;
            background: #f6f8fa;
            padding: 30px;
        }

        input {
            padding: 10px;
            width: 400px;
        }

        button {
            padding: 10px 20px;
        }

        .card {
            background: #fff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
            border: 1px solid #d0d7de;
            width: fit-content;
        }

        /* Layout */
        .contribution-wrapper {
            display: flex;
        }

        /* Weekday labels */
        .days {
            display: grid;
            grid-template-rows: repeat(7, 11px);
            gap: 3px;
            margin-right: 8px;
            font-size: 12px;
            color: #57606a;
        }

        /* Heatmap */
        .heatmap {
            display: grid;
            grid-auto-flow: column;
            grid-template-rows: repeat(7, 11px);
            gap: 3px;
        }

        .cell {
            width: 11px;
            height: 11px;
            background: #ebedf0;
            border-radius: 2px;
        }

        .level-1 { background: #9be9a8; }
        .level-2 { background: #40c463; }
        .level-3 { background: #30a14e; }
        .level-4 { background: #216e39; }

        /* Months */
        .months {
            display: grid;
            grid-auto-flow: column;
            grid-template-columns: repeat(53, auto);
            font-size: 12px;
            color: #57606a;
            margin-left: 30px;
            margin-bottom: 5px;
        }

        .legend {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #57606a;
        }

        .legend-box {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .legend-box div {
            width: 11px;
            height: 11px;
        }

    </style>
</head>
<body>

<h2>GitHub Contribution Merger</h2>

<input type="text" id="usernames" placeholder="username1, username2">
<button onclick="merge()">Submit</button>

<div class="card">

    <h3 id="totalText">0 contributions in the last year</h3>

    <!-- Month labels -->
    <div class="months" id="months"></div>

    <div class="contribution-wrapper">

        <!-- Day labels -->
        <div class="days">
            <span></span>
            <span>Mon</span>
            <span></span>
            <span>Wed</span>
            <span></span>
            <span>Fri</span>
            <span></span>
        </div>

        <!-- Heatmap -->
        <div id="heatmap" class="heatmap"></div>

    </div>

    <!-- Legend -->
    <div class="legend">
        <span>Learn how we count contributions</span>

        <div class="legend-box">
            <span>Less</span>
            <div style="background:#ebedf0"></div>
            <div style="background:#9be9a8"></div>
            <div style="background:#40c463"></div>
            <div style="background:#30a14e"></div>
            <div style="background:#216e39"></div>
            <span>More</span>
        </div>
    </div>

</div>

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

    showHeatmap(data.merged);
}

/**
 * Generate Month Labels
 */
function generateMonths(dates) {
    const monthsContainer = document.getElementById('months');
    monthsContainer.innerHTML = '';

    const monthNames = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

    let lastMonth = -1;

    dates.forEach(date => {
        const d = new Date(date);
        const month = d.getMonth();

        if (month !== lastMonth) {
            const div = document.createElement('div');
            div.textContent = monthNames[month];
            monthsContainer.appendChild(div);
            lastMonth = month;
        } else {
            const div = document.createElement('div');
            div.textContent = '';
            monthsContainer.appendChild(div);
        }
    });
}

/**
 * Heatmap render
 */
function showHeatmap(merged) {

    const container = document.getElementById('heatmap');

    const entries = Object.entries(merged)
        .sort((a, b) => new Date(a[0]) - new Date(b[0]))
        .slice(-371);

    let total = 0;

    // Generate months
    generateMonths(entries.map(e => e[0]));

    // Fill missing days (IMPORTANT for alignment)
    const full = [];
    let start = new Date(entries[0][0]);
    let end = new Date(entries[entries.length - 1][0]);

    let map = Object.fromEntries(entries);

    while (start <= end) {
        let dateStr = start.toISOString().split('T')[0];
        full.push([dateStr, map[dateStr] || 0]);
        start.setDate(start.getDate() + 1);
    }

    let html = '';

    full.forEach(([date, count]) => {

        total += count;

        let level = 0;
        if (count > 0) level = 1;
        if (count > 5) level = 2;
        if (count > 10) level = 3;
        if (count > 20) level = 4;

        html += `<div class="cell level-${level}" title="${date}: ${count}"></div>`;
    });

    container.innerHTML = html;

    document.getElementById('totalText').innerText =
        total + ' contributions in the last year';
}
</script>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>GitHub Contribution Merger</title>

    <style>
        body {
            font-family: Arial;
            background: #f6f8fa;
            padding: 30px;
            width: 1216px;
            margin: auto;
        }

        input {
            padding: 10px;
            width: 400px;
        }

        button {
            padding: 10px 20px;
        }

        .contribution {
            display: flex;
            margin-top: 20px;
        }

        .profile {
            width: 300px;
            min-height: auto;
            border-radius: 8px;
            margin-right: 20px;
            border: 1px solid #d0d7de;
        }

        .profile a:hover {
            text-decoration: underline;
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

        #repository {
            margin-top: 30px;
        }
        .repos {
            margin-top: 20px;
            display: grid;
            gap: 10px;
        }

    </style>
</head>
<body>

    <h2>GitHub Contribution Merger</h2>

    <div class="search">
        <input type="text" id="usernames" placeholder="username1, username2" value="saidul-dev, saidul1996, saidulerpseopage1">
        <button onclick="merge()">Submit</button>

        <select id="yearFilter" onchange="applyYearFilter()" style="padding:10px; margin-left:10px; display:none;">
        </select>
    </div>

    <div class="contribution">
        <div class="profile" id="profile">
            <div id="profiles"></div>
        </div>
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
            <h3 id="repository">Popular Repositories</h3>
            <div class="repos" id="repos">
            </div>
        </div>
    </div>

    <script>
        let fullMergedData = {};
        let selectedYear = "last";

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

            fullMergedData = data.merged;

            showProfiles(data.profiles);
            showRepos(data.repos);

            generateYearFilter(data.yearRange.min, data.yearRange.max);

            applyYearFilter(); // default load
        }

        function generateYearFilter(minYear, maxYear) {

            const yearSelect = document.getElementById("yearFilter");
            yearSelect.innerHTML = "";

            // Default option
            yearSelect.innerHTML += `<option value="last">Last Year</option>`;

            for (let y = minYear; y <= maxYear; y++) {
                yearSelect.innerHTML += `<option value="${y}">${y}</option>`;
            }

            yearSelect.style.display = "inline-block";
        }

        function applyYearFilter() {

            const yearSelect = document.getElementById("yearFilter");
            selectedYear = yearSelect.value;

            if (selectedYear === "last") {
                showHeatmap(fullMergedData);
                return;
            }

            const filtered = {};

            Object.entries(fullMergedData).forEach(([date, count]) => {
                const d = new Date(date);
                const year = d.getFullYear();

                if (year == selectedYear) {
                    filtered[date] = count;
                }
            });

            showHeatmapYear(filtered, selectedYear);
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

            let today = new Date();
            let startDate = new Date();
            startDate.setDate(today.getDate() - 365);

            function formatDate(d) {
                return d.toISOString().split("T")[0];
            }

            const map = merged;

            const full = [];
            let cursor = new Date(startDate);

            while (cursor <= today) {
                let dateStr = formatDate(cursor);
                full.push([dateStr, map[dateStr] || 0]);
                cursor.setDate(cursor.getDate() + 1);
            }

            // Generate months based on full range
            generateMonths(full.map(e => e[0]));

            let total = 0;
            let html = '';

            // ✅ FIX: add empty blocks before first day to align weekday
            let firstDay = new Date(full[0][0]).getDay(); 
            // getDay(): Sun=0, Mon=1, ... Sat=6

            for (let i = 0; i < firstDay; i++) {
                html += `<div class="cell" style="background:transparent;"></div>`;
            }

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

        function showHeatmapYear(merged, year) {

            const container = document.getElementById('heatmap');

            const entries = Object.entries(merged)
                .sort((a, b) => new Date(a[0]) - new Date(b[0]));

            const full = [];
            let start = new Date(year + "-01-01");
            let end = new Date(year + "-12-31");

            let map = Object.fromEntries(entries);

            while (start <= end) {
                let dateStr = start.toISOString().split('T')[0];
                full.push([dateStr, map[dateStr] || 0]);
                start.setDate(start.getDate() + 1);
            }

            generateMonths(full.map(e => e[0]));

            let total = 0;
            let html = '';

            // ✅ FIX alignment
            let firstDay = new Date(full[0][0]).getDay();

            for (let i = 0; i < firstDay; i++) {
                html += `<div class="cell" style="background:transparent;"></div>`;
            }

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
                total + ' contributions in ' + year;
        }

        function showProfiles(profiles) {
            const container = document.getElementById('profiles');

            container.innerHTML = profiles.map(p => `
                <div style="text-align:center; padding:15px;">
                    
                    <img src="${p.avatar}" 
                        style="width:120px; border-radius:50%; margin-bottom:10px;">

                    <h5>${p.name || p.username}</h5>

                    <p style="color:#57606a;">@${p.username}</p>

                    <!-- 🔗 PROFILE LINK -->
                    <a href="${p.url}" target="_blank" 
                    style="text-decoration:none; color:#0969da; font-weight:500;">
                    View GitHub Profile →
                    </a>

                </div>
            `).join('');
        }

        function showRepos(repos) {
            const container = document.getElementById('repos');

            container.innerHTML = repos.map(r => `
                <div style="border:1px solid #d0d7de; padding:10px; border-radius:6px; background:#fff;">
                    
                    <a href="${r.url}" target="_blank" 
                    style="font-weight:600; color:#0969da; text-decoration:none;">
                    ${r.name}
                    </a>

                    <div style="font-size:12px; color:#57606a; margin-top:5px;">
                        ${r.language || 'Unknown'} • ⭐ ${r.stars}
                    </div>

                </div>
            `).join('');
        }
    </script>

</body>
</html>
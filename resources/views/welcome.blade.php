<!DOCTYPE html>
<html>
<head>
    <title>GitHub Contribution Merger</title>

    <style>
        :root {
            --bg: #f6f8fa;
            --card: #ffffff;
            --border: #d0d7de;
            --text: #24292f;
            --muted: #57606a;
            --primary: #0969da;
            --primary-hover: #0550ae;
            --shadow: 0 8px 20px rgba(27, 31, 36, 0.08);
            --radius: 12px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: var(--bg);
            padding: 30px;
            max-width: 1216px;
            margin: auto;
            color: var(--text);
        }

        h2 {
            font-size: 26px;
            margin-bottom: 20px;
        }

        /* HEADER SEARCH BAR */
        .search {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            background: var(--card);
            border: 1px solid var(--border);
            padding: 16px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .search input {
            padding: 12px 14px;
            width: 450px;
            max-width: 100%;
            border: 1px solid var(--border);
            border-radius: 10px;
            outline: none;
            font-size: 14px;
            transition: 0.2s ease;
            background: #fff;
        }

        .search input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(9, 105, 218, 0.15);
        }

        .search button {
            padding: 12px 18px;
            border: none;
            background: var(--primary);
            color: #fff;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s ease;
            font-size: 14px;
        }

        .search button:hover {
            background: var(--primary-hover);
        }

        .search select {
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            cursor: pointer;
            background: #fff;
            transition: 0.2s ease;
        }

        .search select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(9, 105, 218, 0.15);
        }

        #yearFilter {
            margin-left: auto;
        }

        /* Skeleton Loader */
        .skeleton-wrapper {
            display: flex;
            gap: 20px;
            width: 100%;
        }

        .skeleton-profile {
            width: 320px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--card);
            box-shadow: var(--shadow);
            padding: 14px;
        }

        .skeleton-main {
            flex: 1;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--card);
            box-shadow: var(--shadow);
            padding: 20px;
        }

        .sk-header {
            height: 45px;
            border-radius: 10px;
            margin-bottom: 14px;
            background: #e6ebf1;
        }

        .sk-profile-card {
            display: flex;
            gap: 12px;
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 12px;
            border: 1px solid var(--border);
            background: #fff;
        }

        .sk-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #e6ebf1;
        }

        .sk-lines {
            flex: 1;
        }

        .sk-line {
            height: 10px;
            border-radius: 6px;
            background: #e6ebf1;
            margin-bottom: 10px;
        }

        .w-80 { width: 80%; }
        .w-70 { width: 70%; }
        .w-60 { width: 60%; }

        .sk-ad {
            height: 200px;
            border-radius: 14px;
            background: #e6ebf1;
            margin-top: 15px;
        }

        .sk-title {
            height: 22px;
            width: 50%;
            border-radius: 8px;
            background: #e6ebf1;
            margin-bottom: 15px;
        }

        .sk-heatmap {
            height: 160px;
            border-radius: 12px;
            background: #e6ebf1;
            margin-bottom: 20px;
        }

        .sk-repo-title {
            height: 18px;
            width: 40%;
            border-radius: 8px;
            background: #e6ebf1;
            margin-bottom: 12px;
        }

        .sk-repo-card {
            height: 70px;
            border-radius: 12px;
            background: #e6ebf1;
            margin-bottom: 12px;
        }

        /* Skeleton Animation */
        .skeleton-wrapper div {
            position: relative;
            overflow: hidden;
        }

        .skeleton-wrapper div::after {
            content: "";
            position: absolute;
            top: 0;
            left: -150px;
            width: 150px;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.7), transparent);
            animation: shimmer 1.2s infinite;
        }

        @keyframes shimmer {
            0% { left: -150px; }
            100% { left: 100%; }
        }

        /* MAIN LAYOUT */
        .contribution {
            display: flex;
            gap: 20px;
            margin-top: 25px;
            align-items: flex-start;
        }

        /* PROFILE SIDEBAR */
        .profile {
            width: 320px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--card);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .profile-header {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(90deg, rgba(9, 105, 218, 0.08), rgba(46, 160, 67, 0.08));
        }

        .profile-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
        }

        #profiles {
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .profile-card {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px;
            display: flex;
            gap: 12px;
            align-items: center;
            background: #fff;
            transition: 0.2s ease;
        }

        .profile-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(27, 31, 36, 0.08);
        }

        .profile-card img {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(27, 31, 36, 0.15);
        }

        .profile-info {
            flex: 1;
            min-width: 0;
        }

        .profile-info h5 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-info p {
            margin: 4px 0 8px 0;
            font-size: 12px;
            color: var(--muted);
        }

        .profile-info a {
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
        }

        .profile-info a:hover {
            text-decoration: underline;
        }

        /* CONTRIBUTION CARD */
        .card {
            background: var(--card);
            padding: 20px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            flex: 1;
        }

        .card h3 {
            margin-top: 0;
            font-size: 18px;
        }

        #totalText {
            font-size: 18px;
            margin: 0 0 15px 0;
        }

        /* Layout */
        .contribution-wrapper {
            display: flex;
            align-items: flex-start;
        }

        /* Weekday labels */
        .days {
            display: grid;
            grid-template-rows: repeat(7, 11px);
            gap: 3px;
            margin-right: 8px;
            font-size: 12px;
            color: var(--muted);
            margin-top: 18px;
        }

        /* Heatmap */
        .heatmap {
            display: grid;
            grid-auto-flow: column;
            grid-template-rows: repeat(7, 11px);
            gap: 3px;
            padding: 18px 0;
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
            color: var(--muted);
            margin-left: 30px;
            margin-bottom: 6px;
            margin-top: 8px;
        }

        /* Legend */
        .legend {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: var(--muted);
            border-top: 1px solid var(--border);
            padding-top: 12px;
            gap: 15px;
            flex-wrap: wrap;
        }

        .legend span:first-child {
            font-weight: 600;
        }

        .legend-box {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .legend-box div {
            width: 11px;
            height: 11px;
            border-radius: 2px;
            border: 1px solid rgba(27, 31, 36, 0.05);
        }

        /* REPOSITORIES */
        #repository {
            margin-top: 25px;
            font-size: 16px;
            font-weight: 700;
        }

        .repos {
            margin-top: 12px;
            display: grid;
            gap: 12px;
        }

        .repo-card {
            border: 1px solid var(--border);
            padding: 14px;
            border-radius: 12px;
            background: #fff;
            transition: 0.2s ease;
        }

        .repo-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(27, 31, 36, 0.08);
        }

        .repo-card a {
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
        }

        .repo-card a:hover {
            text-decoration: underline;
        }

        .repo-meta {
            font-size: 12px;
            color: var(--muted);
            margin-top: 6px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .badge {
            background: rgba(9, 105, 218, 0.08);
            border: 1px solid rgba(9, 105, 218, 0.2);
            color: var(--primary);
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Sidebar Advertisement */
        .sidebar-ad {
            margin: 14px;
            padding: 16px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: linear-gradient(135deg, rgba(9, 105, 218, 0.08), rgba(46, 160, 67, 0.06));
            box-shadow: 0 6px 14px rgba(27, 31, 36, 0.06);
        }

        .sidebar-ad-header {
            margin-bottom: 10px;
        }

        .sidebar-ad-header h4 {
            margin: 6px 0 0 0;
            font-size: 15px;
            font-weight: 800;
            color: var(--text);
        }

        .sidebar-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(46, 160, 67, 0.14);
            border: 1px solid rgba(46, 160, 67, 0.25);
            color: #1f883d;
        }

        .sidebar-ad-text {
            font-size: 12.5px;
            line-height: 1.6;
            color: var(--muted);
            margin: 0 0 12px 0;
        }

        .sidebar-ad-skills {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 14px;
        }

        .sidebar-ad-skills span {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(9, 105, 218, 0.08);
            border: 1px solid rgba(9, 105, 218, 0.18);
            color: var(--primary);
        }

        .sidebar-ad-btn {
            display: block;
            text-align: center;
            padding: 10px 12px;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
            transition: 0.2s ease;
            margin-bottom: 10px;
        }

        .sidebar-ad-btn:hover {
            background: var(--primary-hover);
        }

        .sidebar-ad-btn-outline {
            display: block;
            text-align: center;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(9, 105, 218, 0.35);
            background: rgba(255, 255, 255, 0.6);
            color: var(--primary);
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
            transition: 0.2s ease;
        }

        .sidebar-ad-btn-outline:hover {
            background: rgba(9, 105, 218, 0.08);
            border-color: var(--primary);
        }

        /* RESPONSIVE */
        @media (max-width: 1050px) {
            body {
                width: 100%;
                padding: 15px;
            }

            .contribution {
                flex-direction: column;
            }

            .profile {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <h2 style="margin-bottom:6px;">GitFusion — GitHub Contribution Dashboard</h2>

    <p style="margin-top:0; color:#57606a; font-size:14px; line-height:1.6;">
        Add one or more GitHub usernames to combine their contribution history into a single heatmap.  
        You can filter by year and view popular repositories instantly.  
        <strong>Please wait a few seconds after clicking Submit</strong> as it may take time to fetch real data from GitHub.
    </p>

    <div class="search">
        <input type="text" id="usernames" value="{{ $savedUsernames ?? '' }}" placeholder="username1, username2">
        <button onclick="merge()">Submit</button>
        <button onclick="openSaveModal()" id="saveBtn" style="display:none; background:#1f883d;">
            Save & Generate URL
        </button>
        <button onclick="copyCurrentUrl()" id="copyUrlBtn" style="display:none; background:#8250df;">
            Copy URL
        </button>
        <select id="yearFilter" onchange="applyYearFilter()" style="display:none;">
        </select>
    </div>

    <div class="contribution">

        <!-- Skeleton Loader -->
        <div id="skeletonLoader" class="skeleton-wrapper" style="display:none;">
            <div class="skeleton-profile">
                <div class="sk-header"></div>

                <div class="sk-profile-card">
                    <div class="sk-avatar"></div>
                    <div class="sk-lines">
                        <div class="sk-line w-80"></div>
                        <div class="sk-line w-60"></div>
                        <div class="sk-line w-70"></div>
                    </div>
                </div>

                <div class="sk-profile-card">
                    <div class="sk-avatar"></div>
                    <div class="sk-lines">
                        <div class="sk-line w-80"></div>
                        <div class="sk-line w-60"></div>
                        <div class="sk-line w-70"></div>
                    </div>
                </div>

                <div class="sk-ad"></div>
            </div>

            <div class="skeleton-main">
                <div class="sk-title"></div>
                <div class="sk-heatmap"></div>
                <div class="sk-repo-title"></div>

                <div class="sk-repo-card"></div>
                <div class="sk-repo-card"></div>
                <div class="sk-repo-card"></div>
            </div>
        </div>

        <!-- REAL CONTENT -->
        <div class="profile" id="profile">
            <div class="profile-header">
                <h3>Profiles</h3>
            </div>
            <div id="profiles"></div>

            <!-- Advertisement Card -->
            <div class="sidebar-ad">
                <div class="sidebar-ad-header">
                    <span class="sidebar-badge">Hire Me</span>
                    <h4>Need a Web Developer?</h4>
                </div>

                <p class="sidebar-ad-text">
                    I build secure, scalable web applications with proper server deployment and optimization.
                    If you need hosting setup, VPS configuration, or Laravel/React development — feel free to contact me.
                </p>

                <div class="sidebar-ad-skills">
                    <span>Laravel</span>
                    <span>React</span>
                    <span>Node.js</span>
                    <span>Server Setup</span>
                </div>

                <a href="https://saidul.software.uttarainfotech.com/" target="_blank" class="sidebar-ad-btn">
                    View Portfolio →
                </a>

                <a href="https://saidul.software.uttarainfotech.com/contact" target="_blank" class="sidebar-ad-btn-outline">
                    Hire / Contact
                </a>
            </div>
        </div>

        <div class="card">
            <h3 id="totalText">0 contributions in the last year</h3>

            <div class="months" id="months"></div>

            <div class="contribution-wrapper">
                <div class="days">
                    <span></span>
                    <span>Mon</span>
                    <span></span>
                    <span>Wed</span>
                    <span></span>
                    <span>Fri</span>
                    <span></span>
                </div>

                <div id="heatmap" class="heatmap"></div>
            </div>

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
            <div class="repos" id="repos"></div>
        </div>
    </div>

    <div id="saveModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); align-items:center; justify-content:center; z-index:9999;">
        <div style="background:#fff; padding:22px; border-radius:14px; width:460px; max-width:92%; box-shadow:0 12px 30px rgba(0,0,0,0.15);">

            <!-- Header -->
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <div>
                    <h3 style="margin:0; font-size:18px; font-weight:800; color:#24292f;">Save Dashboard</h3>
                    <p style="margin:4px 0 0 0; font-size:13px; color:#57606a;">
                        Generate a public shareable URL for your merged GitHub contribution dashboard.
                    </p>
                </div>

                <button onclick="closeSaveModal()"
                    style="border:none; background:transparent; font-size:18px; cursor:pointer; color:#57606a;">
                    ✕
                </button>
            </div>

            <!-- Info Box -->
            <div style="background:#f6f8fa; border:1px solid #d0d7de; border-radius:12px; padding:12px 14px; margin-bottom:14px;">
                <p style="margin:0; font-size:13px; color:#24292f; line-height:1.5;">
                    Your dashboard will be available at:
                </p>

                <div style="margin-top:8px; padding:10px 12px; border-radius:10px; background:#fff; border:1px dashed #d0d7de;">
                    <span style="font-size:13px; font-weight:700; color:#0969da;">
                        https://gitfusion.com/<span id="slugPreview">your-name</span>
                    </span>
                </div>
            </div>

            <!-- Input -->
            <label style="display:block; font-size:13px; font-weight:700; margin-bottom:6px; color:#24292f;">
                Choose your dashboard name (slug)
            </label>

            <input type="text" id="saveSlug"
                placeholder="Example: saidul"
                onkeyup="updateSlugPreview()"
                style="width:100%; padding:12px; border:1px solid #d0d7de; border-radius:10px; font-size:14px; outline:none;">

            <!-- Rules -->
            <div style="margin-top:12px; font-size:12.5px; color:#57606a; line-height:1.6;">
                <p style="margin:0;">
                    <b>Rules:</b> Only <b>letters</b>, <b>numbers</b>, and <b>dashes (-)</b>.  
                    No spaces. Example: <b>saidul-dev</b>
                </p>

                <p style="margin:8px 0 0 0; color:#cf222e; font-weight:600;">
                    ⚠ If the slug already exists, it will overwrite the old dashboard.
                </p>
            </div>

            <!-- Footer Buttons -->
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:18px;">
                <button onclick="closeSaveModal()"
                    style="padding:10px 14px; border-radius:10px; border:1px solid #d0d7de; background:#fff; cursor:pointer; font-weight:700; color:#24292f;">
                    Cancel
                </button>

                <button onclick="saveDashboard()"
                    style="padding:10px 14px; border-radius:10px; border:none; background:#0969da; color:#fff; cursor:pointer; font-weight:800;">
                    Save & Generate URL
                </button>
            </div>

            <!-- Small note -->
            <p style="margin:14px 0 0 0; font-size:12px; color:#57606a; text-align:center;">
                This will save your GitHub usernames list to GitFusion database.
            </p>

        </div>
    </div>

    <script>
        let fullMergedData = {};
        let selectedYear = "last";

        window.onload = function () {
            const usernames = document.getElementById("usernames").value.trim();

            if (window.location.pathname.length > 1) {
                document.getElementById("copyUrlBtn").style.display = "inline-block";
            }

            if (usernames.length > 0) {
                merge();
            }
        };

        function updateSlugPreview() {
            const slug = document.getElementById("saveSlug").value.trim();
            document.getElementById("slugPreview").innerText = slug ? slug : "your-name";
        }

        async function copyCurrentUrl() {
            try {
                await navigator.clipboard.writeText(window.location.href);
                alert("URL copied successfully!");
            } catch (err) {
                console.error(err);
                alert("Copy failed! Please copy manually.");
            }
        }

        async function merge() {

            const usernames = document.getElementById('usernames').value;

            // Show skeleton loader, hide real content
            document.getElementById("skeletonLoader").style.display = "flex";
            document.getElementById("profile").style.display = "none";
            document.querySelector(".card").style.display = "none";

            try {

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

                applyYearFilter();

                document.getElementById("saveBtn").style.display = "inline-block";

            } catch (err) {
                alert("Failed to fetch GitHub data!");
                console.error(err);
            }

            // Hide skeleton loader, show real content
            document.getElementById("skeletonLoader").style.display = "none";
            document.getElementById("profile").style.display = "block";
            document.querySelector(".card").style.display = "block";
        }

        function generateYearFilter(minYear, maxYear) {

            const yearSelect = document.getElementById("yearFilter");
            yearSelect.innerHTML = "";

            // Default option
            yearSelect.innerHTML += `<option value="last">Last Year</option>`;

            for (let y = maxYear; y >= minYear; y--) {
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
                <div class="profile-card">
                    <img src="${p.avatar}">
                    <div class="profile-info">
                        <h5>${p.name || p.username}</h5>
                        <p>@${p.username}</p>
                        <a href="${p.url}" target="_blank">View GitHub Profile →</a>
                    </div>
                </div>
            `).join('');
        }

        function showRepos(repos) {
            const container = document.getElementById('repos');

            container.innerHTML = repos.map(r => `
                <div class="repo-card">
                    <a href="${r.url}" target="_blank">${r.name}</a>

                    <div class="repo-meta">
                        <span class="badge">${r.language || 'Unknown'}</span>
                        <span class="badge">⭐ ${r.stars}</span>
                    </div>
                </div>
            `).join('');
        }

        function openSaveModal() {
            document.getElementById("saveModal").style.display = "flex";
        }

        function closeSaveModal() {
            document.getElementById("saveModal").style.display = "none";
        }

        async function saveDashboard() {

            const slug = document.getElementById("saveSlug").value.trim();
            const usernames = document.getElementById("usernames").value.trim();

            if (!slug) {
                alert("Please enter a name!");
                return;
            }

            try {
                const res = await fetch('/save-dashboard', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ slug, usernames })
                });

                const data = await res.json();

                if (!data.success) {
                    alert(data.message || "Failed to save!");
                    return;
                }

                closeSaveModal();

                alert("Saved successfully!\nURL: " + data.url);

                window.history.pushState({}, "", data.url);

                document.getElementById("copyUrlBtn").style.display = "inline-block";

            } catch (err) {
                console.error(err);
                alert("Failed to save dashboard!");
            }
        }
    </script>

</body>
</html>
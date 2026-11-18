<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
$fullname = htmlspecialchars($_SESSION['fullname']);
$initials = strtoupper(substr($fullname, 0, 2));
$currentDate = date('l, F j');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StudyFlow - Academic Task & Workload Monitoring System</title>
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-slate-100 font-sans text-slate-800 flex h-screen overflow-hidden">
<!-- SIDEBAR -->
<aside class="w-64 bg-green-500 text-white flex flex-col justify-between hidden md:flex shadow-lg z-20">
<div>
<!-- Logo & Title Section (Clipped to Circle) -->
<div class="p-6 flex flex-col items-center text-center border-b border-green-600/40 space-y-2">
<div class="w-28 h-28 rounded-full overflow-hidden flex items-center justify-center drop-shadow-md bg-transparent">
<img src="KNS_LOGO.png" alt="Kolehiyo ng Subic Logo" class="w-full h-full object-cover scale-110">
</div>
<div class="space-y-0.5">
<h2 class="text-base font-black tracking-wide text-white">StudyFlow</h2>
<p class="text-[10px] text-green-100 font-medium tracking-wider uppercase">Study Planner</p>
</div>
</div>
<!-- Navigation Links -->
<nav class="p-4 space-y-1.5">
<a href="#" onclick="switchTab('home')" id="nav-home" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl bg-green-600 text-white font-medium transition shadow-sm">
<span class="text-lg"></span><span class="text-sm font-semibold">Dashboard</span>
</a>
<a href="#" onclick="switchTab('activity')" id="nav-activity" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl hover:bg-green-600/50 transition text-white">
<span class="text-lg"></span><span class="text-sm font-semibold">Activity</span>
</a>
</nav>
</div>
<!-- Sidebar Footer / Logout -->
<div class="p-4 border-t border-green-600/40">
<a href="logout.php" class="block text-center w-full bg-white/20 hover:bg-white hover:text-green-700 text-white py-2.5 rounded-xl text-xs font-bold transition shadow-sm">Logout</a>
</div>
</aside>
<!-- MAIN CONTENT AREA -->
<main class="flex-1 flex flex-col h-screen overflow-y-auto">
<!-- Top Header -->
<header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center shadow-sm">
<div>
<h1 class="text-2xl font-black text-slate-900">Hello, <?= $fullname ?></h1>
<p class="text-xs text-slate-500 font-medium mt-0.5">Today is <?= $currentDate ?></p>
</div>
<div class="flex items-center space-x-4">
<span class="text-sm font-extrabold text-slate-800 hidden sm:inline">Academic Task & Workload Monitoring System</span>
<div class="bg-indigo-600 text-white font-bold h-10 w-10 rounded-full flex items-center justify-center shadow-md"><?= $initials ?></div>
</div>
</header>
<!-- Main Content Views Container -->
<div class="p-8 space-y-6 flex-1">
<!-- 1. HOME TAB -->
<div id="tab-home" class="space-y-6">
<!-- Statistics Bar -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
<div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
<span class="text-xs text-slate-500 font-bold uppercase tracking-wider text-center">Total Tasks</span>
<h3 id="stat-total" class="text-3xl font-black text-slate-900 text-center my-2">0</h3>
</div>
<div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
<span class="text-xs text-slate-500 font-bold uppercase tracking-wider text-center">Completed</span>
<h3 id="stat-completed" class="text-3xl font-black text-emerald-600 text-center my-2">0</h3>
</div>
<div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
<span class="text-xs text-slate-500 font-bold uppercase tracking-wider text-center">Remaining</span>
<h3 id="stat-remaining" class="text-3xl font-black text-indigo-600 text-center my-2">0</h3>
</div>
<div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-between">
<span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Workload Status</span>
<div id="workload-status-badge" class="mt-2 px-4 py-1.5 bg-emerald-100 text-emerald-700 font-bold rounded-full text-xs shadow-sm">Balanced</div>
</div>
</div>

<!-- Add Task Form & Completion Rate Side by Side -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
<div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
<h3 class="font-bold text-base text-slate-900 mb-4">Add a task</h3>
<form id="task-form" class="space-y-4">
<input type="text" id="task-name" placeholder="Task Name" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm bg-slate-50/50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
<input type="time" id="task-time" placeholder="Time" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm bg-slate-50/50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
<input type="text" id="task-subject" placeholder="Course" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm bg-slate-50/50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
<input type="date" id="task-deadline" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm bg-slate-50/50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
<input type="number" id="task-hours" placeholder="Estimated Hours" min="1" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm bg-slate-50/50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
</div>
<div class="grid grid-cols-1 gap-3">
<select id="task-difficulty" class="w-full px-3 py-3 rounded-xl border border-slate-200 text-sm bg-slate-50/50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
<option value="Low">Low Priority</option>
<option value="Medium" selected>Medium Priority</option>
<option value="High">High Priority</option>
</select>
</div>
<button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-xl transition text-sm shadow-md">ADD TASK</button>
</form>
</div>

<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-between">
<h3 class="font-bold text-base text-slate-900 w-full text-left">Completion Rate</h3>
<div class="relative w-40 h-40 my-auto flex items-center justify-center">
<canvas id="completionChart"></canvas>
<div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
<span id="completion-pct" class="text-2xl font-black text-slate-900">0%</span>
<span class="text-xs text-slate-400 font-bold uppercase">Done</span>
</div>
</div>
</div>
</div>

<!-- Priority-Ranked Tasks Table at the Bottom -->
<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
<h3 class="font-bold text-base text-slate-900 mb-4">Priority-Ranked Tasks</h3>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="border-b border-slate-100 text-xs text-slate-400 uppercase tracking-wider">
<th class="py-2.5 font-semibold">Subject</th>
<th class="py-2.5 font-semibold">Task Description</th>
<th class="py-2.5 font-semibold">Time</th>
<th class="py-2.5 font-semibold">Estimated Hours</th>
<th class="py-2.5 font-semibold">Priority</th>
<th class="py-2.5 font-semibold">Due Date</th>
<th class="py-2.5 font-semibold text-center">Action</th>
</tr>
</thead>
<tbody id="priority-task-table" class="text-sm divide-y divide-slate-50"></tbody>
</table>
</div>
</div>
</div>

<!-- 2. DASHBOARD TAB -->
<div id="tab-dashboard" class="hidden space-y-6">
<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
<h2 class="text-xl font-bold mb-2">Dashboard Overview</h2>
<p class="text-sm text-slate-500">Summary metrics and quick insights into your academic progress.</p>
</div>
</div>
<!-- 3. ACTIVITY TAB -->
<div id="tab-activity" class="hidden space-y-6">
<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
<h2 class="text-xl font-bold mb-4">Task Activity Log</h2>
<div id="activity-task-list" class="space-y-3"></div>
</div>
</div>
<!-- 4. ANALYTICS TAB -->
<div id="tab-analytics" class="hidden space-y-6">
<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
<h2 class="text-xl font-bold mb-2">Performance & Analytics</h2>
<p class="text-sm text-slate-500">Detailed workload breakdown and time analytics overview.</p>
</div>
</div>
<!-- 5. SETTINGS TAB -->
<div id="tab-settings" class="hidden space-y-6">
<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
<h2 class="text-xl font-bold mb-2">Settings</h2>
<p class="text-sm text-slate-500">Manage account details and notification preferences.</p>
</div>
</div>
</div>
</main>
<!-- Script Logic -->
<script>
let tasks = [];
let completionChartInstance = null;

document.addEventListener('DOMContentLoaded', () => {
    fetchTasks();
    const form = document.getElementById('task-form');
    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const newTask = {
                name: document.getElementById('task-name').value,
                time: document.getElementById('task-time').value,
                subject: document.getElementById('task-subject').value,
                deadline: document.getElementById('task-deadline').value,
                difficulty: document.getElementById('task-difficulty').value,
                hours: parseFloat(document.getElementById('task-hours').value)
            };
            fetch('api.php?action=add_task', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(newTask)
            }).then(res => res.json()).then(res => {
                if(res.success) { fetchTasks(); form.reset(); }
            });
        });
    }
});

function fetchTasks() {
    fetch('api.php?action=get_tasks')
    .then(res => res.json())
    .then(data => {
        tasks = Array.isArray(data) ? data : [];
        updateDashboard();
    });
}

// Helper function to format time nicely into 12-hour AM/PM
function formatTime(timeString) {
    if (!timeString || timeString === '00:00:00') return 'No Time Set';
    const parts = timeString.split(':');
    let hour = parseInt(parts[0], 10);
    const minute = parts[1] || '00';
    const ampm = hour >= 12 ? 'PM' : 'AM';
    hour = hour % 12;
    hour = hour ? hour : 12; 
    return `${hour}:${minute} ${ampm}`;
}

// Sidebar Navigation Switcher
function switchTab(tabId) {
    const tabs = ['home', 'dashboard', 'activity', 'analytics', 'settings'];
    tabs.forEach(t => {
        const el = document.getElementById(`tab-${t}`);
        const nav = document.getElementById(`nav-${t}`);
        if (el) {
            if (t === tabId) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        }
        if (nav) {
            if (t === tabId) {
                nav.className = "flex items-center space-x-3.5 px-4 py-3 rounded-xl bg-green-600 text-white font-medium transition shadow-sm";
            } else {
                nav.className = "flex items-center space-x-3.5 px-4 py-3 rounded-xl hover:bg-green-600/50 transition text-white";
            }
        }
    });
}

function updateDashboard() {
    const total = tasks.length;
    const completed = tasks.filter(t => Number(t.completed) === 1).length;
    const remaining = total - completed;
    document.getElementById('stat-total').textContent = total;
    document.getElementById('stat-completed').textContent = completed;
    document.getElementById('stat-remaining').textContent = remaining;
    document.getElementById('completion-pct').textContent = `${total > 0 ? Math.round((completed/total) * 100) : 0}%`;
    
    const tableBody = document.getElementById('priority-task-table');
    const activityList = document.getElementById('activity-task-list');
    
    if(tasks.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-6 text-slate-400 text-sm">No tasks added yet.</td></tr>`;
        if (activityList) activityList.innerHTML = `<p class="text-slate-400 text-sm">No activity records found.</p>`;
    } else {
        tableBody.innerHTML = '';
        let activityHTML = '';
        tasks.forEach(task => {
            const isCompleted = Number(task.completed) === 1;
            const badgeColor = task.difficulty === 'High' ? 'bg-rose-50 text-rose-600 border-rose-200' : task.difficulty === 'Medium' ? 'bg-amber-50 text-amber-600 border-amber-200' : 'bg-emerald-50 text-emerald-600 border-emerald-200';
            
            // Table Row with formatted time call & Done / Undo button logic
            const row = document.createElement('tr');
            row.className = `border-b border-slate-50 hover:bg-slate-50/80 transition ${isCompleted ? 'opacity-50 line-through' : ''}`;
            row.innerHTML = `
                <td class="py-3 px-2 font-bold text-slate-900">${task.subject}</td>
                <td class="py-3 px-2 text-slate-600">${task.name}</td>
                <td class="py-3 px-2 text-slate-600">${formatTime(task.time)}</td>
                <td class="py-3 px-2 text-slate-600">${task.hours} hrs</td>
                <td class="py-3 px-2"><span class="text-xs px-2.5 py-1 rounded-full font-bold border ${badgeColor}">${task.difficulty}</span></td>
                <td class="py-3 px-2 text-slate-500 text-xs font-medium">${task.deadline}</td>
                <td class="py-3 px-2 text-center space-x-2">
                    <button onclick="toggleTask(${task.id})" class="text-xs px-3 py-1 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-lg font-bold transition">${isCompleted ? 'Undo' : 'Done'}</button>
                    <button onclick="deleteTask(${task.id})" class="text-xs px-2 py-1 text-rose-500 hover:bg-rose-50 rounded-lg font-bold transition">Delete</button>
                </td>
            `;
            tableBody.appendChild(row);
            
            // Activity Tab Item with formatted time call
            activityHTML += `
                <div class="p-4 rounded-xl border border-slate-200 flex justify-between items-center bg-white shadow-sm">
                    <div>
                        <h4 class="font-bold text-sm text-slate-900">${task.name} (${formatTime(task.time)}) (${task.subject})</h4>
                        <p class="text-xs text-slate-500">Due: ${task.deadline} | Difficulty: ${task.difficulty} | Est: ${task.hours}h</p>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full font-bold ${isCompleted ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600'}">${isCompleted ? 'Completed' : 'Pending'}</span>
                </div>
            `;
        });
        if (activityList) activityList.innerHTML = activityHTML;
    }
    try { renderCharts(completed, remaining, total); } catch(e){}
}

function renderCharts(completed, remaining, total) {
    if (typeof Chart === 'undefined') return;
    const ctxCompletion = document.getElementById('completionChart');
    if(ctxCompletion) {
        if (completionChartInstance) completionChartInstance.destroy();
        
        const chartData = total === 0 || completed === 0 ? [0, 1] : [completed, remaining];
        const chartColors = ['#22c55e', '#e2e8f0'];
        
        completionChartInstance = new Chart(ctxCompletion, {
            type: 'doughnut',
            data: { 
                datasets: [{ 
                    data: chartData, 
                    backgroundColor: chartColors, 
                    borderWidth: 0 
                }] 
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } }
        });
    }
}

function toggleTask(id) {
    fetch('api.php?action=toggle_task', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({id}) }).then(res => res.json()).then(res => { if(res.success) fetchTasks(); });
}

function deleteTask(id) {
    fetch('api.php?action=delete_task', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({id}) }).then(res => res.json()).then(res => { if(res.success) fetchTasks(); });
}
</script>
</body>
</html>
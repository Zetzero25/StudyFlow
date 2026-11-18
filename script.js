let tasks = [];
let workloadChartInstance = null;
let completionChartInstance = null;

document.addEventListener('DOMContentLoaded', () => {
    fetchTasks();
    
    // Form submission handler
    const taskForm = document.getElementById('task-form');
    if (taskForm) {
        taskForm.addEventListener('submit', function(e) {
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
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    fetchTasks();
                    this.reset();
                }
            })
            .catch(err => console.error('Error adding task:', err));
        });
    }
});

// Fetch Tasks from Database via API
function fetchTasks() {
    fetch('api.php?action=get_tasks')
        .then(response => response.json())
        .then(data => {
            tasks = Array.isArray(data) ? data : [];
            updateDashboard();
        })
        .catch(err => console.error('Error fetching tasks:', err));
}

// Tab Switching
function switchTab(tabId) {
    ['dashboard', 'tasks', 'schedule', 'deadlines'].forEach(t => {
        const el = document.getElementById(`tab-${t}`);
        if (el) el.classList.add('hidden');
    });
    const target = document.getElementById(`tab-${tabId === 'home' ? 'dashboard' : tabId}`);
    if (target) target.classList.remove('hidden');
}

// Update Dashboard UI & Charts
function updateDashboard() {
    const timeValue = document.getElementById('tasktime');
    const statTotal = document.getElementById('stat-total');
    const statCompleted = document.getElementById('stat-completed');
    const statRemaining = document.getElementById('stat-remaining');
    const statStatus = document.getElementById('workload-status-badge');
    const tableBody = document.getElementById('priority-task-table');
    const completionPct = document.getElementById('completion-pct');

    const total = tasks.length;
    const completed = tasks.filter(t => Number(t.completed) === 1).length;
    const remaining = total - completed;

    if (statTotal) statTotal.textContent = total;
    if (statCompleted) statCompleted.textContent = completed;
    if (statRemaining) statRemaining.textContent = remaining;

    // Workload Status logic
    if (statStatus) {
        if (remaining > 5) {
            statStatus.textContent = "High Workload";
            statStatus.className = "mt-2 px-4 py-1.5 bg-rose-100 text-rose-700 font-bold rounded-full text-xs shadow-sm";
        } else {
            statStatus.textContent = "Balanced";
            statStatus.className = "mt-2 px-4 py-1.5 bg-emerald-100 text-emerald-700 font-bold rounded-full text-xs shadow-sm";
        }
    }

    // Completion percentage
    const pct = total > 0 ? Math.round((completed / total) * 100) : 0;
    if (completionPct) completionPct.textContent = `${pct}%`;

    // Render Priority-Ranked Tasks Table with Action Buttons
    if (tableBody) {
        if (tasks.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-6 text-slate-400 text-sm">No tasks added yet. Use the form above to add one.</td></tr>`;
        } else {
            tableBody.innerHTML = '';
            tasks.forEach(task => {
                const isCompleted = Number(task.completed) === 1;
                const badgeColor = task.difficulty === 'High' ? 'bg-rose-50 text-rose-600 border-rose-200' :
                                   task.difficulty === 'Medium' ? 'bg-amber-50 text-amber-600 border-amber-200' : 
                                   'bg-emerald-50 text-emerald-600 border-emerald-200';
                
                const row = document.createElement('tr');
                row.className = `border-b border-slate-50 hover:bg-slate-50/80 transition ${isCompleted ? 'opacity-50 line-through' : ''}`;
                row.innerHTML = `
                    <td class="py-3 px-2 font-bold text-slate-900">${task.subject}</td>
                    <td class="py-3 px-2 text-slate-600">${task.name}</td>
                    <td class="py-3 px-2 text-slate-600">${task.time}</td>
                    <td class="py-3 px-2 text-slate-600">${task.hours} hrs</td>
                    <td class="py-3 px-2"><span class="text-xs px-2.5 py-1 rounded-full font-bold border ${badgeColor}">${task.difficulty}</span></td>
                    <td class="py-3 px-2 text-slate-500 text-xs font-medium">${task.deadline}</td>
                    <td class="py-3 px-2 text-center space-x-3">
                        <button onclick="toggleTask(${task.id})" class="text-xs px-3 py-1 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-lg font-bold transition shadow-sm">${isCompleted ? 'Mark Pending' : 'Mark Done'}</button>
                        <button onclick="deleteTask(${task.id})" class="text-xs px-2 py-1 text-rose-500 hover:bg-rose-50 rounded-lg font-bold transition">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }
    }

    // Safely attempt to render charts if Chart.js is available
    try {
        renderCharts(completed, remaining);
    } catch (e) {
        console.warn("Chart.js not loaded or offline. Skipping chart rendering.");
    }
}

// Render Chart.js Analytics (Safe Check)
function renderCharts(completed, remaining) {
    if (typeof Chart === 'undefined') return;

    // Workload Density Bar Chart
    const ctxWorkload = document.getElementById('workloadChart');
    if (ctxWorkload) {
        if (workloadChartInstance) workloadChartInstance.destroy();
        workloadChartInstance = new Chart(ctxWorkload, {
            type: 'bar',
            data: {
                labels: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                datasets: [{
                    label: 'Workload Hours',
                    data: [35, 25, 40, 50, 30, 45, 38],
                    backgroundColor: ['#6366f1', '#22c55e', '#6366f1', '#f43f5e', '#6366f1', '#22c55e', '#6366f1'],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, max: 80, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Completion Rate Doughnut Chart
    const ctxCompletion = document.getElementById('completionChart');
    if (ctxCompletion) {
        if (completionChartInstance) completionChartInstance.destroy();
        completionChartInstance = new Chart(ctxCompletion, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [completed || 1, remaining || 0],
                    backgroundColor: ['#6366f1', '#e2e8f0'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: { legend: { display: false } }
            }
        });
    }
}

// Toggle Task Completion
function toggleTask(id) {
    fetch('api.php?action=toggle_task', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) fetchTasks();
    });
}

// Delete Task
function deleteTask(id) {
    fetch('api.php?action=delete_task', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) fetchTasks();
    });
}
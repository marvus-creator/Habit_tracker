document.addEventListener("DOMContentLoaded", () => {
    loadHabits();
    loadChart();
});

// --- 1. Load Habits ---
async function loadHabits() {
    try {
        const res = await fetch('api/habits.php');
        const habits = await res.json();
        const list = document.getElementById('habitList');
        list.innerHTML = '';

        habits.forEach(habit => {
            const isDone = habit.today_status === 'completed';
            
            // New "Badge" style for streaks
            const streakDisplay = habit.streak > 0 
                ? `<div class="streak-badge">🔥 ${habit.streak} Day Streak</div>` 
                : '';

            const div = document.createElement('div');
            div.className = `habit-item ${isDone ? 'completed' : ''}`;
            
            div.innerHTML = `
                <div class="habit-left" onclick="toggleHabit(${habit.id})" style="flex-grow:1; cursor:pointer;">
                    <div class="habit-icon-display">${habit.habit_icon}</div>
                    <div class="habit-info">
                        <h4>${habit.habit_name}</h4>
                        ${streakDisplay}
                    </div>
                </div>
                
                <div style="display:flex; align-items:center; gap:15px;">
                    <div class="status-indicator" onclick="toggleHabit(${habit.id})"></div>
                    <button class="delete-btn" onclick="deleteHabit(${habit.id}, event)">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            `;
            list.appendChild(div);
        });
    } catch (error) { console.error(error); }
}

// --- 2. Add Habit ---
async function addHabit() {
    const nameInput = document.getElementById('habitName');
    const iconInput = document.getElementById('habitIcon');
    if(!nameInput.value) return;

    await fetch('api/habits.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ habit_name: nameInput.value, habit_icon: iconInput.value || '⚡' })
    });
    nameInput.value = '';
    loadHabits();
}

// --- 3. Toggle & Delete ---
async function toggleHabit(id) {
    await fetch('api/log_progress.php', { method: 'POST', body: JSON.stringify({ habit_id: id }) });
    loadHabits(); loadChart(); 
}

async function deleteHabit(id, event) {
    event.stopPropagation();
    if(!confirm("Delete this habit?")) return;
    await fetch('api/habits.php', { method: 'DELETE', body: JSON.stringify({ id: id }) });
    loadHabits(); loadChart();
}

// --- 4. The NEON Chart ---
async function loadChart() {
    const ctx = document.getElementById('progressChart').getContext('2d');
    
    // Create a Gradient for the chart line
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(0, 210, 255, 0.5)'); // Bright Cyan at top
    gradient.addColorStop(1, 'rgba(0, 210, 255, 0.0)'); // Fade to transparent

    try {
        const res = await fetch('api/analytics.php');
        const chartData = await res.json();

        if(window.myChart) window.myChart.destroy();

        window.myChart = new Chart(ctx, {
            type: 'line', 
            data: {
                labels: chartData.labels, 
                datasets: [{
                    label: 'Completed',
                    data: chartData.data,
                    borderColor: '#00d2ff', // Cyan Line
                    backgroundColor: gradient, // Gradient Fill
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4, // Curvy line
                    pointBackgroundColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: 'rgba(255,255,255,0.6)' }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { color: 'rgba(255,255,255,0.6)' }
                    }
                }
            }
        });
    } catch (error) { console.error(error); }
}
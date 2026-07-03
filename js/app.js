document.addEventListener('DOMContentLoaded', () => {
    renderSummaryCards();
});

function renderSummaryCards() {
    const container = document.getElementById('summaryCards');
    if (!container) return; // not on dashboard page

    const summaryData = [
        { icon: '👨‍🎓', label: 'Total Students', value: 0 },
        { icon: '📚', label: 'Subjects', value: 0 },
        { icon: '⏳', label: 'Pending Grades', value: 0 }
    ];

    container.innerHTML = summaryData.map(item => `
        <div class="card">
            <div class="card-icon">${item.icon}</div>
            <h3>${item.value}</h3>
            <p>${item.label}</p>
        </div>
    `).join('');
}
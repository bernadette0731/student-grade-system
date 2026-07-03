document.addEventListener('DOMContentLoaded', () => {
    renderSummaryCards();

    const loginForm = document.querySelector('.login-form');
    if (!loginForm) return;

    loginForm.addEventListener('submit', (e) => {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();
        const errorEl = document.getElementById('clientError');
        const loginBtn = document.getElementById('loginBtn');

        if (username === '' || password === '') {
            e.preventDefault();
            errorEl.textContent = 'Please fill in both fields.';
            errorEl.style.display = 'block';
            return;
        }

        loginBtn.disabled = true;
        loginBtn.textContent = 'Logging in...';
    });

    // Reset button state on page load (covers failed-login reloads, back button)
    window.addEventListener('pageshow', () => {
        const loginBtn = document.getElementById('loginBtn');
        if (loginBtn) {
            loginBtn.disabled = false;
            loginBtn.textContent = 'Login';
        }
    });

    const togglePassword = document.getElementById('togglePassword');
    if (togglePassword) {
        togglePassword.addEventListener('click', () => {
            const passwordInput = document.getElementById('password');
            passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
            togglePassword.style.opacity = passwordInput.type === 'text' ? '0.6' : '1';
        });
    }
});

function renderSummaryCards() {
    const container = document.getElementById('summaryCards');
    if (!container) return; 

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
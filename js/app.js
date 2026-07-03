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
        {
            icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5-10-5z"></path>
                        <path d="M6 12v5c0 1.5 2.5 3 6 3s6-1.5 6-3v-5"></path>
                    </svg>`,
            label: 'Total Students',
            value: 0
        },
        {
            icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>`,
            label: 'Subjects',
            value: 0
        },
        {
            icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>`,
            label: 'Pending Grades',
            value: 0
        }
    ];

    container.innerHTML = summaryData.map(item => `
        <div class="card">
            <div class="card-icon">${item.icon}</div>
            <h3>${item.value}</h3>
            <p>${item.label}</p>
        </div>
    `).join('');
}
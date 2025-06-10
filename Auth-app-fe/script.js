// Konfiguracija
const API_BASE = 'http://localhost:8000/api';
let currentUser = null;
let authToken = localStorage.getItem('token');

// Pomoćne funkcije
function showAlert(elementId, message, type = 'error') {
    const alertEl = document.getElementById(elementId);
    alertEl.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
    setTimeout(() => {
        alertEl.innerHTML = '';
    }, 5000);
}

function clearErrors(formType) {
    const errorElements = document.querySelectorAll(`#${formType}-form .error-text`);
    errorElements.forEach(el => el.textContent = '');
    
    const inputs = document.querySelectorAll(`#${formType}-form input`);
    inputs.forEach(input => input.classList.remove('error'));
}

function showErrors(errors, formType) {
    Object.keys(errors).forEach(field => {
        const errorEl = document.getElementById(`${formType}-${field}-error`);
        const inputEl = document.getElementById(`${formType}-${field}`);
        
        if (errorEl && errors[field][0]) {
            errorEl.textContent = errors[field][0];
            if (inputEl) inputEl.classList.add('error');
        }
    });
}

async function apiCall(endpoint, options = {}) {
    const url = `${API_BASE}${endpoint}`;
    const config = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        ...options
    };

    if (authToken) {
        config.headers['Authorization'] = `Bearer ${authToken}`;
    }

    try {
        const response = await fetch(url, config);
        const data = await response.json();
        
        if (!response.ok) {
            throw { status: response.status, data };
        }
        
        return data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// Prikaz/sakrivanje forma
function showLogin() {
    document.getElementById('login-form').classList.remove('hidden');
    document.getElementById('register-form').classList.add('hidden');
}

function showRegister() {
    document.getElementById('register-form').classList.remove('hidden');
    document.getElementById('login-form').classList.add('hidden');
}

function showDashboard() {
    document.getElementById('auth-section').classList.add('hidden');
    document.getElementById('dashboard-section').classList.remove('hidden');
    
    if (currentUser) {
        document.getElementById('user-name').textContent = currentUser.name;
        document.getElementById('dash-name').textContent = currentUser.name;
        document.getElementById('dash-email').textContent = currentUser.email;
        
        const date = new Date(currentUser.created_at);
        document.getElementById('dash-date').textContent = date.toLocaleDateString('sr-RS');
    }
}

function showAuth() {
    document.getElementById('dashboard-section').classList.add('hidden');
    document.getElementById('auth-section').classList.remove('hidden');
}

// Autentikacija funkcije
async function login(email, password) {
    try {
        clearErrors('login');
        
        const result = await apiCall('/auth/login', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });

        if (result.success) {
            authToken = result.token;
            currentUser = result.user;
            localStorage.setItem('token', authToken);
            
            showAlert('login-alert', result.message, 'success');
            setTimeout(() => showDashboard(), 1000);
        }
    } catch (error) {
        if (error.status === 422 && error.data.errors) {
            showErrors(error.data.errors, 'login');
        } else {
            showAlert('login-alert', error.data?.message || 'Greška pri prijavi');
        }
    }
}

async function register(formData) {
    try {
        clearErrors('register');
        
        const result = await apiCall('/auth/register', {
            method: 'POST',
            body: JSON.stringify(formData)
        });

        if (result.success) {
            authToken = result.token;
            currentUser = result.user;
            localStorage.setItem('token', authToken);
            
            showAlert('register-alert', result.message, 'success');
            setTimeout(() => showDashboard(), 1000);
        }
    } catch (error) {
        if (error.status === 422 && error.data.errors) {
            showErrors(error.data.errors, 'register');
        } else {
            showAlert('register-alert', error.data?.message || 'Greška pri registraciji');
        }
    }
}

async function getCurrentUser() {
    try {
        const result = await apiCall('/user');
        if (result.success) {
            currentUser = result.user;
            showDashboard();
        }
    } catch (error) {
        logout();
    }
}

function logout() {
    authToken = null;
    currentUser = null;
    localStorage.removeItem('token');
    showAuth();
}

// Event listeners
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
    await login(email, password);
});

document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = {
        name: document.getElementById('register-name').value,
        email: document.getElementById('register-email').value,
        password: document.getElementById('register-password').value,
        password_confirmation: document.getElementById('register-password-confirmation').value
    };
    await register(formData);
});

// Inicijalizacija
if (authToken) {
    getCurrentUser();
} else {
    showAuth();
}
// =============================================
// SVG ICONS LIBRARY
// =============================================
const ICONS = {
    activity: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>',
    camera: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>',
    power: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>',
    zap: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>',
    thermometer: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path></svg>',
    droplet: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>',
    wind: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.59 4.59A2 2 0 1 1 11 8H2m10.59 11.41A2 2 0 1 0 14 16H2m15.73-8.27A2.5 2.5 0 1 1 19.5 12H2"></path></svg>',
    volume: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>',
    light: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>',
    trafficLight: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="2" width="12" height="20" rx="2"></rect><circle cx="12" cy="7" r="2"></circle><circle cx="12" cy="12" r="2"></circle><circle cx="12" cy="17" r="2"></circle></svg>',
    cpu: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line></svg>',
    settings: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M12 1v6m0 6v6m5.656-15.656l-4.243 4.243M10.586 13.414l-4.243 4.243M23 12h-6m-6 0H1m15.656 5.656l-4.243-4.243M10.586 10.586l-4.243-4.243"></path></svg>',
    check: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>',
    x: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>',
    info: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',
    alert: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>'
};

// =============================================
// GLOBAL STATE
// ============================================= 
let socket;
let devices = [];
let currentFilter = 'ALL';

// =============================================
// SOCKET.IO CONNECTION
// =============================================
function initSocket() {
    socket = io();
    
    socket.on('connect', () => {
        updateConnectionStatus('connected', 'CONNECTED');
    });
    
    socket.on('disconnect', () => {
        updateConnectionStatus('error', 'DISCONNECTED');
    });
    
    socket.on('devices-update', (devicesData) => {
        devices = devicesData;
        renderDevices();
    });
    
    socket.on('command-response', (response) => {
        if (response.success) {
            showToast('SUCCESS', response.message, 'success');
        } else {
            showToast('ERROR', response.message, 'error');
        }
    });
}

// =============================================
// CONNECTION STATUS
// =============================================
function updateConnectionStatus(status, text) {
    const indicator = document.getElementById('statusIndicator');
    const statusText = indicator.querySelector('.status-text');
    
    indicator.className = 'status-indicator ' + status;
    statusText.textContent = text;
}

// =============================================
// DEVICE RENDERING
// =============================================
function renderDevices() {
    const grid = document.getElementById('devicesGrid');
    
    let filteredDevices = devices;
    if (currentFilter !== 'ALL') {
        filteredDevices = devices.filter(device => {
            const type = (device.type || '').toUpperCase();
            if (currentFilter === 'SENSORS') {
                return type.includes('SENSOR');
            } else if (currentFilter === 'ACTUATORS') {
                return !type.includes('SENSOR');
            }
            return true;
        });
    }
    
    document.getElementById('deviceCount').innerHTML = `
        <span class="count">${filteredDevices.length}</span>
        <span class="label">DEVICES</span>
    `;
    
    if (filteredDevices.length === 0) {
        grid.innerHTML = `
            <div class="empty-state">
                <h2>NO DEVICES FOUND</h2>
                <p>WAITING FOR DEVICES TO CONNECT TO GATEWAY</p>
            </div>
        `;
        return;
    }
    
    grid.innerHTML = filteredDevices.map(device => createDeviceCard(device)).join('');
}

function createDeviceCard(device) {
    const type = (device.type || 'UNKNOWN').toUpperCase();
    const icon = getDeviceIcon(type);
    
    return `
        <div class="device-card" data-device="${device.name}">
            <div class="device-header">
                <div class="device-title">
                    <div>
                        <h3>${device.name || 'DEVICE'}</h3>
                        <div class="device-type">${type}</div>
                    </div>
                </div>
                <span class="device-badge">ONLINE</span>
            </div>
            
            <div class="device-info">
                <div class="info-row">
                    <span class="info-label">IP ADDRESS</span>
                    <span class="info-value">${device.ip || 'N/A'}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">PORT</span>
                    <span class="info-value">${device.port || 'N/A'}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">STATE</span>
                    <span class="info-value">${device.currentState || device.current_state || 'N/A'}</span>
                </div>
                ${renderSensorData(device)}
            </div>
            
            ${renderDeviceActions(device)}
        </div>
    `;
}

function renderSensorData(device) {
    const data = [];
    
    if (device.temperature) {
        data.push(`
            <div class="sensor-reading">
                <div class="sensor-value">${device.temperature}</div>
            </div>
        `);
    }
    
    if (device.humidity) {
        data.push(`
            <div class="info-row">
                <span class="info-label">HUMIDITY</span>
                <span class="info-value">${device.humidity}</span>
            </div>
        `);
    }
    
    if (device.air_quality) {
        data.push(`
            <div class="info-row">
                <span class="info-label">AIR QUALITY</span>
                <span class="info-value">${device.air_quality}</span>
            </div>
        `);
    }
    
    if (device.noise_level) {
        data.push(`
            <div class="info-row">
                <span class="info-label">NOISE LEVEL</span>
                <span class="info-value">${device.noise_level}</span>
            </div>
        `);
    }
    
    return data.join('');
}

function renderDeviceActions(device) {
    const type = (device.type || '').toUpperCase();
    
    if (type.includes('TRAFFIC_LIGHT') || type.includes('SEMAFORO')) {
        return `
            <div class="device-actions">
                <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'SET_LIGHT', 'RED')">
                    RED
                </button>
                <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'SET_LIGHT', 'YELLOW')">
                    YELLOW
                </button>
                <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'SET_LIGHT', 'GREEN')">
                    GREEN
                </button>
                <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'SET_MODE', 'AUTO')">
                    AUTO
                </button>
                <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'SET_MODE', 'MANUAL')">
                    MANUAL
                </button>
                <button class="btn-action" onclick="openCommandModal('${device.name}')">
                    ADVANCED
                </button>
            </div>
        `;
    }
    
    if (type.includes('CAMERA')) {
        return `
            <div class="device-actions">
                <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'TURN_ON', '')">
                    ON
                </button>
                <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'TURN_OFF', '')">
                    OFF
                </button>
                <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'SET_RESOLUTION', '4K')">
                    4K
                </button>
                <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'SET_RESOLUTION', '1080p')">
                    1080P
                </button>
                <button class="btn-action" onclick="openCommandModal('${device.name}')">
                    ADVANCED
                </button>
            </div>
        `;
    }
    
    if (type.includes('POSTE') || type.includes('LIGHT') || type.includes('LAMP')) {
        return `
            <div class="device-actions">
                <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'TURN_ON', '')">
                    ON
                </button>
                <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'TURN_OFF', '')">
                    OFF
                </button>
                <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'SET_BRIGHTNESS', '100')">
                    100%
                </button>
                <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'SET_BRIGHTNESS', '50')">
                    50%
                </button>
                <button class="btn-action" onclick="openCommandModal('${device.name}')">
                    ADVANCED
                </button>
            </div>
        `;
    }
    
    if (type.includes('SENSOR')) {
        return `
            <div class="device-actions">
                <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'GET_READING', '')">
                    READING
                </button>
                <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'GET_STATUS', '')">
                    STATUS
                </button>
                <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'SET_INTERVAL', '10')">
                    10s
                </button>
                <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'SET_INTERVAL', '30')">
                    30s
                </button>
                <button class="btn-action" onclick="openCommandModal('${device.name}')">
                    ADVANCED
                </button>
            </div>
        `;
    }
    
    return `
        <div class="device-actions">
            <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'TURN_ON', '')">
                ON
            </button>
            <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'TURN_OFF', '')">
                OFF
            </button>
            <button class="btn-action" onclick="sendQuickCommand('${device.name}', 'GET_STATUS', '')">
                STATUS
            </button>
            <button class="btn-action" onclick="openCommandModal('${device.name}')">
                COMMAND
            </button>
        </div>
    `;
}

function getDeviceIcon(type) {
    if (type.includes('TRAFFIC_LIGHT') || type.includes('SEMAFORO')) return ICONS.trafficLight;
    if (type.includes('CAMERA')) return ICONS.camera;
    if (type.includes('TEMPERATURE')) return ICONS.thermometer;
    if (type.includes('HUMIDITY')) return ICONS.droplet;
    if (type.includes('AIR_QUALITY')) return ICONS.wind;
    if (type.includes('NOISE')) return ICONS.volume;
    if (type.includes('LIGHT') || type.includes('LAMP') || type.includes('POSTE')) return ICONS.light;
    if (type.includes('SENSOR')) return ICONS.activity;
    return ICONS.cpu;
}

// =============================================
// COMMAND HANDLING
// =============================================
function sendQuickCommand(deviceName, action, value) {
    socket.emit('send-command', { deviceName, action, value });
    showToast('SENDING', `Command ${action} to ${deviceName}`, 'info');
}

function openCommandModal(deviceName) {
    document.getElementById('modalDeviceName').value = deviceName;
    document.getElementById('modalAction').value = '';
    document.getElementById('modalValue').value = '';
    document.getElementById('commandModal').classList.add('active');
}

function closeCommandModal() {
    document.getElementById('commandModal').classList.remove('active');
}

function submitCommand() {
    const deviceName = document.getElementById('modalDeviceName').value;
    const action = document.getElementById('modalAction').value.trim();
    const value = document.getElementById('modalValue').value.trim();
    
    if (!action) {
        showToast('ERROR', 'Action is required', 'error');
        return;
    }
    
    sendQuickCommand(deviceName, action, value);
    closeCommandModal();
}

// =============================================
// FILTERS
// =============================================
function filterDevices(filter) {
    currentFilter = filter;
    
    document.querySelectorAll('.btn-filter').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    renderDevices();
}

// =============================================
// REFRESH
// =============================================
function refreshDevices() {
    showToast('UPDATING', 'Querying devices...', 'info');
    
    fetch('/api/devices')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                devices = data.devices;
                renderDevices();
                showToast('SUCCESS', `${devices.length} devices found`, 'success');
            }
        })
        .catch(err => {
            console.error('Error updating:', err);
            showToast('ERROR', 'Failed to query devices', 'error');
        });
}

// =============================================
// TOAST NOTIFICATIONS
// =============================================
function showToast(title, message, type = 'info') {
    const container = document.getElementById('toastContainer');
    
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
        </div>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'toastSlideIn 0.3s ease reverse';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// =============================================
// KEYBOARD SHORTCUTS
// =============================================
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeCommandModal();
    }
    
    if (e.key === 'Enter' && document.getElementById('commandModal').classList.contains('active')) {
        submitCommand();
    }
    
    if (e.key === 'r' && !e.ctrlKey && !e.metaKey) {
        const activeElement = document.activeElement;
        if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
            e.preventDefault();
            refreshDevices();
        }
    }
});

document.getElementById('commandModal').addEventListener('click', (e) => {
    if (e.target.id === 'commandModal') {
        closeCommandModal();
    }
});

// =============================================
// INITIALIZATION
// =============================================
document.addEventListener('DOMContentLoaded', () => {
    initSocket();
});

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
    volume: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>',
    light: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line></svg>',
    trafficLight: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="2" width="12" height="20" rx="2"></rect><circle cx="12" cy="7" r="2"></circle><circle cx="12" cy="12" r="2"></circle><circle cx="12" cy="17" r="2"></circle></svg>',
    cpu: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"></rect></svg>'
};

// =============================================
// GLOBAL STATE
// =============================================
let devices = [];
let currentFilter = 'ALL';

const API_URL = window.API_URL;

function initSocket() {
    refreshDevices();
    setInterval(() => {
        refreshDevices();
    }, 3000);
    updateConnectionStatus('connected', 'CONNECTED');
}

// =============================================
// CONNECTION STATUS
// =============================================
function updateConnectionStatus(status, text) {
    const indicator = document.getElementById('statusIndicator');
    const statusText = indicator.querySelector('.status-text');
    indicator.className = `status-indicator ${status}`;
    statusText.textContent = text;
}

// =============================================
// DEVICE RENDERING
// =============================================
function renderDevices() {
    const grid = document.getElementById('devicesGrid');

    let filtered = devices;
    if (currentFilter !== 'ALL') {
        filtered = devices.filter(d => {
            const t = (d.type || '').toUpperCase();
            return currentFilter === 'SENSORS'
                ? t.includes('SENSOR')
                : !t.includes('SENSOR');
        });
    }

    document.getElementById('deviceCount').innerHTML = `
        <span class="count">${filtered.length}</span>
        <span class="label">DEVICES</span>
    `;

    if (!filtered.length) {
        grid.innerHTML = `
            <div class="empty-state">
                <h2>NO DEVICES FOUND</h2>
                <p>WAITING FOR DEVICES TO CONNECT</p>
            </div>`;
        return;
    }

    grid.innerHTML = filtered.map(createDeviceCard).join('');
}

function createDeviceCard(device) {
    const type = (device.type || 'UNKNOWN').toUpperCase();
    const state = device.current_state || device.currentState || '-'; 

    // MODIFICADO: Adicionado id="card-${device.name}" para permitir atualização local
    return `
        <div id="card-${device.name}" class="device-card">
            <div class="device-header">
                <h3>${device.name}</h3>
                <span class="device-badge">${state}</span> 
            </div>

            <div class="device-info">
                <div class="info-row"><span>IP</span><span>${device.ip || '-'}</span></div>
                <div class="info-row"><span>PORT</span><span>${device.port || '-'}</span></div>
                <div class="info-row"><span>STATE</span><span>${state}</span></div>
            </div>

            ${renderDeviceActions(device)}
        </div>
    `;
}

// =============================================
// ACTIONS
// =============================================
function renderDeviceActions(device) {
    const type = (device.type || '').toUpperCase();

    if (type.includes('CAMERA')) {
        return `
            <div class="device-actions">
                <button onclick="sendQuickCommand('${device.name}','TURN_ON','')">ON</button>
                <button onclick="sendQuickCommand('${device.name}','TURN_OFF','')">OFF</button>
                <button onclick="sendQuickCommand('${device.name}','TAKE_SNAPSHOT','')">SNAPSHOT</button>
            </div>`;
    }

    return `
        <div class="device-actions">
            <button onclick="sendQuickCommand('${device.name}','GET_STATUS','')">STATUS</button>
        </div>`;
}

// =============================================
// COMMANDS
// =============================================
function sendQuickCommand(deviceName, action, value) {
    showToast('SENDING', `${action} → ${deviceName}`, 'info');
    
    fetch(`${API_URL}/api/command`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ deviceName, action, value })
    })
    .then(r => r.json())
    .then(resp => {
        const result = resp.result || {};
        console.log('sendQuickCommand response', resp);
        
        if (result.success) {
            showToast('SUCCESS', result.message || 'Success', 'success');

            // MODIFICADO: Se o Gateway retornou o estado ('state'), atualiza a UI imediatamente
            if (result.state) {
                updateLocalDeviceState(deviceName, result.state);
            }
        } else {
            showToast('ERROR', result.message || resp.error || 'No response', 'error');
        }

        // Mantemos o refresh global como garantia
        setTimeout(refreshDevices, 500);
    })
    .catch(err => {
        showToast('ERROR', 'Failed to send command', 'error');
        console.error('sendQuickCommand error', err);
    });
}

// =============================================
// UI HELPER (LOCAL UPDATE)
// =============================================
function updateLocalDeviceState(deviceName, newState) {
    const card = document.getElementById(`card-${deviceName}`);
    if (!card) return;

    // 1. Atualiza o Badge (Topo direita)
    const badge = card.querySelector('.device-badge');
    if (badge) {
        badge.textContent = newState;
    }

    // 2. Atualiza a linha de "STATE" na lista de informações
    const rows = card.querySelectorAll('.info-row');
    rows.forEach(row => {
        // Verifica se o label da linha é 'STATE'
        if (row.firstElementChild.textContent.trim() === 'STATE') {
            row.lastElementChild.textContent = newState;
        }
    });
}

// =============================================
// FILTER
// =============================================
function filterDevices(filter, event) {
    currentFilter = filter;

    document.querySelectorAll('.btn-filter')
        .forEach(b => b.classList.remove('active'));

    if (event?.target) {
        event.target.classList.add('active');
    }

    renderDevices();
}

// =============================================
// REFRESH
// =============================================
function refreshDevices() {
    fetch(`${API_URL}/api/devices`)
        .then(async response => {
            if (!response.ok) {
                const text = await response.text();
                throw new Error(
                    `HTTP ${response.status} - ${text || response.statusText}`
                );
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.error || 'API retornou success=false');
            }
            devices = data.devices;
            
            // Só renderiza tudo de novo se não houver interação ativa
            // (Mas nesse caso simples renderizamos sempre)
            renderDevices();
        })
        .catch(error => {
            console.error('[API /devices]', error);
            showToast(
                'ERROR',
                error.message || 'Erro desconhecido ao buscar dispositivos',
                'error'
            );
        });
}

// =============================================
// TOAST
// =============================================
function showToast(title, message, type) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<strong>${title}</strong><div>${message}</div>`;
    container.appendChild(toast);

    setTimeout(() => toast.remove(), 4000);
}

// =============================================
// INIT
// =============================================
document.addEventListener('DOMContentLoaded', initSocket);
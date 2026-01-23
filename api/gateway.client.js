const net = require('net');

const GATEWAY_HOST = process.env.GATEWAY_HOST || '127.0.0.1';
const GATEWAY_PORT = parseInt(process.env.GATEWAY_PORT || '7000');

function sendToGateway(message) {
    return new Promise((resolve, reject) => {
        const socket = new net.Socket();
        let buffer = '';

        socket.connect(GATEWAY_PORT, GATEWAY_HOST, () => {
            socket.write(message);
        });

        socket.on('data', (data) => {
            buffer += data.toString();
            if (buffer.includes('\n')) {
                socket.end();
                resolve(JSON.parse(buffer.trim()));
            }
        });

        socket.on('error', reject);
        socket.setTimeout(300000, () => reject(new Error('Timeout')));
    });
}

async function listDevices() {
    const res = await sendToGateway('LIST');
    return Array.isArray(res) ? res : [];
}

async function sendCommand(deviceName, action, value = '') {
    const cmd = `CMD ${deviceName} ${action}${value ? ' ' + value : ''}`;
    return sendToGateway(cmd);
}

module.exports = { listDevices, sendCommand };

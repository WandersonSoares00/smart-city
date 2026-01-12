const express = require('express');
const http = require('http');
const socketIO = require('socket.io');
const net = require('net');
const protobuf = require('protobufjs');
const path = require('path');

const app = express();
const server = http.createServer(app);
const io = socketIO(server);

const GATEWAY_HOST = process.env.GATEWAY_HOST || '127.0.0.1';
const GATEWAY_PORT = parseInt(process.env.GATEWAY_PORT || '7000');

let Command, DeviceList, DeviceInfo;
let connectedClients = 0;
let lastDeviceList = [];

// Carrega o Protocol Buffers
async function loadProto() {
    try {
        const root = await protobuf.load(path.join(__dirname, 'messages.proto'));
        Command = root.lookupType('Command');
        DeviceList = root.lookupType('DeviceList');
        DeviceInfo = root.lookupType('DeviceInfo');
        console.log('✓ Protocol Buffers carregado');
        return true;
    } catch (error) {
        console.error('✗ Erro ao carregar .proto:', error.message);
        return false;
    }
}

// Conecta ao Gateway e envia comando
function sendToGateway(message) {
    return new Promise((resolve, reject) => {
        const socket = new net.Socket();
        let buffer = '';
        
        socket.connect(GATEWAY_PORT, GATEWAY_HOST, () => {
            socket.write(message);
        });

        socket.on('data', (data) => {
            buffer += data.toString('utf8');
            
            if (buffer.includes('\n')) {
                try {
                    const response = JSON.parse(buffer.trim());
                    socket.end();
                    resolve(response);
                } catch (error) {
                    console.error('Conteúdo bruto do buffer que falhou:', buffer);
                    socket.end();
                    reject(new Error('Erro ao parsear resposta: ' + error.message));
                }
            }
        });

        socket.on('error', (error) => {
            console.error(`Falha ao conectar em ${GATEWAY_HOST}:${GATEWAY_PORT}`);
            reject(error);
        });

        socket.on('timeout', () => {
            socket.end();
            reject(new Error('Timeout na conexão'));
        });

        socket.setTimeout(5000);
    });
}

// Lista dispositivos conectados ao Gateway
async function listDevices() {
    try {
        const response = await sendToGateway('LIST');
        
        if (Array.isArray(response)) {
            lastDeviceList = response;
            return response;
        }
        
        return [];
    } catch (error) {
        console.error('Erro ao listar dispositivos:', error.message);
        return [];
    }
}

// Envia comando para um dispositivo específico
async function sendCommand(deviceName, action, value = '') {
    try {
        const commandStr = `CMD ${deviceName} ${action}${value ? ' ' + value : ''}`;
        const response = await sendToGateway(commandStr);
        
        return {
            device: response.device || deviceName,
            success: response.success === true,
            message: response.message || 'Comando executado'
        };
    } catch (error) {
        console.error('Erro ao enviar comando:', error.message);
        return {
            device: deviceName,
            success: false,
            message: error.message
        };
    }
}

// Middleware
app.use(express.static('public'));
app.use(express.json());

// API REST - Lista dispositivos
app.get('/api/devices', async (req, res) => {
    try {
        const devices = await listDevices();
        res.json({ success: true, devices });
    } catch (error) {
        res.status(500).json({ success: false, error: error.message });
    }
});

// API REST - Envia comando
app.post('/api/command', async (req, res) => {
    try {
        const { deviceName, action, value } = req.body;
        
        if (!deviceName || !action) {
            return res.status(400).json({ 
                success: false, 
                error: 'deviceName e action são obrigatórios' 
            });
        }
        
        const result = await sendCommand(deviceName, action, value || '');
        res.json({ success: true, result });
    } catch (error) {
        res.status(500).json({ success: false, error: error.message });
    }
});

// WebSocket - Gerenciamento de conexões
io.on('connection', (socket) => {
    connectedClients++;
    
    // Envia lista inicial de dispositivos
    listDevices().then(devices => {
        socket.emit('devices-update', devices);
    });
    
    // Recebe comando do cliente
    socket.on('send-command', async (data) => {
        const { deviceName, action, value } = data;
        const result = await sendCommand(deviceName, action, value);
        socket.emit('command-response', result);
        
        // Atualiza lista de dispositivos após comando
        setTimeout(async () => {
            const devices = await listDevices();
            io.emit('devices-update', devices);
        }, 500);
    });
    
    // Cliente desconectado
    socket.on('disconnect', () => {
        connectedClients--;
    });
});

// Atualização periódica dos dispositivos para todos os clientes
setInterval(async () => {
    if (connectedClients > 0) {
        const devices = await listDevices();
        
        // Só emite se houver mudanças
        if (JSON.stringify(devices) !== JSON.stringify(lastDeviceList)) {
            lastDeviceList = devices;
            io.emit('devices-update', devices);
        }
    }
}, 3000);

// Inicia o servidor
async function start() {
    console.log('╔═══════════════════════════════════════════════╗');
    console.log('║   🏙️  Smart City - Cliente Web              ║');
    console.log('╚═══════════════════════════════════════════════╝');
    
    const protoLoaded = await loadProto();
    if (!protoLoaded) {
        console.error('⚠️  Aviso: Protobuf não carregado, funcionalidade limitada');
    }
    
    const PORT = process.env.PORT || 3000;
    server.listen(PORT, () => {
        console.log(`  🌐 Interface Web: http://localhost:${PORT}`);
        console.log(`  🔗 Gateway: ${GATEWAY_HOST}:${GATEWAY_PORT}`);
        console.log(`  ⚡ WebSocket ativo para atualizações em tempo real`);
        console.log('╚═══════════════════════════════════════════════╝\n');
    });
}

start().catch(console.error);

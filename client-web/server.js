const express = require('express');
const http = require('http');
const WebSocket = require('ws');
const net = require('net');
const protobuf = require('protobufjs');
const path = require('path');

const app = express();
const server = http.createServer(app);
const wss = new WebSocket.Server({ server });

const GATEWAY_HOST = 'localhost';
const GATEWAY_PORT = 7000;

let protoRoot;
let DeviceList, Command, CommandResponse;

// Carrega o arquivo .proto
async function loadProto() {
    protoRoot = await protobuf.load(path.join(__dirname, '../protobuf/messages.proto'));
    DeviceList = protoRoot.lookupType('DeviceList');
    Command = protoRoot.lookupType('Command');
    CommandResponse = protoRoot.lookupType('CommandResponse');
    console.log('✓ Protocol Buffers carregados');
}

// Conecta ao Gateway e envia comando
function sendToGateway(message) {
    return new Promise((resolve, reject) => {
        const client = new net.Socket();
        let responseBuffer = Buffer.alloc(0);

        client.connect(GATEWAY_PORT, GATEWAY_HOST, () => {
            console.log('→ Conectado ao Gateway');
            client.write(message);
        });

        client.on('data', (data) => {
            responseBuffer = Buffer.concat([responseBuffer, data]);
        });

        client.on('end', () => {
            console.log('← Resposta recebida do Gateway');
            resolve(responseBuffer);
            client.destroy();
        });

        client.on('error', (err) => {
            console.error('✗ Erro na conexão:', err.message);
            reject(err);
        });

        setTimeout(() => {
            client.destroy();
            reject(new Error('Timeout na conexão'));
        }, 5000);
    });
}

// Lista dispositivos
async function listDevices() {
    try {
        const response = await sendToGateway(Buffer.from('LIST\n'));
        const deviceList = DeviceList.decode(response);
        return deviceList.devices;
    } catch (error) {
        console.error('Erro ao listar dispositivos:', error);
        throw error;
    }
}

// Envia comando para dispositivo
async function sendCommand(deviceName, action, value = '') {
    try {
        const command = Command.create({ deviceName, action, value });
        const buffer = Command.encode(command).finish();
        
        const cmdStr = value 
            ? `CMD ${deviceName} ${action} ${value}\n`
            : `CMD ${deviceName} ${action}\n`;
        
        const response = await sendToGateway(Buffer.from(cmdStr));
        
        try {
            const cmdResponse = CommandResponse.decode(response);
            return cmdResponse;
        } catch {
            return { 
                deviceName, 
                success: true, 
                message: response.toString() 
            };
        }
    } catch (error) {
        console.error('Erro ao enviar comando:', error);
        throw error;
    }
}

// Servir arquivos estáticos
app.use(express.static('public'));
app.use(express.json());

// Endpoints REST
app.get('/api/devices', async (req, res) => {
    try {
        const devices = await listDevices();
        res.json({ success: true, devices });
    } catch (error) {
        res.status(500).json({ success: false, error: error.message });
    }
});

app.post('/api/command', async (req, res) => {
    try {
        const { deviceName, action, value } = req.body;
        const response = await sendCommand(deviceName, action, value);
        res.json({ success: true, response });
    } catch (error) {
        res.status(500).json({ success: false, error: error.message });
    }
});

// WebSocket para atualizações em tempo real
wss.on('connection', (ws) => {
    console.log('✓ Cliente WebSocket conectado');

    ws.on('message', async (message) => {
        try {
            const data = JSON.parse(message);
            
            if (data.type === 'LIST_DEVICES') {
                const devices = await listDevices();
                ws.send(JSON.stringify({ type: 'DEVICE_LIST', devices }));
            } 
            else if (data.type === 'SEND_COMMAND') {
                const response = await sendCommand(
                    data.deviceName, 
                    data.action, 
                    data.value
                );
                ws.send(JSON.stringify({ type: 'COMMAND_RESPONSE', response }));
            }
        } catch (error) {
            ws.send(JSON.stringify({ 
                type: 'ERROR', 
                message: error.message 
            }));
        }
    });

    ws.on('close', () => {
        console.log('✗ Cliente WebSocket desconectado');
    });
});

// Inicia o servidor
async function start() {
    await loadProto();
    
    const PORT = process.env.PORT || 3000;
    server.listen(PORT, () => {
        console.log('═══════════════════════════════════════════════');
        console.log('  🌐 Cliente Web - Sistema Cidade Inteligente');
        console.log('═══════════════════════════════════════════════');
        console.log(`  Servidor rodando em: http://localhost:${PORT}`);
        console.log(`  Gateway: ${GATEWAY_HOST}:${GATEWAY_PORT}`);
        console.log('═══════════════════════════════════════════════');
    });
}

start().catch(console.error);
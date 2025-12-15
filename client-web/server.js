const express = require('express');
const net = require('net');
const protobuf = require('protobufjs');
const path = require('path');

const app = express();

const GATEWAY_HOST = 'localhost';
const GATEWAY_PORT = 8000;

let Command, DeviceList, DeviceInfo;

// Carrega o Protocol Buffers
async function loadProto() {
    try {
        // Carrega messages.proto
        const root = await protobuf.load(path.join(__dirname, 'messages.proto'));
        Command = root.lookupType('Command');
        DeviceList = root.lookupType('DeviceList');
        DeviceInfo = root.lookupType('DeviceInfo');
        console.log('✓ Protocol Buffers carregado (messages.proto)');
    } catch (error) {
        console.error('✗ Erro ao carregar .proto:', error.message);
        console.log('  Certifique-se que messages.proto está em client-web/');
        process.exit(1);
    }
}

// Envia comando para o Gateway
function sendToGateway(commandBuffer) {
    return new Promise((resolve, reject) => {
        const socket = new net.Socket();
        let buffer = '';
        
        socket.connect(GATEWAY_PORT, GATEWAY_HOST, () => {
            console.log('→ Conectado ao Gateway');
            console.log('→ Enviando comando:', commandBuffer.length, 'bytes');
            socket.write(commandBuffer);
        });

        socket.on('data', (data) => {
            console.log('← Recebeu dados:', data.length, 'bytes');
            buffer += data.toString('utf8');
            
            // Verifica se recebeu linha completa (termina com \n)
            if (buffer.includes('\n')) {
                try {
                    // Gateway responde em JSON: { device, success, message }
                    const jsonResponse = JSON.parse(buffer.trim());
                    console.log('✓ Resposta JSON:', jsonResponse);
                    socket.end();
                    resolve(jsonResponse);
                } catch (error) {
                    console.log('✗ Erro ao parsear JSON:', error.message);
                    console.log('   Buffer recebido:', buffer);
                    socket.end();
                    reject(new Error('Erro ao decodificar JSON: ' + error.message));
                }
            }
        });

        socket.on('error', (error) => {
            console.error('✗ Erro de conexão:', error.message);
            reject(error);
        });

        socket.on('timeout', () => {
            socket.end();
            reject(new Error('Timeout na conexão'));
        });

        socket.setTimeout(5000);
    });
}

// Lista dispositivos
async function listDevices() {
    console.log('\n📋 === LISTANDO DISPOSITIVOS ===');
    
    // Cria comando LIST
    const command = Command.create({
        deviceName: "",      // Vazio = Lista todos
        action: "LIST",
        value: ""
    });

    // Serializa
    const buffer = Command.encode(command).finish();
    console.log('→ Command serializado:', buffer.length, 'bytes');

    // Envia para o Gateway
    const response = await sendToGateway(buffer);

    // Resposta JSON: { device, success, message }
    if (response.success) {
        console.log('✓ Sucesso:', response.message);
        
        // Parse da mensagem que contém a lista
        // O formato pode variar, ajuste conforme necessário
        const message = response.message || '';
        
        // Se a mensagem contém lista de dispositivos separados por quebra de linha
        const devices = message.split('\n')
            .filter(line => line.trim())
            .map((line, index) => {
                // Tenta parsear formato: "Nome (Tipo) - IP:Port - Estado"
                const match = line.match(/(.+?)\s*\((.+?)\)\s*-\s*(.+?):(\d+)\s*-\s*(.+)/);
                
                if (match) {
                    return {
                        name: match[1].trim(),
                        type: match[2].trim(),
                        ip: match[3].trim(),
                        port: parseInt(match[4]),
                        currentState: match[5].trim(),
                        rawInfo: line
                    };
                }
                
                // Formato simples
                return {
                    name: line.trim(),
                    type: 'Unknown',
                    ip: '-',
                    port: 0,
                    currentState: 'Unknown',
                    rawInfo: line
                };
            });

        console.log('  Dispositivos encontrados:', devices.length);
        return devices;
    }

    throw new Error(response.message || 'Erro ao listar dispositivos');
}

// Envia comando para dispositivo específico
async function sendCommand(deviceName, action, value = '') {
    console.log(`\n📤 === ENVIANDO COMANDO ===`);
    console.log(`  Dispositivo: ${deviceName}`);
    console.log(`  Ação: ${action}`);
    console.log(`  Valor: ${value || '(vazio)'}`);
    
    // Cria comando
    const command = Command.create({
        deviceName: deviceName,
        action: action,
        value: value
    });

    // Serializa
    const buffer = Command.encode(command).finish();
    console.log('→ Command serializado:', buffer.length, 'bytes');

    // Envia para o Gateway
    const response = await sendToGateway(buffer);

    // Resposta JSON: { device, success, message }
    console.log('✓ Comando processado');
    console.log(`  Dispositivo: ${response.device}`);
    console.log(`  Sucesso: ${response.success}`);
    console.log(`  Mensagem: ${response.message}`);

    return {
        device: response.device || deviceName,
        success: response.success === true,
        message: response.message || 'Comando executado'
    };
}

// Middleware
app.use(express.static('public'));
app.use(express.json());

// API REST
app.get('/api/devices', async (req, res) => {
    try {
        const devices = await listDevices();
        res.json({ success: true, devices });
    } catch (error) {
        console.error('Erro:', error.message);
        res.status(500).json({ success: false, error: error.message });
    }
});

app.post('/api/command', async (req, res) => {
    try {
        const { deviceName, action, value } = req.body;
        const result = await sendCommand(deviceName, action, value || '');
        res.json({ success: true, result });
    } catch (error) {
        console.error('Erro:', error.message);
        res.status(500).json({ success: false, error: error.message });
    }
});

// Inicia servidor
async function start() {
    console.log('═══════════════════════════════════════════════');
    console.log('  🌐 Cliente Web - Cidade Inteligente');
    console.log('═══════════════════════════════════════════════');
    
    await loadProto();
    
    const PORT = process.env.PORT || 3000;
    app.listen(PORT, () => {
        console.log(`  Web Interface: http://localhost:${PORT}`);
        console.log(`  Gateway: ${GATEWAY_HOST}:${GATEWAY_PORT}`);
        console.log('═══════════════════════════════════════════════\n');
    });
}

start().catch(console.error);
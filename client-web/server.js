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
const GATEWAY_PORT = 8000;

let protoRoot;
let DeviceList, DeviceInfo, Command, CommandResponse;

// Carrega o arquivo .proto
async function loadProto() {
    try {
        // AJUSTE O CAMINHO PARA SEU ARQUIVO messages.proto
        protoRoot = await protobuf.load(path.join(__dirname, '../protobuf/messages.proto'));
        
        DeviceList = protoRoot.lookupType('DeviceList');
        DeviceInfo = protoRoot.lookupType('DeviceInfo');
        Command = protoRoot.lookupType('Command');
        CommandResponse = protoRoot.lookupType('CommandResponse');
        
        console.log('✓ Protocol Buffers carregados');
    } catch (error) {
        console.error('✗ Erro ao carregar .proto:', error.message);
        console.log('  Ajuste o caminho em server.js linha 19');
        console.log('  Caminho tentado:', path.join(__dirname, '../protobuf/messages.proto'));
        process.exit(1);
    }
}

// Conecta ao Gateway e envia comando
function sendToGateway(data, expectProtobuf = true) {
    return new Promise((resolve, reject) => {
        const client = new net.Socket();
        let responseBuffer = Buffer.alloc(0);

        client.connect(GATEWAY_PORT, GATEWAY_HOST, () => {
            console.log('→ Conectado ao Gateway:', GATEWAY_HOST + ':' + GATEWAY_PORT);
            console.log('→ Enviando:', data.length, 'bytes');
            client.write(data);
        });

        client.on('data', (chunk) => {
            responseBuffer = Buffer.concat([responseBuffer, chunk]);
            console.log('← Chunk recebido:', chunk.length, 'bytes');
        });

        client.on('end', () => {
            console.log('← Conexão encerrada, total:', responseBuffer.length, 'bytes');
            resolve(responseBuffer);
            client.destroy();
        });

        client.on('error', (err) => {
            console.error('✗ Erro TCP:', err.message);
            reject(err);
            client.destroy();
        });

        setTimeout(() => {
            if (!client.destroyed) {
                console.log('⏱ Timeout após 5s');
                if (responseBuffer.length > 0) {
                    console.log('  Retornando dados parciais:', responseBuffer.length, 'bytes');
                    resolve(responseBuffer);
                } else {
                    reject(new Error('Timeout: Gateway não respondeu'));
                }
                client.destroy();
            }
        }, 5000);
    });
}

// Lista dispositivos
async function listDevices() {
    try {
        console.log('\n📋 === LISTANDO DISPOSITIVOS ===');
        
        // Envia comando LIST em texto (como no Gateway PHP original)
        const listCommand = Buffer.from('LIST\n');
        const responseBuffer = await sendToGateway(listCommand);
        
        console.log('← Resposta bruta (primeiros 200 bytes):');
        console.log('  ', responseBuffer.slice(0, 200).toString('hex'));
        
        try {
            // Tenta decodificar como DeviceList
            const deviceList = DeviceList.decode(responseBuffer);
            console.log('✓ Decodificado como DeviceList');
            console.log('  Total de dispositivos:', deviceList.devices.length);
            
            deviceList.devices.forEach((dev, i) => {
                console.log(`  [${i}] ${dev.name} (${dev.type}) - ${dev.ip}:${dev.port}`);
            });
            
            return deviceList.devices;
            
        } catch (decodeError) {
            console.log('✗ Falha ao decodificar como Protobuf:', decodeError.message);
            console.log('  Tentando interpretar como texto...');
            
            const textResponse = responseBuffer.toString('utf8');
            console.log('  Resposta como texto:', textResponse.substring(0, 500));
            
            // Se for texto, retorna array vazio
            return [];
        }
        
    } catch (error) {
        console.error('✗ Erro ao listar dispositivos:', error.message);
        throw error;
    }
}

// Envia comando para dispositivo
async function sendCommand(deviceName, action, value = '') {
    try {
        console.log(`\n📤 === ENVIANDO COMANDO ===`);
        console.log(`  Dispositivo: ${deviceName}`);
        console.log(`  Ação: ${action}`);
        console.log(`  Valor: ${value || '(vazio)'}`);
        
        // Cria mensagem Command
        const command = Command.create({
            deviceName: deviceName,
            action: action,
            value: value
        });
        
        // Serializa
        const commandBuffer = Command.encode(command).finish();
        console.log('→ Command serializado:', commandBuffer.length, 'bytes');
        
        // Envia para o Gateway
        const responseBuffer = await sendToGateway(commandBuffer);
        
        try {
            // Tenta decodificar como CommandResponse
            const response = CommandResponse.decode(responseBuffer);
            console.log('✓ Resposta decodificada:');
            console.log(`  Dispositivo: ${response.deviceName}`);
            console.log(`  Sucesso: ${response.success}`);
            console.log(`  Mensagem: ${response.message}`);
            
            return response;
            
        } catch (decodeError) {
            console.log('✗ Falha ao decodificar resposta:', decodeError.message);
            
            // Resposta de fallback
            return {
                deviceName: deviceName,
                success: true,
                message: 'Comando enviado (resposta não decodificada)'
            };
        }
        
    } catch (error) {
        console.error('✗ Erro ao enviar comando:', error.message);
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

// WebSocket
wss.on('connection', (ws) => {
    console.log('\n✓ Cliente WebSocket conectado');

    ws.on('message', async (message) => {
        try {
            const data = JSON.parse(message);
            console.log('📨 WebSocket recebeu:', data.type);
            
            if (data.type === 'LIST_DEVICES') {
                try {
                    const devices = await listDevices();
                    ws.send(JSON.stringify({ 
                        type: 'DEVICE_LIST', 
                        devices: devices 
                    }));
                } catch (error) {
                    ws.send(JSON.stringify({ 
                        type: 'ERROR', 
                        message: 'Erro ao listar: ' + error.message 
                    }));
                }
            } 
            else if (data.type === 'SEND_COMMAND') {
                try {
                    const response = await sendCommand(
                        data.deviceName, 
                        data.action, 
                        data.value || ''
                    );
                    ws.send(JSON.stringify({ 
                        type: 'COMMAND_RESPONSE', 
                        response: response 
                    }));
                } catch (error) {
                    ws.send(JSON.stringify({ 
                        type: 'ERROR', 
                        message: 'Erro ao enviar comando: ' + error.message 
                    }));
                }
            }
        } catch (error) {
            console.error('✗ Erro no WebSocket:', error);
            ws.send(JSON.stringify({ 
                type: 'ERROR', 
                message: 'Erro: ' + error.message 
            }));
        }
    });

    ws.on('close', () => {
        console.log('✗ Cliente WebSocket desconectado');
    });
    
    ws.on('error', (error) => {
        console.error('✗ Erro WebSocket:', error);
    });
});

// Inicia o servidor
async function start() {
    console.log('═══════════════════════════════════════════════');
    console.log('  🌐 Cliente Web - Cidade Inteligente');
    console.log('═══════════════════════════════════════════════');
    
    await loadProto();
    
    const PORT = process.env.PORT || 3000;
    server.listen(PORT, () => {
        console.log(`  Web: http://localhost:${PORT}`);
        console.log(`  Gateway: ${GATEWAY_HOST}:${GATEWAY_PORT}`);
        console.log('═══════════════════════════════════════════════');
        console.log('  Aguardando conexões...\n');
    });
}

start().catch(console.error);
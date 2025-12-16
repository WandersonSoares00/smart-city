# 📚 Guia de Comandos Avançados

## 🚦 Semáforo (Traffic Light)

### Comandos Básicos
```javascript
// Mudar cor do semáforo
SET_LIGHT RED
SET_LIGHT YELLOW
SET_LIGHT GREEN

// Mudar modo de operação
SET_MODE AUTO
SET_MODE MANUAL

// Configurar tempo de cada fase (em segundos)
SET_TIME RED:20
SET_TIME YELLOW:5
SET_TIME GREEN:15

// Obter status
GET_STATUS
```

### Exemplos no Modal
- **Dispositivo**: Semaforo-Centro
- **Ação**: SET_LIGHT
- **Valor**: RED

## 📹 Câmera

### Comandos Básicos
```javascript
// Ligar/Desligar
TURN_ON
TURN_OFF

// Resolução
SET_RESOLUTION 4K
SET_RESOLUTION 1080p
SET_RESOLUTION 720p

// FPS (Frames por segundo)
SET_FPS 60
SET_FPS 30
SET_FPS 24

// Pan-Tilt-Zoom (formato: pan:tilt:zoom)
SET_PTZ 90:45:2
SET_PTZ 0:0:1

// Visão Noturna
SET_NIGHT_VISION ON
SET_NIGHT_VISION OFF

// Status
GET_STATUS
GET_RECORDING_STATUS
```

### Exemplos no Modal
- **Dispositivo**: Camera-Praca
- **Ação**: SET_PTZ
- **Valor**: 180:30:3

## 💡 Poste de Iluminação

### Comandos Básicos
```javascript
// Ligar/Desligar
TURN_ON
TURN_OFF

// Brilho (0-100)
SET_BRIGHTNESS 100
SET_BRIGHTNESS 50
SET_BRIGHTNESS 25

// Modo de operação
SET_MODE AUTO
SET_MODE MANUAL
SET_MODE DIMMING

// Cor (se RGB)
SET_COLOR WHITE
SET_COLOR WARM
SET_COLOR COOL

// Status
GET_STATUS
GET_POWER_CONSUMPTION
```

### Exemplos no Modal
- **Dispositivo**: Poste-Rua-1
- **Ação**: SET_BRIGHTNESS
- **Valor**: 75

## 🌡️ Sensor de Temperatura

### Comandos Básicos
```javascript
// Obter leitura
GET_READING

// Configurar intervalo (segundos)
SET_INTERVAL 10
SET_INTERVAL 30
SET_INTERVAL 60

// Mudar unidade
SET_UNIT CELSIUS
SET_UNIT FAHRENHEIT

// Status detalhado
GET_STATUS

// Configurar alertas
SET_ALERT_MIN 0
SET_ALERT_MAX 35
```

### Exemplos no Modal
- **Dispositivo**: Temp-Centro
- **Ação**: SET_INTERVAL
- **Valor**: 15

## 💧 Sensor de Umidade

### Comandos Básicos
```javascript
// Obter leitura
GET_READING

// Configurar intervalo
SET_INTERVAL 20

// Configurar alertas
SET_ALERT_MIN 30
SET_ALERT_MAX 80

// Status
GET_STATUS
```

## 🌫️ Sensor de Qualidade do Ar

### Comandos Básicos
```javascript
// Leitura simples
GET_READING

// Leitura detalhada (todos os parâmetros)
GET_DETAILED

// Configurar intervalo
SET_INTERVAL 60

// Alertas
SET_ALERT_THRESHOLD 150

// Status
GET_STATUS
```

## 🔊 Sensor de Ruído

### Comandos Básicos
```javascript
// Leitura atual
GET_READING

// Leitura de pico
GET_PEAK

// Configurar intervalo
SET_INTERVAL 5

// Configurar limiar de alerta (dB)
SET_ALERT_THRESHOLD 80

// Status
GET_STATUS
```

## 🎯 Comandos Genéricos

Todos os dispositivos suportam:
```javascript
// Status do dispositivo
GET_STATUS

// Reiniciar dispositivo
RESTART

// Informações do sistema
GET_INFO

// Ping de conectividade
PING
```

## 📝 Formato dos Comandos

No Gateway, os comandos seguem o formato:
```
CMD <nome-do-dispositivo> <ação> [valor-opcional]
```

Exemplos:
```bash
CMD Semaforo-Centro SET_LIGHT RED
CMD Camera-Praca SET_RESOLUTION 4K
CMD Temp-Centro SET_INTERVAL 30
CMD Poste-Rua-1 SET_BRIGHTNESS 80
```

## 🔄 Comandos via WebSocket

No frontend, use:
```javascript
socket.emit('send-command', {
    deviceName: 'Semaforo-Centro',
    action: 'SET_LIGHT',
    value: 'GREEN'
});
```

## 🌐 Comandos via REST API

```bash
curl -X POST http://localhost:3000/api/command \
  -H "Content-Type: application/json" \
  -d '{
    "deviceName": "Semaforo-Centro",
    "action": "SET_LIGHT",
    "value": "RED"
  }'
```

## 💡 Dicas

1. **Case Sensitive**: Os comandos são case-insensitive, mas recomenda-se usar UPPERCASE
2. **Valores Opcionais**: Alguns comandos não precisam de valor (ex: TURN_ON, TURN_OFF)
3. **Feedback**: Sempre aguarde a resposta antes de enviar outro comando
4. **Modo Manual**: Alguns comandos só funcionam em modo MANUAL (ex: SET_LIGHT)
5. **Validação**: O dispositivo validará os comandos e retornará erro se inválido

## 🚨 Tratamento de Erros

Possíveis erros:
- **Device not found**: Dispositivo não existe ou não está conectado
- **Invalid command**: Comando não reconhecido
- **Invalid value**: Valor fora do intervalo permitido
- **Mode required**: Comando requer modo específico (AUTO/MANUAL)
- **Connection timeout**: Falha ao conectar ao dispositivo

---

**Explore e experimente diferentes comandos! 🚀**

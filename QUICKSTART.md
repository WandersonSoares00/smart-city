# 🚀 Guia Rápido de Início

## Passo 1: Iniciar o Gateway
```bash
cd gateway
php run.php
```

## Passo 2: Iniciar os Dispositivos

### Sensor de Temperatura
```bash
cd devices
php bin/run-temperature-sensor.php
```

### Semáforo
```bash
cd devices
php bin/run-traffic-light.php
```

### Câmera
```bash
cd devices
php bin/run-camera.php
```

## Passo 3: Iniciar o Cliente Web
```bash
cd client-web
npm start
```

## Passo 4: Acessar a Interface
Abra o navegador em: **http://localhost:3000**

---

## 🎮 Comandos Rápidos

### Semáforo
- 🔴 **Vermelho**: SET_LIGHT RED
- 🟢 **Verde**: SET_LIGHT GREEN
- 🤖 **Automático**: SET_MODE AUTO

### Câmera
- 📹 **Ligar**: TURN_ON
- ⏸️ **Desligar**: TURN_OFF
- 🎬 **4K**: SET_RESOLUTION 4K

### Sensor
- 📊 **Leitura**: GET_READING
- ℹ️ **Status**: GET_STATUS
- ⏱️ **Intervalo**: SET_INTERVAL 30

---

## 📱 Recursos da Interface

✅ **Atualização em tempo real** via WebSocket
✅ **Filtros** por tipo de dispositivo
✅ **Ações rápidas** específicas por dispositivo
✅ **Modal de comando** para operações avançadas
✅ **Notificações** de feedback visual
✅ **Responsivo** para mobile e desktop

---

**Pronto! Sua cidade inteligente está operacional! 🏙️**

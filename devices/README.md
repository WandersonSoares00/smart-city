# Dispositivos Smart City

Este diretório contém a implementação dos dispositivos simulados (sensores e atuadores) para o projeto Smart City.

## Pré-requisitos

Certifique-se de ter instalado as dependências do projeto:

```bash
composer install
```

## Como Rodar

Cada dispositivo pode ser executado individualmente via linha de comando. Eles se conectarão automaticamente ao Gateway se ele estiver rodando.

### 🚦 Semáforo (Traffic Light)

Simula um semáforo de trânsito que pode ser controlado remotamente.

**Uso:**
```bash
php bin/run-traffic-light.php [nome] [porta]
```

**Exemplo:**
```bash
php bin/run-traffic-light.php "Semaforo-Av-Principal" 7102
```

---

### 📷 Câmera de Vigilância (Camera)

Simula uma câmera de segurança que pode ser ligada/desligada.

**Uso:**
```bash
php bin/run-camera.php [nome] [porta]
```

**Exemplo:**
```bash
php bin/run-camera.php "Camera-Praca-Central" 7103
```

---

### 🌡️ Sensor de Temperatura (Temperature Sensor)

Simula um sensor que envia leituras de temperatura periodicamente para o Gateway.

**Uso:**
```bash
php bin/run-temperature-sensor.php [nome] [porta] [intervalo_envio]
```

**Exemplo:**
```bash
# Roda o sensor na porta 7104 enviando dados a cada 5 segundos
php bin/run-temperature-sensor.php "Temp-Centro" 7104 5
```

## Estrutura

- `src/BaseDevice.php`: Classe base com lógica de conexão e descoberta.
- `src/BaseSensor.php`: Classe base para sensores (envio UDP periódico).
- `src/Actuators/`: Implementações de atuadores (Semáforo, Câmera).
- `src/Sensors/`: Implementações de sensores (Temperatura).

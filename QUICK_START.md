# 🏙️ Smart City - Guia de Inicialização

## ⚡ Forma Rápida (Recomendado)

### Windows - Usando Batch

1. Abra um `cmd` ou `PowerShell` na raiz do projeto
2. Execute:
```bash
start-all.bat
```

Pronto! O script abrirá 5 janelas de terminal automaticamente:
- Gateway
- Sensor de Temperatura
- Câmera
- Semáforo
- Client Web

### Windows - Usando PowerShell

1. Abra um `PowerShell` na raiz do projeto
2. Execute:
```powershell
.\start-all.ps1
```

## 🔧 Forma Manual

Se preferir iniciar cada componente separadamente:

### Terminal 1 - Gateway
```bash
php gateway/run.php
```

### Terminal 2 - Sensor de Temperatura
```bash
php devices/bin/run-temperature-sensor.php
```

### Terminal 3 - Câmera
```bash
php devices/bin/run-camera.php
```

### Terminal 4 - Semáforo
```bash
php devices/bin/run-traffic-light.php
```

### Terminal 5 - Client Web
```bash
cd client-web
npm start
```

## 🌐 Acessar a Interface

Após iniciar todos os serviços, abra o navegador e acesse:
```
http://localhost:3000
```

## 📊 O que esperar

- **Inicial**: Interface vazia (aguarde 5-10 segundos)
- **Após conexão**: Aparecem os 3 dispositivos automaticamente
- **Em tempo real**: Dados de sensores e estado dos atuadores atualizam automaticamente

## ⏹️ Parar os Serviços

Feche cada janela de terminal ou pressione `Ctrl+C` em cada uma.

## 🔄 Remover Dispositivos

Para remover um dispositivo da lista, simplesmente feche a janela do terminal onde ele está rodando. Após 10-15 segundos, ele será removido da interface automaticamente.

---

**Smart City** - Sistema de Gerenciamento de Cidade Inteligente

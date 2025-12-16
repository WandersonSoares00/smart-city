# 🏙️ Smart City - Cliente Web

Interface web moderna e responsiva para gerenciamento de dispositivos IoT em uma cidade inteligente.

## 📋 Características

### 🎨 Interface Moderna
- **Design Dark Mode**: Interface elegante com gradientes e animações suaves
- **Responsivo**: Funciona perfeitamente em desktop, tablet e mobile
- **Tempo Real**: Atualizações automáticas via WebSocket
- **Notificações Toast**: Feedback visual de todas as ações

### 🔌 Conectividade
- **WebSocket**: Comunicação bidirecional em tempo real com o servidor
- **REST API**: Endpoints para listar dispositivos e enviar comandos
- **Auto-reconexão**: Reconecta automaticamente em caso de queda

### 🎮 Controles Inteligentes

#### Semáforos (Traffic Light)
- 🔴 Vermelho / 🟡 Amarelo / 🟢 Verde
- 🤖 Modo Automático
- ✋ Modo Manual
- ⚙️ Configurações avançadas

#### Câmeras
- 📹 Ligar/Desligar
- 🎬 Resolução 4K
- 📺 Resolução 1080p
- 🎥 Controles PTZ
- 🌙 Visão noturna

#### Postes de Iluminação
- 💡 Ligar/Desligar
- ☀️ Brilho 100%
- 🌤️ Brilho 50%
- 🌙 Modo Noturno
- 🤖 Modo Automático

#### Sensores
- 📊 Leitura em tempo real
- ℹ️ Status detalhado
- ⏱️ Configurar intervalo de leitura
- 🌡️ Temperatura
- 💧 Umidade
- 🌫️ Qualidade do ar
- 🔊 Nível de ruído

### 🔍 Filtros
- **Todos**: Exibe todos os dispositivos
- **Sensores**: Apenas dispositivos sensores
- **Atuadores**: Apenas atuadores (câmeras, semáforos, etc)

## 🚀 Como Usar

### 1. Instalação

```bash
cd client-web
npm install
```

### 2. Configuração

Configure as variáveis de ambiente (opcional):

```bash
# .env (na raiz do client-web)
PORT=3000
GATEWAY_HOST=localhost
GATEWAY_PORT=8000
```

### 3. Iniciar o Servidor

```bash
npm start
```

O servidor iniciará em: `http://localhost:3000`

### 4. Acessar a Interface

Abra o navegador e acesse: `http://localhost:3000`

## 🎯 Funcionalidades

### Dashboard Principal
- Visualização em grid de todos os dispositivos conectados
- Cards individuais com informações detalhadas
- Status de conexão em tempo real
- Contador de dispositivos ativos

### Ações Rápidas
Cada tipo de dispositivo possui botões de ação rápida específicos para operações comuns.

### Modal de Comando Avançado
Para operações mais complexas, use o botão "⚙️ Avançado" para abrir o modal de comando personalizado.

### Atalhos de Teclado
- `ESC` - Fecha o modal de comando
- `Enter` - Envia o comando (quando o modal está aberto)
- `R` - Atualiza a lista de dispositivos

### Notificações
Todas as ações geram notificações visuais:
- ✅ **Sucesso**: Comando executado com sucesso
- ❌ **Erro**: Falha na execução
- ℹ️ **Info**: Informações gerais
- ⚠️ **Aviso**: Alertas

## 📡 Comunicação com o Gateway

### REST API

#### GET /api/devices
Lista todos os dispositivos conectados ao Gateway.

**Resposta:**
```json
{
  "success": true,
  "devices": [
    {
      "name": "Semaforo-Centro",
      "type": "TRAFFIC_LIGHT",
      "ip": "192.168.1.100",
      "port": 5001,
      "currentState": "RED"
    }
  ]
}
```

#### POST /api/command
Envia um comando para um dispositivo específico.

**Body:**
```json
{
  "deviceName": "Semaforo-Centro",
  "action": "SET_LIGHT",
  "value": "GREEN"
}
```

**Resposta:**
```json
{
  "success": true,
  "result": {
    "device": "Semaforo-Centro",
    "success": true,
    "message": "Luz alterada para GREEN"
  }
}
```

### WebSocket Events

#### Client → Server
- `send-command`: Envia comando para dispositivo
  ```javascript
  socket.emit('send-command', {
    deviceName: 'Poste-Rua-1',
    action: 'TURN_ON',
    value: ''
  });
  ```

#### Server → Client
- `devices-update`: Atualização da lista de dispositivos
- `command-response`: Resposta de comando enviado

## 🛠️ Tecnologias Utilizadas

- **Backend**:
  - Node.js
  - Express.js
  - Socket.IO (WebSocket)
  - Protocol Buffers (Protobuf)

- **Frontend**:
  - HTML5
  - CSS3 (Grid, Flexbox, Animations)
  - Vanilla JavaScript (ES6+)
  - Socket.IO Client

## 🎨 Personalização

### Cores
As cores podem ser personalizadas no arquivo `styles.css` através das variáveis CSS:

```css
:root {
    --primary: #6366f1;
    --success: #10b981;
    --danger: #ef4444;
    /* ... */
}
```

### Ícones de Dispositivos
Edite a função `getDeviceIcon()` em `app.js` para personalizar os ícones.

### Ações por Tipo de Dispositivo
Edite a função `renderDeviceActions()` em `app.js` para adicionar novos tipos de dispositivos e ações.

## 📱 Responsividade

A interface é totalmente responsiva e se adapta a diferentes tamanhos de tela:

- **Desktop**: Grid com múltiplas colunas
- **Tablet**: Grid com 2 colunas
- **Mobile**: Grid com 1 coluna

## 🔄 Atualização Automática

Os dispositivos são atualizados automaticamente a cada 3 segundos através do WebSocket, garantindo que você sempre veja o estado mais recente.

## 🐛 Troubleshooting

### Dispositivos não aparecem
1. Verifique se o Gateway está rodando
2. Confirme a porta correta (padrão: 8000)
3. Verifique os logs do console do navegador

### WebSocket não conecta
1. Certifique-se que o servidor Node.js está rodando
2. Verifique se a porta está disponível
3. Desabilite bloqueadores de script se necessário

### Comandos não funcionam
1. Verifique se o dispositivo está online
2. Confirme que o Gateway está processando comandos
3. Veja os logs no console do servidor

## 📄 Licença

MIT

## 👥 Autor

Projeto desenvolvido para a disciplina de Sistemas Distribuídos

---

**Boa sorte com seu projeto de Cidade Inteligente! 🏙️✨**

# 🌐 Cliente Web - Sistema Cidade Inteligente

Cliente web com interface gráfica para controlar e monitorar dispositivos inteligentes através do Gateway.

## 📁 Estrutura do Projeto

```
client-web/
├── server.js              # Backend Node.js
├── package.json          # Dependências
├── public/
│   └── index.html        # Interface web
└── README.md
```

## 🚀 Instalação

### 1. Instalar Node.js
Certifique-se de ter o Node.js instalado (v14 ou superior):
```bash
node --version
```

### 2. Criar a estrutura do projeto
```bash
mkdir -p client-web/public
cd client-web
```

### 3. Instalar dependências
```bash
npm install
```

## ⚙️ Configuração

### Ajustar caminho do Protocol Buffers
Edite o `server.js` na linha 14 para apontar para o seu arquivo `messages.proto`:

```javascript
protoRoot = await protobuf.load(path.join(__dirname, '../protobuf/messages.proto'));
```

Ajuste o caminho conforme sua estrutura:
- Se o `.proto` está na raiz: `'../messages.proto'`
- Se está em uma pasta específica: `'../protobuf/messages.proto'`

### Configurar endereço do Gateway
No `server.js`, linhas 10-11:

```javascript
const GATEWAY_HOST = 'localhost';  // Altere se o Gateway estiver em outro host
const GATEWAY_PORT = 7000;         // Porta do seu Gateway
```

## 🎮 Como Usar

### 1. Iniciar o Gateway
Primeiro, certifique-se de que seu Gateway PHP está rodando:
```bash
cd gateway
php run.php
```

### 2. Iniciar os Dispositivos
Execute seus dispositivos inteligentes em terminais separados.

### 3. Iniciar o Cliente Web
```bash
cd client-web
npm start
```

### 4. Acessar a Interface
Abra seu navegador em:
```
http://localhost:3000
```

## 🎯 Funcionalidades

### ✅ Visualização de Dispositivos
- Lista todos os dispositivos conectados ao Gateway
- Mostra tipo, IP, porta e estado atual
- Atualização automática a cada 10 segundos

### ✅ Controle de Postes
- 🔆 Ligar/Desligar
- 💡 Ajustar brilho (0-100)

### ✅ Controle de Semáforos
- 🔴 Definir luz (Vermelho/Amarelo/Verde)
- ⚙️ Modo automático/manual

### ✅ Controle de Câmeras
- ▶️ Ligar/Desligar
- 📹 Ajustar resolução (HD/4K)

### ✅ Controle de Sensores
- 📊 Obter leituras
- ⏱️ Ajustar intervalo de medição

## 🔧 Comandos Suportados

O cliente envia comandos no formato do Gateway:

```
CMD <dispositivo> <ação> [valor]
```

Exemplos:
- `CMD Poste-Rua-1 TURN_ON`
- `CMD Poste-Rua-1 SET_BRIGHTNESS 80`
- `CMD Semaforo-Centro SET_LIGHT RED`
- `CMD Camera-Praca SET_RESOLUTION 4K`
- `CMD Temp-Centro GET_READING`

## 🐛 Troubleshooting

### Erro "Cannot find module 'protobufjs'"
```bash
npm install
```

### "Erro ao conectar com o Gateway"
Verifique se:
1. O Gateway está rodando na porta 7000
2. O endereço `GATEWAY_HOST` está correto
3. Não há firewall bloqueando a conexão

### "Nenhum dispositivo encontrado"
Verifique se:
1. Os dispositivos estão executando
2. Os dispositivos se registraram no Gateway
3. O Gateway está respondendo ao comando `LIST`

### Página não carrega
Verifique se:
1. A porta 3000 está disponível
2. O arquivo `index.html` está em `public/index.html`
3. Tente mudar a porta: `PORT=8080 npm start`

## 📊 Status de Conexão

A interface mostra o status em tempo real:
- 🟢 **Conectado**: Comunicação ativa com o Gateway
- 🔴 **Desconectado**: Sem conexão, tentando reconectar

## 🔄 Atualização Automática

- **Manual**: Botão "🔄 Atualizar Dispositivos"
- **Automática**: A cada 10 segundos
- **Após comandos**: Atualiza automaticamente após enviar um comando

## 💡 Dicas

1. **Mantenha o Gateway rodando** antes de iniciar o cliente
2. **Use Chrome ou Firefox** para melhor compatibilidade
3. **Abra o console do navegador** (F12) para ver logs detalhados
4. **Para desenvolvimento**, use `npm run dev` para auto-reload

## 🎨 Personalização

### Alterar porta do servidor
```bash
PORT=8080 npm start
```

### Customizar interface
Edite o arquivo `public/index.html` para alterar:
- Cores e estilos (tag `<style>`)
- Layout dos cards
- Ações disponíveis por dispositivo

## 📝 Notas

- O cliente usa **WebSockets** para comunicação em tempo real
- O backend gerencia a conexão TCP com o Gateway
- Protocol Buffers é usado para serialização de mensagens
- Interface responsiva, funciona em desktop e mobile

## 🤝 Integração com o Sistema

```
┌─────────────┐      WebSocket       ┌──────────────┐
│   Browser   │ ←─────────────────→ │  Cliente Web │
└─────────────┘                      │  (Node.js)   │
                                     └──────┬───────┘
                                            │ TCP
                                            │ Protocol Buffers
                                     ┌──────▼───────┐
                                     │   Gateway    │
                                     │   (PHP)      │
                                     └──────┬───────┘
                                            │ TCP
                        ┌───────────────────┼───────────────────┐
                        │                   │                   │
                   ┌────▼─────┐      ┌─────▼────┐      ┌──────▼─────┐
                   │  Poste   │      │ Semáforo │      │  Câmera    │
                   └──────────┘      └──────────┘      └────────────┘
```

## 📄 Licença

MIT
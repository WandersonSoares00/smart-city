# 🌐 Cliente Web - Sistema Cidade Inteligente

Cliente web simplificado que se comunica com o Gateway usando Protocol Buffers, idêntico ao `client.py`.

## 📁 Estrutura

```
client-web/
├── server.js           # Backend Node.js (replica client.py)
├── smartcity.proto     # Definição Protocol Buffers
├── package.json        # Dependências
├── public/
│   └── index.html      # Interface web
└── README.md
```

## 🚀 Instalação Rápida

```bash
# 1. Criar pasta e entrar
mkdir client-web
cd client-web

# 2. Criar subpasta public
mkdir public

# 3. Copiar os arquivos:
#    - server.js (backend)
#    - smartcity.proto (protocol buffers)
#    - package.json (dependências)
#    - public/index.html (interface)

# 4. Instalar dependências
npm install

# 5. Iniciar
npm start
```

## 📋 Arquivos Necessários

### 1. `smartcity.proto`
Coloque este arquivo na raiz de `client-web/`:

```protobuf
syntax = "proto3";

message Command {
  string target_id = 1;
  string action = 2;
  string value = 3;
}

message Response {
  string status = 1;
  string message = 2;
  repeated string devices_list = 3;
}

message Message {
  int32 id = 1;
  string source_id = 2;
  Command command = 3;
  Response response = 4;
}
```

## ⚙️ Como Funciona

O cliente web replica **exatamente** o comportamento do `client.py`:

### Python (client.py):
```python
msg = pb.Message()
msg.id = 1
msg.source_id = "CLIENTE_ADMIN"
msg.command.target_id = ""
msg.command.action = "LIST"
s.send(msg.SerializeToString())
```

### Node.js (server.js):
```javascript
const msg = Message.create({
    id: 1,
    source_id: "CLIENT_WEB",
    command: {
        target_id: "",
        action: "LIST",
        value: ""
    }
});
const buffer = Message.encode(msg).finish();
socket.write(buffer);
```

## 🎮 Como Usar

### 1. Inicie o Gateway
```bash
cd gateway
php run.php
```

### 2. Inicie o Cliente Web
```bash
cd client-web
npm start
```

### 3. Acesse a Interface
```
http://localhost:3000
```

## 🔧 Funcionalidades

### ✅ Listar Dispositivos
- Botão: **"🔄 Atualizar Dispositivos"**
- Equivalente ao comando `LIST` do Python
- Mostra todos os dispositivos conectados

### ✅ Enviar Comandos
Cada dispositivo tem formulário com:
- **Target ID**: ID do dispositivo (ex: `TL_01`)
- **Action**: Ação a executar (ex: `SET_COLOR`)
- **Value**: Valor opcional (ex: `RED`, `GREEN`)

### ✅ Comandos Rápidos
Botões pré-configurados:
- 🔴 **TL_01 RED** - Semáforo vermelho
- 🟢 **TL_01 GREEN** - Semáforo verde
- 📋 **LIST** - Atualizar lista

## 📡 Comunicação

```
┌──────────────┐                    ┌──────────────┐
│   Browser    │ ◄── HTTP/JSON ──► │  server.js   │
└──────────────┘                    └──────┬───────┘
                                           │
                                           │ TCP
                                           │ Protocol Buffers
                                           │ (Igual client.py)
                                           │
                                    ┌──────▼───────┐
                                    │   Gateway    │
                                    │   (PHP)      │
                                    └──────────────┘
```

## 🐛 Troubleshooting

### "Cannot find module 'protobufjs'"
```bash
npm install
```

### "Erro ao carregar .proto"
Verifique se `smartcity.proto` está em `client-web/smartcity.proto`

### "Erro de conexão"
Verifique:
1. Gateway rodando na porta **8000**
2. Endereço correto no `server.js` (linha 7-8)

### "Nenhum dispositivo encontrado"
- Gateway está rodando?
- Dispositivos estão conectados?
- Teste com o `client.py` primeiro

## 🔍 Debug

O servidor mostra logs detalhados:
```
→ Conectado ao Gateway
← Recebeu resposta: 156 bytes
Status: OK
Mensagem: Lista de dispositivos
Dispositivos: 3
```

Abra o console do navegador (F12) para ver logs do frontend.

## ⚡ Diferenças do client.py

| Aspecto | client.py | client-web |
|---------|-----------|------------|
| Interface | Terminal CLI | Web Browser |
| Linguagem | Python | Node.js + HTML |
| Protocolo | TCP + Protobuf | TCP + Protobuf |
| Formato mensagem | **Idêntico** | **Idêntico** |
| WebSocket | ❌ | ❌ |
| Complexidade | Simples | Simples |

## 📝 Exemplos de Comandos

### Listar dispositivos:
- Target ID: *(vazio)*
- Action: `LIST`
- Value: *(vazio)*

### Controlar semáforo:
- Target ID: `TL_01`
- Action: `SET_COLOR`
- Value: `RED` ou `GREEN`

### Ligar poste:
- Target ID: `Poste-Rua-1`
- Action: `TURN_ON`
- Value: *(vazio)*

### Ajustar câmera:
- Target ID: `Camera-Praca`
- Action: `SET_RESOLUTION`
- Value: `4K`

## 🎨 Personalização

### Alterar porta do servidor web:
```bash
PORT=8080 npm start
```

### Alterar Gateway:
Edite `server.js` linhas 7-8:
```javascript
const GATEWAY_HOST = 'localhost';
const GATEWAY_PORT = 8000;
```

## ✅ Checklist de Instalação

- [ ] Node.js instalado (v14+)
- [ ] Pasta `client-web/` criada
- [ ] Pasta `client-web/public/` criada
- [ ] Arquivo `smartcity.proto` na raiz
- [ ] Arquivo `server.js` na raiz
- [ ] Arquivo `package.json` na raiz
- [ ] Arquivo `index.html` em `public/`
- [ ] `npm install` executado
- [ ] Gateway rodando na porta 8000
- [ ] `npm start` executado
- [ ] Browser acessando `localhost:3000`

## 📄 Licença

MIT
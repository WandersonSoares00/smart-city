# ﻿Descrição do Projeto

Este projeto implementa uma aplicação de Cidade Inteligente, composta por um Gateway central, múltiplos dispositivos simulados (sensores e atuadores) e um cliente web para interação com o sistema.

O Gateway atua como o núcleo da aplicação, sendo responsável por descobrir automaticamente os dispositivos disponíveis, receber dados enviados por sensores, encaminhar comandos para atuadores e centralizar o estado geral da cidade.

Os dispositivos representam elementos comuns de uma cidade inteligente, como sensores ambientais, câmeras e semáforos. Cada dispositivo opera de forma independente, mas se integra ao sistema através do Gateway.

O cliente web permite que usuários visualizem os dispositivos disponíveis, consultem estados e enviem comandos, como acionar atuadores ou solicitar dados de sensores.

A aplicação foi projetada para funcionar de forma distribuída, com os serviços isolados e executados de maneira integrada.

## Funcionalidades Principais

- Descoberta automática de dispositivos
- Registro e gerenciamento do estado dos dispositivos
- Recebimento contínuo de dados de sensores
- Envio de comandos para atuadores
- Armazenamento de imagens capturadas por câmeras
- Registro de ações e comandos executados
- Interface web para interação com o sistema

## Tecnologias utilizadas
- PHP 8.4
- Node.js
- Protocol Buffers
- MySQL
- Amazon DynamoDB
- MinIO
- Docker e Docker Compose

## Como Executar a Aplicação

- Clone o repositório do projeto.
- A partir da raiz do projeto, execute o comando:

```docker compose up --build```

Aguarde até que todos os serviços estejam em execução.

## Acessos Padrão

Interface Web: http://localhost:3000

Console de armazenamento de arquivos: http://localhost:9001






# 🎨 Interface Minimalista - Smart City

## Design System

### Paleta de Cores
- **Preto**: `#000000` - Background principal
- **Branco**: `#FFFFFF` - Texto e bordas
- **Cinzas**: Apenas para estados desabilitados

### Tipografia
- **Fonte**: Inter (Google Fonts)
- **Pesos**: 300, 400, 500, 600, 700, 900
- **Características**:
  - Títulos grandes e bold (font-weight: 900)
  - Lettering spacing negativo em títulos (-0.05em)
  - Lettering spacing positivo em labels (0.1em - 0.2em)
  - Texto uppercase para hierarquia

### Elementos Visuais
- ✅ Bordas sólidas de 1px
- ✅ Sem sombras
- ✅ Sem gradientes
- ✅ Sem border-radius (design reto)
- ✅ Ícones SVG inline (sem emojis)
- ✅ Hover states com inversão de cores

## Estrutura da Interface

### Header
```
┌──────────────────────────────────────────────────┐
│ SMART CITY                    ● CONNECTED    [0] │
│ CONTROL PANEL                                    │
└──────────────────────────────────────────────────┘
```

### Controls Bar
```
┌──────────────────────────────────────────────────┐
│ [🔄 REFRESH]         [ALL][SENSORS][ACTUATORS]  │
└──────────────────────────────────────────────────┘
```

### Device Cards
```
┌────────────────────────────────┐
│ SEMAFORO-CENTRO      [ONLINE]  │
│ TRAFFIC_LIGHT                  │
├────────────────────────────────┤
│ IP ADDRESS    192.168.1.100    │
│ PORT          5001             │
│ STATE         RED              │
├────────────────────────────────┤
│ [RED][YELLOW][GREEN]           │
│ [AUTO][MANUAL][ADVANCED]       │
└────────────────────────────────┘
```

## Características

### ✅ Minimalismo
- Apenas preto e branco
- Sem decorações desnecessárias
- Hierarquia visual clara através de tipografia

### ✅ Tipografia
- Títulos: 2.5rem / 900 weight
- Subtítulos: 1.5rem / 900 weight
- Labels: 0.75rem / 700 weight / uppercase
- Corpo: 0.875rem / 500 weight

### ✅ Ícones SVG
- Biblioteca de ícones inline
- Stroke width: 2px
- Cor: currentColor (herda do texto)
- Tamanho consistente: 16x16px

### ✅ Interatividade
- Hover inverte cores (preto↔branco)
- Transições suaves (0.2s)
- Estados visuais claros
- Feedback imediato

### ✅ Grid System
- CSS Grid responsivo
- Auto-fill com minmax(400px, 1fr)
- Gap consistente de 2rem
- Mobile-first approach

## Componentes

### Botões
- Borda de 1px sólida
- Padding: 0.875rem 2rem
- Uppercase + letter-spacing
- Hover inverte fundo/texto

### Cards
- Borda de 1px sólida
- Padding interno: 2rem
- Hover inverte todas as cores do card
- Informações em grid interno

### Inputs
- Borda de 1px sólida
- Background preto
- Focus inverte cores
- Placeholder cinza discreto

### Toasts
- Borda de 1px sólida
- Slide-in da direita
- Auto-dismiss em 4s
- Títulos uppercase

## Responsividade

### Desktop (>768px)
- Grid: 3-4 colunas
- Header em linha
- Filtros em linha

### Tablet (768px)
- Grid: 2 colunas
- Header em coluna
- Filtros em linha

### Mobile (<480px)
- Grid: 1 coluna
- Tudo empilhado
- Botões full-width

## Acessibilidade

- ✅ Contraste máximo (preto/branco)
- ✅ Fontes grandes e legíveis
- ✅ Atalhos de teclado (ESC, Enter, R)
- ✅ Focus states visíveis
- ✅ Hierarquia semântica HTML

## Comandos Disponíveis

### npm start
Inicia o servidor em http://localhost:3000

### Atalhos de Teclado
- `R` - Refresh devices
- `ESC` - Close modal
- `Enter` - Submit command (modal)

---

**Design minimalista e funcional para máxima clareza e eficiência** ⚫⚪

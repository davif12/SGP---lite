# 🎨 SGP Lite - Guia de Design Moderno

## 📋 Visão Geral

Este documento descreve a implementação do novo design moderno e "app-like" do SGP Lite, incluindo instruções de instalação, configuração e uso dos novos componentes visuais.

## 🚀 Instalação e Configuração

### 1. Dependências JavaScript

```bash
# Instalar dependências necessárias
npm install sortablejs chart.js

# Compilar assets
npm run dev
# ou para produção
npm run build
```

### 2. Estrutura de Arquivos Criados/Modificados

```
resources/
├── css/
│   ├── app.scss (modificado - imports Bootstrap + custom)
│   └── custom.scss (novo - estilos customizados)
├── js/
│   ├── app.js (modificado - imports Chart.js + kanban)
│   └── kanban.js (novo - funcionalidade drag&drop)
└── views/
    ├── layouts/
    │   ├── app.blade.php (redesenhado)
    │   └── partials/
    │       ├── navbar.blade.php (novo)
    │       ├── sidebar.blade.php (novo)
    │       └── footer.blade.php (novo)
    ├── dashboard.blade.php (redesenhado)
    ├── board/
    │   └── index.blade.php (novo - Kanban)
    └── projects/
        └── index.blade.php (redesenhado)
```

## 🎨 Sistema de Design

### Paleta de Cores

```scss
// Cores Primárias
--primary: #6366f1        // Índigo moderno
--primary-dark: #4f46e5   // Índigo escuro
--primary-light: #818cf8  // Índigo claro

// Cores Neutras
--gray-50: #f8fafc
--gray-100: #f1f5f9
--gray-200: #e2e8f0
--gray-700: #334155
--gray-800: #1e293b

// Cores de Status
--success: #10b981        // Verde
--warning: #f59e0b        // Âmbar
--danger: #ef4444         // Vermelho
--info: #06b6d4           // Ciano
```

### Tipografia

- **Fonte Principal:** Inter (Google Fonts)
- **Pesos:** 300, 400, 500, 600, 700
- **Hierarquia:** h1-h6 com classes utilitárias

### Espaçamento

```scss
--spacing-xs: 0.25rem    // 4px
--spacing-sm: 0.5rem     // 8px
--spacing-md: 1rem       // 16px
--spacing-lg: 1.5rem     // 24px
--spacing-xl: 2rem       // 32px
```

### Border Radius

```scss
--radius-sm: 0.375rem    // 6px
--radius-md: 0.5rem      // 8px
--radius-lg: 0.75rem     // 12px
--radius-xl: 1rem        // 16px
```

## 🧩 Componentes Principais

### 1. Layout Principal

**Arquivo:** `resources/views/layouts/app.blade.php`

**Características:**
- Layout flexível com sidebar responsiva
- Header fixo com gradiente
- Sistema de overlay para mobile
- Suporte a tema escuro

### 2. Navbar

**Arquivo:** `resources/views/layouts/partials/navbar.blade.php`

**Funcionalidades:**
- Busca global
- Notificações com dropdown
- Menu de ações rápidas
- Menu do usuário
- Responsivo com toggle para sidebar

### 3. Sidebar

**Arquivo:** `resources/views/layouts/partials/sidebar.blade.php`

**Funcionalidades:**
- Navegação principal
- Projetos recentes
- Estatísticas rápidas
- Toggle de tema escuro/claro
- Colapsa em mobile

### 4. Dashboard Cards

**Classes CSS:**
```scss
.dashboard-card {
  // Card moderno com hover effects
}

.card-modern {
  // Card padrão com sombras suaves
}
```

**Uso:**
```html
<div class="dashboard-card">
  <div class="card-icon icon-primary">
    <i class="bi bi-folder"></i>
  </div>
  <div class="card-value">42</div>
  <div class="card-label">Projetos Ativos</div>
</div>
```

### 5. Board Kanban

**Arquivo:** `resources/views/board/index.blade.php`
**JavaScript:** `resources/js/kanban.js`

**Funcionalidades:**
- Drag & drop com SortableJS
- Animações suaves
- Responsivo (mobile = stack vertical)
- Modal para criar tarefas
- Notificações toast

**Uso do JavaScript:**
```javascript
// Auto-inicializa quando DOM carrega
// Disponível globalmente como window.kanbanBoard

// Métodos principais:
kanbanBoard.addCard(status, cardData)
kanbanBoard.refreshBoard()
kanbanBoard.showNotification(message, type)
```

## 🎯 Componentes Reutilizáveis

### Botões Modernos

```html
<button class="btn btn-modern btn-primary">
  <i class="bi bi-plus-circle me-1"></i>Ação
</button>
```

**Classes disponíveis:**
- `btn-modern` (base)
- `btn-primary`, `btn-secondary`, `btn-success`, `btn-warning`, `btn-danger`

### Badges Modernos

```html
<span class="badge badge-modern badge-success">Ativo</span>
```

### Cards Modernos

```html
<div class="card-modern">
  <div class="card-header">
    <h5 class="card-title">Título</h5>
  </div>
  <div class="card-body">
    Conteúdo
  </div>
</div>
```

## 📱 Responsividade

### Breakpoints Bootstrap 5

- **xs:** < 576px
- **sm:** ≥ 576px
- **md:** ≥ 768px
- **lg:** ≥ 992px
- **xl:** ≥ 1200px
- **xxl:** ≥ 1400px

### Mobile-First

Todos os componentes são desenvolvidos mobile-first:

```scss
// Mobile por padrão
.component {
  flex-direction: column;
  
  // Desktop
  @media (min-width: 768px) {
    flex-direction: row;
  }
}
```

## ♿ Acessibilidade

### Implementações

1. **Contraste:** Todas as cores atendem WCAG AA
2. **Navegação por teclado:** Focus visível em todos os elementos
3. **ARIA Labels:** Botões e componentes interativos
4. **Semântica:** HTML5 semântico
5. **Reduced Motion:** Respeita preferência do usuário

### Exemplos

```html
<!-- Botão com ARIA -->
<button aria-label="Adicionar projeto" class="btn btn-modern btn-primary">
  <i class="bi bi-plus"></i>
</button>

<!-- Navegação com roles -->
<nav role="navigation" aria-label="Navegação principal">
  <ul class="nav">...</ul>
</nav>
```

## 🌙 Tema Escuro

### Implementação

O tema escuro é controlado via atributo `data-theme`:

```javascript
// Toggle tema
function toggleTheme() {
  const currentTheme = document.documentElement.getAttribute('data-theme');
  const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', newTheme);
  localStorage.setItem('theme', newTheme);
}
```

### Variáveis CSS

```scss
[data-theme="dark"] {
  --gray-50: #0f172a;
  --gray-100: #1e293b;
  --white: #334155;
  // ... outras variáveis
}
```

## 📊 Chart.js Integration

### Dashboard Charts

```javascript
// Gráfico de rosca para épicos
new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ['Backlog', 'Em Progresso', 'Concluído'],
    datasets: [{
      data: [backlogCount, inProgressCount, completedCount],
      backgroundColor: ['var(--secondary)', 'var(--primary)', 'var(--success)']
    }]
  }
});
```

## 🔧 Customização

### Adicionando Novas Cores

1. Definir no `custom.scss`:
```scss
:root {
  --custom-color: #your-color;
}
```

2. Criar classes utilitárias:
```scss
.bg-custom { background-color: var(--custom-color); }
.text-custom { color: var(--custom-color); }
```

### Novos Componentes

1. Seguir padrão de nomenclatura: `.component-modern`
2. Usar variáveis CSS para cores e espaçamentos
3. Implementar estados hover/focus
4. Considerar tema escuro
5. Garantir responsividade

## 🚀 Performance

### Otimizações Implementadas

1. **CSS:** Variáveis CSS para evitar repetição
2. **JavaScript:** Imports dinâmicos quando possível
3. **Fonts:** Preconnect para Google Fonts
4. **Images:** Lazy loading (quando aplicável)
5. **Animations:** Respeita `prefers-reduced-motion`

### Bundle Sizes

- **CSS:** ~379 KiB (inclui Bootstrap + custom)
- **JavaScript:** ~4.15 MiB (inclui Chart.js + SortableJS)

## 🔄 Próximos Passos

### Melhorias Futuras

1. **PWA:** Service Worker para cache
2. **Dark Mode:** Auto-detect sistema
3. **Animações:** Micro-interações avançadas
4. **Componentes:** Biblioteca de componentes
5. **Performance:** Code splitting

### Alternativas Consideradas

- **Inertia.js:** Para SPA experience
- **Alpine.js:** Já integrado para reatividade
- **Tailwind CSS:** Removido em favor do Bootstrap

## 📝 Comandos Úteis

```bash
# Desenvolvimento
npm run dev
npm run watch

# Produção
npm run build

# Linting (se configurado)
npm run lint

# Testes frontend (se configurado)
npm run test
```

## 🎉 Resultado Final

O SGP Lite agora possui:

✅ **Interface moderna** com Bootstrap 5
✅ **Design responsivo** mobile-first
✅ **Tema escuro/claro** com toggle
✅ **Kanban board** com drag & drop
✅ **Dashboard** com gráficos interativos
✅ **Acessibilidade** WCAG AA
✅ **Performance** otimizada
✅ **Componentes** reutilizáveis

O resultado é uma aplicação moderna, profissional e altamente usável que mantém a performance e segue as melhores práticas de UX/UI.

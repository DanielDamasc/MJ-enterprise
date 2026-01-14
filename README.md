# MJ Engenharia - Project Brief

## 📋 Overview

**Project Name:** MJ Engenharia

**Purpose:** Sistema web completo para gerenciar clientes, equipamentos, executores, e ordens de serviço.
Notificações de clientes, geração de relatórios e dashboards, e otimização dos processos da MJ Engenharia. 

**Target Users:**
- Administradores
- Executores

**Status:** Em desenvolvimento

---

## 🎯 Business Goals

1. **Automatizar o controle dos serviços** para os administradores
2. **Automatizar a visualização de agendamentos** para os executores
3. **Automatizar envio de notificações** para os clientes
4. **Automatizar a produção de relatórios e documentos**

---

## 🚀 Setup & Installation

### Requirements
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL

### Quick Start
```bash
# Clone repository
git clone https://github.com/DanielDamasc/MjEngenharia.git
cd MjEngenharia

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env

# Run migrations & seeders
php artisan migrate --seed

# Build assets
npm run build

# Start development server
php artisan serve
```

---

## 🔮 Future Enhancements

### Priority 1 (High Impact)
- [ ] **Sistema de Notificações**
  - Email para clientes sobre agendamentos
  - Envio de WhatsApp para lembretes
  - Alertas de higienizações vencendo
  - Histórico de notificações

- [ ] **Relatórios Exportáveis**
  - PDF de ordens de serviço
  - Excel de relatórios
  - Comprovantes de serviço

### Priority 2 (Medium Impact)
- [ ] **Dashboard com KPIs**
  - Informações gerais do sistema
  - Gráficos com informações relevantes

- [ ] **Histórico de Alterações**
  - Auditoria de ações (quem fez o quê)

- [ ] **PWA (Progressive Web App)**
  - Uso offline para técnicos
  - Instalável em smartphones

### Priority 3 (Nice to Have)
- [ ] **Geolocalização**
  - Mapa com localizações de serviços
  - Otimização de rotas para técnicos

## 👥 Team & Contacts

**Developer:** Daniel Damasceno Meira
**GitHub:** https://github.com/DanielDamasc/MjEngenharia
**Repository:** https://github.com/DanielDamasc/MjEngenharia.git

---

**Last Updated:** 13/01/2026
**Laravel Version:** 12.0
**PHP Version:** 8.2+

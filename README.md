# MJ Engenharia - Project Brief

## 📋 Overview

**Project Name:** MJ Engenharia

**Purpose:** Sistema web completo para gerenciar clientes, equipamentos, empregados, ordens de serviço, e mais.
Notificações de clientes, geração de relatórios, controle e exportação de ordens de serviço, e otimização dos processos empresariais. 

**Target Users:**
- Administradores
- Assistentes Administrativos
- Executores

**Status:** Em produção ⚙️

---

## 🎯 Business Goals

1. **Automatizar o controle dos serviços** para os administradores
2. **Automatizar a visualização de agendamentos** para os executores
3. **Automatizar envio de notificações** para os clientes
4. **Automatizar os processos de geração de relatórios e comprovantes**

---

## 🚀 Setup & Installation

### Requirements
- PHP 8.2+
- Composer 2.8
- Node.js 24 & NPM 11 
- MySQL

### Quick Start
```bash
# Clone repository
git clone https://github.com/DanielDamasc/MJ-enterprise.git

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
  - Histórico mais detalhado de notificações

- [ ] **Relatórios Exportáveis**
  - PDF de ordens de serviço
  - Excel de relatórios
  - Comprovantes de serviço

### Priority 2 (Medium Impact)
- [ ] **Dashboard com KPIs**
  - Informações gerais do sistema
  - Gráficos com informações relevantes

- [ ] **PWA (Progressive Web App)**
  - Uso offline para técnicos
  - Instalável em smartphones

### Priority 3 (Nice to Have)
- [ ] **Geolocalização**
  - Mapa com localizações de serviços
  - Otimização de rotas para técnicos

## 👥 Team & Contacts

**Developer:** Daniel Damasceno Meira
**GitHub:** https://github.com/DanielDamasc
**Repository:** https://github.com/DanielDamasc/MJ-enterprise

---

**Last Updated:** 03/02/2026
**Laravel Version:** 12.0
**PHP Version:** 8.2+

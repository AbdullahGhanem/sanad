# Sanad — AI-Powered Mental Health Screening for Egyptian University Students

Sanad is a bilingual (Arabic/English) web application that provides anonymous, evidence-based mental health screening designed specifically for Egyptian university students. It combines validated clinical instruments (PHQ-9, GAD-7) with AI-powered free-text analysis to assess distress severity and deliver personalized coping recommendations.

## Why Sanad?

Mental health disorders among Egyptian university students have reached critical levels:

- **68.1%** of students suffer from psychological distress (BMC Psychiatry, 2023 — 21 universities, n=3,240)
- **35.3%** reported suicidal ideation
- **90.3%** of distressed students never sought professional help
- Egypt has only **18 mental health hospitals** serving 100+ million people

Sanad removes every barrier — it's **free**, **anonymous**, **Arabic-first**, and requires **no registration**.

## Features

| Feature | Description |
|---------|-------------|
| **Bilingual Screening** | PHQ-9 (depression) and GAD-7 (anxiety) in Arabic and English with full RTL support |
| **AI Analysis** | Free-text distress analysis using NLP for deeper insights beyond questionnaire scores |
| **AI Chat Support** | Supportive chatbot with screening context injection and crisis detection |
| **Crisis Detection** | Real-time crisis keyword detection and PHQ-9 item 9 monitoring with immediate resource referral |
| **PDF Reports** | Downloadable screening reports with scores, severity levels, and recommendations |
| **Admin Dashboard** | Filament-powered panel with analytics, charts, user management, and data export |
| **Personalized Recommendations** | Culturally adapted coping strategies and local resource referrals |
| **Complete Anonymity** | No personal data collected; anonymous session IDs only |

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 12 |
| Frontend | Livewire 4 + Flux UI 2 + Tailwind CSS 4 |
| Admin Panel | Filament 5 + Filament Shield |
| AI Integration | Laravel AI (Anthropic/OpenAI/Gemini) |
| Authentication | Laravel Fortify |
| PDF Generation | Laravel DomPDF |
| Database | SQLite (dev) / MySQL (prod) |
| Testing | PHPUnit 11 |

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- NPM

## Setup

### 1. Clone the repository

```bash
git clone https://github.com/your-username/sanad.git
cd sanad
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Environment configuration

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your settings:

```env
APP_NAME=Sanad
APP_LOCALE=ar

DB_CONNECTION=mysql
DB_DATABASE=sanad
DB_USERNAME=root
DB_PASSWORD=

# AI Provider (for chat and free-text analysis)
# Configure via Admin Panel > AI Settings
```

### 4. Database setup

```bash
php artisan migrate
php artisan db:seed
```

This seeds:
- PHQ-9 and GAD-7 questionnaires with Arabic/English items
- Crisis help resources (hotline, Nefsy.com, university health unit)
- Default crisis keywords (Arabic and English)
- Severity-based recommendations

### 5. Create admin user

```bash
php artisan make:filament-user
```

Assign the `super_admin` role to access the full admin panel.

### 6. Build frontend assets

```bash
npm run build
```

For development with hot reload:

```bash
npm run dev
```

### 7. Run the application

If using Laravel Herd, the site is available at `https://sanad.test`.

Otherwise:

```bash
php artisan serve
```

## Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/Screening/ScreeningWizardTest.php

# Filter by test name
php artisan test --filter=testCrisisOverlay
```

## Project Structure

```
app/
  Ai/Agents/          # AI agent classes (SanadChat, DistressAnalyzer)
  Filament/            # Admin panel resources, widgets, exports
  Livewire/            # Livewire components (ScreeningWizard, ChatInterface)
  Models/              # Eloquent models
  Notifications/       # Crisis notifications (mail + database)
  Policies/            # Authorization policies
  Services/            # Scoring services (PHQ-9, GAD-7, Combined, Ensemble)
lang/
  ar/                  # Arabic translations
  en/                  # English translations
resources/views/
  livewire/            # Livewire component views
  layouts/             # Application layouts
  welcome.blade.php    # Landing page
```

## Admin Panel

Access at `/admin` with a super_admin account.

- **Dashboard** — Screening trends chart, severity distribution, crisis event stats
- **Screening Sessions** — Browse all sessions with export to CSV
- **Questionnaires** — Manage PHQ-9/GAD-7 questions and options (bilingual)
- **Users** — User management with role-based access
- **Crisis Management** — Crisis events log, crisis keywords, help resources
- **Recommendations** — Manage severity-based coping recommendations
- **AI Settings** — Configure AI providers (OpenAI, Anthropic, Gemini, Azure)

## AI Capabilities

### Currently Implemented

| Feature | Description |
|---------|-------------|
| **Sanad Chat Agent** | Conversational chatbot providing emotional support to students after screening, with context injected from their PHQ-9/GAD-7 results |
| **Distress Analyzer** | NLP agent that analyzes free-text input to classify distress severity (minimal → severe) with confidence scores and emotional theme detection |
| **Ensemble Scoring** | Merges questionnaire scores (70%) with AI NLP analysis (30%) for a more accurate combined severity assessment |
| **Crisis Detection** | Real-time keyword-based crisis detection in chat messages with automatic admin notifications |
| **Context Injection** | Converts screening results into natural language context to personalize chatbot conversations |

### Planned Enhancements

| Enhancement | Description |
|-------------|-------------|
| **AI-Powered Crisis Detection** | Replace keyword matching with an AI agent that understands context, sarcasm, and indirect expressions of self-harm — especially in Egyptian Arabic dialect (عامية) |
| **Smart Recommendations** | AI-generated personalized coping strategies based on screening results and free-text input, instead of static score-range recommendations |
| **Session Summaries for Admins** | AI agent that summarizes chat sessions so admin staff can quickly review flagged conversations |
| **Follow-Up Check-Ins** | AI-generated personalized follow-up messages for students with moderate/severe scores |
| **Arabic Dialect Understanding** | Enhanced DistressAnalyzer with better support for Egyptian colloquial Arabic vs. formal Arabic |
| **Trend Analysis** | AI that detects worsening mental health patterns across multiple screening sessions for the same anonymous user |

## Research Context

This project is part of an Applied Diploma in Artificial Intelligence at the Faculty of Artificial Intelligence, Kaferelsheikh University. The research addresses the critical gap between the high prevalence of mental health distress among Egyptian university students and the near-zero rate of help-seeking, as documented by Baklola et al. (BMC Psychiatry, 2023).

The full research paper is available in two formats:
- [Markdown version](public/Sanad_Mental_Health_Research.md)
- [Word document (.docx)](public/Sanad_Mental_Health_Research.docx)

**Author:** Abdullah Ghanem Atya

## License

This project is proprietary and developed for academic research purposes.

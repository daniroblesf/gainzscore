<div align="center">

# ⚡ GainzScore

### _Level up your lifts. Compete in the leagues. Own the platform._

A mobile-first, gamified gym tracking web application where every kilogram lifted earns XP,
every personal record pushes you up the league table, and every session is a boss fight against yesterday's numbers.

<br/>

[![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org/)
[![Vite](https://img.shields.io/badge/Vite-8.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev/)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![SQLite](https://img.shields.io/badge/SQLite-Local-003B57?style=for-the-badge&logo=sqlite&logoColor=white)](https://www.sqlite.org/)
[![pnpm](https://img.shields.io/badge/pnpm-10.x-F69220?style=for-the-badge&logo=pnpm&logoColor=white)](https://pnpm.io/)
[![License: MIT](https://img.shields.io/badge/License-MIT-22FF77?style=for-the-badge)](LICENSE)

</div>

---

## 📋 Table of Contents

- [Key Features](#-key-features)
- [Tech Stack](#-tech-stack)
- [Project Architecture](#-project-architecture)
- [Directory Structure](#-directory-structure)
- [Getting Started](#-getting-started)
  - [Prerequisites](#prerequisites)
  - [Backend Setup](#1-backend-setup-laravel-api)
  - [Frontend Setup](#2-frontend-setup-vuejs-spa)
- [API Endpoints](#-api-endpoints)
- [XP & League System](#-xp--league-system)
- [Contributing](#-contributing)

---

## ✨ Key Features

| Feature | Description |
|---|---|
| 🎮 **Gamified XP System** | Every completed set awards XP calculated from `Volume × Exercise Multiplier`. Hit thresholds to level up automatically. |
| 🏆 **League Ranking** | 12-tier league table from **Bronze I** up to **Diamond**, promoted automatically as your total XP grows. |
| 📱 **Mobile-First UI** | Designed for portrait smartphone screens first. High-contrast Hacker/Gamer aesthetic — neon green on near-black. |
| ⚡ **Real-Time Tracker** | Dynamic set rows with per-field `v-model` binding. Mark each set complete with a single tap to POST to the API instantly. |
| 🔌 **Offline Graceful** | If the API is unreachable, the frontend still marks sets locally so training is never interrupted. |
| 🌱 **Zero-Config Database** | SQLite file-based database. No database server required — just run `migrate --seed` and go. |

---

## 🛠 Tech Stack

### Backend
| Layer | Technology | Notes |
|---|---|---|
| Language | **PHP 8.5** | Strict-typed, modern PHP |
| Framework | **Laravel 13** | API-only mode (no Blade views) |
| Database | **SQLite** | Local file `backend/database/database.sqlite` |
| ORM | **Eloquent** | Models: `User`, `Exercise`, `Workout`, `WorkoutSet` |
| Business Logic | **XpService** | Stateless service class for XP calculation & rank resolution |

### Frontend
| Layer | Technology | Notes |
|---|---|---|
| Framework | **Vue.js 3.5** | Composition API with `<script setup>` |
| Build tool | **Vite 8** | Sub-second HMR |
| Package manager | **pnpm 10** | Faster installs, strict linking |
| CSS | **Tailwind CSS 3.4** | Custom palette: `dark-bg`, `dark-card`, `neon-green` |
| HTTP | **fetch API** | Native browser API, no extra dependency |
| Routing | **Vue Router 4** | Hash-mode SPA (`/tracker`, `/profile`) |

---

## 🏛 Project Architecture

GainzScore follows a **clean decoupled architecture** — the backend and frontend are completely independent processes that communicate exclusively through a JSON REST API.

```
┌─────────────────────────────────────────────────────────────┐
│                        Browser (SPA)                        │
│  Vue 3 + Vite  │  Vue Router  │  Tailwind CSS               │
│  localhost:5173                                             │
└─────────────────────┬───────────────────────────────────────┘
                      │  HTTP/JSON  (REST API)
                      ▼
┌─────────────────────────────────────────────────────────────┐
│                   Laravel API Server                        │
│  PHP 8.5 + Laravel 13  │  Eloquent ORM  │  XpService        │
│  localhost:8000/api/*                                       │
└─────────────────────┬───────────────────────────────────────┘
                      │  PDO / SQLite driver
                      ▼
┌─────────────────────────────────────────────────────────────┐
│              SQLite  (database/database.sqlite)             │
│   users · exercises · workouts · workout_sets               │
└─────────────────────────────────────────────────────────────┘
```

- The **backend** is a pure **REST API server** — it returns JSON only, no HTML rendering.
- The **frontend** is a **Single Page Application (SPA)** — it owns all routing and UI state; it calls the API only when persisting data.
- Either layer can be replaced independently (e.g., swap SQLite for PostgreSQL, or the Vue SPA for a React Native app) without touching the other.

---

## 📁 Directory Structure

```
gainz/
├── backend/                    # Laravel REST API
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   │   ├── ExerciseController.php
│   │   │   ├── WorkoutController.php
│   │   │   ├── SetController.php
│   │   │   └── UserController.php
│   │   ├── Models/
│   │   │   ├── User.php
│   │   │   ├── Exercise.php
│   │   │   ├── Workout.php
│   │   │   └── WorkoutSet.php
│   │   └── Services/
│   │       └── XpService.php   # XP calculation & league logic
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/            # ExerciseSeeder, DemoUserSeeder
│   ├── routes/
│   │   └── api.php             # All /api/* routes
│   └── .env                    # DB_CONNECTION=sqlite
│
└── frontend/                   # Vue 3 SPA
    ├── src/
    │   ├── components/
    │   │   ├── ExerciseCard.vue # Set tracker card with v-model inputs
    │   │   └── ProgressBar.vue  # Animated XP progress bar
    │   ├── App.vue              # Root layout & exercise list
    │   ├── main.js
    │   └── style.css           # Tailwind directives + JetBrains Mono
    ├── tailwind.config.js      # Custom color palette
    ├── postcss.config.js
    └── vite.config.js
```

---

## 🚀 Getting Started

### Prerequisites

Make sure the following are installed on your system:

| Tool | Minimum Version | Check |
|---|---|---|
| PHP | 8.2+ | `php --version` |
| Composer | 2.x | `composer --version` |
| Node.js | 18+ | `node --version` |
| pnpm | 9+ | `pnpm --version` |

> **Linux (Arch/Manjaro):** `sudo pacman -S php composer nodejs pnpm`
>
> **Windows:** Install [Laragon](https://laragon.org/) (bundles PHP + Composer) and [Node.js](https://nodejs.org/). Then run `npm install -g pnpm`.
>
> **macOS:** `brew install php composer node && npm install -g pnpm`

---

### 1. Backend Setup (Laravel API)

```bash
# Clone the repository
git clone https://github.com/your-username/gainzscore.git
cd gainzscore/backend

# Install PHP dependencies
composer install

# Create your local environment file
cp .env.example .env

# Generate the application encryption key
php artisan key:generate

# Create the SQLite database file
# Linux / macOS:
touch database/database.sqlite

# Windows (PowerShell):
New-Item -ItemType File database\database.sqlite

# Run all migrations and seed the default exercises
php artisan migrate --seed

# Start the development server (default: http://localhost:8000)
php artisan serve
```

> The seeder will create 4 default exercises (`Bankdrücken`, `Latziehen`, `Kniebeugen`, `Bizeps Curls`) and a demo user at `demo@gainzscore.app` / `password`.

---

### 2. Frontend Setup (Vue.js SPA)

Open a **second terminal** and run:

```bash
cd gainzscore/frontend

# Install JavaScript dependencies
pnpm install

# Start the Vite dev server with Hot Module Replacement
# App available at: http://localhost:5173
pnpm dev
```

> **Windows note:** if `pnpm` is not recognized after installation, restart your terminal or run `npm install -g pnpm` as Administrator.

---

## 📡 API Endpoints

All endpoints are prefixed with `/api` and respond with `Content-Type: application/json`.

| Method | Endpoint | Description | Body / Params |
|---|---|---|---|
| `GET` | `/api/exercises` | List all available exercises | — |
| `POST` | `/api/workouts/start` | Create a new workout session | `user_id`, `date?` |
| `POST` | `/api/sets/log` | Log or update an individual set | `workout_id`, `exercise_id`, `set_number`, `weight`, `reps`, `is_completed?` |
| `GET` | `/api/users/{id}` | Get user profile, level & XP | — |

### Example: Log a set

```bash
curl -X POST http://localhost:8000/api/sets/log \
  -H "Content-Type: application/json" \
  -d '{
    "workout_id": 1,
    "exercise_id": 1,
    "set_number": 1,
    "weight": 80,
    "reps": 8,
    "is_completed": true
  }'
```

**Response:**
```json
{
  "message": "Set logged",
  "data": {
    "id": 1,
    "workout_id": 1,
    "exercise_id": 1,
    "set_number": 1,
    "weight": 80,
    "reps": 8,
    "is_completed": true
  },
  "xp": {
    "xp_gained": 768,
    "leveled_up": false,
    "current_xp": 768,
    "level": 1,
    "rank": "BRONCE I",
    "xp_for_next": 1000
  }
}
```

---

## 🏆 XP & League System

XP is awarded each time a set is marked as completed, using the following formula:

```
XP = (weight_kg × reps) × exercise_multiplier
```

| Exercise | Multiplier |
|---|---|
| Kniebeugen (Squats) | `× 1.3` |
| Bankdrücken (Bench Press) | `× 1.2` |
| Latziehen (Lat Pulldown) | `× 1.1` |
| Bizeps Curls | `× 1.0` |

Level progression uses a **tiered threshold**: to advance from level `N` to level `N+1`, the user must accumulate `N × 1000 XP`. On level-up, the league rank is automatically recalculated:

| League | Cumulative XP Required |
|---|---|
| 🥉 Bronze I | 0 |
| 🥉 Bronze II | 500 |
| 🥉 Bronze III | 1,000 |
| 🥈 Silver I | 2,000 |
| 🥈 Silver II | 3,500 |
| 🥈 Silver III | 5,000 |
| 🥇 Gold I | 7,000 |
| 🥇 Gold II | 9,500 |
| 🥇 Gold III | 12,500 |
| 🏅 Platinum I | 16,000 |
| 🏅 Platinum II | 20,000 |
| 💎 Diamond | 25,000 |

---

## 🤝 Contributing

This project is a university assignment. Contributions and feedback are welcome.

1. Fork the repository
2. Create a feature branch: `git checkout -b feat/your-feature`
3. Commit your changes: `git commit -m 'feat: add your feature'`
4. Push the branch: `git push origin feat/your-feature`
5. Open a Pull Request

---

<div align="center">
  <sub>Built with ⚡ by the GainzScore team · University Project 2026</sub>
</div>

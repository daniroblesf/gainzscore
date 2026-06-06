<div align="center">

# GainzScore

### _Your Gainz; Your Score_

A mobile-first, gamified gym tracking app where every kilogram lifted earns XP
and pushes you up the league table.

[![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org/)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![SQLite](https://img.shields.io/badge/SQLite-Local-003B57?style=for-the-badge&logo=sqlite&logoColor=white)](https://www.sqlite.org/)

</div>

---

## Table of Contents

- [What is this project?](#what-is-this-project)
- [How the project is organized](#how-the-project-is-organized)
- [Step 1 — Install the required tools](#step-1--install-the-required-tools)
- [Step 2 — Download the project from GitHub](#step-2--download-the-project-from-github)
- [Step 3 — Set up the Backend](#step-3--set-up-the-backend)
- [Step 4 — Set up the Frontend](#step-4--set-up-the-frontend)
- [Step 5 — Open the app in your browser](#step-5--open-the-app-in-your-browser)
- [How to save and share your changes (Git basics)](#how-to-save-and-share-your-changes-git-basics)
- [API Reference](#api-reference)
- [XP and League System](#xp-and-league-system)
- [Troubleshooting](#troubleshooting)

---

## What is this project?

GainzScore is a web app that lets you log your gym sets and earn XP (experience points) for each completed set, just like a video game. Users compete on a league table — the more you train, the higher your rank.

The project is split into two separate parts that talk to each other:

- **Backend** — a PHP/Laravel server that stores data and handles logic. Think of it as the "brain" of the app.
- **Frontend** — a Vue.js app that runs in the browser. Think of it as the "face" of the app.

They communicate like this:

```
Your Browser  ←→  Frontend (Vue, port 5173)  ←→  Backend API (Laravel, port 8000)  ←→  SQLite database file
```

---

## How the project is organized

```
gainz/
│
├── backend/              ← PHP / Laravel (the server, the API, the database)
│   ├── app/
│   │   ├── Controllers/  ← handle incoming requests, return JSON responses
│   │   ├── Models/       ← represent database tables (User, Exercise, Workout...)
│   │   └── Services/     ← business logic (XpService: calculates XP and ranks)
│   ├── database/
│   │   ├── migrations/   ← scripts that create the database tables
│   │   └── seeders/      ← scripts that fill the database with test data
│   └── routes/
│       └── api.php       ← all the URL endpoints the frontend can call
│
└── frontend/             ← JavaScript / Vue.js (what runs in the browser)
    └── src/
        ├── views/        ← full pages (LoginView, TrackerView, RankingView)
        ├── components/   ← reusable UI pieces (ExerciseCard, ProgressBar)
        └── router/       ← decides which page to show for each URL
```

---

## Step 1 — Install the required tools

You need to install four tools before you can run this project. Do this once on your computer.

### 1a. PHP

PHP is the language the backend is written in.

- **Windows:** Download and install [Laragon](https://laragon.org/download/) — it installs PHP and Composer automatically. Recommended for beginners on Windows.
- **macOS:** Open a terminal and run: `brew install php`
- **Linux (Arch/Manjaro):** Open a terminal and run: `sudo pacman -S php`

To verify it worked, open a terminal and type:
```bash
php --version
# You should see something like: PHP 8.5.x
```

### 1b. Composer

Composer is the package manager for PHP (like npm but for PHP).

- **Windows (if not using Laragon):** Download from [getcomposer.org](https://getcomposer.org/download/)
- **macOS:** `brew install composer`
- **Linux:** `sudo pacman -S composer`

To verify:
```bash
composer --version
# You should see: Composer version 2.x.x
```

### 1c. Node.js

Node.js lets you run JavaScript tools (like Vite) outside the browser.

- **All platforms:** Download the LTS version from [nodejs.org](https://nodejs.org/)

To verify:
```bash
node --version
# You should see: v22.x.x (or similar, anything above v18 is fine)
```

### 1d. pnpm

pnpm is the package manager we use for the frontend (faster than npm).

After Node.js is installed, run this in your terminal:
```bash
npm install -g pnpm
```

To verify:
```bash
pnpm --version
# You should see: 10.x.x
```

---

## Step 2 — Download the project from GitHub

> **What is GitHub?** GitHub is a website where code is stored and shared. Think of it like Google Drive, but for code. "Cloning" a repository means downloading a copy of the project to your computer.

### Option A — Using Git (recommended)

If you have Git installed, open a terminal and run:

```bash
git clone https://github.com/daniroblesf/gainzscore.git
cd gainzscore
```

> **Don't have Git?**
> - **Windows:** Download from [git-scm.com](https://git-scm.com/download/win)
> - **macOS:** `brew install git`
> - **Linux:** `sudo pacman -S git`

### Option B — Download as ZIP

1. Go to the repository page on GitHub
2. Click the green **Code** button
3. Click **Download ZIP**
4. Extract the ZIP file to a folder on your computer

---

## Step 3 — Set up the Backend

Open a terminal and navigate to the `backend` folder:

```bash
cd gainzscore/backend
```

Then run these commands **one by one**:

**Install PHP dependencies** (downloads all the Laravel packages):
```bash
composer install
```

**Create your local environment file** (this is a copy of the config template):
```bash
cp .env.example .env
```
> On Windows, use: `copy .env.example .env`

**Generate the app security key** (Laravel needs this to run):
```bash
php artisan key:generate
```

**Create the SQLite database file** (this is the actual database — just an empty file):
```bash
# macOS / Linux:
touch database/database.sqlite

# Windows (PowerShell):
New-Item -ItemType File database\database.sqlite
```

**Create the tables and fill them with demo data:**
```bash
php artisan migrate --seed
```
> This creates all the tables and adds: 4 exercises + 1 demo user + 4 ranking users.

**Start the backend server:**
```bash
php artisan serve
```

You should see:
```
INFO  Server running on [http://127.0.0.1:8000].
```

> Leave this terminal open. The backend needs to keep running.

---

## Step 4 — Set up the Frontend

Open a **new terminal window** (keep the backend terminal running!) and run:

```bash
cd gainzscore/frontend

# Install JavaScript dependencies
pnpm install

# Start the frontend dev server
pnpm dev
```

You should see:
```
  VITE v8.x.x  ready in xxx ms

  ➜  Local:   http://localhost:5173/
```

> Leave this terminal open too.


---

## How to save and share your changes (Git basics)

> **What is Git?** Git is a tool that tracks changes to your code. Think of it like "save history" — you can go back to any previous version.

### Everyday workflow

**1. Before you start working, always pull the latest changes from your teammates:**
```bash
git pull
```

**2. Make your changes** to the files you want to edit.

**3. Check what files you changed:**
```bash
git status
```

**4. Stage the files you want to save:**
```bash
# Stage specific files:
git add backend/app/Http/Controllers/Api/UserController.php

# Or stage everything you changed:
git add .
```

**5. Save your changes with a descriptive message:**
```bash
git commit -m "feat: add login endpoint to UserController"
```

**6. Upload your changes to GitHub:**
```bash
git push
```

### Good commit message examples

```
feat: add XP calculation to SetController
fix: correct rank threshold for SILVER I
style: update button colors in TrackerView
```

### If you get a conflict

A conflict happens when two people edit the same file at the same time. Git will mark the conflicting lines inside the file. Open the file, choose which version to keep, save it, then run `git add .` and `git commit`.

---

## API Reference

All endpoints return JSON. The backend runs on `http://localhost:8000`.

| Method | URL | What it does |
|---|---|---|
| `POST` | `/api/login` | Log in with email + password |
| `GET` | `/api/exercises` | Get the list of all exercises |
| `POST` | `/api/workouts/start` | Create a new workout session |
| `POST` | `/api/sets/log` | Log a completed set and receive XP |
| `GET` | `/api/users/{id}` | Get a user's level, rank and XP |
| `GET` | `/api/ranking` | Get all users sorted by total XP |

### Example: Log a completed set

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

Response:
```json
{
  "message": "Set logged",
  "data": { "workout_id": 1, "exercise_id": 1, "weight": 80, "reps": 8 },
  "xp": {
    "xp_gained": 768,
    "leveled_up": false,
    "current_xp": 768,
    "level": 1,
    "rank": "BRONZE I",
    "xp_for_next": 1000
  }
}
```

---

## XP and League System

Every time you mark a set as completed, XP is calculated like this:

```
XP = (weight in kg × reps) × exercise multiplier
```

**Exercise multipliers:**

| Exercise | Multiplier |
|---|---|
| Kniebeugen (Squats) | × 1.3 |
| Bankdrücken (Bench Press) | × 1.2 |
| Latziehen (Lat Pulldown) | × 1.1 |
| Bizeps Curls | × 1.0 |

**League ranks** (based on total XP accumulated across all level-ups):

| Rank | Total XP needed |
|---|---|
| Bronze I | 0 |
| Bronze II | 500 |
| Bronze III | 1,000 |
| Silver I | 2,000 |
| Silver II | 3,500 |
| Silver III | 5,000 |
| Gold I | 7,000 |
| Gold II | 9,500 |
| Gold III | 12,500 |
| Platinum I | 16,000 |
| Platinum II | 20,000 |
| Diamond | 25,000 |

---

## Troubleshooting

**`composer: command not found`**
Composer is not installed or not in your PATH. Re-install it following the instructions in Step 1b.

**`php artisan serve` shows a 500 error**
Your `.env` file might be missing or the app key might not be generated. Run:
```bash
cp .env.example .env
php artisan key:generate
```

**`pnpm: command not found`**
Run `npm install -g pnpm`. If npm itself is not found, install Node.js first.

**The frontend loads but the exercises don't appear / sets don't save**
The backend is not running. Open a new terminal and run `php artisan serve` inside the `backend/` folder.

**`SQLSTATE[HY000]: unable to open database file`**
The SQLite file doesn't exist yet. Run:
```bash
# macOS / Linux:
touch database/database.sqlite
php artisan migrate --seed

# Windows:
New-Item -ItemType File database\database.sqlite
php artisan migrate --seed
```

**`git push` fails with "Authentication failed"**
You need to authenticate with GitHub. The easiest way is a Personal Access Token:
1. Go to [github.com/settings/tokens](https://github.com/settings/tokens)
2. Click **Generate new token (classic)**
3. Check the **repo** scope and generate
4. When `git push` asks for your password, paste the token instead

---

<div align="center">
  <sub>Built by the GainzScore team · University Project 2026</sub>
</div>

# 🎨 My Portfolio — Full-Stack Web with Admin Panel

A personal portfolio website built with a **Neo-Brutalism / Comic Book** design theme. It features a public-facing showcase and a private admin panel to manage all portfolio content. The project is structured as a **monorepo**, separating the frontend and backend.

---

## 🗂️ Project Structure

```
my-portofolio-web/
├── frontend/          # Vue 3 SPA (Vite + TailwindCSS v4)
├── backend/           # Laravel 12 REST API
└── .github/
    └── workflows/
        └── ci.yml     # GitHub Actions CI/CD
```

---

## ✨ Features

### 🌐 Public Portfolio
- **Homepage** — Hero section, quick highlights
- **About** — Personal bio and overview
- **Projects** — Filterable project showcase with live demo & GitHub links, featured badge
- **Services** — Available services offered
- **Certificates** — Academic and professional certifications
- **Contacts** — Social media and contact links

### 🔐 Admin Panel (Protected)
Full CRUD management for all portfolio content via a private dashboard accessible at `/admin/dashboard`.

| Section | Capabilities |
|---|---|
| **Profile** | Update bio, photo, availability status |
| **Projects** | Create / Edit / Delete, set featured, attach skills, upload thumbnail |
| **Skills** | Create / Edit / Delete, icon identifier support |
| **Experiences** | Create / Edit / Delete, date range, description |
| **Certificates** | Create / Edit / Delete, upload image |
| **Services** | Create / Edit / Delete, toggle active/inactive status |
| **Contacts** | Manage contact links & social media |
| **Visitors** | View realtime visitor count |

Authentication is token-based using **Laravel Sanctum**. The frontend stores the token in `localStorage` and guards routes using Vue Router's global navigation guard.

---

## 🛠️ Tech Stack

### Frontend
| Technology | Purpose |
|---|---|
| [Vue 3](https://vuejs.org/) | UI Framework (Composition API) |
| [Vite](https://vitejs.dev/) | Build tool & dev server |
| [TailwindCSS v4](https://tailwindcss.com/) | Utility-first styling |
| [Vue Router 4](https://router.vuejs.org/) | Client-side SPA routing |
| [GSAP](https://gsap.com/) | Scroll and entrance animations |
| [Iconify for Vue](https://iconify.design/) | Icon library |
| [SweetAlert2](https://sweetalert2.github.io/) | Stylish dialog & toast notifications |
| [marked](https://marked.js.org/) | Markdown rendering |
| [@vueuse/core](https://vueuse.org/) | Composition utility helpers |

### Backend
| Technology | Purpose |
|---|---|
| [Laravel 12](https://laravel.com/) | REST API Framework |
| [PHP 8.2+](https://www.php.net/) | Server runtime |
| [Laravel Sanctum](https://laravel.com/docs/sanctum) | API Token Authentication |
| [Cloudinary](https://cloudinary.com/) | Cloud image storage & delivery |
| [MySQL](https://www.mysql.com/) | Production database |
| [SQLite](https://www.sqlite.org/) | Testing database |
| [PHPUnit](https://phpunit.de/) | Automated testing |

---

## 🚀 Getting Started

### Prerequisites
- Node.js >= 20
- PHP >= 8.2
- Composer
- MySQL (for local dev) or SQLite (for testing)
- A [Cloudinary](https://cloudinary.com/) account

---

### 1. Clone the Repository

```bash
git clone https://github.com/gilangages/my-portofolio-new.git
cd my-portofolio-new
```

---

### 2. Backend Setup

```bash
cd backend

# Install PHP dependencies
composer install

# Copy environment file and configure it
cp .env.example .env

# Fill in your database credentials and Cloudinary keys in .env
# DB_DATABASE, DB_USERNAME, DB_PASSWORD
# CLOUDINARY_URL=cloudinary://...

# Generate app key
php artisan key:generate

# Run database migrations
php artisan migrate

# (Optional) Seed initial admin user
php artisan db:seed

# Start the development server (default: localhost:8000)
php artisan serve
```

---

### 3. Frontend Setup

```bash
cd frontend

# Install JS dependencies
npm install

# Start development server (default: localhost:5173)
npm run dev
```

> Make sure your `VITE_API_BASE_URL` in the frontend points to the backend server URL (e.g., `http://localhost:8000/api`).

---

## 🔌 API Overview

All endpoints are prefixed with `/api`. The backend exposes the following routes:

**Public (No Auth)**
- `GET /profile`
- `GET /projects` · `GET /projects/{id}`
- `GET /skills` · `GET /skills/{id}`
- `GET /experiences` · `GET /experiences/{id}`
- `GET /certificates` · `GET /certificates/{id}`
- `GET /contacts` · `GET /contacts/{id}`
- `GET /services` · `GET /services/{id}`
- `POST /visitors`
- `POST /admin/login`

**Protected (Bearer Token required)**
- `DELETE /admin/logout`
- `GET /admin/visitors/count`
- Full CRUD (`POST`, `PUT`, `DELETE`) for: `projects`, `skills`, `experiences`, `certificates`, `contacts`, `services`
- `POST /profile` (update)

---

## 🤖 CI/CD

This project uses **GitHub Actions** to automatically validate both the backend and frontend on every push or pull request to the `main` branch.

Pipeline (`.github/workflows/ci.yml`):
- ✅ **Backend** → Install Composer, run migrate, run `php artisan test`
- ✅ **Frontend** → Install NPM, run `npm run build`

---

## 📁 Frontend Pages & Routes

| Route | Component | Auth Required |
|---|---|---|
| `/` | Homepage | ❌ |
| `/about` | About | ❌ |
| `/projects` | AllProjects | ❌ |
| `/services` | AllServices | ❌ |
| `/certificates` | AllCertificates | ❌ |
| `/contacts` | AllContacts | ❌ |
| `/admin/login` | AdminLogin | Guest Only |
| `/admin/dashboard` | AdminDashboard | ✅ |
| `/admin/dashboard/projects` | Project List | ✅ |
| `/admin/dashboard/skills` | Skill List | ✅ |
| `/admin/dashboard/experiences` | Experience List | ✅ |
| `/admin/dashboard/certificates` | Certificate List | ✅ |
| `/admin/dashboard/contacts` | Contact List | ✅ |
| `/admin/dashboard/services` | Service List | ✅ |
| `/admin/dashboard/profile` | Update Profile | ✅ |

---

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

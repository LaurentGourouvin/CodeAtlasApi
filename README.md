# 🚀 CodeAtlas

**CodeAtlas** is a personal learning project designed to map and grow your developer skills through a structured and visual system. It features a Symfony backend, full Docker containerization, and a modern API-first approach.

---

## 🧰 Tech Stack

### 🔙 Backend

- **PHP**: 8.3
- **Framework**: Symfony 7
- **API**: API Platform
- **Database**: PostgreSQL
- **Containerization**: Docker / Docker Compose
- **Reverse Proxy**: Caddy
- **Database Interface**: Adminer

---

## 🐳 Docker Architecture

The project uses **Docker Compose** to manage all services:

```yaml
services:
  api:         # Main Symfony container (PHP 8.3 + Composer)
  db:          # PostgreSQL
  adminer:     # Web SQL interface
  caddy:       # Reverse proxy with automatic HTTPS
```

Each service is defined in `docker-compose.yml` with a volume that mounts your local project into the container (`. : /var/www/symfony`).

---

## ▶️ Getting Started

### Prerequisites

- Docker + Docker Compose
- Make (recommended for simplified commands)

---

## 🛠️ Makefile Commands

```bash
make                # 📘 Show all available make commands
make install        # 🚀 Build and start all containers
make stop           # 🛑 Stop running containers
make delete         # 🔥 Remove containers and volumes created by docker-compose
make link           # 🔗 Show URLs of local services (API, Adminer)
make status         # 📊 Show container ID, image, name and status with colors
make down service=  # 🧯 Stop a specific container (e.g. service=adminer)
make restart service= # ♻️ Restart a specific container (e.g. service=api)
make restart-all    # ♻️ Restart all containers
make logs service=  # 📜 Show logs of a specific container
make api-logs       # 📄 Show Symfony API logs (dev environment)
make connect service= # 🐚 Open a shell in a specific container
```

---

## 📂 Project Structure

```bash
codeatlas-api/
├── config/
├── docker-compose.yml
├── Dockerfile
├── Makefile
├── public/
├── src/
├── var/
└── vendor/
```

---

## ✨ Coming Soon

- GitHub & Gmail OAuth login
- Skill tree editor and self-assessment
- Personalized learning paths
- Real-time chat (optional bonus)
- Frontend in Vue 3 + TypeScript

---

## 👤 Author

Built by **@GourouvinLaurent**

_"No matter your level, a dev learns something new every day — that’s the beauty of it."_ 😎
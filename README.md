# Helpdesk App

A Laravel + Vue.js based Helpdesk system with dark mode support and OpenAI integration.

---

## Setup Instructions

Follow these steps to get the project running locally:

1. Clone the repository  
   ```bash
   git clone https://github.com/your-username/helpdesk-app.git
   cd helpdesk-app
   ```

2. Install PHP dependencies  
   ```bash
   composer install
   ```

3. Install JavaScript dependencies  
   ```bash
   npm install
   ```

4. Copy environment file  
   ```bash
   cp .env.example .env
   ```

5. Generate application key  
   ```bash
   php artisan key:generate
   ```

6. Configure database in `.env` and run migrations with seed data  
   ```bash
   php artisan migrate --seed
   ```

7. Build frontend assets  
   ```bash
   npm run dev
   # or
   npm run build
   ```

8. Start the Laravel development server  
   ```bash
   php artisan serve
   ```

9. Start the queue worker (for background jobs)  
   ```bash
   php artisan queue:work
   ```

10. Open the app in your browser  
   ```
   http://127.0.0.1:8000
   ```

---

## ⚖Assumptions & Trade-offs

- Assumed authentication is required for creating or managing tickets.  
- Implemented dark mode toggle using Vue Options API for simplicity.  
- Queue system is set to `database` for portability; in production, Redis/SQS would be preferable.  
- Chose polling for ticket updates instead of WebSockets due to time constraints.

---

## What I’d Do With More Time

- Add role-based access control (admin vs. agent vs. user).  
- Replace polling with WebSockets (Laravel Echo + Pusher/Redis).  
- Add unit and feature tests for tickets and API endpoints.  
- Implement Docker for easier setup across environments.  
- Add CI/CD pipeline (GitHub Actions / GitLab CI).  

---

## Environment Variables

The project uses a `.env` file for configuration. Example:

```dotenv
APP_NAME=Helpdesk
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=helpdesk
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database

# OpenAI
OPENAI_API_KEY=
OPENAI_ORG_ID=
```

---

## 📂 Project Structure

```
helpdesk-app/
├── app/            # Laravel backend
├── resources/js/   # Vue frontend
├── database/       # Migrations & seeders
├── routes/         # API & web routes
└── README.md
```

---

## 📝 Notes

- No restrictions on commit frequency or branch structure.  
- Recommended: use small, meaningful commits and feature branches.  
- Ensure `php artisan queue:work` is running to process background jobs.  

---

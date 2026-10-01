# Integrative Programming 2 – Module 1
**PFEBSIT 002 · IT Free Elective 2**
Foundations of Systems Integration and Laravel REST API Development (Weeks 1–6)

Employee Management REST API built with **Laravel 12**, **MySQL**, **Eloquent ORM**, and **Laravel Sanctum**.

| Week | Lab | Where it is in this project |
|---|---|---|
| 1 | Development Environment & GitHub Setup | This project, `.env.example` (MySQL `integrative_db`) |
| 2 | Build a Simple REST API | `routes/api.php` → `GET /api/students` |
| 3 | Design and Document an API | `docs/API_DESIGN.md`, `docs/openapi.yaml` (import into Postman) |
| 4 | Develop CRUD APIs | `app/Http/Controllers/Api/EmployeeController.php`, `app/Http/Resources/`, migrations |
| 5 | Relationships, Pagination, Filtering, Searching | `Department` ↔ `Employee` models, `index()` with `paginate`, `search`, `department_id` |
| 6 | Secure Login API | `AuthController.php`, `auth:sanctum` middleware, `ForceJsonResponse` middleware |

Written reflections/explanations for every week: **`docs/WRITTEN_ANSWERS.md`**
Postman collection: **`docs/Integrative-Programming-2.postman_collection.json`**

---

## Requirements
- PHP 8.2+ (XAMPP / Laragon is fine)
- Composer
- MySQL or MariaDB
- Git, Postman, VS Code

## Setup

```bash
# 1. Install PHP packages (Laravel + Sanctum)
composer install

# 2. Create your environment file and app key
cp .env.example .env          # Windows CMD: copy .env.example .env
php artisan key:generate
```

3. Create the database in phpMyAdmin or MySQL:
```sql
CREATE DATABASE integrative_db;
```
`.env` is already set to:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=integrative_db
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# 4. Create the tables and sample data (4 departments, 25 employees, 1 test user)
php artisan migrate --seed

# 5. Run the server
php artisan serve
```

In Postman, send `GET http://127.0.0.1:8000` – it returns a JSON status message.

> This is an **API-only** project: there are no web pages. Everything is tested in Postman.

> **Sanctum note:** the `personal_access_tokens` migration is already included, so you don't need to publish Sanctum's migrations. If you run `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"` and it copies a second `create_personal_access_tokens_table` migration, delete the newly copied one or `migrate` will fail with "table already exists".

Reset everything at any time with:
```bash
php artisan migrate:fresh --seed
```

---

## Endpoints

Base URL: `http://127.0.0.1:8000/api` (also available versioned at `/api/v1`)

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| GET | `/students` | – | Week 2 static JSON |
| POST | `/register` | – | Register, returns token |
| POST | `/login` | – | Login, returns token |
| GET | `/user` | Bearer | Current user |
| POST | `/logout` | Bearer | Revoke token |
| GET | `/employees` | Bearer | List (paginated) – `?page=`, `?per_page=`, `?search=`, `?department_id=`, `?department=` |
| POST | `/employees` | Bearer | Create |
| GET | `/employees/{id}` | Bearer | Show |
| PUT/PATCH | `/employees/{id}` | Bearer | Update |
| DELETE | `/employees/{id}` | Bearer | Delete |
| GET | `/departments` | Bearer | List with employee counts |
| GET | `/departments/{id}` | Bearer | Department with its employees |
| POST | `/departments` | Bearer | Create |
| PUT/PATCH | `/departments/{id}` | Bearer | Rename |
| DELETE | `/departments/{id}` | Bearer | Delete (cascades employees) |

OpenAPI/Swagger spec: `docs/openapi.yaml` – in Postman, **Import** → select the file to see it as documented API requests.

### Seeded data
- Departments: IT (id 1), HR (id 2), Finance (id 3), Marketing (id 4)
- Employees: Juan Dela Cruz (IT), Maria Santos (HR), plus 23 random employees
- User: `test@example.com` / `password`

---

## Testing with Postman
1. Postman → **Import** → choose `docs/Integrative-Programming-2.postman_collection.json`.
2. Run **Week 6 – Auth → Register** (first time) or **Login** (afterwards). The token is saved automatically into the collection variable `{{token}}` and used by every protected request.
3. Run the other folders. Each request has a test script that checks the status code (see the **Test Results** tab).
4. You can run the entire collection with **Run collection**.

> `Register` returns `422` the second time because `admin@example.com` already exists – use `Login` instead, or run `php artisan migrate:fresh --seed`.

## Automated tests
```bash
php artisan test
```
Feature tests in `tests/Feature/` cover the students endpoint, register/login/logout, unauthorized rejection, CRUD, validation, 404s, pagination, search, filters, versioning, and the department relationship. They use an in-memory SQLite database, so your MySQL data is not touched.

---

## Git & GitHub (Week 1)
Create an empty repository on GitHub named `pfe-bsit002-integrative-programming-2`, then:

```bash
git init
git add .
git commit -m "Initial Laravel setup"
git branch -M main
git remote add origin https://github.com/<your-username>/pfe-bsit002-integrative-programming-2.git
git push -u origin main
```

Suggested commit history if you want one commit per week:
```
Week 1: Initial Laravel setup
Week 2: Add simple students REST endpoint
Week 3: Add API design document and OpenAPI spec
Week 4: Employee CRUD API with validation
Week 5: Department relationship, pagination, search and filter
Week 6: Sanctum authentication and protected routes
```

---

## Submission checklist (screenshots you need to take)

**Week 1**
- [ ] Postman `GET http://127.0.0.1:8000` showing `"status": "running"`
- [ ] GitHub repository link
- [ ] Terminal showing successful `git push`
- [ ] Reflection (`docs/WRITTEN_ANSWERS.md`)

**Week 2**
- [ ] `routes/api.php` students route
- [ ] Postman `GET /api/students` response
- [ ] Short explanation of the endpoint
- [ ] GitHub commit link

**Week 3**
- [ ] `docs/API_DESIGN.md` (endpoints, request/response samples, status codes)
- [ ] `docs/openapi.yaml` (and a screenshot of it imported in Postman)

**Week 4**
- [ ] `employees` table in phpMyAdmin
- [ ] Postman GET, POST, PUT, DELETE
- [ ] Validation explanation + reflection

**Week 5**
- [ ] Migration files (`database/migrations/2026_08_01_*`)
- [ ] `Department.php` / `Employee.php` relationship methods
- [ ] Postman: `?page=2`, `?search=juan`, `?department_id=1`
- [ ] Relationship explanation

**Week 6**
- [ ] Postman: Register, Login (token), protected route with token, protected route **without** token (401), Logout
- [ ] Exported Postman collection
- [ ] Middleware + token-based authentication explanations

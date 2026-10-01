# Written Answers – Module 1 (Weeks 1–6)

Short explanations and reflections required in each week's submission. Edit these into your own words before submitting.

---

## Week 1 – Reflection
**"What tools do I need to improve before building a Laravel API?"**

I need to get more comfortable with Composer and the Artisan commands (`make:model`, `make:controller`, `migrate`) because almost every Laravel task starts there. I also need to practice Git beyond `add`, `commit`, and `push` – especially branches and writing clear commit messages – so my repository shows my progress week by week. Finally, I need to learn Postman properly: organizing requests into collections, using variables for the base URL and token, and writing simple test scripts instead of just reading the response by eye.

---

## Week 2 – Explanation of the endpoint
`GET /api/students` is a route defined in `routes/api.php`. Laravel automatically adds the `/api` prefix to routes in that file. When a client (Postman) sends a GET request to `http://127.0.0.1:8000/api/students`, the route's closure runs and returns a PHP array through `response()->json()`, which converts it to JSON and sets the `Content-Type: application/json` header.

- **HTTP method:** GET (read data only)
- **URL:** `http://127.0.0.1:8000/api/students`
- **Status code:** 200 OK
- **Response body:** a JSON array of two student objects, each with `id`, `name`, and `course`

The data is hard-coded for now; in later weeks the data comes from the database through Eloquent.

---

## Week 4 – Validation rules used
| Field | Rule | Why |
|---|---|---|
| first_name, last_name | `required\|string\|max:100` | Every employee needs a name; the length limit protects the column size |
| email | `required\|email\|unique:employees,email` | Must be a real email format and no two employees can share one |
| department_id | `required\|integer\|exists:departments,id` | The employee must be linked to a department that actually exists |
| position | `required\|string\|max:100` | Job title is required for every employee |

On update, the rules use `sometimes`, so only the fields that are sent get validated. The unique email rule ignores the current employee's own id (`unique:employees,email,{id}`) so an employee can be saved without changing their email.

## Week 4 – Reflection
**"Why is validation important in API development?"**

An API cannot trust the client. Anyone can send any JSON body using Postman or a script, so the server must check the data before saving it. Validation keeps incomplete or incorrect records (missing names, invalid emails, duplicates, departments that don't exist) out of the database, and it gives the client a clear `422` response that lists exactly which fields are wrong. It is also a first layer of security, because it limits what kind of data can reach the database.

---

## Week 5 – Explanation of the relationship used
I used a **One-to-Many** relationship: **a Department has many Employees**, and **an Employee belongs to a Department**.

- The `employees` table has a `department_id` foreign key that references `departments.id` (`constrained()->onDelete('cascade')`).
- In `Department.php`, `employees()` returns `$this->hasMany(Employee::class)`.
- In `Employee.php`, `department()` returns `$this->belongsTo(Department::class)`.

In the controller, `Employee::with('department')` eager-loads the department of every employee in one extra query instead of one query per employee, so each employee in the response includes its department name. `GET /api/departments/{id}` does the reverse and shows a department with all its employees.

The index endpoint also uses `paginate(10)` to return only 10 records per page, `?search=` to look for a keyword in first name, last name, or email, and `?department_id=` / `?department=` to filter by department.

---

## Week 6 – Explanation of middleware
Middleware is code that runs **between the incoming request and the controller**. It can inspect the request and either let it continue or stop it with a response.

In this project:
- **`auth:sanctum`** wraps all employee and department routes. It reads the `Authorization: Bearer <token>` header, looks up the token in the `personal_access_tokens` table, and attaches the user to the request. If the token is missing or invalid, the request never reaches the controller and the API returns `401 {"message": "Unauthenticated."}`.
- **`ForceJsonResponse`** (custom middleware in `app/Http/Middleware`) runs on every API request and sets `Accept: application/json`, so Laravel always answers in JSON (for example a 401 instead of redirecting to a login page).

## Week 6 – Explanation of token-based authentication
1. The user sends their email and password to `POST /api/login` (or registers with `POST /api/register`).
2. The server checks the password against the stored hash using `Hash::check()`.
3. If correct, Sanctum creates a random token with `createToken('api-token')`. Only a hash of the token is stored in the database; the plain token is returned to the client once.
4. The client sends that token on every later request in the header `Authorization: Bearer <token>`.
5. The `auth:sanctum` middleware validates the token and identifies the user. No session or cookie is needed – the API is stateless.
6. `POST /api/logout` deletes the current token, so using it again returns `401`.

**Sanctum vs. JWT:** A JWT is a self-contained, signed token that carries the user's claims (like user id and expiry) inside it, so the server can verify it without a database lookup. Sanctum tokens are random strings that are checked against the database. Sanctum is simpler and lets us revoke a token instantly by deleting it, while JWT is useful when several separate services need to verify the same token without sharing a database.

**Authentication vs. authorization:** authentication answers *"Who are you?"* (login + token); authorization answers *"What are you allowed to do?"* (for example, only an admin may delete records).

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

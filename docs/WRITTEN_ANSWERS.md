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

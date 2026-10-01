# Week 3 – API Design Document

## 1. API Title
**Employee Management API**

## 2. Base URL
```
http://127.0.0.1:8000/api/v1
```
(The same endpoints also respond at `http://127.0.0.1:8000/api` without the version prefix.)

## 3. Resources
| Resource | Description |
|---|---|
| `employees` | Staff records (name, email, position) – each belongs to one department |
| `departments` | Organizational units – each has many employees |
| `users` (via `/register`, `/login`) | Accounts that are allowed to use the API |

Resource names are **plural nouns**. The HTTP method describes the action, so there are no verbs such as `/getEmployees` or `/deleteEmployee` in the URLs.

## 4. Endpoints

### Authentication
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| POST | `/register` | Create an account and receive a token | Public |
| POST | `/login` | Log in and receive a token | Public |
| GET | `/user` | Show the logged-in user | Bearer token |
| POST | `/logout` | Revoke the current token | Bearer token |

### Employees
| CRUD | Method | Endpoint | Description |
|---|---|---|---|
| Read all | GET | `/employees` | List employees (10 per page) |
| Read one | GET | `/employees/{id}` | Show one employee |
| Create | POST | `/employees` | Add an employee |
| Update | PUT | `/employees/{id}` | Update employee information |
| Update | PATCH | `/employees/{id}` | Partially update (e.g., email only) |
| Delete | DELETE | `/employees/{id}` | Delete an employee |
| Search | GET | `/employees?search=juan` | Search by first name, last name, or email |
| Filter | GET | `/employees?department=IT` | Filter by department name |
| Filter | GET | `/employees?department_id=1` | Filter by department id |
| Paginate | GET | `/employees?page=2&per_page=10` | Choose page and page size |

### Departments
| Method | Endpoint | Description |
|---|---|---|
| GET | `/departments` | List departments with employee count |
| GET | `/departments/{id}` | Show a department and its employees |
| POST | `/departments` | Add a department |
| PUT | `/departments/{id}` | Rename a department |
| DELETE | `/departments/{id}` | Delete a department (cascades to its employees) |

All employee and department endpoints require the header:
```
Authorization: Bearer <token>
```

## 5. Sample Request Body – `POST /employees`
```json
{
  "first_name": "Juan",
  "last_name": "Dela Cruz",
  "email": "juan@example.com",
  "department_id": 1,
  "position": "Programmer"
}
```
> The original design used `"department": "IT"`. After Week 5 (relationships), the employee stores `department_id` and the department name is returned as a nested object.

### Validation rules
| Field | Rules |
|---|---|
| first_name | required, string, max 100 |
| last_name | required, string, max 100 |
| email | required, valid email, unique in `employees` |
| department_id | required, integer, must exist in `departments` |
| position | required, string, max 100 |

On update (PUT/PATCH) every field is optional (`sometimes`), but any field that is sent must still be valid.

## 6. Sample Response Body – `201 Created`
```json
{
  "data": {
    "id": 1,
    "first_name": "Juan",
    "last_name": "Dela Cruz",
    "full_name": "Juan Dela Cruz",
    "email": "juan@example.com",
    "department_id": 1,
    "department": { "id": 1, "name": "IT" },
    "position": "Programmer",
    "created_at": "2026-08-04T10:00:00.000000Z",
    "updated_at": "2026-08-04T10:00:00.000000Z"
  }
}
```

### Paginated list – `GET /employees?page=1`
```json
{
  "data": [ { "id": 1, "first_name": "Juan", "...": "..." } ],
  "links": {
    "first": "http://127.0.0.1:8000/api/employees?page=1",
    "last": "http://127.0.0.1:8000/api/employees?page=3",
    "prev": null,
    "next": "http://127.0.0.1:8000/api/employees?page=2"
  },
  "meta": { "current_page": 1, "last_page": 3, "per_page": 10, "total": 25 }
}
```

### Error responses
```json
// 401 – no token / invalid token
{ "message": "Unauthenticated." }

// 404 – record does not exist
{ "message": "Employee not found" }

// 422 – validation error
{
  "message": "The email field must be a valid email address. (and 1 more error)",
  "errors": {
    "email": ["The email field must be a valid email address."],
    "department_id": ["The selected department id is invalid."]
  }
}
```

## 7. Status Code Plan
| Code | Meaning | When this API returns it |
|---|---|---|
| 200 | OK | Successful GET, PUT/PATCH, DELETE, login, logout |
| 201 | Created | Successful POST `/employees`, `/departments`, `/register` |
| 400 | Bad Request | Reserved for requests the server cannot understand (Laravel reports most bad input as 422) |
| 401 | Unauthorized | Missing/invalid/revoked token, or wrong login password |
| 403 | Forbidden | Reserved for role-based rules (e.g., non-admin trying an admin action) |
| 404 | Not Found | Employee or department id does not exist |
| 422 | Validation Error | Required field missing, invalid email, duplicate email, unknown department |
| 500 | Server Error | Unexpected error (e.g., database is down) |

## 8. Versioning
The API is exposed under `/api/v1/...`. If a breaking change is needed later (for example, renaming fields), a `/api/v2/...` group can be added while `v1` keeps working for existing clients.

## 9. Swagger / OpenAPI
- Specification file: [`docs/openapi.yaml`](openapi.yaml) (OpenAPI 3.0.3)
- In Postman: **Import** → select `docs/openapi.yaml`. Postman creates a documented collection from the spec.

# Tasks API (v1)

## Base URL

```
http://your-domain.com/api/v1
```

All endpoints require authentication:

```
Authorization: Bearer {token}
```

---

## GET /api/v1/projects/{project}/tasks

List tasks for a specific project.

### Response 200

```json
{
  "data": [
    {
      "id": 10,
      "project_id" : ... ,
      "title": "Implement API",
      "status": "in_progress",
      "priority": "medium",
      "due_date" : ...,
      "created_at" : ...,
      "updated_at" : ...,
    }
  ]
}
```

### Errors

- 401 Unauthenticated  
- 403 Forbidden  
- 404 Project not found  

---

## POST /api/v1/projects/{project}/tasks

Create a new task inside a project.

### Request Body

```json
{
  "title": "Write documentation",
  "description": "Prepare API docs",
  "status": "to_do",
  "priority": "high",
  "due_date": "2026-03-01"
}
```

### Response 201

```json
{
  "data": {
    "id": 25,
    "title": "Write documentation",
    "status": "to_do",
    "priority": "high"
  }
}
```

### Errors

- 401 Unauthenticated  
- 403 Forbidden  
- 422 Validation failed  

---

## PATCH /api/v1/projects/{project}/tasks/{task}

Update a task.

### Request Body (partial allowed)

```json
{
  "title": "Updated task title",
  "priority": "low"
}
```

### Response 200

```json
{
  "data": {
    "id": 25,
    "title": "Updated task title",
    "status": "to_do",
    "priority": "low"
  }
}
```

### Errors

- 401 Unauthenticated  
- 403 Forbidden  
- 404 Task not found  
- 422 Validation failed  

---

## PATCH /api/v1/projects/{project}/tasks/{task}/status

Update only the status of a task.

### Request Body

```json
{
  "status": "in_progress"
}
```

### Response 200

```json
{
  "data": {
    "id": 25,
    "status": "in_progress"
  }
}
```

---

## DELETE /api/v1/projects/{project}/tasks/{task}

Delete a task.

### Response 204

No content.

### Errors

- 401 Unauthenticated  
- 403 Forbidden  
- 404 Not found  

---

# Status Enum

```
to_do
in_progress
done
```

---

# Priority Enum

```
low
medium
high
```

# Task Manager (Laravel Backend Practice)

This repository contains a personal backend learning project built with Laravel.
The main goal of this project is to gain hands-on experience with the main work 
of a backend engineer.

The project is a task manager application that allows users to create accounts,
manage projects, and organize tasks and subtasks within those projects.
A task manager application is a common starting point when learning a new
language or framework, so this project's main aim is to focus on backend architecture
and business logic rather than UI complexity.

Before this project, I built a small blog application using vanilla PHP to
understand how things work at a lower level. That experience, combined with
working with Laravel, highlighted the value of frameworks in accelerating 
application development.

---

## Tech Stack

- PHP 8+
- Laravel Framework
- SQLite (development database)
- Laravel Queues & Jobs
- Laravel Events & Observers
- Laravel Policies

---

## Some Key Features

- Task lifecycle management (To Do ↔ In Progress ↔ Done)
- Controlled task status transitions enforced by domain rules
- Background jobs for asynchronous work ( e.g. sending overdue task reminders)
- Events and observers for task creation, deletion, and status changes
- Event and observer handling project renaming
- Centralized authorization using policies
- Centralized HTTP request validation
- Configuration-driven behavior
- Unit tests covering edge cases (e.g. progress calculation with zero values)
- Feature tests enforcing authorization rules (e.g. preventing access to other users’ tasks)

## Setup (Local)

```bash
git clone <repo-url>
cd task-manager
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

---

## Project Direction

future plans and development phases are documented here:
[Archived Roadmap](docs/roadmap.md)


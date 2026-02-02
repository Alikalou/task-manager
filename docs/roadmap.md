# Roadmap

This is a plan for turning the Task Manager into a more realistic app.

## Phase 1 — Performance + UX polishing
Goal: Make it feel fast, clean, and usable.

- Add pagination for tasks
- Add debounced search (Blade + minimal JS is fine)
- Small UX improvements:
  - Task counters by status
  - Empty states
  - Better flash messages
  - Quick action buttons

## Phase 2 — File storage
Goal: Learn real file handling with Laravel Storage.

- Add task attachments (store via Storage)
- Validate file types/sizes
- Delete attachments safely when task is deleted
- (Optional) Disk abstraction prep for S3-compatible storage later

## Phase 3 — API layer
Goal: Create clean boundaries and make the app usable by other clients later.

- Build API v1 for:
  - projects, projects/tasks, tags, subtasks
- Use Sanctum tokens (simple token auth)
- Use Resources (Transformers) + consistent error format
- Add pagination + basic rate limiting

## Phase 4 — Deployment
Goal: Prove you can ship a Laravel app.

- Environment setup (APP_KEY, configs, caches)
- Storage symlink + permissions
- Production migrations
- Logging setup
- Basic monitoring (logs + uptime ping is enough)

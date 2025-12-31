# Copilot instructions for zinzino.mlarca.com

Purpose
- Fast onboard for AI agents to edit front-end Blade/Alpine integrations and backend routes that the frontend expects.

Big picture
- Laravel app (Blade views) with lightweight frontend logic in Alpine.js inside Blade templates.
- Frontend expects JSON endpoints (clients.store, clients.update, clients.show) returning { success: true, client: { ... } } for the client modal to work.
- File upload uses a standard multipart/form-data form post to route tests.store.

Key files / places to inspect
- resources/views/tests/create.blade.php — main form + Alpine clientModal() component, drag/drop, hidden inputs and client selection logic.
- Controllers: look for ClientsController (store/update) and TestsController@store to confirm JSON shapes and validation responses (422).
- routes/web.php — verify resource routes for clients and tests.

Frontend patterns and expectations
- clientModal() (in create.blade.php):
  - Sends JSON to clients.store (POST) or clients/{id} (PATCH). Expects JSON { success, client }.
  - On success it appends/updates <option> in #client-select and sets hidden client_id.
  - If option has no data-birthdate the select change dispatches open-client-edit.
- Hidden inputs are used to ensure critical client data is submitted with the test form even if controls are disabled.
- File attachment uses drag & drop and assigns files to an <input type="file"> so normal form submission handles upload.

API contract (frontend assumptions)
- Validation errors return 422 with JSON { errors: { field: [..] } }.
- Successful client creation/updates return 200/201 and JSON: { success: true, client: { id, name, birthdate, height_cm, weight_kg, email, phone, identifier, is_self } }.

Developer workflows / useful commands
- Typical Laravel local dev:
  - composer install
  - cp .env.example .env && configure DB
  - php artisan key:generate
  - php artisan migrate
  - npm install && npm run dev (if assets compiled)
  - php artisan serve
- Debugging tips:
  - Use browser Network tab to inspect POST/PATCH from clientModal(); verify responses match expected JSON shape.
  - Check Laravel logs (storage/logs/laravel.log) for backend errors and validation details.

Project-specific conventions
- Minimal external JS libs: avoid jQuery/Select2 in these views (recent refactor removed them).
- Prefer updating DOM options directly (createElement / option) rather than re-rendering server HTML.
- Keep client payload small JSON for modal; server must return full client object for frontend to update attributes (data-birthdate, data-height, etc).

When editing the modal or API handlers, verify:
- The controller returns proper JSON for success and 422 for validation.
- Hidden client_id is set/updated before submitting the test form.
- Birthdate presence triggers edit modal if missing.

If anything is unclear or you need examples for controllers/routes, say which endpoint or use-case you want and I will expand with concrete code snippets or tests.

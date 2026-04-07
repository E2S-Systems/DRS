# GitHub Copilot Instructions for DRS ERP

This is a monorepo for the **DRS (Dynamic Resource System)**, a SaaS ERP platform for the visual communication retail sector, built by E2S Systems. The codebase uses an event-driven architecture with strict separation between presentation, business logic, and analytics.

## Project Structure

```
drs-erp/
├── backend/         # Laravel 12 (PHP 8.4) - Transactional API
├── frontend/        # Nuxt 4 (Vue 3 + TypeScript) - SPA
├── bi-service/      # FastAPI (Python 3.11) - Analytics & ML
├── infra/           # IaC manifests (Kubernetes, Docker, Prometheus)
├── docs/            # Technical documentation and governance
└── docker-compose.yml  # Local development orchestration
```

## Build, Test, and Lint

### Backend (Laravel)

All commands should be run inside the Docker container:

```bash
# Run all tests
docker compose exec backend php artisan test

# Run a single test file
docker compose exec backend php artisan test tests/Feature/UserControllerTest.php

# Run a specific test method
docker compose exec backend php artisan test --filter test_user_can_login

# Run linter (Laravel Pint)
docker compose exec backend ./vendor/bin/pint

# Run linter on specific file
docker compose exec backend ./vendor/bin/pint app/Models/User.php

# Generate API documentation (Scramble/Swagger)
docker compose exec backend php artisan scramble:generate

# Run migrations
docker compose exec backend php artisan migrate

# Run seeders
docker compose exec backend php artisan db:seed
```

### Frontend (Nuxt)

```bash
# Install dependencies
docker compose exec frontend npm install

# Development server (already running in container)
docker compose exec frontend npm run dev

# Build for production
docker compose exec frontend npm run build

# Preview production build
docker compose exec frontend npm run preview
```

### BI Service (FastAPI)

```bash
# Install dependencies
docker compose exec bi-service pip install -r requirements.txt

# Run the service (already running in container)
docker compose exec bi-service uvicorn main:app --reload --host 0.0.0.0 --port 8001
```

### Composer Scripts (Backend)

The backend has helpful Composer scripts:

```bash
# Full setup (install, migrate, build frontend assets)
docker compose exec backend composer setup

# Start all dev services (server + queue + logs + vite)
docker compose exec backend composer dev

# Run tests
docker compose exec backend composer test
```

## Architecture & Key Patterns

### Multi-Branch Isolation (Hierarchical Data Isolation)

This is **not** multi-tenancy. The system supports a single company (matrix) with multiple branches (filiais). All transactional tables include:

- `branch_id` (FK to `branches` table)
- `created_at`, `updated_at`
- `deleted_at` (soft deletes are **mandatory**)

**Context-based filtering:**
- Frontend sends `X-Branch-Id` header in all requests to indicate which branch the user is operating in
- Users without `view-all-branches` permission automatically get `->where('branch_id', auth()->user()->current_branch_id)` applied to queries
- Users with `view-all-branches` permission (e.g., directors, backoffice) see all branches

### Permissions System (Spatie)

Permissions are defined in `backend/config/permissions.php` as hierarchical arrays:

```php
return [
    'branches' => ['view', 'create', 'update', 'delete'],
    'users' => ['view', 'create', 'update', 'delete'],
    // ...
];
```

These are flattened into strings like `branches view`, `branches create` by `PermissionsHelper::getFlattenPermissions()` and seeded via `PermissionsSeeder`. Role grants are defined in `backend/config/profile-permissions.php` and synced via `RolesSeeder`.

**Admin superuser:** The `admin` role is treated as a superuser via `Gate::before` in `AppServiceProvider`, granting all abilities when `user->hasRole(RoleUser::ADMIN)`.

### Actions Pattern

Business logic is isolated in Action classes that use the `Newable` trait for clean instantiation:

```php
// In controller
use App\Actions\User\CreateUserAction;

$user = CreateUserAction::new()->execute($request->validated());
```

Actions are located in `backend/app/Actions/{Domain}/{ActionName}Action.php`.

### Request Validation

Use separate FormRequest classes for each HTTP verb:

- `backend/app/Http/Requests/Store/{Model}Request.php` for POST
- `backend/app/Http/Requests/Update/{Model}Request.php` for PUT/PATCH

### Controllers

Controllers should be thin orchestrators (max ~15 lines per method):

1. Authorize via `Gate::authorize()`
2. Validate via FormRequest
3. Invoke Action/Service
4. Return Resource (DTO)

Example pattern:

```php
public function store(StoreRequest $request): ResourceClass
{
    $model = ActionClass::new()->execute($request->validated());
    return new ResourceClass($model);
}
```

### Frontend Structure (Nuxt)

Custom directory configuration in `nuxt.config.ts`:

```
frontend/app/
├── Pages/              # Routes (auto-imported)
├── Layouts/            # Layout components
├── components/         # Vue components (auto-imported, no path prefix)
├── Composables/        # Composables
├── Plugins/            # Nuxt plugins
└── assets/css/         # Global styles
```

**Authentication:** Uses `nuxt-auth-sanctum` with token mode. Endpoints:
- Login: `/api/v1/login`
- Logout: `/api/v1/logout`
- User: `/api/v1/me`

**UI Library:** PrimeVue (Aura preset) + Tailwind CSS
**State Management:** Pinia

## CRUD Checklist (Backend)

When creating a new entity, follow this order:

1. **Permissions:** Update `config/permissions.php` and `config/profile-permissions.php`, then run `php artisan db:seed --class=PermissionsSeeder`
2. **Migration:** Create table with `branch_id` (if branch-scoped) and `$table->softDeletes()`
3. **Seeder/Factory:** Create test data (minimum 10 records)
4. **Model:** Configure `$fillable`, relationships, casts
5. **Policy:** Create Policy linking Spatie permissions with branch isolation
6. **FormRequests:** Create `Store{Model}Request` and `Update{Model}Request`
7. **Action:** Create Action class in `app/Actions/{Domain}/` for business logic
8. **Resource:** Create DTO in `app/Http/Resources/{Model}Resource.php`
9. **Controller:** Create thin orchestrator in `app/Http/Controllers/{Model}Controller.php`
10. **Routes:** Register routes in `routes/api.php`
11. **Tests:** Create Feature tests in `tests/Feature/`

## Coding Standards

### Backend (PHP)

- Use `declare(strict_types=1);` at the top of all new PHP files
- Follow Laravel conventions and PSR-12
- Use typed properties and return types
- Run `./vendor/bin/pint` before committing
- Use Actions for business logic, not Controllers
- All database queries must respect soft deletes and branch isolation

### Frontend (TypeScript)

- Use TypeScript strict mode
- Prefer Composition API with `<script setup>`
- Use auto-imported components (no manual imports needed)
- Follow Vue 3 best practices

### Commit Messages

Follow Conventional Commits:

- `feat:` New feature
- `fix:` Bug fix
- `refactor:` Code change without behavior change
- `docs:` Documentation only
- `style:` Formatting, linting
- `test:` Tests
- `chore:` Dependencies, build config
- `perf:` Performance improvement

Example: `feat: adiciona calculo de juros no parcelamento`

### Branch Naming

Pattern: `<type>/<TICKET-ID>-<short-description>`

Examples:
- `feat/DRS-102-calculo-impostos`
- `fix/DRS-105-erro-login-nulo`
- `tech-debt/DRS-110-refatorar-service-vendas`

## Definition of Done (DoD)

Before merging a PR:

- [ ] Code follows linting standards (no warnings)
- [ ] Branch isolation (`branch_id`) applied and tested where applicable
- [ ] No debug code (`dd()`, `console.log()`, `print()`) remains
- [ ] API contracts implemented and reflected in Swagger/Scramble
- [ ] At least 1 peer code review approval

## Local Development

```bash
# Start all services
docker compose up -d --build

# View logs
docker compose logs -f

# Access services:
# - Frontend: http://localhost:3000
# - Backend API: http://localhost:8000
# - API Docs (Scramble): http://localhost:8000/docs/api
# - BI Service Docs: http://localhost:8001/docs
```

## Important Documentation

Before making changes, review:

- [Technical Design Document](../docs/technical-design-document.md) - Architecture and stack details
- [Development Guide](../docs/development-guide.md) - Coding standards and workflows
- [Project Governance](../docs/project-governance.md) - Process and requirements
- [MCP Servers Guide](../docs/mcp-servers-guide.md) - How to use PostgreSQL and Docker MCP servers

## Stack Reference

**Backend:** PHP 8.4, Laravel 12, Sanctum, Scramble, Spatie (Query Builder, Data, Permission, Media Library)
**Frontend:** Vue 3, Nuxt 4, TypeScript, Tailwind CSS, PrimeVue, Pinia
**Data/Analytics:** Python 3.11, FastAPI, Polars, LangChain
**Infrastructure:** PostgreSQL 16, Redis 7, Apache Kafka
**DevOps:** Docker, Kubernetes

# Agent Rules — SureShotSupply

## Project Overview
SureShotSupply is a Laravel/PHP photography e-commerce site selling:
- **Permanent stock**: Hot shoe covers, wrist straps, neck straps
- **One-off listings**: Flipped/second-hand cameras (managed via Filament admin panel)

**Stack**: Laravel, Filament (admin), Stripe (payments), MySQL, local disk storage (images)  
**Environment**: WSL Ubuntu 22.04, VSCode + GPT Codex, GitHub repo

---

## Autonomy Level: HIGH
Crack on and commit freely within the allowed commands below. Do not stop to ask for
permission on routine tasks — make a sensible decision and move forward.

---

## Commands — Run Without Asking

### Git
- `git status`
- `git diff`
- `git log`
- `git fetch`
- `git pull`
- `git checkout -b`
- `git checkout`
- `git merge`
- `git commit`
- `git push`

### Composer & NPM
- `composer install --no-interaction`
- `composer update --no-interaction`
- `composer require <package>`
- `npm install`
- `npm run build`
- `npm run dev`

### Laravel Artisan
- `php artisan migrate --force` (local only)
- `php artisan migrate:fresh --seed` (local only)
- `php artisan db:seed`
- `php artisan make:model`
- `php artisan make:migration`
- `php artisan make:controller`
- `php artisan make:request`
- `php artisan make:policy`
- `php artisan make:job`
- `php artisan make:event`
- `php artisan make:listener`
- `php artisan make:mail`
- `php artisan make:notification`
- `php artisan make:resource`
- `php artisan make:seeder`
- `php artisan make:factory`
- `php artisan make:filament-resource`
- `php artisan make:filament-page`
- `php artisan make:filament-widget`
- `php artisan storage:link`
- `php artisan config:clear`
- `php artisan cache:clear`
- `php artisan route:clear`
- `php artisan view:clear`
- `php artisan optimize`
- `php artisan test`
- `php artisan route:list`
- `php artisan tinker` (read-only queries only)

### Testing
- `vendor/bin/pest`
- `vendor/bin/pest --coverage`
- `vendor/bin/pest --filter`
- `phpunit`

### Shell / File Navigation
- `cat`, `ls`, `find`, `grep`, `rg`
- `cp`, `mv`
- `mkdir`
- `touch`
- `chmod`

---

## Commands — Do NOT Run Without Asking
- `rm -rf`
- `php artisan migrate:fresh` or `migrate:reset` **in any environment that could be production**
- Dropping databases or tables manually
- `git push --force` / `git push --force-with-lease`
- Deleting branches (local or remote)
- Modifying `.env.production` or any production config
- `php artisan down` (maintenance mode) in production
- Changing Stripe API keys or webhook secrets
- Any `artisan` command directly against a production database

---

## Branching Strategy
```
main        → production-ready code only
develop     → integration branch, all features merge here first
feature/*   → short-lived feature branches cut from develop
hotfix/*    → cut from main, merged back to main AND develop
```

**Rules:**
- Always branch off `develop` for new features: `git checkout -b feature/my-feature develop`
- Never commit directly to `main`
- Merge `feature/*` → `develop` when complete and tests pass
- `develop` → `main` only when a release is ready
- Use descriptive branch names: `feature/stripe-checkout`, `feature/filament-camera-resource`, `hotfix/product-image-path`

---

## Commit Convention
Use conventional commits format:

```
feat: add Filament resource for camera listings
fix: correct Stripe webhook signature verification
chore: run migrations for product image column
test: add Pest tests for checkout flow
refactor: extract order status logic to service class
docs: update AGENTS.md with storage rules
```

- Keep commits scoped — one logical change per commit
- Always run tests before committing
- Push after every completed task

---

## Testing Rules
- **Write Pest tests as features are built** — not after
- Every new Model should have a corresponding Factory
- Every Filament Resource should have a basic CRUD test
- Stripe-related code **must** have test coverage (use Stripe's test mode keys in `.env.testing`)
- Test file structure mirrors `app/` structure under `tests/Feature/` and `tests/Unit/`
- Run `vendor/bin/pest` after every task before committing

---

## Database Rules
- **MySQL** for both local and production
- Always use migrations — never modify the database schema manually
- Every migration must be reversible (implement `down()` properly)
- Seeders are for **dev/test data only** — never seed production with fake data
- Use Factories for all test data generation
- Keep sensitive data (emails, addresses) out of seeders — use `fake()` helpers

---

## File Storage Rules
- Images stored on **local disk** via Laravel's `storage` disk
- Always call `php artisan storage:link` after fresh installs
- Store product images under `storage/app/public/products/`
- Store camera listing images under `storage/app/public/cameras/`
- Validate image uploads: accepted types `jpg, jpeg, png, webp`, max `5MB`
- Never store uploaded files in `public/` directly — always go through `storage`

---

## Stripe Rules
- Use **test mode keys** in `.env` locally (prefix `sk_test_`, `pk_test_`)
- Never hardcode Stripe keys — always read from `config/services.php` → `.env`
- Webhook signature verification is **mandatory** — never skip it
- Log all Stripe events to a dedicated `stripe_events` table or log channel
- Test the full checkout flow with Pest using Stripe's test card numbers

---

## Filament Rules
- Filament admin panel is for **internal use only** — protect with middleware
- All Filament resources must define `$navigationGroup` for clean sidebar grouping:
  - `Shop` — Products (straps, covers), Camera Listings
  - `Orders` — Orders, Payments
  - `Customers` — Users
  - `Settings` — General config
- Camera listings should have a clear **status field**: `draft`, `available`, `sold`
- Use Filament's `SpatieMediaLibraryFileUpload` or standard `FileUpload` for camera images

---

## Product & Listing Conventions
- **Permanent stock products** (straps, covers): managed as standard `Product` models with stock quantity
- **Camera flips**: separate `CameraListing` model with fields for condition, make, model, year, asking price, status
- Both types should share a unified **storefront** listing page
- Use `slug` fields on both models for clean URLs

---

## Code Style & Standards
- Follow **PSR-12** coding standards
- Use **typed properties** and **return types** wherever possible
- Extract business logic into **Service classes** under `app/Services/`
- Use **Form Requests** for all validation — never validate in controllers
- Use **API Resources** if building any JSON endpoints
- Avoid fat controllers — keep them thin
- Use Laravel's **Policy** classes for all authorization checks

---

## Environment Notes
- Dev environment: WSL Ubuntu 22.04
- Working directory: `/home/elum/code/sureShotSupply`
- Editor: VSCode with GPT Codex
- Local `.env` must never be committed — confirm `.env` is in `.gitignore`
- Use `.env.example` to document all required environment variables
- MySQL credentials for local dev should use a dedicated `sureshotsupply` database and user

---

## Always
- Keep changes scoped to the task at hand
- Run tests after every change
- Commit and push after completing each task
- Use descriptive commit messages following the convention above
- Each commit should represent one cohesive unit of work — a feature, fix, or chore.
  A single commit may touch multiple files and layers (model, migration, resource, test) as long as they all serve the same goal. Avoid bundling unrelated concerns together.
- If a task touches Stripe or migrations, double-check before pushing


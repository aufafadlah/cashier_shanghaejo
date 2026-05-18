# Cashier Shanghaejo - Agent Instructions

## Key Commands
- Setup: `composer run setup` (installs deps, creates .env, migrates, builds assets)
- Dev server: `composer run dev` (runs Laravel dev server, queue listener, logs, and Vite concurrently)
- Build assets: `npm run build`
- Test: `composer run test` (clears config then runs PHPUnit)

## Architecture Notes
- Multi-tenant POS system with Admin and Kasir (cashier) interfaces
- Admin controls: branch management, menus, users, inventory purchases, toppings, targets
- Kasir interface: order processing, cart management, checkout, receipt printing, history
- Critical feature: Data persistence - transactions are never deleted, only marked as failed for audit trails
- Price snapshot logic: Transaction prices are frozen at time of sale in `harga_snapshot`

## Environment
- Default database: SQLite (`database/database.sqlite`)
- To use MySQL: Update `.env` with DB_CONNECTION=mysql and appropriate credentials
- Queue connection: Database (configured in .env)
- Session storage: Database (configured in .env)

## Testing
- Run single test: `php artisan test --filter=TestName`
- Tests located in `tests/` directory
- Pest not configured - using PHPUnit
- Database migrations use table prefix `tb_` (e.g., `tb_menu`, `tb_penjualan`)
- Transaction tables: `tb_penjualan` (sales header), `tb_detail_penjualan` (sales details), `tb_harga_snapshot` (price snapshots)

## File Watching
- Vite dev server: `npm run dev` (for frontend asset watching during development)
- Laravel artisan serve: Included in `composer run dev`

## Important Conventions
- Route groups: Authenticated routes under `middleware(['auth'])`
- Admin routes prefixed with `/admin` and named `admin.*`
- Kasir routes prefixed with `/kasir` and named `kasir.*`
- Controllers separated: `App\Http\Controllers\Admin\*` and `App\Http\Controllers\Kasir\*`
- Blade views: Located in `resources/views/admin/` and `resources/views/kasir/`
- Database migration files follow pattern: `YYYY_MM_DD_HHMMSS_create_tb_{table_name}_table.php`
- All admin controllers extend base Laravel Controller and are in `App\Http\Controllers\Admin\`
- Kasir controllers are in `App\Http\Controllers\Kasir\` (currently only PesanKasirController)
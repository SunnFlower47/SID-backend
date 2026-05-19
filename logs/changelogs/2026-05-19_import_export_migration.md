# Changelog - 2026-05-19

## Added
- **Export Data UI (React)**: Created `resources/js/Pages/Tenant/DataManagement/Export/Index.jsx` to serve as the new frontend for exporting various entities (Penduduk, KK, Bansos, Pengaduan, UMKM, Surat Pengajuan). Includes interactive hover cards and dynamic Lottie/Icon assets matching the "Gold Standard".
- **Import Data UI (React)**: Created `resources/js/Pages/Tenant/DataManagement/Import/Index.jsx`. Migrated the complex Excel import forms and the JavaScript AJAX preview logic into a clean React component using Axios and TailwindCSS.
- **Session Memory**: Documented the migration decisions in `memory/2026-05-19_import_export_migration.md`.

## Changed
- **Routing Fixes**: Modified `routes/tenant/admin.php` to fix a major routing bug. The `export-import.index` route was erroneously pointing to `ImportController`. It is now properly separated into `import.*` (pointing to `ImportController`) and `export.*` (pointing to `ExportController`).
- **Sidebar Navigation**: Updated `resources/js/Components/Layout/Sidebar.jsx` to reflect the new route name (`export.index` instead of `export-import.index`) for the Export menu item.
- **Controllers**:
  - `ImportController@index` now returns `\Inertia\Inertia::render('Tenant/DataManagement/Import/Index')`.
  - `ExportController@index` now returns `\Inertia\Inertia::render('Tenant/DataManagement/Export/Index')`.

## Removed
- Obsolete Blade views `resources/views/import/index.blade.php` and `resources/views/export-import/index.blade.php` are effectively retired from active use.

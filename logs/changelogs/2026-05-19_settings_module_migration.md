# Changelog - 2026-05-19

## Added
- **Unified Settings UI (React)**: Created `resources/js/Pages/Tenant/Settings/Index.jsx` to serve as the unified frontend dashboard for all administrative and profile settings, split into four tabs: Profil Saya, Manajemen Pengguna, Role & Izin, and Sistem & Stats.
- **My Profile Features**: Integrated account profile information form, change password form (with eye visibility toggle), and account deletion danger zone inside the settings view.
- **Modals & Badges**: Implemented interactive popup modals for adding/updating system users and Spatie roles, complete with dynamic permission check list and user role badges.

## Changed
- **Profile Controller Redirect**: Modified `ProfileController@edit` to redirect directly to `settings.index` with the `tab=profile` query parameter, centralizing user profile editing.
- **Settings Controller (Inertia conversion)**:
  - Updated `SettingsController@index` to render the Inertia settings page.
  - Removed obsolete `users()` method.
  - Refactored `createUser`, `updateUser`, `deleteUser`, `createRole`, `updateRole`, and `deleteRole` to return Inertia redirects (`Redirect::back()`) rather than JSON responses to align with Inertia's automatic page state refreshes.
- **Session Memory**: Created session memory file at `memory/2026-05-19_session.md` to document session outcomes.

## Removed
- **Obsolete Blade Views**: Permanently deleted the retired blade views folder under `resources/views/settings` and `resources/views/profile`.

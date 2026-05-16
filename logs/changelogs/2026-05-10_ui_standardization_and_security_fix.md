# Changelog - 10 Mei 2026

## [UI/UX Standardization & Polish]
- **Unified Headers:** Implemented standardized 6xl bold headers with category badges across all pages (Berita, Statistik, Surat, Status, Pengaduan, Kontak).
- **Navigation Consistency:** Added "Kembali" (Back) buttons to all sub-pages for better user flow.
- **Home Page Modernization:** Updated "Berita Desa" and "Suara Warga" section headers to match the new design system.
- **AI Focus:** Replaced generic Home page cards/CTAs with AI-centric elements:
    - Added "Asisten AI" floating card with pulse animation.
    - Added "Tanya Asisten AI" CTA button that triggers the chat assistant.
- **Border Radius Optimization:** Reduced corner roundness from `rounded-3xl` to `rounded-2xl` on result cards and response blocks for a sharper, more professional look.
- **Demo Clarity:** Added helper text to the PDF attachment field clarifying its optional status for demo purposes.

## [Feature Enhancements]
- **Tracking ID Priority:** Set "Nomor Surat" as the default search mode in the "Cek Status" page and swapped UI tab positions for better UX.
- **Dynamic Status Responses:** 
    - Implemented `getTanggapan` logic to show dynamic messages based on real-time letter status.
    - Updated "Selesai" fallback message to direct citizens to the village office for physical document collection.

## [Bug Fixes & Technical Improvements]
- **Copy Functionality Fix:** 
    - Rewrote `copyToClipboard` to prioritize `navigator.clipboard` API with a robust `execCommand` fallback.
    - Fixed a UI bug where a decorative blur element was blocking copy button clicks by adding `pointer-events-none`.
- **Frontend Syntax Fixes:** Resolved build errors caused by unclosed `<main>` tags and mismatched `div` structures.
- **CSP & Security Overhaul:** 
    - Updated `CspNonceMiddleware` to whitelist necessary external CDNs (Tailwind, reCAPTCHA, Cloudflare).
    - Cleaned up `.htaccess` from conflicting hardcoded CSP headers.
- **API Robustness:**
    - Moved AI credentials to `config/services.php` to support production environment caching.
    - Fixed PHP syntax errors in `AiController.php`.

**Status:** Production Ready & Juara Vibe Coding Compliant 🚀🏆🏁

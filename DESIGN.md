# Design System: Programaciones
**Project ID:** (Pendiente — se asignará al crear el proyecto en Stitch)

## 1. Visual Theme & Atmosphere
A **professional SaaS-grade academic management platform** built for Colombia's SENA (Servicio Nacional de Aprendizaje). The aesthetic is **clean, airy, and institutional** — inspired by modern admin dashboards with generous whitespace, glassmorphism sidebar, and a delicate blue-tinted gradient background. The overall mood is **trustworthy, organized, and approachable**, reflecting the educational mission of the institution. The vibe combines government-institutional gravitas with a fresh, modern interface that feels alive through subtle micro-animations and hover transitions.

## 2. Color Palette & Roles

### Primary Colors
- **SENA Institutional Green** (#39A900) — The hero color, used for primary buttons, active navigation states, and brand accent. Represents the core SENA brand identity.
- **Deep SENA Green** (#007832) — For gradient endpoints in primary CTA buttons and active nav backgrounds. Adds depth and authority.
- **Dark Forest Green** (#2D7A00) — Hover states for primary buttons. Communicates interaction feedback.
- **Mint Whisper Green** (#E8F5E8) — Light green wash for hover backgrounds on nav items and icon containers. Soft, non-intrusive accent.

### Secondary Colors (Blue Theme)
- **Soft Royal Blue** (#1565C0) — Used in welcome badges and subtle UI accents. Represents the "blue wash" background theme.
- **Periwinkle Mist** (#BBDEFB) — Radial gradient background tint and stat-grid borders. Creates the distinctive airy blue atmosphere.
- **Glass Blue** (rgba(21, 101, 192, 0.08)) — Transparent overlay for glass effects.

### Neutrals
- **Pure White** (#FFFFFF) — Card backgrounds, button text on dark bg
- **Snow Gray** (#F9FAFB) — Table headers, form headers, subtle section backgrounds
- **Feather Gray** (#F3F4F6) — Secondary backgrounds
- **Silver** (#E5E7EB) — Borders, dividers
- **Slate** (#6B7280) — Secondary body text, labels
- **Charcoal** (#374151) — Standard body text
- **Near Black** (#1F2937) — Primary headings, important text
- **Ink** (#111827) — Page titles, stat numbers

### Semantic Colors
- **Alert Red** (#EF4444) — Danger buttons, error states, delete actions
- **Warning Amber** (#EAB308) — Caution states
- **Info Blue** (#3B82F6) — Info badges, program stat icons
- **Violet Accent** (#8B5CF6) — Fichas stat icon cards
- **Emerald Status** (#10B981) — Active/success status indicators

## 3. Typography Rules
- **Primary Font:** Work Sans (SENA institutional) / Public Sans (project current) — clean, geometric sans-serif
- **Headings:** Weight 700-800, tight letter-spacing (-0.02em for hero titles)
- **Body:** Weight 400-500, 14px standard, color `#374151`
- **Navigation Labels:** Weight 500, 14px, color `#4B5563`
- **Section Headers:** Weight 700, 11px, UPPERCASE, letter-spacing 0.08em, color `#6B7280`
- **Stat Numbers:** Weight 700, 36px, color `#111827`
- **Micro Text:** Weight 500-600, 12-13px for descriptions, badges

## 4. Component Stylings

### Buttons
- **Primary:** Gradient from `#39A900` to `#007832` (135deg), gently rounded (10px), green shadow glow (`rgba(57,169,0,0.2)`), text white, lifts on hover (`translateY(-1px)`)
- **Secondary:** White background, `#D1D5DB` border, 8px radius, darkens border on hover
- **Danger:** Solid `#EF4444`, 8px radius, subtle shadow

### Cards / Containers
- **Stat Cards:** Frosted glass effect (`rgba(255,255,255,0.85)` + `backdrop-filter: blur(12px)`), 12px generously rounded corners, whisper-soft blue-tinted shadow
- **Table Containers:** Pure white, 12px radius, minimal border, slight shadow
- **Form Cards:** Pure white, 12px radius, with gray header section

### Sidebar
- **Background:** Translucent white (`rgba(255,255,255,0.85)`) with 12px blur
- **Width:** 256px fixed
- **Active Item:** Full green gradient background with white text and green glow shadow
- **Hover:** Light green wash with 4px slide-right animation

### Inputs / Forms
- **Border:** 1px solid `#D1D5DB`, 8px radius
- **Focus:** Green border ring (`#39A900`) with 3px soft green glow (`rgba(57,169,0,0.1)`)
- **Background:** Pure white

## 5. Layout Principles
- **Global Background:** Radial gradient from `#BBDEFB` (top-right) to `#F0F4F8`
- **Content Area:** Max-width 1200px, centered, 32px padding
- **Grid:** CSS Grid with `repeat(auto-fit, minmax(200px, 1fr))` for stat cards
- **Spacing:** 24px gap between sections, 20px gap within grids
- **Header:** Sticky, frosted glass (`rgba(255,255,255,0.8)` + blur), z-index 20
- **Sidebar + Layout:** Flexbox with `height: 100vh`, sidebar fixed width, main content scrollable

## 6. Design System Notes for Stitch Generation

```
DESIGN SYSTEM (REQUIRED):
- Platform: Web, Desktop-first
- Theme: Light, institutional-modern, glassmorphism accents
- Background: Soft radial blue gradient (#BBDEFB to #F0F4F8)
- Primary Accent: SENA Green (#39A900) for buttons, active states, CTAs
- Secondary: Deep Green gradient (#007832) for depth in CTAs
- Surface/Cards: Frosted white (rgba(255,255,255,0.85)) with backdrop-blur(12px), 12px rounded
- Text Primary: Near Black (#111827) for headings
- Text Secondary: Slate (#6B7280) for labels and descriptions
- Sidebar: 256px fixed, glassmorphism white, green active gradient
- Buttons: Green gradient (135deg), 10px rounded, subtle lift on hover
- Inputs: White bg, gray border, green focus ring with glow
- Tables: White bg, gray-50 header, hover row highlight
- Font: Work Sans or Inter, clean geometric sans-serif
- Icons: Ionicons outline style, 20-24px
- Shadows: Whisper-soft, blue-tinted (rgba(21,101,192,0.05))
- Animations: Cubic-bezier(0.4, 0, 0.2, 1) transitions, 0.2-0.3s
- Language: Spanish (all labels, titles in Spanish)
```

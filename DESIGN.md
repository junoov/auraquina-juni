# Auraquina Admin Design System

## 1. Atmosphere & Identity

Auraquina admin is a clean, focused command surface for Indonesian operators. Two themes only: crisp white for daylight work, deep black for low-light focus. The signature is high-contrast clarity - every element earns its place through contrast and spacing alone, never through decorative color.

## 2. Color

### Palette

| Role | Token | White | Black | Usage |
|------|-------|-------|-------|-------|
| Brand/cream | `--aq-cream` | `#F3F4F6` | `#1F2937` | Soft highlights, stat wells |
| Brand/warm | `--aq-warm` | `#FFFFFF` | `#000000` | Main background |
| Brand/sand | `--aq-sand` | `#E5E7EB` | `#1F2937` | Dividers, secondary surfaces |
| Brand/ink | `--aq-brown` | `#111827` | `#F9FAFB` | Primary actions, active nav |
| Brand/ink-hover | `--aq-brown-hover` | `#1F2937` | `#E5E7EB` | Hover state |
| Brand/text | `--aq-ink` | `#111827` | `#F9FAFB` | Primary text |
| Brand/muted | `--aq-muted` | `#6B7280` | `#9CA3AF` | Helper text, metadata |
| Surface/base | `--aq-surface-base` | `#FFFFFF` | `#000000` | App shell |
| Surface/panel | `--aq-panel` | `#FFFFFF` | `#111827` | Cards, panels |
| Surface/subtle | `--aq-surface-subtle` | `#F3F4F6` | `#1F2937` | Stat wells, inactive rows |
| Border/default | `--aq-border` | `#E5E7EB` | `#1F2937` | Field, card, table borders |
| Border/strong | `--aq-border-strong` | `#D1D5DB` | `#374151` | Focus-adjacent structure |
| Status/success | `--aq-success` | `#059669` | `#34D399` | Paid, complete, stock safe |
| Status/warning | `--aq-warning` | `#D97706` | `#FBBF24` | Needs attention |
| Status/error | `--aq-danger` | `#DC2626` | `#F87171` | Failed, cancelled |
| Status/info | `--aq-info` | `#6B7280` | `#9CA3AF` | Draft, waiting |

### Rules

- Use black/white only for primary action, current location, and keyboard focus.
- Use semantic status tokens for state meaning.
- Add any new raw color here before using it in PHP, Blade, or CSS.

## 3. Typography

### Scale

| Level | Size | Weight | Line Height | Tracking | Usage |
|-------|------|--------|-------------|----------|-------|
| Display | `clamp(32px, 4vw, 44px)` | 650 | 1.1 | -0.02em | Rare admin landing title |
| H1 | `clamp(26px, 3vw, 34px)` | 650 | 1.18 | -0.015em | Page titles |
| H2 | `clamp(22px, 2.2vw, 28px)` | 620 | 1.25 | -0.01em | Dashboard section title |
| H3 | `18px` | 620 | 1.35 | 0 | Widget titles |
| Body/lg | `16px` | 450 | 1.65 | 0 | Intro copy |
| Body | `14px` | 450 | 1.6 | 0 | Default admin text |
| Body/sm | `13px` | 450 | 1.55 | 0 | Table metadata, helper copy |
| Caption | `12px` | 560 | 1.45 | 0.02em | Badges, field hints |
| Overline | `11px` | 650 | 1.3 | 0.08em | Optional section labels |

### Font Stack

- Primary admin: `Instrument Sans, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif`.
- Numeric data uses same family with `font-variant-numeric: tabular-nums`.
- Storefront may keep its serif display system; admin does not use storefront serif headings.

### Rules

- Body text never below 13px in dense tables, never below 14px for primary reading.
- Use sentence case for Indonesian labels.
- Prefer verbs on buttons: `Buka pesanan`, `Simpan stok`, `Tambah produk`.

## 4. Spacing & Layout

### Base Unit

All admin spacing derives from base 4px.

| Token | Value | Usage |
|-------|-------|-------|
| `--aq-space-1` | `4px` | Icon-to-label gap, tight separators |
| `--aq-space-2` | `8px` | Compact inline groups |
| `--aq-space-3` | `12px` | Field padding, badge padding |
| `--aq-space-4` | `16px` | Default mobile card padding |
| `--aq-space-5` | `20px` | Comfortable groups |
| `--aq-space-6` | `24px` | Default desktop card padding |
| `--aq-space-8` | `32px` | Dashboard widget gap |
| `--aq-space-10` | `40px` | Major dashboard separation |
| `--aq-space-12` | `48px` | Page-level breaks |

### Grid

- Max content width follows Filament container rules; do not force new shell width.
- Stack dashboard widgets in one outer column so charts and tables keep their full width. Only the priority-stat grid adapts from 1 column on mobile, to 2 on tablet, and 4 on wide desktop.
- Minimum touch target: 44px height or width on mobile for buttons, nav, table actions.

### Rules

- No horizontal overflow. Tables may scroll inside Filament containers, never force page overflow.
- Reduce dashboard height by prioritizing four metrics before charts and lists.
- Group navigation by user task, not data model internals.

## 5. Components

### Admin stat card

- **Structure**: icon well, metric label, large numeric value, one short description, optional action URL.
- **Variants**: priority, warning, danger, neutral.
- **Spacing**: `--aq-space-4` mobile, `--aq-space-5` desktop.
- **States**: hover uses subtle tonal shift; focus uses brown ring.
- **Accessibility**: labels must be plain Indonesian and understandable without color.
- **Motion**: opacity/transform only, disabled under reduced motion.

### Admin navigation group

- **Structure**: Heroicon, plain Indonesian group label, model links under task group.
- **Variants**: product stock, orders, promotion, settings.
- **Spacing**: Filament default plus 44px mobile target rule.
- **States**: active item uses brown text on cream panel.
- **Accessibility**: icons support recognition only; text carries meaning.
- **Motion**: Filament default collapse only.

### Stock status summary

- **Structure**: Heroicon, concise label, numeric value, short helper text.
- **Variants**: `Aman`, `Hampir habis`, `Habis`.
- **Spacing**: `--aq-space-4` card padding, `--aq-space-3` inner gap.
- **States**: status color plus text label; no emoji.
- **Accessibility**: status meaning not color-only.
- **Motion**: none.

## 6. Motion & Interaction

### Timing

| Type | Duration | Easing | Usage |
|------|----------|--------|-------|
| Micro | `120ms` | `ease-out` | Button press, row hover |
| Standard | `200ms` | `ease-in-out` | Sidebar/nav state shifts |
| Emphasis | `320ms` | `cubic-bezier(0.16, 1, 0.3, 1)` | Dashboard widget entry if used |

### Rules

- Animate `transform`, `opacity`, `background-color`, `border-color`, `box-shadow` only.
- Every interactive surface needs visible hover, active, and focus state.
- Respect `prefers-reduced-motion: reduce` by removing non-essential transitions.

## 7. Depth & Surface

### Strategy

Mixed depth using monochrome tonal shifts: pure white/black backgrounds, near-white/near-black panels, and subtle gray borders. No warm tones. Shadows use neutral black/white at low opacity.

| Level | Value | Usage |
|-------|-------|-------|
| Border/default | `1px solid var(--aq-border)` | Cards, fields, tables |
| Border/strong | `1px solid var(--aq-border-strong)` | Focus-adjacent |
| Shadow/soft | `0 1px 3px rgba(0,0,0,0.08)` | Priority cards (white theme) |
| Shadow/overlay | `0 8px 24px rgba(0,0,0,0.12)` | Dropdowns, modals |

### Rules

- No glassmorphism, gradients, or decorative glow.
- Do not nest card surfaces unless Filament requires it.
- Two themes only: white (#FFFFFF base) and black (#000000 base).
- No warm brown/sand/cream tones in any theme.

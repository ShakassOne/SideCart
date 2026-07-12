# Shakass Side Cart Pro

Version: `1.0.0-beta.1`

Phase 1 foundation for an original WooCommerce Ajax side cart plugin.

## Included in 1.0.0-beta.1

- Plugin bootstrap with namespaced autoloader.
- WooCommerce dependency notice and HPOS compatibility declaration.
- Activation/deactivation defaults.
- Frontend drawer, overlay, floating cart icon, shortcodes and public JS API.
- REST endpoints for cart read, quantity update and item removal using WordPress REST nonces.
- Overrideable templates in `/templates/` with theme overrides from `shakass-side-cart/`.
- More maintainable Phase 1 services and JavaScript modules with safer DOM rendering and localized labels.
- Sanitized versioned settings schema and a minimal WooCommerce admin settings page.
- Phase 2 design CSS variables generated from settings.
- Phase 3 foundations for coupons, reward progress, and cross-sell recommendations.

## Changelog

### 1.0.0-beta.1

- Added generated design variables, admin design/reward toggles, Ajax coupon application/removal, reward progress data, and cross-sell recommendation data/rendering.

### 1.0.0-alpha.3

- Added Settings API registration, admin settings fields, stricter settings sanitization, safer template path normalization, expanded shortcode helper code, and uninstall data synchronization.

### 1.0.0-alpha.2

- Refined the Phase 1 foundation with a dedicated asset service, expanded REST argument validation, improved server-side cart validation, safer frontend DOM rendering, and clearer accessibility/live-region behavior.

### 1.0.0-alpha.1

- Initial Phase 1 foundation.

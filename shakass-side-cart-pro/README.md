# Shakass Side Cart Pro

Version: `1.0.0-beta.2`

Module original de panier latéral Ajax pour WooCommerce, développé pour Shakass Communication.

## Inclus dans 1.0.0-beta.2

- Interface publique et interface d’administration traduites en français.
- Menu d’administration déplacé au premier niveau de WordPress, en dehors du sous-menu WooCommerce.
- Chargement explicite du panier WooCommerce côté REST pour garder le drawer synchronisé avec la session panier.
- Requêtes Ajax configurées avec les cookies de session du site afin de lire le bon panier client.
- Bootstrap du module avec autoloader namespacé.
- Notice de dépendance WooCommerce et déclaration de compatibilité HPOS.
- Valeurs par défaut à l’activation/désactivation.
- Tiroir frontend, voile, icône flottante, shortcodes et API JavaScript publique.
- Endpoints REST pour lire le panier, modifier les quantités, retirer des articles et gérer les codes promo avec les nonces REST WordPress.
- Templates surchargeables dans `/templates/` avec overrides de thème depuis `shakass-side-cart/`.
- Services PHP et modules JavaScript maintenables avec rendu DOM sécurisé et libellés localisés.
- Schéma de réglages versionné et assaini avec page d’administration dédiée.
- Variables CSS de design générées depuis les réglages.
- Bases pour les codes promo, la progression de récompense et les recommandations cross-sell.

## Journal des modifications

### 1.0.0-beta.2

- Traduction française des libellés front/admin, passage du menu admin au premier niveau WordPress, ajout du chargement explicite du panier WooCommerce dans le service REST et envoi des cookies de session dans les requêtes Ajax.

### 1.0.0-beta.1

- Ajout des variables de design générées, réglages admin design/récompenses, application/retrait Ajax des codes promo, données de progression récompense et rendu/données des recommandations cross-sell.

### 1.0.0-alpha.3

- Ajout de l’enregistrement Settings API, champs de réglages admin, assainissement renforcé des réglages, normalisation plus sûre des chemins de templates, helpers shortcode étendus et synchronisation des données de désinstallation.

### 1.0.0-alpha.2

- Amélioration de la base Phase 1 avec un service d’assets dédié, validation REST étendue, validation serveur du panier renforcée, rendu DOM frontend plus sûr et comportement d’accessibilité/live-region clarifié.

### 1.0.0-alpha.1

- Base initiale de la Phase 1.

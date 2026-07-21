# Shakass Side Cart Pro

Version: `1.0.0-beta.9`

Module original de panier latéral Ajax pour WooCommerce, développé pour Shakass Communication.

## Inclus dans 1.0.0-beta.9

- Correction de l’erreur serveur lors de l’ajout T-Shirt Studio : la route REST est maintenant détectée sans accéder à l’objet serveur REST indisponible dans ce contexte.

## Inclus dans 1.0.0-beta.8

- Compatibilité avec T-Shirt Studio 2.0 : les ajouts personnalisés transmis par l’endpoint REST `/wp-json/tsl2/v1/cart` mémorisent l’ouverture du drawer pour la page cible de sa redirection.

## Inclus dans 1.0.0-beta.7

- Interception des ajouts non Ajax WooCommerce : le client revient sur la page d’origine et le drawer s’ouvre automatiquement au lieu d’être redirigé vers la page panier.
- Prise en charge des produits WooCommerce natifs et des modules de personnalisation qui utilisent le flux d’ajout WooCommerce.
- Synchronisation du drawer après les ajouts Ajax sans ouverture intempestive lors du seul rafraîchissement des fragments WooCommerce.

## Inclus dans 1.0.0-beta.6

- Visuels produits de 150 × 150 px dans le panier latéral, avec une mise en page qui réserve l’espace des prix et du bouton de retrait.
- Bouton de retrait centré et mises à jour de quantité/suppression basées sur la clé réelle de la ligne WooCommerce, y compris pour les articles personnalisés.
- Progression commerciale de livraison gratuite configurable depuis l’administration.

## Inclus dans 1.0.0-beta.5

- Affichage des mockups personnalisés T-Shirt Studio dans le drawer via les helpers et filtres panier WooCommerce.
- Correction de la sauvegarde des cases décochées dans l’administration.
- Ajout du shortcode `[ssc_menu_cart]` utilisable dans les libellés de menus WordPress et dans les contenus.
- Séparation visuelle du compteur d’articles et du total sur l’icône flottante.
- Fiabilisation des changements de quantité Ajax depuis le drawer.
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

### 1.0.0-beta.9

- Correction de l’erreur 500 déclenchée pendant l’ajout au panier depuis T-Shirt Studio 2.0.

### 1.0.0-beta.8

- Le drawer est désormais ouvert automatiquement après l’ajout d’un produit personnalisé via l’endpoint REST de T-Shirt Studio 2.0.

### 1.0.0-beta.7

- Interception des redirections WooCommerce après ajout au panier afin d’ouvrir le drawer sur la page d’origine, y compris pour les ajouts issus d’un module de personnalisation compatible WooCommerce.

### 1.0.0-beta.5

- Refonte graphique premium sombre du drawer frontend, icône menu, bouton flottant, coupons, produits, recommandations, totaux et boutons via CSS scoped.
- Ajout d’une sauvegarde du CSS frontend précédent dans `public/assets/css/frontend.css.bak-1.0.0-beta.4`.


### 1.0.0-beta.4

- Affichage des visuels personnalisés T-Shirt Studio dans le drawer et lien vers la reprise de personnalisation quand disponible.

### 1.0.0-beta.3

- Correction de la sauvegarde des cases décochées, ajout du shortcode menu `[ssc_menu_cart]`, séparation visuelle du compteur et du total du bouton flottant, et fiabilisation des changements de quantité Ajax depuis le drawer.

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

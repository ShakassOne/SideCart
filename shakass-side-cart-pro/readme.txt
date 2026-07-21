=== Shakass Side Cart Pro ===
Contributors: shakass
Tags: woocommerce, panier, ajax, side-cart
Requires at least: 6.4
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0-beta.15
License: Proprietary

Module original de panier latéral Ajax pour WooCommerce, développé pour Shakass Communication.

== Journal des modifications ==

= 1.0.0-beta.15 =
* Les états survol, focus et clic du bouton Switch imposent désormais un fond transparent afin d’empêcher le fond rose du thème.

= 1.0.0-beta.14 =
* Suppression des bordures, ombres et animations CSS du bouton Switch ; seules les images normale et survolée sont affichées.

= 1.0.0-beta.13 =
* Les emplacements des icônes Switch sont conservés, sans inclure leurs fichiers PNG dans le plugin.

= 1.0.0-beta.12 =
* Le bouton de bascule recto-verso utilise les icônes Switch et SwitchH fournies.
* L’animation de bascule des visuels est ralentie.

= 1.0.0-beta.11 =
* Ajout de l’affichage recto-verso sécurisé pour les lignes T-Shirt Studio 2.
* Retour complet à l’image WooCommerce si les helpers TSL sont indisponibles ou invalides.

= 1.0.0-beta.9 =
* Correction de compatibilité avec l’endpoint REST de T-Shirt Studio : la détection de la route n’utilise plus l’objet serveur REST, ce qui évite l’erreur 500 pendant l’ajout au panier.

= 1.0.0-beta.8 =
* Compatibilité ajoutée avec T-Shirt Studio 2.0 : après un ajout via `/wp-json/tsl2/v1/cart`, le drawer s’ouvre automatiquement sur la page cible de la redirection du studio.

= 1.0.0-beta.7 =
* Les ajouts non Ajax (produits WooCommerce natifs ou modules de personnalisation) reviennent désormais sur la page d’origine et ouvrent automatiquement le drawer, au lieu de rediriger vers la page panier.
* Les ajouts Ajax et les événements `ssc:item-added` ouvrent également le drawer après synchronisation du panier.

= 1.0.0-beta.6 =
* Agrandissement des visuels produits, amélioration de l’alignement des informations et centrage du bouton de suppression.
* Fiabilisation des mises à jour et suppressions des lignes du panier WooCommerce, y compris pour les produits personnalisés.
* Libellés de progression centrés sur la livraison gratuite.

= 1.0.0-beta.5 =
* Refonte graphique premium sombre du drawer frontend, icône menu, bouton flottant, coupons, produits, recommandations, totaux et boutons via CSS scoped.
* Ajout d’une sauvegarde du CSS frontend précédent dans `public/assets/css/frontend.css.bak-1.0.0-beta.4`.


= 1.0.0-beta.4 =
* Affichage des visuels personnalisés T-Shirt Studio dans le drawer et lien vers la reprise de personnalisation quand disponible.

= 1.0.0-beta.3 =
* Correction de la sauvegarde des cases décochées, ajout du shortcode menu `[ssc_menu_cart]`, séparation visuelle du compteur et du total du bouton flottant, et fiabilisation des changements de quantité Ajax depuis le drawer.

= 1.0.0-beta.2 =
* Traduction française des libellés front/admin, menu admin au premier niveau WordPress, chargement explicite du panier WooCommerce dans le service REST et envoi des cookies de session dans les requêtes Ajax.

= 1.0.0-beta.1 =
* Ajout des variables de design générées, réglages admin design/récompenses, application/retrait Ajax des codes promo, données de progression récompense et rendu/données des recommandations cross-sell.

= 1.0.0-alpha.3 =
* Ajout de l’enregistrement Settings API, champs de réglages admin, assainissement renforcé des réglages, normalisation plus sûre des chemins de templates, helpers shortcode étendus et synchronisation des données de désinstallation.

= 1.0.0-alpha.2 =
* Amélioration de la base Phase 1 avec un service d’assets dédié, validation REST étendue, validation serveur du panier renforcée, rendu DOM frontend plus sûr et comportement d’accessibilité/live-region clarifié.

= 1.0.0-alpha.1 =
* Base initiale de la Phase 1.

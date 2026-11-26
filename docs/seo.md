# SEO et performance — `inc/seo.php` + `inc/performance.php`

## SEO de base, sans extension

| Quoi | Où | Détail |
|---|---|---|
| `<meta name="description">` | Toutes les pages (sauf recherche et 404) | 160 caractères max |
| Open Graph + Twitter Card | Idem | Titre, description, image : aperçu des liens partagés (LinkedIn, Facebook, WhatsApp, Slack…) |
| JSON-LD `LocalBusiness` | Accueil | Nom, description, téléphone, e-mail, adresse, logo, réseaux — depuis **Infos du site** |

**D'où vient la description** (dans l'ordre) : l'**extrait** du contenu (champ « Extrait », activé aussi sur les pages) → le texte du **Hero / Hero slider** en tête de page → le début du texte → pour les listes : description de la catégorie / du type de contenu / contenu de la page des articles → le **slogan** du site.

**Image de partage** : image mise en avant → image du Hero (ou 1re image du Hero slider) → icône du site (Apparence > Personnaliser > Identité).

**Adresse** dans « Infos du site » : rue sur la 1re ligne (ou plusieurs), **code postal + ville sur la dernière** (`59800 Lille`) — c'est ce format qui est découpé en `streetAddress` / `postalCode` / `addressLocality`.

**Type d'entreprise** : `LocalBusiness` par défaut. Pour un restaurant, une boutique… :

```php
add_filter( 'starter_schema_type', fn() => 'Restaurant' );
// Ajouter des horaires, une zone desservie… :
add_filter( 'starter_schema', function ( $schema ) {
	$schema['openingHours'] = 'Mo-Fr 09:00-18:00';
	return $schema;
} );
```

**Avec une extension SEO** (Yoast, Rank Math, SEOPress, All in One SEO, The SEO Framework) : tout ce fichier se désactive tout seul, pas de balises en double.

Vérifier : `curl -s http://site/ | grep -E 'og:|description|ld\+json'`, puis l'[outil de test des résultats enrichis](https://search.google.com/test/rich-results) une fois en ligne.

## Performance

| Quoi | Pourquoi |
|---|---|
| `<link rel="preload">` de `onest.woff2` | Le grand titre est l'élément principal du 1er écran (LCP) : sa police arrive plus tôt, sans saut de texte |
| Tailles d'images en **WebP** (`image_editor_output_format`) | ≈ 30 % plus légères que le JPEG. Seulement si le serveur sait produire du WebP. Images déjà en ligne : `wp media regenerate` |
| 1re image du Hero en `fetchpriority="high"` | Déjà dans les blocs Hero / Hero slider |
| JS par module, chargé seulement si la page en a besoin | `assets/src/js/main.js` |

> Changer de police des titres : mettre à jour le chemin dans `starter_preload_fonts()` **et** dans `theme.json` (les deux URL doivent être identiques, sinon la police est téléchargée deux fois).

## Lighthouse

```bash
npx lighthouse http://wordpress-starter.local/ --view
```

Résultat de référence (mobile, en local) : **Performance 100 · Accessibilité 100 · SEO 100**. « Bonnes pratiques » reste à 79 en local uniquement à cause de l'absence de HTTPS : à 100 en production avec HTTPS et redirection.

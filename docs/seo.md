# SEO, sitemap, robots.txt, llms.txt, performance

Le SEO est dans le **mu-plugin** (`mu-plugin/starter-core/seo/`) et pas dans le thème : c'est du contenu, il doit survivre à un changement de thème.

| Fichier            | Rôle                                                                                                                            |
| ------------------ | ------------------------------------------------------------------------------------------------------------------------------- |
| `seo/settings.php` | Page **Réglages > SEO** (image par défaut, présentation llms.txt, robots IA, lignes robots.txt) + ouverture du site aux moteurs |
| `seo/fields.php`   | Encadré **« SEO »** sous l'éditeur de chaque page / article / réalisation + colonne « SEO » dans les listes                     |
| `seo/head.php`     | `<title>`, meta description, `noindex`, Open Graph, JSON-LD `LocalBusiness`                                                     |
| `seo/crawlers.php` | Sitemap, `robots.txt`, `/llms.txt`                                                                                              |

**Avec une extension SEO** (Yoast, Rank Math, SEOPress, All in One SEO, The SEO Framework) : tout le module s'efface (pas d'encadré, pas de balises, robots.txt et sitemap laissés à l'extension).

## Pour le client : changer le titre et la description d'une page

Encadré **SEO** sous l'éditeur de la page :

| Champ                   | Vide =                                                                                  | Conseil                                                                                   |
| ----------------------- | --------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------- |
| Titre dans Google       | « Titre de la page – Nom du site »                                                      | 50–60 caractères, mot-clé + ville + marque                                                |
| Description dans Google | Extrait → texte du Hero → début du contenu → slogan                                     | 120–160 caractères, donne envie de cliquer                                                |
| Image de partage        | Image mise en avant → image du Hero → image par défaut (Réglages > SEO) → icône du site | 1200 × 630 px                                                                             |
| Ne pas indexer          | —                                                                                       | Page de remerciement, page en construction : absente de Google, du sitemap et de llms.txt |

L'encadré montre un **aperçu Google** en direct et un compteur (vert / orange / rouge). Les valeurs automatiques sont affichées en gris dans les champs vides.

La page des articles (Actualités) : ses champs SEO servent pour la liste des articles. Les catégories utilisent leur description.

## Sitemap — `/wp-sitemap.xml`

Le sitemap **natif** de WordPress (5.5+), complété :

- sans les contenus cochés « Ne pas indexer » ;
- sans les étiquettes ni les auteurs (`security.php`) ;
- les messages de contact ne sont pas publics : jamais dans le sitemap.

À déclarer dans la Google Search Console une fois en ligne.

## robots.txt — `/robots.txt`

Fichier **virtuel** (supprimer un éventuel vrai fichier `robots.txt` à la racine, qui passerait devant) :

```
User-agent: *
Disallow: /wp-admin/
Allow: /wp-admin/admin-ajax.php
Disallow: /?s=
Disallow: /search/
(lignes de Réglages > SEO)
(robots d'entraînement des IA, si la case est cochée)
Sitemap: https://site.fr/wp-sitemap.xml
```

**Site fermé** (`Disallow: /` + `noindex` sur toutes les pages) quand :

- Réglages > Lecture : « Demander aux moteurs de ne pas indexer » est coché ;
- ou `WP_ENVIRONMENT_TYPE` vaut **`staging`** (préprod) : elle ne finira jamais dans Google, même si on oublie la case.

Un site `local` n'est pas fermé (inaccessible depuis Internet de toute façon, et Lighthouse peut auditer le SEO). Forcer : filtre `starter_seo_site_indexable`.

**Robots des IA** (case dans Réglages > SEO) : bloque les robots qui **entraînent** des modèles (GPTBot, Google-Extended, CCBot, ClaudeBot, Applebot-Extended…). Ceux des moteurs de recherche et des assistants qui citent leurs sources ne sont pas concernés. Liste : filtre `starter_seo_ai_training_bots`.

## llms.txt — `/llms.txt`

Résumé du site en Markdown pour les IA (format [llmstxt.org](https://llmstxt.org)) : nom, description de l'accueil, **présentation** (Réglages > SEO), coordonnées (Infos du site), puis une section par type de contenu (pages, articles, réalisations…) avec la description SEO de chaque contenu. Sans les contenus « Ne pas indexer ».

- Mis en cache 12 h, vidé à chaque modification de contenu ou de réglage.
- 404 si le site est fermé aux moteurs ou si une extension SEO est active.
- Modifier le contenu : filtre `starter_seo_llms`.

## Données structurées (accueil)

JSON-LD `LocalBusiness` depuis **Infos du site** : nom, description, téléphone, e-mail, adresse, logo, réseaux.

**Adresse** : rue sur la 1re ligne (ou plusieurs), **code postal + ville sur la dernière** (`59800 Lille`) — format découpé en `streetAddress` / `postalCode` / `addressLocality`.

```php
add_filter( 'starter_schema_type', fn() => 'Restaurant' ); // Store, ProfessionalService…
add_filter( 'starter_schema', function ( $schema ) {
	$schema['openingHours'] = 'Mo-Fr 09:00-18:00';
	return $schema;
} );
```

Vérifier une fois en ligne : [test des résultats enrichis](https://search.google.com/test/rich-results).

## Vérifier en local

```bash
curl -s http://site.local/ | grep -E '<title>|description|og:|ld\+json'
curl -s http://site.local/robots.txt
curl -s http://site.local/llms.txt
curl -s http://site.local/wp-sitemap.xml
```

## Performance

| Quoi                                                        | Pourquoi                                                                                                                     |
| ----------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------- |
| `<link rel="preload">` de `onest.woff2`                     | Le grand titre est l'élément principal du 1er écran (LCP) : sa police arrive plus tôt, sans saut de texte                    |
| Tailles d'images en **WebP** (`image_editor_output_format`) | ≈ 30 % plus légères que le JPEG. Seulement si le serveur sait produire du WebP. Images déjà en ligne : `wp media regenerate` |
| 1re image du Hero en `fetchpriority="high"`                 | Déjà dans les blocs Hero / Hero slider                                                                                       |
| JS par module, chargé seulement si la page en a besoin      | `assets/src/js/main.js`                                                                                                      |

> Changer de police des titres : mettre à jour le chemin dans `starter_preload_fonts()` **et** dans `theme.json` (les deux URL doivent être identiques, sinon la police est téléchargée deux fois).

## Lighthouse

```bash
npx lighthouse http://wordpress-starter.local/ --view
```

Résultat de référence (mobile, en local) : **Performance 100 · Accessibilité 100 · SEO 100**. « Bonnes pratiques » reste à 79 en local uniquement à cause de l'absence de HTTPS : à 100 en production avec HTTPS et redirection.

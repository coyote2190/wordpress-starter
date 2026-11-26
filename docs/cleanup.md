# Nettoyage de WordPress — `inc/cleanup.php`

Par défaut, WordPress injecte dans chaque page des scripts, des styles et des balises dont un site vitrine n'a pas besoin. `inc/cleanup.php` les retire.

## Ce qui est retiré

| Élément                      | Pourquoi WordPress l'ajoute                                   | Pourquoi on l'enlève                                                                  |
| ---------------------------- | ------------------------------------------------------------- | ------------------------------------------------------------------------------------- |
| Script + CSS des emojis      | Remplacer les emojis par des images sur les vieux navigateurs | Les navigateurs actuels gèrent les emojis : ~15 Ko de JS inutile                      |
| `<meta name="generator">`    | Signature « WordPress x.y »                                   | Donne la version exacte aux robots malveillants                                       |
| Lien RSD (`xmlrpc.php?rsd`)  | Anciens logiciels d'édition à distance                        | Plus utilisé                                                                          |
| Lien court (`?p=123`)        | Partage d'URL courtes                                         | Inutile, et dupliqué dans les en-têtes HTTP                                           |
| Liens REST (`/wp-json/`)     | Découverte de l'API                                           | L'API **reste active** (Gutenberg en a besoin), seuls les liens publicitaires partent |
| oEmbed (découverte + script) | Permettre à d'autres sites d'intégrer nos pages               | Inutile pour un site client                                                           |
| Flux RSS extra               | Flux des commentaires / catégories                            | Le flux principal des articles est gardé                                              |
| `classic-theme-styles`       | Couleurs par défaut des boutons Gutenberg                     | On les style nous-mêmes avec Tailwind                                                 |

## Ce qui est volontairement conservé

| Élément                     | Raison                                                                                                                                                  |
| --------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `wp-block-library`          | CSS des blocs Gutenberg (colonnes, images…). Avec `should_load_separate_core_block_assets`, **seul le CSS des blocs présents dans la page** est chargé. |
| `global-styles`             | Variables CSS générées depuis `theme.json` (`--wp--preset--color--…`). On s'en sert pour relier Tailwind et l'éditeur (voir `docs/design-tokens.md`).   |
| `wp-img-auto-sizes-contain` | Petit correctif pour les images en chargement différé (`loading="lazy"`).                                                                               |
| Règles de spéculation       | Le navigateur précharge la page suivante au survol d'un lien : navigation plus rapide.                                                                  |
| Flux RSS principal          | Utile pour les agrégateurs et certains outils SEO.                                                                                                      |

## Vérifier

```bash
curl -s http://wordpress-starter.local/ | grep -iE "emoji|generator|EditURI"
# → aucune ligne = nettoyage OK
```

## Réactiver un élément

Supprimer (ou commenter) la ligne correspondante dans `inc/cleanup.php`. Chaque `remove_action` est précédé d'un commentaire qui dit ce qu'il retire.

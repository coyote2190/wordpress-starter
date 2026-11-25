# Le dossier `inc/`

`inc/` contient la logique PHP du thème, découpée par responsabilité. Chaque fichier correspond à une fonctionnalité.

[functions.php](../functions.php) est le point d'entrée du thème : il ne contient aucune logique et se contente d'inclure les fichiers de `inc/` avec `require_once`.

## Structure

```
wordpress-starter/
├── functions.php
└── inc/
    ├── setup.php
    ├── enqueue.php
    ├── helpers.php
    ├── islands.php
    ├── contact-form.php
    ├── cleanup.php
    ├── custom-post-types.php
    └── acf.php
```

## Rôle de chaque fichier

| Fichier                 | Rôle                                                                                                                                                                                                                              |
| ----------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `setup.php`             | Configuration de base du thème : traductions (`load_theme_textdomain`), supports du thème, menus.                                                                                                                                 |
| `enqueue.php`           | Chargement des assets. Utilise le serveur de dev Vite (HMR) s'il tourne, sinon le build de production. Le mode dev est détecté via le fichier flag `assets/dist/.vite-hot`, créé par le plugin `wpHotFile` de `vite.config.js`.   |
| `helpers.php`           | Fonctions utilitaires réutilisables entre projets. Exemple : `starter_field()`, un wrapper sécurisé autour de `get_field()` qui renvoie `null` si SCF/ACF n'est pas actif.                                                        |
| `islands.php`           | Îlots React : affiche le conteneur d'un composant interactif dans le thème PHP, avec un contenu de repli (`$fallback`) visible tant que React n'est pas monté. Le montage côté JS est fait par `assets/src/js/islands/index.jsx`. |
| `contact-form.php`      | Traitement de la soumission du formulaire de contact, avec vérification du nonce (protection CSRF).                                                                                                                               |
| `cleanup.php`           | Nettoyage et optimisation de WordPress : retire du `<head>` et du front ce qui alourdit un site vitrine sans être utilisé (par exemple la version de WordPress).                                                                  |
| `custom-post-types.php` | Custom Post Types réutilisables entre projets (par exemple le CPT « Service »).                                                                                                                                                   |
| `acf.php`               | Configuration des champs personnalisés SCF/ACF, dont la sauvegarde des groupes de champs en JSON dans `acf-json/`.                                                                                                                |

## Ordre de chargement

Défini dans [functions.php](../functions.php) :

1. `setup.php`
2. `enqueue.php`
3. `helpers.php`
4. `islands.php`
5. `contact-form.php`
6. `cleanup.php`
7. `custom-post-types.php`
8. `acf.php`, chargé **uniquement si SCF ou ACF est présent**

L'ordre compte : `helpers.php` est chargé avant les fichiers qui utilisent ses fonctions.

## Conventions

- **Garde ABSPATH.** Chaque fichier commence par `if (!defined('ABSPATH')) exit;`. Cela empêche de l'exécuter directement par URL, en dehors de WordPress.
- **Préfixe `starter_`.** Les fonctions sont préfixées pour éviter les collisions de noms avec WordPress ou des plugins.
- **SCF et ACF.** SCF (Secure Custom Fields) est un fork d'ACF avec la même API (`get_field`, hooks `acf/*`, format JSON). Le thème fonctionne avec l'un ou l'autre, jamais les deux activés en même temps.

## Ajouter un module

1. Créer `inc/mon-module.php` avec la garde `ABSPATH` en en-tête.
2. L'inclure dans [functions.php](../functions.php) avec `require_once STARTER_THEME_DIR . '/inc/mon-module.php';`, après les fichiers dont il dépend.

## Réutiliser le starter

Pour un nouveau projet, on garde, retire ou adapte un fichier sans toucher aux autres. Par exemple, on supprime `contact-form.php` ou `custom-post-types.php` s'ils ne servent pas, puis on retire la ligne `require_once` correspondante dans `functions.php`.

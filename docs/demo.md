# Contenu de démo — `demo/demo-content.php`

Remplit un site vide avec un faux studio web (« Studio Horizon ») pour voir le thème en situation : toutes les pages, tous les blocs, les menus, le footer.

## Commandes

Depuis la racine WordPress (`app/public/`) :

```bash
# Créer (ou recréer) la démo
wp eval-file wp-content/themes/wordpress-starter/demo/demo-content.php

# Tout supprimer et remettre les réglages d'avant
wp eval-file wp-content/themes/wordpress-starter/demo/demo-content.php reset
```

> **Avec Local**, WP-CLI s'utilise depuis **clic droit sur le site > Open site shell** (la base de données y est déjà configurée).

Relancer la création remplace l'ancienne démo, sans doublons. Les **pages sont mises à jour sur place** (même ID, même URL) : un éditeur ouvert sur une page de démo reste valide. Articles, réalisations, images et menus sont recréés.

## Ce qui est créé

| Quoi | Détail |
|---|---|
| **Pages** | Accueil (Hero slider 3 diapositives, 2 blocs Cartes, encadré, bouton), À propos (Hero, Cartes style « fond clair », encadré), Contact (Hero + formulaire), Actualités (page des articles), Mentions légales, Politique de confidentialité |
| **Articles** | 5 articles en 2 catégories, avec image, extrait, titres, liste et encadré |
| **Réalisations** | 6 réalisations (CPT du mu-plugin) en 3 catégories |
| **Images** | Générées à la volée (dégradés aux couleurs de `theme.json` + titre), ajoutées à la médiathèque |
| **Infos du site** | Téléphone, e-mail (= e-mail d'admin, pour recevoir les tests du formulaire), adresse, Instagram, LinkedIn |
| **Menus** | « Démo – Principal » et « Démo – Pied de page », placés dans les emplacements du thème |
| **Réglages** | Accueil = page statique, page des articles, 6 articles par page, page de confidentialité, slogan |

## Ce que la démo permet de vérifier

- Hiérarchie des gabarits : accueil, page, article, archive de CPT, catégorie, recherche, 404.
- Blocs SCF (Hero, Cartes, Formulaire) et natif (Encadré) côté site **et** dans l'éditeur.
- Blocs Gutenberg du cœur (titres, listes, bouton) avec le style du thème.
- Menu mobile, footer avec coordonnées, pagination.
- Envoi du formulaire de contact (le mail part à l'e-mail d'admin → visible dans Mailpit en local).

## Comment ça marche

- Chaque contenu et image créé porte une méta `_starter_demo`.
- Les termes, menus et **valeurs d'origine des réglages** sont mémorisés dans l'option `starter_demo_state`.
- `reset` supprime ce qui est marqué, puis restaure les réglages depuis `starter_demo_state`.
- Le contenu est écrit en **balisage de blocs** (`<!-- wp:paragraph -->…`), exactement comme l'enregistre l'éditeur. Les fonctions `starter_demo_p()`, `starter_demo_h()`, `starter_demo_hero()`, `starter_demo_features()`… servent d'exemples pour générer du contenu de blocs en PHP.
- `wp_insert_post()` attend des données **slashées** (il retire les antislashs) : le script passe tout par `wp_slash()`, sinon le JSON des blocs (`\u003cstrong\u003e`) est cassé. Piège à retenir pour tout import de contenu en PHP.
- Dans un bloc SCF, un **répéteur** est stocké « à plat » : `items` = nombre de lignes, `items_0_title` = valeur, `_items_0_title` = clé du champ (voir `starter_demo_features()`).
- Le fichier refuse de s'exécuter hors WP-CLI : il n'est jamais accessible depuis le navigateur.

## Adapter pour un client

Pour une présentation client, modifier les textes de `starter_demo_create()` (nom du studio → nom du client, services…). On peut aussi garder la démo comme base de travail : supprimer la méta `_starter_demo` d'une page pour que `reset` ne la touche plus.

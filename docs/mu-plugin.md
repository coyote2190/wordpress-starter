# Le mu-plugin « Starter Core » — `mu-plugin/`

## Pourquoi un mu-plugin ?

**Règle : le thème affiche, le plugin stocke.**

Si les types de contenu (« Réalisations »…) étaient déclarés dans le thème et que le client changeait de thème, ses réalisations disparaîtraient de l'admin. Elles seraient toujours en base, mais invisibles. Tout ce qui **crée ou traite des données** va donc dans un plugin.

Pourquoi un **mu**-plugin (« must-use ») plutôt qu'un plugin classique :

| | Plugin classique | MU-plugin |
|---|---|---|
| Activation | Manuelle, peut être désactivé par erreur | **Toujours actif**, pas de bouton « Désactiver » |
| Chargement | Après les mu-plugins | Avant tout le reste |
| Emplacement | `wp-content/plugins/nom/` | `wp-content/mu-plugins/` (uniquement les `.php` à la racine) |
| Visible | Extensions | Extensions > « Indispensables » |

## Les fichiers

```
mu-plugin/
├── starter-core.php            Chargeur (en-tête de plugin + liste des fichiers)
└── starter-core/
    ├── post-types.php          CPT « Réalisations » + taxonomie « Catégories de réalisation »
    ├── comments.php            Désactive les commentaires partout (front + admin)
    └── …                       (le formulaire de contact arrive ici : docs/contact-form.md)
```

WordPress ne charge que les fichiers `.php` **à la racine** de `mu-plugins/`. D'où le chargeur `starter-core.php`, qui inclut les fichiers du sous-dossier.

## Installation

### En local (déjà fait sur ce site)

Des liens symboliques pointent vers le dépôt, donc toute modification est prise en compte immédiatement :

```bash
cd wp-content/mu-plugins
ln -s ../themes/wordpress-starter/mu-plugin/starter-core.php starter-core.php
ln -s ../themes/wordpress-starter/mu-plugin/starter-core starter-core
```

### En production

**Copier** (pas de lien) le fichier et le dossier :

```
wp-content/mu-plugins/
├── starter-core.php
└── starter-core/
```

> ⚠️ À chaque déploiement qui touche `mu-plugin/`, penser à recopier ces fichiers. Voir `docs/new-project.md`.

## Le CPT « Réalisations » (exemple)

| Élément | Valeur |
|---|---|
| Identifiant | `reference` |
| Liste | `/realisations/` → `archive.php` (ou `archive-reference.php` si créé) |
| Fiche | `/realisations/mon-projet/` → `single.php` (ou `single-reference.php`) |
| Taxonomie | `reference_category` → `/realisations/categorie/web/` |
| Éditeur | Gutenberg (`show_in_rest: true`) |

### Ajouter un type de contenu

1. Copier le bloc `register_post_type( 'reference', … )` dans `post-types.php` et l'adapter.
2. **Régénérer les permaliens** : Réglages > Permaliens > Enregistrer (sinon les URL tombent en 404).
3. Pour un affichage spécifique, créer `single-{type}.php` / `archive-{type}.php` dans le thème.

Identifiant : 20 caractères max, minuscules, sans tiret de préférence, au **singulier** (`reference`, `service`, `team_member`).

### Le client n'a pas besoin de réalisations ?

Supprimer `register_post_type` / `register_taxonomy` dans `post-types.php`, puis régénérer les permaliens.

## Les commentaires

`comments.php` les ferme partout, masque les commentaires existants, retire le menu, la page de réglages « Discussion », le widget du tableau de bord et l'icône de la barre d'admin.

Pour un client qui veut un blog avec commentaires : retirer `'comments'` de la liste dans `starter-core.php`, puis ajouter `comments_template()` dans `single.php`.

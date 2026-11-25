# Champs personnalisés — Secure Custom Fields (SCF)

SCF est la version libre et maintenue par WordPress.org d'**ACF** (Advanced Custom Fields). Elle inclut les fonctions qui étaient payantes dans ACF : répéteurs, contenu flexible, pages d'options, blocs. L'API est **identique à ACF** : `get_field()`, hooks `acf/…`. Les tutos ACF s'appliquent donc tels quels.

## Ce que fait `inc/scf.php`

| Élément | Rôle |
|---|---|
| `starter_scf_missing_notice()` | Message rouge dans l'admin si SCF n'est pas actif |
| Filtres `acf/settings/save_json` / `load_json` | Les groupes de champs sont enregistrés en JSON dans `scf-json/` |
| `starter_scf_options_pages()` | Crée le menu **Infos du site** dans l'admin |
| `starter_field()` | `get_field()` qui ne plante pas si SCF est désactivé, avec valeur par défaut |
| `starter_option()` | Pareil, pour un champ de la page « Infos du site » |

## Local JSON : les champs versionnés avec Git

```
Admin > SCF > Groupes de champs > Enregistrer
            │
            ▼
scf-json/group_xxx.json  ← SCF écrit le fichier automatiquement
            │
            ▼  git commit
Autre site / prod : SCF lit le JSON → les champs existent sans rien recréer à la main
```

- **Toujours créer/modifier les champs en local**, puis commiter le JSON.
- Si le JSON est plus récent que la version en base, SCF propose une **synchronisation** (SCF > Groupes de champs > onglet « Synchronisation disponible »). Pas obligatoire : les champs fonctionnent directement depuis le JSON.
- Les **blocs**, **types de contenu** et **pages d'options** créés dans l'interface SCF sont aussi sauvegardés dans ce dossier.

## Lire un champ

```php
// Contenu courant
$sous_titre = starter_field( 'subtitle' );
$sous_titre = starter_field( 'subtitle', false, 'Valeur par défaut' );

// Un autre contenu
$prix = starter_field( 'price', 42 );

// Page « Infos du site »
$tel = starter_option( 'phone' );
```

Puis **toujours échapper à l'affichage** : `esc_html( $sous_titre )`, `esc_url( $lien )`… (voir `docs/templates.md`).

### Formats de retour utiles

| Type de champ | Régler « Format de retour » sur | On obtient |
|---|---|---|
| Image | **ID** | Un entier → `wp_get_attachment_image( $id, 'large' )` génère `srcset`, `alt`, lazy-loading |
| Lien | **Tableau** | `[ 'url', 'title', 'target' ]` |
| Sélection | **Valeur et libellé** | `[ 'value', 'label' ]` |
| Relation / Objet | **ID** | Des ID → `get_the_title( $id )`… |

## La page « Infos du site »

Menu **Infos du site** dans l'admin (accessible aux éditeurs). Champs définis dans `scf-json/group_starter_site_settings.json` :

| Champ | Nom | Utilisé dans |
|---|---|---|
| Téléphone | `phone` | Footer (lien `tel:`) |
| E-mail | `email` | Footer (encodé contre les robots) + destinataire du formulaire de contact |
| Adresse | `address` | Footer |
| Réseaux sociaux | `socials` (répéteur : `network`, `url`) | Footer. Affiche `assets/icons/{réseau}.svg` s'il existe, sinon le nom en texte. |

Affichage : `template-parts/site-contact.php`.

## Ajouter un groupe de champs (exemple : sous-titre sur les pages)

1. Admin > SCF > Groupes de champs > Ajouter.
2. Champ « Sous-titre », nom `subtitle`. Emplacement : « Type de publication = Page ».
3. Enregistrer → `scf-json/group_….json` apparaît.
4. Dans `page.php` :
   ```php
   <?php if ( $sous_titre = starter_field( 'subtitle' ) ) : ?>
       <p class="text-xl text-gray-600"><?php echo esc_html( $sous_titre ); ?></p>
   <?php endif; ?>
   ```
5. Commiter le PHP **et** le JSON.

## Champs « icon » et « color » : choix automatiques

`inc/scf.php` remplit tout seul les choix de deux types de champs, d'après leur **nom** :

| Champ Sélection nommé | Choix proposés | Source |
|---|---|---|
| `icon` | Toutes les icônes | Fichiers de `assets/icons/` (`starter_icon_list()`) |
| `color` | Toutes les couleurs de la charte | Palette de `theme.json` (`starter_palette()`) |

Dans l'admin SCF, il suffit de créer un champ **Sélection** avec ce nom, sans saisir de choix. Une icône ou une couleur ajoutée devient disponible dans tous les blocs.

## Nommage

| Élément | Convention | Exemple |
|---|---|---|
| Nom de champ | anglais, snake_case | `subtitle`, `cta_link` |
| Clé de groupe | `group_starter_{sujet}` | `group_starter_site_settings` |
| Clé de champ | `field_starter_{nom}` (ou celle générée par SCF) | `field_starter_phone` |

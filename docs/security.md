# Sécurité — `mu-plugin/starter-core/security.php`

Placé dans le mu-plugin (et pas dans le thème) : la protection reste active même si le thème change.

## Ce que fait le code

| Mesure                                          | Ce qui est bloqué                                                                           | Vérifier                                                             |
| ----------------------------------------------- | ------------------------------------------------------------------------------------------- | -------------------------------------------------------------------- |
| XML-RPC désactivé                               | Force brute via `xmlrpc.php` (des centaines de mots de passe testés par requête), pingbacks | `xmlrpc.php` répond « services XML-RPC désactivés »                  |
| Pages auteur bloquées pour les visiteurs        | `/?author=1` → `/author/admin/` révèle l'**identifiant de connexion**                       | `curl -I "http://site/?author=1"` → 301 vers l'accueil               |
| Route REST `/wp/v2/users` masquée aux visiteurs | Liste des identifiants en JSON                                                              | `/wp-json/wp/v2/users` → 404 (Gutenberg connecté y a toujours accès) |
| Sitemap des auteurs retiré                      | `wp-sitemap-users-1.xml`                                                                    | → 404                                                                |
| Message de connexion générique                  | « L'identifiant X n'existe pas » confirme les identifiants valides                          |                                                                      |
| En-têtes HTTP                                   | Voir ci-dessous                                                                             | `curl -I http://site/`                                               |

### En-têtes HTTP ajoutés

| En-tête                                            | Effet                                                                           |
| -------------------------------------------------- | ------------------------------------------------------------------------------- |
| `X-Content-Type-Options: nosniff`                  | Le navigateur respecte le type annoncé d'un fichier                             |
| `X-Frame-Options: SAMEORIGIN`                      | Le site ne peut pas être affiché dans une iframe d'un autre site (clickjacking) |
| `Referrer-Policy: strict-origin-when-cross-origin` | Les sites externes ne reçoivent que le domaine, pas l'URL complète              |
| `Permissions-Policy`                               | Caméra, micro et géolocalisation désactivés                                     |

> Ces en-têtes ne s'appliquent qu'aux pages générées par WordPress. Pour les images et fichiers statiques, les ajouter dans `.htaccess` (voir plus bas).

## À mettre dans `wp-config.php` en production

```php
// Pas d'éditeur de fichiers dans l'admin (Apparence > Éditeur de fichiers).
// Si un compte admin est piraté, l'attaquant ne peut pas injecter de PHP.
define( 'DISALLOW_FILE_EDIT', true );

// Environnement : désactive le mode dev de Vite (fichier hot) et sert à adapter le comportement.
define( 'WP_ENVIRONMENT_TYPE', 'production' );

// Pas d'erreurs PHP affichées aux visiteurs.
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_DISPLAY', false );

// Forcer HTTPS dans l'admin.
define( 'FORCE_SSL_ADMIN', true );
```

Et **régénérer les clés de sécurité** (`AUTH_KEY`…) : https://api.wordpress.org/secret-key/1.1/salt/

## `.htaccess` (Apache, hébergement mutualisé)

À ajouter **au-dessus** du bloc `# BEGIN WordPress` :

```apache
# Interdit l'accès direct aux fichiers sensibles
<FilesMatch "^(wp-config\.php|readme\.html|license\.txt|xmlrpc\.php)$">
  Require all denied
</FilesMatch>

# Pas de liste des fichiers d'un dossier
Options -Indexes

# Pas d'exécution de PHP dans les uploads
<IfModule mod_rewrite.c>
  RewriteRule ^wp-content/uploads/.*\.php$ - [F,L]
</IfModule>

# Forcer HTTPS
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteCond %{HTTPS} off
  RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

## Bonnes pratiques (hors code)

- Pas d'utilisateur nommé `admin`. Le client a un compte **Éditeur**, pas Administrateur, sauf s'il en a vraiment besoin.
- Mots de passe forts + authentification à deux facteurs (extension _Two Factor_).
- Mises à jour WordPress et extensions activées (mises à jour automatiques pour les versions mineures).
- Sauvegardes automatiques (souvent fournies par l'hébergeur) **testées** au moins une fois.
- Le moins d'extensions possible : chaque extension est une porte d'entrée de plus.

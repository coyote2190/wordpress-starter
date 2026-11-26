# Nouveau projet client — checklist

## 1. Récupérer le starter

```bash
# Dans wp-content/themes/ du nouveau site Local
git clone git@github.com:coyote2190/wordpress-starter.git nom-du-client
cd nom-du-client
rm -rf .git && git init          # nouvel historique pour le projet client
npm install
npm run build
```

> On garde les préfixes `starter_` / `STARTER_` et le text domain `starter` : invisibles pour les visiteurs, et ça permet de reporter facilement les améliorations du starter.

## 2. Identité du thème

- [ ] `style.css` : `Theme Name`, `Description`, `Version: 1.0.0`
- [ ] `screenshot.png` (1200×900) : capture de la maquette
- [ ] `theme.json` : couleurs de la charte, polices (voir `docs/design-tokens.md`)
- [ ] `README.md` : remplacer l'intro par le nom du client

## 3. WordPress

- [ ] Réglages > Général : langue **Français**, fuseau **Paris**, format de date
- [ ] Réglages > Lecture : **page statique** pour l'accueil ; **« Demander aux moteurs de recherche de ne pas indexer »** coché pendant le dev
- [ ] Réglages > Permaliens : **Titre de la publication**, enregistrer
- [ ] Supprimer le contenu de démo (article « Bonjour tout le monde », « Page d'exemple »)
- [ ] Supprimer les thèmes par défaut inutiles (en garder **un** comme secours)
- [ ] Extensions : **Secure Custom Fields**, **FluentSMTP**. Supprimer Hello Dolly / Akismet.
- [ ] Créer la page **Politique de confidentialité** (Réglages > Confidentialité) : elle est liée au formulaire de contact

## 4. MU-plugin

```bash
cd ../../mu-plugins   # wp-content/mu-plugins/
ln -s ../themes/nom-du-client/mu-plugin/starter-core.php starter-core.php
ln -s ../themes/nom-du-client/mu-plugin/starter-core starter-core
```
- [ ] Extensions > Indispensables : « Starter Core » apparaît
- [ ] Adapter / supprimer le CPT « Réalisations » (`mu-plugin/starter-core/post-types.php`), puis réenregistrer les permaliens

## 5. Contenus de base

> Pour voir le thème rempli tout de suite : `wp eval-file …/demo/demo-content.php` (voir `docs/demo.md`), puis `… reset` avant de saisir le vrai contenu.


- [ ] **Infos du site** : téléphone, e-mail (reçoit les messages du formulaire), adresse, réseaux
- [ ] Apparence > Menus : menu **principal** et **pied de page**
- [ ] Apparence > Personnaliser > Identité : **logo** et **icône du site** (favicon)
- [ ] Page Contact avec le bloc **Formulaire de contact**
- [ ] **Extrait** de chaque page importante (= meta description et aperçu de partage, voir `docs/seo.md`)
- [ ] Réglages > Général : **slogan** (description par défaut)

## 6. Avant la mise en ligne

- [ ] `npm run build` puis commit (dist/ et blocks-native/build/ à jour)
- [ ] FluentSMTP configuré sur le serveur + **e-mail de test** + test du formulaire (voir `docs/contact-form.md`)
- [ ] `wp-config.php` de prod : `DISALLOW_FILE_EDIT`, `WP_ENVIRONMENT_TYPE = 'production'`, `WP_DEBUG = false` (voir `docs/security.md`)
- [ ] `.htaccess` : règles de `docs/security.md`
- [ ] HTTPS actif, redirection http → https
- [ ] Réglages > Lecture : **décocher** « ne pas indexer »
- [ ] Page contact **exclue du cache** s'il y a une extension de cache
- [ ] Compte du client en rôle **Éditeur**
- [ ] Lighthouse sur l'accueil, une page, un article et la page contact (voir `docs/seo.md`)
- [ ] Envoi d'un message de test : il apparaît dans **Messages** avec ✅

## Déployer (hébergement mutualisé, FTP/SFTP)

Envoyer dans `wp-content/themes/nom-du-client/` **tout le thème sauf** :

| Ne pas envoyer | Pourquoi |
|---|---|
| `node_modules/` | Outils de dev, très lourd |
| `.git/` | Historique Git |
| `hot` | Fichier du serveur de dev (ignoré en prod de toute façon) |
| `demo/` | Script de démo (inoffensif, mais inutile en prod) |

Et **copier** (pas de lien symbolique) :

```
mu-plugin/starter-core.php  →  wp-content/mu-plugins/starter-core.php
mu-plugin/starter-core/     →  wp-content/mu-plugins/starter-core/
```

Pas besoin de Node sur le serveur : `dist/` et `blocks-native/build/` sont déjà compilés et commités.

### Mise à jour d'un site en ligne

1. En local : modifier, `npm run build`, tester, commit.
2. Envoyer les fichiers modifiés (avec les nouveaux fichiers de `dist/assets/`).
3. Si `mu-plugin/` a changé : le recopier dans `wp-content/mu-plugins/`.
4. Si des groupes de champs ont changé : SCF > Groupes de champs > **Synchroniser** (facultatif, le JSON suffit).
5. Si un CPT ou un slug a changé : Réglages > Permaliens > Enregistrer.

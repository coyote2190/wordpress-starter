# Formulaire de contact (fait main)

## Vue d'ensemble

```
THÈME (affichage)                                  MU-PLUGIN (traitement)
─────────────────                                  ──────────────────────
blocks/contact-form/      bloc à placer dans
                          n'importe quelle page
        │
        ▼
template-parts/contact-form.php  ── POST ──►  wp-admin/admin-post.php?action=starter_contact
        │                                               │
        │                                     mu-plugin/starter-core/contact-form.php
assets/src/js/modules/contact-form.js            1. nonce          4. limite par IP
(envoi en arrière-plan, facultatif)              2. pot de miel    5. validation
                                                 3. délai ≥ 3 s    6. wp_mail()
                                                                          │
                                                                    FluentSMTP → SMTP → boîte du client
```

## Les fichiers

| Fichier | Rôle |
|---|---|
| `blocks/contact-form/` | Bloc « Formulaire de contact » (sans champs) : le client le place où il veut. Une seule fois par page (`"multiple": false`). |
| `template-parts/contact-form.php` | HTML du formulaire + réaffichage des erreurs (sans JS) |
| `assets/src/js/modules/contact-form.js` | Envoi en `fetch`, message immédiat, erreurs sous les champs |
| `mu-plugin/starter-core/contact-form.php` | Anti-spam, validation, envoi, réponse JSON ou redirection |

## Deux modes, un seul traitement

| | Avec JS | Sans JS |
|---|---|---|
| Envoi | `fetch` avec `Accept: application/json` | POST classique |
| Réponse | JSON `{ success, message, errors }` (code 200 ou 422) | Redirection vers la page + `?contact=sent` ou `?contact=error&contact_key=…` |
| Erreurs | Affichées par le JS sous chaque champ | Gardées 10 min dans un *transient*, réaffichées une fois puis supprimées |

Le formulaire **marche toujours**, même si le JS plante : c'est l'amélioration progressive.

## Anti-spam (sans captcha)

| Protection | Principe | Si déclenchée |
|---|---|---|
| Nonce | Jeton WordPress qui prouve que le formulaire vient du site | « Session expirée » |
| Pot de miel | Champ `website` invisible : un humain le laisse vide, un robot le remplit | **Faux succès** (le robot ne sait pas qu'il est bloqué) |
| Délai | Heure d'affichage signée (`_started`). Envoi en moins de 3 s = robot | Faux succès |
| Limite par IP | 5 envois par heure | Message d'erreur |

Si le spam passe malgré tout : ajouter Cloudflare Turnstile (gratuit, sans puzzle) dans `starter_contact_process()`.

> ⚠️ **Cache de page** (WP Rocket, cache de l'hébergeur…) : le nonce et le jeton `_started` sont figés dans le HTML mis en cache. Le nonce expire après 24 h maximum. **Exclure la page contact du cache.**

## E-mail pré-rempli

`/contact/?email=jean@exemple.fr` pré-remplit le champ e-mail (utilisé par le bloc **Appel à l'action** en mode « champ e-mail »). La valeur passe par `sanitize_email()`.

## Validation

| Champ | Règle |
|---|---|
| `name` | obligatoire, 100 caractères max |
| `email` | adresse valide (`is_email`) |
| `phone` | facultatif ; chiffres, espaces, `+ ( ) . -`, 6 à 20 caractères |
| `message` | 10 à 5 000 caractères |
| `consent` | case cochée (RGPD) |

Le HTML a aussi `required`, `maxlength`… : le navigateur bloque les erreurs évidentes avant l'envoi. **Le serveur revérifie toujours** (le HTML se contourne facilement).

## Le mail envoyé

- **Destinataire** : champ « E-mail » de **Infos du site**, sinon l'e-mail d'administration (Réglages > Général).
- **Sujet** : `[Nom du site] Nouveau message de Jean`.
- **Reply-To** : l'adresse du visiteur. Le client clique « Répondre » et répond directement au visiteur.
- **Expéditeur (From)** : défini par FluentSMTP (adresse du domaine du site). **Jamais l'adresse du visiteur**, sinon le mail part en spam (SPF/DKIM).

## Personnaliser

```php
// Destinataire différent (dans le mu-plugin)
add_filter( 'starter_contact_recipient', fn() => 'devis@client.fr' );

// Action après envoi (enregistrer en base, CRM…)
add_action( 'starter_contact_sent', function ( array $values ) {
    // $values['name'], ['email'], ['phone'], ['message']
} );
```

**Ajouter un champ** (ex. « Sujet ») :
1. `template-parts/contact-form.php` : copier un bloc `<div>` de champ, changer le nom.
2. `contact-form.php` (mu-plugin) : l'ajouter à `$values`, à la validation et au `$body` du mail.

---

# Envoi des mails — FluentSMTP

Par défaut, WordPress envoie avec la fonction `mail()` de PHP. Sur un mutualisé, ces mails arrivent souvent **en spam ou pas du tout**. FluentSMTP fait passer **tous** les `wp_mail()` (formulaire, mot de passe oublié, notifications…) par un vrai serveur SMTP authentifié. Aucun code dans le thème.

## Configuration (une fois par site)

1. Extensions > FluentSMTP > Settings > **Add connection**.
2. Choisir le fournisseur : SMTP de l'hébergeur (OVH, o2switch…), Brevo, Mailgun, Amazon SES…
3. **From Email** : une adresse **du domaine du site** (`contact@client.fr`), cocher « Force From Email ».
4. Pour le SMTP : **Store credentials in wp-config.php**, puis dans `wp-config.php` (jamais dans Git) :
   ```php
   define( 'FLUENTMAIL_SMTP_USERNAME', 'contact@client.fr' );
   define( 'FLUENTMAIL_SMTP_PASSWORD', '••••••••' );
   ```
   Pour un fournisseur avec clé d'API : `FLUENTMAIL_BREVO_API_KEY`… (le nom exact est affiché par FluentSMTP).
5. **Email Test** dans FluentSMTP, puis tester le formulaire du site.
6. Vérifier le DNS du domaine : enregistrements **SPF** et **DKIM** fournis par l'hébergeur ou le fournisseur. Sans eux : spam.

## En local

- Local (by Flywheel) intercepte les mails dans **Mailpit** (Local > onglet Tools > Mailpit) : rien ne part vraiment.
- Ou, dans `wp-config.php` en local : `define( 'FLUENTMAIL_SIMULATE_EMAILS', true );`. Les mails sont journalisés dans FluentSMTP mais pas envoyés.

## Journal

FluentSMTP > **Email Logs** : chaque mail envoyé ou en échec, avec possibilité de le renvoyer. Premier endroit à regarder quand un client dit « je ne reçois pas les messages ».

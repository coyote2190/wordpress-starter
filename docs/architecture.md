# Architecture

## Les grands principes

1. **Un fichier = un sujet.** `functions.php` ne fait que charger des fichiers de `inc/`. Quand on cherche « où sont déclarés les menus ? », la réponse est dans le nom du fichier.
2. **Le thème affiche, il ne stocke pas.** Tout ce qui crée des données (types de contenu, taxonomies, traitement de formulaire) vit dans le **mu-plugin** (`mu-plugin/`). Si le client change de thème, ses contenus restent.
3. **Pas de dépendance cachée.** Pas de jQuery, pas de librairie JS côté front : du JS natif découpé en modules.
4. **WordPress allégé.** On retire tout ce que WordPress charge par défaut et dont on ne se sert pas (emojis, styles inutiles…).

## Comment WordPress charge le thème

```
Requête d'une page
  │
  ├─ 1. wp-content/mu-plugins/*.php   (toujours chargés, impossibles à désactiver)
  ├─ 2. wp-content/plugins/…          (SCF, FluentSMTP)
  ├─ 3. functions.php du thème        → charge inc/*.php
  │      └─ les fichiers inc/ s'accrochent à des « hooks » (add_action / add_filter)
  └─ 4. Gabarit choisi selon la hiérarchie (front-page.php, single.php…)
```

## Les hooks en deux phrases

- `add_action( 'nom', 'ma_fonction' )` : « quand WordPress arrive à l'étape *nom*, exécute ma fonction ».
- `add_filter( 'nom', 'ma_fonction' )` : « quand WordPress calcule la valeur *nom*, laisse ma fonction la modifier et la renvoyer ».

Exemple dans `inc/setup.php` : `after_setup_theme` est l'étape où le thème déclare ce qu'il supporte.

## Conventions

| Quoi | Convention | Exemple |
|---|---|---|
| Fonctions PHP | préfixe `starter_` | `starter_setup()` |
| Constantes | préfixe `STARTER_` | `STARTER_DIR` |
| Text domain (traductions) | `starter` | `__( 'Menu', 'starter' )` |
| Indentation | tabulations en PHP, 2 espaces en JS/CSS/JSON | voir `.editorconfig` |

Pour un nouveau client, ces préfixes peuvent rester tels quels : ils ne sont pas visibles par les visiteurs.

## Constantes disponibles

| Constante | Valeur |
|---|---|
| `STARTER_VERSION` | Version lue dans `style.css` |
| `STARTER_DIR` | Chemin disque du thème (pour `require`, lire un fichier) |
| `STARTER_URI` | URL du thème (pour pointer vers une image, un script) |

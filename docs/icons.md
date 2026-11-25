# Icônes SVG — `inc/icons.php` + `assets/icons/`

## Pourquoi des SVG insérés dans le HTML ?

| Approche | Problème |
|---|---|
| Police d'icônes (Font Awesome…) | Charge des centaines d'icônes pour en utiliser 10, pas nette, accessibilité moyenne |
| `<img src="icon.svg">` | Impossible de changer la couleur en CSS |
| **SVG dans le HTML** ✅ | Seules les icônes utilisées sont envoyées, la couleur suit le texte, taille avec Tailwind |

## Utilisation

```php
<?php echo starter_icon( 'arrow-right' ); ?>                          // taille par défaut : size-5
<?php echo starter_icon( 'phone', 'size-4 text-primary' ); ?>         // classes Tailwind
<?php echo starter_icon( 'search', 'size-6', 'Rechercher' ); ?>        // icône seule dans un bouton : donner un label
```

| Paramètre | Rôle |
|---|---|
| `$name` | Nom du fichier dans `assets/icons/`, sans `.svg` |
| `$class` | Classes ajoutées sur `<svg>` (par défaut `size-5`). `icon shrink-0` est toujours ajouté. |
| `$label` | Vide → icône **décorative**, cachée aux lecteurs d'écran (`aria-hidden`). Rempli → annoncée (`role="img"` + `aria-label`). |

**Règle d'accessibilité** : si l'icône est à côté d'un texte (« 📞 01 23 45 67 89 »), pas de label. Si elle est **seule** dans un bouton ou un lien (bouton menu, loupe), soit un label, soit un texte caché `<span class="sr-only">`.

## Icônes fournies

`menu` · `close` · `arrow-right` · `chevron-down` · `search` · `mail` · `phone` · `map-pin` · `check` · `alert`

## Ajouter une icône

1. Récupérer le SVG (Lucide, Heroicons, Tabler… sont libres de droits) ou l'exporter depuis Figma.
2. Le nettoyer pour qu'il ressemble à ceci :
   ```svg
   <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" …>…</svg>
   ```
   - **garder** `viewBox` ;
   - **retirer** `width`, `height`, `class`, les commentaires et les `id` ;
   - les couleurs en dur → `currentColor` (la couleur suivra `text-…`). Pour une icône pleine : `fill="currentColor"`.
3. L'enregistrer dans `assets/icons/mon-icone.svg` (minuscules et tirets uniquement).

Pas besoin de rebuild : PHP lit le fichier directement.

## Sécurité

Le nom est vérifié (`a-z`, `0-9`, `-` uniquement), donc on ne peut pas lire un autre fichier du serveur. Comme le SVG est inséré tel quel, **ne mettre dans `assets/icons/` que des fichiers dont on connaît la source** (pas d'upload client).

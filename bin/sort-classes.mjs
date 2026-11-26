/**
 * Trie les classes Tailwind dans les fichiers PHP, dans l'ordre officiel de Tailwind.
 *
 * Pourquoi ce script : Prettier (et son plugin Tailwind) trie les classes du JS,
 * mais ne sait pas lire le PHP. On réutilise ici le MÊME algorithme de tri
 * (`prettier-plugin-tailwindcss/sorter`), sans toucher au reste du code PHP.
 *
 * Ce qui est trié :
 * - les attributs HTML `class="…"` (les morceaux `<?php … ?>` restent en place,
 *   seuls les morceaux de texte autour sont triés) ;
 * - les tableaux PHP `'class' => '…'` (wp_get_attachment_image, get_block_wrapper_attributes…) ;
 * - le 2e argument de starter_icon( 'nom', '…' ).
 *
 * Ce qui n'est PAS trié : les classes construites par concaténation ou rangées
 * dans des tableaux PHP (`$widths = array( … )`). Rien de grave : l'ordre des
 * classes ne change jamais le rendu, c'est seulement une question de lisibilité.
 *
 * Usage :
 *   node bin/sort-classes.mjs          → trie et écrit les fichiers modifiés
 *   node bin/sort-classes.mjs --check  → liste les fichiers non triés, code de sortie 1 (CI)
 *
 * Lancé par `npm run format` (écriture) et `npm run format:check` (vérification).
 */
import fs from 'node:fs';
import path from 'node:path';
import { createSorter } from 'prettier-plugin-tailwindcss/sorter';

const root = path.resolve(import.meta.dirname, '..');
const check = process.argv.includes('--check');

// Dossiers à parcourir (mêmes que assets/src/css/sources.css, côté PHP).
const targets = ['.', 'inc', 'template-parts', 'blocks', 'blocks-native/src'];

/** Fichiers .php des dossiers cibles (la racine sans sous-dossiers). */
function phpFiles() {
  const files = [];
  for (const target of targets) {
    const dir = path.join(root, target);
    const walk = (current, recursive) => {
      for (const entry of fs.readdirSync(current, { withFileTypes: true })) {
        const full = path.join(current, entry.name);
        if (entry.isDirectory() && recursive) walk(full, true);
        else if (entry.isFile() && entry.name.endsWith('.php')) files.push(full);
      }
    };
    walk(dir, target !== '.');
  }
  return files;
}

const sorter = await createSorter({ base: root, stylesheetPath: 'assets/src/css/main.css' });

/** Trie une liste de classes en gardant les espaces au début et à la fin. */
function sortList(list) {
  if (!list.trim()) return list;
  const [, before, classes, after] = list.match(/^(\s*)([\s\S]*?)(\s*)$/);
  return before + sorter.sortClassAttributes([classes])[0] + after;
}

/** Trie la valeur d'un attribut class="…" : seulement les morceaux hors <?php … ?>. */
function sortAttribute(value) {
  const parts = value.split(/(<\?php[\s\S]*?\?>)/);
  // PHP mal fermé dans la valeur : on ne touche à rien.
  if (parts.some((part) => !part.startsWith('<?php') && part.includes('<?php'))) return value;
  return parts.map((part) => (part.startsWith('<?php') ? part : sortList(part))).join('');
}

const rules = [
  // class="…"  (attribut HTML, y compris dans une chaîne PHP entre apostrophes)
  { regex: /(\bclass=")([^"]*)(")/g, sort: sortAttribute },
  // 'class' => '…'  (tableaux PHP)
  { regex: /('class'\s*=>\s*')([^'\\]*)(')/g, sort: sortList },
  // starter_icon( 'nom', '…'
  { regex: /(starter_icon\(\s*[^,()]+,\s*')([^'\\]*)(')/g, sort: sortList },
];

const changed = [];
for (const file of phpFiles()) {
  const source = fs.readFileSync(file, 'utf8');
  let output = source;
  for (const { regex, sort } of rules) {
    output = output.replace(regex, (_, start, classes, end) => start + sort(classes) + end);
  }
  if (output !== source) {
    changed.push(path.relative(root, file));
    if (!check) fs.writeFileSync(file, output);
  }
}

if (check && changed.length) {
  console.log(
    `Classes Tailwind non triées (lancer « npm run format ») :\n  ${changed.join('\n  ')}`,
  );
  process.exit(1);
}
console.log(
  changed.length
    ? `Classes triées : ${changed.length} fichier(s)\n  ${changed.join('\n  ')}`
    : 'Classes Tailwind : tout est trié.',
);

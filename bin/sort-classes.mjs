/**
 * Trie les classes Tailwind par ordre ALPHABÉTIQUE, dans le PHP et le JS.
 *
 * Choix du projet : ordre alphabétique (et non l'ordre « officiel » de Tailwind),
 * plus simple à lire et à retrouver. L'ordre des classes ne change JAMAIS le
 * rendu : c'est Tailwind qui décide de l'ordre des règles dans le CSS.
 *
 * Règle : comparaison caractère par caractère, en ignorant un `-` ou `!` initial
 * (`-translate-x-1` se range avec `translate-…`). Doublons conservés.
 *
 * Ce qui est trié :
 * - PHP : attributs `class="…"` (les morceaux `<?php … ?>` restent en place,
 *   seuls les morceaux de texte autour sont triés), tableaux `'class' => '…'`,
 *   2e argument de starter_icon( 'nom', '…' ) ;
 * - JS : `className="…"`, `className: '…'`, parties fixes de className={`…`},
 *   constantes de classes en MAJUSCULES (`export const INNER = '…'`).
 *
 * Ce qui n'est PAS trié : les classes construites par concaténation ou rangées
 * dans des tableaux PHP (`$widths = array( … )`).
 *
 * Usage :
 *   node bin/sort-classes.mjs          → trie et écrit les fichiers modifiés
 *   node bin/sort-classes.mjs --check  → liste les fichiers non triés, code de sortie 1 (CI)
 *
 * Lancé par `npm run format` (écriture) et `npm run format:check` (vérification).
 */
import fs from 'node:fs';
import path from 'node:path';

const root = path.resolve(import.meta.dirname, '..');
const check = process.argv.includes('--check');

// Dossiers à parcourir. `false` = sans les sous-dossiers.
const targets = [
  ['.', false],
  ['inc', true],
  ['template-parts', true],
  ['blocks', true],
  ['blocks-native/src', true],
  ['assets/src/js', true],
];

/** Fichiers .php et .js des dossiers cibles. */
function sourceFiles() {
  const files = [];
  const walk = (dir, recursive) => {
    for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
      const full = path.join(dir, entry.name);
      if (entry.isDirectory() && recursive) walk(full, true);
      else if (entry.isFile() && /\.(php|js)$/.test(entry.name)) files.push(full);
    }
  };
  for (const [target, recursive] of targets) walk(path.join(root, target), recursive);
  return files;
}

/** Clé de tri : sans le `-` (valeur négative) ni le `!` (important) du début. */
const key = (cls) => cls.replace(/^[-!]+/, '');
const compare = (a, b) => {
  const [ka, kb] = [key(a), key(b)];
  if (ka !== kb) return ka < kb ? -1 : 1;
  return a < b ? -1 : a > b ? 1 : 0;
};

/**
 * Est-ce bien une classe Tailwind ? Une lettre au moins, et hors crochets
 * (valeurs arbitraires `h-[clamp(…)]`) et variables `bg-(--ma-couleur)`, aucun
 * caractère de code : `(`, `$`, `'`…
 * Garde-fou : une concaténation PHP prise pour une liste de classes
 * (`class="' . esc_attr( $x ) . '"`) n'est jamais modifiée.
 */
const isClass = (token) =>
  /[a-z]/i.test(token) &&
  /^[!-]?[\w@\-:/.%#&>+*~]*$/.test(token.replace(/\[[^\]]*\]|\(--[^)]*\)/g, ''));

/** Trie une liste de classes séparées par des espaces, en gardant les espaces autour. */
function sortList(list) {
  if (!list.trim()) return list;
  const [, before, classes, after] = list.match(/^(\s*)([\s\S]*?)(\s*)$/);
  const tokens = classes.split(/\s+/);
  if (!tokens.every(isClass)) return list; // Pas une liste de classes : on n'y touche pas.
  return before + tokens.sort(compare).join(' ') + after;
}

/**
 * Trie les parties fixes d'une valeur qui contient du code (`<?php … ?>` ou `${…}`).
 * Code mal fermé dans la valeur : on ne touche à rien.
 */
function sortAround(code, open) {
  return (value) => {
    const parts = value.split(code);
    if (parts.some((part, i) => i % 2 === 0 && part.includes(open))) return value;
    return parts.map((part, i) => (i % 2 ? part : sortList(part))).join('');
  };
}
const sortPhpValue = sortAround(/(<\?php[\s\S]*?\?>)/, '<?php');
const sortTemplate = sortAround(/(\$\{[^}]*\})/, '${');

const rules = {
  php: [
    // class="…" (attribut HTML, y compris dans une chaîne PHP entre apostrophes)
    { regex: /(\bclass=")([^"]*)(")/g, sort: sortPhpValue },
    // 'class' => '…' (tableaux PHP)
    { regex: /('class'\s*=>\s*')([^'\\]*)(')/g, sort: sortList },
    // starter_icon( 'nom', '…'
    { regex: /(starter_icon\(\s*[^,()]+,\s*')([^'\\]*)(')/g, sort: sortList },
  ],
  js: [
    // className="…"
    { regex: /(\bclassName=")([^"]*)(")/g, sort: sortList },
    // className: '…'
    { regex: /(\bclassName:\s*')([^'\\]*)(')/g, sort: sortList },
    // className={`… ${…} …`}
    { regex: /(\bclassName=\{`)([^`]*)(`\})/g, sort: sortTemplate },
    // export const INNER = '…' (constantes de classes en majuscules)
    { regex: /(\bconst [A-Z][A-Z_]*\s*=\s*')([^'\\]*)(')/g, sort: sortList },
  ],
};

const changed = [];
for (const file of sourceFiles()) {
  const source = fs.readFileSync(file, 'utf8');
  let output = source;
  for (const { regex, sort } of rules[file.endsWith('.php') ? 'php' : 'js']) {
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
    ? `Classes triées (A → Z) : ${changed.length} fichier(s)\n  ${changed.join('\n  ')}`
    : 'Classes Tailwind : tout est trié (A → Z).',
);

/**
 * Configuration Vite.
 *
 * - `npm run dev`   : serveur de dev sur http://localhost:5173. Le CSS/JS se met à
 *                     jour sans recharger la page, et la page se recharge quand
 *                     un fichier PHP change.
 * - `npm run build` : compile et minifie dans dist/ (fichiers à déployer).
 *
 * Voir docs/build.md.
 */
import fs from 'node:fs';
import path from 'node:path';
import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

const DEV_PORT = 5173;

/**
 * Points d'entrée : chaque fichier listé ici produit un fichier dans dist/
 * et peut être chargé en PHP avec starter_vite_enqueue().
 */
const entries = ['assets/src/css/main.css', 'assets/src/js/main.js'];

/**
 * Petit plugin maison pour faire le lien avec WordPress :
 * 1. crée le fichier `hot` quand le serveur de dev démarre (PHP le lit pour
 *    savoir qu'il doit charger les fichiers depuis Vite) et le supprime à l'arrêt ;
 * 2. recharge la page quand un fichier .php est modifié.
 */
function wordpress() {
  const hotFile = path.resolve(import.meta.dirname, 'hot');

  return {
    name: 'starter-wordpress',
    configureServer(server) {
      server.httpServer?.once('listening', () => {
        fs.writeFileSync(hotFile, `http://localhost:${DEV_PORT}`);
      });

      const removeHotFile = () => fs.rmSync(hotFile, { force: true });
      process.on('exit', removeHotFile);
      process.on('SIGINT', () => process.exit());
      process.on('SIGTERM', () => process.exit());

      server.watcher.on('change', (file) => {
        if (file.endsWith('.php')) {
          server.ws.send({ type: 'full-reload' });
        }
      });
    },
  };
}

export default defineConfig({
  // Chemins relatifs dans le CSS compilé (polices, images) : dist/ peut être
  // servi depuis n'importe quelle URL.
  base: './',
  plugins: [tailwindcss(), wordpress()],
  server: {
    port: DEV_PORT,
    strictPort: true,
    // Le site (wordpress-starter.local) charge les fichiers depuis localhost:5173.
    cors: true,
    origin: `http://localhost:${DEV_PORT}`,
  },
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    // dist/.vite/manifest.json : table « fichier source → fichier compilé »
    // lue par inc/vite.php.
    manifest: true,
    rolldownOptions: {
      input: entries,
    },
  },
});

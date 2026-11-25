/**
 * Point d'entrée du bloc : l'enregistre côté éditeur (JavaScript).
 *
 * Les réglages (nom, attributs, supports…) viennent de block.json : on ne les
 * répète pas ici. On fournit seulement le composant d'édition.
 */
import { registerBlockType } from '@wordpress/blocks';

import metadata from './block.json';
import Edit from './edit.js';

registerBlockType(metadata.name, {
  edit: Edit,
  // Pas de `save` : le HTML du site est généré en PHP par render.php
  // (bloc « dynamique »). Voir docs/native-blocks.md.
  save: () => null,
});

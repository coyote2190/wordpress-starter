import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import Edit from './edit.js';

// Bloc dynamique : pas de save(), le rendu front est fait par render.php
registerBlockType(metadata.name, { edit: Edit });

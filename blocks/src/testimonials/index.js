import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import Edit from './edit.js';

// Bloc dynamique : rendu front par render.php
registerBlockType(metadata.name, { edit: Edit });

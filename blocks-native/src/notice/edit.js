/**
 * Composant React affiché dans l'éditeur.
 *
 * - `attributes`     : valeurs du bloc (type, content), définies dans block.json ;
 * - `setAttributes`  : les modifie (WordPress sauvegarde tout seul).
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

import { VARIANTS } from './styles.js';

export default function Edit({ attributes, setAttributes }) {
  const { type, content, align } = attributes;
  const isFull = align === 'full';
  const variant = VARIANTS[type] ?? VARIANTS.info;

  // useBlockProps() : classes et attributs que WordPress doit poser sur la
  // racine du bloc (sélection, ancre…). Équivalent de get_block_wrapper_attributes() en PHP.
  const blockProps = useBlockProps({
    // Même règle que render.php : en pleine largeur, le fond couvre l'écran et
    // le contenu est dans un `container` (voir starter_block_inner_class()).
    className: `not-prose my-6 border-l-4 py-4 ${variant.className} ${isFull ? '' : 'rounded-md px-4'}`,
  });

  return (
    <>
      {/* Panneau de réglages à droite */}
      <InspectorControls>
        <PanelBody title={__('Réglages', 'starter')}>
          <SelectControl
            label={__('Type', 'starter')}
            value={type}
            options={Object.entries(VARIANTS).map(([value, { label }]) => ({ value, label }))}
            onChange={(value) => setAttributes({ type: value })}
            __next40pxDefaultSize
            __nextHasNoMarginBottom
          />
        </PanelBody>
      </InspectorControls>

      {/* Le bloc lui-même, éditable directement */}
      <div {...blockProps}>
        <div className={`flex gap-3 ${isFull ? 'container' : ''}`}>
          <span aria-hidden="true">{variant.icon}</span>
          <RichText
            tagName="p"
            className="m-0"
            value={content}
            onChange={(value) => setAttributes({ content: value })}
            placeholder={__('Votre message…', 'starter')}
            allowedFormats={['core/bold', 'core/italic', 'core/link']}
          />
        </div>
      </div>
    </>
  );
}

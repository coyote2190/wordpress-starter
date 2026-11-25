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
  const { type, content } = attributes;
  const variant = VARIANTS[type] ?? VARIANTS.info;

  // useBlockProps() : classes et attributs que WordPress doit poser sur la
  // racine du bloc (sélection, ancre…). Équivalent de get_block_wrapper_attributes() en PHP.
  const blockProps = useBlockProps({
    className: `not-prose my-6 flex gap-3 rounded-md border-l-4 p-4 ${variant.className}`,
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
    </>
  );
}

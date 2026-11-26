/**
 * Composant React affiché dans l'éditeur.
 *
 * - `attributes`     : valeurs du bloc (type, badge, content), définies dans block.json ;
 * - `setAttributes`  : les modifie (WordPress sauvegarde tout seul).
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';

import { INNER, TAG, VARIANTS, WRAPPER } from './styles.js';

export default function Edit({ attributes, setAttributes }) {
  const { type, badge, content, align } = attributes;
  const isFull = align === 'full';
  const variant = VARIANTS[type] ?? VARIANTS.info;

  // useBlockProps() : classes et attributs que WordPress doit poser sur la
  // racine du bloc (sélection, ancre…). Équivalent de get_block_wrapper_attributes() en PHP.
  // Même règle que render.php : en pleine largeur, le contenu est dans un `container`.
  const blockProps = useBlockProps({ className: WRAPPER });

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
          <TextControl
            label={__('Étiquette', 'starter')}
            help={__(
              'Un ou deux mots (ex. « 30 min », « Nouveau »). Vide = icône du type.',
              'starter',
            )}
            value={badge}
            onChange={(value) => setAttributes({ badge: value })}
            __next40pxDefaultSize
            __nextHasNoMarginBottom
          />
        </PanelBody>
      </InspectorControls>

      {/* Le bloc lui-même, éditable directement */}
      <div {...blockProps}>
        <div className={`flex justify-center ${isFull ? 'container' : ''}`}>
          <div className={INNER}>
            <span
              className={`${TAG} ${variant.className}`}
              aria-hidden={badge ? undefined : 'true'}
            >
              {badge || variant.icon}
            </span>
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
      </div>
    </>
  );
}

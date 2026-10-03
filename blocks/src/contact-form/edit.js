import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { Disabled, PanelBody, TextControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

const fieldProps = { __next40pxDefaultSize: true, __nextHasNoMarginBottom: true };

export default function Edit({ attributes, setAttributes, name }) {
  const { title, buttonText } = attributes;

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Réglages', 'starter')}>
          <TextControl
            {...fieldProps}
            label={__('Titre (optionnel)', 'starter')}
            value={title}
            onChange={(value) => setAttributes({ title: value })}
          />
          <TextControl
            {...fieldProps}
            label={__('Texte du bouton', 'starter')}
            value={buttonText}
            onChange={(value) => setAttributes({ buttonText: value })}
          />
          <p className="components-base-control__help">
            {__(
              "Les messages sont envoyés à l'email de l'administrateur (Réglages > Général).",
              'starter'
            )}
          </p>
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        {/* Aperçu réel (render.php), non interactif dans l'éditeur */}
        <Disabled>
          <ServerSideRender block={name} attributes={attributes} />
        </Disabled>
      </div>
    </>
  );
}

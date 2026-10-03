import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { Button, Flex, FlexBlock, FlexItem, PanelBody, TextControl } from '@wordpress/components';

// Même composant qu'en front : l'aperçu dans l'éditeur est le vrai simulateur
import Simulator from '../../../assets/src/js/islands/Simulator.jsx';

const fieldProps = { __next40pxDefaultSize: true, __nextHasNoMarginBottom: true };

export default function Edit({ attributes, setAttributes }) {
  const { title, basePrice, options, vatRate, ctaText, ctaUrl } = attributes;

  const updateOption = (index, changes) =>
    setAttributes({
      options: options.map((option, i) => (i === index ? { ...option, ...changes } : option)),
    });

  const removeOption = (index) => setAttributes({ options: options.filter((_, i) => i !== index) });

  const addOption = () =>
    setAttributes({ options: [...options, { label: __('Nouvelle option', 'starter'), price: 0 }] });

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Réglages', 'starter')}>
          <TextControl
            {...fieldProps}
            label={__('Titre', 'starter')}
            value={title}
            onChange={(value) => setAttributes({ title: value })}
          />
          <TextControl
            {...fieldProps}
            type="number"
            label={__('Prix de base HT (€)', 'starter')}
            value={basePrice}
            onChange={(value) => setAttributes({ basePrice: Number(value) || 0 })}
          />
          <TextControl
            {...fieldProps}
            type="number"
            label={__('TVA (%) — 0 pour masquer le TTC', 'starter')}
            value={vatRate}
            onChange={(value) => setAttributes({ vatRate: Number(value) || 0 })}
          />
        </PanelBody>

        <PanelBody title={__('Options', 'starter')}>
          {options.map((option, index) => (
            <Flex key={index} align="flex-end" style={{ marginBottom: 12 }}>
              <FlexBlock>
                <TextControl
                  {...fieldProps}
                  label={__('Libellé', 'starter')}
                  value={option.label}
                  onChange={(value) => updateOption(index, { label: value })}
                />
              </FlexBlock>
              <FlexItem style={{ width: 80 }}>
                <TextControl
                  {...fieldProps}
                  type="number"
                  label={__('Prix', 'starter')}
                  value={option.price}
                  onChange={(value) => updateOption(index, { price: Number(value) || 0 })}
                />
              </FlexItem>
              <FlexItem>
                <Button
                  icon="trash"
                  label={__('Supprimer', 'starter')}
                  isDestructive
                  onClick={() => removeOption(index)}
                />
              </FlexItem>
            </Flex>
          ))}
          <Button variant="secondary" onClick={addOption}>
            {__('Ajouter une option', 'starter')}
          </Button>
        </PanelBody>

        <PanelBody title={__('Bouton', 'starter')} initialOpen={false}>
          <TextControl
            {...fieldProps}
            label={__('Texte', 'starter')}
            value={ctaText}
            onChange={(value) => setAttributes({ ctaText: value })}
          />
          <TextControl
            {...fieldProps}
            label={__('URL', 'starter')}
            value={ctaUrl}
            onChange={(value) => setAttributes({ ctaUrl: value })}
          />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <Simulator {...attributes} />
      </div>
    </>
  );
}

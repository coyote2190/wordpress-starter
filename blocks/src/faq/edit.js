import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import ItemList from '../../shared/ItemList.js';

export default function Edit({ attributes, setAttributes }) {
  const { title, items, schema } = attributes;

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Réglages', 'starter')}>
          <ToggleControl
            __nextHasNoMarginBottom
            label={__('Balisage FAQ pour Google (JSON-LD)', 'starter')}
            help={__('À désactiver si la même FAQ apparaît sur plusieurs pages.', 'starter')}
            checked={schema}
            onChange={(value) => setAttributes({ schema: value })}
          />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps({ className: 'mx-auto max-w-narrow' })}>
        <RichText
          tagName="h2"
          className="mb-6 text-center text-[1.75rem] md:text-[2rem]"
          value={title}
          allowedFormats={[]}
          placeholder={__('Titre (optionnel)', 'starter')}
          onChange={(value) => setAttributes({ title: value })}
        />
        <ItemList
          items={items}
          onChange={(value) => setAttributes({ items: value })}
          newItem={{ question: '', answer: '' }}
          addLabel={__('Ajouter une question', 'starter')}
          className="rounded-ui border border-line"
          itemClassName="border-line px-4 py-3 not-first:border-t"
          renderItem={(item, update) => (
            <>
              <RichText
                tagName="p"
                className="m-0 font-medium"
                value={item.question}
                allowedFormats={[]}
                placeholder={__('Question', 'starter')}
                onChange={(question) => update({ question })}
              />
              <RichText
                tagName="div"
                className="mt-2 text-muted"
                value={item.answer}
                allowedFormats={['core/bold', 'core/italic', 'core/link']}
                placeholder={__('Réponse', 'starter')}
                onChange={(answer) => update({ answer })}
              />
            </>
          )}
        />
      </div>
    </>
  );
}

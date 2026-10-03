import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import ItemList from '../../shared/ItemList.js';
import { COLUMNS } from './columns.js';

export default function Edit({ attributes, setAttributes }) {
  const { title, columns, items } = attributes;

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Réglages', 'starter')}>
          <RangeControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            label={__('Colonnes', 'starter')}
            min={1}
            max={3}
            value={columns}
            onChange={(value) => setAttributes({ columns: value })}
          />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <RichText
          tagName="h2"
          className="mb-8 text-center text-[1.75rem] md:text-[2rem]"
          value={title}
          allowedFormats={[]}
          placeholder={__('Titre (optionnel)', 'starter')}
          onChange={(value) => setAttributes({ title: value })}
        />
        <ItemList
          items={items}
          onChange={(value) => setAttributes({ items: value })}
          newItem={{ quote: '', name: '', role: '' }}
          addLabel={__('Ajouter un témoignage', 'starter')}
          className={`grid gap-6 ${COLUMNS[columns] || COLUMNS[3]}`}
          itemClassName="card flex flex-col"
          renderItem={(item, update) => (
            <>
              <RichText
                tagName="p"
                className="mb-4 flex-1 text-lg"
                value={item.quote}
                allowedFormats={['core/bold', 'core/italic']}
                placeholder={__('Citation', 'starter')}
                onChange={(quote) => update({ quote })}
              />
              <RichText
                tagName="p"
                className="m-0 font-medium"
                value={item.name}
                allowedFormats={[]}
                placeholder={__('Nom', 'starter')}
                onChange={(name) => update({ name })}
              />
              <RichText
                tagName="p"
                className="m-0 text-sm text-muted"
                value={item.role}
                allowedFormats={[]}
                placeholder={__('Fonction, entreprise (optionnel)', 'starter')}
                onChange={(role) => update({ role })}
              />
            </>
          )}
        />
      </div>
    </>
  );
}

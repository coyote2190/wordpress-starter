import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
// Icônes SVG : la police Dashicons n'est pas chargée dans l'iframe de l'éditeur
import { chevronDown, chevronUp, trash } from '@wordpress/icons';

/**
 * Liste d'éléments éditable (FAQ, témoignages…) pour les blocs du thème
 * Ajout, suppression et réordonnancement ; le rendu de chaque élément
 * est fourni par le bloc via renderItem.
 *
 * @param {Object}   props
 * @param {Array}    props.items         Valeur de l'attribut (tableau d'objets)
 * @param {Function} props.onChange      (items) => void
 * @param {Object}   props.newItem       Élément ajouté par « Ajouter »
 * @param {string}   props.addLabel      Texte du bouton d'ajout
 * @param {Function} props.renderItem    (item, update(changes), index) => JSX
 * @param {string}   props.className     Classes du conteneur
 * @param {string}   props.itemClassName Classes de chaque élément
 */
export default function ItemList({
  items = [],
  onChange,
  newItem,
  addLabel,
  renderItem,
  className = '',
  itemClassName = '',
}) {
  const update = (index, changes) =>
    onChange(items.map((item, i) => (i === index ? { ...item, ...changes } : item)));

  const remove = (index) => onChange(items.filter((_, i) => i !== index));

  const move = (index, offset) => {
    const target = index + offset;
    if (target < 0 || target >= items.length) return;
    const next = [...items];
    [next[index], next[target]] = [next[target], next[index]];
    onChange(next);
  };

  return (
    <>
      <div className={className}>
        {items.map((item, index) => (
          <div key={index} className={itemClassName}>
            {renderItem(item, (changes) => update(index, changes), index)}
            <div className="mt-2 flex justify-end gap-1">
              <Button
                size="small"
                icon={chevronUp}
                label={__('Monter', 'starter')}
                disabled={index === 0}
                onClick={() => move(index, -1)}
              />
              <Button
                size="small"
                icon={chevronDown}
                label={__('Descendre', 'starter')}
                disabled={index === items.length - 1}
                onClick={() => move(index, 1)}
              />
              <Button
                size="small"
                icon={trash}
                isDestructive
                label={__('Supprimer', 'starter')}
                onClick={() => remove(index)}
              />
            </div>
          </div>
        ))}
      </div>
      <Button
        className="mt-4"
        variant="secondary"
        onClick={() => onChange([...items, { ...newItem }])}
      >
        {addLabel}
      </Button>
    </>
  );
}

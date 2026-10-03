import { useState } from 'react';
import Checkbox from '../ui/Checkbox.jsx';
import Slider from '../ui/Slider.jsx';

/**
 * Simulateur de devis
 *
 * Composant React pur : utilisé en front (îlot Vite) ET dans l'éditeur
 * (aperçu du bloc starter/simulator, avec le React fourni par WordPress).
 * → Pas de composant à portal (Select, Dialog) ici : ils s'ouvriraient
 *   hors de l'iframe de l'éditeur.
 *
 * Prix = base + (quantité − quantité incluse) × prix unitaire + options
 *
 * @param {Object} props
 * @param {string} props.title         Titre affiché
 * @param {number} props.basePrice     Prix de base HT
 * @param {string} props.quantityLabel Libellé du curseur (vide = pas de curseur)
 * @param {string} props.quantityUnit  Unité affichée (ex : « pages »)
 * @param {number} props.quantityMin   Quantité incluse dans le prix de base
 * @param {number} props.quantityMax   Quantité maximum
 * @param {number} props.quantityPrice Prix HT par unité supplémentaire
 * @param {Array}  props.options       [{ label: string, price: number }]
 * @param {number} props.vatRate       Taux de TVA en % (0 pour masquer le TTC)
 * @param {string} props.ctaText       Texte du bouton (optionnel)
 * @param {string} props.ctaUrl        URL du bouton (optionnel)
 */

const formatPrice = (value) =>
  new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
    maximumFractionDigits: 0,
  }).format(value);

export default function Simulator({
  title = '',
  basePrice = 0,
  quantityLabel = '',
  quantityUnit = '',
  quantityMin = 1,
  quantityMax = 10,
  quantityPrice = 0,
  options = [],
  vatRate = 20,
  ctaText = '',
  ctaUrl = '',
}) {
  const min = Number(quantityMin);
  const max = Math.max(min, Number(quantityMax));
  const hasQuantity = Boolean(quantityLabel) && Number(quantityPrice) > 0 && max > min;

  const [quantity, setQuantity] = useState(min);
  const [selected, setSelected] = useState([]);

  const toggle = (index, checked) =>
    setSelected((current) => (checked ? [...current, index] : current.filter((i) => i !== index)));

  // Quantité bornée : les réglages peuvent changer dans l'éditeur
  const currentQuantity = Math.min(Math.max(quantity, min), max);
  const extraUnits = hasQuantity ? currentQuantity - min : 0;

  const totalHT =
    Number(basePrice) +
    extraUnits * Number(quantityPrice) +
    selected.reduce((sum, index) => sum + Number(options[index]?.price || 0), 0);
  const totalTTC = totalHT * (1 + Number(vatRate) / 100);

  return (
    <div className="card mx-auto max-w-narrow">
      {title && <h2 className="mb-4 text-2xl">{title}</h2>}

      <p className="mb-6 text-muted">
        Base : <strong>{formatPrice(basePrice)} HT</strong>
        {hasQuantity && (
          <>
            {' '}
            pour {min} {quantityUnit}
          </>
        )}
      </p>

      {hasQuantity && (
        <Slider
          className="mb-6"
          label={quantityLabel}
          min={min}
          max={max}
          value={currentQuantity}
          onValueChange={setQuantity}
          format={(value) => `${value} ${quantityUnit}`.trim()}
        />
      )}

      {options.length > 0 && (
        <fieldset className="m-0 mb-6 border-0 p-0">
          <legend className="mb-2 font-medium">Options</legend>
          {options.map((option, index) => (
            <Checkbox
              key={index}
              className="justify-between border-b border-line py-2"
              checked={selected.includes(index)}
              onCheckedChange={(checked) => toggle(index, checked)}
            >
              <span className="flex-1">{option.label}</span>
              <span className="whitespace-nowrap text-muted">
                + {formatPrice(option.price || 0)}
              </span>
            </Checkbox>
          ))}
        </fieldset>
      )}

      <output className="mb-6 flex flex-wrap items-baseline gap-x-4 gap-y-2" aria-live="polite">
        <span>Estimation</span>
        <strong className="text-3xl">{formatPrice(totalHT)} HT</strong>
        {Number(vatRate) > 0 && (
          <small className="text-muted">soit {formatPrice(totalTTC)} TTC</small>
        )}
      </output>

      {ctaText && ctaUrl && (
        <a className="btn" href={ctaUrl}>
          {ctaText}
        </a>
      )}
    </div>
  );
}

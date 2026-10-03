import { useId, useState } from 'react';

/**
 * Simulateur de devis
 *
 * Composant React pur : utilisé en front (îlot Vite) ET dans l'éditeur
 * (aperçu du bloc starter/simulator, avec le React fourni par WordPress).
 * → Ne pas importer de dépendance spécifique à l'un ou l'autre ici.
 *
 * @param {Object}   props
 * @param {string}   props.title     Titre affiché
 * @param {number}   props.basePrice Prix de base HT
 * @param {Array}    props.options   [{ label: string, price: number }]
 * @param {number}   props.vatRate   Taux de TVA en % (0 pour masquer le TTC)
 * @param {string}   props.ctaText   Texte du bouton (optionnel)
 * @param {string}   props.ctaUrl    URL du bouton (optionnel)
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
  options = [],
  vatRate = 20,
  ctaText = '',
  ctaUrl = '',
}) {
  const id = useId();
  const [selected, setSelected] = useState([]);

  const toggle = (index) =>
    setSelected((current) =>
      current.includes(index) ? current.filter((i) => i !== index) : [...current, index]
    );

  const totalHT =
    Number(basePrice) +
    selected.reduce((sum, index) => sum + Number(options[index]?.price || 0), 0);
  const totalTTC = totalHT * (1 + Number(vatRate) / 100);

  return (
    <div className="simulator">
      {title && <h2 className="simulator__title">{title}</h2>}

      <p className="simulator__base">
        Base : <strong>{formatPrice(basePrice)} HT</strong>
      </p>

      {options.length > 0 && (
        <fieldset className="simulator__options">
          <legend>Options</legend>
          {options.map((option, index) => (
            <div className="simulator__option" key={index}>
              <input
                type="checkbox"
                id={`${id}-${index}`}
                checked={selected.includes(index)}
                onChange={() => toggle(index)}
              />
              <label htmlFor={`${id}-${index}`}>
                {option.label}
                <span className="simulator__price">+ {formatPrice(option.price || 0)}</span>
              </label>
            </div>
          ))}
        </fieldset>
      )}

      <output className="simulator__total" aria-live="polite">
        <span>Estimation</span>
        <strong>{formatPrice(totalHT)} HT</strong>
        {Number(vatRate) > 0 && <small>soit {formatPrice(totalTTC)} TTC</small>}
      </output>

      {ctaText && ctaUrl && (
        <a className="btn simulator__cta" href={ctaUrl}>
          {ctaText}
        </a>
      )}
    </div>
  );
}

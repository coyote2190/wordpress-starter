/**
 * Kit UI React — composants Base UI stylés avec les tokens du thème
 *
 * Base UI fournit le comportement (clavier, focus, ARIA), pas le style :
 * le style vient des tokens @theme de main.css (couleurs, rounded-ui, shadow-ui)
 * et des classes partagées avec le PHP (.btn, .field, .card).
 *
 * @example import { Checkbox, Slider } from '../ui';
 */
export { default as Accordion } from './Accordion.jsx';
export { default as Checkbox } from './Checkbox.jsx';
export { default as Dialog } from './Dialog.jsx';
export { default as NumberField } from './NumberField.jsx';
export { default as Select } from './Select.jsx';
export { default as Slider } from './Slider.jsx';
export * from './icons.jsx';

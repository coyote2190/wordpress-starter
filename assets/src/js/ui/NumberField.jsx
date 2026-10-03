import { useId } from 'react';
import { NumberField as BaseNumberField } from '@base-ui/react/number-field';
import { MinusIcon, PlusIcon } from './icons.jsx';

const stepper =
  'flex h-full w-10 items-center justify-center border border-line bg-canvas text-ink select-none hover:not-data-disabled:bg-line/50 data-disabled:text-muted';

/**
 * Champ numérique avec boutons − / +
 *
 * @example <NumberField label="Quantité" min={1} value={qty} onValueChange={setQty} />
 *
 * @param {Object}   props
 * @param {string}   props.label
 * @param {number}   props.value
 * @param {Function} props.onValueChange (value: number|null) => void
 * @param {number}   props.min / max / step
 */
export default function NumberField({ label, className = '', ...props }) {
  const id = useId();

  return (
    <BaseNumberField.Root
      id={id}
      className={`flex flex-col items-start gap-1 ${className}`}
      {...props}
    >
      <label htmlFor={id} className="m-0">
        {label}
      </label>
      <BaseNumberField.Group className="flex h-10">
        <BaseNumberField.Decrement className={`${stepper} rounded-l-ui border-r-0`}>
          <MinusIcon />
        </BaseNumberField.Decrement>
        <BaseNumberField.Input className="h-full w-[8ch] rounded-none border border-line bg-canvas px-2 text-center tabular-nums focus:z-1" />
        <BaseNumberField.Increment className={`${stepper} rounded-r-ui border-l-0`}>
          <PlusIcon />
        </BaseNumberField.Increment>
      </BaseNumberField.Group>
    </BaseNumberField.Root>
  );
}

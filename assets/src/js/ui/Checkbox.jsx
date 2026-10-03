import { Checkbox as BaseCheckbox } from '@base-ui/react/checkbox';
import { CheckIcon } from './icons.jsx';

/**
 * Case à cocher
 *
 * @example <Checkbox checked={on} onCheckedChange={setOn}>Newsletter</Checkbox>
 *
 * @param {Object}   props
 * @param {boolean}  props.checked
 * @param {Function} props.onCheckedChange (checked: boolean) => void
 * @param {*}        props.children        Libellé (cliquable)
 * @param {string}   props.className       Classes du <label>
 */
export default function Checkbox({ children, className = '', ...props }) {
  return (
    <label className={`m-0 flex cursor-pointer items-center gap-2 ${className}`}>
      <BaseCheckbox.Root
        className="flex size-4 shrink-0 items-center justify-center rounded-[min(var(--radius-ui),0.25rem)] border border-ink bg-canvas p-0 text-canvas data-checked:bg-ink"
        {...props}
      >
        <BaseCheckbox.Indicator className="flex data-unchecked:hidden">
          <CheckIcon />
        </BaseCheckbox.Indicator>
      </BaseCheckbox.Root>
      {children}
    </label>
  );
}

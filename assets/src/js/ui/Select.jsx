import { Select as BaseSelect } from '@base-ui/react/select';
import { CaretUpDownIcon, CheckIcon } from './icons.jsx';

/**
 * Liste déroulante
 *
 * @example
 * <Select label="Formule" items={[{ label: 'Essentiel', value: 'ess' }]} value={v} onValueChange={setV} />
 *
 * ⚠️ La liste s'ouvre dans un portal (document.body) : à éviter dans un composant
 *    aussi utilisé comme aperçu dans l'éditeur Gutenberg (iframe).
 *
 * @param {Object}   props
 * @param {string}   props.label
 * @param {Array}    props.items         [{ label: string, value: string }]
 * @param {string}   props.placeholder
 * @param {string}   props.value
 * @param {Function} props.onValueChange (value: string) => void
 */
export default function Select({
  label,
  items = [],
  placeholder = 'Choisir…',
  className = '',
  ...props
}) {
  return (
    <div className={`flex flex-col items-start gap-1 ${className}`}>
      <BaseSelect.Root items={items} {...props}>
        <BaseSelect.Label className="m-0">{label}</BaseSelect.Label>
        <BaseSelect.Trigger className="field flex cursor-pointer items-center justify-between gap-3 select-none hover:not-data-disabled:bg-line/50 data-disabled:text-muted">
          <BaseSelect.Value className="data-placeholder:text-muted" placeholder={placeholder} />
          <BaseSelect.Icon>
            <CaretUpDownIcon />
          </BaseSelect.Icon>
        </BaseSelect.Trigger>
        <BaseSelect.Portal>
          <BaseSelect.Positioner className="z-50 outline-hidden select-none" sideOffset={4}>
            <BaseSelect.Popup className="min-w-[var(--anchor-width)] origin-[var(--transform-origin)] rounded-ui border border-ink bg-canvas text-ink shadow-ui outline-hidden transition-[scale,opacity] duration-100 ease-out data-ending-style:scale-[0.98] data-ending-style:opacity-0 data-starting-style:scale-[0.98] data-starting-style:opacity-0">
              <BaseSelect.List className="relative max-h-[var(--available-height)] scroll-py-6 overflow-y-auto py-1">
                {items.map((item) => (
                  <BaseSelect.Item
                    key={item.value}
                    value={item.value}
                    className="grid cursor-default grid-cols-[1rem_1fr] items-center gap-2 py-2 pr-4 pl-3 outline-hidden select-none data-highlighted:bg-ink data-highlighted:text-canvas"
                  >
                    <BaseSelect.ItemIndicator className="col-start-1">
                      <CheckIcon />
                    </BaseSelect.ItemIndicator>
                    <BaseSelect.ItemText className="col-start-2">{item.label}</BaseSelect.ItemText>
                  </BaseSelect.Item>
                ))}
              </BaseSelect.List>
            </BaseSelect.Popup>
          </BaseSelect.Positioner>
        </BaseSelect.Portal>
      </BaseSelect.Root>
    </div>
  );
}

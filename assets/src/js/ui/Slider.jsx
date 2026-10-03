import { Slider as BaseSlider } from '@base-ui/react/slider';

/**
 * Curseur (une valeur)
 *
 * @example <Slider label="Nombre de pages" min={1} max={30} value={n} onValueChange={setN} />
 *
 * @param {Object}   props
 * @param {string}   props.label         Libellé visible (requis pour l'accessibilité)
 * @param {number}   props.value
 * @param {Function} props.onValueChange (value: number) => void
 * @param {Function} props.format        Formatage de la valeur affichée (optionnel)
 * @param {number}   props.min / max / step
 */
export default function Slider({ label, format = (v) => v, className = '', ...props }) {
  return (
    <BaseSlider.Root className={`flex flex-col gap-1 ${className}`} {...props}>
      <div className="flex items-baseline justify-between gap-4">
        <BaseSlider.Label className="m-0">{label}</BaseSlider.Label>
        <BaseSlider.Value className="font-medium tabular-nums">
          {(formatted, values) => format(values[0])}
        </BaseSlider.Value>
      </div>
      <BaseSlider.Control className="flex w-full touch-none items-center py-3 select-none">
        <BaseSlider.Track className="h-1 w-full bg-line select-none">
          <BaseSlider.Indicator className="bg-ink select-none" />
          <BaseSlider.Thumb className="size-4 rounded-ui border border-ink bg-canvas select-none has-[:focus-visible]:outline-2 has-[:focus-visible]:outline-offset-2 has-[:focus-visible]:outline-ink" />
        </BaseSlider.Track>
      </BaseSlider.Control>
    </BaseSlider.Root>
  );
}

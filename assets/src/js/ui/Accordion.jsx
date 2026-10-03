import { Accordion as BaseAccordion } from '@base-ui/react/accordion';
import { PlusIcon } from './icons.jsx';

/**
 * Accordéon (FAQ, détails…)
 *
 * @example <Accordion items={[{ title: 'Question ?', content: 'Réponse.' }]} />
 *
 * @param {Object}  props
 * @param {Array}   props.items    [{ title: string, content: ReactNode }]
 * @param {boolean} props.multiple Plusieurs panneaux ouverts à la fois (défaut : false)
 */
export default function Accordion({ items = [], className = '', ...props }) {
  return (
    <BaseAccordion.Root
      className={`flex w-full flex-col rounded-ui border border-line ${className}`}
      {...props}
    >
      {items.map((item, index) => (
        <BaseAccordion.Item key={index} className="border-line not-first:border-t">
          <BaseAccordion.Header className="m-0 text-base">
            <BaseAccordion.Trigger className="group flex w-full cursor-pointer items-center justify-between gap-4 bg-transparent px-4 py-3 text-left font-medium select-none hover:not-data-disabled:bg-line/50 focus-visible:relative focus-visible:z-1">
              {item.title}
              <PlusIcon className="shrink-0 transition-transform duration-150 group-data-panel-open:rotate-45" />
            </BaseAccordion.Trigger>
          </BaseAccordion.Header>
          <BaseAccordion.Panel className="h-[var(--accordion-panel-height)] overflow-hidden transition-[height] duration-150 ease-out data-ending-style:h-0 data-starting-style:h-0">
            <div className="px-4 pb-4 text-muted">{item.content}</div>
          </BaseAccordion.Panel>
        </BaseAccordion.Item>
      ))}
    </BaseAccordion.Root>
  );
}

import { useState } from 'react';
import { Accordion, Checkbox, Dialog, NumberField, Select, Slider } from '../ui';

/**
 * Démo du kit UI React — affichée par la page « Styleguide »
 * Sert de référence visuelle et de copier-coller pour les nouveaux îlots.
 */
export default function UiDemo() {
  const [checked, setChecked] = useState(true);
  const [pages, setPages] = useState(8);
  const [quantity, setQuantity] = useState(2);
  const [plan, setPlan] = useState(null);

  return (
    <div className="grid gap-8 md:grid-cols-2">
      <div className="card flex flex-col gap-6">
        <Checkbox checked={checked} onCheckedChange={setChecked}>
          Checkbox
        </Checkbox>

        <Slider
          label="Slider"
          min={1}
          max={30}
          value={pages}
          onValueChange={setPages}
          format={(value) => `${value} pages`}
        />

        <NumberField
          label="NumberField"
          min={1}
          max={99}
          value={quantity}
          onValueChange={setQuantity}
        />

        <Select
          label="Select"
          placeholder="Choisir une formule"
          value={plan}
          onValueChange={setPlan}
          items={[
            { label: 'Essentiel', value: 'essentiel' },
            { label: 'Pro', value: 'pro' },
            { label: 'Sur mesure', value: 'sur-mesure' },
          ]}
        />

        <div>
          <Dialog trigger="Dialog" title="Titre de la modale" description="Une description courte.">
            <p className="m-0">Contenu libre : texte, formulaire, composants du kit…</p>
          </Dialog>
        </div>
      </div>

      <Accordion
        className="self-start"
        items={[
          { title: 'Accordion — question 1 ?', content: 'Réponse à la première question.' },
          { title: 'Question 2 ?', content: 'Réponse à la deuxième question.' },
          { title: 'Question 3 ?', content: 'Réponse à la troisième question.' },
        ]}
      />
    </div>
  );
}

import { createRoot } from 'react-dom/client';

/**
 * Registre des îlots React
 *
 * Clé = valeur de data-island côté PHP (voir starter_island()).
 * Chaque composant est chargé à la demande : une page sans îlot
 * ne télécharge ni React ni le composant.
 */
const registry = {
  simulator: () => import('./Simulator.jsx'),
  'ui-demo': () => import('./UiDemo.jsx'),
};

/**
 * Monte chaque îlot dans son conteneur, avec les props JSON de data-props
 *
 * @param {NodeListOf<HTMLElement>} nodes
 */
export function mountIslands(nodes) {
  nodes.forEach(async (node) => {
    const name = node.dataset.island;
    const load = registry[name];

    if (!load) {
      console.warn(`[islands] Composant inconnu : "${name}"`);
      return;
    }

    let props = {};
    try {
      props = JSON.parse(node.dataset.props || '{}');
    } catch (error) {
      console.error(`[islands] data-props invalide pour "${name}"`, error);
      return;
    }

    const { default: Component } = await load();
    createRoot(node).render(<Component {...props} />);
  });
}

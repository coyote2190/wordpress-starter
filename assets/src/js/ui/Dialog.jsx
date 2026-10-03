import { Dialog as BaseDialog } from '@base-ui/react/dialog';
import { CloseIcon } from './icons.jsx';

/**
 * Fenêtre modale
 *
 * @example
 * <Dialog trigger="Voir le détail" title="Détail du devis" description="…">
 *   <p>Contenu</p>
 * </Dialog>
 *
 * ⚠️ S'ouvre dans un portal (document.body) : à éviter dans un aperçu Gutenberg.
 *
 * @param {Object} props
 * @param {*}      props.trigger      Contenu du bouton d'ouverture
 * @param {string} props.title
 * @param {string} props.description  (optionnel)
 * @param {*}      props.children     Contenu de la modale
 */
export default function Dialog({ trigger, title, description, children, ...props }) {
  return (
    <BaseDialog.Root {...props}>
      <BaseDialog.Trigger className="btn">{trigger}</BaseDialog.Trigger>
      <BaseDialog.Portal>
        <BaseDialog.Backdrop className="fixed inset-0 z-50 min-h-dvh bg-ink/20 transition-opacity duration-150 data-ending-style:opacity-0 data-starting-style:opacity-0" />
        <BaseDialog.Popup className="fixed top-1/2 left-1/2 z-50 flex w-[32rem] max-w-[calc(100vw-2rem)] -translate-x-1/2 -translate-y-1/2 flex-col gap-4 rounded-ui border border-ink bg-canvas p-6 text-ink shadow-ui transition-[scale,opacity] duration-150 ease-out data-ending-style:scale-[0.98] data-ending-style:opacity-0 data-starting-style:scale-[0.98] data-starting-style:opacity-0">
          <div className="flex items-start justify-between gap-4">
            <div>
              <BaseDialog.Title className="mb-1 text-xl">{title}</BaseDialog.Title>
              {description && (
                <BaseDialog.Description className="m-0 text-muted">
                  {description}
                </BaseDialog.Description>
              )}
            </div>
            <BaseDialog.Close className="cursor-pointer p-1" aria-label="Fermer">
              <CloseIcon />
            </BaseDialog.Close>
          </div>
          {children}
        </BaseDialog.Popup>
      </BaseDialog.Portal>
    </BaseDialog.Root>
  );
}

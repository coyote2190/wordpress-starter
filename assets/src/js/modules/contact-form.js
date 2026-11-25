/**
 * Formulaire de contact : envoi en arrière-plan (sans rechargement de page).
 *
 * Sans JS, le formulaire fonctionne quand même (envoi classique + redirection).
 * Ce module améliore l'expérience : pas de rechargement, message immédiat,
 * erreurs affichées sous chaque champ.
 *
 * Le serveur (mu-plugin/starter-core/contact-form.php) répond en JSON quand
 * la requête contient l'en-tête Accept: application/json :
 *   { success: bool, message: string, errors: { champ: "message" } }
 *
 * @param {HTMLFormElement} form Élément qui porte data-module="contact-form".
 */
export default function contactForm(form) {
  const status = form.querySelector('[data-contact-status]');
  const submit = form.querySelector('[data-contact-submit]');

  const showStatus = (message, ok) => {
    status.textContent = message;
    status.classList.toggle('bg-green-50', ok);
    status.classList.toggle('text-green-800', ok);
    status.classList.toggle('bg-red-50', !ok);
    status.classList.toggle('text-red-700', !ok);
  };

  const clearErrors = () => {
    form.querySelectorAll('[data-error-for]').forEach((el) => (el.textContent = ''));
    form.querySelectorAll('[aria-invalid]').forEach((el) => el.removeAttribute('aria-invalid'));
  };

  const showErrors = (errors) => {
    Object.entries(errors).forEach(([name, message]) => {
      const hint = form.querySelector(`[data-error-for="${name}"]`);
      const field = form.elements.namedItem(name);
      if (hint) hint.textContent = message;
      if (field instanceof HTMLElement) field.setAttribute('aria-invalid', 'true');
    });
    // Focus sur le premier champ en erreur.
    form.querySelector('[aria-invalid="true"]')?.focus();
  };

  form.addEventListener('submit', async (event) => {
    event.preventDefault(); // Le navigateur a déjà vérifié les champs « required ».

    clearErrors();
    showStatus('', true);
    submit.disabled = true;

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { Accept: 'application/json' },
      });
      const data = await response.json();

      showStatus(data.message, data.success);

      if (data.success) {
        form.reset();
      } else {
        showErrors(data.errors ?? {});
      }
    } catch {
      showStatus("Erreur réseau : le message n'a pas pu être envoyé. Réessayez.", false);
    } finally {
      submit.disabled = false;
    }
  });
}

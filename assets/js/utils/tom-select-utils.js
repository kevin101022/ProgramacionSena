/**
 * tom-select-utils.js
 * Helpers globales para inicializar TomSelect en selects del sistema SENA.
 * TomSelect debe estar cargado globalmente desde head.php antes de este script.
 */

/**
 * Inicializa TomSelect en un <select> que ya tiene sus opciones pobladas.
 * Incluye guardia anti-doble-init: si ya tiene tomselect, no hace nada.
 * @param {string|HTMLElement} selector - ID CSS (#id) o elemento DOM
 * @param {string} placeholder - Texto del placeholder de búsqueda
 * @returns {TomSelect|null}
 */
window.initTS = function(selector, placeholder = 'Buscar...') {
    if (!window.TomSelect) return null;
    const el = typeof selector === 'string' ? document.querySelector(selector) : selector;
    if (!el) return null;
    if (el.tomselect) return el.tomselect; // ya inicializado
    return new TomSelect(el, {
        create: false,
        maxOptions: null,
        placeholder,
        allowEmptyOption: true,
    });
};

/**
 * Destruye TomSelect si existe, repuebla el <select> con nuevas opciones
 * y vuelve a inicializarlo. Útil para selects cuyas opciones cambian (ej: ambiente_id).
 * @param {string|HTMLElement} selector
 * @param {Array<{value, text}>} newOptions
 * @param {string} placeholder
 * @returns {TomSelect|null}
 */
window.refreshTS = function(selector, newOptions, placeholder = 'Buscar...') {
    if (!window.TomSelect) return null;
    const el = typeof selector === 'string' ? document.querySelector(selector) : selector;
    if (!el) return null;
    if (el.tomselect) el.tomselect.destroy();
    el.innerHTML = `<option value="">${placeholder}</option>`;
    newOptions.forEach(({ value, text }) => {
        const opt = document.createElement('option');
        opt.value = value;
        opt.textContent = text;
        el.appendChild(opt);
    });
    return window.initTS(el, placeholder);
};

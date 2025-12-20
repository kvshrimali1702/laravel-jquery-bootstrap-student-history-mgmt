import $ from 'jquery';

// Select2 (dist build) expects a global jQuery at load time, so we set it before dynamically importing.
if (typeof window !== 'undefined') {
    window.$ = $;
    window.jQuery = $;
}

export async function ensureSelect2() {
    const globalJQ = typeof window !== 'undefined' ? (window.jQuery || window.$) : undefined;

    if (typeof $.fn.select2 !== 'undefined') {
        return $;
    }

    if (globalJQ && globalJQ.fn && typeof globalJQ.fn.select2 !== 'undefined') {
        return $;
    }

    try {
        // Prefer the package entry so Vite can transform it correctly.
        // Some dist builds won't auto-attach in an ESM environment without explicit wiring.
        const mod = await import('select2');

        const maybeFactory = mod?.default ?? mod;
        if (typeof maybeFactory === 'function') {
            maybeFactory(globalJQ ?? $);
        } else {
            // Fallback: load the dist bundle (UMD) if the package entry wasn't callable.
            await import('select2/dist/js/select2.full.min.js');
        }
    } catch (error) {
        // Surface any bundling / resolution errors clearly in console.
        // eslint-disable-next-line no-console
        console.error('Failed to load Select2:', error);
        throw error;
    }

    const globalAfter = typeof window !== 'undefined' ? (window.jQuery || window.$) : undefined;

    // If Select2 attached to a different jQuery instance, bridge it to this instance too.
    if (globalAfter && globalAfter.fn && typeof globalAfter.fn.select2 !== 'undefined' && typeof $.fn.select2 === 'undefined') {
        $.fn.select2 = globalAfter.fn.select2;
    }

    if (typeof $.fn.select2 !== 'undefined' && globalAfter && globalAfter.fn && typeof globalAfter.fn.select2 === 'undefined') {
        globalAfter.fn.select2 = $.fn.select2;
    }

    return $;
}

export default $;



/**
 * Hide ePayco document fields from all pages
 * (Thank You page, My Account, Order details, etc.)
 */
document.addEventListener('DOMContentLoaded', function() {
    
    // Ocultar el bloque de "Additional information" de WooCommerce Blocks
    // (used on Thank You page and order details)
    var additionalFieldsWrapper = document.querySelector('.wp-block-woocommerce-order-confirmation-additional-fields-wrapper');
    if (additionalFieldsWrapper) {
        additionalFieldsWrapper.style.display = 'none';
        console.log('[ePayco] Hidden: wp-block-woocommerce-order-confirmation-additional-fields-wrapper');
    }
    
    // Ocultar la sección de "Additional information" en mi-cuenta
    // (used on My Account subscription details)
    var sections = document.querySelectorAll('section.wc-block-order-confirmation-additional-fields-wrapper');
    sections.forEach(function(section) {
        section.style.display = 'none';
        console.log('[ePayco] Hidden: wc-block-order-confirmation-additional-fields-wrapper section');
    });
    
    // Alternativa: ocultar por el contenedor de lista de definiciones
    var additionalFieldsLists = document.querySelectorAll('.wc-block-components-additional-fields-list');
    additionalFieldsLists.forEach(function(list) {
        // Revisar si contiene campos de ePayco (Identification Document o Document Number)
        var text = list.textContent;
        if (text.includes('Identification Document') || text.includes('Documento de Identificación') ||
            text.includes('Document Number') || text.includes('Número de documento')) {
            // Ocultar el contenedor padre (la sección)
            var section = list.closest('section');
            if (section) {
                section.style.display = 'none';
                console.log('[ePayco] Hidden: section containing additional fields list');
            }
        }
    });
});

window.addEventListener("load", (function () {
    (function ($) {
        console.log("epayco")
        
        // Translation strings
        var i18n = {
            es: {
                validationSuccess: "✓ Llaves validadas correctamente",
                validationSuccessDesc: "Las credenciales API fueron verificadas exitosamente.<br>Ya puedes usar ePayco con normalidad.",
                validationError: "✗ Error en validación",
                connectionError: "Error de conexión",
                connectionErrorDesc: "No se pudo conectar con el servidor. Verifica tu conexión e intenta nuevamente.",
                unexpectedError: "Error inesperado",
                unexpectedErrorDesc: "No se recibió respuesta válida del servidor",
                fieldRequired: "Campo requerido",
                fieldRequiredDesc: "Por favor ingresa la clave pública de API",
                invalidCredentials: "Las credenciales no son válidas"
            },
            en: {
                validationSuccess: "✓ Keys validated successfully",
                validationSuccessDesc: "API credentials were verified successfully.<br>You can now use ePayco normally.",
                validationError: "✗ Validation error",
                connectionError: "Connection error",
                connectionErrorDesc: "Could not connect to the server. Check your connection and try again.",
                unexpectedError: "Unexpected error",
                unexpectedErrorDesc: "Invalid response from server",
                fieldRequired: "Required field",
                fieldRequiredDesc: "Please enter your API public key",
                invalidCredentials: "Credentials are not valid"
            }
        };
        
        // Get current language from wp_localize_script
        var currentLang = (typeof wc_epaycosuscription_admin_components_params !== 'undefined' && wc_epaycosuscription_admin_components_params.is_english) 
            ? 'en' 
            : 'es';
        
        var msgs = i18n[currentLang];
        
        var modal = document.getElementById("myModal");
        var modalContent = document.getElementsByClassName("modal-content")[0];
        var span = document.getElementsByClassName("closeEpaycoModal")[0];
        var loader = document.getElementsByClassName("loader")[0];
        span.onclick = function() {
            modal.style.display = "none";
            modalContent.style.display = "none";
        }
        /*var shop_name = document.getElementById("woocommerce_woo-epaycosubscription_shop_name")
        shop_name.closest('tr').style.display = "none";
        var shop_icon = document.getElementById("woocommerce_woo-epaycosubscription_shop_icon")
        shop_icon.closest('tr').style.display = "none";
        */
        $(".validar").on("click", function() {
            loader.style.display = "block";
            modal.style.display = "block";
            var url_validate = $("#path_validate")[0].innerHTML.trim();
            var url_plugin = $("#path_plugin")[0].innerHTML.trim();
            
            const epayco_publickey = $("input:text[name=woocommerce_woo-epaycosubscription_apiKey]").val().replace(/\s/g,"");
            
            console.log('DEBUG: Public key length:', epayco_publickey.length);
            if (epayco_publickey !== "") {
                var formData = new FormData();
                formData.append("epayco_publickey", epayco_publickey);
                $.ajax({
                    url: url_validate,
                    type: "post",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        console.log('Response from ePayco API:', response);
                        loader.style.display = "none";
                        
                        // Handle success response from ePayco API
                        if (response && response.success === true) {
                            updateEpaycoModal(
                                url_plugin+"check.png",
                                msgs.validationSuccess,
                                msgs.validationSuccessDesc
                            );
                        } else if (response && response.success === false) {
                            // Handle error response
                            const errorMsg = response.message || msgs.invalidCredentials;
                            updateEpaycoModal(
                                url_plugin+"logo_warning.png",
                                msgs.validationError,
                                errorMsg
                            );
                        } else {
                            // Handle unexpected response format
                            updateEpaycoModal(
                                url_plugin+"logo_warning.png",
                                msgs.unexpectedError,
                                msgs.unexpectedErrorDesc
                            );
                        }
                        modalContent.style.display = "block";
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error, xhr.responseText);
                        loader.style.display = "none";
                        
                        // Handle AJAX request errors
                        updateEpaycoModal(
                            url_plugin+"logo_warning.png",
                            msgs.connectionError,
                            msgs.connectionErrorDesc
                        );
                        modalContent.style.display = "block";
                    }
                });
            } else {
                updateEpaycoModal(
                    url_plugin+"logo_warning.png",
                    msgs.fieldRequired,
                    msgs.fieldRequiredDesc
                );  
                loader.style.display = "none";
                modalContent.style.display = "block";
            }
        });
        function updateEpaycoModal(newImg, newTitle, newDescription) {
            $("#epaycoModalImg").attr("src", newImg);
            $("#epaycoCredentialTittle").html("<strong>" + newTitle + "</strong>");
            $("#epaycoCredentialDescription").html(newDescription);
        }
    })(jQuery);
}));

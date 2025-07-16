window.addEventListener("load", (function () {
    (function ($) {
        console.log("epayco")
        var modal = document.getElementById("myModal");
        var modalContent = document.getElementsByClassName("modal-content")[0];
        var span = document.getElementsByClassName("close")[0];
        var loader = document.getElementsByClassName("loader")[0];
        span.onclick = function() {
            modal.style.display = "none";
            modalContent.style.display = "none";
        }
        var shop_name = document.getElementById("woocommerce_woo-epaycosubscription_shop_name")
        shop_name.closest('tr').style.display = "none";
        var shop_icon = document.getElementById("woocommerce_woo-epaycosubscription_shop_icon")
        shop_icon.closest('tr').style.display = "none";
        $(".validar").on("click", function() {
            modal.style.display = "block";
            var url_validate = $("#path_validate")[0].innerHTML.trim();
            const epayco_publickey = $("input:text[name=woocommerce_woo-epaycosubscription_apiKey]").val().replace(/\s/g,"");
            const epayco_privatey = $("input:text[name=woocommerce_woo-epaycosubscription_privateKey]").val().replace(/\s/g,"");
            if (epayco_publickey !== "" &&
                epayco_privatey !== "") {
                var formData = new FormData();
                formData.append("epayco_publickey",epayco_publickey.replace(/\s/g,""));
                formData.append("epayco_privatey",epayco_privatey.replace(/\s/g,""));
                $.ajax({
                    url: url_validate,
                    type: "post",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);
                            if (data.success){
                                if(data.comercio_estado){
                                    document.getElementById("woocommerce_woo-epaycosubscription_shop_name").value=data.comercio;
                                    document.getElementById("woocommerce_woo-epaycosubscription_shop_icon").value=data.logo;
                                    alert("validacion exitosa!");
                                }else{
                                    alert("comercio inactivo, por favor contacte con soporte!");
                                }
                                modal.style.display = "none";
                                modalContent.style.display = "none";
                            } else {
                                loader.style.display = "none";
                                modalContent.style.display = "block";
                            }
                        } catch (error) {
                             modalContent.style.display = "block";
                             loader.style.display = "none";
                        }
                        
                    }
                });
            }
        });
    })(jQuery)
}));
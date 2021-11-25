jQuery( function( $ ) {

    const loadoverlay_ = document.getElementById('loadoverlay')
    loadoverlay_.style.display='none'

    function alertar(){
    try {
        var string = CryptoJS.AES.encrypt(CryptoJS.enc.Utf8.parse('traysads'), 'secrasdasdaset').toString();
            console.log(string)
            } catch (error) {
            console.log(error);
            location.reload();
        }
    }
    $(document).ready(function(){
        alertar()
        var url = "https://restcountries.com/v3.1/alpha/"+$("#result")[0].innerText;
        divFoo = document.getElementById('foo');
        divSample = document.getElementById('countryName');
        divResult = document.getElementById('result');
        divFlag = document.getElementById('flag');
        $.getJSON(url, function(result){
            $.each(result, function(i, field){
                divSample.innerText = field.name.common;
                divSample.id = field.altSpellings[0];
                divFlag.innerText = field.flag;
            });
        });

        $.getJSON("https://restcountries.com/v3.1/all", function(result){
            $.each(result, function(i, field){
                newlink = document.createElement('a');
                li = document.createElement('li');
                img = document.createElement('img');
                img.style.width = "23px";
                img.style.float = "left";
                img.style.marginTop = "8px";
                img.style.marginRight = "10px";
                img.src = field.flags.png;
                newlink.text = field.name.common;
                newlink.href = '#';
                newlink.className = field.flag;
                newlink.id = field.altSpellings[0];
                divFoo.appendChild(li).appendChild(newlink).appendChild(img);
            });
        });

    });

    $(".dropdown a").click(function() {
        $(".dropdown dd ul").toggle();
    });

    $(".dropdown dd ul").click(function(e) {
        var $clicked = $(e.target);
        var texts = $clicked[0].innerText;
        var id = $clicked[0].id;
        var flag = $clicked[0].className;
        divSample.innerText = texts;
        divSample.id = id;
        divFlag.innerText = flag;
        $(".dropdown dd ul").toggle();
    });

    if( $("#lang_epayco").text() == 'en')
    {
    document.getElementById('esButton').classList.remove('bgcolor')
    document.getElementById('esButton').classList.remove('active')
    $("#info_es").hide();
    $("#pagar_es").hide();
    $("#info_en").show();
    $("#pagar_en").show();
    
    document.getElementById('enButton').classList.add('bgcolor')
    document.getElementById('enButton').classList.add('active')
    }else{
        document.getElementById('enButton').classList.remove('bgcolor')
        document.getElementById('enButton').classList.remove('active')
        $("#info_en").hide();
        $("#pagar_en").hide();
        $("#info_es").show();
        $("#pagar_es").show();
        document.getElementById('esButton').classList.add('bgcolor')
        document.getElementById('esButton').classList.add('active')
    }

    function load() {
        const modal = document.getElementById('centered')
        openModal(modal)
    }

    var myVar = setTimeout(load, 2000);
    const openModalButtons = document.querySelectorAll('[data-modal-target]')
    const closeModalButtons = document.querySelectorAll('[data-close-button]')
    const esModalButtons = document.querySelectorAll('[data-es-button]')
    const enModalButtons = document.querySelectorAll('[data-en-button]')
    const overlay = document.getElementById('overlay')
    var info_lenguage = document.getElementById("info_lenguage");

    openModalButtons.forEach(button => {
        button.addEventListener('click', () => {
            const modal = document.querySelector(button.dataset.modalTarget)
            openModal(modal)
        })
    })

    overlay.addEventListener('click', (button) =>{
        const modals = button.closest('.centered.active')
        modals.forEach(modal =>{
            closeModal(modal)
        })
    })

    closeModalButtons.forEach(button => {
        button.addEventListener('click', () => {
            const modal = button.closest('.centered')
            closeModal(modal)
        })
    })

    esModalButtons.forEach(button => {
        button.addEventListener('click', () => {
        document.getElementById('enButton').classList.remove('bgcolor')
        document.getElementById('enButton').classList.remove('active')
        $("#info_en").hide();
        $("#pagar_en").hide();
        $("#info_es").show();
        $("#pagar_es").show();
        document.getElementById('esButton').classList.add('bgcolor')
        document.getElementById('esButton').classList.add('active')
        })
    })

    enModalButtons.forEach(button => {
        button.addEventListener('click', () => {

            document.getElementById('esButton').classList.remove('bgcolor')
            document.getElementById('esButton').classList.remove('active')
            $("#info_es").hide();
            $("#pagar_es").hide();
            $("#info_en").show();
            $("#pagar_en").show();
            document.getElementById('enButton').classList.add('bgcolor')
            document.getElementById('enButton').classList.add('active')
        })
    })

    function openModal(modal) {
        if(modal == null) return
        modal.classList.add('active')
        overlay.classList.add('active')
    }

    function closeModal(modal) {
        if(modal == null) return
        modal.classList.remove('active')
        overlay.classList.remove('active')
    }
    $('#send-form').click(function(){
        $('#token-credit').submit();
    });

    const $checkout_form = $( '#token-credit' );
        $checkout_form.on('submit', function (event) {  
        event.preventDefault();
        var contador = 0;
        contador++;
        var key = $("#p_c").text();
        var key_p = $("#p_p").text();
        var lang = $("#lang_epayco").text();
        
                ePayco.setPublicKey(key);
    
                ePayco.setLanguage(lang);
                var $form = $(this);
                function getPosts() {
                   
                    return  new Promise(function(resolve, reject) {
                    
                        ePayco.token.create($form, function(error, token) {
                            
                            if(!error) {
                                console.log(token)
                                resolve(token)
                                } else {
                                    
                                    if(!error) {
                                        resolve(token)
                                        } else {
                                        if(lang=="en"){
                                            let atributte_info = error.replace('The format is incorrect or the field is empty:', '');
                                            if(atributte_info.trim() == 'number'){
                                                $("#web-checkout-content").addClass("animated shake");
                                                document.getElementById('the-card-number-element').classList.add('inputerror')
                                                reject('credit card number incorrect or empty')
                                            }
                                            
                                        }else{
                                            try {
                                                 if(contador<2)
                                                    { 
                                                        reject('No se pudo realizar el pago, por favor reintente neuvamente')
                                                    }else {
                                                        loadoverlay_.style.display='none';
                                                        alert('No se pudo realizar el pago, por favor reintente neuvamente')
                                                    }
                                                  
                                                } catch(e) {
                                                    loadoverlay_.style.display='none';
                                                    alert('No se pudo realizar el pago, por favor reintente neuvamente')
                                                }
                                        }
                                    
                                    }
                                }
                            });
                        });
                }
        
        var name = document.getElementById('the-card-name-element').value.replace(/[ -]/g, "").length;
        var number = document.getElementById('the-card-number-element').value.replace(/[ -]/g, "").length;
        var month = document.getElementById('month-value').value.replace(/[ -]/g, "").length;
        var year = document.getElementById('year-value').value.replace(/[ -]/g, "").length;
        var cvc = document.getElementById('card_cvc').value.replace(/[ -]/g, "").length;
        $("#web-checkout-content").removeClass("animated shake");
        if( number <= 14 || name <= 5 || month < 1 || year < 2 || cvc < 3 ){
            $("#web-checkout-content").addClass("animated shake");
            if( number <= 14){
                document.getElementById('the-card-number-element').classList.add('inputerror')
            }
            if( name <= 5){
                document.getElementById('the-card-name-element').classList.add('inputerror')
            }
            if( month < 1 ){
                document.getElementById('expiration').classList.add('inputerror')
            }
            if( year < 2 ){
                document.getElementById('expiration').classList.add('inputerror')
            }
            if( cvc < 3 ){
                document.getElementById('cvc_').classList.add('inputerror')
            }
            
        }else{
            const content = document.getElementById('web-checkout-content')
            loadoverlay_.style.display='block';
            getPosts().then(r =>{
                console.log('ready!',r);
                $checkout_form.find('input[name=my-custom-form-field__card-number]').remove();
                    $checkout_form.find('input[name=cvc]').remove();
                    $checkout_form.find('input[name=year]').remove();
                    $checkout_form.find('input[name=month]').remove();
                    $checkout_form.find('input[name=card_email]').remove();
                    $checkout_form.find('input[name=card_number]').remove();
                var form = document.getElementById('token-credit');
                var hiddenInput = document.createElement('input');
                //
                let token = r;
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'epaycoToken');
                hiddenInput.setAttribute('value', r);
                form.appendChild(hiddenInput);
                form.submit();
            }).catch((e) => {
                if(contador<2)
                {
                    contador++;
                    getPosts()
                    
                }else
                {
                  console.log('Algo saliò mal!');
                  loadoverlay_.style.display='none';
                  alert(e)
                }
            });
        }       
            
    });

});
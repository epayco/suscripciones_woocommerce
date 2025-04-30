document.addEventListener('DOMContentLoaded', function () {
    const cardInput = document.getElementById('the-card-number2-element');
    const cardLogo = document.getElementById('logo_franchise');
  
    if (cardInput) {
      cardInput.addEventListener('input', function () {
        const cardNumber = this.value.replace(/\s+/g, ''); // Eliminar espacios
  
        // Formatear el número de tarjeta para mostrar un espacio cada 4 dígitos
        cardInput.value = cardNumber.replace(/(\d{4})(?=\d)/g, '$1 ');
  
        // Detectar el tipo de tarjeta y actualizar el ícono
        let isCardDetected = false; // Variable para saber si hay una tarjeta detectada
        if (/^4[0-9]{6,}$/.test(cardNumber)) {
          cardLogo.src = 'https://msecure.epayco.co/img/credit-cards/vs.png'; // Ruta de la imagen de Visa
          isCardDetected = true;
        } else if (/^5[1-5][0-9]{5,}$/.test(cardNumber)) {
          cardLogo.src = 'https://msecure.epayco.co/img/credit-cards/mc.png'; // Ruta de la imagen de Mastercard
          isCardDetected = true;
        } else if (/^3[47][0-9]{3,}$/.test(cardNumber)) {
          cardLogo.src = 'https://msecure.epayco.co/img/credit-cards/amex.png'; // Ruta de la imagen de Amex
          isCardDetected = true;
        } else if (/^6(?:011|5[0-9]{2})[0-9]{3,}$/.test(cardNumber)) {
          cardLogo.src = 'https://msecure.epayco.co/img/credit-cards/discover.png'; // Ruta de la imagen de Discover
          isCardDetected = true;
        } else {
          cardLogo.src = 'https://msecure.epayco.co/img/credit-cards/disable.png'; // Ruta de la imagen deshabilitada
        }
  
        // Mostrar el logo si se detectó la tarjeta
        cardLogo.style.display = isCardDetected ? 'block' : 'none';
  
        // Agregar o quitar la clase para ajustar el padding
        if (isCardDetected) {
          cardInput.classList.add('with-logo'); // Agrega padding-left extra
        } else {
          cardInput.classList.remove('with-logo'); // Restablece el padding-left
        }
      });
    }
  });

  //Diseño desktop 
  document.addEventListener('DOMContentLoaded', function () {
    const cardInput = document.getElementById('the-card-number-element');
    const cardLogo = document.getElementById('logo_franchise_2');
  
    if (cardInput) {
      cardInput.addEventListener('input', function () {
        const cardNumber = this.value.replace(/\s+/g, ''); // Eliminar espacios
  
        // Formatear el número de tarjeta para mostrar un espacio cada 4 dígitos
        cardInput.value = cardNumber.replace(/(\d{4})(?=\d)/g, '$1 ');
  
        // Detectar el tipo de tarjeta y actualizar el ícono
        let isCardDetected = false; // Variable para saber si hay una tarjeta detectada
        if (/^4[0-9]{6,}$/.test(cardNumber)) {
          cardLogo.src = 'https://msecure.epayco.co/img/credit-cards/vs.png'; // Ruta de la imagen de Visa
          isCardDetected = true;
        } else if (/^5[1-5][0-9]{5,}$/.test(cardNumber)) {
          cardLogo.src = 'https://msecure.epayco.co/img/credit-cards/mc.png'; // Ruta de la imagen de Mastercard
          isCardDetected = true;
        } else if (/^3[47][0-9]{3,}$/.test(cardNumber)) {
          cardLogo.src = 'https://msecure.epayco.co/img/credit-cards/amex.png'; // Ruta de la imagen de Amex
          isCardDetected = true;
        } else if (/^6(?:011|5[0-9]{2})[0-9]{3,}$/.test(cardNumber)) {
          cardLogo.src = 'https://msecure.epayco.co/img/credit-cards/discover.png'; // Ruta de la imagen de Discover
          isCardDetected = true;
        } else {
          cardLogo.src = 'https://msecure.epayco.co/img/credit-cards/disable.png'; // Ruta de la imagen deshabilitada
        }
  
        // Mostrar el logo si se detectó la tarjeta
        cardLogo.style.display = isCardDetected ? 'block' : 'none';
  
        // Agregar o quitar la clase para ajustar el padding
        if (isCardDetected) {
          cardInput.classList.add('with-logo'); // Agrega padding-left extra
        } else {
          cardInput.classList.remove('with-logo'); // Restablece el padding-left
        }
      });
    }
  });

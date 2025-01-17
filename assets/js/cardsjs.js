function CardJs2(elem) {
    (this.elem = jQuery(elem)),
        (this.captureName = this.elem.data("capture-name") ? this.elem.data("capture-name") : !1),
        (this.iconColour = this.elem.data("icon-colour") ? this.elem.data("icon-colour") : !1),
        (this.stripe = this.elem.data("stripe") ? this.elem.data("stripe") : !1),
        this.stripe && (this.captureName = !1),
        this.initNameInput(),
        this.initCardNumberInput(),
        this.elem.empty(),
        this.setupCardNumberInput(),
        this.iconColour && this.setIconColour(this.iconColour),
        this.refreshCreditCardTypeIcon();
}
!(function ($) {
    var methods = {
        init: function () {
            return this.data("cardjss", new CardJs2(this)), this;
        },
        cardNumber: function () {
            return this.data("cardjss").getCardNumber();
        },
        cardType: function () {
            return this.data("cardjss").getCardType();
        },
        name: function () {
            return this.data("cardjss").getName();
        },
    };
    $.fn.CardJs2 = function (methodOrOptions) {
        return methods[methodOrOptions]
            ? methods[methodOrOptions].apply(this, Array.prototype.slice.call(arguments, 1))
            : "object" != typeof methodOrOptions && methodOrOptions
                ? void $.error("Method " + methodOrOptions + " does not exist on jQuery.CardJs")
                : methods.init.apply(this, arguments);
    };
})(jQuery),
    $(function () {
        $(".card-jss").each(function (i, obj) {
            $(obj).CardJs2();
        });
    }),
    (CardJs2.prototype.constructor = CardJs2),
    (CardJs2.KEYS = { 0: 48, 9: 57, NUMPAD_0: 96, NUMPAD_9: 105, DELETE: 46, BACKSPACE: 8, ARROW_LEFT: 37, ARROW_RIGHT: 39, ARROW_UP: 38, ARROW_DOWN: 40, HOME: 36, END: 35, TAB: 9, A: 65, X: 88, C: 67, V: 86 }),
    (CardJs2.CREDIT_CARD_NUMBER_DEFAULT_MASK = "XXXX XXXX XXXX XXXX"),
    (CardJs2.CREDIT_CARD_NUMBER_VISA_MASK = "XXXX XXXX XXXX XXXX"),
    (CardJs2.CREDIT_CARD_NUMBER_MASTERCARD_MASK = "XXXX XXXX XXXX XXXX"),
    (CardJs2.CREDIT_CARD_NUMBER_DISCOVER_MASK = "XXXX XXXX XXXX XXXX"),
    (CardJs2.CREDIT_CARD_NUMBER_JCB_MASK = "XXXX XXXX XXXX XXXX"),
    (CardJs2.CREDIT_CARD_NUMBER_AMEX_MASK = "XXXX XXXXXX XXXXX"),
    (CardJs2.CREDIT_CARD_NUMBER_DINERS_MASK = "XXXX XXXX XXXX XX"),
    (CardJs2.prototype.creditCardNumberMask = CardJs2.CREDIT_CARD_NUMBER_DEFAULT_MASK),
    (CardJs2.CREDIT_CARD_NUMBER_PLACEHOLDER = "**** **** **** ****"),
    (CardJs2.logo_franchise = document.getElementById('logo_franchise')),
    (CardJs2.CREDIT_CARD_SVG =
        '<svg class="svg-inline--fa fa-credit-card fa-w-18 icon" aria-hidden="true" data-prefix="fa" data-icon="credit-card" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M0 432c0 26.5 21.5 48 48 48h480c26.5 0 48-21.5 48-48V256H0v176zm192-68c0-6.6 5.4-12 12-12h136c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12H204c-6.6 0-12-5.4-12-12v-40zm-128 0c0-6.6 5.4-12 12-12h72c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12H76c-6.6 0-12-5.4-12-12v-40zM576 80v48H0V80c0-26.5 21.5-48 48-48h480c26.5 0 48 21.5 48 48z"></path></svg>'),
    (CardJs2.keyCodeFromEvent = function (e) {
        return e.which || e.keyCode;
    }),
    (CardJs2.keyIsCommandFromEvent = function (e) {
        return e.ctrlKey || e.metaKey;
    }),
    (CardJs2.keyIsNumber = function (e) {
        return CardJs2.keyIsTopNumber(e) || CardJs2.keyIsKeypadNumber(e);
    }),
    (CardJs2.keyIsTopNumber = function (e) {
        var keyCode = CardJs2.keyCodeFromEvent(e);
        return keyCode >= CardJs2.KEYS[0] && keyCode <= CardJs2.KEYS[9];
    }),
    (CardJs2.keyIsKeypadNumber = function (e) {
        var keyCode = CardJs2.keyCodeFromEvent(e);
        return keyCode >= CardJs2.KEYS.NUMPAD_0 && keyCode <= CardJs2.KEYS.NUMPAD_9;
    }),
    (CardJs2.keyIsDelete = function (e) {
        return CardJs2.keyCodeFromEvent(e) == CardJs2.KEYS.DELETE;
    }),
    (CardJs2.keyIsBackspace = function (e) {
        return CardJs2.keyCodeFromEvent(e) == CardJs2.KEYS.BACKSPACE;
    }),
    (CardJs2.keyIsDeletion = function (e) {
        return CardJs2.keyIsDelete(e) || CardJs2.keyIsBackspace(e);
    }),
    (CardJs2.keyIsArrow = function (e) {
        var keyCode = CardJs2.keyCodeFromEvent(e);
        return keyCode >= CardJs2.KEYS.ARROW_LEFT && keyCode <= CardJs2.KEYS.ARROW_DOWN;
    }),
    (CardJs2.keyIsNavigation = function (e) {
        var keyCode = CardJs2.keyCodeFromEvent(e);
        return keyCode == CardJs2.KEYS.HOME || keyCode == CardJs2.KEYS.END;
    }),
    (CardJs2.keyIsKeyboardCommand = function (e) {
        var keyCode = CardJs2.keyCodeFromEvent(e);
        return CardJs2.keyIsCommandFromEvent(e) && (keyCode == CardJs2.KEYS.A || keyCode == CardJs2.KEYS.X || keyCode == CardJs2.KEYS.C || keyCode == CardJs2.KEYS.V);
    }),
    (CardJs2.keyIsTab = function (e) {
        return CardJs2.keyCodeFromEvent(e) == CardJs2.KEYS.TAB;
    }),
    (CardJs2.copyAllElementAttributes = function (source, destination) {
        $.each(source[0].attributes, function (idx, attr) {
            destination.attr(attr.nodeName, attr.nodeValue);
        });
    }),
    (CardJs2.numbersOnlyString = function (string) {
        for (var numbersOnlyString = "", i = 0; i < string.length; i++) {
            var currentChar = string.charAt(i),
                isValid = !isNaN(parseInt(currentChar));
            isValid && (numbersOnlyString += currentChar);
        }
        return numbersOnlyString;
    }),
    (CardJs2.applyFormatMask = function (string, mask) {
        for (var formattedString = "", numberPos = 0, j = 0; j < mask.length; j++) {
            var currentMaskChar = mask[j];
            if ("X" == currentMaskChar) {
                var digit = string.charAt(numberPos);
                if (!digit) break;
                (formattedString += string.charAt(numberPos)), numberPos++;
            } else formattedString += currentMaskChar;
        }
        return formattedString;
    }),
    (CardJs2.cardTypeFromNumber = function (number) {
        if (((re = new RegExp("^30[0-5]")), null != number.match(re))) return "Diners - Carte Blanche";
        if (((re = new RegExp("^(30[6-9]|36|38)")), null != number.match(re))) return "Diners";
        if (((re = new RegExp("^35(2[89]|[3-8][0-9])")), null != number.match(re))) return "JCB";
        if (((re = new RegExp("^3[47]")), null != number.match(re))) return "AMEX";
        if (((re = new RegExp("^(4026|417500|4508|4844|491(3|7))")), null != number.match(re))) return "Visa Electron";
        var re = new RegExp("^4");
        return null != number.match(re)
            ? "Visa"
            : ((re = new RegExp("^5[1-5]")), null != number.match(re) ? "Mastercard" : ((re = new RegExp("^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)")), null != number.match(re) ? "Discover" : ""));
    }),
    (CardJs2.caretStartPosition = function (element) {
        return "number" == typeof element.selectionStart ? element.selectionStart : !1;
    }),
    (CardJs2.caretEndPosition = function (element) {
        return "number" == typeof element.selectionEnd ? element.selectionEnd : !1;
    }),
    (CardJs2.setCaretPosition = function (element, caretPos) {
        if (null != element)
            if (element.createTextRange) {
                var range = element.createTextRange();
                range.move("character", caretPos), range.select();
            } else element.selectionStart ? (element.focus(), element.setSelectionRange(caretPos, caretPos)) : element.focus();
    }),
    (CardJs2.normaliseCaretPosition = function (mask, caretPosition) {
        var numberPos = 0;
        if (0 > caretPosition || caretPosition > mask.length) return 0;
        for (var i = 0; i < mask.length; i++) {
            if (i == caretPosition) return numberPos;
            "X" == mask[i] && numberPos++;
        }
        return numberPos;
    }),
    (CardJs2.denormaliseCaretPosition = function (mask, caretPosition) {
        var numberPos = 0;
        if (0 > caretPosition || caretPosition > mask.length) return 0;
        for (var i = 0; i < mask.length; i++) {
            if (numberPos == caretPosition) return i;
            "X" == mask[i] && numberPos++;
        }
        return mask.length;
    }),
    (CardJs2.filterNumberOnlyKey = function (e) {
        var isNumber = CardJs2.keyIsNumber(e),
            isDeletion = CardJs2.keyIsDeletion(e),
            isArrow = CardJs2.keyIsArrow(e),
            isNavigation = CardJs2.keyIsNavigation(e),
            isKeyboardCommand = CardJs2.keyIsKeyboardCommand(e),
            isTab = CardJs2.keyIsTab(e);
        isNumber || isDeletion || isArrow || isNavigation || isKeyboardCommand || isTab || e.preventDefault();
    }),
    (CardJs2.digitFromKeyCode = function (keyCode) {
        return keyCode >= CardJs2.KEYS[0] && keyCode <= CardJs2.KEYS[9] ? keyCode - CardJs2.KEYS[0] : keyCode >= CardJs2.KEYS.NUMPAD_0 && keyCode <= CardJs2.KEYS.NUMPAD_9 ? keyCode - CardJs2.KEYS.NUMPAD_0 : null;
    }),
    (CardJs2.handleMaskedNumberInputKey = function (e, mask) {
        CardJs2.filterNumberOnlyKey(e);
        var keyCode = e.which || e.keyCode,
            element = e.target,
            caretStart = CardJs2.caretStartPosition(element),
            caretEnd = CardJs2.caretEndPosition(element),
            normalisedStartCaretPosition = CardJs2.normaliseCaretPosition(mask, caretStart),
            normalisedEndCaretPosition = CardJs2.normaliseCaretPosition(mask, caretEnd),
            newCaretPosition = caretStart,
            isNumber = CardJs2.keyIsNumber(e),
            isDelete = CardJs2.keyIsDelete(e),
            isBackspace = CardJs2.keyIsBackspace(e);
        if (isNumber || isDelete || isBackspace) {
            e.preventDefault();

            var rawText = $(element).val(),
                numbersOnly = CardJs2.numbersOnlyString(rawText),
                digit = CardJs2.digitFromKeyCode(keyCode),
                rangeHighlighted = normalisedEndCaretPosition > normalisedStartCaretPosition;

            rangeHighlighted && (numbersOnly = numbersOnly.slice(0, normalisedStartCaretPosition) + numbersOnly.slice(normalisedEndCaretPosition)),
                caretStart != mask.length &&
                (isNumber &&
                    rawText.length <= mask.length &&
                    ((numbersOnly = numbersOnly.slice(0, normalisedStartCaretPosition) + digit + numbersOnly.slice(normalisedStartCaretPosition)),
                        (newCaretPosition = Math.max(CardJs2.denormaliseCaretPosition(mask, normalisedStartCaretPosition + 1), CardJs2.denormaliseCaretPosition(mask, normalisedStartCaretPosition + 2) - 1))),
                    isDelete && (numbersOnly = numbersOnly.slice(0, normalisedStartCaretPosition) + numbersOnly.slice(normalisedStartCaretPosition + 1))),
                0 != caretStart &&
                isBackspace &&
                !rangeHighlighted &&
                ((numbersOnly = numbersOnly.slice(0, normalisedStartCaretPosition - 1) + numbersOnly.slice(normalisedStartCaretPosition)), (newCaretPosition = CardJs2.denormaliseCaretPosition(mask, normalisedStartCaretPosition - 1))),
                $(element).val(CardJs2.applyFormatMask(numbersOnly, mask)),
                CardJs2.setCaretPosition(element, newCaretPosition);
        }
    }),
    (CardJs2.handleCreditCardNumberKey = function (e, cardMask) {
        CardJs2.handleMaskedNumberInputKey(e, cardMask);
    }),
    (CardJs2.handleCreditCardNumberChange = function (e) { }),
    (CardJs2.prototype.getCardNumber = function () {
        return this.cardNumberInput.val();
    }),
    (CardJs2.prototype.getCardType = function () {
        return CardJs2.cardTypeFromNumber(this.getCardNumber());
    }),
    (CardJs2.prototype.getName = function () {
        return this.nameInput.val();
    }),
    (CardJs2.prototype.setIconColour = function (colour) {
        this.elem.find(".icon .svg").css({ fill: colour });
    }),
    (CardJs2.prototype.setIconColour = function (colour) {
        this.elem.find(".icon .svg").css({ fill: colour });
    }),
    (CardJs2.prototype.refreshCreditCardTypeIcon = function () {
        this.setCardTypeIconFromNumber(CardJs2.numbersOnlyString(this.cardNumberInput.val()));
    }),
    (CardJs2.prototype.refreshCreditCardNumberFormat = function () {
        var numbersOnly = CardJs2.numbersOnlyString($(this.cardNumberInput).val()),
            formattedNumber = CardJs2.applyFormatMask(numbersOnly, this.creditCardNumberMask);
        $(this.cardNumberInput).val(formattedNumber);
    }),
    (CardJs2.prototype.setCardTypeIconFromNumber = function (number) {
        switch (CardJs2.cardTypeFromNumber(number)) {
            case "Visa Electron":
            case "Visa":
                this.setCardTypeAsVisa();
                break;
            case "Mastercard":
                this.setCardTypeAsMasterCard();
                break;
            case "AMEX":
                this.setCardTypeAsAmericanExpress();
                break;
            case "Discover":
                this.setCardTypeAsDiscover();
                break;
            case "Diners - Carte Blanche":
            case "Diners":
                this.setCardTypeAsDiners();
                break;
            case "JCB":
                this.setCardTypeAsJcb();
                break;
            default:
                this.clearCardType();
        }
    }),
    (CardJs2.prototype.setCardMask = function (cardMask) {
        (this.creditCardNumberMask = cardMask), this.cardNumberInput.attr("maxlength", cardMask.length);
    }),
    (CardJs2.prototype.clearCardTypeIcon = function () {
        CardJs2.logo_franchise.hidden = false;

        let logo = document.getElementById('franchise_logo');
        let input = document.getElementById('the-card-number2-element');
        if (input.value < 1) {
            logo.className = 'card-type-icon';
        }

        this.elem.find(".card-number2-wrapper .card-type-icon").removeClass("show");
    }),
    (CardJs2.prototype.setCardTypeIconAsVisa = function () {
        CardJs2.logo_franchise.hidden = true;
        this.elem.find(".card-number2-wrapper .card-type-icon").attr("class", "card-type-icon show visa");
    }),
    (CardJs2.prototype.setCardTypeIconAsMasterCard = function () {
        CardJs2.logo_franchise.hidden = true;
        this.elem.find(".card-number2-wrapper .card-type-icon").attr("class", "card-type-icon show master-card");
    }),
    (CardJs2.prototype.setCardTypeIconAsAmericanExpress = function () {
        CardJs2.logo_franchise.hidden = true;
        this.elem.find(".card-number2-wrapper .card-type-icon").attr("class", "card-type-icon show american-express");
    }),
    (CardJs2.prototype.setCardTypeIconAsDiscover = function () {
        CardJs2.logo_franchise.hidden = true;
        this.elem.find(".card-number2-wrapper .card-type-icon").attr("class", "card-type-icon show discover");
    }),
    (CardJs2.prototype.setCardTypeIconAsDiners = function () {
        CardJs2.logo_franchise.hidden = true;
        this.elem.find(".card-number2-wrapper .card-type-icon").attr("class", "card-type-icon show diners");
    }),
    (CardJs2.prototype.setCardTypeIconAsJcb = function () {
        CardJs2.logo_franchise.hidden = true;
        this.elem.find(".card-number2-wrapper .card-type-icon").attr("class", "card-type-icon show jcb");
    }),
    (CardJs2.prototype.clearCardType = function () {
        this.clearCardTypeIcon(), this.setCardMask(CardJs2.CREDIT_CARD_NUMBER_DEFAULT_MASK);
    }),
    (CardJs2.prototype.setCardTypeAsVisa = function () {
        this.setCardTypeIconAsVisa(), this.setCardMask(CardJs2.CREDIT_CARD_NUMBER_VISA_MASK);
    }),
    (CardJs2.prototype.setCardTypeAsMasterCard = function () {
        this.setCardTypeIconAsMasterCard(), this.setCardMask(CardJs2.CREDIT_CARD_NUMBER_MASTERCARD_MASK);
    }),
    (CardJs2.prototype.setCardTypeAsAmericanExpress = function () {
        this.setCardTypeIconAsAmericanExpress(), this.setCardMask(CardJs2.CREDIT_CARD_NUMBER_AMEX_MASK);
    }),
    (CardJs2.prototype.setCardTypeAsDiscover = function () {
        this.setCardTypeIconAsDiscover(), this.setCardMask(CardJs2.CREDIT_CARD_NUMBER_DISCOVER_MASK);
    }),
    (CardJs2.prototype.setCardTypeAsDiners = function () {
        this.setCardTypeIconAsDiners(), this.setCardMask(CardJs2.CREDIT_CARD_NUMBER_DINERS_MASK);
    }),
    (CardJs2.prototype.setCardTypeAsJcb = function () {
        this.setCardTypeIconAsJcb(), this.setCardMask(CardJs2.CREDIT_CARD_NUMBER_JCB_MASK);
    }),
    (CardJs2.prototype.initCardNumberInput = function () {
        (this.cardNumberInput = CardJs2.detachOrCreateElement(this.elem, ".card-number2", "<input class='card-number2' />")),
            CardJs2.elementHasAttribute(this.cardNumberInput, "name") || this.cardNumberInput.attr("name", "card-number2"),
            CardJs2.elementHasAttribute(this.cardNumberInput, "placeholder") || this.cardNumberInput.attr("placeholder", CardJs2.CREDIT_CARD_NUMBER_PLACEHOLDER),
            this.cardNumberInput.attr("type", "tel"),
            this.cardNumberInput.attr("maxlength", this.creditCardNumberMask.length),
            this.cardNumberInput.attr("x-autocompletetype", "cc-number"),
            this.cardNumberInput.attr("autocompletetype", "cc-number"),
            this.cardNumberInput.attr("autocorrect", "off"),
            this.cardNumberInput.attr("spellcheck", "off"),
            this.cardNumberInput.attr("autocapitalize", "off");
        var $this = this;
        this.cardNumberInput.keydown(function (e) {
            CardJs2.handleCreditCardNumberKey(e, $this.creditCardNumberMask);
        }),
            this.cardNumberInput.keyup(function () {
                $this.refreshCreditCardTypeIcon();
            }),
            this.cardNumberInput.on("paste", function () {
                setTimeout(function () {
                    $this.refreshCreditCardNumberFormat(), $this.refreshCreditCardTypeIcon();
                }, 1);
            });
    }),
    (CardJs2.prototype.initNameInput = function () {
        (this.captureName = null != this.elem.find(".name")[0]),
            (this.nameInput = CardJs2.detachOrCreateElement(this.elem, ".name", "<input class='name' />")),
            CardJs2.elementHasAttribute(this.nameInput, "name") || this.nameInput.attr("name", "card-number2"),
            CardJs2.elementHasAttribute(this.nameInput, "placeholder") || this.nameInput.attr("placeholder", CardJs2.NAME_PLACEHOLDER);
    }),
    (CardJs2.prototype.setupCardNumberInput = function () {
        this.stripe && this.cardNumberInput.attr("data-stripe", "number"), this.elem.append("<div class='card-number2-wrapper'></div>");
        var wrapper = this.elem.find(".card-number2-wrapper");
        wrapper.append(this.cardNumberInput), wrapper.append("<div class='card-type-icon'  id='franchise_logo'></div>"), wrapper.append("<div class='icon'></div>"), wrapper.find(".icon");
    }),
    (CardJs2.elementHasAttribute = function (element, attributeName) {
        var attr = $(element).attr(attributeName);
        return "undefined" != typeof attr && attr !== !1;
    }),
    (CardJs2.detachOrCreateElement = function (parentElement, selector, html) {
        var element = parentElement.find(selector);
        return element[0] ? element.detach() : (element = $(html)), element;
    });
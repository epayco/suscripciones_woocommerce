(function () {
    var $tag,
        Base64,
        antifraud_config,
        base_url,
        getAntifraudConfig,
        localStorageGet,
        localStorageSet,
        public_key,
        session_id,
        setPublicKey,
        setLanguage,
        _language,
        debug,
        alerts;
    languages = new Array();
    base_url = "https://api.secure.epayco.co/";
    session_id = "";
    //_language = "es";

    debug = true;
    alerts = true;
    antifraud_config = {};
    languages["es"] = {
        errors: [
            {
                type: 101,
                title: "[101] Datos ilegibles",
                description:
                    "Los Datos son ilegibles compruebe la integridad del formulario",
            },
            {
                type: 102,
                title: "[102] Error publicKey",
                description:
                    "La publicKey es ilegible o no se tiene acceso, por favor compruebe",
            },
            {
                type: 103,
                title: "[103] Campo errÃ³neo o vacÃ­o",
                description: "El formato es incorrecto o esta vacÃ­o en:",
            },
        ],
    };
    languages["en"] = {
        errors: [
            {
                type: 101,
                title: "[101] Illegible data",
                description: "The Data is illegible check the integrity of the form",
            },
            {
                type: 102,
                title: "[102] Error publicKey",
                description:
                    "The publicKey is unreadable or not accessible, please check",
            },
            {
                type: 103,
                title: "[103] Bad or empty field",
                description: "The format is incorrect or the field is empty:",
            },
        ],
    };

    localStorageGet = function (key, value) {
        var error;
        if (
            typeof localStorage !== "undefined" &&
            typeof localStorage.getItem !== "undefined"
        ) {
            try {
                localStorage.getItem("hashKey", "1");
                localStorage.removeItem("hashKey");
                return localStorage(key, value);
            } catch (_error) {
                error = _error;
                return null;
            }
        } else {
            return null;
        }
    };

    localStorageSet = function (key, value) {
        var error;
        if (
            typeof localStorage !== "undefined" &&
            typeof localStorage.setItem !== "undefined"
        ) {
            try {
                localStorage.setItem("hashKey", "1");
                localStorage.removeItem("hashKey");
                return localStorage(key, value);
            } catch (_error) {
                error = _error;
                return null;
            }
        } else {
            return null;
        }
    };

    getError = function (type, name, form) {
        $(form).find("button").prop("disabled", false);
        let languageCheckout = ePayco.getLanguage();
        if (languageCheckout == "es" || languageCheckout == "en") {
            var data = languages[ePayco.getLanguage()].errors,
                typeError = type,
                result;
        } else {
            var data = languages["en"].errors,
                typeError = type,
                result;
        }

        for (var i = 0; i < data.length; i++) {
            if (typeError == data[i].type) {
                result = data[i];
            }
        }

        if (name) {
            var org = result.description.slice(0, 47);
            result.description = org + " " + name;
        }

        if (debug) console.log(result);

        if (alerts) {
            alert(result.description);
        }
        return result;
    };

    dump = function (data) {
        return data;
    };

    public_key = localStorageGet("epayco_publish_key");
    _language = localStorageGet("epayco_language");
    if (!window.ePayco) {
        window.ePayco = {
            setPublicKey: function (key) {
                if (typeof key === "string") {
                    public_key = key;
                    localStorageSet("epayco_publish_key", public_key);
                } else {
                    getError(102);
                }
            },
            setLanguage: function (key) {
                if (typeof key === "string") {
                    _language = key;
                    localStorageSet("epayco_language", _language);
                } else {
                    getError(102);
                }
            },
            getPublicKey: function () {
                return public_key;
            },
            getLanguage: function () {
                return _language;
            },
            _errors: {
                alert: function (error) {
                    alert(error);
                },
            },
            _utils: {
                objectKeys: function (obj) {
                    var keys, p;
                    keys = [];
                    for (p in obj) {
                        if (Object.prototype.hasOwnProperty.call(obj, p)) {
                            keys.push(p);
                        }
                    }
                    return keys;
                },

                parseForm: function (form_object) {
                    var all_inputs,
                        attribute,
                        attribute_name,
                        attributes,
                        input,
                        inputs,
                        json_object,
                        l,
                        last_attribute,
                        len,
                        len1,
                        m,
                        node,
                        o,
                        parent_node,
                        q,
                        r,
                        ref1,
                        ref2,
                        ref3,
                        selects,
                        textareas,
                        val;

                    json_object = {};

                    if (typeof form_object === "object") {
                        if (
                            typeof jQuery !== "undefined" &&
                            (form_object instanceof jQuery || "jquery" in Object(form_object))
                        ) {
                            form_object = form_object.get()[0];
                            if (typeof form_object !== "object") {
                                return {};
                            }
                        }

                        if (form_object.nodeType) {
                            textareas = form_object.getElementsByTagName("textarea");
                            inputs = form_object.getElementsByTagName("input");
                            selects = form_object.getElementsByTagName("select");
                            all_inputs = new Array(
                                textareas.length + inputs.length + selects.length
                            );

                            for (
                                i = l = 0, ref1 = textareas.length - 1;
                                l <= ref1;
                                i = l += 1
                            ) {
                                all_inputs[i] = textareas[i];
                            }

                            for (i = m = 0, ref2 = inputs.length - 1; m <= ref2; i = m += 1) {
                                all_inputs[i + textareas.length] = inputs[i];
                            }

                            for (
                                i = o = 0, ref3 = selects.length - 1;
                                o <= ref3;
                                i = o += 1
                            ) {
                                all_inputs[i + textareas.length + inputs.length] = selects[i];
                            }
                            for (q = 0, len = all_inputs.length; q < len; q++) {
                                input = all_inputs[q];

                                if (input) {
                                    attribute_name = input.getAttribute("data-epayco");
                                    if (attribute_name) {
                                        if (input.tagName === "SELECT") {
                                            val = input.value;
                                        } else {
                                            val =
                                                input.getAttribute("value") ||
                                                input.innerHTML ||
                                                input.value;
                                        }
                                        attributes = attribute_name
                                            .replace(/\]/g, "")
                                            .replace(/\-/g, "_")
                                            .split(/\[/);
                                        parent_node = null;
                                        node = json_object;
                                        last_attribute = null;
                                        for (r = 0, len1 = attributes.length; r < len1; r++) {
                                            attribute = attributes[r];
                                            if (!node[attribute]) {
                                                node[attribute] = {};
                                            }
                                            parent_node = node;
                                            last_attribute = attribute;
                                            node = node[attribute];
                                        }
                                        parent_node[last_attribute] = val;
                                    }
                                }
                            }
                        } else {
                            json_object = form_object;
                        }
                    }
                    return json_object;
                },

                requestUrl: function (params) {
                    var rep, error, success;
                    if (typeof new XMLHttpRequest().withCredentials !== "undefined") {
                    }
                },

                createTokenEncrypt: function (id, payment, callback) {
                    var error = undefined,
                        result = undefined;
                    var key;
                    debugger
                    $.ajax({
                        type: "POST",
                        url: base_url + "token/encrypt",
                        crossDomain: true,
                        dataType: "json",
                        data: {
                            public_key: ePayco.getPublicKey(),
                            session: id,
                        },
                    })
                        .done(function (token) {
                            if (debug) {
                                dump(token);
                            }

                            key = token.data.token;
                            function encrypt(text, secret) {
                                if (text && secret !== "undefined") {
                                    try {
                                        var string = CryptoJS.AES.encrypt(
                                            CryptoJS.enc.Utf8.parse(text),
                                            secret
                                        ).toString();
                                        return string.toString();
                                    } catch (error) {
                                        var string = CryptoJS.AES.encrypt(
                                            CryptoJS.enc.Utf8.parse(text),
                                            CryptoJS.enc.Utf8.parse(secret).toString()
                                        );
                                        return string.toString();
                                    }
                                } else {
                                    console.log("hay algunos valores invalidos");
                                    return;
                                }
                            }
                            function encryptE(value, userKey) {
                                var key = CryptoJS.enc.Hex.parse(userKey),
                                    iv = CryptoJS.enc.Hex.parse(userKey),
                                    text = CryptoJS.AES.encrypt(value, key, {
                                        iv: iv,
                                        mode: CryptoJS.mode.CBC,
                                        padding: CryptoJS.pad.Pkcs7,
                                    });
                                return text.ciphertext.toString(CryptoJS.enc.Base64);
                            }
                            function createCreditCard() {
                                var encryptData = [];
                                for (var i = 0; i < payment.customer.length; i++) {
                                    //
                                    encryptData.push({
                                        type: payment.customer[i].type,
                                        value: encrypt(payment.customer[i].value, key),
                                    });
                                }
                                var publicKey = {
                                    type: "publicKey",
                                    value: ePayco.getPublicKey(),
                                };
                                var session = {
                                    type: "session",
                                    value: localStorage.getItem("keyUserIndex"),
                                };
                                encryptData.push(publicKey);
                                encryptData.push(session);
                                return encryptData;
                            }

                            var json = JSON.stringify(createCreditCard());
                            $.ajax({
                                type: "POST",
                                url: base_url + "token/tokenize",
                                crossDomain: true,
                                dataType: "json",
                                data: {
                                    values: json,
                                },
                                error: function () {
                                    console.log("No se ha podido obtener la informaciÃ³n");
                                },
                            })
                                .done(function (done) {
                                    alert(JSON.stringify(done));
                                    if ((done.data.status = "created")) {
                                        callback(done.data.token, null);
                                    } else {
                                        callback(null, done.data);
                                    }
                                })
                                .fail(function (error) {
                                    if (debug) {
                                        dump(error);
                                    }
                                });
                        })

                        .fail(function (error) {
                            if (debug) {
                                dump(error);
                            }
                        });
                },

                createGuid: function () {
                    function s4() {
                        return Math.floor((1 + Math.random()) * 0x10000)
                            .toString(16)
                            .substring(1);
                    }
                    return (
                        s4() +
                        s4() +
                        "-" +
                        s4() +
                        "-" +
                        s4() +
                        "-" +
                        s4() +
                        "-" +
                        s4() +
                        s4() +
                        s4()
                    );
                },

                log: function (data) {
                    if (typeof console !== "undefined" && console.log) {
                        return console.log(data);
                    }
                },
            },
        };
    }
}.call(this));

(function () {
    var accepted_cards,
        card_types,
        get_card_type,
        is_valid_length,
        parseMonth,
        parseYear,
        indexOf =
            [].indexOf ||
            function (item) {
                for (var i = 0, l = this.length; i < l; i++) {
                    if (i in this && this[i] === item) return i;
                }
                return -1;
            };

    card_types = [
        {
            name: "amex",
            pattern: /^3[47]/,
            valid_length: [15],
        },
        {
            name: "diners_club_carte_blanche",
            pattern: /^30[0-5]/,
            valid_length: [14],
        },
        {
            name: "diners_club_international",
            pattern: /^36/,
            valid_length: [14],
        },
        {
            name: "laser",
            pattern: /^(6304|670[69]|6771)/,
            valid_length: [16, 17, 18, 19],
        },
        {
            name: "visa_electron",
            pattern: /^(4026|417500|4508|4844|491(3|7))/,
            valid_length: [16],
        },
        {
            name: "visa",
            pattern: /^4/,
            valid_length: [16],
        },
        {
            name: "mastercard",
            pattern: /^5[1-5]/,
            valid_length: [16],
        },
        {
            name: "maestro",
            pattern: /^(5018|5020|5038|6304|6759|676[1-3])/,
            valid_length: [12, 13, 14, 15, 16, 17, 18, 19],
        },
    ];

    accepted_cards = [
        "visa",
        "mastercard",
        "maestro",
        "visa_electron",
        "amex",
        "diners_club_carte_blanche",
        "diners_club_international",
    ];

    get_card_type = function (number) {
        var card, card_type, i, len, ref;
        ref = (function () {
            var j, len, ref, results;
            results = [];
            for (j = 0, len = card_types.length; j < len; j++) {
                card = card_types[j];
                if (((ref = card.name), indexOf.call(accepted_cards, ref) >= 0)) {
                    results.push(card);
                }
            }
            return results;
        })();

        for (i = 0, len = ref.length; i < len; i++) {
            card_type = ref[i];
            if (number.match(card_type.pattern)) {
                return card_type;
            }
        }
        return null;
    };

    is_valid_luhn = function (number) {
        var digit, i, len, n, ref, sum;
        sum = 0;
        ref = number.split("").reverse();
        for (n = i = 0, len = ref.length; i < len; n = ++i) {
            digit = ref[n];
            digit = +digit;
            if (n % 2) {
                digit *= 2;
                if (digit < 10) {
                    sum += digit;
                } else {
                    sum += digit - 9;
                }
            } else {
                sum += digit;
            }
        }
        return sum % 10 === 0;
    };

    is_valid_length = function (number, card_type) {
        var ref;
        return (
            (ref = number.length), indexOf.call(card_type.valid_length, ref) >= 0
        );
    };

    parseMonth = function (month) {
        if (typeof month === "string" && month.match(/^[\d]{1,2}$/)) {
            return parseInt(month);
        } else {
            return month;
        }
    };

    parseYear = function (year) {
        if (typeof year === "number" && year < 100) {
            year += 2000;
        }
        if (typeof year === "string" && year.match(/^([\d]{2,2}|20[\d]{2,2})$/)) {
            if (year.match(/^([\d]{2,2})$/)) {
                year = "20" + year;
            }
            return parseInt(year);
        } else {
            return year;
        }
    };

    ePayco.card = {};
    ePayco.card.name = function (name) {
        var eval = new RegExp();
        return eval.test(name);
    };

    ePayco.card.email = function (email) {
        var eval = new RegExp(
            /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        );
        return eval.test(email);
    };

    ePayco.card.validateNumber = function (number) {
        var card_type, length_valid, luhn_valid;
        if (typeof number === "string") {
            number = number.replace(/[ -]/g, "");
        } else if (typeof number === "number") {
            number = number.toString();
        } else {
            number = "";
        }
        card_type = get_card_type(number);
        luhn_valid = false;
        length_valid = false;
        if (card_type != null) {
            luhn_valid = is_valid_luhn(number);
            length_valid = is_valid_length(number, card_type);
        }
        return luhn_valid && length_valid;
    };

    ePayco.card.validateCVC = function (cvc) {
        return (
            (typeof cvc === "number" && cvc >= 0 && cvc < 10000) ||
            (typeof cvc === "string" && cvc.match(/^[\d]{3,4}$/) !== null)
        );
    };

    ePayco.card.validateExpirationDate = function (exp_month, exp_year) {
        var month, year;
        month = parseMonth(exp_month);
        year = parseYear(exp_year);
        if (
            typeof month === "number" &&
            month > 0 &&
            month < 13 &&
            typeof year === "number" &&
            year > 2020 &&
            year < 2035
        ) {
            return (
                new Date(year, month, new Date(year, month, 0).getDate()) > new Date()
            );
        } else {
            return false;
        }
    };
}.call(this));

(function () {
    ePayco.token = {};
    ePayco.token.create = function (form, callback) {
        var error = undefined,
            result = undefined,
            token = ePayco._utils.parseForm(form);

        if (typeof token === "object") {
            if (ePayco._utils.objectKeys(token).length > 0) {
                if (token.card) {
                    var name, email, number, cvc, exp_month, exp_year, paymentProcess;
                    paymentProcess = {
                        customer: [
                            {
                                type: "name",
                                value: token.card.name,
                                required: true,
                                validate: ePayco.card.name(token.card.name),
                            },
                            {
                                type: "email",
                                value: token.card.email,
                                required: true,
                                validate: ePayco.card.email(token.card.email),
                            },
                            {
                                type: "number",
                                value: token.card.number.replace(/ /g, ""),
                                required: true,
                                validate: ePayco.card.validateNumber(
                                    token.card.number.replace(/ /g, "")
                                ),
                            },
                            {
                                type: "cvc",
                                value: token.card.cvc,
                                required: true,
                                validate: ePayco.card.validateCVC(token.card.cvc),
                            },
                            {
                                type: "date_exp",
                                value: token.card.exp_month + "/" + token.card.exp_year,
                                required: true,
                                validate: ePayco.card.validateExpirationDate(
                                    token.card.exp_month,
                                    token.card.exp_year
                                ),
                            },
                        ],
                    };

                    var sessionId;
                    if (localStorage.getItem("keyUserIndex") == undefined) {
                        sessionId = localStorage.setItem(
                            "keyUserIndex",
                            ePayco._utils.createGuid()
                        );
                    } else {
                        sessionId = localStorage.getItem("keyUserIndex");
                    }

                    for (var i = 0; i < paymentProcess.customer.length; i++) {
                        var current = paymentProcess.customer[i];
                        if (current.required) {
                            if (!current.validate) {
                                let geterror_ = getError(103, current.type, form);
                                callback(geterror_.description, null);
                                return false;
                            }
                        }
                    }

                    ePayco._utils.createTokenEncrypt(
                        sessionId,
                        paymentProcess,
                        function (result, error) {
                            if (result) {
                                //
                                alert(JSON.stringify(result));

                                callback(error, result);
                            } else {
                                //

                                callback(error, null);
                            }
                        }
                    );
                } else {
                    getError(101);
                }
            }
        }
    };
}.call(this));

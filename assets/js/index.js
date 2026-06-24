jQuery(function ($) {
    $("body").on("contextmenu", function (e) {
        return false;
    });
    const countryList = [
        {
            "name": "Colombia",
            "name_es": "Colombia",
            "continent_en": "South America",
            "continent_es": "América del Sur",
            "capital_en": "Bogota",
            "capital_es": "Bogotá",
            "dial_code": "+57",
            "id": "CO",
            "code_3": "COL",
            "tld": ".co",
            "km2": 1141748,
            "flag": "🇨🇴"
        },
        {
            "name": "Afghanistan",
            "name_es": "Afganistán",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Kabul",
            "capital_es": "Kabul",
            "dial_code": "+93",
            "id": "AF",
            "code_3": "AFG",
            "tld": ".af",
            "km2": 652230,
            "flag": "🇦🇫"
        },
        {
            "name": "Albania",
            "name_es": "Albania",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Tirana",
            "capital_es": "Tirana",
            "dial_code": "+355",
            "id": "AL",
            "code_3": "ALB",
            "tld": ".al",
            "km2": 28748,
            "flag": "🇦🇱"
        },
        {
            "name": "Algeria",
            "name_es": "Argelia",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Algiers",
            "capital_es": "Argel",
            "dial_code": "+213",
            "id": "DZ",
            "code_3": "DZA",
            "tld": ".dz",
            "km2": 2381741,
            "flag": "🇩🇿"
        },
        {
            "name": "American Samoa",
            "name_es": "Samoa Americana",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Pago Pago",
            "capital_es": "Pago Pago",
            "dial_code": "+1684",
            "id": "AS",
            "code_3": "ASM",
            "tld": ".as",
            "km2": 199,
            "flag": "🇦🇸"
        },
        {
            "name": "Andorra",
            "name_es": "Andorra",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Andorra la Vella",
            "capital_es": "Andorra la Vieja",
            "dial_code": "+376",
            "id": "AD",
            "code_3": "AND",
            "tld": ".ad",
            "km2": 468,
            "flag": "🇦🇩"
        },
        {
            "name": "Angola",
            "name_es": "Angola",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Luanda",
            "capital_es": "Luanda",
            "dial_code": "+244",
            "id": "AO",
            "code_3": "AGO",
            "tld": ".ao",
            "km2": 1246700,
            "flag": "🇦🇴"
        },
        {
            "name": "Anguilla",
            "name_es": "Anguilla",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "The Valley",
            "capital_es": "El Valle",
            "dial_code": "+1264",
            "id": "AI",
            "code_3": "AIA",
            "tld": ".ai",
            "km2": 91,
            "flag": "🇦🇮"
        },
        {
            "name": "Antarctica",
            "name_es": "Antártida",
            "continent_en": "Antarctica",
            "continent_es": "Antártida",
            "capital_en": "",
            "capital_es": "",
            "dial_code": "+672",
            "id": "AQ",
            "code_3": "ATA",
            "tld": ".aq",
            "km2": 14200000,
            "flag": "🇦🇶"
        },
        {
            "name": "Antigua and Barbuda",
            "name_es": "Antigua y Barbuda",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "St. John's",
            "capital_es": "Saint John",
            "dial_code": "+1268",
            "id": "AG",
            "code_3": "ATG",
            "tld": ".ag",
            "km2": 442,
            "flag": "🇦🇬"
        },
        {
            "name": "Argentina",
            "name_es": "Argentina",
            "continent_en": "South America",
            "continent_es": "América del Sur",
            "capital_en": "Buenos Aires",
            "capital_es": "Buenos Aires",
            "dial_code": "+54",
            "id": "AR",
            "code_3": "ARG",
            "tld": ".ar",
            "km2": 2780400,
            "flag": "🇦🇷"
        },
        {
            "name": "Armenia",
            "name_es": "Armenia",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Yerevan",
            "capital_es": "Ereván",
            "dial_code": "+374",
            "id": "AM",
            "code_3": "ARM",
            "tld": ".am",
            "km2": 29743,
            "flag": "🇦🇲"
        },
        {
            "name": "Aruba",
            "name_es": "Aruba",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Oranjestad",
            "capital_es": "Oranjestad",
            "dial_code": "+297",
            "id": "AW",
            "code_3": "ABW",
            "tld": ".aw",
            "km2": 193,
            "flag": "🇦🇼"
        },

        {
            "name": "Australia",
            "name_es": "Australia",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Canberra",
            "capital_es": "Canberra",
            "dial_code": "+61",
            "id": "AU",
            "code_3": "AUS",
            "tld": ".au",
            "km2": 7692024,
            "flag": "🇦🇺"
        },
        {
            "name": "Austria",
            "name_es": "Austria",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Vienna",
            "capital_es": "Viena",
            "dial_code": "+43",
            "id": "AT",
            "code_3": "AUT",
            "tld": ".at",
            "km2": 83871,
            "flag": "🇦🇹"
        },
        {
            "name": "Azerbaijan",
            "name_es": "Azerbaiyán",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Baku",
            "capital_es": "Bakú",
            "dial_code": "+994",
            "id": "AZ",
            "code_3": "AZE",
            "tld": ".az",
            "km2": 86600,
            "flag": "🇦🇿"
        },
        {
            "name": "Bahamas",
            "name_es": "Bahamas",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Nassau",
            "capital_es": "Nassau",
            "dial_code": "+1242",
            "id": "BS",
            "code_3": "BHS",
            "tld": ".bs",
            "km2": 13940,
            "flag": "🇧🇸"
        },
        {
            "name": "Bahrain",
            "name_es": "Baréin",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Manama",
            "capital_es": "Manama",
            "dial_code": "+973",
            "id": "BH",
            "code_3": "BHR",
            "tld": ".bh",
            "km2": 765,
            "flag": "🇧🇭"
        },
        {
            "name": "Bangladesh",
            "name_es": "Bangladés",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Dhaka",
            "capital_es": "Daca",
            "dial_code": "+880",
            "id": "BD",
            "code_3": "BGD",
            "tld": ".bd",
            "km2": 147570,
            "flag": "🇧🇩"
        },
        {
            "name": "Barbados",
            "name_es": "Barbados",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Bridgetown",
            "capital_es": "Bridgetown",
            "dial_code": "+1246",
            "id": "BB",
            "code_3": "BRB",
            "tld": ".bb",
            "km2": 430,
            "flag": "🇧🇧"
        },
        {
            "name": "Belarus",
            "name_es": "Bielorrusia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Minsk",
            "capital_es": "Minsk",
            "dial_code": "+375",
            "id": "BY",
            "code_3": "BLR",
            "tld": ".by",
            "km2": 207600,
            "flag": "🇧🇾"
        },
        {
            "name": "Belgium",
            "name_es": "Bélgica",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Brussels",
            "capital_es": "Bruselas",
            "dial_code": "+32",
            "id": "BE",
            "code_3": "BEL",
            "tld": ".be",
            "km2": 30528,
            "flag": "🇧🇪"
        },
        {
            "name": "Belize",
            "name_es": "Belice",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Belmopan",
            "capital_es": "Belmopán",
            "dial_code": "+501",
            "id": "BZ",
            "code_3": "BLZ",
            "tld": ".bz",
            "km2": 22966,
            "flag": "🇧🇿"
        },
        {
            "name": "Benin",
            "name_es": "Benin",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Porto-Novo",
            "capital_es": "Porto-Novo",
            "dial_code": "+229",
            "id": "BJ",
            "code_3": "BEN",
            "tld": ".bj",
            "km2": 112622,
            "flag": "🇧🇯"
        },
        {
            "name": "Bermuda",
            "name_es": "Bermudas",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Hamilton",
            "capital_es": "Hamilton",
            "dial_code": "+1441",
            "id": "BM",
            "code_3": "BMU",
            "tld": ".bm",
            "km2": 54,
            "flag": "🇧🇲"
        },
        {
            "name": "Bhutan",
            "name_es": "Bután",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Thimphu",
            "capital_es": "Timbu",
            "dial_code": "+975",
            "id": "BT",
            "code_3": "BTN",
            "tld": ".bt",
            "km2": 38394,
            "flag": "🇧🇹"
        },
        {
            "name": "Bolivia",
            "name_es": "Bolivia",
            "continent_en": "South America",
            "continent_es": "América del Sur",
            "capital_en": "Sucre",
            "capital_es": "Sucre",
            "dial_code": "+591",
            "id": "BO",
            "code_3": "BOL",
            "tld": ".bo",
            "km2": 1098581,
            "flag": "🇧🇴"
        },
        {
            "name": "Bonaire",
            "name_es": "Bonaire",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Kralendijk",
            "capital_es": "Kralendijk",
            "dial_code": "+599",
            "id": "BQ",
            "code_3": "BES",
            "tld": ".bq",
            "km2": 288,
            "flag": "🇧🇶"
        },
        {
            "name": "Bosnia and Herzegovina",
            "name_es": "Bosnia y Herzegovina",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Sarajevo",
            "capital_es": "Sarajevo",
            "dial_code": "+387",
            "id": "BA",
            "code_3": "BIH",
            "tld": ".ba",
            "km2": 51209,
            "flag": "🇧🇦"
        },
        {
            "name": "Botswana",
            "name_es": "Botsuana",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Gaborone",
            "capital_es": "Gaborone",
            "dial_code": "+267",
            "id": "BW",
            "code_3": "BWA",
            "tld": ".bw",
            "km2": 582000,
            "flag": "🇧🇼"
        },
        {
            "name": "Brazil",
            "name_es": "Brasil",
            "continent_en": "South America",
            "continent_es": "América del Sur",
            "capital_en": "Brasilia",
            "capital_es": "Brasilia",
            "dial_code": "+55",
            "id": "BR",
            "code_3": "BRA",
            "tld": ".br",
            "km2": 8515767,
            "flag": "🇧🇷"
        },
        {
            "name": "British Indian Ocean Territory",
            "name_es": "Territorio Británico del Océano Índico",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Diego Garcia",
            "capital_es": "Diego García",
            "dial_code": "+246",
            "id": "IO",
            "code_3": "IOT",
            "tld": ".io",
            "km2": 60,
            "flag": "🇮🇴"
        },
        {
            "name": "Brunei Darussalam",
            "name_es": "Brunéi",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Bandar Seri Begawan",
            "capital_es": "Bandar Seri Begawan",
            "dial_code": "+673",
            "id": "BN",
            "code_3": "BRN",
            "tld": ".bn",
            "km2": 5765,
            "flag": "🇧🇳"
        },
        {
            "name": "Bulgaria",
            "name_es": "Bulgaria",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Sofia",
            "capital_es": "Sofía",
            "dial_code": "+359",
            "id": "BG",
            "code_3": "BGR",
            "tld": ".bg",
            "km2": 110879,
            "flag": "🇧🇬"
        },
        {
            "name": "Burkina Faso",
            "name_es": "Burkina Faso",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Ouagadougou",
            "capital_es": "Uagadugú",
            "dial_code": "+226",
            "id": "BF",
            "code_3": "BFA",
            "tld": ".bf",
            "km2": 274200,
            "flag": "🇧🇫"
        },
        {
            "name": "Burundi",
            "name_es": "Burundi",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Bujumbura",
            "capital_es": "Bujumbura",
            "dial_code": "+257",
            "id": "BI",
            "code_3": "BDI",
            "tld": ".bi",
            "km2": 27834,
            "flag": "🇧🇮"
        },
        {
            "name": "Cambodia",
            "name_es": "Camboya",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Phnom Penh",
            "capital_es": "Phnom Penh",
            "dial_code": "+855",
            "id": "KH",
            "code_3": "KHM",
            "tld": ".kh",
            "km2": 181035,
            "flag": "🇰🇭"
        },
        {
            "name": "Cameroon",
            "name_es": "Camerún",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Yaounde",
            "capital_es": "Yaundé",
            "dial_code": "+237",
            "id": "CM",
            "code_3": "CMR",
            "tld": ".cm",
            "km2": 475442,
            "flag": "🇨🇲"
        },
        {
            "name": "Canada",
            "name_es": "Canadá",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Ottawa",
            "capital_es": "Ottawa",
            "dial_code": "+1",
            "id": "CA",
            "code_3": "CAN",
            "tld": ".ca",
            "km2": 9984670,
            "flag": "🇨🇦"
        },
        {
            "name": "Cape Verde",
            "name_es": "Cabo Verde",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Praia",
            "capital_es": "Praia",
            "dial_code": "+238",
            "id": "CV",
            "code_3": "CPV",
            "tld": ".cv",
            "km2": 4033,
            "flag": "🇨🇻"
        },
        {
            "name": "Cayman Islands",
            "name_es": "Islas Caimán",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "George Town",
            "capital_es": "George Town",
            "dial_code": "+1345",
            "id": "KY",
            "code_3": "CYM",
            "tld": ".ky",
            "km2": 264,
            "flag": "🇰🇾"
        },
        {
            "name": "Central African Republic",
            "name_es": "República Centroafricana",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Bangui",
            "capital_es": "Bangui",
            "dial_code": "+236",
            "id": "CF",
            "code_3": "CAF",
            "tld": ".cf",
            "km2": 622984,
            "flag": "🇨🇫"
        },
        {
            "name": "Chad",
            "name_es": "Chad",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "N'Djamena",
            "capital_es": "N'Djamena",
            "dial_code": "+235",
            "id": "TD",
            "code_3": "TCD",
            "tld": ".td",
            "km2": 1284000,
            "flag": "🇹🇩"
        },
        {
            "name": "Chile",
            "name_es": "Chile",
            "continent_en": "South America",
            "continent_es": "América del Sur",
            "capital_en": "Santiago",
            "capital_es": "Santiago",
            "dial_code": "+56",
            "id": "CL",
            "code_3": "CHL",
            "tld": ".cl",
            "km2": 756102,
            "flag": "🇨🇱"
        },
        {
            "name": "China",
            "name_es": "China",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Beijing",
            "capital_es": "Pekín",
            "dial_code": "+86",
            "id": "CN",
            "code_3": "CHN",
            "tld": ".cn",
            "km2": 9706961,
            "flag": "🇨🇳"
        },
        {
            "name": "Christmas Island",
            "name_es": "Isla de Navidad",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Flying Fish Cove",
            "capital_es": "Flying Fish Cove",
            "dial_code": "+61",
            "id": "CX",
            "code_3": "CXR",
            "tld": ".cx",
            "km2": 135,
            "flag": "🇨🇽"
        },
        {
            "name": "Cocos (Keeling) Islands",
            "name_es": "Islas Cocos",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "West Island",
            "capital_es": "West Island",
            "dial_code": "+61",
            "id": "CC",
            "code_3": "CCK",
            "tld": ".cc",
            "km2": 14,
            "flag": "🇨🇨"
        },
        {
            "name": "Comoros",
            "name_es": "Comoras",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Moroni",
            "capital_es": "Moroni",
            "dial_code": "+269",
            "id": "KM",
            "code_3": "COM",
            "tld": ".km",
            "km2": 1862,
            "flag": "🇰🇲"
        },
        {
            "name": "Congo",
            "name_es": "Congo",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Brazzaville",
            "capital_es": "Brazzaville",
            "dial_code": "+242",
            "id": "CG",
            "code_3": "COG",
            "tld": ".cg",
            "km2": 342000,
            "flag": "🇨🇬"
        },
        {
            "name": "Cook Islands",
            "name_es": "Islas Cook",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Avarua",
            "capital_es": "Avarua",
            "dial_code": "+682",
            "id": "CK",
            "code_3": "COK",
            "tld": ".ck",
            "km2": 236,
            "flag": "🇨🇰"
        },
        {
            "name": "Costa Rica",
            "name_es": "Costa Rica",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "San Jose",
            "capital_es": "San José",
            "dial_code": "+506",
            "id": "CR",
            "code_3": "CRI",
            "tld": ".cr",
            "km2": 51100,
            "flag": "🇨🇷"
        },
        {
            "name": "Croatia",
            "name_es": "Croacia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Zagreb",
            "capital_es": "Zagreb",
            "dial_code": "+385",
            "id": "HR",
            "code_3": "HRV",
            "tld": ".hr",
            "km2": 56594,
            "flag": "🇭🇷"
        },
        {
            "name": "Cuba",
            "name_es": "Cuba",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Havana",
            "capital_es": "La Habana",
            "dial_code": "+53",
            "id": "CU",
            "code_3": "CUB",
            "tld": ".cu",
            "km2": 109884,
            "flag": "🇨🇺"
        },
        {
            "name": "Curaçao",
            "name_es": "Curazao",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Willemstad",
            "capital_es": "Willemstad",
            "dial_code": "+599",
            "id": "CW",
            "code_3": "CUW",
            "tld": ".cw",
            "km2": 444,
            "flag": "🇨🇼"
        },
        {
            "name": "Cyprus",
            "name_es": "Chipre",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Nicosia",
            "capital_es": "Nicosia",
            "dial_code": "+357",
            "id": "CY",
            "code_3": "CYP",
            "tld": ".cy",
            "km2": 9251,
            "flag": "🇨🇾"
        },
        {
            "name": "Czechia",
            "name_es": "Chequia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Prague",
            "capital_es": "Praga",
            "dial_code": "+420",
            "id": "CZ",
            "code_3": "CZE",
            "tld": ".cz",
            "km2": 78865,
            "flag": "🇨🇿"
        },
        {
            "name": "Côte d'Ivoire",
            "name_es": "Costa de Marfil",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Yamoussoukro",
            "capital_es": "Yamoussoukro",
            "dial_code": "+225",
            "id": "CI",
            "code_3": "CIV",
            "tld": ".ci",
            "km2": 322463,
            "flag": "🇨🇮"
        },
        {
            "name": "Democratic Republic of the Congo",
            "name_es": "República Democrática del Congo",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Kinshasa",
            "capital_es": "Kinshasa",
            "dial_code": "+243",
            "id": "CD",
            "code_3": "COD",
            "tld": ".cd",
            "km2": 2345409,
            "flag": "🇨🇩"
        },
        {
            "name": "Denmark",
            "name_es": "Dinamarca",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Copenhagen",
            "capital_es": "Copenhague",
            "dial_code": "+45",
            "id": "DK",
            "code_3": "DNK",
            "tld": ".dk",
            "km2": 43094,
            "flag": "🇩🇰"
        },
        {
            "name": "Djibouti",
            "name_es": "Yibuti",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Djibouti",
            "capital_es": "Yibuti",
            "dial_code": "+253",
            "id": "DJ",
            "code_3": "DJI",
            "tld": ".dj",
            "km2": 23200,
            "flag": "🇩🇯"
        },
        {
            "name": "Dominica",
            "name_es": "Dominica",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Roseau",
            "capital_es": "Roseau",
            "dial_code": "+1767",
            "id": "DM",
            "code_3": "DMA",
            "tld": ".dm",
            "km2": 751,
            "flag": "🇩🇲"
        },
        {
            "name": "Dominican Republic",
            "name_es": "República Dominicana",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Santo Domingo",
            "capital_es": "Santo Domingo",
            "dial_code": "+1849",
            "id": "DO",
            "code_3": "DOM",
            "tld": ".do",
            "km2": 48671,
            "flag": "🇩🇴"
        },
        {
            "name": "Ecuador",
            "name_es": "Ecuador",
            "continent_en": "South America",
            "continent_es": "América del Sur",
            "capital_en": "Quito",
            "capital_es": "Quito",
            "dial_code": "+593",
            "id": "EC",
            "code_3": "ECU",
            "tld": ".ec",
            "km2": 276841,
            "flag": "🇪🇨"
        },
        {
            "name": "England",
            "name_es": "Inglaterra",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "London",
            "capital_es": "Londres",
            "dial_code": "+44",
            "id": "EN",
            "code_3": "ENG",
            "tld": ".uk",
            "km2": 132932,
            "flag": "gb"
        },
        {
            "name": "Egypt",
            "name_es": "Egipto",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Cairo",
            "capital_es": "El Cairo",
            "dial_code": "+20",
            "id": "EG",
            "code_3": "EGY",
            "tld": ".eg",
            "km2": 1002450,
            "flag": "🇪🇬"
        },
        {
            "name": "El Salvador",
            "name_es": "El Salvador",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "San Salvador",
            "capital_es": "San Salvador",
            "dial_code": "+503",
            "id": "SV",
            "code_3": "SLV",
            "tld": ".sv",
            "km2": 21041,
            "flag": "🇸🇻"
        },
        {
            "name": "Equatorial Guinea",
            "name_es": "Guinea Ecuatorial",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Malabo",
            "capital_es": "Malabo",
            "dial_code": "+240",
            "id": "GQ",
            "code_3": "GNQ",
            "tld": ".gq",
            "km2": 28051,
            "flag": "🇬🇶"
        },
        {
            "name": "Eritrea",
            "name_es": "Eritrea",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Asmara",
            "capital_es": "Asmara",
            "dial_code": "+291",
            "id": "ER",
            "code_3": "ERI",
            "tld": ".er",
            "km2": 117600,
            "flag": "🇪🇷"
        },
        {
            "name": "Estonia",
            "name_es": "Estonia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Tallinn",
            "capital_es": "Tallin",
            "dial_code": "+372",
            "id": "EE",
            "code_3": "EST",
            "tld": ".ee",
            "km2": 45227,
            "flag": "🇪🇪"
        },
        {
            "name": "Eswatini",
            "name_es": "Suazilandia",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Mbabane",
            "capital_es": "Mbabane",
            "dial_code": "+268",
            "id": "SZ",
            "code_3": "SWZ",
            "tld": ".sz",
            "km2": 17364,
            "flag": "🇸🇿"
        },
        {
            "name": "Ethiopia",
            "name_es": "Etiopía",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Addis Ababa",
            "capital_es": "Adís Abeba",
            "dial_code": "+251",
            "id": "ET",
            "code_3": "ETH",
            "tld": ".et",
            "km2": 1104300,
            "flag": "🇪🇹"
        },
        {
            "name": "Falkland Islands (Malvinas)",
            "name_es": "Islas Malvinas",
            "continent_en": "South America",
            "continent_es": "América del Sur",
            "capital_en": "Stanley",
            "capital_es": "Stanley",
            "dial_code": "+500",
            "id": "FK",
            "code_3": "FLK",
            "tld": ".fk",
            "km2": 12173,
            "flag": "🇫🇰"
        },
        {
            "name": "Faroe Islands",
            "name_es": "Islas Feroe",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Tórshavn",
            "capital_es": "Tórshavn",
            "dial_code": "+298",
            "id": "FO",
            "code_3": "FRO",
            "tld": ".fo",
            "km2": 1393,
            "flag": "🇫🇴"
        },
        {
            "name": "Fiji",
            "name_es": "Fiyi",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Suva",
            "capital_es": "Suva",
            "dial_code": "+679",
            "id": "FJ",
            "code_3": "FJI",
            "tld": ".fj",
            "km2": 18272,
            "flag": "🇫🇯"
        },
        {
            "name": "Finland",
            "name_es": "Finlandia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Helsinki",
            "capital_es": "Helsinki",
            "dial_code": "+358",
            "id": "FI",
            "code_3": "FIN",
            "tld": ".fi",
            "km2": 338424,
            "flag": "🇫🇮"
        },
        {
            "name": "France",
            "name_es": "Francia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Paris",
            "capital_es": "París",
            "dial_code": "+33",
            "id": "FR",
            "code_3": "FRA",
            "tld": ".fr",
            "km2": 640679,
            "flag": "🇫🇷"
        },
        {
            "name": "French Guiana",
            "name_es": "Guayana Francesa",
            "continent_en": "South America",
            "continent_es": "América del Sur",
            "capital_en": "Cayenne",
            "capital_es": "Cayena",
            "dial_code": "+594",
            "id": "GF",
            "code_3": "GUF",
            "tld": ".gf",
            "km2": 83534,
            "flag": "🇬🇫"
        },
        {
            "name": "French Polynesia",
            "name_es": "Polinesia Francesa",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Papeete",
            "capital_es": "Papeete",
            "dial_code": "+689",
            "id": "PF",
            "code_3": "PYF",
            "tld": ".pf",
            "km2": 4167,
            "flag": "🇵🇫"
        },
        {
            "name": "French Southern Territories",
            "name_es": "Territorios Australes Franceses",
            "continent_en": "Antarctica",
            "continent_es": "Antártida",
            "capital_en": "Port-aux-Français",
            "capital_es": "Port-aux-Français",
            "dial_code": "+262",
            "id": "TF",
            "code_3": "ATF",
            "tld": ".tf",
            "km2": 7747,
            "flag": "🇹🇫"
        },
        {
            "name": "Gabon",
            "name_es": "Gabón",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Libreville",
            "capital_es": "Libreville",
            "dial_code": "+241",
            "id": "GA",
            "code_3": "GAB",
            "tld": ".ga",
            "km2": 267668,
            "flag": "🇬🇦"
        },
        {
            "name": "Gambia",
            "name_es": "Gambia",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Banjul",
            "capital_es": "Banjul",
            "dial_code": "+220",
            "id": "GM",
            "code_3": "GMB",
            "tld": ".gm",
            "km2": 10689,
            "flag": "🇬🇲"
        },
        {
            "name": "Georgia",
            "name_es": "Georgia",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Tbilisi",
            "capital_es": "Tiflis",
            "dial_code": "+995",
            "id": "GE",
            "code_3": "GEO",
            "tld": ".ge",
            "km2": 69700,
            "flag": "🇬🇪"
        },
        {
            "name": "Germany",
            "name_es": "Alemania",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Berlin",
            "capital_es": "Berlín",
            "dial_code": "+49",
            "id": "DE",
            "code_3": "DEU",
            "tld": ".de",
            "km2": 357114,
            "flag": "🇩🇪"
        },
        {
            "name": "Ghana",
            "name_es": "Ghana",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Accra",
            "capital_es": "Acra",
            "dial_code": "+233",
            "id": "GH",
            "code_3": "GHA",
            "tld": ".gh",
            "km2": 238533,
            "flag": "🇬🇭"
        },
        {
            "name": "Gibraltar",
            "name_es": "Gibraltar",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Gibraltar",
            "capital_es": "Gibraltar",
            "dial_code": "+350",
            "id": "GI",
            "code_3": "GIB",
            "tld": ".gi",
            "km2": 6,
            "flag": "🇬🇮"
        },
        {
            "name": "Greece",
            "name_es": "Grecia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Athens",
            "capital_es": "Atenas",
            "dial_code": "+30",
            "id": "GR",
            "code_3": "GRC",
            "tld": ".gr",
            "km2": 131990,
            "flag": "🇬🇷"
        },
        {
            "name": "Greenland",
            "name_es": "Groenlandia",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Nuuk",
            "capital_es": "Nuuk",
            "dial_code": "+299",
            "id": "GL",
            "code_3": "GRL",
            "tld": ".gl",
            "km2": 2166086,
            "flag": "🇬🇱"
        },
        {
            "name": "Grenada",
            "name_es": "Granada",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "St. George's",
            "capital_es": "St. George's",
            "dial_code": "+1473",
            "id": "GD",
            "code_3": "GRD",
            "tld": ".gd",
            "km2": 344,
            "flag": "🇬🇩"
        },
        {
            "name": "Guadeloupe",
            "name_es": "Guadalupe",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Basse-Terre",
            "capital_es": "Basse-Terre",
            "dial_code": "+590",
            "id": "GP",
            "code_3": "GLP",
            "tld": ".gp",
            "km2": 1628,
            "flag": "🇬🇵"
        },
        {
            "name": "Guam",
            "name_es": "Guam",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Hagåtña",
            "capital_es": "Hagåtña",
            "dial_code": "+1671",
            "id": "GU",
            "code_3": "GUM",
            "tld": ".gu",
            "km2": 549,
            "flag": "🇬🇺"
        },
        {
            "name": "Guatemala",
            "name_es": "Guatemala",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Guatemala City",
            "capital_es": "Ciudad de Guatemala",
            "dial_code": "+502",
            "id": "GT",
            "code_3": "GTM",
            "tld": ".gt",
            "km2": 108889,
            "flag": "🇬🇹"
        },
        {
            "name": "Guernsey",
            "name_es": "Guernsey",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "St. Peter Port",
            "capital_es": "St. Peter Port",
            "dial_code": "+44",
            "id": "GG",
            "code_3": "GGY",
            "tld": ".gg",
            "km2": 78,
            "flag": "🇬🇬"
        },
        {
            "name": "Guinea",
            "name_es": "Guinea",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Conakry",
            "capital_es": "Conakry",
            "dial_code": "+224",
            "id": "GN",
            "code_3": "GIN",
            "tld": ".gn",
            "km2": 245857,
            "flag": "🇬🇳"
        },
        {
            "name": "Guinea-Bissau",
            "name_es": "Guinea-Bissau",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Bissau",
            "capital_es": "Bissau",
            "dial_code": "+245",
            "id": "GW",
            "code_3": "GNB",
            "tld": ".gw",
            "km2": 36125,
            "flag": "🇬🇼"
        },
        {
            "name": "Guyana",
            "name_es": "Guyana",
            "continent_en": "South America",
            "continent_es": "América del Sur",
            "capital_en": "Georgetown",
            "capital_es": "Georgetown",
            "dial_code": "+592",
            "id": "GY",
            "code_3": "GUY",
            "tld": ".gy",
            "km2": 214969,
            "flag": "🇬🇾"
        },
        {
            "name": "Haiti",
            "name_es": "Haití",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Port-au-Prince",
            "capital_es": "Puerto Príncipe",
            "dial_code": "+509",
            "id": "HT",
            "code_3": "HTI",
            "tld": ".ht",
            "km2": 27750,
            "flag": "🇭🇹"
        },
        {
            "name": "Heard Island and McDonald Islands",
            "name_es": "Islas Heard y McDonald",
            "continent_en": "Antarctica",
            "continent_es": "Antártida",
            "capital_en": "",
            "capital_es": "",
            "dial_code": "+672",
            "id": "HM",
            "code_3": "HMD",
            "tld": ".hm",
            "km2": 412,
            "flag": "🇭🇲"
        },
        {
            "name": "Honduras",
            "name_es": "Honduras",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Tegucigalpa",
            "capital_es": "Tegucigalpa",
            "dial_code": "+504",
            "id": "HN",
            "code_3": "HND",
            "tld": ".hn",
            "km2": 112492,
            "flag": "🇭🇳"
        },
        {
            "name": "Hong Kong",
            "name_es": "Hong Kong",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Hong Kong",
            "capital_es": "Hong Kong",
            "dial_code": "+852",
            "id": "HK",
            "code_3": "HKG",
            "tld": ".hk",
            "km2": 2755,
            "flag": "🇭🇰"
        },
        {
            "name": "Hungary",
            "name_es": "Hungría",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Budapest",
            "capital_es": "Budapest",
            "dial_code": "+36",
            "id": "HU",
            "code_3": "HUN",
            "tld": ".hu",
            "km2": 93028,
            "flag": "🇭🇺"
        },
        {
            "name": "Iceland",
            "name_es": "Islandia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Reykjavik",
            "capital_es": "Reykjavik",
            "dial_code": "+354",
            "id": "IS",
            "code_3": "ISL",
            "tld": ".is",
            "km2": 103000,
            "flag": "🇮🇸"
        },
        {
            "name": "India",
            "name_es": "India",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "New Delhi",
            "capital_es": "Nueva Delhi",
            "dial_code": "+91",
            "id": "IN",
            "code_3": "IND",
            "tld": ".in",
            "km2": 3287590,
            "flag": "🇮🇳"
        },
        {
            "name": "Indonesia",
            "name_es": "Indonesia",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Jakarta",
            "capital_es": "Yakarta",
            "dial_code": "+62",
            "id": "ID",
            "code_3": "IDN",
            "tld": ".id",
            "km2": 1904569,
            "flag": "🇮🇩"
        },
        {
            "name": "Iran",
            "name_es": "Irán",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Tehran",
            "capital_es": "Teherán",
            "dial_code": "+98",
            "id": "IR",
            "code_3": "IRN",
            "tld": ".ir",
            "km2": 1648195,
            "flag": "🇮🇷"
        },
        {
            "name": "Iraq",
            "name_es": "Irak",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Baghdad",
            "capital_es": "Bagdad",
            "dial_code": "+964",
            "id": "IQ",
            "code_3": "IRQ",
            "tld": ".iq",
            "km2": 438317,
            "flag": "🇮🇶"
        },
        {
            "name": "Ireland",
            "name_es": "Irlanda",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Dublin",
            "capital_es": "Dublín",
            "dial_code": "+353",
            "id": "IE",
            "code_3": "IRL",
            "tld": ".ie",
            "km2": 70273,
            "flag": "🇮🇪"
        },
        {
            "name": "Isle of Man",
            "name_es": "Isla de Man",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Douglas",
            "capital_es": "Douglas",
            "dial_code": "+44",
            "id": "IM",
            "code_3": "IMN",
            "tld": ".im",
            "km2": 572,
            "flag": "🇮🇲"
        },
        {
            "name": "Israel",
            "name_es": "Israel",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Jerusalem",
            "capital_es": "Jerusalén",
            "dial_code": "+972",
            "id": "IL",
            "code_3": "ISR",
            "tld": ".il",
            "km2": 20770,
            "flag": "🇮🇱"
        },
        {
            "name": "Italy",
            "name_es": "Italia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Rome",
            "capital_es": "Roma",
            "dial_code": "+39",
            "id": "IT",
            "code_3": "ITA",
            "tld": ".it",
            "km2": 301336,
            "flag": "🇮🇹"
        },
        {
            "name": "Jamaica",
            "name_es": "Jamaica",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Kingston",
            "capital_es": "Kingston",
            "dial_code": "+1 876",
            "id": "JM",
            "code_3": "JAM",
            "tld": ".jm",
            "km2": 10991,
            "flag": "🇯🇲"
        },
        {
            "name": "Japan",
            "name_es": "Japón",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Tokyo",
            "capital_es": "Tokio",
            "dial_code": "+81",
            "id": "JP",
            "code_3": "JPN",
            "tld": ".jp",
            "km2": 377930,
            "flag": "🇯🇵"
        },
        {
            "name": "Jersey",
            "name_es": "Jersey",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Saint Helier",
            "capital_es": "Saint Helier",
            "dial_code": "+44",
            "id": "JE",
            "code_3": "JEY",
            "tld": ".je",
            "km2": 116,
            "flag": "🇯🇪"
        },
        {
            "name": "Jordan",
            "name_es": "Jordania",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Amman",
            "capital_es": "Amán",
            "dial_code": "+962",
            "id": "JO",
            "code_3": "JOR",
            "tld": ".jo",
            "km2": 89342,
            "flag": "🇯🇴"
        },
        {
            "name": "Kazakhstan",
            "name_es": "Kazajistán",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Astana",
            "capital_es": "Astana",
            "dial_code": "+7 7",
            "id": "KZ",
            "code_3": "KAZ",
            "tld": ".kz",
            "km2": 2724900,
            "flag": "🇰🇿"
        },
        {
            "name": "Kenya",
            "name_es": "Kenia",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Nairobi",
            "capital_es": "Nairobi",
            "dial_code": "+254",
            "id": "KE",
            "code_3": "KEN",
            "tld": ".ke",
            "km2": 580367,
            "flag": "🇰🇪"
        },
        {
            "name": "Kiribati",
            "name_es": "Kiribati",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "South Tarawa",
            "capital_es": "Sur Tarawa",
            "dial_code": "+686",
            "id": "KI",
            "code_3": "KIR",
            "tld": ".ki",
            "km2": 811,
            "flag": "🇰🇮"
        },
        {
            "name": "Kosovo",
            "name_es": "Kosovo",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Pristina",
            "capital_es": "Pristina",
            "dial_code": "+383",
            "id": "XK",
            "code_3": "XKX",
            "tld": ".xk",
            "km2": 10887,
            "flag": "🇽🇰"
        },
        {
            "name": "Kuwait",
            "name_es": "Kuwait",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Kuwait City",
            "capital_es": "Kuwait City",
            "dial_code": "+965",
            "id": "KW",
            "code_3": "KWT",
            "tld": ".kw",
            "km2": 17818,
            "flag": "🇰🇼"
        },
        {
            "name": "Kyrgyzstan",
            "name_es": "Kirguistán",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Bishkek",
            "capital_es": "Bishkek",
            "dial_code": "+996",
            "id": "KG",
            "code_3": "KGZ",
            "tld": ".kg",
            "km2": 199951,
            "flag": "🇰🇬"
        },
        {
            "name": "Laos",
            "name_es": "Laos",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Vientiane",
            "capital_es": "Vientiane",
            "dial_code": "+856",
            "id": "LA",
            "code_3": "LAO",
            "tld": ".la",
            "km2": 236800,
            "flag": "🇱🇦"
        },
        {
            "name": "Latvia",
            "name_es": "Letonia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Riga",
            "capital_es": "Riga",
            "dial_code": "+371",
            "id": "LV",
            "code_3": "LVA",
            "tld": ".lv",
            "km2": 64559,
            "flag": "🇱🇻"
        },
        {
            "name": "Lebanon",
            "name_es": "Líbano",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Beirut",
            "capital_es": "Beirut",
            "dial_code": "+961",
            "id": "LB",
            "code_3": "LBN",
            "tld": ".lb",
            "km2": 10452,
            "flag": "🇱🇧"
        },
        {
            "name": "Lesotho",
            "name_es": "Lesoto",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Maseru",
            "capital_es": "Maseru",
            "dial_code": "+266",
            "id": "LS",
            "code_3": "LSO",
            "tld": ".ls",
            "km2": 30355,
            "flag": "🇱🇸"
        },
        {
            "name": "Liberia",
            "name_es": "Liberia",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Monrovia",
            "capital_es": "Monrovia",
            "dial_code": "+231",
            "id": "LR",
            "code_3": "LBR",
            "tld": ".lr",
            "km2": 111369,
            "flag": "🇱🇷"
        },
        {
            "name": "Libya",
            "name_es": "Libia",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Tripoli",
            "capital_es": "Trípoli",
            "dial_code": "+218",
            "id": "LY",
            "code_3": "LBY",
            "tld": ".ly",
            "km2": 1759540,
            "flag": "🇱🇾"
        },
        {
            "name": "Liechtenstein",
            "name_es": "Liechtenstein",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Vaduz",
            "capital_es": "Vaduz",
            "dial_code": "+423",
            "id": "LI",
            "code_3": "LIE",
            "tld": ".li",
            "km2": 160,
            "flag": "🇱🇮"
        },
        {
            "name": "Lithuania",
            "name_es": "Lituania",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Vilnius",
            "capital_es": "Vilna",
            "dial_code": "+370",
            "id": "LT",
            "code_3": "LTU",
            "tld": ".lt",
            "km2": 65300,
            "flag": "🇱🇹"
        },
        {
            "name": "Luxembourg",
            "name_es": "Luxemburgo",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Luxembourg",
            "capital_es": "Luxemburgo",
            "dial_code": "+352",
            "id": "LU",
            "code_3": "LUX",
            "tld": ".lu",
            "km2": 2586,
            "flag": "🇱🇺"
        },
        {
            "name": "Macao",
            "name_es": "Macao",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Macao",
            "capital_es": "Macao",
            "dial_code": "+853",
            "id": "MO",
            "code_3": "MAC",
            "tld": ".mo",
            "km2": 30,
            "flag": "🇲🇴"
        },
        {
            "name": "Madagascar",
            "name_es": "Madagascar",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Antananarivo",
            "capital_es": "Antananarivo",
            "dial_code": "+261",
            "id": "MG",
            "code_3": "MDG",
            "tld": ".mg",
            "km2": 587041,
            "flag": "🇲🇬"
        },
        {
            "name": "Malawi",
            "name_es": "Malawi",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Lilongwe",
            "capital_es": "Lilongwe",
            "dial_code": "+265",
            "id": "MW",
            "code_3": "MWI",
            "tld": ".mw",
            "km2": 118484,
            "flag": "🇲🇼"
        },
        {
            "name": "Malaysia",
            "name_es": "Malasia",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Kuala Lumpur",
            "capital_es": "Kuala Lumpur",
            "dial_code": "+60",
            "id": "MY",
            "code_3": "MYS",
            "tld": ".my",
            "km2": 330803,
            "flag": "🇲🇾"
        },
        {
            "name": "Maldives",
            "name_es": "Maldivas",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Malé",
            "capital_es": "Malé",
            "dial_code": "+960",
            "id": "MV",
            "code_3": "MDV",
            "tld": ".mv",
            "km2": 300,
            "flag": "🇲🇻"
        },
        {
            "name": "Mali",
            "name_es": "Mali",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Bamako",
            "capital_es": "Bamako",
            "dial_code": "+223",
            "id": "ML",
            "code_3": "MLI",
            "tld": ".ml",
            "km2": 1240192,
            "flag": "🇲🇱"
        },
        {
            "name": "Malta",
            "name_es": "Malta",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Valletta",
            "capital_es": "La Valeta",
            "dial_code": "+356",
            "id": "MT",
            "code_3": "MLT",
            "tld": ".mt",
            "km2": 316,
            "flag": "🇲🇹"
        },
        {
            "name": "Marshall Islands",
            "name_es": "Islas Marshall",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Majuro",
            "capital_es": "Majuro",
            "dial_code": "+692",
            "id": "MH",
            "code_3": "MHL",
            "tld": ".mh",
            "km2": 181,
            "flag": "🇲🇭"
        },
        {
            "name": "Martinique",
            "name_es": "Martinica",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Fort-de-France",
            "capital_es": "Fort-de-France",
            "dial_code": "+596",
            "id": "MQ",
            "code_3": "MTQ",
            "tld": ".mq",
            "km2": 1128,
            "flag": "🇲🇶"
        },
        {
            "name": "Mauritania",
            "name_es": "Mauritania",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Nouakchott",
            "capital_es": "Nouakchott",
            "dial_code": "+222",
            "id": "MR",
            "code_3": "MRT",
            "tld": ".mr",
            "km2": 1030700,
            "flag": "🇲🇷"
        },
        {
            "name": "Mauritius",
            "name_es": "Mauricio",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Port Louis",
            "capital_es": "Port Louis",
            "dial_code": "+230",
            "id": "MU",
            "code_3": "MUS",
            "tld": ".mu",
            "km2": 2040,
            "flag": "🇲🇺"
        },
        {
            "name": "Mayotte",
            "name_es": "Mayotte",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Mamoudzou",
            "capital_es": "Mamoudzou",
            "dial_code": "+262",
            "id": "YT",
            "code_3": "MYT",
            "tld": ".yt",
            "km2": 374,
            "flag": "🇾🇹"
        },
        {
            "name": "Mexico",
            "name_es": "México",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Mexico City",
            "capital_es": "Ciudad de México",
            "dial_code": "+52",
            "id": "MX",
            "code_3": "MEX",
            "tld": ".mx",
            "km2": 1964375,
            "flag": "🇲🇽"
        },
        {
            "name": "Micronesia",
            "name_es": "Micronesia",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Palikir",
            "capital_es": "Palikir",
            "dial_code": "+691",
            "id": "FM",
            "code_3": "FSM",
            "tld": ".fm",
            "km2": 702,
            "flag": "🇫🇲"
        },
        {
            "name": "Moldova",
            "name_es": "Moldavia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Chisinau",
            "capital_es": "Chisinau",
            "dial_code": "+373",
            "id": "MD",
            "code_3": "MDA",
            "tld": ".md",
            "km2": 33846,
            "flag": "🇲🇩"
        },
        {
            "name": "Monaco",
            "name_es": "Mónaco",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Monaco",
            "capital_es": "Mónaco",
            "dial_code": "+377",
            "id": "MC",
            "code_3": "MCO",
            "tld": ".mc",
            "km2": 2,
            "flag": "🇲🇨"
        },
        {
            "name": "Mongolia",
            "name_es": "Mongolia",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Ulan Bator",
            "capital_es": "Ulan Bator",
            "dial_code": "+976",
            "id": "MN",
            "code_3": "MNG",
            "tld": ".mn",
            "km2": 1564110,
            "flag": "🇲🇳"
        },
        {
            "name": "Montenegro",
            "name_es": "Montenegro",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Podgorica",
            "capital_es": "Podgorica",
            "dial_code": "+382",
            "id": "ME",
            "code_3": "MNE",
            "tld": ".me",
            "km2": 13812,
            "flag": "🇲🇪"
        },
        {
            "name": "Montserrat",
            "name_es": "Montserrat",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Plymouth",
            "capital_es": "Plymouth",
            "dial_code": "+1664",
            "id": "MS",
            "code_3": "MSR",
            "tld": ".ms",
            "km2": 102,
            "flag": "🇲🇸"
        },
        {
            "name": "Morocco",
            "name_es": "Marruecos",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Rabat",
            "capital_es": "Rabat",
            "dial_code": "+212",
            "id": "MA",
            "code_3": "MAR",
            "tld": ".ma",
            "km2": 446550,
            "flag": "🇲🇦"
        },
        {
            "name": "Mozambique",
            "name_es": "Mozambique",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Maputo",
            "capital_es": "Maputo",
            "dial_code": "+258",
            "id": "MZ",
            "code_3": "MOZ",
            "tld": ".mz",
            "km2": 801590,
            "flag": "🇲🇿"
        },
        {
            "name": "Myanmar",
            "name_es": "Myanmar",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Naypyidaw",
            "capital_es": "Naypyidaw",
            "dial_code": "+95",
            "id": "MM",
            "code_3": "MMR",
            "tld": ".mm",
            "km2": 676578,
            "flag": "🇲🇲"
        },
        {
            "name": "Namibia",
            "name_es": "Namibia",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Windhoek",
            "capital_es": "Windhoek",
            "dial_code": "+264",
            "id": "NA",
            "code_3": "NAM",
            "tld": ".na",
            "km2": 825615,
            "flag": "🇳🇦"
        },
        {
            "name": "Nauru",
            "name_es": "Nauru",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Yaren",
            "capital_es": "Yaren",
            "dial_code": "+674",
            "id": "NR",
            "code_3": "NRU",
            "tld": ".nr",
            "km2": 21,
            "flag": "🇳🇷"
        },
        {
            "name": "Nepal",
            "name_es": "Nepal",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Kathmandu",
            "capital_es": "Kathmandu",
            "dial_code": "+977",
            "id": "NP",
            "code_3": "NPL",
            "tld": ".np",
            "km2": 147181,
            "flag": "🇳🇵"
        },
        {
            "name": "Netherlands",
            "name_es": "Países Bajos",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Amsterdam",
            "capital_es": "Ámsterdam",
            "dial_code": "+31",
            "id": "NL",
            "code_3": "NLD",
            "tld": ".nl",
            "km2": 41850,
            "flag": "🇳🇱"
        },
        {
            "name": "New Caledonia",
            "name_es": "Nueva Caledonia",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Noumea",
            "capital_es": "Noumea",
            "dial_code": "+687",
            "id": "NC",
            "code_3": "NCL",
            "tld": ".nc",
            "km2": 18575,
            "flag": "🇳🇨"
        },
        {
            "name": "New Zealand",
            "name_es": "Nueva Zelanda",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Wellington",
            "capital_es": "Wellington",
            "dial_code": "+64",
            "id": "NZ",
            "code_3": "NZL",
            "tld": ".nz",
            "km2": 270467,
            "flag": "🇳🇿"
        },
        {
            "name": "Nicaragua",
            "name_es": "Nicaragua",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Managua",
            "capital_es": "Managua",
            "dial_code": "+505",
            "id": "NI",
            "code_3": "NIC",
            "tld": ".ni",
            "km2": 130373,
            "flag": "🇳🇮"
        },
        {
            "name": "Niger",
            "name_es": "Níger",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Niamey",
            "capital_es": "Niamey",
            "dial_code": "+227",
            "id": "NE",
            "code_3": "NER",
            "tld": ".ne",
            "km2": 1267000,
            "flag": "🇳🇪"
        },
        {
            "name": "Nigeria",
            "name_es": "Nigeria",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Abuja",
            "capital_es": "Abuja",
            "dial_code": "+234",
            "id": "NG",
            "code_3": "NGA",
            "tld": ".ng",
            "km2": 923768,
            "flag": "🇳🇬"
        },
        {
            "name": "Niue",
            "name_es": "Niue",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Alofi",
            "capital_es": "Alofi",
            "dial_code": "+683",
            "id": "NU",
            "code_3": "NIU",
            "tld": ".nu",
            "km2": 260,
            "flag": "🇳🇺"
        },
        {
            "name": "Norfolk Island",
            "name_es": "Isla Norfolk",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Kingston",
            "capital_es": "Kingston",
            "dial_code": "+672",
            "id": "NF",
            "code_3": "NFK",
            "tld": ".nf",
            "km2": 36,
            "flag": "🇳🇫"
        },
        {
            "name": "North Korea",
            "name_es": "Corea del Norte",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Pyongyang",
            "capital_es": "Pyongyang",
            "dial_code": "+850",
            "id": "KP",
            "code_3": "PRK",
            "tld": ".kp",
            "km2": 120538,
            "flag": "🇰🇵"
        },
        {
            "name": "North Macedonia",
            "name_es": "Macedonia del Norte",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Skopje",
            "capital_es": "Skopje",
            "dial_code": "+389",
            "id": "MK",
            "code_3": "MKD",
            "tld": ".mk",
            "km2": 25713,
            "flag": "🇲🇰"
        },
        {
            "name": "Northern Ireland",
            "name_es": "Irlanda del Norte",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Belfast",
            "capital_es": "Belfast",
            "dial_code": "+44",
            "id": "EI",
            "code_3": "NIR",
            "tld": ".uk",
            "km2": 14330,
            "flag": "🏴󠁧󠁢󠁮󠁩󠁲󠁿"
        },
        {
            "name": "Northern Mariana Islands",
            "name_es": "Islas Marianas del Norte",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Saipan",
            "capital_es": "Saipan",
            "dial_code": "+1670",
            "id": "MP",
            "code_3": "MNP",
            "tld": ".mp",
            "km2": 464,
            "flag": "🇲🇵"
        },
        {
            "name": "Norway",
            "name_es": "Noruega",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Oslo",
            "capital_es": "Oslo",
            "dial_code": "+47",
            "id": "NO",
            "code_3": "NOR",
            "tld": ".no",
            "km2": 323802,
            "flag": "🇳🇴"
        },
        {
            "name": "Oman",
            "name_es": "Omán",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Muscat",
            "capital_es": "Mascate",
            "dial_code": "+968",
            "id": "OM",
            "code_3": "OMN",
            "tld": ".om",
            "km2": 309500,
            "flag": "🇴🇲"
        },
        {
            "name": "Pakistan",
            "name_es": "Pakistán",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Islamabad",
            "capital_es": "Islamabad",
            "dial_code": "+92",
            "id": "PK",
            "code_3": "PAK",
            "tld": ".pk",
            "km2": 881912,
            "flag": "🇵🇰"
        },
        {
            "name": "Palau",
            "name_es": "Palaos",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Ngerulmud",
            "capital_es": "Ngerulmud",
            "dial_code": "+680",
            "id": "PW",
            "code_3": "PLW",
            "tld": ".pw",
            "km2": 459,
            "flag": "🇵🇼"
        },
        {
            "name": "Palestine",
            "name_es": "Palestina",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Ramallah",
            "capital_es": "Ramala",
            "dial_code": "+970",
            "id": "PS",
            "code_3": "PSE",
            "tld": ".ps",
            "km2": 6020,
            "flag": "🇵🇸"
        },
        {
            "name": "Panama",
            "name_es": "Panamá",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Panama City",
            "capital_es": "Ciudad de Panamá",
            "dial_code": "+507",
            "id": "PA",
            "code_3": "PAN",
            "tld": ".pa",
            "km2": 75417,
            "flag": "🇵🇦"
        },
        {
            "name": "Papua New Guinea",
            "name_es": "Papúa Nueva Guinea",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Port Moresby",
            "capital_es": "Puerto Moresby",
            "dial_code": "+675",
            "id": "PG",
            "code_3": "PNG",
            "tld": ".pg",
            "km2": 462840,
            "flag": "🇵🇬"
        },
        {
            "name": "Paraguay",
            "name_es": "Paraguay",
            "continent_en": "South America",
            "continent_es": "América del Sur",
            "capital_en": "Asunción",
            "capital_es": "Asunción",
            "dial_code": "+595",
            "id": "PY",
            "code_3": "PRY",
            "tld": ".py",
            "km2": 406752,
            "flag": "🇵🇾"
        },
        {
            "name": "Peru",
            "name_es": "Perú",
            "continent_en": "South America",
            "continent_es": "América del Sur",
            "capital_en": "Lima",
            "capital_es": "Lima",
            "dial_code": "+51",
            "id": "PE",
            "code_3": "PER",
            "tld": ".pe",
            "km2": 1285216,
            "flag": "🇵🇪"
        },
        {
            "name": "Philippines",
            "name_es": "Filipinas",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Manila",
            "capital_es": "Manila",
            "dial_code": "+63",
            "id": "PH",
            "code_3": "PHL",
            "tld": ".ph",
            "km2": 342353,
            "flag": "🇵🇭"
        },
        {
            "name": "Pitcairn",
            "name_es": "Islas Pitcairn",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Adamstown",
            "capital_es": "Adamstown",
            "dial_code": "+64",
            "id": "PN",
            "code_3": "PCN",
            "tld": ".pn",
            "km2": 47,
            "flag": "🇵🇳"
        },
        {
            "name": "Poland",
            "name_es": "Polonia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Warsaw",
            "capital_es": "Varsovia",
            "dial_code": "+48",
            "id": "PL",
            "code_3": "POL",
            "tld": ".pl",
            "km2": 312679,
            "flag": "🇵🇱"
        },
        {
            "name": "Portugal",
            "name_es": "Portugal",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Lisbon",
            "capital_es": "Lisboa",
            "dial_code": "+351",
            "id": "PT",
            "code_3": "PRT",
            "tld": ".pt",
            "km2": 92090,
            "flag": "🇵🇹"
        },
        {
            "name": "Puerto Rico",
            "name_es": "Puerto Rico",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "San Juan",
            "capital_es": "San Juan",
            "dial_code": "+1939",
            "id": "PR",
            "code_3": "PRI",
            "tld": ".pr",
            "km2": 8870,
            "flag": "🇵🇷"
        },
        {
            "name": "Qatar",
            "name_es": "Catar",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Doha",
            "capital_es": "Doha",
            "dial_code": "+974",
            "id": "QA",
            "code_3": "QAT",
            "tld": ".qa",
            "km2": 11586,
            "flag": "🇶🇦"
        },
        {
            "name": "Romania",
            "name_es": "Rumania",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Bucharest",
            "capital_es": "Bucarest",
            "dial_code": "+40",
            "id": "RO",
            "code_3": "ROU",
            "tld": ".ro",
            "km2": 238391,
            "flag": "🇷🇴"
        },
        {
            "name": "Russia",
            "name_es": "Rusia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Moscow",
            "capital_es": "Moscú",
            "dial_code": "+7",
            "id": "RU",
            "code_3": "RUS",
            "tld": ".ru",
            "km2": 17098242,
            "flag": "🇷🇺"
        },
        {
            "name": "Rwanda",
            "name_es": "Ruanda",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Kigali",
            "capital_es": "Kigali",
            "dial_code": "+250",
            "id": "RW",
            "code_3": "RWA",
            "tld": ".rw",
            "km2": 26338,
            "flag": "🇷🇼"
        },
        {
            "name": "Réunion",
            "name_es": "Reunión",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Saint-Denis",
            "capital_es": "Saint-Denis",
            "dial_code": "+262",
            "id": "RE",
            "code_3": "REU",
            "tld": ".re",
            "km2": 2511,
            "flag": "🇷🇪"
        },
        {
            "name": "Saba",
            "name_es": "Saba",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "The Bottom",
            "capital_es": "The Bottom",
            "dial_code": "+599",
            "id": "BQ",
            "code_3": "BES",
            "tld": ".bq",
            "km2": 13,
            "flag": "🇧🇶"
        },
        {
            "name": "Saint Barthélemy",
            "name_es": "San Bartolomé",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Gustavia",
            "capital_es": "Gustavia",
            "dial_code": "+590",
            "id": "BL",
            "code_3": "BLM",
            "tld": ".bl",
            "km2": 21,
            "flag": "🇧🇱"
        },
        {
            "name": "Saint Helena",
            "name_es": "Santa Elena",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Jamestown",
            "capital_es": "Jamestown",
            "dial_code": "+290",
            "id": "SH",
            "code_3": "SHN",
            "tld": ".sh",
            "km2": 394,
            "flag": "🇸🇭"
        },
        {
            "name": "Saint Kitts and Nevis",
            "name_es": "San Cristóbal y Nieves",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Basseterre",
            "capital_es": "Basseterre",
            "dial_code": "+1869",
            "id": "KN",
            "code_3": "KNA",
            "tld": ".kn",
            "km2": 261,
            "flag": "🇰🇳"
        },
        {
            "name": "Saint Lucia",
            "name_es": "Santa Lucía",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Castries",
            "capital_es": "Castries",
            "dial_code": "+1758",
            "id": "LC",
            "code_3": "LCA",
            "tld": ".lc",
            "km2": 616,
            "flag": "🇱🇨"
        },
        {
            "name": "Sint Eustatius",
            "name_es": "San Eustaquio",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Philipsburg",
            "capital_es": "Philipsburg",
            "dial_code": "+599",
            "id": "BQ",
            "code_3": "BES",
            "tld": ".bq",
            "km2": 21,
            "flag": "🇧🇶"
        },
        {
            "name": "Saint Martin",
            "name_es": "San Martín",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Marigot",
            "capital_es": "Marigot",
            "dial_code": "+590",
            "id": "MF",
            "code_3": "MAF",
            "tld": ".mf",
            "km2": 53,
            "flag": "🇲🇫"
        },
        {
            "name": "Saint Pierre and Miquelon",
            "name_es": "San Pedro y Miquelón",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Saint-Pierre",
            "capital_es": "Saint-Pierre",
            "dial_code": "+508",
            "id": "PM",
            "code_3": "SPM",
            "tld": ".pm",
            "km2": 242,
            "flag": "🇵🇲"
        },
        {
            "name": "Saint Vincent and the Grenadines",
            "name_es": "San Vicente y las Granadinas",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Kingstown",
            "capital_es": "Kingstown",
            "dial_code": "+1784",
            "id": "VC",
            "code_3": "VCT",
            "tld": ".vc",
            "km2": 389,
            "flag": "🇻🇨"
        },
        {
            "name": "Samoa",
            "name_es": "Samoa",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Apia",
            "capital_es": "Apia",
            "dial_code": "+685",
            "id": "WS",
            "code_3": "WSM",
            "tld": ".ws",
            "km2": 2842,
            "flag": "🇼🇸"
        },
        {
            "name": "San Marino",
            "name_es": "San Marino",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "San Marino",
            "capital_es": "San Marino",
            "dial_code": "+378",
            "id": "SM",
            "code_3": "SMR",
            "tld": ".sm",
            "km2": 61,
            "flag": "🇸🇲"
        },
        {
            "name": "Sao Tome and Principe",
            "name_es": "Santo Tomé y Príncipe",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "São Tomé",
            "capital_es": "São Tomé",
            "dial_code": "+239",
            "id": "ST",
            "code_3": "STP",
            "tld": ".st",
            "km2": 964,
            "flag": "🇸🇹"
        },
        {
            "name": "Saudi Arabia",
            "name_es": "Arabia Saudita",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Riyadh",
            "capital_es": "Riad",
            "dial_code": "+966",
            "id": "SA",
            "code_3": "SAU",
            "tld": ".sa",
            "km2": 2149690,
            "flag": "🇸🇦"
        },
        {
            "name": "Scotland",
            "name_es": "Escocia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Edinburgh",
            "capital_es": "Edimburgo",
            "dial_code": "+44",
            "id": "SQ",
            "code_3": "SCT",
            "tld": ".uk",
            "km2": 80231,
            "flag": "🏴󠁧󠁢󠁳󠁣󠁴󠁿"
        },
        {
            "name": "Senegal",
            "name_es": "Senegal",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Dakar",
            "capital_es": "Dakar",
            "dial_code": "+221",
            "id": "SN",
            "code_3": "SEN",
            "tld": ".sn",
            "km2": 196722,
            "flag": "🇸🇳"
        },
        {
            "name": "Serbia",
            "name_es": "Serbia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Belgrade",
            "capital_es": "Belgrado",
            "dial_code": "+381",
            "id": "RS",
            "code_3": "SRB",
            "tld": ".rs",
            "km2": 88361,
            "flag": "🇷🇸"
        },
        {
            "name": "Seychelles",
            "name_es": "Seychelles",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Victoria",
            "capital_es": "Victoria",
            "dial_code": "+248",
            "id": "SC",
            "code_3": "SYC",
            "tld": ".sc",
            "km2": 452,
            "flag": "🇸🇨"
        },
        {
            "name": "Sierra Leone",
            "name_es": "Sierra Leona",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Freetown",
            "capital_es": "Freetown",
            "dial_code": "+232",
            "id": "SL",
            "code_3": "SLE",
            "tld": ".sl",
            "km2": 71740,
            "flag": "🇸🇱"
        },
        {
            "name": "Singapore",
            "name_es": "Singapur",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Singapore",
            "capital_es": "Singapur",
            "dial_code": "+65",
            "id": "SG",
            "code_3": "SGP",
            "tld": ".sg",
            "km2": 710,
            "flag": "🇸🇬"
        },
        {
            "name": "Sint Maarten",
            "name_es": "Sint Maarten",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Philipsburg",
            "capital_es": "Philipsburg",
            "dial_code": "+1721",
            "id": "SX",
            "code_3": "SXM",
            "tld": ".sx",
            "km2": 34,
            "flag": "🇸🇽"
        },
        {
            "name": "Slovakia",
            "name_es": "Eslovaquia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Bratislava",
            "capital_es": "Bratislava",
            "dial_code": "+421",
            "id": "SK",
            "code_3": "SVK",
            "tld": ".sk",
            "km2": 49037,
            "flag": "🇸🇰"
        },
        {
            "name": "Slovenia",
            "name_es": "Eslovenia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Ljubljana",
            "capital_es": "Ljubljana",
            "dial_code": "+386",
            "id": "SI",
            "code_3": "SVN",
            "tld": ".si",
            "km2": 20273,
            "flag": "🇸🇮"
        },
        {
            "name": "Solomon Islands",
            "name_es": "Islas Salomón",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Honiara",
            "capital_es": "Honiara",
            "dial_code": "+677",
            "id": "SB",
            "code_3": "SLB",
            "tld": ".sb",
            "km2": 28896,
            "flag": "🇸🇧"
        },
        {
            "name": "Somalia",
            "name_es": "Somalia",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Mogadishu",
            "capital_es": "Mogadiscio",
            "dial_code": "+252",
            "id": "SO",
            "code_3": "SOM",
            "tld": ".so",
            "km2": 637657,
            "flag": "🇸🇴"
        },
        {
            "name": "South Africa",
            "name_es": "Sudáfrica",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Pretoria",
            "capital_es": "Pretoria",
            "dial_code": "+27",
            "id": "ZA",
            "code_3": "ZAF",
            "tld": ".za",
            "km2": 1221037,
            "flag": "🇿🇦"
        },
        {
            "name": "South Georgia and the South Sandwich Islands",
            "name_es": "Islas Georgias del Sur y Sandwich del Sur",
            "continent_en": "Antarctica",
            "continent_es": "Antártida",
            "capital_en": "King Edward Point",
            "capital_es": "King Edward Point",
            "dial_code": "+500",
            "id": "GS",
            "code_3": "SGS",
            "tld": ".gs",
            "km2": 3903,
            "flag": "🇬🇸"
        },
        {
            "name": "South Korea",
            "name_es": "Corea del Sur",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Seoul",
            "capital_es": "Seúl",
            "dial_code": "+82",
            "id": "KR",
            "code_3": "KOR",
            "tld": ".kr",
            "km2": 100210,
            "flag": "🇰🇷"
        },
        {
            "name": "South Sudan",
            "name_es": "Sudán del Sur",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Juba",
            "capital_es": "Juba",
            "dial_code": "+211",
            "id": "SS",
            "code_3": "SSD",
            "tld": ".ss",
            "km2": 619745,
            "flag": "🇸🇸"
        },
        {
            "name": "Spain",
            "name_es": "España",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Madrid",
            "capital_es": "Madrid",
            "dial_code": "+34",
            "id": "ES",
            "code_3": "ESP",
            "tld": ".es",
            "km2": 505992,
            "flag": "🇪🇸"
        },
        {
            "name": "Sri Lanka",
            "name_es": "Sri Lanka",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Colombo",
            "capital_es": "Colombo",
            "dial_code": "+94",
            "id": "LK",
            "code_3": "LKA",
            "tld": ".lk",
            "km2": 65610,
            "flag": "🇱🇰"
        },
        {
            "name": "Sudan",
            "name_es": "Sudán",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Khartoum",
            "capital_es": "Jartum",
            "dial_code": "+249",
            "id": "SD",
            "code_3": "SDN",
            "tld": ".sd",
            "km2": 1886068,
            "flag": "🇸🇩"
        },
        {
            "name": "Suriname",
            "name_es": "Surinam",
            "continent_en": "South America",
            "continent_es": "América del Sur",
            "capital_en": "Paramaribo",
            "capital_es": "Paramaribo",
            "dial_code": "+597",
            "id": "SR",
            "code_3": "SUR",
            "tld": ".sr",
            "km2": 163820,
            "flag": "🇸🇷"
        },
        {
            "name": "Svalbard and Jan Mayen",
            "name_es": "Svalbard y Jan Mayen",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Longyearbyen",
            "capital_es": "Longyearbyen",
            "dial_code": "+47",
            "id": "SJ",
            "code_3": "SJM",
            "tld": ".sj",
            "km2": 62422,
            "flag": "🇸🇯"
        },
        {
            "name": "Sweden",
            "name_es": "Suecia",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Stockholm",
            "capital_es": "Estocolmo",
            "dial_code": "+46",
            "id": "SE",
            "code_3": "SWE",
            "tld": ".se",
            "km2": 450295,
            "flag": "🇸🇪"
        },
        {
            "name": "Switzerland",
            "name_es": "Suiza",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Bern",
            "capital_es": "Berna",
            "dial_code": "+41",
            "id": "CH",
            "code_3": "CHE",
            "tld": ".ch",
            "km2": 41284,
            "flag": "🇨🇭"
        },
        {
            "name": "Syria",
            "name_es": "Siria",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Damascus",
            "capital_es": "Damasco",
            "dial_code": "+963",
            "id": "SY",
            "code_3": "SYR",
            "tld": ".sy",
            "km2": 185180,
            "flag": "🇸🇾"
        },
        {
            "name": "Taiwan",
            "name_es": "Taiwán",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Taipei",
            "capital_es": "Taipei",
            "dial_code": "+886",
            "id": "TW",
            "code_3": "TWN",
            "tld": ".tw",
            "km2": 36193,
            "flag": "🇹🇼"
        },
        {
            "name": "Tajikistan",
            "name_es": "Tayikistán",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Dushanbe",
            "capital_es": "Dusambé",
            "dial_code": "+992",
            "id": "TJ",
            "code_3": "TJK",
            "tld": ".tj",
            "km2": 143100,
            "flag": "🇹🇯"
        },
        {
            "name": "Tanzania",
            "name_es": "Tanzania",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Dodoma",
            "capital_es": "Dodoma",
            "dial_code": "+255",
            "id": "TZ",
            "code_3": "TZA",
            "tld": ".tz",
            "km2": 945087,
            "flag": "🇹🇿"
        },
        {
            "name": "Thailand",
            "name_es": "Tailandia",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Bangkok",
            "capital_es": "Bangkok",
            "dial_code": "+66",
            "id": "TH",
            "code_3": "THA",
            "tld": ".th",
            "km2": 513120,
            "flag": "🇹🇭"
        },
        {
            "name": "Timor-Leste",
            "name_es": "Timor-Leste",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Dili",
            "capital_es": "Dili",
            "dial_code": "+670",
            "id": "TL",
            "code_3": "TLS",
            "tld": ".tl",
            "km2": 14874,
            "flag": "🇹🇱"
        },
        {
            "name": "Togo",
            "name_es": "Togo",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Lome",
            "capital_es": "Lomé",
            "dial_code": "+228",
            "id": "TG",
            "code_3": "TGO",
            "tld": ".tg",
            "km2": 56785,
            "flag": "🇹🇬"
        },
        {
            "name": "Tokelau",
            "name_es": "Tokelau",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Fakaofo",
            "capital_es": "Fakaofo",
            "dial_code": "+690",
            "id": "TK",
            "code_3": "TKL",
            "tld": ".tk",
            "km2": 12,
            "flag": "🇹🇰"
        },
        {
            "name": "Tonga",
            "name_es": "Tonga",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Nuku'alofa",
            "capital_es": "Nuku'alofa",
            "dial_code": "+676",
            "id": "TO",
            "code_3": "TON",
            "tld": ".to",
            "km2": 747,
            "flag": "🇹🇴"
        },
        {
            "name": "Trinidad and Tobago",
            "name_es": "Trinidad y Tobago",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Port of Spain",
            "capital_es": "Puerto España",
            "dial_code": "+1868",
            "id": "TT",
            "code_3": "TTO",
            "tld": ".tt",
            "km2": 5130,
            "flag": "🇹🇹"
        },
        {
            "name": "Tristan da Cunha",
            "name_es": "Tristán de Acuña",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Edinburgh of the Seven Seas",
            "capital_es": "Edinburgh of the Seven Seas",
            "dial_code": "+290",
            "id": "TA",
            "code_3": "SHN",
            "tld": ".ta",
            "km2": 207,
            "flag": "🇹🇦"
        },
        {
            "name": "Tunisia",
            "name_es": "Túnez",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Tunis",
            "capital_es": "Túnez",
            "dial_code": "+216",
            "id": "TN",
            "code_3": "TUN",
            "tld": ".tn",
            "km2": 163610,
            "flag": "🇹🇳"
        },
        {
            "name": "Turkmenistan",
            "name_es": "Turkmenistán",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Ashgabat",
            "capital_es": "Ashgabat",
            "dial_code": "+993",
            "id": "TM",
            "code_3": "TKM",
            "tld": ".tm",
            "km2": 488100,
            "flag": "🇹🇲"
        },
        {
            "name": "Turks and Caicos Islands",
            "name_es": "Islas Turcas y Caicos",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Cockburn Town",
            "capital_es": "Cockburn Town",
            "dial_code": "+1649",
            "id": "TC",
            "code_3": "TCA",
            "tld": ".tc",
            "km2": 948,
            "flag": "🇹🇨"
        },
        {
            "name": "Tuvalu",
            "name_es": "Tuvalu",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Funafuti",
            "capital_es": "Funafuti",
            "dial_code": "+688",
            "id": "TV",
            "code_3": "TUV",
            "tld": ".tv",
            "km2": 26,
            "flag": "🇹🇻"
        },
        {
            "name": "Türkiye",
            "name_es": "Turquía",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Ankara",
            "capital_es": "Ankara",
            "dial_code": "+90",
            "id": "TR",
            "code_3": "TUR",
            "tld": ".tr",
            "km2": 783562,
            "flag": "🇹🇷"
        },
        {
            "name": "Uganda",
            "name_es": "Uganda",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Kampala",
            "capital_es": "Kampala",
            "dial_code": "+256",
            "id": "UG",
            "code_3": "UGA",
            "tld": ".ug",
            "km2": 241550,
            "flag": "🇺🇬"
        },
        {
            "name": "Ukraine",
            "name_es": "Ucrania",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Kiev",
            "capital_es": "Kiev",
            "dial_code": "+380",
            "id": "UA",
            "code_3": "UKR",
            "tld": ".ua",
            "km2": 603500,
            "flag": "🇺🇦"
        },
        {
            "name": "United Arab Emirates",
            "name_es": "Emiratos Árabes Unidos",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Abu Dhabi",
            "capital_es": "Abu Dhabi",
            "dial_code": "+971",
            "id": "AE",
            "code_3": "ARE",
            "tld": ".ae",
            "km2": 83600,
            "flag": "🇦🇪"
        },
        {
            "name": "United Kingdom",
            "name_es": "Reino Unido",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "London",
            "capital_es": "Londres",
            "dial_code": "+44",
            "id": "GB",
            "code_3": "GBR",
            "tld": ".uk",
            "km2": 242900,
            "flag": "🇬🇧"
        },
        {
            "name": "United States",
            "name_es": "Estados Unidos",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Washington, D.C.",
            "capital_es": "Washington, D.C.",
            "dial_code": "+1",
            "id": "US",
            "code_3": "USA",
            "tld": ".us",
            "km2": 9833520,
            "flag": "🇺🇸"
        },
        {
            "name": "Uruguay",
            "name_es": "Uruguay",
            "continent_en": "South America",
            "continent_es": "América del Sur",
            "capital_en": "Montevideo",
            "capital_es": "Montevideo",
            "dial_code": "+598",
            "id": "UY",
            "code_3": "URY",
            "tld": ".uy",
            "km2": 181034,
            "flag": "🇺🇾"
        },
        {
            "name": "Uzbekistan",
            "name_es": "Uzbekistán",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Tashkent",
            "capital_es": "Tashkent",
            "dial_code": "+998",
            "id": "UZ",
            "code_3": "UZB",
            "tld": ".uz",
            "km2": 447400,
            "flag": "🇺🇿"
        },
        {
            "name": "Vanuatu",
            "name_es": "Vanuatu",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Port Vila",
            "capital_es": "Port Vila",
            "dial_code": "+678",
            "id": "VU",
            "code_3": "VUT",
            "tld": ".vu",
            "km2": 12189,
            "flag": "🇻🇺"
        },
        {
            "name": "Vatican City State",
            "name_es": "Ciudad del Vaticano",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Vatican City",
            "capital_es": "Ciudad del Vaticano",
            "dial_code": "+379",
            "id": "VA",
            "code_3": "VAT",
            "tld": ".va",
            "km2": 0.49,
            "flag": "🇻🇦"
        },
        {
            "name": "Venezuela",
            "name_es": "Venezuela",
            "continent_en": "South America",
            "continent_es": "América del Sur",
            "capital_en": "Caracas",
            "capital_es": "Caracas",
            "dial_code": "+58",
            "id": "VE",
            "code_3": "VEN",
            "tld": ".ve",
            "km2": 916445,
            "flag": "🇻🇪"
        },
        {
            "name": "Vietnam",
            "name_es": "Vietnam",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Hanoi",
            "capital_es": "Hanoi",
            "dial_code": "+84",
            "id": "VN",
            "code_3": "VNM",
            "tld": ".vn",
            "km2": 331212,
            "flag": "🇻🇳"
        },
        {
            "name": "Virgin Islands, British",
            "name_es": "Islas Vírgenes Británicas",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Road Town",
            "capital_es": "Road Town",
            "dial_code": "+1284",
            "id": "VG",
            "code_3": "VGB",
            "tld": ".vg",
            "km2": 151,
            "flag": "🇻🇬"
        },
        {
            "name": "Virgin Islands, U.S.",
            "name_es": "Islas Vírgenes de los Estados Unidos",
            "continent_en": "North America",
            "continent_es": "América del Norte",
            "capital_en": "Charlotte Amalie",
            "capital_es": "Charlotte Amalie",
            "dial_code": "+1340",
            "id": "VI",
            "code_3": "VIR",
            "tld": ".vi",
            "km2": 347,
            "flag": "🇻🇮"
        },
        {
            "name": "Wales",
            "name_es": "Gales",
            "continent_en": "Europe",
            "continent_es": "Europa",
            "capital_en": "Cardiff",
            "capital_es": "Cardiff",
            "dial_code": "+44",
            "id": "WA",
            "code_3": "WLS",
            "tld": ".uk",
            "km2": 21218,
            "flag": "🏴󠁧󠁢󠁷󠁬󠁳󠁿"
        },
        {
            "name": "Wallis and Futuna",
            "name_es": "Wallis y Futuna",
            "continent_en": "Oceania",
            "continent_es": "Oceanía",
            "capital_en": "Mata-Utu",
            "capital_es": "Mata-Utu",
            "dial_code": "+681",
            "id": "WF",
            "code_3": "WLF",
            "tld": ".wf",
            "km2": 142,
            "flag": "🇼🇫"
        },
        {
            "name": "Western Sahara",
            "name_es": "Sahara Occidental",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "El Aaiún",
            "capital_es": "El Aaiún",
            "dial_code": "+212",
            "id": "EH",
            "code_3": "ESH",
            "tld": ".eh",
            "km2": 266000,
            "flag": "🇪🇭"
        },
        {
            "name": "Yemen",
            "name_es": "Yemen",
            "continent_en": "Asia",
            "continent_es": "Asia",
            "capital_en": "Sana'a",
            "capital_es": "Sana'a",
            "dial_code": "+967",
            "id": "YE",
            "code_3": "YEM",
            "tld": ".ye",
            "km2": 527968,
            "flag": "🇾🇪"
        },
        {
            "name": "Zambia",
            "name_es": "Zambia",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Lusaka",
            "capital_es": "Lusaka",
            "dial_code": "+260",
            "id": "ZM",
            "code_3": "ZMB",
            "tld": ".zm",
            "km2": 752612,
            "flag": "🇿🇲"
        },
        {
            "name": "Zimbabwe",
            "name_es": "Zimbabue",
            "continent_en": "Africa",
            "continent_es": "África",
            "capital_en": "Harare",
            "capital_es": "Harare",
            "dial_code": "+263",
            "id": "ZW",
            "code_3": "ZWE",
            "tld": ".zw",
            "km2": 390757,
            "flag": "🇿🇼"
        }
    ];

    var myVar = setTimeout(load, 2000);
    const openModalButtons = document.querySelectorAll('[data-modal-target]')
    const closeModalButtons = document.querySelectorAll('[data-close-button]')
    const esModalButtons = document.querySelectorAll('[data-es-button]')
    const enModalButtons = document.querySelectorAll('[data-en-button]')
    var info_lenguage = document.getElementById("info_lenguage")
    const overlay = document.getElementById('overlay')
    const loadoverlay_ = document.getElementById('loadoverlay')
    const webCheckoutContent = document.getElementById('web-checkout-content')
    const movil = document.getElementById('movil');
    const movil_header = document.getElementById('movil_header');
    const cardjsmincss = document.getElementById('cardjsmincss');
    const style_min = document.getElementById('style_min');
    loadoverlay_.style.display = 'none'
    if (webCheckoutContent) webCheckoutContent.style.display = 'block'
    const mainContainer = document.getElementById('movil_mainContainer')
    const movil_modal = document.getElementById('movil_modal')
    const movil_footer = document.getElementById('movil_footer')
    const mdlInactivityTime = document.getElementById('mdlInactivityTime')
    const cancelT_modal = document.getElementById('cancelT_modal')
    const mdlTimeExpired = document.getElementById('mdlTimeExpired')
    mdlInactivityTime.style.display = 'none'
    cancelT_modal.style.display = 'none'
    mdlTimeExpired.style.display = 'none'
    var contador = 0;
    var loading;
    function alertar() {
        try {
            var string = CryptoJS.AES.encrypt(CryptoJS.enc.Utf8.parse('traysads'), 'secrasdasdaset').toString();
        } catch (error) {
            console.log(error);
            location.reload();
        }
    }
    const INACTIVITY_TIME = 6e4
        , TIMER_EXTEND_SESSION = 45;
    var form = document.getElementById('form-action');
    let interval, counter = TIMER_EXTEND_SESSION, urlRedirect = form.action + "&canceled=1", counterDos = 0;
    const idleTimeouts = () => {
        let e;
        function t() {
            clearTimeout(e),
                e = setTimeout(inactivityTimes, INACTIVITY_TIME)
            mdlTimeExpired.style.display = 'none';
            mdlInactivityTime.style.display = 'none';
            clearInterval(interval)
            counter = TIMER_EXTEND_SESSION
        }
        null == $("#trx-finish-status").val() && (window.onload = t,
            window.ontouchstart = t,
            window.onclick = t,
            window.onkeypress = t,
            window.addEventListener("scroll", t, !0))
    }, inactivityTimes = () => {
        mdlTimeExpired.style.display = 'none';
        mdlInactivityTime.style.display = 'flex',
            $("#counterInactivity").text(counter),
            showMdl("mdlInactivityTime"),
            interval = setInterval(counterInactivityTime, 1e3)
    };
    function counterInactivityTime() {
        0 === counter && (resetInterval(),
            closeTimeExpired()),
            counter--,
            $("#counterInactivity").text(counter)
    }
    function resetInterval() {
        mdlTimeExpired.style.display = 'flex',
            mdlInactivityTime.style.display = 'none',
            clearInterval(interval),
            hideMdl("mdlInactivityTime"),
            showMdl("mdlTimeExpired"),
            counter = TIMER_EXTEND_SESSION,
            $("#counterInactivity").text(counter)
    }

    closeTimeExpired = () => {
        let e = [];
    }
        , showMdl = e => {
            let t = [`#${e}`, `#${e}Body`];
            addRemoveClass(t, "dn", !1),
                addRemoveClass(t, "op")
        }
        , hideMdl = e => {
            let t = [`#${e}`, `#${e}Body`];
            addRemoveClass(t, "op", !1),
                addRemoveClass(t, "dn")
        }, addRemoveClass = (e, t, c = !0) => {
            c ? e.forEach(e => {
                $(e).addClass(t)
            }
            ) : e.forEach(e => {
                $(e).removeClass(t)
            }
            )
        };
    $("#btnMdlTimeExpired").click(() => {
        window.location = urlRedirect;
    });
    $(document).ready(function () {

        const epayco_title = document.getElementById('epayco_title')
        const button_epayco = document.getElementById('button_epayco')
        let first_widtht = $(window).width();
        if (first_widtht > 425) {
            if (mainContainer) {
                mainContainer.className = "";
            }
        } else {
            if (epayco_title) epayco_title.hidden = true;
            if (button_epayco) button_epayco.hidden = true;
            // Show the hidden movil script instead of creating a new one
            const movilScript = document.getElementById('movil');
            if (movilScript) {
                movilScript.hidden = false;
            }
            let link = document.createElement('link');
            let linkValue = style_min.innerText.replace(/ /g, "");
            link.rel = "stylesheet";
            link.type = "text/css";
            link.href = linkValue;
            cardjsmincss.appendChild(link);
            let modal = document.getElementById('centered');
            modal.hidden = true;
            overlay.hidden = true;
            cargarMovil()
        }

        alertar()

        idleTimeouts();


        divFoo = document.getElementById('foo');
        divSample = document.getElementById('countryName');
        divResult = document.getElementById('result');
        divFlag = document.getElementById('flag');

        let countries = [];
        try {
            const el = document.getElementById('countriesData');
            if (el && el.textContent.trim()) {
                countries = JSON.parse(el.textContent);
            }
        } catch (e) {
            countries = [];
        }

        if (!countries || !countries.length) {
            if (window.EPAYCO_COUNTRIES && Array.isArray(window.EPAYCO_COUNTRIES)) {
                countries = window.EPAYCO_COUNTRIES;
            } else if (window.countriesJson && Array.isArray(window.countriesJson)) {
                countries = window.countriesJson;
            } else if (typeof countryList !== 'undefined' && Array.isArray(countryList)) {
                countries = countryList;
            }
        }


        // Detectar idioma de la página 
        const pageLangEl = document.getElementById('lang_epayco');
        const pageLang = pageLangEl && pageLangEl.textContent ? pageLangEl.textContent.trim().toLowerCase() : '';

        function createSelect(n, selectedCode) {
            if (!divFoo) return;
            divFoo.innerHTML = '';
            const selectedIndex = Math.max(0, n.findIndex(c => (c.id || '').toLowerCase() === (selectedCode || '').toLowerCase()));

            const updateSelected = (idx) => {
                const c = n[idx];
                if (!c) return;
                let displayName;
                if (pageLang && pageLang.indexOf('es') === 0) {
                    displayName = c.name_es || c.name;
                } else if (pageLang && pageLang.indexOf('en') === 0) {
                    displayName = c.name || c.name_es;
                } else {
                    displayName = c.name_es || c.name;
                }
                if (divSample) {
                    divSample.innerText = displayName;
                    divSample.id = c.id;
                }
                if (divResult) {
                    divResult.innerText = c.id;
                }
                if (divFlag) {

                    const baseClass = 'flag flag-icon-background';
                    divFlag.className = baseClass + ' flag-icon-' + (c.id || '').toLowerCase();

                 
                    const isUrl = typeof c.flag === 'string' && c.flag.indexOf('http') === 0;
                    const existingImg = divFlag.querySelector('img.flag-repl');
                    const existingEmoji = divFlag.querySelector('span.flag-emoji');

                    const countryCode = c.id ? (c.id + '').toLowerCase() : '';
                    if (countryCode && countryCode.length === 2) {
                        if (existingEmoji) existingEmoji.remove();
                        let img = existingImg;
                        if (!img) {
                            img = document.createElement('img');
                            img.className = 'flag-repl';
                            img.alt = displayName + ' flag';
                            img.style.display = 'inline-block';
                            img.style.width = '23px';
                            img.style.height = '16px';
                            img.style.objectFit = 'cover';
                            img.style.marginRight = '8px';
                            img.crossOrigin = 'anonymous';
                            divFlag.insertBefore(img, divFlag.firstChild);
                        }
                        img.onerror = function () {
                            this.remove();
                            const span = document.createElement('span');
                            span.className = 'flag-emoji';
                            span.style.display = 'inline-block';
                            span.style.fontSize = '18px';
                            span.style.lineHeight = '16px';
                            span.style.marginRight = '8px';
                            span.textContent = c.flag || '';
                            try { divFlag.insertBefore(span, divFlag.firstChild); } catch (e) { }
                        };
                        img.src = 'https://flagcdn.com/w40/' + countryCode + '.png';
                    } else {
                        if (existingImg) existingImg.remove();
                        let span = existingEmoji;
                        if (!span) {
                            span = document.createElement('span');
                            span.className = 'flag-emoji';
                            span.style.display = 'inline-block';
                            span.style.fontSize = '18px';
                            span.style.lineHeight = '16px';
                            span.style.marginRight = '8px';
                            divFlag.insertBefore(span, divFlag.firstChild);
                        }
                        span.textContent = c.flag || '';
                    }
                }
            };

            n.forEach((country, idx) => {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.href = '#';
                a.className = country.flag || '';
                a.id = country.id || '';

                a.style.display = 'flex';
                a.style.flexDirection = 'row';
                a.style.alignItems = 'center';
                a.style.gap = '8px';
                a.style.padding = '6px 10px';
                a.style.textAlign = 'left';

             
                let displayName;
                if (pageLang && pageLang.indexOf('es') === 0) {
                    displayName = country.name_es || country.name;
                } else if (pageLang && pageLang.indexOf('en') === 0) {
                    displayName = country.name || country.name_es;
                } else {
                    displayName = country.name_es || country.name;
                }
                let flagEl;
                const countryCode = country.id ? (country.id + '').toLowerCase() : '';
                if (countryCode && countryCode.length === 2) {
                    const img = document.createElement('img');
                    img.alt = displayName + ' flag';
                    img.width = 23;
                    img.height = 16;
                    img.style.objectFit = 'cover';
                    img.style.marginRight = '10px';
                    img.crossOrigin = 'anonymous';
                    img.src = 'https://flagcdn.com/w40/' + countryCode + '.png';
                    // si falla la carga, mostrar emoji del JSON como fallback
                    img.onerror = function () {
                        this.remove();
                        const span = document.createElement('span');
                        span.className = 'flag-emoji';
                        span.textContent = country.flag || '';
                        span.style.fontSize = '18px';
                        span.style.lineHeight = '16px';
                        span.style.marginRight = '10px';
                        span.style.display = 'inline-block';
                        try { a.insertBefore(span, a.firstChild); } catch (e) { }
                    };
                    flagEl = img;
                } else {
                    flagEl = document.createElement('span');
                    flagEl.className = 'flag-emoji';
                    flagEl.textContent = country.flag || '';
                    flagEl.style.fontSize = '18px';
                    flagEl.style.lineHeight = '16px';
                    flagEl.style.marginRight = '10px';
                    flagEl.style.display = 'inline-block';
                }

                const span = document.createElement('span');
                span.textContent = displayName;
                span.style.display = 'inline-block';
                span.style.fontSize = '14px';
                span.style.color = '#222';
                span.style.lineHeight = '1.1';

                a.appendChild(flagEl);
                a.appendChild(span);

                a.addEventListener('click', function (ev) {
                    ev.preventDefault();
                    ev.stopPropagation();
                    updateSelected(idx);

                    try { $('.dropdown dd ul').toggle(); } catch (e) { }
                });

                li.appendChild(a);
                divFoo.appendChild(li);
            });


            updateSelected(selectedIndex >= 0 ? selectedIndex : 0);
        }

        const serverCode = (document.getElementById('result') && document.getElementById('result').innerText) ? document.getElementById('result').innerText : 'CO';
        createSelect(countries, serverCode);

        const monthInput = document.getElementById('month-value');
        if (monthInput) {
            // mejor soporte en móviles
            monthInput.setAttribute('inputmode', 'numeric');
            monthInput.setAttribute('pattern', '[0-9]*');
            // input event: elimina no dígitos y trunca a 2
            monthInput.addEventListener('input', function () {
                const cleaned = this.value.replace(/\D+/g, '').slice(0, 2);
                if (this.value !== cleaned) this.value = cleaned;
            });
            // evitar pegar texto no numérico
            monthInput.addEventListener('paste', function (e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                const cleaned = paste.replace(/\D+/g, '').slice(0, 2);
                document.execCommand('insertText', false, cleaned);
            });
            // bloquear teclas no numéricas (permite control/navigation keys)
            monthInput.addEventListener('keydown', function (e) {
                const allowed = ['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'Delete'];
                if (allowed.indexOf(e.key) !== -1) return;
                // si es carácter y no es dígito, bloquear
                if (e.key.length === 1 && /\D/.test(e.key)) e.preventDefault();
                // evitar superar 2 dígitos cuando no hay selección
                if (this.value.length >= 2 && this.selectionStart === this.selectionEnd && e.key.length === 1) {
                    e.preventDefault();
                }
            });
        }

        const yearInput = document.getElementById('year-value');
        if (yearInput) {
            // mejor soporte en móviles
            yearInput.setAttribute('inputmode', 'numeric');
            yearInput.setAttribute('pattern', '[0-9]*');
            // input event: elimina no dígitos y trunca a 4
            yearInput.addEventListener('input', function () {
                const cleaned = this.value.replace(/\D+/g, '').slice(0, 4);
                if (this.value !== cleaned) this.value = cleaned;
            });
            // evitar pegar texto no numérico
            yearInput.addEventListener('paste', function (e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                const cleaned = paste.replace(/\D+/g, '').slice(0, 4);
                document.execCommand('insertText', false, cleaned);
            });
            // bloquear teclas no numéricas (permite control/navigation keys)
            yearInput.addEventListener('keydown', function (e) {
                const allowed = ['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'Delete'];
                if (allowed.indexOf(e.key) !== -1) return;
                // si es carácter y no es dígito, bloquear
                if (e.key.length === 1 && /\D/.test(e.key)) e.preventDefault();
                // evitar superar 4 dígitos cuando no hay selección
                if (this.value.length >= 4 && this.selectionStart === this.selectionEnd && e.key.length === 1) {
                    e.preventDefault();
                }
            });
        }


    });

    function cargarMovil() {
        setTimeout(function () {
            mdlInactivityTime.style.display = 'flex'
            cancelT_modal.style.display = 'flex'
            mdlTimeExpired.style.display = 'flex'
           
            if (mainContainer) {
                mainContainer.className = "mainContainer";
                mainContainer.style.position = "fixed";
                mainContainer.style.top = "0px"; 
                mainContainer.style.left = "0px";
                mainContainer.style.height = "100%";
                mainContainer.style.zIndex = "999999";
            }
            movil_modal.hidden = false;
            movil_footer.hidden = false;
        }, 3000);
    }

   
    $(".dropdown a").click(function () {
        $(".dropdown dd ul").toggle();
    });

    $(".dropdown dd ul").click(function (e) {
        var $clicked = $(e.target);
        var texts = $clicked[0].innerText;
        var id = $clicked[0].id;
        var flag = $clicked[0].className;
        divSample.innerText = texts;
        divSample.id = id;
        divFlag.innerText = flag;
        $(".dropdown dd ul").toggle();
    });

    // Cerrar dropdown cuando se hace clic fuera
    $(document).click(function (e) {
        var target = e.target;
        // Si el click no es dentro del dropdown, cerrarlo
        if (!$(target).closest("#sample").length && !$(target).closest(".dropdown").length) {
            $(".dropdown dd ul").hide();
        }
    });

    if ($("#lang_epayco").text() == 'en') {
        document.getElementById('esButton').classList.remove('bgcolor')
        document.getElementById('esButton').classList.remove('active')
        $("#info_es").hide();
        $("#pagar_es").hide();
        $("#pagar_logo_es").hide();
        $("#info_en").show();
        $("#pagar_en").show();
        $("#pagar_logo_en").show();

        document.getElementById('enButton').classList.add('bgcolor')
        document.getElementById('enButton').classList.add('active')
    } else {
        document.getElementById('enButton').classList.remove('bgcolor')
        document.getElementById('enButton').classList.remove('active')
        $("#info_en").hide();
        $("#pagar_en").hide();
        $("#pagar_logo_en").hide();
        $("#info_es").show();
        $("#pagar_es").show();
        $("#pagar_logo_es").show();
        document.getElementById('esButton').classList.add('bgcolor')
        document.getElementById('esButton').classList.add('active')
    }

    function load() {
        const modal = document.getElementById('centered')
        openModal(modal)
    }

    openModalButtons.forEach(button => {
        button.addEventListener('click', () => {
            const modal = document.querySelector(button.dataset.modalTarget)
            openModal(modal)
        })
    })

    overlay.addEventListener('click', (button) => {
        const modals = button.closest('.centered.active')
        modals.forEach(modal => {
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
            $("#pagar_logo_en").hide();
            $("#info_es").show();
            $("#pagar_es").show();
            $("#pagar_logo_es").show();
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
            $("#pagar_logo_es").hide();
            $("#info_en").show();
            $("#pagar_en").show();
            $("#pagar_logo_en").show();
            document.getElementById('enButton').classList.add('bgcolor')
            document.getElementById('enButton').classList.add('active')
        })
    })

    function openModal(modal) {
        if (modal == null) return
        modal.classList.add('active')
        overlay.classList.add('active')
    }

    function closeModal(modal) {
        if (modal == null) return
        modal.classList.remove('active')
        overlay.classList.remove('active')
    }
    async function getPosts(e) {
        return await new Promise(function (resolve, reject) {
            var r = ePayco._utils.parseForm(e);
            let data = btoa(JSON.stringify(r.card));
            resolve(data)
            /*ePayco.token.create(e, function(error, token) {
                loading=false;
                if(!error) {
                    if(error != undefined){
                        enviarData(token)
                    }else{
                        if(token){
                            enviarData(token)
                        }else{
                            reject("No pudimos procesar la transacción, por favor contacte con soporte.")
                            }
                        }
                } else {
                    if(!error || error !== undefined) {
                        resolve(token)
                    } else {
                        try {
                            if(error != undefined){
                                if(!error.status){
                                    let message = error.data.description;
                                    reject(message)
                                }else{
                                    console.error(error)
                                }
                            }else{
                                reject("No pudimos procesar la transacción, por favor contacte con soporte.")
                            }
                        } catch(e) {
                            reject('No se pudo realizar el pago, por favor reintente nuevamente')
                        } 
                    }
                }
            });*/
        });
    }
    $('#send-form').click(function () {
        $('#token-credit').submit();
    });
    const $checkout_movil_fomr = $('#form-action');
    $checkout_movil_fomr.on('submit', function (event) {
        event.preventDefault();
    });
    const $checkout_form = $('#token-credit');
    $checkout_form.on('submit', function (event) {
        event.preventDefault();
        var key = $("#p_c").text();
        var key_p = $("#p_p").text();
        var lang = $("#lang_epayco").text();
        ePayco.setPublicKey(key);
        ePayco.setLanguage(lang);
        var $form = $(this);
        // Name rule aligned to: mínimo 2 letras (cuenta letras, incluye acentos y ñ/ü)
        var nameLetters = (document.getElementById('the-card-name-element').value.match(/[A-Za-zÁÉÍÓÚÜÑáéíóúüñ]/g) || []).length;
        var number = document.getElementById('the-card-number-element').value.replace(/[^0-9]/g, "").length;
        var month = document.getElementById('month-value').value.replace(/[^0-9]/g, "").length;
        var year = document.getElementById('year-value').value.replace(/[^0-9]/g, "").length;
        var cvc = document.getElementById('card_cvc').value.replace(/[ -]/g, "").length;
        $("#web-checkout-content").removeClass("animated shake");
        if (number <= 14 || nameLetters < 2 || month < 1 || year < 2 || cvc < 3) {
            $("#web-checkout-content").addClass("animated shake");
            if (number <= 14) {
                document.getElementById('the-card-number-element').classList.add('inputerror')
            }
            if (nameLetters < 2) {
                document.getElementById('the-card-name-element').classList.add('inputerror')
            }
            if (month < 1) {
                var expEl = document.getElementById('expInput');
                if (expEl) expEl.classList.add('inputerror');
            }
            if (year < 2) {
                var expEl2 = document.getElementById('expInput');
                if (expEl2) expEl2.classList.add('inputerror');
            }
            if (cvc < 3) {
                document.getElementById('cvc_').classList.add('inputerror')
            }
        } else {
            if (!loading) {
                loadoverlay_.style.display = 'block';
                if (webCheckoutContent) webCheckoutContent.style.display = 'none';
                loading = true;
                getPosts($form).then(r => {
                    contador = 0;
                    $checkout_form.find('input[name=my-custom-form-field__card-number]').remove();
                    $checkout_form.find('input[name=cvc]').remove();
                    $checkout_form.find('input[name=year]').remove();
                    $checkout_form.find('input[name=month]').remove();
                    $checkout_form.find('input[name=card_email]').remove();
                    $checkout_form.find('input[name=card_number]').remove();
                    var form = document.getElementById('token-credit');
                    var hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'epaycoToken');
                    hiddenInput.setAttribute('value', r);
                    form.appendChild(hiddenInput);
                    form.submit();
                }).catch((e) => {
                    console.log('Algo saliò mal!');
                    loadoverlay_.style.display = 'none';
                    if (webCheckoutContent) webCheckoutContent.style.display = 'block';
                    alert(e)
                });
            }
        }
    });

    async function enviarData(r) {
        setTimeout(function () {
            $checkout_form.find('input[name=my-custom-form-field__card-number]').remove();
            $checkout_form.find('input[name=cvc]').remove();
            $checkout_form.find('input[name=year]').remove();
            $checkout_form.find('input[name=month]').remove();
            $checkout_form.find('input[name=card_email]').remove();
            $checkout_form.find('input[name=card_number]').remove();
            var form = document.getElementById('token-credit');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'epaycoToken');
            hiddenInput.setAttribute('value', r);
            form.appendChild(hiddenInput);
            form.submit();
        }, 3000);
    }

});

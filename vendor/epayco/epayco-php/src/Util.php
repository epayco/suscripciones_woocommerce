<?php

namespace Epayco;

use Epayco\Utils\PaycoAes;

class Util
{
<<<<<<< HEAD:vendor/epayco/epayco-php/src/Util.php
        public function setKeys($array)
        {
            $aux = array();
            $file = dirname(__FILE__) . "/Utils/key_lang.json";
            $values = json_decode(file_get_contents($file), true);
            foreach ($array as $key => $value) {
                if (array_key_exists($key, $values)) {
                    $aux[$values[$key]] = $value;
                } else {
                    $aux[$key] = $value;
                }
=======
    public function setKeys($array)
    {
        $aux = array();
        $file = dirname(__FILE__). "/Utils/key_lang.json";
        $values = json_decode(file_get_contents($file), true);
        foreach ($array as $key => $value) {
            if (array_key_exists($key, $values)) {
                $aux[$values[$key]] = $value;
            } else {
                $aux[$key] = $value;
>>>>>>> 0422165f4ec4576b068d34a57a4c3f86bde0a55a:lib/vendor/epayco/epayco-php/src/Util.php
            }
        }
        return $aux;
    }

<<<<<<< HEAD:vendor/epayco/epayco-php/src/Util.php
        public function setKeys_apify($array)
        {
            $aux = array();
            $file = dirname(__FILE__) . "/Utils/key_lang_apify.json";
            $values = json_decode(file_get_contents($file), true);
            foreach ($array as $key => $value) {
                if (array_key_exists($key, $values)) {
                    $aux[$values[$key]] = $value;
                } else {
                    $aux[$key] = $value;
                }
=======

    public function setKeys_apify($array)
    {
        $aux = array();
        $file = dirname(__FILE__). "/Utils/key_lang_apify.json";
        $values = json_decode(file_get_contents($file), true);
        foreach ($array as $key => $value) {
            if (array_key_exists($key, $values)) {
                $aux[$values[$key]] = $value;
            } else {
                $aux[$key] = $value;
>>>>>>> 0422165f4ec4576b068d34a57a4c3f86bde0a55a:lib/vendor/epayco/epayco-php/src/Util.php
            }
        }
        return $aux;
    }


    public function mergeSet($data, $test, $lang, $private_key, $api_key, $cash)
    {
        $data["ip"] = isset($data["ip"]) ? $data["ip"] : getHostByName(getHostName());
        $data["test"] = $test;

        /**
         * Init AES
         * @var PaycoAes
         */

        $aes = new PaycoAes($private_key, Client::IV, $lang);
        if (!$cash) {
            $data = $aes->encryptArray($data);
        }
        $adddata = array(
            "public_key" => $api_key,
            "i" => base64_encode(Client::IV),
            "enpruebas" => $aes->encrypt($test),
            "lenguaje" => Client::LENGUAGE,
            "p" => "",
        );
        return array_merge($data, $adddata);
    }
}
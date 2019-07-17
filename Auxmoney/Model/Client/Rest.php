<?php
namespace App\modules\Auxmoney\Model\Client;

use \App\modules\Core\Model\Base as BaseModel;
class Rest extends BaseModel {

    /**
     * 
     * @param array $requestData
     * @return mixed
     */
    public function execute(array $requestData) {

dump(2);
        $headers = [
            'Content-Type: application/json',
            sprintf('urlkey: %s', $this->getUrlKey())
        ];

    // cURL-Session initialisieren
        $ch = curl_init();

        // URL setzen
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());

        // Header setzen
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Daten in JSON umwandeln und einfügen
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));

        // SSL deaktivieren
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        // Rückgabe der Antwort als String
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // cURL-Session ausführen
        $result = curl_exec($ch);

        // cURL-Session beenden
        curl_close($ch);
		dump(12);
        return $result;


    }
    
    public function testParam() {
        $result = [
            'url' => $this->getUrl(),
            'urlKey' => $this->getUrlKey()
        ];

        return $result;
    }
    
}


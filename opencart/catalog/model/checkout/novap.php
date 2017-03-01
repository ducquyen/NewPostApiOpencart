<?php
class ModelCheckoutNovap {

    private $API_KEY = 'beccbded8b9472831cffa9e28268fb4f'; // заменить на свой ключ

    private function sendRequest($xml) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://orders.novaposhta.ua/xml.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function getOfficess() {
        $xml = "
                <?xml version=\"1.0\" encoding=\"utf-8\"?>
                <file>
                <auth>{$this->API_KEY}</auth>
                <citywarehouses/>
                </file>";

        $this->cache = md5($xml);
        $offices = $this->cache->get('nova.poshta.' . $this->cache); // запрос к кеш
        if (!empty($offices)) {
            return $offices;
        } else {
            $offices = $this->sendRequest($xml);
            $this->cache->set('nova.poshta.' . $cache, $offices); // сохранение в кеш
            return $offices;
        }//if
    }

    public function getOfficesByCity($city) {
        $xml = "
                <?xml version=\"1.0\" encoding=\"utf-8\"?>
                <file>
                <auth>{$this->API_KEY}</auth>
                <warenhouse/>
                <filter>$city</filter>
                </file>";
        $cache = md5($xml);
        $cities = $this->cache->get('nova.poshta.' . $cache); // запрос в кеш
        if (!empty($cities)) {
            return $cities;
        } else {
            $cities = $this->sendRequest($xml);
            $this->cache->set('nova.poshta.' . $cache, $cities); // сохранение в кеш
            return $cities;
        }//if
    }

    public function callAPI() {
        echo 33333333333333333333333;
        $np = new ModelCheckoutNovap();
        $np->getOfficess(); // получить список городов
        $np->getOfficesByCity("Киев"); // получить список отделений для города "Киев"

    }

}

?>
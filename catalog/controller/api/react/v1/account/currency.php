<?php
class ControllerApiReactV1AccountCurrency extends Controller {
    
     public function initValutes() {
        
        $this->load->model('account/currency');
        $date = date('d/m/Y');

        $url_codes = "http://www.cbr.ru/scripts/XML_daily.asp?date_req=" . $date;
        $xml_codes = simplexml_load_file($url_codes);
        $currency_codes_json = json_encode($xml_codes);
        $currency_codes = json_decode($currency_codes_json,TRUE);
        
        foreach($currency_codes['Valute'] as $currency_code){
            
            $exist = $this->model_account_currency->getValuteByValuteID($currency_code['@attributes']['ID']);

            if(!isset($exist['valuteID'])){        
                $currency_item = array(
                    'valuteID'  => $currency_code['@attributes']['ID'],
                    'numCode'   => $currency_code['NumCode'],
                    'charCode'  => $currency_code['CharCode'],
                    'numCode'   => $currency_code['NumCode'],
                    'nominal'   => $currency_code['Nominal'],
                    'name'      => $currency_code['Name']
                );

                $this->model_account_currency->addValute($currency_item);
            }
            
            $currency_items[] = $currency_item;
        }
    }
        
    public function initCurrencies($date2day = null) {    
   
        $this->load->model('account/currency');
        $date2 = date('d/m/Y');
        
        if(!$date2day){
            $date2day = strtotime(date('d/m/Y')) / 86400;
        }
        
        $date1 = date('d/m/Y', strtotime(date() . " - 30 day"));
        
        $max_date = $this->model_account_currency->getLastCurrenciesDate();

        if($max_date['max']){
            $date1 = date('d/m/Y', ((int)$max_date['max'] + 1) * 86400);
            $date1day = (int)$max_date['max'];
        }

        if($date2day > $date1day){
            $valutes = $this->model_account_currency->getAllValutes();
            foreach($valutes as $valute){
                $url = "http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=" . $date1 . "&date_req2=" . $date2 . "&VAL_NM_RQ=" . $valute['valuteID'];
                $xml = simplexml_load_file($url);
                $json = json_encode($xml);
                $currencies = json_decode($json,TRUE);

                if(!isset($currencies['Record']['@attributes'])){
                    foreach($currencies['Record'] as $currency){

                        $currency_item = array(
                            'valute_id' => $valute['valute_id'],
                            'value'     => str_replace(",", ".", $currency['Value']),
                            'date'      => strtotime(date($currency['@attributes']['Date'])) / 86400,
                        );

                        $this->model_account_currency->addCurrency($currency_item);
                    }
                } else {
                    $currency_item = array(
                        'valute_id' => $valute['valute_id'],
                        'value'     => str_replace(",", ".", $currencies['Record']['Value']),
                        'date'      => strtotime(date($currencies['Record']['@attributes']['Date'])) / 86400,
                    );                   
                    
                    $this->model_account_currency->addCurrency($currency_item);
                }
            }
        }
    }
    
    public function currency() {
        if(isset($this->request->get['valute']) && isset($this->request->get['date1']) && isset($this->request->get['date2'])){
                       
            $valuteID = $this->request->get['valute'];
            $date1 = strtotime(date($this->request->get['date1'])) / 86400;
            $date2 = strtotime(date($this->request->get['date2'])) / 86400;

            if($date1 && $date2 && $valuteID){
                
                $this->initValutes();
                $this->initCurrencies($date2);

                $this->load->model('account/currency');

                $currencies = $this->model_account_currency->getCurrencyByValuteIdDateRange($valuteID, $date1, $date2);

                $data['currency'] = array(
                    'valuteID'  => $currencies[0]['valuteID'],
                    'numCode'    => $currencies[0]['numCode'],
                    'charCode'  => $currencies[0]['charCode'],
                    'valuteID'  => $currencies[0]['valuteID'],
                    'nominal'   => $currencies[0]['nominal'],
                    'name'      => $currencies[0]['name'],
                    'date_from' => $date1 = date('d/m/Y', (int)$date1 * 86400),
                    'date_to'   => $date1 = date('d/m/Y', (int)$date2 * 86400),
                );
                foreach ($currencies as $currency){
                    $data['rates'][] = array(
                        'date' => date('d/m/Y', (int)$currency['date'] * 86400),
                        'value' => $currency['value']
                    );

                }
            } else {
                $data['error'] = "Bad request";
            }
        } else {
            $data['error'] = "Bad request";
        }
            
        if (isset($this->request->server['HTTP_ORIGIN'])) {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Credentials: true');
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers:  Access-Control-Allow-Origin, Access-Control-Allow-Credentials, Content-Type, Authorization, X-Requested-With');
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    
}
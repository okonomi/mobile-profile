<?php
require_once 'HTTP/Request2.php';


class Mobile_Profile_Collector_Softbank_Product
{
    public function scrape()
    {
        try {
            // 機種一覧
            $xml = self::_getXML('http://mb.softbank.jp/mb/shared/xml/hub/product/models.xml');

            $models = array();
            foreach ($xml->item as $item) {
                $models[(string)$item['id']] = array(
                    'group' => (string)$item['g'],
                    'name'  => (string)$item,
                );
            }

            // サービス一覧
            $xml = self::_getXML('http://mb.softbank.jp/mb/shared/xml/hub/service/services.xml');

            $services = array();
            foreach ($xml->item as $item) {
                $services[(string)$item['id']] = (string)$item;
            }

            // サービス/機種 対応一覧
            $xml = self::_getXML('http://mb.softbank.jp/mb/shared/xml/hub/service/models.xml');

            $allows = array();
            foreach ($xml->item as $item) {
                $service_id = (string)$item['id'];

                $allows[$service_id]['name'] = $services[$service_id];

                $model_id_list = explode(',', $item);
                foreach ($model_id_list as $id) {
                    switch ($id) {
                    case '3G':
                        foreach ($models as $model) {
                            if ($model['group'] === 's3') {
                                $allows[$service_id]['model'][] = $model['name'];
                            }
                        }
                        break;
                    case '3G_e1':
                        foreach ($models as $model) {
                            if ($model['group'] === 's3') {
                                if ($model['name'] !== '820T' && $model['name'] !== '821T') {
                                    $allows[$service_id]['model'][] = $model['name'];
                                }
                            }
                        }
                        break;
                    case '2G':
                        foreach ($models as $model) {
                            if ($model['group'] === 's2') {
                                $allows[$service_id]['model'][] = $model['name'];
                            }
                        }
                        break;
                    case 'X':
                        foreach ($models as $model) {
                            if ($model['group'] === 'x') {
                                $allows[$service_id]['model'][] = $model['name'];
                            }
                        }
                        break;
                    default:
                        $allows[$service_id]['model'][] = $models[$id]['name'];
                        break;
                    }
                }
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $allows;
    }

    private function _getXML($url)
    {
        try {
            $request = new HTTP_Request2($url, HTTP_Request2::METHOD_GET);

            $response = $request->send();
            if ($response->getStatus() === 200) {
                return simplexml_load_string($response->getBody());
            } else {
                throw new Exception('Server returned status: '.$response->getStatus());
            }
        } catch (HTTP_Request2_Exception $e) {
            throw $e;
        }
    }
}

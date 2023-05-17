<?php
    /**
     * Firebase CRUD
     * */
    namespace PHPFireStore {
        use Carbon\Carbon;

        /**
         * Class to Manage a Document
         * */ 
        class FireStoreDocument
        {
            private $fields = [];
            private $name = null;
            private $createTime = null;
            private $updateTime = null;
            
            private $funcsInsert = array(
                'string' => 'setString',
                'double' => 'setDouble',
                'array'  => 'setArray',
                'boolean'=> 'setBoolean',
                'integer'=> 'setInteger',
                'null'   => 'nullValue',
                'geo'    => 'geoPointValue',
                'bytes'  => 'bytesValue',
                'reference' => 'referenceValue',
                'timestamp' => 'timestampValue'
            );
    
            public function __construct($json = null,$edit=null)
            {
                if ($json !== null) {
                    $data = json_decode($json, true, 16);
                    // dd($data); // []

                    if(count($data) !=0){
                        if(isset($data['documents']))
                        {
                            $arr = [];
                            foreach ($data['documents'] as $key => $value) {
                                $_c = explode('documents/', $value['name'])[1];
                                $c  = explode('/',$_c);
                                
                                foreach ($value['fields'] as $fieldName => $val) {
                                    $arr[$key][$fieldName] = reset($val);
                                }
    
                                $arr[$key]['id']         = $c[1];
                                $arr[$key]['created']    = $this->comparePastToNow($value['createTime'],null,true);
                                $arr[$key]['updated']    = $this->comparePastToNow($value['updateTime'],null,true);
                                $arr[$key]['createTime'] = $this->comparePastToNow($value['createTime']);
                                $arr[$key]['updateTime'] = $this->comparePastToNow($value['updateTime']);
                            } 
                            $this->fields = $arr;
                        }
                        else 
                        {
                            if(!isset($data['error'])){
                                foreach ($data['fields'] as $fieldName => $value) {
                                    $this->fields[$fieldName] = reset($value);
                                }
        
                                $_c = explode('documents/', $data['name'])[1];
                                $c  = explode('/',$_c);
                                $this->fields['id'] = $c[1];
                                
                                if($edit==null || $edit){
                                    if($edit){
                                        $this->fields['created']    = $this->comparePastToNow($data['createTime'],null,true);
                                        $this->fields['updated']    = $this->comparePastToNow($data['updateTime'],null,true);
                                        $this->fields['createTime'] = $this->comparePastToNow($data['createTime']);
                                        $this->fields['updateTime'] = $this->comparePastToNow($data['updateTime']);
                                    }
                                }
                            }
                            else{
                                $this->fields = [];
                            }
                        }
                    }
                    else{
                        $this->fields = [];
                    }
                }
            }

            public function getDatas()
            {
                return $this->fields;
            }

            public function comparePastToNow($fromDate, $timezone = null, $human = null)
            {
                // ========= Comparer un moment dans le passé à maintenant =================
                if (isset(explode('T', $fromDate)[1])) {
                    //$fromDate = YYYY-MM-dd T 11:45:44.000000Z
                    $c    = explode('T', $fromDate);
                    $dat  = explode('-', $c[0]);
                    $h    = explode('.', $c[1])[0];
                    $heur = explode(":", $h);
                    $date = Carbon::create($dat[0], $dat[1], $dat[2], $heur[0], $heur[1], $heur[2], $timezone);
                } else {
                    //$fromDate = date(YYYY-MM-dd) , date(YYYY-MM-dd HH:mm:ss), YYYY-MM-dd T 11:45:44.000000Z
                    $coup = explode(" ", $fromDate);
                    $dat  = explode("-", $coup[0]);
            
                    // dd($fromDate);
                    if (!isset($coup[1])) {
                        $date = Carbon::create($dat[0], $dat[1], $dat[2], 0, 0, 0, $timezone);
                    } else {
                        $heur = explode(":", $coup[1]);
                        $date = Carbon::create($dat[0], $dat[1], $dat[2], $heur[0], $heur[1], $heur[2], $timezone);
                    }
                }
            
                if ($human == null) {
                    return $date;
                }
                return $date->diffForHumans();
                // =========================================================================
            }

            public function setString($fieldName, $value)
            {
                return $this->fields[$fieldName] = [
                    'stringValue' => $value
                ];
            }

            public function setDouble($fieldName, $value)
            {
                return $this->fields[$fieldName] = [
                    'doubleValue' => floatval($value)
                ];
            }

            public function setArray($fieldName, $value)
            {
                return $this->fields[$fieldName] = [
                    'arrayValue' => $value
                ];
            }

            public function setBoolean($fieldName, $value)
            {
                return $this->fields[$fieldName] = [
                    'booleanValue' => !!$value
                ];
            }

            public function setInteger($fieldName, $value)
            {
                return $this->fields[$fieldName] = [
                    'integerValue' => intval($value)
                ];
            }

            public function setMap($fieldName, $value)
            {
                return $this->fields[$fieldName] = [
                  'mapValue' => $value
                ];			
            }

            public function geoPointValue($fieldName, $value)
            {
                return $this->fields[$fieldName] = [
                  'geoPointValue' => $value
                ];			
            }

            public function bytesValue($fieldName, $value)
            {
                return $this->fields[$fieldName] = [
                  'bytesValue' => $value
                ];			
            }

            public function nullValue($fieldName, $value)
            {
                return $this->fields[$fieldName] = [
                  'nullValue' => $value
                ];			
            }

            public function referenceValue($fieldName, $value)
            {
                return $this->fields[$fieldName] = [
                  'referenceValue' => $value
                ];			
            }

            public function timestampValue($fieldName, $value)
            {
                return $this->fields[$fieldName] = [
                  'timestampValue' => $value
                ];			
            }

            public function funcInsert($funcName,$key,$value)
            { 
                $func = $this->funcsInsert[$funcName];
                return $this->$func($key,$value);
            }

            public function existTypes($type)
            { 
                return array_key_exists($type, $this->funcsInsert);
            }

            public function get($fieldName)
            {
                if (array_key_exists($fieldName, $this->fields)) {
                    return reset($this->fields);
                }
                else{
                    return null;
                }
            }

            public function toJson()
            {
                return json_encode([
                    'fields' => $this->fields
                ]);
            }
        }

        /**
         * Class to Manage my StoreDatabase
         * */ 
        class FireStoreApiClient
        {
            private $apiRoot = 'https://firestore.googleapis.com/v1beta1/';
            private $project;
            private $apiKey;

            function __construct($project, $apiKey)
            {
                $this->project = $project;
                $this->apiKey = $apiKey;
            }

            private function constructUrl($method, $params = null)
            {
                $params = is_array($params) ? $params : [];
                return ($this->apiRoot . 'projects/' . $this->project . '/' .
                    'databases/(default)/' . $method . '?key=' . $this->apiKey . '&' . http_build_query($params)
                );
            }

            private function _get($method, $params = null)
            {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $this->constructUrl($method, $params),
                    CURLOPT_USERAGENT => 'cURL'
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                return $response;
            }

            private function _post($method, $params, $postBody)
            {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_URL => $this->constructUrl($method, $params),
                    CURLOPT_HTTPHEADER => array('Content-Type: application/json', 'Content-Length: ' . strlen($postBody)),
                    CURLOPT_USERAGENT => 'cURL',
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $postBody
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                return $response;
            }

            private function _put($method, $params, $postBody)
            {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'PUT',
                    CURLOPT_HTTPHEADER => array('Content-Type: application/json', 'Content-Length: ' . strlen($postBody)),
                    CURLOPT_URL => $this->constructUrl($method, $params),
                    CURLOPT_USERAGENT => 'cURL',
                    CURLOPT_POSTFIELDS => $postBody
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                return $response;
            }

            private function _patch($method, $params, $postBody)
            {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'PATCH',
                    CURLOPT_HTTPHEADER => array('Content-Type: application/json', 'Content-Length: ' . strlen($postBody)),
                    CURLOPT_URL => $this->constructUrl($method, $params),
                    CURLOPT_USERAGENT => 'cURL',
                    CURLOPT_POSTFIELDS => $postBody
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                return $response;
            }

            private function _delete($method, $params)
            {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_CUSTOMREQUEST => 'DELETE',
                    CURLOPT_URL => $this->constructUrl($method, $params),
                    CURLOPT_USERAGENT => 'cURL'
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                return $response;
            }

            /**
             * Store a Document
             * addDocument($collection, $document)
             * @param string $collection 
             * @param mixed $document 
             * @return JSON response
             * */
            public function addDocument($collection, $document)
            {
                return $this->_post(
                    "documents/$collection",
                    [],
                    $document->toJson()
                );
            }

            /**
             * Get all Documents
             * getAll($collection)
             * @param string $collection  
             * @return FireStoreDocument array or empty Array
             * */
            public function getAll($collection,$p=null)
            {
                if ($response = $this->_get("documents/$collection")) {
                    $rest = new FireStoreDocument($response);
                    $data = $rest->getDatas();
                    if($p==null){
                        return $data;
                    }
                    else{
                        $arr=[];
                        $k=$p['key'];
                        $v=$p['val'];
                        foreach ($data as $key => $value) {
                            if(isset($val[$k]) && $val[$k]==$v){
                                $arr[] = $value;
                            }
                        }
                        return $arr;
                    }
                }
                return [];
            }

            /**
             * Get a Document
             * getDocument($collection, $documentID)
             * @param string $collection 
             * @param mixed $documentID 
             * @return FireStoreDocument item or empty Array
             * */
            public function getDocument($collection, $documentID, $edit=null)
            {
                if ($response = $this->_get("documents/$collection/$documentID")) {
                    $rest = new FireStoreDocument($response,$edit);
                    return $rest->getDatas();
                }
                return [];
            }

            /**
             * Set a Document with its ID
             * setDocument($collection, $documentID, $document)
             * @param string $collection 
             * @param mixed $documentID 
             * @param mixed $document 
             * @return JSON response
             * */
            public function setDocument($collection, $documentID, $document)
            {
                return $this->_put(
                    "documents/$collection/$documentID",
                    [],
                    $document->toJson()
                );
            }
 
            /**
             * Update a Document if exists
             * updateDocument($collection, $documentID, $document, $isExists = null)
             * @param string $collection 
             * @param mixed $documentID 
             * @param mixed $document 
             * @param mixed $isExists 
             * @return JSON response
             * */
            public function updateDocument($collection, $documentID, $document, $isExists = null)
            {
                $params = [];
                if ($isExists !== null) {
                    $params['currentDocument.exists'] = !!$isExists;
                }
                return $this->_patch(
                    "documents/$collection/$documentID",
                    $params,
                    $document->toJson()
                );
            }

            /**
             * Delete a Document
             * deleteDocument($collection, $documentID)
             * @param string $collection 
             * @param mixed $documentID 
             * @return JSON response
             * */
            public function deleteDocument($collection, $documentID)
            {
                return $this->_delete(
                    "documents/$collection/$documentID",
                    []
                );
            }
        }

        /**
         * Class to Manage my StoreDatabase
         * */ 
        class FireNotification
        {
            public function sendToTokens($arrayTokens,$_data)
            {
                $SERVER_API_KEY= env('FCM_SERVER_KEY');

                //$firebaseToken = User::whereNotNull('device_token')->pluck('device_token')->all();
                $firebaseToken = $arrayTokens; 
                $data = [
                    "registration_ids" => $firebaseToken,
                    "notification" => [
                        "title"=> $_data['title'],
                        "body" => $_data['body'],  
                    ]
                ];
                $dataStr = json_encode($data);
                
                $headers = [
                    'Authorization: key=' . $SERVER_API_KEY,
                    'Content-Type: application/json',
                ];
            
                $ch = curl_init();
                
                curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataStr);
                        
                $response = curl_exec($ch);
                return $response;
            }
        }
    }

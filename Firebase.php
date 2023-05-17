<?php
    /**
     * Firebase CRUD
     * */

    define('FIRESTORE_API_KEY', 'AIzaSyBE3XjohcvSM7x6204zhF4VByaofizQwDU');
    define('FIRESTORE_PROJECT_ID', 'elstudio-45901'); //numero:230750800309

    require_once __DIR__ . '/firebase/index.php';
    
    use PHPFireStore\FireStoreApiClient;
    use PHPFireStore\FireStoreDocument;
     
    // $params[action,collection,document,documentID]

    
    /**
     * Global function FireStore
     * FireStore($params)
     * @param array $params[action,collection,document,documentID]
     * All functions (action) are : 
     * 
     * [Add]: Store a Document
     * addDocument($collection, $document)
     * 
     * [Get]: getDocument($collection, $documentID)
     * 
     * [GetAll]: Get all Documents
     * getAll($collection)
     * 
     * [SetDocument]: Set a Document with its ID
     * setDocument($collection, $documentID, $document)
     * 
     * [Save,Update,Updated]: Update/Create a Document if exists or not
     * updateDocument($collection, $documentID, $document, $isExists = null)
     * 
     * [Delete]: Delete a Document
     * deleteDocument($collection, $documentID)
     *  
     * */
    function FireStore($params)
    {
        $firestore = new FireStoreApiClient(FIRESTORE_PROJECT_ID, FIRESTORE_API_KEY);
        $document  = new FireStoreDocument();

        switch ($params['action']) {
            case 'add':
                if(is_array($params['document'])){
                    foreach ($params['document'] as $key => $value) {
                        # "boolean", "integer", "double", "string", "array", "object", "resource", "NULL", "unknown type"
                        $type = gettype($key); 
                        if($document->existTypes($type)){
                            $document->funcInsert($type,$key,$value);
                        }
                        else{
                            // $type = 'geo'    => 'geoPointValue',
                            // $type = 'bytes'  => 'bytesValue',
                            // $type = 'reference' => 'referenceValue',
                            // $type = 'timestamp' => 'timestampValue'
                        }
                    }
                } 
                return $firestore->addDocument($params['collection'], $document);
                break;
            
            case 'save':
                // Create this document, fail if it already exists
                // Créer un document. Error s'il existe déjà
                if(is_array($params['document'])){
                    foreach ($params['document'] as $key => $value) {
                        # "boolean", "integer", "double", "string", "array", "object", "resource", "NULL", "unknown type"
                        $type = gettype($key); 
                        if($document->existTypes($type)){
                            $document->funcInsert($type,$key,$value);
                        }
                        else{
                            // 
                        }
                    }
                } 
                return $firestore->updateDocument($params['collection'], $params['documentID'], $document, false);
                break;
            
            case 'get':
                // Fetch the document $params['collection']/$params['documentID']
                return $firestore->getDocument($params['collection'], $params['documentID'], $params['edit']=null);
                break;
            
            case 'getall':
                // Fetch the document $params['collection']
                return $firestore->getAll($params['collection']);
                break;
            
            case 'update':
                // Update this document, fail if it does not exist
                // Update un document. Error s'il n'existe pas
                if(is_array($params['document'])){
                    foreach ($params['document'] as $key => $value) {
                        # "boolean", "integer", "double", "string", "array", "object", "resource", "NULL", "unknown type"
                        $type = gettype($key); 
                        if($document->existTypes($type)){
                            $document->funcInsert($type,$key,$value);
                        }
                        else{
                            // 
                        }
                    }
                } 
                return $firestore->updateDocument($params['collection'], $params['documentID'], $document, true);
                break;
        
            case 'updated':
                // Create or update document $params['collection']/$params['documentID']
                if(is_array($params['document'])){
                    foreach ($params['document'] as $key => $value) {
                        # "boolean", "integer", "double", "string", "array", "object", "resource", "NULL", "unknown type"
                        $type = gettype($key); 
                        if($document->existTypes($type)){
                            $document->funcInsert($type,$key,$value);
                        }
                        else{
                            // 
                        }
                    }
                } 
                return $firestore->updateDocument($params['collection'], $params['documentID'], $document);
                break;
            
            case 'setdocument':
                // Update a document $params['collection']/$params['documentID']
                if(is_array($params['document'])){
                    foreach ($params['document'] as $key => $value) {
                        # "boolean", "integer", "double", "string", "array", "object", "resource", "NULL", "unknown type"
                        $type = gettype($key); 
                        if($document->existTypes($type)){
                            $document->funcInsert($type,$key,$value);
                        }
                        else{
                            // 
                        }
                    }
                } 
                return $firestore->setDocument($params['collection'], $params['documentID'], $document);
                break;
            
            case 'delete':
                // Remove the document $params['collection']/$params['documentID']
                return $firestore->deleteDocument($params['collection'], $params['documentID']);
                break;
            
            default:
                # Fetch all data of Collection...
                return $firestore->getAll($params['collection']);
                break;
        }
    }

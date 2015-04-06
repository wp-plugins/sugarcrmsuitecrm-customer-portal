<?php

define('sugarEntry', TRUE);

Class SugarRestApiCall {     

    var $username;
    var $password;
    var $url;
    var $session_id;

    function __construct($url, $username, $password) {
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
        $this->session_id = $this->login();
    }

    public function call($method, $parameters, $url) {
        ob_start();
        $curl_request = curl_init();

        curl_setopt($curl_request, CURLOPT_URL, $url);
        curl_setopt($curl_request, CURLOPT_POST, 1);
        curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl_request, CURLOPT_HEADER, 1);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);

        $jsonEncodedData = json_encode($parameters);

        $post = array(
            "method" => $method,
            "input_type" => "JSON",
            "response_type" => "JSON",
            "rest_data" => $jsonEncodedData
        );

        curl_setopt($curl_request, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($curl_request);
        curl_close($curl_request);

        $result = explode("\r\n\r\n", $result, 2);
        $response = json_decode($result[1]);
        ob_end_flush();

        return $response;
    }

 // login into sugar
    public function login() {
        $login_parameters = array(
            "user_auth" => array(
                "user_name" => $this->username,
                "password" => md5($this->password),
            ),
        );
        $login_response = $this->call('login', $login_parameters, $this->url);
        $session_id = $login_response->id;
        return $session_id;
    } 
    
 // login into Portal (login call in contacts module, it give all information about contact)
    public function PortalLogin($username,$password){
        /* $username and $password are passed from login page */
     $get_entry_list = array(
            'session' => $this->session_id,
            'module_name' => 'Contacts',
            'query' => "username_c = '{$username}' AND  password_c = '{$password}'",
            'order_by' => '',
            'offset' => 0,
            'select_fields' => array('id', 'username_c', 'password_c', 'salutation', 'first_name', 'last_name', 'email1','account_id','title','phone_work','phone_mobile','phone_fax'),
            'max_results' => 0,
        );
      $get_entry_list_result = $this->call("get_entry_list", $get_entry_list, $this->url);
      return $get_entry_list_result;
    }
    
    
    //get Case  and Contact Relationship 
     public function getRelatedCasefor_LoggedContact($contact_id, $limit = '', $offset = '', $order_by = '') {
                                     
        $get_relationship_params = array(
            'session' => $this->session_id,
            'module_name' => 'Contacts',
            "module_id" => $contact_id,
            "link_field_name" => "cases",
            "related_module_query" => "",
            "related_fields" => array("id", "case_number", "name", "date_entered", "priority", "status"),
            "related_module_link_name_to_fields_array" => array(),
            "deleted" => 0,
            'order_by' => "$order_by",
            "offset" => $offset,
            "limit" => $limit,
        );
        $get_entry_list_result = $this->call("get_relationships", $get_relationship_params, $this->url);
        return $get_entry_list_result;
    }
    
    //get Case  and Contact Relationship Count
     public function getRelatedCasefor_LoggedContact_Count($contact_id) {
             
        $get_relationship_params = array(
            'session' => $this->session_id,
            'module_name' => 'Contacts',
            "module_id" => $contact_id,
            "link_field_name" => "cases",
            "related_module_query" => "",
            "related_fields" => array("id", "case_number", "name", "date_entered", "priority", "status"),
            "related_module_link_name_to_fields_array" => array(),
            "deleted" => 0,
        );
        $get_entry_list_result = $this->call("get_relationships", $get_relationship_params, $this->url);
        return $get_entry_list_result;
    }
    
    //get Case Details
    public function getCaseDetail($case_id){
         $get_entry_parameters = array(
            'session' => $this->session_id,
            'module_name' => 'Cases',
            'id' => $case_id,
            //'select_fields' => '*',
        );
        $get_entry_result = $this->call("get_entry", $get_entry_parameters, $this->url);
        return $get_entry_result;
    }
    
    //Add or Update given module record 
    public function set_entry($module_name,$set_entry_dataArray){
        $nameValueListArray = array();
        $i = 0;
        foreach ($set_entry_dataArray as $field => $value) {
            $nameValueListArray[$i]['name'] = $field;
            $nameValueListArray[$i]['value'] = $value;
            $i++;
        }
        $set_entry_parameters = array(
            "session" => $this->session_id,
            "module_name" => $module_name,
            "name_value_list" => $nameValueListArray
        );
        $set_entry_result = $this->call("set_entry", $set_entry_parameters, $this->url);
        $recordID = $set_entry_result->id;
        return $recordID;
    }
    
    //relate created case with loged contact
    public function relateCasewithContact($contact_id,$case_id){
        $set_relationships_parameters = array(
                'session' => $this->session_id,
                'module_name' => 'Contacts',
                'module_id' => $contact_id,
                'link_field_name' => 'cases',
                'related_ids' => array($case_id),
                'delete' => 0,
            );
            $set_relationships_result = $this->call("set_relationship", $set_relationships_parameters, $this->url);
            return $set_relationships_result;
    }
    
    //create note
     function createNote($caseId, $upload_file, $upload_path,$notes_subject,$notes_description) {     
        $note_params = array(
            'session' => $this->session_id,
            'module_name' => 'Notes',
            'name_value_list' => array(
                array('name' => 'name', 'value' => $notes_subject),
                array('name' => 'description', 'value' => $notes_description),
                array('name' => 'parent_type', 'value' => 'Cases'),
                array('name' => 'parent_id', 'value' => $caseId),
        ));
        
        
            
        $noteResult = $this->call('set_entry', $note_params ,  $this->url);
        
        if($upload_path != NULL) {
            $getFileContents = base64_encode(file_get_contents($upload_path));
        }
        else {
            $getFileContents = "";
        }
        
        $attachment = array(
            'id' => $noteResult->id,
            'filename' => $upload_file,
            'file' => $getFileContents,
        );                              
        //set attachment call
        $note_attachment = array(
            'session' => $this->session_id,
            'note' => $attachment
        );
        
        $upload_dir = wp_upload_dir();
        $upload_dir = $upload_dir['basedir']."/scp-uploads/";                           
        $target_file = $upload_dir . basename($_FILES["add-notes-attachment"]["name"]);
            
        move_uploaded_file($_FILES["add-notes-attachment"]["tmp_name"],$target_file);
        
        $attachmentResult = $this->call('set_note_attachment', $note_attachment,  $this->url);
        if ($attachmentResult->id != -1) {
            return true;
        }
        return false;
    }
    
    //get related notes for cases 
    public function getNotes($case_id){        
        $get_relationship_params = array( 
            'session' => $this->session_id, 
            'module_name' => 'Cases', 
            "module_id" => $case_id, "link_field_name" => "notes", 'related_module_query' => "", 
            'related_fields' => array( 'id', 'name','description','filename' ), 
            "deleted" => false, 
        ); 
        
        $get_entry_list_result = $this->call("get_relationships", $get_relationship_params, $this->url); 
        return $get_entry_list_result->entry_list; 
    }

    
    // Get User Information
    public function getUserInformation($contact_id){
         $get_entry_parameters = array(
            'session' => $this->session_id,
            'module_name' => 'Contacts',
            'id' => $contact_id,
        );
        $get_entry_result = $this->call("get_entry", $get_entry_parameters, $this->url);
        return $get_entry_result;
    }
    
    // Check user exists or not
    public function getUserExists($username){
        $get_entry_list = array(
            'session' => $this->session_id,
            'module_name' => 'Contacts',
            'query' => "username_c = '{$username}'",
            'order_by' => '',
            'offset' => 0,
            'select_fields' => array('id', 'username_c'),
            'max_results' => 0,
        );
      $get_entry_list_result = $this->call("get_entry_list", $get_entry_list, $this->url);
      $isUser = $get_entry_list_result->entry_list[0]->name_value_list->username_c->value; 
      if($isUser == $username)
      {
            return true;
      }
      else{
            return false;            
      }
    }
    
    // Check user information by username
    public function getUserInformationByUsername($username){
        $get_entry_list = array(
            'session' => $this->session_id,
            'module_name' => 'Contacts',
            'query' => "username_c = '{$username}'",
            'order_by' => '',
            'offset' => 0,
            'select_fields' => array('id', 'username_c','password_c','email1'),
            'max_results' => 0,
        );
      $get_entry_list_result = $this->call("get_entry_list", $get_entry_list, $this->url);
      $isUser = $get_entry_list_result->entry_list[0]->name_value_list->username_c->value; 
      if($isUser == $username)
      {
            return $get_entry_list_result;
      }
      else{
            return false;            
      }
    }
     
    
    // Get contact all email address
    public function getContactAllEmail(){
        $get_entry_list = array(
            'session' => $this->session_id,
            'module_name' => 'Contacts',
            'query' => "",
            'order_by' => '',
            'offset' => 0,
            'select_fields' => array('id', 'email1'),
            'max_results' => 0,
        );
      $get_entry_list_result = $this->call("get_entry_list", $get_entry_list, $this->url);
      $getAllEmailsData = $get_entry_list_result->entry_list;
      
        foreach($getAllEmailsData as $getAllEmailsObj) {
            $getEmails[] = $getAllEmailsObj->name_value_list->email1->value;    
        }
      return $getEmails;
    }
}
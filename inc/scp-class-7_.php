<?php

define('sugarEntry', TRUE);
class SugarRestApiCall {

    var $token;
    var $username;
    var $password;
    var $url;

    function __construct($url, $username, $password) {
        $this->username = $username;
        $this->password = $password;
        $this->url = $url;
        $this->token = $this->login();
    }

    function call($url, $oauthtoken = '', $type = 'GET', $parameters = array(), $encodeData = true) {
        $type = strtoupper($type);

        $curl_request = curl_init($url);

        if ($type == 'POST') {
            curl_setopt($curl_request, CURLOPT_POST, 1);
        } elseif ($type == 'PUT') {
            curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, "PUT");
        } elseif ($type == 'DELETE') {
            curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, "DELETE");
        }

        curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl_request, CURLOPT_HEADER, false);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);

        if (!empty($oauthtoken)) {
            $token = array("oauth-token: {$oauthtoken}");
            curl_setopt($curl_request, CURLOPT_HTTPHEADER, $token);
        }

        if (!empty($parameters)) {
            if ($encodeData) {
                $parameters = json_encode($parameters);
            }

            curl_setopt($curl_request, CURLOPT_POSTFIELDS, $parameters);
        }

        $result = curl_exec($curl_request);
        if (curl_error($curl_request)) {
            return false;            
        }
        curl_close($curl_request);
                                           
        $response = json_decode($result);
        
        if($response != NULL) {
            if (property_exists($response, "error_message") && $response->error_message == 'The access token provided is invalid.') {
                $this->token = $this->login();
                $response = $this->call($url, $this->token, $type, $parameters, false);
            }
            try {
                if (empty($response)) {
                    throw new Exception("Response not received from SugarCRM.");
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }             
        }
        else {
            return false;
        }
        

        return $response;
    }

    function login() {
        $url = $this->url;
        $url = $url . "/oauth2/token";

        $oauth2_token_parameters = array(
            "grant_type" => "password",
            "client_id" => "sugar",
            "client_secret" => "",
            "username" => $this->username,
            "password" => $this->password,
            "platform" => "base"
        );

        $oauth2_token_result = self::call($url, '', 'POST', $oauth2_token_parameters);
        if (empty($oauth2_token_result->access_token)) {
            return false;
        }
        return $oauth2_token_result->access_token;
    }

    function PortalLogin($username,$password) {
        if ($this->token != '') {
            $filter_arguments = array(
                "filter" => array(
                    array(
                        '$and' => array(
                            array(
                                "username_c" => array(
                                    '$equals'=>"{$username}",
                                )
                            ),
                            array(
                                "password_c" => array(
                                    '$equals'=>"{$password}",
                                ),
                            )
                        ),
                    ),
                ),
                "offset" => 0,
                "fields" => "id,username_c,password_c,salutation,first_name,last_name,email1,account_id,title,phone_work,phone_mobile','phone_fax",
            );
            $module = "Contacts";
            $url = $this->url;
            $url = $url . "/{$module}/filter";
            $response = self::call($url, $this->token, 'POST', $filter_arguments);
            return $response;
        }
    }

    /* you will get related cases of logged contact */
    public function getRelatedCasefor_LoggedContact($contact_id, $limit = '', $offset = '', $order_by = '') {
        if ($this->token != '') {
            $filter_arguments = array(
                "offset" => $offset,
                "order_by" => $order_by,
                "max_num" => $limit,
                "fields" => "id,case_number,name,date_entered,priority,status"
            );
            $url = $this->url . "/Contacts/" . $contact_id . "/link/cases/?".http_build_query($filter_arguments);
            $link_response = self::call($url, $this->token, 'GET');

            return $link_response;
        }
    }
    
    public function getRelatedCasefor_LoggedContact_Count($contact_id, $limit = '', $offset = '', $order_by = '') {
        if ($this->token != '') {
            $filter_arguments = array(
                "offset" => $offset,
                "order_by" => $order_by,
                "max_num" => $limit,
                "fields" => "id,case_number,name,date_entered,priority,status"
            );
            $url = $this->url . "/Contacts/" . $contact_id . "/link/cases/?".http_build_query($filter_arguments);
            $link_response = self::call($url, $this->token, 'GET');

            return $link_response;
        }
    }

    //get Case Details
    public function getCaseDetail($case_id){
        if ($this->token != '') {
            $url = $this->url . "/Cases/{$case_id}";
            $response = self::call($url, $this->token, 'GET');
            return $response;
        }
    }

      function set_entry($module_name,$set_entry_dataArray) {
          $url = $this->url;
          if(isset($set_entry_dataArray['id'])){
              $isUpdate = true;
          }else{
              $isUpdate = false;
          }
          if ($this->token != '') {
              if ($isUpdate == true) {
                  $url = $url . "/{$module_name}/{$set_entry_dataArray['id']}";
                  unset($set_entry_dataArray['id']);
                  $response = self::call($url, $this->token, 'PUT', $set_entry_dataArray);
                  return $response->id;
              } else {
                  $url = $url . "/{$module_name}";
                  $response = self::call($url, $this->token, 'POST', $set_entry_dataArray);
                  return $response->id;
              }
          }
      }

    function relateCasewithContact($contact_id,$case_id) {
        $url = $this->url;
        if ($this->token != '') {
            $nameValueList = array(
                'link_name' => "contacts",
                'ids' => array($contact_id), // second module id.
                'sugar_id' => $case_id // first module id
            );
            $module = "Cases";
            $url = $url . "{$module}/{$nameValueList['sugar_id']}/link";
            unset($nameValueList['sugar_id']);
            $response = self::call($url, $this->token, 'POST', $nameValueList);
            return $response->id;
        }
    }

    function createNote($case_id,$upload_file, $upload_path,$notes_subject,$notes_description){ 
        $url = $this->url;
        $note_params = array(
            'name' => $notes_subject,
            'description' => $notes_description,
            'parent_type' => 'Cases',
            'parent_id' => $case_id,
        );
            
        if ($this->token != '') {
            $url = $url . "/Notes";
            $response = self::call($url, $this->token, 'POST', $note_params);
            $note_id = $response->id;
            
                //$file = base64_encode(file_get_contents($upload_path));
               //DebugBreak(); 
            $upload_dir = wp_upload_dir();
            $upload_dir = $upload_dir['basedir']."/scp-uploads/";                           
            $target_file = $upload_dir . basename($_FILES["add-notes-attachment"]["name"]);
            
            move_uploaded_file($_FILES["add-notes-attachment"]["tmp_name"],$target_file);
             
            $url = $this->url . "Notes/{$note_id}/file/filename";
            $file_arguments = array(
                "format" => "sugar-html-json",
                "delete_if_fails" => true,
                "oauth_token" => $this->token,
                'filename' => "@{$target_file}",
            );
            
            $file_response = self::call($url, $this->token, 'POST', $file_arguments, false);
            return $file_response;
        }
    }
    
    function getNotes($case_id) {
        $url = $this->url;
        $caseid = "$case_id";
        $link_name = "notes";
        $url = $url . "/Cases/{$caseid}/link/{$link_name}";
        $response = self::call($url, $this->token, 'GET');
        return $response->records;
    }

    // Get User Information
     function getUserInformation($contact_id){
         if ($this->token != '') {
             $url = $this->url."/Contacts/".$contact_id;
             $user_response = self::call($url, $this->token, 'GET');
             return $user_response;
         }
    }

    // Check user exists or not
    function getUserExists($username){
        if($this->token != ''){
            $filter_arguments = array(
                "filter" => array(
                    array(
                        "username_c" => array(
                            '$equals'=>"{$username}",
                        ),
                 ),
            ),
                "offset" => 0,
                "fields" => "id,username_c",
            );
            $url = $this->url."/Contacts/filter";
            $response = self::call($url, $this->token, 'POST', $filter_arguments);
            $isUser = $response->records[0]->username_c;
            if($isUser == $username)
            {
                return true;
            }
            else{
                return false;
            }
        }
    }

    // Check user information by username
    function getUserInformationByUsername($username){
        if($this->token != '') {
            $filter_arguments = array(
                "filter" => array(
                    array(
                        "username_c" => array(
                            '$equals' => "{$username}",
                        ),
                    ),
                ),
                "offset" => 0,
                "fields" => "id,username_c,password_c,email1",
            );
            $url = $this->url . "/Contacts/filter";
            $response = self::call($url, $this->token, 'GET', $filter_arguments);
        }
        $isUser = $response->records[0]->username_c;
        if($isUser == $username)
        {
            return $response;
        }
        else{
            return false;
        }
    }

    // Get contact all email address
    public function getContactAllEmail(){
        if($this->token != '') {
            $url = $this->url . "/Contacts/filter";
            $filter_arguments = array(
                "offset" => 0,
                "fields" => "id,email1",
            );
            $response = self::call($url, $this->token, 'GET',$filter_arguments);
            $email_records = $response->records;
            foreach($email_records as $record){
                $emails[] = $record->email1;
            }
        }

        return $emails;
    }

}
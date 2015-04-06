<?php

add_action( 'admin_post_scp_sign_up', 'prefix_admin_scp_sign_up' ); // Sign Up      
add_action( 'admin_post_nopriv_scp_sign_up', 'prefix_admin_scp_sign_up' ); // Sign Up
function prefix_admin_scp_sign_up() {
    global $sugar_crm_version;
    
    $scp_sugar_rest_url = get_option('biztech_scp_rest_url');    
    $scp_sugar_username = get_option('biztech_scp_username');
    $scp_sugar_password = get_option('biztech_scp_password');
     
    $objSCP = new SugarRestApiCall($scp_sugar_rest_url, $scp_sugar_username, $scp_sugar_password);
           
    $username_c = stripslashes_deep($_REQUEST['add-signup-username']);
    $password_c = stripslashes_deep($_REQUEST['add-signup-password']);    
    $first_name = stripslashes_deep($_REQUEST['add-signup-first-name']);
    $department = stripslashes_deep($_REQUEST['add-signup-department']);
    $last_name = stripslashes_deep($_REQUEST['add-signup-last-name']);
    $phone_work = stripslashes_deep($_REQUEST['add-signup-office-phone']);
    $title = stripslashes_deep($_REQUEST['add-signup-title']);
    $phone_mobile = stripslashes_deep($_REQUEST['add-signup-mobile']);
    $phone_fax = stripslashes_deep($_REQUEST['add-signup-fax']);
    $email1 = stripslashes_deep($_REQUEST['add-signup-email-address']);
    $primary_address_street = stripslashes_deep($_REQUEST['add-signup-primary-address']);
    $alt_address_street = stripslashes_deep($_REQUEST['add-signup-other-address']);
    $description = stripslashes_deep($_REQUEST['add-signup-description']);

        $addSignUp = array(
            'username_c' => $username_c,
            'password_c' => $password_c,
            'first_name' => $first_name,
            'department' => $department,
            'last_name' => $last_name,
            'phone_work' => $phone_work,
            'title' => $title,
            'phone_mobile' => $phone_mobile,
            'phone_fax' => $phone_fax,
            'email1' => $email1,
            'primary_address_street' => $primary_address_street,
            'alt_address_street' => $alt_address_street,
            'description' => $description
        ); 
        
        $checkUserExists = $objSCP->getUserExists($username_c);
        $getAllEmails = $objSCP->getContactAllEmail();
        
        if(($checkUserExists == true) && (in_array($email1,$getAllEmails) == true))
        {
            $redirect_url = $_REQUEST['scp_current_url'].'?scp-page=signup&signup=userandemailexists';
            wp_redirect( $redirect_url );     
        }
        else if($checkUserExists == true) {
            $redirect_url = $_REQUEST['scp_current_url'].'?scp-page=signup&signup=userexists';
            wp_redirect( $redirect_url );    
        }
        else if(in_array($email1,$getAllEmails) == true) {
            $redirect_url = $_REQUEST['scp_current_url'].'?scp-page=signup&signup=emailexists';
            wp_redirect( $redirect_url );
        }
        else {
             $isSignUp = $objSCP->set_entry('Contacts',$addSignUp);
             
             if($isSignUp != NULL ) {
                $redirect_url = $_REQUEST['scp_current_url'].'?signup=true';
                wp_redirect( $redirect_url );    
             } 
        }       
}

add_action( 'admin_post_scp_create_notes', 'prefix_admin_scp_create_notes' );   // Create Notes
add_action( 'admin_post_nopriv_scp_create_notes', 'prefix_admin_scp_create_notes' );   // Create Notes
function prefix_admin_scp_create_notes() {
    $scp_sugar_rest_url = get_option('biztech_scp_rest_url');    
    $scp_sugar_username = get_option('biztech_scp_username');
    $scp_sugar_password = get_option('biztech_scp_password');
     
    $objSCP = new SugarRestApiCall($scp_sugar_rest_url, $scp_sugar_username, $scp_sugar_password);
                       
    $case_id = $_REQUEST['scp_case_id'];   
    $upload_file = $_FILES['add-notes-attachment']['name'];
    $upload_path = $_FILES['add-notes-attachment']['tmp_name'];
    $notes_subject = stripslashes_deep($_REQUEST['add-notes-name']);
    $notes_description = stripslashes_deep($_REQUEST['add-notes-description']);
             
    $createNote = $objSCP->createNote($case_id, $upload_file, $upload_path,$notes_subject,$notes_description);
    
    if($createNote == 1) {
        $redirect_url = $_REQUEST['scp_current_url'].'&success=true';
        wp_redirect( $redirect_url );    
    }      
}

add_action( 'admin_post_scp_get_note_asttachment', 'prefix_admin_scp_get_note_asttachment' );   // Get Note Attachment
add_action( 'admin_post_nopriv_scp_get_note_asttachment', 'prefix_admin_scp_get_note_asttachment' );   // Get Note Attachment
function prefix_admin_scp_get_note_asttachment() {
    $scp_sugar_rest_url = get_option('biztech_scp_rest_url');    
    $scp_sugar_username = get_option('biztech_scp_username');
    $scp_sugar_password = get_option('biztech_scp_password');
     
    $objSCP = new SugarRestApiCall($scp_sugar_rest_url, $scp_sugar_username, $scp_sugar_password);
                   
    $note_id = $_REQUEST['scp_note_id'];       
    $objSCP->getNoteAttachment($note_id); 
}

add_action( 'admin_post_scp_add_new_case', 'prefix_admin_scp_add_new_case' );   // Add New Case
add_action( 'admin_post_nopriv_scp_add_new_case', 'prefix_admin_scp_add_new_case' );   // Add New Case
function prefix_admin_scp_add_new_case() {
    $scp_sugar_rest_url = get_option('biztech_scp_rest_url');    
    $scp_sugar_username = get_option('biztech_scp_username');
    $scp_sugar_password = get_option('biztech_scp_password');
     
    $objSCP = new SugarRestApiCall($scp_sugar_rest_url, $scp_sugar_username, $scp_sugar_password);

     $setAddCaseData = array(
        'name'              => stripslashes_deep($_REQUEST['add-name']),
        'description'       => stripslashes_deep($_REQUEST['add-description']),
        'status'            => $_REQUEST['add-status'],
        'priority'          => $_REQUEST['add-priority'],
        'type'              => $_REQUEST['add-type'],
        'resolution'        => stripslashes_deep($_REQUEST['add-resolution']),
        'account_id'        => $_SESSION['scp_account_id']
    );
    
    $getNewCaseId = $objSCP->set_entry('Cases',$setAddCaseData); 
    $objSCP->relateCasewithContact($_SESSION['scp_user_id'],$getNewCaseId);
    
    $redirect_url = $_REQUEST['scp_current_url'].'&success=true';
    wp_redirect( $redirect_url );
}

add_action( 'admin_post_scp_update_new_case', 'prefix_admin_scp_update_new_case' );   // Add New Case
add_action( 'admin_post_nopriv_scp_update_new_case', 'prefix_admin_scp_update_new_case' );   // Add New Case
function prefix_admin_scp_update_new_case() { 
    $scp_sugar_rest_url = get_option('biztech_scp_rest_url');    
    $scp_sugar_username = get_option('biztech_scp_username');
    $scp_sugar_password = get_option('biztech_scp_password');
     
    $objSCP = new SugarRestApiCall($scp_sugar_rest_url, $scp_sugar_username, $scp_sugar_password);
                                      
     $setUpdateCaseData = array(
        'id'                => $_REQUEST['scp_case_id'],
        'name'              => stripslashes_deep($_REQUEST['update-name']),
        'description'       => stripslashes_deep($_REQUEST['update-description']),
        'status'            => $_REQUEST['update-status'],
        'priority'          => $_REQUEST['update-priority'],
        'type'              => $_REQUEST['update-type'],
        'resolution'        => stripslashes_deep($_REQUEST['update-resolution']),
        'account_id' => $_SESSION['scp_account_id']
    );
              
    $getNewCaseId = $objSCP->set_entry('Cases',$setUpdateCaseData); 
    
    $redirect_url = $_REQUEST['scp_current_url']."?scp-page=case-details&case_id='$_REQUEST[scp_case_id]'&success_update=true";
    wp_redirect( $redirect_url );
}

add_action( 'admin_post_scp_update_profile', 'prefix_admin_scp_update_profile' );   // Update Profile
add_action( 'admin_post_nopriv_scp_update_profile', 'prefix_admin_scp_update_profile' );   // Update Profile
function prefix_admin_scp_update_profile() {
    global $sugar_crm_version;
    
    $scp_sugar_rest_url = get_option('biztech_scp_rest_url');    
    $scp_sugar_username = get_option('biztech_scp_username');
    $scp_sugar_password = get_option('biztech_scp_password');
     
    $objSCP = new SugarRestApiCall($scp_sugar_rest_url, $scp_sugar_username, $scp_sugar_password);

        $first_name = stripslashes_deep($_REQUEST['add-profile-first-name']);
        $department = stripslashes_deep($_REQUEST['add-profile-department']);
        $last_name = stripslashes_deep($_REQUEST['add-profile-last-name']);
        $phone_work = stripslashes_deep($_REQUEST['add-profile-office-phone']);
        $title = stripslashes_deep($_REQUEST['add-profile-title']);
        $phone_mobile = stripslashes_deep($_REQUEST['add-profile-mobile']);
        $phone_fax = stripslashes_deep($_REQUEST['add-profile-fax']);
        $email1 = stripslashes_deep($_REQUEST['add-profile-email-address']);
        $primary_address_street = stripslashes_deep($_REQUEST['add-profile-primary-address']);
        $alt_address_street = stripslashes_deep($_REQUEST['add-profile-other-address']);
        $description = stripslashes_deep($_REQUEST['add-profile-description']);
            
            if($sugar_crm_version == 6) {
                $updateUserInfo = array(
                    'id' => $_SESSION['scp_user_id'],
                    'first_name' => $first_name,
                    'department' => $department,
                    'last_name' => $last_name,
                    'phone_work' => $phone_work,
                    'title' => $title,
                    'phone_mobile' => $phone_mobile,
                    'phone_fax' => $phone_fax,
                    'email1' => $email1,
                    'primary_address_street' => $primary_address_street,
                    'alt_address_street' => $alt_address_street,
                    'description' => $description
                );     
            }
            else if($sugar_crm_version == 7) {
                $updateUserInfo = array(
                    'id' => $_SESSION['scp_user_id'],
                    'first_name' => $first_name,
                    'department' => $department,
                    'last_name' => $last_name,
                    'phone_work' => $phone_work,
                    'title' => $title,
                    'phone_mobile' => $phone_mobile,
                    'phone_fax' => $phone_fax,
                    'email' => array(
                        0 =>
                            array(
                                'email_address' => $email1,
                                'opt_out' => '0',
                                'invalid_email' => '0',
                                'primary_address' => '1',
                            ),
                    ),
                    'primary_address_street' => $primary_address_street,
                    'alt_address_street' => $alt_address_street,
                    'description' => $description
                );         
            }
            else {
                
            }
                          
    $isUpdate = $objSCP->set_entry('Contacts',$updateUserInfo);
    
    if($isUpdate != NULL) {
        $redirect_url = $_REQUEST['scp_current_url'].'&success=true';
        wp_redirect( $redirect_url );   
    }
}
add_action( 'admin_post_scp_change_password', 'prefix_admin_scp_change_password' );
add_action( 'admin_post_nopriv_scp_change_password', 'prefix_admin_scp_change_password' );   // Change Password
function prefix_admin_scp_change_password() {
    global $sugar_crm_version;
    
    $scp_sugar_rest_url = get_option('biztech_scp_rest_url');    
    $scp_sugar_username = get_option('biztech_scp_username');
    $scp_sugar_password = get_option('biztech_scp_password');
     
    $objSCP = new SugarRestApiCall($scp_sugar_rest_url, $scp_sugar_username, $scp_sugar_password);
    
    if($sugar_crm_version == 6) {
        $getContactInfo = $objSCP->getUserInformation($_SESSION['scp_user_id'])->entry_list[0]->name_value_list;
        $password = $getContactInfo->password_c->value;
        
        if($password == stripslashes_deep($_REQUEST['add-profile-old-password']) ) {
            if(stripslashes_deep($_REQUEST['add-profile-new-password']) == stripslashes_deep($_REQUEST['add-profile-confirm-password'])) {
                
                $new_password = stripslashes_deep($_REQUEST['add-profile-new-password']);
                 $updateUserInfo = array(
                    'id' => $_SESSION['scp_user_id'],
                    'password_c' => $new_password
                );
                
                $isChangePassword = $objSCP->set_entry('Contacts',$updateUserInfo);
                
            if($isChangePassword != NULL) {
                     $redirect_url = $_REQUEST['scp_current_url'].'&success=true';
                     wp_redirect( $redirect_url );
                }   
            }
            else {
                   $redirect_url = $_REQUEST['scp_current_url'].'&error=1';
                   wp_redirect( $redirect_url );                                           
            }                                            
        }
        else {
                $redirect_url = $_REQUEST['scp_current_url'].'&error=2';
                wp_redirect( $redirect_url );  
        }    
    }
    else if($sugar_crm_version == 7){
        $getContactInfo = $objSCP->getUserInformation($_SESSION['scp_user_id']);
        $password = $getContactInfo->password_c;
        
        if($password == stripslashes_deep($_REQUEST['add-profile-old-password']) ) {
            if(stripslashes_deep($_REQUEST['add-profile-new-password']) == stripslashes_deep($_REQUEST['add-profile-confirm-password'])) {
                
                $new_password = stripslashes_deep($_REQUEST['add-profile-new-password']);
                 $updateUserInfo = array(
                    'id' => $_SESSION['scp_user_id'],
                    'password_c' => $new_password
                );
                
                $isChangePassword = $objSCP->set_entry('Contacts',$updateUserInfo);
                
            if($isChangePassword != NULL) {
                     $redirect_url = $_REQUEST['scp_current_url'].'&success=true';
                     wp_redirect( $redirect_url );
                }   
            }
            else {
                   $redirect_url = $_REQUEST['scp_current_url'].'&error=1';
                   wp_redirect( $redirect_url );                                           
            }                                            
        }
        else {
                $redirect_url = $_REQUEST['scp_current_url'].'&error=2';
                wp_redirect( $redirect_url );  
        }        
    }
    else {
        
    }       
}

add_action( 'admin_post_scp_forgot_password', 'prefix_admin_scp_forgot_password' );   // Change Password
add_action( 'admin_post_nopriv_scp_forgot_password', 'prefix_admin_scp_forgot_password' );   // Change Password
function prefix_admin_scp_forgot_password() {
    global $sugar_crm_version;
    
    $scp_sugar_rest_url = get_option('biztech_scp_rest_url');    
    $scp_sugar_username = get_option('biztech_scp_username');
    $scp_sugar_password = get_option('biztech_scp_password');
     
    $objSCP = new SugarRestApiCall($scp_sugar_rest_url, $scp_sugar_username, $scp_sugar_password);
    
    $checkUsername = stripslashes_deep($_REQUEST['forgot-password-username']);
    $checkEmialAddress = stripslashes_deep($_REQUEST['forgot-password-email-address']);
    
    if($sugar_crm_version == 6) {
        $getEmails = $objSCP->getContactAllEmail();
        $checkUserExists = $objSCP->getUserInformationByUsername($checkUsername);
        $username = $checkUserExists->entry_list[0]->name_value_list->username_c->value;
        $emailAddress = $checkUserExists->entry_list[0]->name_value_list->email1->value;     
        $getAdminEmail = get_option( 'admin_email' );
        if(($username == $checkUsername) && ($emailAddress == $checkEmialAddress)) {
            $password = $checkUserExists->entry_list[0]->name_value_list->password_c->value;
            $headers = "From: ".get_option('biztech_scp_name')." <$getAdminEmail>' . '\r\n";
            $body = '';
            $body .= 'Your Password: '.$password;
            $isSendEmail = wp_mail("$emailAddress", 'Password Recover', $body, $headers );
            
            if($isSendEmail == true) {                                                                                       
                $redirect_url = $_REQUEST['scp_current_url'].'&success=true';
                wp_redirect( $redirect_url );        
            }      
            else {
                $redirect_url = $_REQUEST['scp_current_url'].'&error=1';
                wp_redirect( $redirect_url );  
            }
        }
        else if(($username == $checkUsername) && ($emailAddress != $checkEmialAddress)) {
            $redirect_url = $_REQUEST['scp_current_url'].'&error=2';
            wp_redirect( $redirect_url );                  
        }
        else {
            $redirect_url = $_REQUEST['scp_current_url'].'&error=3';
            wp_redirect( $redirect_url );           
        }          
    }
    else if($sugar_crm_version == 7) {
        $getEmails = $objSCP->getContactAllEmail();
        $checkUserExists = $objSCP->getUserInformationByUsername($checkUsername);
        $username = $checkUserExists->records[0]->username_c;
        $emailAddress = $checkUserExists->records[0]->email1;       
        $getAdminEmail = get_option( 'admin_email' );
        if(($username == $checkUsername) && ($emailAddress == $checkEmialAddress)) {
            $password = $checkUserExists->records[0]->password_c;
            $headers = "From: ".get_option('biztech_scp_name')." <$getAdminEmail>' . '\r\n";
            $body = '';
            $body .= 'Your Password: '.$password;
            $isSendEmail = wp_mail("$emailAddress", 'Password Recover', $body, $headers );
            
            if($isSendEmail == true) {                                                                                       
                $redirect_url = $_REQUEST['scp_current_url'].'&success=true';
                wp_redirect( $redirect_url );        
            }      
            else {
                $redirect_url = $_REQUEST['scp_current_url'].'&error=1';
                wp_redirect( $redirect_url );  
            }
        }
        else if(($username == $checkUsername) && ($emailAddress != $checkEmialAddress)) {
            $redirect_url = $_REQUEST['scp_current_url'].'&error=2';
            wp_redirect( $redirect_url );                  
        }
        else {
            $redirect_url = $_REQUEST['scp_current_url'].'&error=3';
            wp_redirect( $redirect_url );           
        }             
    }
    else {
        
    }
}
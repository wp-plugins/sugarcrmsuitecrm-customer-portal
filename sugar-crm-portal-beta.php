<?php
/**
 * Plugin Name: SugarCRM/SuiteCRM Customer Portal
 * Description: Sugar CRM Portal + Integration 
 * Author: biztechc
 * Author URI: http://www.biztechconsultancy.com
 * Version: 1.0.0 
 */

global $sugar_crm_version; 
$sugar_crm_version = get_option('biztech_scp_sugar_crm_version');

if($sugar_crm_version == 6) {
    include( plugin_dir_path( __FILE__ ) . 'inc/scp-class-6.php');     
} 

if($sugar_crm_version == 7) {
    include( plugin_dir_path( __FILE__ ) . 'inc/scp-class-7.php');     
}

include( plugin_dir_path( __FILE__ ) . 'inc/scp-action.php'); 
      
add_action('admin_menu', 'sugar_crm_portal_create_menu');
function sugar_crm_portal_create_menu(){

    //create admin side menu
    add_menu_page('Sugar CRM Portal Settings', 'Sugar CRM Portal', 'administrator', 'sugar-crm-portal', 'sugar_crm_portal_settings_page');

    //call register settings function
    add_action( 'admin_init', 'register_sugar_crm_portal_settings' );
}
                                                           
function register_sugar_crm_portal_settings(){
//register our settings
    register_setting( 'sugar_crm_portal-settings-group', 'biztech_scp_name' );
    register_setting( 'sugar_crm_portal-settings-group', 'biztech_scp_rest_url' );
    register_setting( 'sugar_crm_portal-settings-group', 'biztech_scp_username' );
    register_setting( 'sugar_crm_portal-settings-group', 'biztech_scp_password' );
    register_setting( 'sugar_crm_portal-settings-group', 'biztech_scp_case_per_page' );
    register_setting( 'sugar_crm_portal-settings-group', 'biztech_scp_sugar_crm_version' );
}

function sugar_crm_portal_settings_page(){ 
// Admin side page options
    ?>
        <div class='wrap'>
            <h2>Sugar CRM Portal Settings</h2>

            <form method='post' action='options.php'>
                <?php settings_fields( 'sugar_crm_portal-settings-group' ); ?>
                <?php do_settings_sections( 'sugar_crm_portal-settings-group' ); ?>
                <table class='form-table'>
                    <tr valign='top'>
                        <th scope='row'>Portal Name</th>
                        <td><input type='text'  class='regular-text' value="<?php echo get_option('biztech_scp_name');  ?>" name='biztech_scp_name'></td>
                    </tr>
                    
                    <tr>
                    <?php
                        $sugarCrmVersion = array(
                            ''  => 'Select Version',
                            '7' => 'SugarCRM 7',
                            '6' => 'SugarCRM 6 or SuiteCRM',
                        );
                    ?>
                    <th scope="row">Version</th>
                        <td>
                            <select id="biztech_scp_sugar_crm_version" name="biztech_scp_sugar_crm_version">
                                <?php 
                                    foreach($sugarCrmVersion as $key => $velue) {
                                        
                                        if(get_option('biztech_scp_sugar_crm_version') == $key) {
                                            $sel = 'selected="selected"';
                                        }
                                        ?>
                                            <option value="<?php echo $key; ?>" <?php echo $sel; ?>><?php echo $velue; ?></option>
                                        <?php 
                                        $sel = "";   
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>

                    <tr valign='top'>
                        <th scope='row'>REST URL</th>
                        <td><input type='text'  class='regular-text' value="<?php echo get_option('biztech_scp_rest_url');  ?>" name='biztech_scp_rest_url'></td>
                    </tr>
                    
                    <tr valign='top'>
                        <th scope='row'>Username</th>
                        <td><input type='text' value="<?php echo get_option('biztech_scp_username');  ?>" name='biztech_scp_username'></td>
                    </tr>
                    
                    <tr valign='top'>
                        <th scope='row'>Password</th>
                        <td><input type='password' value="<?php echo get_option('biztech_scp_password');  ?>" name='biztech_scp_password'></td>
                    </tr>
                    
                    <tr valign='top'>
                        <th scope='row'>Cases Per Page</th>
                        <td><input type="number" class="small-text" value="<?php if(get_option('biztech_scp_case_per_page') != NULL ){ echo get_option('biztech_scp_case_per_page'); } else { echo "10"; }  ?>" min="1" step="1" name="biztech_scp_case_per_page"></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
    <?php    
    $scp_sugar_rest_url = get_option('biztech_scp_rest_url');    
    $scp_sugar_username = get_option('biztech_scp_username');
    $scp_sugar_password = get_option('biztech_scp_password');
        
        if (class_exists('SugarRestApiCall')) {
            $objSCP = new SugarRestApiCall($scp_sugar_rest_url, $scp_sugar_username, $scp_sugar_password);
            if($objSCP->login() != NULL){
                ?>
                    <div class='updated settings-error' id='setting-error-settings_updated'> 
                        <p><strong>Connection successful.</strong></p>
                    </div>
                <?php 
            }
            else{
                ?>
                    <div class='error settings-error' id='setting-error-settings_updated'> 
                        <p><strong>Connection not successful. Please check SugarCRM Version, REST URL, Username and Password.</strong></p>
                    </div>
                <?php
            }  
        }
        else {
            ?>
                <div class='error settings-error' id='setting-error-settings_updated'> 
                    <p><strong>Connection not successful. Please check SugarCRM Version, REST URL, Username and Password.</strong></p>
                </div>
            <?php
        }                
} 
                                            
function sugar_crm_portal_login_form( $html = "" ){ 
    // login form                    
    $scp_name = get_option('biztech_scp_name');
        if($scp_name != NULL) {
            $html .= "<h3>$scp_name</h3>";
        }
        else {
            $html .= "<h3>Technical Support Login</h3>"; 
        }
        $html .= "
        <form name='scp-login-form' id='scp-login-form' action='' method='post'> 
            <ul>
                <li>
                    <label>Username:</label>
                    <span><input type='text' class='input-text' name='scp_username' id='scp-username' required></span>
                </li>
                <li>
                    <label>Password:</label>
                    <span><input type='password' class='input-text' name='scp_password' id='scp-password' required></span>
                </li>
                <li class='scp-send  last'>
                    <span><input type='submit' name='scp_login_form_submit' id='scp-login-form-submit' value='Log In'></span>
                        <span class='right'>
                            <a href='?scp-page=signup'>Sign Up? Click Here</a> <br />
                            <a href='?scp-page=forgot-password'>Forgot Password? Click Here</a>
                        </span>
                </li>
            </ul>
        </form>";    
    return $html; 
}

function sugar_crm_portal_check_user_and_login( $html = "" ) { 
// check user and login  
    global $sugar_crm_version;
          
    $scp_sugar_rest_url = get_option('biztech_scp_rest_url');           
    $scp_sugar_username = get_option('biztech_scp_username');
    $scp_sugar_password = get_option('biztech_scp_password');
     
    $objSCP = new SugarRestApiCall($scp_sugar_rest_url, $scp_sugar_username, $scp_sugar_password);
    
    if(isset($_REQUEST['scp_login_form_submit']) == true) {
        $scp_username = $_REQUEST['scp_username'];
        $scp_password = $_REQUEST['scp_password'];
        
            if($sugar_crm_version == 6) {
                $isLogin = $objSCP->PortalLogin($scp_username, $scp_password);

                if(($isLogin->entry_list[0] != NULL) && ($scp_username != NULL) && ($scp_password != NULL)) {      
                    $_SESSION['scp_user_id'] = $isLogin->entry_list[0]->id;
                    $_SESSION['scp_account_id'] = $isLogin->entry_list[0]->name_value_list->account_id->value;
                    $_SESSION['scp_user_account_name'] = $isLogin->entry_list[0]->name_value_list->username_c->value;
                   $html .= sugar_crm_portal_index();                
                }
                else { 
                    $html .="<div class='scp-login-form scp-form'>"; 
                        $html .= sugar_crm_portal_login_form();
                        $html .= "<span class='error'>Invalid Username & Password.</span>";
                    $html .= "</div>";
         
                }    
            }
            else if($sugar_crm_version == 7) {            
                $isLogin = $objSCP->PortalLogin($scp_username, $scp_password);  
                if(($isLogin->records[0] != NULL) && ($scp_username != NULL) && ($scp_password != NULL)) {      
                    $_SESSION['scp_user_id'] = $isLogin->records[0]->id;
                    $_SESSION['scp_account_id'] = $isLogin->records[0]->account_id;
                    $_SESSION['scp_user_account_name'] = $isLogin->records[0]->username_c;
                   $html .= sugar_crm_portal_index();                
                }
                else { 
                    $html .="<div class='scp-login-form scp-form'>"; 
                        $html .= sugar_crm_portal_login_form();
                        $html .= "<span class='error'>Invalid Username & Password.</span>";
                    $html .= "</div>";
         
                }    
            }
            else {
                    
            }
    }
    else { 
        $html .= "<div class='scp-login-form scp-form'>"; 
            $html .= sugar_crm_portal_login_form();
            if($_REQUEST['signup'] == true)
            { 
                $html .= "<span class='success'>You are successfully sign up.</span>"; 
            }   
        $html .= "</div>";
    }
    return $html;
} 

function sugar_crm_portal_signup( $html = "" ) {
    // signup form
    global $sugar_crm_version;
    
    $current_url = explode('?', $_SERVER['REQUEST_URI'], 2);
    $current_url = $current_url[0]; 
              
        $html .= "<div class='scp-entry-header'>";
            $html .= "<h3>Sign Up</h3>";
                if($_REQUEST['signup'] == 'userandemailexists') {
                        $html .= "<span class='error'>Username and Email Address already exists.</span>"; 
                }
                
                if($_REQUEST['signup'] == 'userexists') {
                        $html .= "<span class='error'>Username already exists.</span>"; 
                }
                
                if($_REQUEST['signup'] == 'emailexists') {
                        $html .= "<span class='error'>Email Address already exists.</span>"; 
                }
                
        if(isset($_REQUEST['add-sign-up']) == true) {
            $scp_sugar_rest_url = get_option('biztech_scp_rest_url');    
            $scp_sugar_username = get_option('biztech_scp_username');
            $scp_sugar_password = get_option('biztech_scp_password');
             
            $objSCP = new SugarRestApiCall($scp_sugar_rest_url, $scp_sugar_username, $scp_sugar_password);
                   
            $username_c = $_REQUEST['add-signup-username'];
            $password_c = $_REQUEST['add-signup-password'];    
            $first_name = $_REQUEST['add-signup-first-name'];
            $department = $_REQUEST['add-signup-department'];
            $last_name = $_REQUEST['add-signup-last-name'];
            $phone_work = $_REQUEST['add-signup-office-phone'];
            $title = $_REQUEST['add-signup-title'];
            $phone_mobile = $_REQUEST['add-signup-mobile'];
            $phone_fax = $_REQUEST['add-signup-fax'];
            $email1 = $_REQUEST['add-signup-email-address'];
            $primary_address_street = $_REQUEST['add-signup-primary-address'];
            $alt_address_street = $_REQUEST['add-signup-other-address'];
            $description = $_REQUEST['add-signup-description'];

               if($sugar_crm_version == 6) {
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
                }
                else if($sugar_crm_version == 7) {
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
                
                $checkUserExists = $objSCP->getUserExists($username_c);
                $getAllEmails = $objSCP->getContactAllEmail();
                
                if(($checkUserExists == true) && (in_array($email1,$getAllEmails) == true))
                {
                    $html .= "<span class='error'>Username and Email Address already exists.</span>";    
                }
                else if($checkUserExists == true) {
                    $html .= "<span class='error'>Username already exists.</span>";  
                }
                else if(in_array($email1,$getAllEmails) == true) {
                    $html .= "<span class='error'>Email Address already exists.</span>";
                }
                else {
                     $isSignUp = $objSCP->set_entry('Contacts',$addSignUp);
                     
                     if($isSignUp != NULL ) {
                        $redirect_url = $_REQUEST['scp_current_url'].'?signup=true';
                        wp_redirect( $redirect_url );    
                     } 
                }         
        }            
        $html .= "</div>";
        
        $html .= "
        <div class='scp-form scp-form-two-col'>
            <form  method='post'>
                <ul>
                    <li>                                          
                                <label>Username :</label>
                                <span><input class='input-text' type='text' name='add-signup-username' id='add-signup-username' value='".$_REQUEST['add-signup-username']."' required /> </span>    
                                                                 
                    </li>
                    <li class='last'>                                            
                                <label>Password :</label>
                                <span><input class='input-text' type='password' name='add-signup-password' id='add-signup-password' value='".$_REQUEST['add-signup-password']."' required /> </span>                                                  
                    </li>
                    
                    <li>                                          
                                <label>First Name :</label>
                                <span><input class='input-text' type='text' name='add-signup-first-name' id='add-signup-first-name' value='".$_REQUEST['add-signup-first-name']."' required /> </span>                                            
                    </li>
                    <li class='last'>                                            
                                <label>Department :</label>
                                <span><input class='input-text' type='text' name='add-signup-department' id='add-signup-department' value='".$_REQUEST['add-signup-department']."' /> </span>                                            
                    </li>
                    
                    <li>                                            
                                <label>Last Name :</label>
                                <span><input class='input-text' type='text' name='add-signup-last-name' id='aadd-signup-last-name' value='".$_REQUEST['add-signup-last-name']."' required /> </span>                                            
                    </li>
                    <li class='last'>                                            
                                <label>Office Phone :</label>
                                <span><input class='input-text' type='text' name='add-signup-office-phone' id='add-signup-office-phone' value='".$_REQUEST['add-signup-office-phone']."' /> </span>                                            
                    </li>                                    
                    
                    <li>                                            
                                <label>Title :</label>
                                <span><input class='input-text' type='text' name='add-signup-title' id='add-signup-title' value='".$_REQUEST['add-signup-title']."' /> </span>                                            
                    </li>
                    <li class='last'>                                            
                                <label>Mobile :</label>
                                <span><input class='input-text' type='text' name='add-signup-mobile' id='add-signup-mobile' value='".$_REQUEST['add-signup-mobile']."' /> </span>                                            
                    </li>
                    
                    <li>                                            
                                <label>Email Address :</label>
                                <span><input class='input-text' type='email' name='add-signup-email-address' id='add-signup-email-address' value='".$_REQUEST['add-signup-email-address']."' required /> </span>                                             
                    </li>
                    <li class='last'>                                            
                                <label>Fax :</label>
                                <span><input class='input-text' type='text' name='add-signup-fax' id='add-signup-fax' value='".$_REQUEST['add-signup-fax']."' /> </span>                                            
                    </li>

                    <li>                                          
                                <label>Primary Address :</label>
                                <span>
                                    <textarea class='input-text' id='add-signup-primary-address' name='add-signup-primary-address' > ".$_REQUEST['add-signup-primary-address']." </textarea>
                                </span>                                             
                    </li>
                    <li class='last'>                                            
                                <label>Other Address :</label>
                                <span>
                                    <textarea class='input-text' id='add-signup-other-address' name='add-signup-other-address' > ".$_REQUEST['add-signup-other-address']." </textarea>
                                </span>                                              
                    </li>
                    
                    <li>                                          
                                <label>Description:</label>
                                <span>
                                    <textarea class='input-text' id='add-signup-description' name='add-signup-description' > ".$_REQUEST['add-signup-description']." </textarea>
                                </span>                                          
                    </li>
                    <li class='last'>                                            
                                <label></label>
                               <span></span>                                          
                    </li>
                    
                    <li class='scp-send'>
                                <input type='hidden' name='scp_current_url' value='".$current_url."'>
                                <span class='desc'><input type='submit' name='add-sign-up' value='Submit' /></span>
                    </li>    
                </ul>   
            </form>
        </div>";
        
        return $html;                            
}

function sugar_crm_portal_forgot_password( $html = "" ) {
    // forgot password
    
    $current_url = explode('?', $_SERVER['REQUEST_URI'], 2);
    $current_url = $current_url[0].'?scp-page=forgot-password';
    
        $html .= "<div class='scp-entry-header'>
            <h3>Forgot Password</h3>";
                            
            if($_REQUEST['success'] == true) {                                                                                       
                $html .= "<span class='success'>Your password was send successfully. Thanks.</span>";
            }
            
            if($_REQUEST['error'] == 1) {                                                                                       
                $html .= "<span class='error'>Failed to send your your password. Please try later or contact the administrator by another method.</span>";
            }
            
            if($_REQUEST['error'] == 2) {                                                                                       
                $html .= "<span class='error'>Your email address not match.</span>";
            }
            
            if($_REQUEST['error'] == 3) {                                                                                       
                $html .= "<span class='error'>Your username not exists.</span>";
            }
                    
        $html .= "</div>";
        
        $html .= "
        <div class='scp-form scp-form-two-col'>
            <form action='".site_url()."/wp-admin/admin-post.php' method='post'>
                <ul>
                    <li>                                          
                                <label>Enter Username :</label>
                                <span><input class='input-text' type='text' name='forgot-password-username' id='forgot-password-username' required /> </span>    
                                                                 
                    </li>
                    <li>                                          
                                <label>Enter Email Address :</label>
                                <span><input class='input-text' type='email' name='forgot-password-email-address' id='forgot-password-email-address' required /> </span>    
                                                                 
                    </li>
                    <li class='scp-send'>
                    <input type='hidden' name='action' value='scp_forgot_password'>
                                <input type='hidden' name='scp_current_url' value='".$current_url."'>
                                <span class='desc'><input type='submit' value='Submit' /></span>
                    </li>    
                </ul>   
            </form>
        </div>";
        
        return $html;
} 

function sugar_crm_portal_index( $html = "" ) {  // index
    global $sugar_crm_version;
    
    $scp_sugar_rest_url = get_option('biztech_scp_rest_url');           
    $scp_sugar_username = get_option('biztech_scp_username');
    $scp_sugar_password = get_option('biztech_scp_password');
    $current_url = explode('?', $_SERVER['REQUEST_URI'], 2);
    $current_url = $current_url[0];
    
    $objSCP = new SugarRestApiCall($scp_sugar_rest_url, $scp_sugar_username, $scp_sugar_password); 
		$html .= "<div class='userinfo'>Welcome <span><a href='?scp-page=profile'>".$_SESSION['scp_user_account_name']."</a> </span>&nbsp; <a href='?logout=true'> (Log Out)</a></div>";
		
        $html .= "<div class='scp-container'>
            <ul class='scp-tab'> 
                <li class='";
                    if(($_REQUEST['scp-page'] == NULL) || ($_REQUEST['scp-page'] == 'show-all-cases') ) { $html .= 'active'; }
                    $html .= "'><a href='$current_url'>Show All Cases</a>
                </li>
                <li class='";
                    if($_REQUEST['scp-page'] == 'add-new-case') { $html .= 'active'; }
                    $html .= "'><a href='?scp-page=add-new-case'>Add New Case</a>
                </li>
                <li class='";
                    if($_REQUEST['scp-page'] == 'profile') { $html .='active'; }
                    $html .= "'><a href='?scp-page=profile'>Profile</a>
                </li>
                <li class='";
                    if($_REQUEST['scp-page'] == 'change-password') { $html .= 'active'; }
                    $html .= "'><a href='?scp-page=change-password'>Change Password</a>
                </li>
            </ul>";
                 
            $html .= "<div class='scp-tab-content'>";                
                    if($_REQUEST['scp-page'] == NULL) {   
                            $html .= "<div class='scp-entry-header'>
                                <h3>Show All Cases</h3>"; 
                                    $limit = get_option('biztech_scp_case_per_page');                                    
                                    $page_no = $_REQUEST['page_no']; 
                                    $offset = ($page_no * $limit) - $limit;                                  
                                    
                                    if($sugar_crm_version == 6) {
                                        if((isset($_REQUEST['order_by']) == true ) && (isset($_REQUEST['order']) == true) ){
                                            $order_by = "$_REQUEST[order_by] $_REQUEST[order]";
                                        }
                                        else {
                                            $order_by = "";    
                                        }
                                        
                                        $getAllCases = $objSCP->getRelatedCasefor_LoggedContact($_SESSION['scp_user_id'], $limit, $offset,$order_by); 
                                        $countCases = count($objSCP->getRelatedCasefor_LoggedContact_Count($_SESSION['scp_user_id'])->entry_list);  
                                        
                                        if($getAllCases->entry_list != NULL){                                      
                                            $html .= "<div class='scp-table-responsive'>
                                            <table id='example' class='display' cellspacing='0' width='100%'>
                                                <thead>
                                                    <tr class='row main-col'>";                                                
                                                        if(($_REQUEST['order_by'] == 'case_number') && ($_REQUEST['order'] == 'desc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=case_number&order=asc' class='scp-desc-sort'>Case Number</a></th>";        
                                                        } else if(($_REQUEST['order_by'] == 'case_number') && ($_REQUEST['order'] == 'asc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=case_number&order=desc' class='scp-asc-sort'>Case Number</a></th>";     
                                                        } else {
                                                            $html .= "<th><a href='$current_url?order_by=case_number&order=asc' class='scp-both-sort'>Case Number</a></th>";    
                                                        }
                                                        
                                                        if(($_REQUEST['order_by'] == 'name') && ($_REQUEST['order'] == 'desc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=name&order=asc' class='scp-desc-sort'>Case Name</a></th>";        
                                                        } else if(($_REQUEST['order_by'] == 'name') && ($_REQUEST['order'] == 'asc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=name&order=desc' class='scp-asc-sort'>Case Name</a></th>";     
                                                        } else {
                                                            $html .= "<th><a href='$current_url?order_by=name&order=asc' class='scp-both-sort'>Case Name</a></th>";    
                                                        }
                                                        
                                                        if(($_REQUEST['order_by'] == 'date_entered') && ($_REQUEST['order'] == 'desc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=date_entered&order=asc' class='scp-desc-sort'>Date Created</a></th>";        
                                                        } else if(($_REQUEST['order_by'] == 'date_entered') && ($_REQUEST['order'] == 'asc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=date_entered&order=desc' class='scp-asc-sort'>Date Created</a></th>";     
                                                        } else {
                                                            $html .= "<th><a href='$current_url?order_by=date_entered&order=asc' class='scp-both-sort'>Date Created</a></th>";    
                                                        }
                                                        
                                                        if(($_REQUEST['order_by'] == 'priority') && ($_REQUEST['order'] == 'desc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=priority&order=asc' class='scp-desc-sort'>Priority</a></th>";        
                                                        } else if(($_REQUEST['order_by'] == 'priority') && ($_REQUEST['order'] == 'asc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=priority&order=desc' class='scp-asc-sort'>Priority</a></th>";     
                                                        } else {
                                                            $html .= "<th><a href='$current_url?order_by=priority&order=asc' class='scp-both-sort'>Priority</a></th>";    
                                                        }
                                                        
                                                        if(($_REQUEST['order_by'] == 'status') && ($_REQUEST['order'] == 'desc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=status&order=asc' class='scp-desc-sort'>Status</a></th>";        
                                                        } else if(($_REQUEST['order_by'] == 'status') && ($_REQUEST['order'] == 'asc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=status&order=desc' class='scp-asc-sort'>Status</a></th>";     
                                                        } else {
                                                            $html .= "<th><a href='$current_url?order_by=status&order=asc' class='scp-both-sort'>Status</a></th>";    
                                                        }                                                                                                                                                  
                                                        
                                                        $html .= "<th>Action</th>
                                                    </tr>
                                                 </thead>";
                                                 
                                            foreach($getAllCases as $getAllCasesObj) {
                                                  foreach($getAllCasesObj as $setAllCases) {     
                                                      $setAllCasesObj = $setAllCases->name_value_list;
                                                      
                                                      if($setAllCasesObj->priority->value == 'P1') {
                                                          $priority = 'High';
                                                      } else if($setAllCasesObj->priority->value == 'P2') {
                                                          $priority = 'Medium';    
                                                      } else if($setAllCasesObj->priority->value == 'P3') {
                                                          $priority = 'Low';
                                                      } else {
                                                          $priority = $setAllCasesObj->priority->value;
                                                      } 
                                                        $html .= "<tr>
                                                            <td>".$setAllCasesObj->case_number->value."</td>
                                                            <td>".$setAllCasesObj->name->value."</td>
                                                            <td>".date('d-m-Y', strtotime($setAllCasesObj->date_entered->value))."</td>
                                                            <td>".$priority."</td>
                                                            <td>".$setAllCasesObj->status->value."</td>
                                                            <td><a href='?scp-page=case-details&case_id=".$setAllCasesObj->id->value."'>Case Details</a></td>                                                        
                                                        </tr>";               
                                                  }
                                            }
                                          $html .= "</table></div>"; 
                                          $html .= pagination($countCases,$limit,$page_no); 
                                        }
                                        else {                                   
                                            $html .= "<strong>No Record Found.</strong>"; 
                                        }     
                                    }
                                    else if($sugar_crm_version ==7) {
                                        if((isset($_REQUEST['order_by']) == true ) && (isset($_REQUEST['order']) == true) ){
                                            $order_by = "$_REQUEST[order_by]:$_REQUEST[order]";  
                                        }
                                        else {
                                            $order_by = "";    
                                        }
                                        
                                        $getAllCases = $objSCP->getRelatedCasefor_LoggedContact($_SESSION['scp_user_id'], $limit, $offset,$order_by);
                                        $countCases = count($objSCP->getRelatedCasefor_LoggedContact_Count($_SESSION['scp_user_id'])->records);
                                        
                                        if($getAllCases->records != NULL){                                      
                                            $html .= "<div class='scp-table-responsive'>
                                            <table id='example' class='display' cellspacing='0' width='100%'>
                                                <thead>
                                                    <tr class='row main-col'>";                                                
                                                        if(($_REQUEST['order_by'] == 'case_number') && ($_REQUEST['order'] == 'desc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=case_number&order=asc' class='scp-desc-sort'>Case Number</a></th>";        
                                                        } else if(($_REQUEST['order_by'] == 'case_number') && ($_REQUEST['order'] == 'asc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=case_number&order=desc' class='scp-asc-sort'>Case Number</a></th>";     
                                                        } else {
                                                            $html .= "<th><a href='$current_url?order_by=case_number&order=asc' class='scp-both-sort'>Case Number</a></th>";    
                                                        }
                                                        
                                                        if(($_REQUEST['order_by'] == 'name') && ($_REQUEST['order'] == 'desc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=name&order=asc' class='scp-desc-sort'>Case Name</a></th>";        
                                                        } else if(($_REQUEST['order_by'] == 'name') && ($_REQUEST['order'] == 'asc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=name&order=desc' class='scp-asc-sort'>Case Name</a></th>";     
                                                        } else {
                                                            $html .= "<th><a href='$current_url?order_by=name&order=asc' class='scp-both-sort'>Case Name</a></th>";    
                                                        }
                                                        
                                                        if(($_REQUEST['order_by'] == 'date_entered') && ($_REQUEST['order'] == 'desc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=date_entered&order=asc' class='scp-desc-sort'>Date Created</a></th>";        
                                                        } else if(($_REQUEST['order_by'] == 'date_entered') && ($_REQUEST['order'] == 'asc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=date_entered&order=desc' class='scp-asc-sort'>Date Created</a></th>";     
                                                        } else {
                                                            $html .= "<th><a href='$current_url?order_by=date_entered&order=asc' class='scp-both-sort'>Date Created</a></th>";    
                                                        }
                                                        
                                                        if(($_REQUEST['order_by'] == 'priority') && ($_REQUEST['order'] == 'desc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=priority&order=asc' class='scp-desc-sort'>Priority</a></th>";        
                                                        } else if(($_REQUEST['order_by'] == 'priority') && ($_REQUEST['order'] == 'asc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=priority&order=desc' class='scp-asc-sort'>Priority</a></th>";     
                                                        } else {
                                                            $html .= "<th><a href='$current_url?order_by=priority&order=asc' class='scp-both-sort'>Priority</a></th>";    
                                                        }
                                                        
                                                        if(($_REQUEST['order_by'] == 'status') && ($_REQUEST['order'] == 'desc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=status&order=asc' class='scp-desc-sort'>Status</a></th>";        
                                                        } else if(($_REQUEST['order_by'] == 'status') && ($_REQUEST['order'] == 'asc') ) {
                                                            $html .= "<th><a href='$current_url?order_by=status&order=desc' class='scp-asc-sort'>Status</a></th>";     
                                                        } else {
                                                            $html .= "<th><a href='$current_url?order_by=status&order=asc' class='scp-both-sort'>Status</a></th>";    
                                                        }                                                                                                                                                  
                                                        
                                                        $html .= "<th>Action</th>
                                                    </tr>
                                                 </thead>";
                                                     
                                            foreach($getAllCases->records as $setAllCases) {  
                                                      if($setAllCases->priority == 'P1') {
                                                          $priority = 'High';
                                                      } else if($setAllCases->priority == 'P2') {
                                                          $priority = 'Medium';    
                                                      } else if($setAllCases->priority == 'P3') {
                                                          $priority = 'Low';
                                                      } else {
                                                          $priority = $setAllCases->priority;
                                                      } 
                                                        $html .= "<tr>
                                                            <td>".$setAllCases->case_number."</td>
                                                            <td>".$setAllCases->name."</td>
                                                            <td>".date('d-m-Y', strtotime($setAllCases->date_entered))."</td>
                                                            <td>".$priority."</td>
                                                            <td>".$setAllCases->status."</td>
                                                            <td><a href='?scp-page=case-details&case_id=".$setAllCases->id."'>Case Details</a></td>                                                        
                                                        </tr>";               
                                            }
                                          $html .= "</table></div>"; 
                                          $html .= pagination($countCases,$limit,$page_no); 
                                        }
                                        else {                                   
                                            $html .= "<strong>No Record Found.</strong>"; 
                                        }         
                                    }
                                    else {
                                        
                                    }                               
                            $html .= "</div>";                               
                    }
                    
                    if($_REQUEST['scp-page'] == 'case-details') {
                        if($sugar_crm_version == 6) {
                            $html .= "<div class='scp-entry-header'>";                              
                                        
                                        $getCurrentCaseDetails = $objSCP->getCaseDetail($_REQUEST['case_id']);
                                        $getCurrentCaseDetails = $getCurrentCaseDetails->entry_list[0]->name_value_list;                                                                               
                                        $html .= "<h3>Details Of ".$getCurrentCaseDetails->name->value."</h3>";
                                        
                                                  
                                            $priorityArray = array(
                                                'P1' => 'High',
                                                'P2' => 'Medium',
                                                'P3' => 'Low',
                                            );
                                            
                                            $statusArray = array(
                                                'New' => 'New',
                                                'Assigned' => 'Assigned',
                                                'Closed' => 'Closed',
                                                'Pending Input' => 'Pending Input',
                                                'Rejected' => 'Rejected',
                                                'Duplicate' => 'Duplicate',
                                            );
                                            
                                            $typeArray = array(
                                                'Administration' => 'Administration',
                                                'Product' => 'Product',
                                                'User' => 'User',
                                            );
                                            
                                            $html .= "<div class='scp-entry-header'>";
                                                   
                                                        if(isset($_REQUEST['success_update']) == true) {                                      
                                                            $html .= "<span class='success'> Case Updated Successfully.</span>";                    
                                                        }                              
                                                    $html .= "<div class='scp-form scp-form-two-col'>
                                                    <form action='".site_url()."/wp-admin/admin-post.php' method='post'>
                                                         <ul>                                     
                                                            <li>
                                                                
                                                                    <label>Priority :</label>
                                                                    <span>
                                                                        <select class='input-text' title='' id='update-priority' name='update-priority'>";
                                                                                                                                                   
                                                                            foreach($priorityArray as $key => $value) {
                                                                                if($getCurrentCaseDetails->priority->value == $key) {
                                                                                    $sel = "selected='selected'";    
                                                                                }
                                                                                $html .= "<option value='".$key."' label='".$value."' ".$sel.">".$value."</option>"; 
                                                                                $sel = "";    
                                                                            }    
                                                                        $html .= "</select>
                                                                    </span>
                                                                
                                                            </li>
                                                            
                                                            <li class='last'>
                                                                
                                                                    <label>Status :</label>
                                                                    <span>
                                                                        <select class='input-text' title='' id='update-status' name='update-status'>";
                                                                            foreach($statusArray as $key => $value) {
                                                                                if($getCurrentCaseDetails->status->value == $key) {
                                                                                    $sel = "selected='selected'";    
                                                                                }
                                                                                $html .= "<option value='".$key."' label='".$value."' ".$sel.">".$value."</option>";
                                                                                $sel = "";    
                                                                            } 
                                                                        $html .= "</select>
                                                                    </span>
                                                                
                                                            </li>
                                                            
                                                            <li>
                                                                
                                                                    <label>Type :</label>
                                                                    <span>
                                                                        <select class='input-text' title='' id='update-type' name='update-type'>";
                                                                            foreach($typeArray as $key => $value) {
                                                                                if($getCurrentCaseDetails->type->value == $key) {
                                                                                    $sel = "selected='selected'";    
                                                                                }
                                                                                $html .= "<option value='".$key."' label='".$value."' ".$sel.">".$value."</option>"; 
                                                                                $sel = "";    
                                                                            } 
                                                                        $html .= "</select>
                                                                    </span>
                                                                
                                                            </li>
                                                            
                                                            <li class='last'>
                                                                
                                                                    <label>Subject :</label>
                                                                    <span><input class='input-text' type='text' name='update-name' id='update-name' value='".$getCurrentCaseDetails->name->value."' required /> </span>
                                                                
                                                            </li>
                                                            
                                                            <li>
                                                                
                                                                    <label>Description :</label>
                                                                    <span>
                                                                        <textarea class='input-text' id='update-description' name='update-description' required>".$getCurrentCaseDetails->description->value."</textarea>
                                                                    </span>
                                                                
                                                            </li>
                                                            
                                                            <li class='last'>
                                                                
                                                                    <label>Resolution :</label>
                                                                    <span>
                                                                        <textarea class='input-text' id='update-description' name='update-resolution'>".$getCurrentCaseDetails->resolution->value."</textarea>
                                                                    </span>
                                                                
                                                            </li>
                                                            
                                                            <li class='scp-send'>
                                                                <input type='hidden' name='action' value='scp_update_new_case'>
                                                                <input type='hidden' name='scp_case_id' value='".$_REQUEST['case_id']."'>
                                                                <input type='hidden' name='scp_current_url' value='".$current_url."'>
                                                               <span class='desc'><input type='submit' value='Update' /></span>
                                                            </li>   
                                                         </ul>
                                                    </form>
                                                    </div>
                                                </div>"; 
                                                
                                            $html .= "<div class='all-notes'>
                                                <h3>All Notes</h3>";
                                                $currentCaseId = $_REQUEST['case_id'];
                                                $getCurrentCaseNotes = $objSCP->getNotes($currentCaseId);   
                                                if($getCurrentCaseNotes != NULL) {
                                                    $html .= "<ul>";                                                  
                                                    $cntnotes = 0;
                                                    
                                                    $countNotes = 0;
                                                    foreach($getCurrentCaseNotes as $setCurrentCaseNotesObj) {
                                                        $countNotes++;    
                                                    }
                                                    $countNotes = $countNotes-1;                                                    
                                                    
                                                    foreach($getCurrentCaseNotes as $setCurrentCaseNotesObj) {
                                                        $setCurrentCaseNotes = $setCurrentCaseNotesObj->name_value_list; 
                                                        if($countNotes == $cntnotes) {
                                                            $last = 'last';
                                                        }                                                                                                                                                                                                                                 
                                                        $html .= "<li class='".$last."'>
                                                                <span class='name'>".$setCurrentCaseNotes->name->value."</span>
                                                                <span class='description'>".$setCurrentCaseNotes->description->value."</span>";
                                                                if($setCurrentCaseNotes->filename->value != NULL) {
                                                                    $html .= "<span class='asttachment'> <span>Download: </span>  
                                                                        <form action='".site_url()."/wp-admin/admin-post.php' method='post'>
                                                                            <input type='hidden' name='action' value='scp_get_note_asttachment'>
                                                                            <input type='hidden' name='scp_note_id' value='".$setCurrentCaseNotes->id->value."'>
                                                                            <input type='submit' value='".$setCurrentCaseNotes->filename->value."' class='download-link' />
                                                                        </form>                                                                    
                                                                    </span>";     
                                                                }                                                                
                                                            $html .= "</li>";
                                                        $cntnotes++;
                                                        $last = '';          
                                                    }                                                                                                       
                                                    
                                                    $html .= "</ul>";      
                                                }
                                                else {
                                                    $html .= "<strong>No Record Found.</strong>";     
                                                }
                                             $html .= "</div>"; 
                                            
                                             $html .= "<div class='scp-form scp-form-two-col'>
                                                <h3>Create Notes</h3>";
                                                          if(isset($_REQUEST['success']) == true) {                                       
                                                                    $html .= " <span class='success'>Notes created Successfully.</span>";
                                                            }  
                                               $html .= "<ul>
                                                <form action='".site_url()."/wp-admin/admin-post.php' method='post' enctype='multipart/form-data'>
                                                     <li>
                                                        
                                                            <label>Subject:</label>
                                                            <span><input class='input-text' type='text' name='add-notes-name' id='add-notes-name' required /> </span>
                                                        
                                                     </li>
                                                     <li class='last'>
                                                        
                                                            <label>Attachment:</label>
                                                            <span>
                                                                <input class='input-text' type='file' id='add-notes-attachment' name='add-notes-attachment' />
                                                            </span>
                                                        
                                                     </li>
                                                     
                                                      <li>
                                                            <label>Note:</label>
                                                            <span>
                                                                <textarea class='input-text' id='add-notes-description' name='add-notes-description' required ></textarea>
                                                            </span>
                                                     </li>
                                                         
                                                     
                                                     
                                                     <li class='scp-send'>
                                                           <span>
                                                                <input type='hidden' name='action' value='scp_create_notes'>
                                                                <input type='hidden' name='scp_case_id' value='".$_REQUEST['case_id']."'>
                                                                <input type='hidden' name='scp_current_url' value='".$current_url."?scp-page=case-details&case_id=".$_REQUEST['case_id']."'>
                                                                <input type='submit' value='Submit' onclick='return submitNotesForm()' />
                                                            </span>
                                                        
                                                     </li> 
                                                </form>                                      
                                            </ul>
                                            </div>    
                            </div>";                      
                        }
                        else if($sugar_crm_version == 7) {
                            $html .= "<div class='scp-entry-header'>";                              
                                        
                                        $getCurrentCaseDetails = $objSCP->getCaseDetail($_REQUEST['case_id']);
                                                                            
                                        $html .= "<h3>Details Of ".$getCurrentCaseDetails->name."</h3>";
                                        
                                                  
                                            $priorityArray = array(
                                                'P1' => 'High',
                                                'P2' => 'Medium',
                                                'P3' => 'Low',
                                            );
                                            
                                            $statusArray = array(
                                                'New' => 'New',
                                                'Assigned' => 'Assigned',
                                                'Closed' => 'Closed',
                                                'Pending Input' => 'Pending Input',
                                                'Rejected' => 'Rejected',
                                                'Duplicate' => 'Duplicate',
                                            );
                                            
                                            $typeArray = array(
                                                'Administration' => 'Administration',
                                                'Product' => 'Product',
                                                'User' => 'User',
                                            );
                                            
                                            $html .= "<div class='scp-entry-header'>";
                                                   
                                                        if(isset($_REQUEST['success_update']) == true) {                                      
                                                            $html .= "<span class='success'> Case Updated Successfully.</span>";                    
                                                        }                              
                                                    $html .= "<div class='scp-form scp-form-two-col'>
                                                    <form action='".site_url()."/wp-admin/admin-post.php' method='post'>
                                                         <ul>                                     
                                                            <li>
                                                                
                                                                    <label>Priority :</label>
                                                                    <span>
                                                                        <select class='input-text' title='' id='update-priority' name='update-priority'>";
                                                                                                                                                   
                                                                            foreach($priorityArray as $key => $value) {
                                                                                if($getCurrentCaseDetails->priority == $key) {
                                                                                    $sel = "selected='selected'";    
                                                                                }
                                                                                $html .= "<option value='".$key."' label='".$value."' ".$sel.">".$value."</option>"; 
                                                                                $sel = "";    
                                                                            }    
                                                                        $html .= "</select>
                                                                    </span>
                                                                
                                                            </li>
                                                            
                                                            <li class='last'>
                                                                
                                                                    <label>Status :</label>
                                                                    <span>
                                                                        <select class='input-text' title='' id='update-status' name='update-status'>";
                                                                            foreach($statusArray as $key => $value) {
                                                                                if($getCurrentCaseDetails->status == $key) {
                                                                                    $sel = "selected='selected'";    
                                                                                }
                                                                                $html .= "<option value='".$key."' label='".$value."' ".$sel.">".$value."</option>";
                                                                                $sel = "";    
                                                                            } 
                                                                        $html .= "</select>
                                                                    </span>
                                                                
                                                            </li>
                                                            
                                                            <li>
                                                                
                                                                    <label>Type :</label>
                                                                    <span>
                                                                        <select class='input-text' title='' id='update-type' name='update-type'>";
                                                                            foreach($typeArray as $key => $value) {
                                                                                if($getCurrentCaseDetails->type == $key) {
                                                                                    $sel = "selected='selected'";    
                                                                                }
                                                                                $html .= "<option value='".$key."' label='".$value."' ".$sel.">".$value."</option>"; 
                                                                                $sel = "";    
                                                                            } 
                                                                        $html .= "</select>
                                                                    </span>
                                                                
                                                            </li>
                                                            
                                                            <li class='last'>
                                                                
                                                                    <label>Subject :</label>
                                                                    <span><input class='input-text' type='text' name='update-name' id='update-name' value='".$getCurrentCaseDetails->name."' required /> </span>
                                                                
                                                            </li>
                                                            
                                                            <li>
                                                                
                                                                    <label>Description :</label>
                                                                    <span>
                                                                        <textarea class='input-text' id='update-description' name='update-description' required>".$getCurrentCaseDetails->description."</textarea>
                                                                    </span>
                                                                
                                                            </li>
                                                            
                                                            <li class='last'>
                                                                
                                                                    <label>Resolution :</label>
                                                                    <span>
                                                                        <textarea class='input-text' id='update-description' name='update-resolution'>".$getCurrentCaseDetails->resolution."</textarea>
                                                                    </span>
                                                                
                                                            </li>
                                                            
                                                            <li class='scp-send'>
                                                                <input type='hidden' name='action' value='scp_update_new_case'>
                                                                <input type='hidden' name='scp_case_id' value='".$_REQUEST['case_id']."'>
                                                                <input type='hidden' name='scp_current_url' value='".$current_url."'>
                                                               <span class='desc'><input type='submit' value='Update' /></span>
                                                            </li>   
                                                         </ul>
                                                    </form>
                                                    </div>
                                                </div>"; 
                                             $html .= "<div class='all-notes'>
                                                <h3>All Notes</h3>";
                                                $currentCaseId = $_REQUEST['case_id'];
                                                $getCurrentCaseNotes = $objSCP->getNotes($currentCaseId);
                                                if($getCurrentCaseNotes != NULL) {
                                                    $html .= "<ul>";  
                                                    $cntnotes = 0;
                                                    
                                                    $countNotes = 0;
                                                    foreach($getCurrentCaseNotes as $setCurrentCaseNotesObj) {
                                                        $countNotes++;    
                                                    }
                                                    $countNotes = $countNotes-1;  
                                                    
                                                    foreach($getCurrentCaseNotes as $setCurrentCaseNotes) {
                                                        if($countNotes == $cntnotes) {
                                                            $last = 'last';
                                                        } 
                                                        $html .= "<li class='".$last."'>
                                                                <span class='name'>".$setCurrentCaseNotes->name."</span>
                                                                <span class='description'>".$setCurrentCaseNotes->description."</span>";
                                                                if($setCurrentCaseNotes->filename != NULL) {
                                                                    $html .= "<span class='asttachment'>  <span>Download: </span> 
                                                                        <form action='".site_url()."/wp-admin/admin-post.php' method='post'>
                                                                            <input type='hidden' name='action' value='scp_get_note_asttachment'>
                                                                            <input type='hidden' name='scp_note_id' value='".$setCurrentCaseNotes->id."'>
                                                                            <input type='submit' value='".$setCurrentCaseNotes->filename."' class='download-link' />
                                                                        </form>                                                                    
                                                                    </span>";    
                                                                }                                                                
                                                               
                                                            $html .= "</li>"; 
                                                        $cntnotes++;
                                                        $last = '';         
                                                    } 
                                                    
                                                    $html .= "</ul>";      
                                                }
                                                else {
                                                    $html .= "<strong>No Record Found.</strong>";     
                                                }
                                             $html .= "</div>";                                            
                                                
                                             $html .= "<div class='scp-form scp-form-two-col'>
                                                <h3>Create Notes</h3>";
                                                          if(isset($_REQUEST['success']) == true) {                                       
                                                                    $html .= " <span class='success'>Notes created Successfully.</span>";
                                                            }  
                                               $html .= "<ul>
                                                <form action='".site_url()."/wp-admin/admin-post.php' method='post' enctype='multipart/form-data'>
                                                     <li>
                                                        
                                                            <label>Subject:</label>
                                                            <span><input class='input-text' type='text' name='add-notes-name' id='add-notes-name' required /> </span>
                                                        
                                                     </li>
                                                     <li class='last'>
                                                        
                                                            <label>Attachment:</label>
                                                            <span>
                                                                <input class='input-text' type='file' id='add-notes-attachment' name='add-notes-attachment' />
                                                            </span>
                                                        
                                                     </li>
                                                     
                                                      <li>
                                                            <label>Note:</label>
                                                            <span>
                                                                <textarea class='input-text' id='add-notes-description' name='add-notes-description' required ></textarea>
                                                            </span>
                                                     </li>
                                                         
                                                     
                                                     
                                                     <li class='scp-send'>
                                                           <span>
                                                                <input type='hidden' name='action' value='scp_create_notes'>
                                                                <input type='hidden' name='scp_case_id' value='".$_REQUEST['case_id']."'>
                                                                <input type='hidden' name='scp_current_url' value='".$current_url."?scp-page=case-details&case_id=".$_REQUEST['case_id']."'>
                                                                <input type='submit' value='Submit' onclick='return submitNotesForm()' />
                                                            </span>
                                                        
                                                     </li> 
                                                </form>                                      
                                            </ul>
                                            </div>    
                            </div>";      
                        }
                        else {
                            
                        } 
                                                     
                    }
                    
                    if($_REQUEST['scp-page'] == 'add-new-case') {                       
                            $html .= "<div class='scp-entry-header'>
                                <h3>Add New Case</h3>";
                                    if(isset($_REQUEST['success']) == true) {                                      
                                        $html .= "<span class='success'> Case Added Successfully.</span>";                    
                                    }                              
                                $html .= "<div class='scp-form scp-form-two-col'>
                                <form action='".site_url()."/wp-admin/admin-post.php' method='post'>
                                     <ul>                                     
                                        <li>
                                            
                                                <label>Priority :</label>
                                                <span>
                                                    <select class='input-text' title='' id='add-priority' name='add-priority'>
                                                        <option value='P1' label='High'>High</option>
                                                        <option value='P2' label='Medium'>Medium</option>
                                                        <option value='P3' label='Low'>Low</option>
                                                    </select>
                                                </span>
                                            
                                        </li>
                                        
                                        <li class='last'>
                                            
                                                <label>Status :</label>
                                                <span>
                                                    <select class='input-text' title='' id='add-status' name='add-status'>
                                                        <option value='New' label='New'>New</option>
                                                        <option value='Assigned' label='Assigned'>Assigned</option>
                                                        <option value='Closed' label='Closed'>Closed</option>
                                                        <option value='Pending Input' label='Pending Input'>Pending Input</option>
                                                        <option value='Rejected' label='Rejected'>Rejected</option>
                                                        <option value='Duplicate' label='Duplicate'>Duplicate</option>
                                                    </select>
                                                </span>
                                            
                                        </li>
                                        
                                        <li>
                                            
                                                <label>Type :</label>
                                                <span>
                                                    <select class='input-text' title='' id='add-type' name='add-type'>
                                                        <option value='Administration' label='Administration'>Administration</option>
                                                        <option value='Product' label='Product'>Product</option>
                                                        <option value='User' label='User'>User</option>
                                                    </select>
                                                </span>
                                            
                                        </li>
                                        
                                        <li class='last'>
                                            
                                                <label>Subject :</label>
                                                <span><input class='input-text' type='text' name='add-name' id='add-name' required /> </span>
                                            
                                        </li>
                                        
                                        <li>
                                            
                                                <label>Description :</label>
                                                <span>
                                                    <textarea class='input-text' id='add-description' name='add-description' required></textarea>
                                                </span>
                                            
                                        </li>
                                        
                                        <li class='last'>
                                            
                                                <label>Resolution :</label>
                                                <span>
                                                    <textarea class='input-text' id='add-description' name='add-resolution'></textarea>
                                                </span>
                                            
                                        </li>
                                        
                                        <li class='scp-send'>
                                            <input type='hidden' name='action' value='scp_add_new_case'>
                                            <input type='hidden' name='scp_current_url' value='".$_SERVER['REQUEST_URI']."'>
                                           <span class='desc'><input type='submit' value='Submit' /></span>
                                        </li>   
                                     </ul>
                                </form>
                                </div>
                            </div>";                            
                    }
                    
                    if($_REQUEST['scp-page'] == 'profile') {
                        
                        if($sugar_crm_version == 6) {
                            $getContactInfo = $objSCP->getUserInformation($_SESSION['scp_user_id'])->entry_list[0]->name_value_list;  
                            $html .= "<div class='scp-entry-header'>
                             
                                    <h3>Profile</h3>";
                                        if($_REQUEST['success'] == true) {
                                                $html .= "<span class='success'>Your Profile Updated Successfully.</span>";    
                                        }
                            $html .= "</div>";
                                $html .= "<div class='scp-form scp-form-two-col'>
                                    <form action='".site_url()."/wp-admin/admin-post.php' method='post'>
                                    <ul>
                                        <li>                                          
                                                    <label>First Name :</label>
                                                    <span><input class='input-text' type='text' name='add-profile-first-name' id='add-profile-first-name' value='".$getContactInfo->first_name->value."' required /> </span>                                            
                                        </li>
                                        <li class='last'>                                            
                                                    <label>Department :</label>
                                                    <span><input class='input-text' type='text' name='add-profile-department' id='add-profile-department' value='".$getContactInfo->department->value."'   /> </span>                                            
                                        </li>
                                        
                                        <li>                                            
                                                    <label>Last Name :</label>
                                                    <span><input class='input-text' type='text' name='add-profile-last-name' id='aadd-profile-last-name' value='".$getContactInfo->last_name->value."' required /> </span>                                            
                                        </li>
                                        <li class='last'>                                            
                                                    <label>Office Phone :</label>
                                                    <span><input class='input-text' type='text' name='add-profile-office-phone' id='add-profile-office-phone' value='".$getContactInfo->phone_work->value."' /> </span>                                            
                                        </li>                                    
                                        
                                        <li>                                            
                                                    <label>Title :</label>
                                                    <span><input class='input-text' type='text' name='add-profile-title' id='add-profile-title' value='".$getContactInfo->title->value."' /> </span>                                            
                                        </li>
                                        <li class='last'>                                            
                                                    <label>Mobile :</label>
                                                    <span><input class='input-text' type='text' name='add-profile-mobile' id='add-profile-mobile' value='".$getContactInfo->phone_mobile->value."' /> </span>                                            
                                        </li>
                                        
                                        <li>                                            
                                                    <label>Email Address :</label>
                                                    <span><input class='input-text' type='email' name='add-profile-email-address' id='add-profile-email-address' value='".$getContactInfo->email1->value."' required /> </span>                                             
                                        </li>
                                        <li class='last'>                                            
                                                    <label>Fax :</label>
                                                    <span><input class='input-text' type='text' name='add-profile-fax' id='add-profile-fax' value='".$getContactInfo->phone_fax->value."' /> </span>                                            
                                        </li>

                                        <li>                                          
                                                    <label>Primary Address :</label>
                                                    <span>
                                                        <textarea class='input-text' id='add-profile-primary-address' name='add-profile-primary-address' >".$getContactInfo->primary_address_street->value."</textarea>
                                                    </span>                                             
                                        </li>
                                        <li class='last'>                                            
                                                    <label>Other Address :</label>
                                                    <span>
                                                        <textarea class='input-text' id='add-profile-other-address' name='add-profile-other-address' >".$getContactInfo->alt_address_street->value."</textarea>
                                                    </span>                                              
                                        </li>
                                        
                                        <li>                                          
                                                    <label>Description:</label>
                                                    <span>
                                                        <textarea class='input-text' id='add-profile-description' name='add-profile-description' > ".$getContactInfo->description->value." </textarea>
                                                    </span>                                          
                                        </li>
                                        <li class='last'>                                            
                                                    <label></label>
                                                   <span></span>                                          
                                        </li>
                                        
                                        <li class='scp-send'>
                                                    <input type='hidden' name='action' value='scp_update_profile'>
                                                    <input type='hidden' name='scp_current_url' value='".$_SERVER['REQUEST_URI']."'>
                                                    <span class='desc'><input type='submit' value='Update' /></span>
                                        </li>    
                                    </ul>   
                                    </form>
                                </div>";                             
                        }
                        else if($sugar_crm_version ==7) {
                            $getContactInfo = $objSCP->getUserInformation($_SESSION['scp_user_id']); 
                            $html .= "<div class='scp-entry-header'>
                             
                                    <h3>Profile</h3>";
                                        if($_REQUEST['success'] == true) {
                                                $html .= "<span class='success'>Your Profile Updated Successfully.</span>";    
                                        }
                            $html .= "</div>";
                                $html .= "<div class='scp-form scp-form-two-col'>
                                    <form action='".site_url()."/wp-admin/admin-post.php' method='post'>
                                    <ul>
                                        <li>                                          
                                                    <label>First Name :</label>
                                                    <span><input class='input-text' type='text' name='add-profile-first-name' id='add-profile-first-name' value='".$getContactInfo->first_name."' required /> </span>                                            
                                        </li>
                                        <li class='last'>                                            
                                                    <label>Department :</label>
                                                    <span><input class='input-text' type='text' name='add-profile-department' id='add-profile-department' value='".$getContactInfo->department."'   /> </span>                                            
                                        </li>
                                        
                                        <li>                                            
                                                    <label>Last Name :</label>
                                                    <span><input class='input-text' type='text' name='add-profile-last-name' id='aadd-profile-last-name' value='".$getContactInfo->last_name."' required /> </span>                                            
                                        </li>
                                        <li class='last'>                                            
                                                    <label>Office Phone :</label>
                                                    <span><input class='input-text' type='text' name='add-profile-office-phone' id='add-profile-office-phone' value='".$getContactInfo->phone_work."' /> </span>                                            
                                        </li>                                    
                                        
                                        <li>                                            
                                                    <label>Title :</label>
                                                    <span><input class='input-text' type='text' name='add-profile-title' id='add-profile-title' value='".$getContactInfo->title."' /> </span>                                            
                                        </li>
                                        <li class='last'>                                            
                                                    <label>Mobile :</label>
                                                    <span><input class='input-text' type='text' name='add-profile-mobile' id='add-profile-mobile' value='".$getContactInfo->phone_mobile."' /> </span>                                            
                                        </li>
                                        
                                        <li>                                            
                                                    <label>Email Address :</label>
                                                    <span><input class='input-text' type='email' name='add-profile-email-address' id='add-profile-email-address' value='".$getContactInfo->email1."' required /> </span>                                             
                                        </li>
                                        <li class='last'>                                            
                                                    <label>Fax :</label>
                                                    <span><input class='input-text' type='text' name='add-profile-fax' id='add-profile-fax' value='".$getContactInfo->phone_fax."' /> </span>                                            
                                        </li>

                                        <li>                                          
                                                    <label>Primary Address :</label>
                                                    <span>
                                                        <textarea class='input-text' id='add-profile-primary-address' name='add-profile-primary-address' >".$getContactInfo->primary_address_street."</textarea>
                                                    </span>                                             
                                        </li>
                                        <li class='last'>                                            
                                                    <label>Other Address :</label>
                                                    <span>
                                                        <textarea class='input-text' id='add-profile-other-address' name='add-profile-other-address' >".$getContactInfo->alt_address_street."</textarea>
                                                    </span>                                              
                                        </li>
                                        
                                        <li>                                          
                                                    <label>Description:</label>
                                                    <span>
                                                        <textarea class='input-text' id='add-profile-description' name='add-profile-description' > ".$getContactInfo->description." </textarea>
                                                    </span>                                          
                                        </li>
                                        <li class='last'>                                            
                                                    <label></label>
                                                   <span></span>                                          
                                        </li>
                                        
                                        <li class='scp-send'>
                                                    <input type='hidden' name='action' value='scp_update_profile'>
                                                    <input type='hidden' name='scp_current_url' value='".$_SERVER['REQUEST_URI']."'>
                                                    <span class='desc'><input type='submit' value='Update' /></span>
                                        </li>    
                                    </ul>   
                                    </form>
                                </div>";                
                        }
                        else {
                            
                        }    
                    }
                    
                    if($_REQUEST['scp-page'] == 'change-password') {
                         $current_url = explode('?', $_SERVER['REQUEST_URI'], 2);
                         $current_url = $current_url[0]."?scp-page=change-password"; 
                       $html .= " <div class='scp-entry-header'>                         
                                <h3>Change Password</h3>";                                                                                                       
                                    if($_REQUEST['success'] == true) {
                                        $html .= "<span class='success'>Your password change successfully.</span>";
                                    }
                                    
                                    if($_REQUEST['error'] == 1) {
                                        $html .= "<span class='error'>Your confirm password not match.</span>";                                            
                                    }
                                    
                                    if($_REQUEST['error'] == 2) {
                                            $html .= "<span class='error'>Please enter correct old password.</span>";    
                                    }                                             
                                                                      
                        $html .= "</div>";
                        
                        $html .= "<div class='scp-form scp-form-two-col'>
                                <form action='".site_url()."/wp-admin/admin-post.php' method='post'>
                                <ul>
                                    <li>                                          
                                                <label>Old Password :</label>
                                                <span><input class='input-text' type='password' name='add-profile-old-password' id='add-profile-old-password' required /> </span>                                            
                                    </li>
                                    <li class='last'>                                            
                                                <label></label>
                                                <span></span>                                            
                                    </li>
                                    
                                    <li>                                            
                                                <label>New Password :</label>
                                                <span><input class='input-text' type='password' name='add-profile-new-password' id='add-profile-new-password' required /> </span>                                            
                                    </li>
                                    <li class='last'>                                            
                                                <label>Confirm Password :</label>
                                                <span><input class='input-text' type='password' name='add-profile-confirm-password' id='add-profile-confirm-password' required /> </span>                                            
                                    </li>
                                    
                                    <li class='scp-send'>
                                                <input type='hidden' name='action' value='scp_change_password'>
                                                <input type='hidden' name='scp_current_url' value='".$current_url."'>
                                                <span class='desc'><input type='submit' value='Submit' /></span>
                                    </li>                                    
                                </ul>
                                </form>
                        </div>";
                    }
            $html .= "</div>
        </div>";    
        
        return $html;   
}

add_shortcode('sugar-crm-portal','sugar_crm_portal_shortcode');  // add shortcode [sugar-crm-portal]
function sugar_crm_portal_shortcode( $content = "" ){         
    if(isset($_SESSION['scp_user_id']) == true) {
       $content .= sugar_crm_portal_index();
    }       
    else {
        if($_REQUEST['scp-page'] == 'signup')  {
            $content .= sugar_crm_portal_signup();     
        }
        else if($_REQUEST['scp-page'] == 'forgot-password')  {
            $content .= sugar_crm_portal_forgot_password();     
        }
        else {
               $content .= sugar_crm_portal_check_user_and_login(); 
        }
    }
    return $content;
}  

add_action('init', 'sugar_crm_portal_start_session', 1);  // start session
function sugar_crm_portal_start_session() {
    if(!session_id()) {
        session_start();
    }
}

if(isset($_REQUEST['logout']) == 'true')    // logout 
{    
    add_action('init', 'sugar_crm_portal_louout',1); 
    function sugar_crm_portal_louout() {
        unset($_SESSION['scp_user_id']);
        unset($_SESSION['scp_account_id']);
        unset($_SESSION['scp_user_account_name']);
        $redirect_url = explode('?', $_SERVER['REQUEST_URI'], 2);
        $redirect_url = $redirect_url[0];
        wp_redirect( $redirect_url ); 
        exit;  
    }    
}

add_action('wp_enqueue_scripts','sugar_crm_portal_style_and_script');  // add custom style and script
function sugar_crm_portal_style_and_script()
{
    // css
    wp_enqueue_style( 'scp-style', plugins_url('css/scp-style.css', __FILE__) );
	
    // js
    wp_enqueue_script( 'scp-js', plugins_url('js/scp-js.js', __FILE__),array( 'jquery' ) );
} 

function pagination($total_record,$per_page=10,$page=1,$url='?'){  
    $total = $total_record;
    $adjacents = "2";
     
    $prevlabel = "&lsaquo; Prev";
    $nextlabel = "Next &rsaquo;";
     
    $page = ($page == 0 ? 1 : $page); 
    $start = ($page - 1) * $per_page;                              
     
    $prev = $page - 1;                         
    $next = $page + 1;
     
    $lastpage = ceil($total/$per_page);
     
    $lpm1 = $lastpage - 1; // //last page minus 1
     
    $pagination = "";
    if($lastpage > 1){  
        $pagination .= "<ul class='pagination'>";
        $pagination .= "<li class='page_info'>Page {$page} of {$lastpage}</li>";
             
            if ($page > 1) {
                if(($_REQUEST['order_by'] != NULL) && ($_REQUEST['order'] != NULL)) {
                    $pagination.= "<li><a href='{$url}page_no={$prev}&order_by=$_REQUEST[order_by]&order=$_REQUEST[order]'>{$prevlabel}</a></li>";           
                } else {
                    $pagination.= "<li><a href='{$url}page_no={$prev}'>{$prevlabel}</a></li>";                                                              
                }  
            }
             
        if ($lastpage < 7 + ($adjacents * 2)){  
            for ($counter = 1; $counter <= $lastpage; $counter++){
                if ($counter == $page) {    
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";                   
                }                    
                else { 
                        if(($_REQUEST['order_by'] != NULL) && ($_REQUEST['order'] != NULL)) {
                            $pagination.= "<li><a href='{$url}page_no={$counter}&order_by=$_REQUEST[order_by]&order=$_REQUEST[order]'>{$counter}</a></li>";            
                        } else {
                            $pagination.= "<li><a href='{$url}page_no={$counter}'>{$counter}</a></li>";                                                               
                        }                           
                }     
            }
         
        } elseif($lastpage > 5 + ($adjacents * 2)){
             
            if($page < 1 + ($adjacents * 2)) {
                 
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
                    if ($counter == $page) {
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";
                    }
                    else {
                        if(($_REQUEST['order_by'] != NULL) && ($_REQUEST['order'] != NULL)) {
                            $pagination.= "<li><a href='{$url}page_no={$counter}&order_by=$_REQUEST[order_by]&order=$_REQUEST[order]'>{$counter}</a></li>";            
                        } else {
                            $pagination.= "<li><a href='{$url}page_no={$counter}'>{$counter}</a></li>";                                   
                        }                                    
                    }                  
                }
                
                if(($_REQUEST['order_by'] != NULL) && ($_REQUEST['order'] != NULL)) {
                    $pagination.= "<li class='dot'>...</li>";
                    $pagination.= "<li><a href='{$url}page_no={$lpm1}&order_by=$_REQUEST[order_by]&order=$_REQUEST[order]'>{$lpm1}</a></li>";
                    $pagination.= "<li><a href='{$url}page_no={$lastpage}&order_by=$_REQUEST[order_by]&order=$_REQUEST[order]'>{$lastpage}</a></li>";            
                } else {
                    $pagination.= "<li class='dot'>...</li>";
                    $pagination.= "<li><a href='{$url}page_no={$lpm1}'>{$lpm1}</a></li>";
                    $pagination.= "<li><a href='{$url}page_no={$lastpage}'>{$lastpage}</a></li>";                                                                
                }  
                     
            } elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                
                if(($_REQUEST['order_by'] != NULL) && ($_REQUEST['order'] != NULL)) {
                    $pagination.= "<li><a href='{$url}page_no=1&order_by=$_REQUEST[order_by]&order=$_REQUEST[order]'>1</a></li>";
                    $pagination.= "<li><a href='{$url}page_no=2&order_by=$_REQUEST[order_by]&order=$_REQUEST[order]'>2</a></li>";
                    $pagination.= "<li class='dot'>...</li>";              
                } else {
                    $pagination.= "<li><a href='{$url}page_no=1'>1</a></li>";
                    $pagination.= "<li><a href='{$url}page_no=2'>2</a></li>";
                    $pagination.= "<li class='dot'>...</li>";      
                }  
                
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page) {
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";
                    }
                    else {
                        if(($_REQUEST['order_by'] != NULL) && ($_REQUEST['order'] != NULL)) {
                            $pagination.= "<li><a href='{$url}page_no={$counter}&order_by=$_REQUEST[order_by]&order=$_REQUEST[order]'>{$counter}</a></li>";            
                        } else {
                            $pagination.= "<li><a href='{$url}page_no={$counter}'>{$counter}</a></li>";       
                        } 
                    }                  
                }
                 
                if(($_REQUEST['order_by'] != NULL) && ($_REQUEST['order'] != NULL)) {
                    $pagination.= "<li class='dot'>..</li>";
                    $pagination.= "<li><a href='{$url}page_no={$lpm1}&order_by=$_REQUEST[order_by]&order=$_REQUEST[order]'>{$lpm1}</a></li>";
                    $pagination.= "<li><a href='{$url}page_no={$lastpage}&order_by=$_REQUEST[order_by]&order=$_REQUEST[order]'>{$lastpage}</a></li>";            
                } else {
                    $pagination.= "<li class='dot'>..</li>";
                    $pagination.= "<li><a href='{$url}page_no={$lpm1}'>{$lpm1}</a></li>";
                    $pagination.= "<li><a href='{$url}page_no={$lastpage}'>{$lastpage}</a></li>"; 
                }     
                 
            } else {
                 
                if(($_REQUEST['order_by'] != NULL) && ($_REQUEST['order'] != NULL)) {
                    $pagination.= "<li><a href='{$url}page_no=1&order_by=$_REQUEST[order_by]&order=$_REQUEST[order]'>1</a></li>";
                    $pagination.= "<li><a href='{$url}page_no=2&order_by=$_REQUEST[order_by]&order=$_REQUEST[order]'>2</a></li>";
                    $pagination.= "<li class='dot'>..</li>";             
                } else {
                    $pagination.= "<li><a href='{$url}page_no=1'>1</a></li>";
                    $pagination.= "<li><a href='{$url}page_no=2'>2</a></li>";
                    $pagination.= "<li class='dot'>..</li>";      
                }  
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page) {
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";
                    }
                    else {
                        if(($_REQUEST['order_by'] != NULL) && ($_REQUEST['order'] != NULL)) {
                            $pagination.= "<li><a href='{$url}page_no={$counter}&order_by=$_REQUEST[order_by]&order=$_REQUEST[order]'>{$counter}</a></li>";            
                        } else {
                            $pagination.= "<li><a href='{$url}page_no={$counter}'>{$counter}</a></li>";       
                        }                         
                    }                  
                }
            }
        }
         
            if ($page < $counter - 1) {
                           
                if(($_REQUEST['order_by'] != NULL) && ($_REQUEST['order'] != NULL)) {
                    $pagination.= "<li><a href='{$url}page_no={$next}&order_by=$_REQUEST[order_by]&order=$_REQUEST[order]'>{$nextlabel}</a></li>";           
                } else {
                    $pagination.= "<li><a href='{$url}page_no={$next}'>{$nextlabel}</a></li>";   
                }
            }
         
        $pagination.= "</ul>";       
    } 
         
    return $pagination;
} 

register_activation_hook( __FILE__, 'scp_folder' );
function scp_folder() {  
  $upload_dir = wp_upload_dir();
  $upload_scp_uploads = $upload_dir['basedir']."/scp-uploads";
  if (!is_dir($upload_scp_uploads)) {
    wp_mkdir_p($upload_scp_uploads);
    }
}

function scp_deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!scp_deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }

    }

    return rmdir($dir);
}

register_uninstall_hook( __FILE__, 'sugar_crm_portal_uninstall' ); // uninstall plug-in 
function sugar_crm_portal_uninstall(){ 
    delete_option('biztech_scp_name');
    delete_option('biztech_scp_rest_url');
    delete_option('biztech_scp_username');         
    delete_option('biztech_scp_password');
    
    $upload_dir = wp_upload_dir();
    $upload_scp_uploads = $upload_dir['basedir']."/scp-uploads";
    
    if (is_dir($upload_scp_uploads)) {
        scp_deleteDirectory($upload_scp_uploads);
    }   
}                       
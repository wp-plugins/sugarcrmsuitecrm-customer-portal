=== SugarCRM/SuiteCRM Customer Portal ===
Contributors: biztechc
Tags: SugarCRM/SuiteCRM Customer Portal,sugarcrm,suitecrm,CRM,Case 
Requires at least: 3.6.1
Tested up to: 4.0
Stable tag: 1.0.0
License: GPLv2 or later

This plug-in use for sugarcrm. It manage Cases services-releated problems reported by your users and customers. 

== Description ==

This WordPress plugin can help SugarCRM/SuiteCRM Portal users to manage their customer's complaints and grievances easily by offering them an interface to lodge their complaint, which they can use to find the current status and follow ups of their lodged complaints also.  

Plugin's Short Code: [sugar-crm-portal]

= Demo =

You can check here (http://wpdemo.biztechconsultancy.com).


== Installation ==

= From your WordPress dashboard =

1. Visit 'Plugins > Add New'
2. Search for 'SugarCRM/SuiteCRM Customer Portal'
3. Activate SugarCRM/SuiteCRM Customer Portal from your Plugins page.

= From WordPress.org =

1. Download SugarCRM/SuiteCRM Customer Portal.
2. Upload the 'SugarCRM/SuiteCRM Customer Portal' directory to your '/wp-content/plugins/' directory, using your favorite method (ftp, sftp, scp, etc...)
3. Activate SugarCRM/SuiteCRM Customer Portal from your Plugins page. 
4. You can set setting.
5. Use short code at any pages/posts e.g.[sugar-crm-portal]

= Wordpress Side Settings =

1. Portal Name: Add your portal name
2. Version: Select SugarCRM version
3. REST URL: 
	* SugarCRM Version 6:- {SugarCRM Site URL}/service/v4_1/rest.php 
	* SugarCRM Version 7:- {SugarCRM Site URL}/rest/v10/ 
	* SuiteCRM Version 7:- {SuiteCRM Site URL}/service/v4_1/rest.php 
4. Username: Add your SugarCRM admin username
5. Password: Add your SugarCRM admin password
6. Cases Per Page: Allow number of Cases to display on page when using pagination

= SugarCRM/SuiteCRM Side Settings =

Our plugin requires 2 fields in Contacts module of SugarCRM/ SuiteCRM instance being integrated, these are, username and password, which is used by the Portal to log the user into the SugarCRM/SuiteCRM , and display his/her case details.

Follow below steps to create these fields in SugarCRM/SuiteCRM  after installing the Wordpress Portal plugin into your Wordpress instance.

1. Create 'username_c' and 'password_c' fields in Contacts
	* Go to Admin -> Studio -> Contacts Module -> Fields -> Add Field
		1. Give name and label of the field.
		2. Check Required checkbox  and click Create.
		3. Both fields should be of varchar type.
		4. Follow this process for both the fields.
2. Place both the fields in Editview and Detailview.
	* Go to Admin -> Studio -> Contacts Module -> Layouts -> EditView/DetailView
		1. Drag and Drop field from left side panel to desired place.
		2. Follow same steps for both Edit and  Detail View.
   

== Frequently Asked Questions ==
Is this plugin prepared for multisites? Yes.

== Screenshots ==

1. screenshot-1.png

2. screenshot-2.png

3. screenshot-3.png

4. screenshot-4.png

5. screenshot-5.png

6. screenshot-6.png

7. screenshot-7.png

8. screenshot-8.png

9. screenshot-9.png


== Changelog ==
= 1.0.0 =
* Stable Version release

== Upgrade Notice ==




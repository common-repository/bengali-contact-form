<?php
/*
Plugin Name: Bengali Contact Form
Plugin URI: http://www.ojobs.co.uk/products/bengali-contact-form/
Description: Creative Bengali Contact Form with attachment support and also include Empty Form validation.</br>Before using this plugin, you need to edit this plugin. Please go to "Plugin Editor" and (Bengali Contact Form.php) replace 'yourname@yourdomain.com' with your own email id then save the plugin. Now it's ready to use. You just need to put the shortcode [bcf_form]  in the post or page and it'll be start. Thanks for your support. Good Luck to your success.
Author: Abu Saeed Mohammad Sayem
Version: 1.0
Author URI: http://www.facebook.com/msisayeed
*/

function bcf_markup() {

$form_action    = get_permalink();
$author_default = $_COOKIE['comment_author_'.COOKIEHASH];
$email_default  = $_COOKIE['comment_author_email_'.COOKIEHASH];

if ( ($_SESSION['contact_form_success']) ) {
$contact_form_success = '<p style="color:#0066FF"><strong>আপনার ই-মেইলের জন্যে ধন্যবাদ,আমরা আপনার সাথে যোগাযোগ করবো.</strong></p>';
unset($_SESSION['contact_form_success']);
}

$markup = <<<EOT

<div id="commentform"><h3>যোগাযোগ করুনঃ</h3>

	{$contact_form_success}
     
   <form onsubmit="return validateForm(this);" action="{$form_action}" method="post" enctype="multipart/form-data" style="text-align: left">
   
   <p><input type="text" name="author" id="author" value="{$author_default}" size="22" /> <label for="author"><strong>আপনার নাম লিখুন *</strong></label></p>
   <p><input type="text" name="email" id="email" value="{$email_default}" size="22" /> <label for="email"><strong>আপনার ইমেইল ঠিকানা লিখুন *</strong></label></p>
   <p><input type="text" name="subject" id="subject" value="" size="22" /> <label for="subject"><strong>বিষয় উল্লেখ্ করুন *</strong></label></p>
   <p><textarea name="message" id="message" cols="50%" rows="10">এখানে আপনার বার্তা লিখুন...</textarea></p>
   <p><label for="attachment"><strong>ফাইল আপলোড/সংযুক্ত করুন</strong></label> <input type="file" name="attachment" id="attachment" /></p>
   <p><input name="send" type="submit" id="send" value="বার্তা পাঠান" /> <input name="reset" type="reset" id="reset" value="মুছুন" /> </p>
   
   <input type="hidden" name="contact_form_submitted" value="1">
   
   </form>
   
</div>

EOT;

return $markup;

}

add_shortcode('bcf_form', 'bcf_markup');

function contact_form_process() {

session_start();

 if ( !isset($_POST['contact_form_submitted']) ) return;

 $author  = ( isset($_POST['author']) )  ? trim(strip_tags($_POST['author'])) : null;
 $email   = ( isset($_POST['email']) )   ? trim(strip_tags($_POST['email'])) : null;
 $subject = ( isset($_POST['subject']) ) ? trim(strip_tags($_POST['subject'])) : null;
 $message = ( isset($_POST['message']) ) ? trim(strip_tags($_POST['message'])) : null;

 if ( $author == '' ) wp_die('ইরর: প্রতিটি বক্সে লিখুন/আপনার নাম (name).'); 
 if ( !is_email($email) ) wp_die('ইরর: সঠিক ইমেইল ঠিকানা লিখুন.');
 if ( $subject == '' ) wp_die('ইরর: কোন বিষয় উল্লেখ করুন/বিষয় (subject).');
 
 //we will add e-mail sending support here soon
 
require_once ABSPATH . WPINC . '/class-phpmailer.php';
$mail_to_send = new PHPMailer();

$mail_to_send->FromName = $author;
$mail_to_send->From     = $email;
$mail_to_send->Subject  = $subject;
$mail_to_send->Body     = $message;

$mail_to_send->AddReplyTo($email);
$mail_to_send->AddAddress('yourname@yourdomain.com'); //contact form destination e-mail

if ( !$_FILES['attachment']['error'] == 4 ) { //something was send
	
	if ( $_FILES['attachment']['error'] == 0 && is_uploaded_file($_FILES['attachment']['tmp_name']) )
	
		$mail_to_send->AddAttachment($_FILES['attachment']['tmp_name'], $_FILES['attachment']['name']);
	
	else 
		
		wp_die('ইরর: নেট সমস্যার কারনে ফাইল আপলোড হচ্ছে না ,আবার ট্রাই করুন.(Please Try again later)');
		
}

if ( !$mail_to_send->Send() ) wp_die('ইরর : মেসেজ পাঠানো যাচ্ছে না - বাংলা কনট্যাক্ট ফর্ম সিস্টেম : ' . $mail_to_send->ErrorInfo);

$_SESSION['contact_form_success'] = 1;

 
 header('Location: ' . $_SERVER['HTTP_REFERER']);
 exit();

} 

add_action('init', 'contact_form_process');

function contact_form_js() { ?>

<script type="text/javascript">
function validateForm(form) {

	var errors = '';
	var regexpEmail = /\w{1,}[@][\w\-]{1,}([.]([\w\-]{1,})){1,3}$/;
		
	if (!form.author.value) errors += "ইরর : নাম লিখুন (name).\n";
	if (!regexpEmail.test(form.email.value)) errors += "ইরর : ইমেইল লিখুন.\n";
	if (!form.subject.value) errors += "ইরর : বিষয় লিখুন(subject).\n";

	if (errors != '') {
		alert(errors);
		return false;
	}
	
return true;
	
}
</script>

<?php }

add_action('wp_head', 'contact_form_js');

?>
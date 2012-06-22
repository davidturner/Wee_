<?php
if($site->akismet != ''){
  $WordPressAPIKey = $site->akismet;
  $MyBlogURL = $site->url;
  $akismet = new Akismet($site->url ,$site->akismet);
}
$printMail = false;
$send = false;
$contactMsg = '';
if(isset($_POST['name']) && $_POST['name'] == "" || isset($_POST['email']) && $_POST['email'] == "" || isset($_POST['comment']) && $_POST['comment'] == ""){
  $printMail = true;
  $contactMsg = '<p class="contact-warning">Your message doesn\'t have all the required information. Please check the form below and make all required fields are filled out.</p>';
}elseif(isset($_POST['important-input']) && $_POST['important-input'] == ""){


  $contact['name'] = trim($_POST['name']);
  $contact['email'] = trim($_POST['email']);
  $contact['contact'] = trim($_POST['contact']);

  $akismet->setCommentAuthor($contact['name']);
  $akismet->setCommentAuthorEmail($contact['email']);
  $akismet->setCommentContent($contact['contact']);
  $akismet->setPermalink($site->url.$site->query);


  if($akismet->isCommentSpam()){
    //  Probably Spam, but better to keep it just in case. Flag spam files in the naming structure too (spam.date.md)
    $contactMsg = '<p class="contact-flagged">Unfortunately your message has been flagged as spam. You may want to consider rewording it below.</p>';
  }else{
    $send = true;
    $contactMsg = '<p class="contact-success">Your message has been successfully sent. Thank you for getting in touch.</p>';

    $emailtxt = "<h2>Contact Form Submission from ".$site->url."</h2><hr />";
  	$emailtxt .= "<p>Name: ".$contact['name']."<br />Email: ".$contact['email']."<br />Message: ".$contact['contact']."</p>";

  	$to      = $site->author->email;
  	$subject = "Website enquiry from ".$contact['name']." - ".$site->url;
  	$message = "<html><body>".Markdown(stripslashes($emailtxt))."</body></html>";

  	$headers  = 'From: '.$site->title.' Mailer <'.str_replace('http://', 'form-mailer@', $site->url).'> '."\n";
  	$headers .= 'Reply-To: '.$contact['email'].''."\n";
  	$headers .= "MIME-Version: 1.0\n";
  	$headers .= "Content-Type: text/html; charset=ISO-8859-1\n";
  	$headers .= 'X-Mailer: PHP/' . phpversion();

  	mail($to, $subject, $message, $headers);
  	unlink($site->cachefile);
  	header('Location: /');
  	die;
  }
}
?>
<form id="contact-form" action="<?php echo $site->query; ?>" method="post">
  <?=$contactMsg;?>
  <div class="commenter-data">
    <input type="hidden" name="important-input" id="important-input" />
    <label for="name">Name (required):</label>
    <input type="text" id="name" name="name" placeholder="Your Name..." value="<?php if($printMail){ echo $_POST['name'];} ?>" required />
    <label for="email">Email Address (required):</label>
    <input type="email" id="email" name="email" placeholder="Your Email Address..." value="<?php if($printMail){ echo $_POST['email'];} ?>" required />
  </div>
  <div class="comment-message">
    <label for="contact" class="contact-label">Your Message (required):</label>
    <textarea name="contact" id="contact" cols="30" rows="10" placeholder="Your Message..." required><?php if($printMail){ echo $_POST['comment'];} ?></textarea>
    <input type="submit" value="Send Message" />
  </div>
</form>
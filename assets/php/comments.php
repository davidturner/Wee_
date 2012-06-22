<?php
if($site->akismet != ''){
  $WordPressAPIKey = $site->akismet;
  $MyBlogURL = $site->url;
  $akismet = new Akismet($site->url ,$site->akismet);
}

$comments->post = 'categories'.$site->query;
$comments->dir = $comments->post.'comments/';

$md = new Markdownify_Extra;

$post['name'] = '';
$post['email'] = '';
$post['site-url'] = '';
$post['comment'] = '';
$commentMsg = '';
$allow = array('important-input');

if(count(array_filter($_POST)) == 0){
  $site->process = 0;
} else {
  foreach ($_POST as $key => $value) {
    $post[$key] = trim($value);
    if($value == '' && !in_array($key, $allow)) { $site->process = 0; }
  }
  if($key == 'important-input' && $value != ''){
    $site->process = 0;
  }
}

if($site->process){
  $akismet->setCommentAuthor($post["name"]);
  $akismet->setCommentAuthorEmail($post["email"]);
  $akismet->setCommentAuthorURL($post["site-url"]);
  $akismet->setCommentContent($post["comment"]);
  $akismet->setPermalink($site->url.$site->query);

  $finalcomment["name"] = strip_tags($post["name"]);
  $finalcomment["email"] = strip_tags($post["email"]);
  $finalcomment["gravatar-url"] = "http://www.gravatar.com/avatar/".md5($finalcomment["email"]).'?d='.urlencode( $site->url.'/avatars/unknown-commenter.png' );
;
  $finalcomment["site-url"] = strip_tags($post["site-url"]);
  $finalcomment["comment"] = '<p>'.str_replace("\n\n", '</p><p>', $post["comment"]).'</p>';
  $finalcomment["comment"] = str_replace("\n", '<br />', $post["comment"]);

  $finalcomment["comment"] = $md->parseString($finalcomment["comment"]);

  if($akismet->isCommentSpam()){
    //  Probably Spam, but better to keep it just in case. Flag spam files in the naming structure too (spam.date.md)
    $finalcomment["flagged"] = "true";
    $commentMsg = '<p class="comment-flagged">Unfortunately your comment has been flagged as spam. The comment has still been saved, but won\'t show until it is determined to be an actual comment.</p>';
  }else{
    $finalcomment["flagged"] = "false";
    $commentMsg = '<p class="comment-success">Your post has been successfully added. Check it out above.</p>';
    $site->purge = 'cache/'.str_replace("/","-",substr($site->query, 1, -1)).'.html';
  }

  $time = time();
  $finalcomment["comment"] = str_replace("<","&lt;",str_replace(">", "&gt;", stripslashes($finalcomment["comment"])));
  $finalcomment["comment"] = str_replace('&lt;a', '<a', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;/a&gt;','</a>',$finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('"&gt;', '">', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;strong&gt;', '<strong>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;/strong&gt;', '</strong>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;b&gt;', '<b>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;/b&gt;', '</b>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;em&gt;', '<em>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;/em&gt;', '</em>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;i&gt;', '<i>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;/i&gt;', '</i>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;del&gt;', '<del>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;/del&gt;', '</del>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;ins&gt;', '<ins>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;/ins&gt;', '</ins>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;blockquote&gt;', '<blockquote>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;/blockquote&gt;', '</blockquote>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;pre&gt;', '<pre>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;/pre&gt;', '</pre>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;code&gt;', '<code>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;/code&gt;', '</code>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;cite&gt;', '<cite>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;/cite&gt;', '</cite>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;q&gt;', '<q>', $finalcomment["comment"]);
  $finalcomment["comment"] = str_replace('&lt;/q&gt;', '</q>', $finalcomment["comment"]);

  $fullcomment = "Comment Author: ".$finalcomment["name"]."\n";
  $fullcomment .= "Email: ".$finalcomment["email"]."\n";
  $fullcomment .= "Gravatar: ".$finalcomment["gravatar-url"]."\n";
  $fullcomment .= "URL: ".$finalcomment["site-url"]."\n";
  $fullcomment .= "Posted on: ".$time."\n";
  $fullcomment .= "No-Follow: true\n";
  $fullcomment .= "Flagged: ".$finalcomment["flagged"]."\n";
  $fullcomment .= "=-=-=\n";
  $fullcomment .= $finalcomment["comment"];

  if($finalcomment["flagged"] == "true"){
    $file = $comments->dir."spam-".$time.".md";
  }else{
    $file = $comments->dir.$time.".md";
  }
  if($finalcomment["flagged"] == "true" && $site->comments == 'all' || $finalcomment["flagged"] == "false"){
    $handle = fopen($file, 'w') or die('Cannot open file:  '.$file);
    fwrite($handle, $fullcomment);

      $commenttxt = "<h2>Comment submitted on your site!</h2><hr />";
    	$commenttxt .= "<p>Name: ".$finalcomment["name"]."<br />Email: ".$finalcomment["email"]."<br />Comment: </p>";
    	$commenttxt .= Markdown($finalcomment["comment"]);
    	$commenttxt .= '<p>You can view this comment on your site <a href="'.$site->url.$site->query.'#comment-'.$time.'">here</a>.</p>';

    	$to      = $site->author->email;
    	$subject = "Comment posted by ".$finalcomment["name"];
    	$message = "<html><body>".$commenttxt."</body></html>";

    	$headers  = 'From: '.$finalcomment["name"].' <'.$finalcomment["email"].'> '."\n";
    	$headers .= 'Reply-To: '.$finalcomment["email"].''."\n";
    	$headers .= "MIME-Version: 1.0\n";
    	$headers .= "Content-Type: text/html; charset=ISO-8859-1\n";
    	$headers .= 'X-Mailer: PHP/' . phpversion();

    	mail($to, $subject, $message, $headers);

    	unlink($site->cachefile);

    	header('Location: '.$site->query.'#comment-'.$time);
    	die;
  }

}

# If comments folder doesn't exist, make it. Make it naow!
if(!is_dir($comments->dir) && is_dir($comments->post)){
  mkdir($comments->dir, 0777);
}

$comments->exist = 0;

$comments->comments = scandir($comments->dir);

//print_r($comments->comments);

echo '<section id="comments-area">';
if(!isset($page->closecomments) && !isset($site->closecomments) && $site->closecomments == 1){
  echo '<h1>Comments</h1>';
}

foreach($comments->comments as $comment){
  if($comment != '.' && $comment != '..' && $comment != '.DS_Store' && !strlen(strstr($comment,"spam"))>0){
    $commentText = $comments->dir.$comment;
    if(file_exists($commentText)){
      $comments->exist = 1;
      $singleComment = getComment($commentText);
      if($singleComment["URL"] != ""){
        $commenter = '<a href="'.$singleComment["URL"].'">'.$singleComment["Comment Author"].'</a>';
        if($singleComment["No-Follow"] == "true"){
          $commenter = str_replace("<a", '<a rel="nofollow"', $commenter);
        }
      }else{
        $commenter = $singleComment["Comment Author"];
      }
      if($singleComment["No-Follow"] == "true"){
        $singleComment["post"] = str_replace("<a ", '<a rel="nofollow" ', $singleComment["post"]);
      }
      echo '<article class="post-comment clearfix" id="comment-'.$singleComment["Posted on"].'">'."\n";
      echo '<img class="gravatar" src="'.$singleComment["Gravatar"].'" alt="'.$singleComment["Comment Author"].'\'s Avatar" />'."\n";
      echo '<div class="comment-content">'."\n";
      echo '<p>Comment by '.$commenter.' on <time datetime="'.date("c",$singleComment["Posted on"]).'">'.date('F \t\h\e jS, Y',$singleComment["Posted on"]).' at '.date("g:ia",$singleComment["Posted on"]).'</time></p>'."\n";
      echo $singleComment["post"].'</div>'."\n";
      echo '</article>'."\n\n";
    }
  }
}

if(!$comments->exist && !isset($page->closecomments) && !isset($site->closecomments) && $site->closecomments == 1){
  echo '<p>No comments have been made on this article just yet. Would you like yours to <a href="#comment-form">be the first</a>?</p>';
}

/*
Comment Form pl0x
*/

if(!isset($page->closecomments) && !$site->closecomments){ ?>
<h2>Have Your Say</h2>
<form id="comment-form" action="#comment-form" method="post">
  <?=$commentMsg;?>
  <div class="commenter-data">
    <input type="hidden" name="important-input" id="important-input" />
    <label for="name">Name (required):</label>
    <input type="text" id="name" name="name" placeholder="Your Name..." value="<?php if(!$site->process){ echo $post["name"];} ?>" required />
    <label for="email">Email Address (required, never shared):</label>
    <input type="email" id="email" name="email" placeholder="Your Email Address..." value="<?php if(!$site->process){ echo $post["email"];} ?>" required />
    <label for="site-url">Site URL:</label>
    <input type="url" id="site-url" name="site-url" placeholder="Your Site's URL..." value="<?php if(!$site->process){ echo $post["site-url"];} ?>" />
  </div>
  <div class="comment-message">
    <label for="comment" class="comment-label">Your Comment (required, supports <a href="http://daringfireball.net/projects/markdown/syntax/">markdown</a> formatting):</label>
    <textarea name="comment" id="comment" cols="30" rows="10" placeholder="Your Comment..." required><?php if(!$site->process){ echo $post["comment"];} ?></textarea>
    <input type="submit" value="Add Comment" />
  </div>
</form>
<?php } elseif(isset($page->closecomments)) { ?>
<h2>Comments are closed</h2>
<p>Unfortunately comments on this post are now closed.</p>
<?php } else { ?>
<h2>Comments on this site are Closed</h2>
<p>Comments have now been closed throughout the site. If you would like to share your thoughts on this post feel free to write something on your own site<?php if(isset($site->author->twitter)){ echo ' or get in touch with me on <a href="http://twitter.com/'.$site->author->twitter.'">twitter</a>'; } ?>.</p>
<?php
}
echo '</section>';
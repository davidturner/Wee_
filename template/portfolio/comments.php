<?php
  $commentMsg = "";
  $post = "categories/".$bits[1]."/".$bits[2].'/';
  $dir = "categories/".$bits[1]."/".$bits[2].'/comments/';
  if(!is_dir($dir) && is_dir($post)){
    mkdir($dir, 0777);
  }
  if(is_dir($dir)){
  $printComment = false;
  if(isset($_POST["name"]) && $_POST["name"] == "" || isset($_POST["email"]) && $_POST["email"] == "" || isset($_POST["comment"]) && $_POST["comment"] == ""){
    $printComment = true;
    $commentMsg = '<p class="comment-warning">Your comment doesn\'t have all the required information. Please check the form below and make all required fields are filled out.</p>';
  }elseif(isset($_POST["important-input"]) && $_POST["important-input"] == ""){
    
    
    $comment["name"] = trim($_POST["name"]);
    $comment["email"] = trim($_POST["email"]);
    $comment["site-url"] = trim($_POST["site-url"]);
    $comment["comment"] = trim($_POST["comment"]);
    
    $akismet->setCommentAuthor($comment["name"]);
    $akismet->setCommentAuthorEmail($comment["email"]);
    $akismet->setCommentAuthorURL($comment["site-url"]);
    $akismet->setCommentContent($comment["comment"]);
    $akismet->setPermalink(url.$path);
    
    $finalcomment["name"] = strip_tags($comment["name"]);
    $finalcomment["email"] = strip_tags($comment["email"]);
    $finalcomment["gravatar-url"] = "http://www.gravatar.com/avatar/".md5($finalcomment["email"]);
    $finalcomment["site-url"] = strip_tags($comment["site-url"]);
    //$finalcomment["comment"] = strip_tags($md->parseString($comment["comment"]), '<a><strong><b><em><i><del><ins><blockquote><pre><code><cite><q>');
    $finalcomment["comment"] = $md->parseString($comment["comment"]);
    
    /* Comment Structure:
      
      Comment Author: [[Name]]
      Email: [[Email]]
      Gravatar: [[Gravatar]]
      URL: [[URL]]
      No-Follow: true
      Flagged: [[Flagged]]
      =-=-=
      [[Comment]]
      
    */
     
    if($akismet->isCommentSpam()){
      //  Probably Spam, but better to keep it just in case. Flag spam files in the naming structure too (spam.date.md)
      $finalcomment["flagged"] = "true";
      $commentMsg = '<p class="comment-flagged">Unfortunately your comment has been flagged as spam. The comment has still been saved, but won\'t show until it is determined to be an actual comment.</p>';
    }else{
      $finalcomment["flagged"] = "false";
      $commentMsg = '<p class="comment-success">Your post has been successfully added. Check it out above.</p>';
      $toPurge = 'cache/'.str_replace("/","-",substr($path, 1, -1)).'.html';
      if(file_exists($toPurge)){
        unlink($toPurge);
      }
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
      $file = $dir."spam-".$time.".md";
    }else{
      $file = $dir.$time.".md";
    }
    if($finalcomment["flagged"] == "true" && defined("saveSpam") && saveSpam || $finalcomment["flagged"] == "false"){
      $handle = fopen($file, 'w') or die('Cannot open file:  '.$file);
      fwrite($handle, $fullcomment);
    }
  }
?>
<section id="comments-area">
  <h1>Comments</h1>
  <?php
    $comments=scandir($dir);
    $showComments = false;
    foreach($comments as $comment){
      if($comment != '.' && $comment != '..' && $comment != '.DS_Store' && !strlen(strstr($comment,"spam"))>0){
        $myText = $dir.$comment;
        //echo $myText;
        if(file_exists($myText)){
          $showComments = true;
          $thecomment = getComment($myText);
          if($thecomment["URL"] != ""){
            $commenter = '<a href="'.$thecomment["URL"].'">'.$thecomment["Comment Author"].'</a>';
            if($thecomment["No-Follow"] == "true"){
              $commenter = str_replace("<a", '<a rel="nofollow"', $commenter);
            }
          }else{
            $commenter = $thecomment["Comment Author"];
          }
          
          //$thecomment["post"] = str_replace(array('"&gt;','" &gt;'),'">',str_replace("&lt;a ", '<a  ', str_replace("&lt;/a&gt;", "</a>", $thecomment["post"])));
          if($thecomment["No-Follow"] == "true"){
            $thecomment["post"] = str_replace("<a ", '<a rel="nofollow" ', $thecomment["post"]);
          }
          echo '<article class="comment" id="comment-'.$thecomment["Posted on"].'">'."\n";
          echo '<img class="gravatar" src="'.$thecomment["Gravatar"].'" alt="'.$thecomment["Comment Author"].'\'s Avatar" />'."\n";
          echo '<div class="comment-content">'."\n";
          echo '<p>Comment by '.$commenter.' on <time datetime="'.date("c",$thecomment["Posted on"]).'">'.date('F \t\h\e jS, Y',$thecomment["Posted on"]).' at '.date("g:ia",$thecomment["Posted on"]).'</time></p>'."\n";
          echo $thecomment["post"].'</div>'."\n";
          echo '</article>'."\n\n";
        }
      }
    }
    if(!$showComments){
      echo '<p>No comments have been made on this article just yet. Would you like yours to <a href="#comment-form">be the first</a>?</p>';
    }
  ?>
  <?php if(!isset($contentMeta["NoComments"])){ ?>
  <h2>Have Your Say</h2>
  <form id="comment-form" action="#comment-form" method="post">
    <?=$commentMsg;?>
    <div class="commenter-data">
      <input type="hidden" name="important-input" id="important-input" />
      <label for="name">Name (required):</label>
      <input type="text" id="name" name="name" placeholder="Your Name..." value="<?php if($printComment){ echo $_POST["name"];} ?>" required />
      <label for="email">Email Address (required, never shared):</label>
      <input type="email" id="email" name="email" placeholder="Your Email Address..." value="<?php if($printComment){ echo $_POST["email"];} ?>" required />
      <label for="site-url">Site URL:</label>
      <input type="url" id="site-url" name="site-url" placeholder="Your Site's URL..." value="<?php if($printComment){ echo $_POST["site-url"];} ?>" />
    </div>
    <div class="comment-message">
      <label for="comment" class="comment-label">Your Comment (required):</label>
      <textarea name="comment" id="comment" cols="30" rows="10" placeholder="Your Comment..." required><?php if($printComment){ echo $_POST["comment"];} ?></textarea>
      <input type="submit" value="Add Comment" />
    </div>
  </form>
  <?php } else { ?>
  <h2>Comments are closed</h2>
  <p>Unfortunately comments on this post are now closed.</p>
  <?php } ?>
</section>
<?php } ?>
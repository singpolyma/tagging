<?php
require_once 'XNC/Comment.php';
require_once 'removeEvils.php';

function extComments($file) {

?>

<xn:head>
<style type="text/css">
/* Comment Styles */
   .comments {
      list-style-type: none;
      margin-left: 0px;
      margin-top: 0.5em;
   }
   .comments li {
      display: block;
      margin-left: 0px;
      margin-bottom: 1em;
   }
   .comments dl {
      display: inline;
   }
   .comments dd {
      margin-left: 0px;
   }
   .comments dt {
      display: none;
   }
   .comments dd.author {
      display: inline;
      font-size: 1em;
   }
   .comments dd.content {
      display: block;
      margin-top: 0.5em;
   }
</style>
</xn:head>

<?php

$newComment = new XNC_Comment($file);
// Handle any form submission of adding a new comment
if ($newComment->willProcessForm()) {
   $newComment->processForm();
   $cnt = XN_Content::load($newComment->id);
   $cnt->my->set('parentid',$file->id);
   $cnt->isPrivate = false;
   $cnt->save();
} elseif ($newComment->lastError() != XNC_Comment::ERROR_FORM_ABSENT)
   print $newComment->lastError();
// Display a list of comments belonging to a parent object
if ($file->my->content($newComment->referenceAttribute,true)) {
echo '<ul class="xoxo comments">';
   foreach ($file->my->content($newComment->referenceAttribute,true) as $comment) {
      $data = new XNC_Comment($comment);
      ?>
<li id="<?php echo 'c'.$data->id; ?>">
   Posted on <a href="<?php echo $_SERVER['SCRIPT_URI'].($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''); ?>#<?php echo 'c'.$data->id; ?>" title="<?php echo strtotime($data->createdDate); ?>"><?php echo date('Y-m-d H:i',strtotime($data->createdDate)); ?></a>
   by <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/?user=<?php echo $data->contributorName ?>" class="author user"><?php echo $data->contributorName ?></a>
<dl>
   <dt>body</dt>
      <dd class="content"><?php echo removeEvilTags(nl2br($data->description)); ?></dd>
</dl>
</li>
      <?php
   }//end foreach
echo '</ul>';
}//end if
// Display the add a comment form
if(XN_Profile::current()->isLoggedIn()) {
?>
<form id="commentForm" method="post" action="<?php echo $_SERVER['SCRIPT_URI'].($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''); ?>">
<input type="hidden" name="xnc_comment" value="xnc_comment" /><input type="hidden" name="Comment:_parent_id" value="<?php echo $file->id; ?>" />Comment: <br />
<textarea name="Comment:description" rows="5" cols="50"></textarea><br />
<input type="submit" name="submit" value="Save Comment" class="button"/><br />
</form>
<a href="http://cocomment.com/"><img src="http://cocomment.com/images/cocomment-integrated.gif" alt="coComment Integrated" /></a>
<script type="text/javascript">
  var blogTool              = "Ning App";
  var blogURL               = "http://<?php echo $_SERVER['HTTP_HOST']; ?>/";
  var blogTitle             = "<?php echo addslashes(XN_Application::load()->name); ?>";
  var postURL               = "<?php echo $_SERVER['SCRIPT_URI'].($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''); ?>";
  var postTitle             = "<?php echo addslashes($file->title); ?>";
  var commentTextFieldName  = "Comment:description";
  var commentButtonName     = "submit";
  var commentAuthorLoggedIn = true;
  var commentAuthor         = "<?php echo XN_Profile::current()->screenName; ?>";
  var commentFormID         = "commentForm";
  var cocomment_force       = false;

var cocoscript = document.createElement('script');
cocoscript.setAttribute('id', 'cocomment-fetchlet');
cocoscript.setAttribute('trackAllComments', 'true');
cocoscript.setAttribute('src', 'http://www.cocomment.com/js/enabler.js');
document.getElementsByTagName('head')[0].appendChild(cocoscript);

</script>
<?php } ?>
</div>

<?php

}

?>
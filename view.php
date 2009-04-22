<?php

if(!$_REQUEST['id']) die('NO ID SPECIFIED');

require_once 'tagFunctions.php';
require_once 'XNC/Ajax.php';

$group = XN_Content::load(intval($_REQUEST['id']));
$grouptags = explode(' ',$group->my->grouptags);
foreach($grouptags as $id => $tag)
   $grouptags[$id] = str_replace('+',' ',$tag);

if($_REQUEST['format']) {
   if(!class_exists('Outline'))
      XN_Application::includeFile('xoxotools','/OutlineClasses/Outline.php');
   $struct = new Outline();
   $struct->addNode($grouptags);
   $struct->getNode(0)->setField('title',$group->title);
   $struct->getNode(0)->setField('relatedtags',new Outline(fetchTags($group)));
   if($_REQUEST['format'] == 'xoxo') {
      header('Content-Type: application/xml;charset=utf-8');
      $struct->getNode(0)->setField('text',$group->title);
      if($_ITEMONLY)
         echo $struct->getNode(0)->toXOXO('item');
      else
         echo $struct->toXOXO();
   }//end if format == xoxo
   if($_REQUEST['format'] == 'json') {
      header('Content-Type: text/javascript;charset=utf-8');
      if(!isset($_REQUEST['raw']) && !$_ITEMONLY) {
         if($_REQUEST['callback'])
            echo $_REQUEST['callback'].'(';
         else
            echo 'if(typeof(Tagging) != "object") var Tagging = {};'."\n".'Tagging.groups = [';
      }//end if ! isset raw
      echo $struct->getNode(0)->toJSON('grouptags',true);
      if(!isset($_REQUEST['raw']) && !$_ITEMONLY) {
         if($_REQUEST['callback'])
            echo ')';
         else
            echo '];'."\n".'if(Tagging.callbacks && Tagging.callbacks.groups) Tagging.callbacks.groups(Tagging.groups)';
      }//end if ! isset raw
   }//end if format == json
} else {

echo XNC_Ajax::scriptInclude();
$group->focus();

echo '<xn:head><title>tagging / tags / '.strtolower($grouptags[0]).'</title></xn:head>';
echo '<h2 style="display:inline;" id="group-title">'.$group->h('title').'</h2>';
if(XN_Profile::current()->screenName == $group->contributorName) {
   echo ' <a href="javascript:toggleitem(&quot;edit-title&quot;);"><img src="http://group.ning.com/images/icn/edit.gif" alt="Edit &raquo;" /></a>';
   echo '<div id="edit-title" style="display:none;">';
   $edititleform = XNC_Ajax::Form()->method('post')
                   ->actionUrl('/groupEdit.php?form')
                   ->htmlElement('group-title')
                   ->callback('Loading', '$(&quot;group-title&quot;).innerHTML = &quot;<i>Saving...</i>&quot;;toggleitem(&quot;edit-title&quot;);');
   $edititleform->open();
   echo '<input type="hidden" name="edit-title" value="yes" />';
   echo '<input type="hidden" name="id" value="'.$_REQUEST['id'].'" />';
   echo '<input type="hidden" name="return_to" value="http://'.$_SERVER['HTTP_HOST'].'/view.php?id='.$_REQUEST['id'].'" />';
   echo '<p>Edit Title: <input type="text" name="title" value="'.$group->h('title').'" /> <input type="submit" value="Save" /></p>';
   $edititleform->close();
   echo '</div>';
}//end if current == contributorName
echo '<br />';

echo '<p id="group-desc" style="display:inline;">'.nl2br($group->h('description')).'</p>';
if(XN_Profile::current()->screenName == $group->contributorName) {
   echo ' <a href="javascript:toggleitem(&quot;edit-desc&quot;);"><img src="http://group.ning.com/images/icn/edit.gif" alt="Edit &raquo;" /></a>';
   echo '<div id="edit-desc" style="display:none;">';
   $edititleform = XNC_Ajax::Form()->method('post')
                   ->actionUrl('/groupEdit.php?form')
                   ->htmlElement('group-desc')
                   ->callback('Loading', '$(&quot;group-desc&quot;).innerHTML = &quot;<i>Saving...</i>&quot;;toggleitem(&quot;edit-desc&quot;);');
   $edititleform->open();
   echo '<input type="hidden" name="edit-desc" value="yes" />';
   echo '<input type="hidden" name="id" value="'.$_REQUEST['id'].'" />';
   echo '<input type="hidden" name="return_to" value="http://'.$_SERVER['HTTP_HOST'].'/view.php?id='.$_REQUEST['id'].'" />';
   echo '<p>Edit Description:<br /> <textarea name="desc" style="width:300px;height:100px;">'.$group->h('description').'</textarea><br /> <input type="submit" value="Save" /></p>';
   $edititleform->close();
   echo '</div>';
}//end if current == contributorName
echo '<br />';

echo '<p id="group-tags">';
echo '<b>Group Tags:</b> ';
foreach($grouptags as $id => $grouptag) {
   if($id > 0)
      echo ', ';
   echo $grouptag.' ';
   XNC_Ajax::Link('<img src="http://www.ning.com/xnstatic/icn/cross.gif" alt="[X]" />')
      ->actionUrl('/groupEdit.php?form&delete&return_to='.urlencode('http://'.$_SERVER['HTTP_HOST'].'/view.php?id='.$_REQUEST['id']).'&grouptag='.urlencode($grouptag).'&id='.$_REQUEST['id'])
      ->htmlElement('group-tags')
      ->callback('Loading', '$(&quot;group-tags&quot;).innerHTML = &quot;<i>Deleting...</i>&quot;;')
      ->confirm('Delete the '.$grouptag.' tag?')
      ->make();
}//end foreach grouptags
echo '</p>';

$grouptagform = XNC_Ajax::Form()->method('post')
                   ->actionUrl('/groupEdit.php?form')
                   ->htmlElement('group-tags')
                   ->callback('Loading', '$(&quot;group-tags&quot;).innerHTML = &quot;<i>Adding...</i>&quot;;');
$grouptagform->open();
echo '<input type="hidden" name="add" value="yes" />';
echo '<input type="hidden" name="id" value="'.$_REQUEST['id'].'" />';
echo '<input type="hidden" name="return_to" value="http://'.$_SERVER['HTTP_HOST'].'/view.php?id='.$_REQUEST['id'].'" />';
echo '<p>Add a group tag: <input type="text" name="grouptag" /> <input type="submit" value="Add" /></p>';
$grouptagform->close();

echo '<p>';
echo '<b>Related Tags:</b> ';
foreach(fetchTags($group) as $id => $tag) {
   if($id > 0)
      echo ', ';
   echo '<a class="tag" href="/index.php/tag/'.$tag.'" rel="tag">'.$tag.'</a>';
}//end foreach tags
echo '</p>';
echo '<p style="font-style:italic;">Add/remove related tags using the sidebar.</p>';

echo '<h3 id="comments">Discuss TagGroup</h3>';
include_once 'extComments.php';

$_SERVER['SCRIPT_URI'] = 'http://'.$_SERVER['HTTP_HOST'].'/view.php';
$_SERVER['QUERY_STRING'] = 'id='.$group->id;

extComments($group);

}//end if-else format

?>
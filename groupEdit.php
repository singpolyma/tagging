<?php

require_once 'XNC/Ajax.php';
XNC_Ajax::startAjaxPage();

if(!$_REQUEST['id']) die;

$group = XN_Content::load(intval($_REQUEST['id']));
$grouptags = explode(' ',$group->my->grouptags);

$_REQUEST['grouptag'] = str_replace(' ','+',$_REQUEST['grouptag']);

if(isset($_REQUEST['edit-title'])) {
   $group->set('title',$_REQUEST['title']);
   $group->save();
   header('Content-Type: text/plain');
   if(isset($_REQUEST['form']))
      exit($_REQUEST['title']);
   header('Location: '.$_REQUEST['return_to'],TRUE,303);
   exit;
}//end if edit-title

if(isset($_REQUEST['edit-desc'])) {
   $group->set('description',$_REQUEST['desc']);
   $group->save();
   header('Content-Type: text/plain');
   if(isset($_REQUEST['form']))
      exit(nl2br($_REQUEST['desc']));
   header('Location: '.$_REQUEST['return_to'],TRUE,303);
   exit;
}//end if edit-title

if(isset($_REQUEST['delete'])) {
   foreach($grouptags as $id => $tag) {
      if(!$tag || $tag == strtolower($_REQUEST['grouptag'])) unset($grouptags[$id]);
   }//end foreach grouptags
}//end if delete

if(isset($_REQUEST['add'])) {
   $grouptags[] = strtolower($_REQUEST['grouptag']);
}//end if add

$grouptags = implode(' ',array_unique($grouptags));
$group->my->set('grouptags',$grouptags);
$group->save();

header('Content-Type: text/plain');
if(isset($_REQUEST['form'])) {

echo '<b>Group Tags:</b> ';
foreach(explode(' ',$grouptags) as $id => $grouptag) {
   $grouptag = str_replace('+',' ',$grouptag);
   if($id > 0)
      echo ', ';
   echo $grouptag.' ';
   XNC_Ajax::Link('<img src="http://www.ning.com/xnstatic/icn/cross.gif" alt="[X]" />')
      ->actionUrl('/groupEdit.php?form&delete&grouptag='.urlencode($grouptag).'&id='.$_REQUEST['id'])
      ->htmlElement('group-tags')
      ->callback('Loading', '$(&quot;group-tags&quot;).innerHTML = &quot;<i>Deleting...</i>&quot;;')
      ->confirm('Delete the '.$grouptag.' tag?')
      ->make();
}//end foreach grouptags
exit;

}//end if isset form
header('Location: '.$_REQUEST['return_to'],TRUE,303);

?>
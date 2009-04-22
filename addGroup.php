<?php

if(!XN_Profile::current()->isLoggedIn())
   die('<b>Please log in</b>');

   if($_REQUEST['grouptags']) {
      if(!isset($_REQUEST['confirm'])) {
         $query = XN_Query::create('Content')
            ->filter('owner','=')
            ->filter('type','eic','TagGroup')
            ->filter('my.grouptags','likeic',strtolower($_REQUEST['grouptags']));
         $items = $query->execute();
         if(count($items)) {
           echo '<h2>Confirm Creation</h2> <p>The following items contain tags that appear to intersect with those of the group you are trying to create, please ensure that none of these groups is in any way synonymous with that which you are trying to create (See <a href="about.php#group-creation">About Group Creation</a> for more details).</p><br />';
            echo '<ul>';
            foreach($items as $item)
               echo '<li><a href="view.php?id='.$item->id.'">'.$item->h('title').'</a><br />'.substr($item->h('description'),0,90).'...<br /><b>Group Tags:</b> '.substr($item->my->h('grouptags'),0,60).'...</li>';
            echo '</ul>';
            echo '<form method="get" action="'.$_SERVER['PHP_SELF'].'"> <input type="hidden" name="title" value="'.$_REQUEST['title'].'" /> <input type="hidden" name="description" value="'.$_REQUEST['description'].'" /> <input type="hidden" name="grouptags" value="'.strtolower($_REQUEST['grouptags']).'" /> <input type="submit" name="confirm" value="Confirm Creation" /></form>';
            exit;
         }//end if count items
      }//end if ! isset confirm
      $group = XN_Content::create('TagGroup',$_REQUEST['title'],$_REQUEST['description']);
      $group->my->set('grouptags',strtolower($_REQUEST['grouptags']));
      $group->save();
      $group->focus();
      echo '<h2>Group '.$_REQUEST['title'].' Added</h2>';
      exit;
   }//end if grouptags
?>
<h2>New Tag Group</h2>
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>"><dl>
<dt>Title</dt> <dd><input type="text" name="title" style="width:250px;" /></dd>
<dt>Description</dt> <dd><textarea style="width:250px;height:100px;" name="description">Describe and define this group of tags.</textarea></dd>
<dt>Group Tags</dt> <dd><input type="text" name="grouptags" style="width:250px;" /></dd>
<dd><input type="submit" value="Add Group" /></dd>
</dl></form>
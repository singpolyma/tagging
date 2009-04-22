<?php

$pathvars = array_reverse(explode('/',$_SERVER['SCRIPT_URI']));
if(!$pathvars[0]) {$pathvars[0] = $pathvars[1]; $pathvars[1] = $pathvars[2];}

$query = XN_Query::create('Content')
         ->filter('owner','=')
         ->filter('type','eic','TagGroup')
         ->alwaysReturnTotalCount(true);

if($pathvars[1] == 'tag' || $pathvars[0] == 'tag') {
   if(!$pathvars[0] || $pathvars[0] == 'tag') {
      require_once 'tagFunctions.php';
      require_once 'XNC/HTML.php';
      echo '<xn:head><title>tagging / tag</title></xn:head>';
      echo '<h2>Tags</h2>';
      echo XNC_HTML::buildMap(getTagCount(), 'index.php/tag/%s','',true,60,300);
      exit;
   }//end if ! pathvars[0]
   if(!isset($_REQUEST['auto']) && !$_REQUEST['format']) {
      echo '<xn:head><title>tagging / tag / '.htmlspecialchars($pathvars[0]).'</title></xn:head>';
      echo '<h2>Tag Groups With Related Tag of '.htmlspecialchars($pathvars[0]).'</h2>';
   }//end if ! isset auto
   $_REQUEST['tag'] = $pathvars[0];
}//end if pathvars[1] == tag

if($pathvars[1] == 'tags') {
   if(!isset($_REQUEST['auto']) && !$_REQUEST['format']) {
      echo '<xn:head><title>tagging / tags / '.htmlspecialchars($pathvars[0]).'</title></xn:head>';
      echo '<h2>Tag Groups With GroupTag of '.htmlspecialchars($pathvars[0]).'</h2>';
   }//end if ! isset auto
   $_REQUEST['tags'] = $pathvars[0];
}//end if pathvars[1] == tags

if($pathvars[1] != 'tag' && $pathvars[1] != 'tags') {
   if(!isset($_REQUEST['auto']) && !$_REQUEST['format']) {
      echo '<xn:head><title>tagging - recent TagGroups</title></xn:head>';
      echo '<h2>Recent TagGroups</h2>';
   }//end if ! isset auto
}//end if !tag && !tags

if($_REQUEST['tag'])
   $query->filter('tag.value','eic',$_REQUEST['tag']);

if($_REQUEST['tags'])
   $query->filter('my.grouptags','likeic',$_REQUEST['tags']);

if($_REQUEST['desc']) {
   $query->filter('description','likeic',$_REQUEST['desc']);
   if(!isset($_REQUEST['auto']) && !$_REQUEST['format'])
      echo '<p style="font-style:italic;font-size:10pt;">Filtered by "'.htmlspecialchars($_REQUEST['desc']).'"</p>';
}//end if desc
$items = $query->execute();

if($items && count($items)) {
   if(isset($_REQUEST['auto'])) {
      $_REQUEST['id'] = $items[0]->id;
      require('view.php');
      exit;
   }//end if ! isset auto
   if($_REQUEST['format']) {
      $_ITEMONLY = true;
      if($_REQUEST['format'] == 'xoxo')
         echo '<ul class="xoxo">';
      if($_REQUEST['format'] == 'json') {
         if(!isset($_REQUEST['raw'])) {
            if($_REQUEST['callback'])
               echo $_REQUEST['callback'].'([';
            else
               echo 'if(typeof(Tagging) != "object") var Tagging = {};'."\n".'Tagging.groups = [';
         } else {echo '[';}//end if-else ! isset raw
      }//end if format == json
      foreach($items as $idx => $item) {
         $_REQUEST['id'] = $item->id;
         if($_REQUEST['format'] == 'json' && $idx > 0)
            echo ',';
         require('view.php');
      }//end foreach items
      if($_REQUEST['format'] == 'json') {
         if(!isset($_REQUEST['raw'])) {
            if($_REQUEST['callback'])
               echo '])';
            else
               echo '];'."\n".'if(Tagging.callbacks && Tagging.callbacks.groups) Tagging.callbacks.groups(Tagging.groups)';
         } else {echo ']';}//end if-else ! isset raw
      }//end if format == json
      if($_REQUEST['format'] == 'xoxo')
         echo '</ul>';
      exit;
   }//end if ! auot && format
   echo '<ul>';
   foreach($items as $item)
      echo '<li><a href="/view.php?id='.$item->id.'">'.$item->h('title').'</a><br />'.substr($item->h('description'),0,90).'...<br /><b>Group Tags:</b> '.substr($item->my->h('grouptags'),0,60).'...</li>';
   echo '</ul>';
} else {
   if(!$_REQUEST['format'])
      echo '<p>No such group(s) exist.  We reccomend you <a href="/addGroup.php">create one</a>.</p>';
   if(!class_exists('Outline'))
      XN_Application::includeFile('xoxotools','/OutlineClasses/Outline.php');
   $struct = new Outline();
   if($_REQUEST['format'] == 'xoxo') {
      header('Content-Type: application/xml;charset=utf-8');
      echo $struct->toXOXO();
   }//end if format == xoxo
   if($_REQUEST['format'] == 'json') {
      header('Content-Type: text/javascript;charset=utf-8');
      if(!isset($_REQUEST['raw'])) {
         if($_REQUEST['callback'])
            echo $_REQUEST['callback'].'(';
         else
            echo 'if(typeof(Tagging) != "object") var Tagging = {};'."\n".'Tagging.groups = [';
      }//end if ! isset raw
      echo $struct->toJSON('grouptags',true);
      if(!isset($_REQUEST['raw'])) {
         if($_REQUEST['callback'])
            echo ')';
         else
            echo '];'."\n".'if(Tagging.callbacks && Tagging.callbacks.groups) Tagging.callbacks.groups(Tagging.groups)';
      }//end if ! isset raw
   }//end if format == json
}//end if-else items && count items

?>
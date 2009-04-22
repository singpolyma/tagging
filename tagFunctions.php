<?php

    function getTagCount($limit=null,$sort=false) {
        $tags = XN_Query::create('tag_valuecount')
                                ->filter('content->owner');
        $tags = $tags->execute();
        if(is_array($tags)) {
            // sort, if true
            if($sort) {
                arsort($tags, SORT_NUMERIC);
            }
            // apply limit, if any
            if((int)$limit) {
                $tags = array_slice($tags,0,$limit);
            }
        }
        return $tags;
    }

    function fetchTags($content) {
        try{
              $results =  XN_Query::create( 'Tag' )
                         ->filter( 'content', '=', $content )
                         ->order( 'count(value)', 'desc' )
                         ->order( 'value', 'asc' );
              $results = $results->execute(); 
              // Put all the tags into an array
              $tagArray = array();
              foreach($results as $tag) {
                  $tagArray[] = $tag->value;
              }
          }
          catch (XN_Exception $e) {
              echo 'Whoops! Something\'s Gone Wrong';
              echo 'There was an error in this page and I can\'t fetch the list of tags. The error is: '.$e->getMessage();
          }
          
        return $tagArray;
    }

    function fetchTagsWithCount($content) {
        try{
              $results =  XN_Query::create( 'Tag' )
                         ->filter( 'content', '=', $content )
                         ->order( 'count(value)', 'desc' )
                         ->order( 'value', 'asc' );        
              $results = $results->execute(); 
              // Put all the tags into an array
              $tagcounts = getTagCount();
              $tagArray = array();
              foreach($results as $tag) {
                  $tagArray[$tag->value] = $tagcounts[$tag->value];
              }
          }
          catch (XN_Exception $e) {
              echo 'Whoops! Something\'s Gone Wrong';
              echo 'There was an error in this page and I can\'t fetch the list of tags. The error is: '.$e->getMessage();
          }
          
        return $tagArray;
    }

?>
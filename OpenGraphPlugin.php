<?php

class OpenGraphPlugin extends Omeka_Plugin_AbstractPlugin
{
  protected $_hooks =array(
	'install',
    'uninstall',
    'public_head'
  );

  public function hookInstall()
  {

  }

  public function hookUninstall()
  {

  }

  public function hookPublicHead($args)
  {
   
    $title = '';
    $description = '';
    $image_url = '';

    // Is the curent record an exhibit?  Use its metadata.
    try {
      $exhibit = get_current_record('exhibit');
      $title = metadata($exhibit, 'title', array('no_escape' => false));
      $description = metadata($exhibit, 'description',array('no_escape' => false));

      $file = $exhibit->getFile();
      if($file){
        $image_url = file_display_url($file, 'thumbnail');
      }
    }
    catch (Omeka_View_Exception $ove){
      //  no exhibit, don't do anything
    }

    // Is the curent record an item?  Use its metadata.
    try {
      $item = get_current_record('item');
      $title = metadata('item', array('Dublin Core', 'Title'));
	  $description = metadata('item', array('Dublin Core', 'Description'), array('delimiter' => ' '));
      if (strlen($title) > 0){
        foreach (loop('files', $item->Files) as $file){
          if($file->hasThumbnail()){
            $image_url = file_display_url($file, 'thumbnail');
            break;
          }
        }
      }
    }
    catch (Omeka_View_Exception $ove){
      //  no item, don't do anything
    }

    // Is the curent record a file?  Use its metadata.
    try {
      $fileRecord = get_current_record('file');
      $title = metadata('file', array('Dublin Core', 'Title'));
      $description = metadata('file', array('Dublin Core', 'Description'), array('delimiter' => ' '));
      if (strlen($title) > 0){
          if($fileRecord->hasThumbnail()){
            $image_url = file_display_url($fileRecord, 'thumbnail');
          }
      }
    }
    catch (Omeka_View_Exception $ove){
      //  no file, don't do anything
    }

    // Is the curent record a collection?  Use its metadata.
    try {
      $collection = get_current_record('collection');
      $title = metadata('collection', array('Dublin Core', 'Title'));
      $description = metadata('collection', array('Dublin Core', 'Description'));

      $file = $collection->getFile();
      if($file){
        $image_url = file_display_url($file, 'thumbnail');
      }
    }
    catch (Omeka_View_Exception $ove){
      //  no collection, don't do anything
    }

    // Default to the site settings if we didn't find anything else to use
    if (strlen($title) < 1){
      echo "<meta object=\"default\" />"."\n";
      $title = option('site_title');
      $description = option('description');
      $items = get_random_featured_items(1, true);
      if (isset($items[0])){
        foreach (loop('files', $items[0]->Files) as $file){
          if($file->hasThumbnail()){
            $image_url = file_display_url($file, 'thumbnail');
            break;
          }
        }      
      }
    }

    if (strlen($title) > 0){
      
      //opengraph
      echo '<meta property="og:url" content="'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'" />'."\n";
      echo '<meta property="og:type" content="article" />'."\n";       
      echo '<meta property="og:title" content="'.strip_tags(html_entity_decode($title)).'" />'."\n";
      echo '<meta property="og:description" content="'.strip_tags(html_entity_decode($description)).'" />'."\n";
      
      if (strlen($image_url) > 0){
        echo '<meta property="og:image" content="'.$image_url.'" />'."\n"; 
      }
    }
  }
}

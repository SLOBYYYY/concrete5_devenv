<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$rssUrl = $showRss ? $controller->getRssUrl($b) : '';
$th = Loader::helper('text');
//$ih = Loader::helper('image'); //<--uncomment this line if displaying image attributes (see below)
//Note that $nh (navigation helper) is already loaded for us by the controller (for legacy reasons)
//$parent = page::getByID($pages[0]->getCollectionParentID());
//$parentname = $parent->getCollectionName(); 
$db = Loader::db();
//Load up the users most recent order
$u = new User();
$uID = $u->getUserID();
//$orderInfo = $db->getRow("SELECT * FROM C5CBT_hsd_orders WHERE uID = $uID ORDER by timestamp DESC");
	foreach ($pages as $page):
	// Prepare data for each page being listed...
		$title = $th->entities($page->getCollectionName());
		$children = $page->getCollectionChildrenArray();
?>
<div class="sidebar-component member-sidebar-component <?php print (str_replace(' ', '-', strtolower($title))); ?>-sidebar">
    <h3><?=$title ?></h3>
	<ul class="page-list">
	<?php  
		foreach ($children as $childId) {
			$child = Page::getByID($childId);
			$title = $child->getCollectionName();
            if($title == "Final Exam"){
                $p = new Permissions($child);
                if(!$p->canRead()){ // Final already completed.
                    echo '<li class="page-navigation-item completed">You have completed this certification.</li>';
                    continue;
                }
            }
			$url = $nh->getLinkToCollection($child);
			$target = ($child->getCollectionPointerExternalLink() != '' && $child->openCollectionPointerExternalLinkInNewWindow()) ? '_blank' : $child->getAttribute('nav_target');
			$target = empty($target) ? '_self' : $target;
    ?>
        <li class="page-navigation-item" ><a href="<?=$url ?>" target="<?=$target ?>"><?=$title ?></a></li>
  
    <?php	
	}
	?>
    </ul>
</div><!-- end .ccm-page-list -->
<?php  endforeach; ?>
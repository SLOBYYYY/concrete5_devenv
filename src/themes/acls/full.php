<?php defined('C5_EXECUTE') or die("Access Denied.");
//the full.php template is for single column layouts
$this->inc('elements/header.php');
?>
<div class="acls-header">
<?php
$this->inc('navigation.php');
?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                    <?php
                    $a = new Area('Main');
                    $a->enableGridContainer();
                    $a->display($c);
                    ?>
            </div>
        </div>
    </div>
</div>
<?php $this->inc('elements/footer.php');?>

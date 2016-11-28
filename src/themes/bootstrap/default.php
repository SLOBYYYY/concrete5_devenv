<?php defined('C5_EXECUTE') or die("Access Denied.");
//this default.php template file us used to render pages when the theme does not include a matching template file already.
$this->inc('elements/header.php');
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
<?php $this->inc('elements/footer.php');?>
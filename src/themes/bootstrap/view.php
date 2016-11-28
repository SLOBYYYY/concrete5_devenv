<?php defined('C5_EXECUTE') or die("Access Denied.");
//this view.php template file is used to render "singlepages" that do not have corresponding template with the singlepage name.
$this->inc('elements/header.php');
?>
    <!-- Page Content -->
    <div class="container">
        <div class="row">
            <section class="hero col-xs-12">
                <?php
                $a = new Area('Hero');
                $a->display($c);
                ?>
            </section>
        </div>
        <div class="row">
            <main class="col-xs-12 col-sm-8">
                <?php
                print $innerContent;
                $a = new Area('Main');
                $a->display($c);
                ?>

            </main>
            <aside class="col-xs-12 col-sm-4">
                <?php
                $a = new Area('Sidebar');
                $a->display($c);
                ?>
            </aside>
        </div>
    </div>
    <!-- /.container -->
	<?php $this->inc('elements/footer.php');?>
    
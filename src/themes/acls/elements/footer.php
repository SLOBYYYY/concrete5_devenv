<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
    
    <footer class="container">
    <div class="row">
    <div class="col-xs-12">
                <?php
                $a = new GlobalArea('Footer');
                $a->display();
                ?>
    </div>
    </div>
	</footer>

    <!-- Bootstrap Core JavaScript -->
    <script src="<?php echo $view->getThemePath()?>/js/bootstrap.min.js"></script>

	<?php Loader::element('footer_required'); ?>
	
    </div><!-- Close Page Wrapper Div -->
</body>
</html>

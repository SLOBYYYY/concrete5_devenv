	<nav class="container navbar navbar-default">
		  <div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
			  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			  </button>
			  <a class="navbar-brand navbar-title" href="<?= Config::get('concrete.seo.canonical_url') ?>"><?= Config::get('app.contact_details.business_name') ?></a>
			  <p class="subtitle"><?= Config::get('app.contact_details.business_subtitle')?></p>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			  <ul class="nav navbar-nav">
				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Courses <span class="caret"></span></a>
				  <ul class="dropdown-menu">
					<li><a href="/acls-certification">ACLS Certification</a></li>
					<li><a href="/acls-recertification">ACLS Recertification</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="/bls-certification">BLS Certification</a></li>
					<li><a href="/bls-recertification">BLS Recertification</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="/pals-certification">PALS Certification</a></li>
					<li><a href="/pals-recertification">PALS Recertification</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="/nrp-certification">NRP Certification</a></li>
					<li><a href="/nrp-recertification">NRP Recertification</a></li>
				  </ul>
				</li>
				<li><a href="/group-orders">Group Orders <span class="sr-only">(current)</span></a></li>
				<li><a href="/faq">FAQ's</a></li>
				<li><a href="/contact">Contact</a></li>
			  </ul>
			  <ul class="nav navbar-nav navbar-right">
			  <li><a href="/member">My Account</a></li>
			  </ul>
			  <form class="navbar-form navbar-right" action="/search-results" method="get">
				<div class="form-group">
				  <input type="text" class="form-control" name="query" placeholder="Search">
				</div>
				<button type="submit" class="btn btn-default">Search</button>
			  </form>
			</div><!-- /.navbar-collapse -->
		  </div><!-- /.container-fluid -->
		</nav>

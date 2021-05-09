<header class="main-header">
	<!-- Logo -->
	<a href="<?php echo WEB_ROOT; ?>" class="logo">
		<!-- mini logo for sidebar mini 50x50 pixels -->
		<span class="logo-mini"><b>E</b>LS</span>
		<!-- logo for regular state and mobile devices -->
		<span class="logo-lg"><b>EGE</b>LS</span>
	</a>
	<!-- Header Navbar: style can be found in header.less -->
	<nav class="navbar navbar-static-top">
		<!-- Sidebar toggle button-->
		<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
			<span class="sr-only">Toggle navigation</span>
		</a>
		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">
				<!-- User Account: style can be found in dropdown.less -->
				<li class="dropdown user user-menu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<img src="<?php echo WEB_ROOT; ?>dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
						<span class="hidden-xs"><?php echo $lu_uname; ?></span>
					</a>
					<ul class="dropdown-menu">
						<!-- User image -->
						<li class="user-header">
							<img src="<?php echo WEB_ROOT; ?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
							<p>
								<?php echo $lu_uname; ?> - <?php echo $lu_email; ?>
								<small>Member since <?php echo $date->format('M. Y'); ?></small>
							</p>
						</li>
						<!-- Menu Body -->
						<li class="user-body">
							<div class="row">
								<div class="col-xs-4 text-center">
									<a href="#"><i class="fa fa-facebook"></i></a>
								</div>
								<div class="col-xs-4 text-center">
									<a href="#"><i class="fa fa-twitter"></i></a>
								</div>
								<div class="col-xs-4 text-center">
									<a href="#"><i class="fa fa-github"></i></a>
								</div>
							</div>
							<!-- /.row -->
						</li>
						<!-- Menu Footer-->
						<li class="user-footer">
							<div class="pull-left">
								<a href="#" class="btn btn-default btn-flat"><i class="fa fa-vcard-o"></i> Profile</a>
							</div>
							<div class="pull-right">
								<a onclick="return confirm('Do you want to Sign Out?')" href="<?php echo WEB_ROOT; ?>logout.php?logout=true" class="btn btn-default btn-flat btn-sm"><i class="fa fa-sign-out"></i> Sign out</a>
							</div>
						</li>
					</ul>
				</li>
				<!-- Control Sidebar Toggle Button -->
				<li>
					<a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
				</li>
			</ul>
		</div>
	</nav>
</header>
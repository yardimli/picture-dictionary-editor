<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="<?php echo WEB_ROOT; ?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p><?php echo $lu_uname; ?></p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
    <!-- search form -->
    <form action="#" method="get" class="sidebar-form">
      <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Search...">
        <span class="input-group-btn">
          <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
          </button>
        </span>
      </div>
    </form>
    <!-- /.search form -->
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
      <li class="header">MAIN NAVIGATION</li>
      <li class="treeview <?php if(isset($_GET['dashboard'])) { echo 'active'; } ?>">
        <a href="<?php echo WEB_ROOT; ?>Application/?dashboard">
          <i class="fa fa-dashboard"></i>&nbsp;&nbsp;<span>Dashboard</span>
        </a>
      </li>

      <li class="treeview <?php if(isset($_GET['users'])) { echo 'active'; } ?>">
        <a href="<?php echo WEB_ROOT; ?>users/?users">
          <i class="fa fa-user"></i>&nbsp;&nbsp;<span>Users</span>
        </a>
      </li>

	    <li class="treeview <?php if(isset($_GET['dictionary'])) { echo 'active'; } ?>">
		    <a href="<?php echo WEB_ROOT; ?>dictionary/?dictionary">
			    <i class="fa fa-list"></i>&nbsp;&nbsp;<span>Dictionary</span>
		    </a>
	    </li>

	    <li class="treeview <?php if(isset($_GET['categories'])) { echo 'active'; } ?>">
		    <a href="<?php echo WEB_ROOT; ?>categories/?categories">
			    <i class="fa fa-list"></i>&nbsp;&nbsp;<span>Categories</span>
		    </a>
	    </li>

	    <li class="treeview <?php if(isset($_GET['stories'])) { echo 'active'; } ?>">
		    <a href="<?php echo WEB_ROOT; ?>stories/?stories">
			    <i class="fa fa-list"></i>&nbsp;&nbsp;<span>Stories</span>
		    </a>
	    </li>

    </ul>
  </section>
  <!-- /.sidebar -->
</aside>
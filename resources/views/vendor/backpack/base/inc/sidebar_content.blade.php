<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
@hasanyrole('administrator|resident|official|employee')
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="fa fa-dashboard nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
@endhasanyrole

<li class='nav-item'><a class='nav-link' href='{{ backpack_url('edit-account-info') }}'><i class='nav-icon fa fa-user'></i> My Account</a></li>

@hasanyrole('administrator|employee|official|resident')
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('announcement') }}'><i class='nav-icon fa fa-bullhorn'></i> Announcements</a></li>
@endhasanyrole

@hasanyrole('administrator|resident|official|employee')
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('alert') }}'><i class='nav-icon fa fa-bell'></i> Alerts</a></li>
@endhasanyrole

@hasanyrole('administrator|official|employee')
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('evacuation') }}'><i class='nav-icon fa fa-home'></i> Evacuations</a></li>
@endhasanyrole

@hasanyrole('administrator|official|employee')
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('barangay') }}'><i class='nav-icon fa fa-building'></i> Barangays</a></li>
@endhasanyrole

@hasanyrole('administrator|official|employee')
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fa fa-bar-chart"></i> Reports</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('report/weekly') }}"><i class="nav-icon fa fa-user"></i> <span>Weekly Report</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('report/monthly') }}"><i class="nav-icon fa fa-group"></i> <span>Monthly Report</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('report/generate') }}"><i class="nav-icon fa fa-group"></i> <span>Generate Report</span></a></li>
    </ul>
</li>
@endhasanyrole

@hasanyrole('administrator')
<li class="nav-item nav-dropdown">
  <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fa fa-group"></i> Users</a>
  <ul class="nav-dropdown-items">
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('resident') }}'><i class='nav-icon fa fa-user'></i> Residents</a></li>
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('official') }}'><i class='nav-icon fa fa-user'></i> Officials</a></li>
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('relative') }}'><i class='nav-icon fa fa-user'></i> Relatives</a></li>
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('employee') }}'><i class='nav-icon fa fa-user'></i> Employees</a></li>
  </ul>
</li>
@endhasanyrole

@hasanyrole('administrator')
<!-- Users, Roles, Permissions -->
<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fa fa-group"></i> Authentication</a>
	<ul class="nav-dropdown-items">
	  <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon fa fa-user"></i> <span>Users</span></a></li>
	  <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon fa fa-group"></i> <span>Roles</span></a></li>
	  <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon fa fa-key"></i> <span>Permissions</span></a></li>
	</ul>
</li>
@endhasanyrole

@hasanyrole('administrator')
<!-- <li class=nav-item><a class=nav-link href="{{ backpack_url('elfinder') }}"><i class="nav-icon fa fa-files-o"></i> <span>{{ trans('backpack::crud.file_manager') }}</span></a></li> -->
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('log') }}'><i class='nav-icon fa fa-terminal'></i> Logs</a></li>
@endhasanyrole

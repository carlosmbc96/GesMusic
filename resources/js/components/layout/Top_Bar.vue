<template>
  <!-- BEGIN HEADER -->
  <div class="page-header navbar navbar-fixed">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner">
      <!-- BEGIN LOGO -->
      <div
        class="page-logo"
        style="background-color: rgba(255, 255, 255, 0); padding-left: 0px"
      >
        <a href="index.html">
          <img
            src="../../../../public/assets/images/Logo ver Horizontal_Ltr Blancas.png"
            class="logo-default"
            style="width: 150px; margin-top: 0px"
          />
        </a>
        <div class="menu-toggler sidebar-toggler">
          <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
        </div>
      </div>
      <!-- END LOGO -->
      <a
        href="javascript:;"
        class="menu-toggler responsive-toggler"
        data-toggle="collapse"
        data-target=".navbar-collapse"
      >
        <img src="../../../../public/assets/images/sidebar-toggler.png" />
        <!-- <span></span> -->
      </a>
      <!-- BEGIN PAGE TOP -->
      <div class="page-top" style="background-color: rgba(255, 255, 255, 0.4)">
        <!-- BEGIN TOP NAVIGATION MENU -->
        <div class="top-menu">
          <ul class="nav navbar-nav pull-right">
            <!-- BEGIN USER LOGIN DROPDOWN -->
            <li class="dropdown dropdown-user">
              <a
                href="javascript:"
                class="dropdown-toggle"
                data-toggle="dropdown"
                data-hover="dropdown"
                data-close-others="true"
              >
                <img
                  alt=""
                  class="img-circle"
                  src="../../../../public/assets/layouts/layout2/img/avatar3_small.jpg"
                />
                <span class="username username-hide-on-mobile font-white">
                  Antonio
                </span>
                <i class="fa fa-angle-down font-white" />
              </a>
              <ul class="dropdown-menu dropdown-menu-default">
                <li>
                  <a href="page_user_profile_1.html">
                    <i class="icon-user" /> Mi Perfil
                  </a>
                </li>
                <li>
                  <a href="app_calendar.html">
                    <i class="icon-calendar" /> Mi Calendario</a
                  >
                </li>
                <li>
                  <a href="app_todo_2.html">
                    <i class="icon-rocket" /> Mis Tareas
                    <span class="badge badge-success"> 7 </span>
                  </a>
                </li>
                <li class="divider"></li>
                <li>
                  <a href="page_user_lock_1.html">
                    <i class="icon-lock" /> Bloquear Pantalla
                  </a>
                </li>
                <li>
                  <a href="page_user_login_1.html">
                    <i class="icon-key" /> Cerrar Sesión
                  </a>
                </li>
              </ul>
            </li>
            <!-- END USER LOGIN DROPDOWN -->
            <!-- BEGIN QUICK SIDEBAR TOGGLER -->
          </ul>
        </div>
        <!-- END TOP NAVIGATION MENU -->
      </div>
      <!-- END PAGE TOP -->
    </div>
    <!-- END HEADER INNER -->
  </div>
  <!-- END HEADER -->
</template>

<script>
export default {
  name: "Top_Bar",
};
</script>

<style>
.page-header.navbar .top-menu .navbar-nav > li.dropdown.open .dropdown-toggle {
  background-color: rgba(255, 255, 255, 0.4) !important;
}
.dropdown-user a:hover {
  background-color: rgba(255, 255, 255, 0.4) !important;
}
.page-header.navbar .menu-toggler {
  opacity: 1 !important;
}
</style>

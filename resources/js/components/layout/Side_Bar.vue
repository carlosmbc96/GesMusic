<template>
  <!-- BEGIN SIDEBAR -->
  <div class="page-sidebar-wrapper">
    <!-- END SIDEBAR -->
    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
    <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
    <div class="page-sidebar navbar-collapse collapse">
      <!-- BEGIN SIDEBAR MENU -->
      <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
      <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
      <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
      <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
      <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
      <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->

      <ul
        class="page-sidebar-menu page-sidebar-menu-compact"
        data-keep-expanded="false"
        data-auto-scroll="true"
        data-slide-speed="500"
        style="padding-top: 20px"
      >
        <li class="nav-item">
          <a href="javascript:;" class="nav-link nav-toggle">
            <i class="icon-layers font-white" />
            <span class="title font-white">Proyectos</span>
            <span class="arrow" />
          </a>
          <ul class="sub-menu">
            <li class="nav-item">
              <a class="nav-link">
                <i class="icon-bar-chart font-white" />
                <span class="title font-white">Analítica</span>
              </a>
            </li>
            <li @click="change_route('/proyecto_index')">
              <a class="nav-link">
                <i class="icon-bulb font-white" />
                <span class="title font-white">Gestión</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link">
                <i class="icon-doc font-white" />
                <span class="title font-white">Reportes</span>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="javascript:;" class="nav-link nav-toggle">
            <i class="icon-briefcase font-white" />
            <span class="title font-white">Productos</span>
            <span class="arrow" />
          </a>
          <ul class="sub-menu">
            <li class="nav-item">
              <a href="table_static_basic.html" class="nav-link">
                <i class="icon-bar-chart font-white" />
                <span class="title font-white">Analítica</span>
              </a>
            </li>
            <li @click="change_route('/producto_index')">
              <a class="nav-link">
                <i class="icon-bulb font-white" />
                <span class="title font-white">Gestión</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="table_bootstrap.html" class="nav-link">
                <i class="icon-doc font-white" />
                <span class="title font-white">Reportes</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-folder font-white" />
                <span class="title font-white">Otros</span>

                <span class="arrow" />
              </a>
              <ul class="sub-menu">
                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-handbag font-white" />
                    <span class="title font-white">Gestión Elementos</span>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="javascript:;" class="nav-link nav-toggle">
            <i class="icon-film font-white" />
            <span class="title font-white">Audiovisuales</span>
            <span class="arrow" />
          </a>
          <ul class="sub-menu">
            <li class="nav-item">
              <a class="nav-link nav-toggle">
                <i class="icon-bar-chart font-white" />
                <span class="title font-white">Analítica</span>
              </a>
            </li>
            <li class="nav-item" @click="change_route('/audiovisual_index')">
              <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-bulb font-white" />
                <span class="title font-white">Gestión</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-doc font-white" />
                <span class="title font-white">Reportes</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-folder font-white" />
                <span class="title font-white">Otros</span>
                <span class="arrow" />
              </a>

              <ul class="sub-menu">
                <li class="nav-item" @click="change_route('/realizador_index')">
                  <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-users font-white" />
                    <span class="title font-white">Gestión Realizadores</span>
                  </a>
                </li>

                <li
                  class="nav-item"
                  @click="change_route('/entrevistado_index')"
                >
                  <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-users font-white" />
                    <span class="title font-white">Gestión Entrevistados</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-handbag font-white" />
                    <span class="title font-white">Gestión Elementos</span>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="javascript:;" class="nav-link nav-toggle">
            <i class="icon-earphones font-white" />
            <span class="title font-white">Fonogramas</span>
            <span class="arrow" />
          </a>
          <ul class="sub-menu">
            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-earphones font-white" />
                <span class="title font-white">Fonogramas</span>
                <span class="arrow" />
              </a>
              <ul class="sub-menu">
                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-bar-chart font-white" />
                    <span class="title font-white"> Analítica </span>
                  </a>
                </li>
                <li class="nav-item" @click="change_route('/fonograma_index')">
                  <a class="nav-link">
                    <i class="icon-bulb font-white" />
                    <span class="title font-white"> Gestión </span>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-doc font-white" />
                    <span class="title font-white"> Reportes </span>
                  </a>
                </li>
              </ul>
            </li>

            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-music-tone font-white" />
                <span class="title font-white">Tracks</span>
                <span class="arrow" />
              </a>
              <ul class="sub-menu">
                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-bar-chart font-white" />
                    <span class="title font-white"> Analítica </span>
                  </a>
                </li>
                <li class="nav-item" @click="change_route('/track_index')">
                  <a class="nav-link">
                    <i class="icon-bulb font-white" />
                    <span class="title font-white"> Gestión </span>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-doc font-white" />
                    <span class="title font-white"> Reportes </span>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-folder font-white" />
                <span class="title font-white">Otros</span>
                <span class="arrow" />
              </a>
              <ul class="sub-menu">
                <li class="nav-item" @click="change_route('/tema_index')">
                  <a class="nav-link">
                    <i class="icon-note font-white" />
                    <span class="title font-white"> Gestión temas </span>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-handbag font-white" />
                    <span class="title font-white"> Gestión Elementos </span>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="javascript:;" class="nav-link nav-toggle">
            <i class="icon-user font-white" />
            <span class="title font-white"> Artistas </span>
            <span class="arrow" />
          </a>
          <ul class="sub-menu">
            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-users font-white" />
                <span class="title font-white"> Autores </span>
                <span class="arrow" />
              </a>
              <ul class="sub-menu">
                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-bar-chart font-white" />
                    <span class="title font-white"> Analítica </span>
                  </a>
                </li>
                <li @click="change_route('/autor_index')">
                  <a class="nav-link">
                    <i class="icon-bulb font-white" />
                    <span class="title font-white"> Gestión </span>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-doc font-white" />
                    <span class="title font-white"> Reportes </span>
                  </a>
                </li>
              </ul>
            </li>

            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-users font-white" />
                <span class="title font-white"> Intérpretes </span>
                <span class="arrow" />
              </a>
              <ul class="sub-menu">
                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-bar-chart font-white" />
                    <span class="title font-white"> Analítica </span>
                  </a>
                </li>
                <li @click="change_route('/interprete_index')">
                  <a class="nav-link">
                    <i class="icon-bulb font-white" />
                    <span class="title font-white"> Gestión </span>
                  </a>
                </li>
                <li @click="change_route('/artisticos_index')">
                  <a class="nav-link">
                    <i class="icon-bulb font-white" />
                    <span class="title font-white">
                      Gestión de Nombres Artísticos
                    </span>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-doc font-white" />
                    <span class="title font-white"> Reportes </span>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="javascript:;" class="nav-link nav-toggle">
            <i class="icon-users font-white" />
            <span class="title font-white"> Capital Humano </span>
            <span class="arrow"></span>
          </a>
          <ul class="sub-menu">
            <li class="nav-item">
              <a href="table_static_basic.html" class="nav-link">
                <i class="icon-bar-chart font-white" />
                <span class="title font-white">Analítica</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="table_static_responsive.html" class="nav-link">
                <i class="icon-bulb font-white" />
                <span class="title font-white">Gestión</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="table_bootstrap.html" class="nav-link">
                <i class="icon-doc font-white" />
                <span class="title font-white">Reportes</span>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="javascript:;" class="nav-link nav-toggle">
            <i class="icon-doc font-white" />
            <span class="title font-white"> Reportes </span>
            <span class="arrow" />
          </a>
          <ul class="sub-menu">
            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-doc font-white" />
                <span class="title font-white"> Generales </span>
                <span class="arrow" />
              </a>
              <ul class="sub-menu">
                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-graph font-white" />
                    <span class="title font-white"> Reporte 1 </span>
                  </a>
                </li>
              </ul>
            </li>

            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-docs font-white" />
                <span class="title font-white"> Específicos </span>
                <span class="arrow" />
              </a>
              <ul class="sub-menu">
                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-graph font-white" />
                    <span class="title font-white"> Reporte 1 </span>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="javascript:;" class="nav-link nav-toggle">
            <i class="icon-settings font-white" />
            <span class="title font-white"> Configuraciones </span>
            <span class="arrow" />
          </a>
          <ul class="sub-menu">
            <li class="nav-item">
              <a href="table_static_basic.html" class="nav-link">
                <i class="icon-home font-white" />
                <span class="title font-white"> Organización </span>
              </a>
            </li>

            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-users font-white" />
                <span class="title font-white"> Usuarios </span>
                <span class="arrow" />
              </a>
              <ul class="sub-menu">
                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-user-follow font-white" />
                    <span class="title font-white"> Gestión Usuarios </span>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-eyeglasses font-white" />
                    <span class="title font-white"> Gestión Trazas </span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-shield font-white" />
                    <span class="title font-white"> Gestión Roles </span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-shield font-white" />
                    <span class="title font-white"> Gestión Permisos </span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-bar-chart font-white" />
                    <span class="title font-white"> Analítica </span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="table_static_basic.html" class="nav-link">
                    <i class="icon-doc font-white" />
                    <span class="title font-white"> Reportes </span>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item">
              <a href="table_static_basic.html" class="nav-link">
                <i class="icon-link font-white" />
                <span class="title font-white"> Nomencladores </span>
              </a>
            </li>

            <li class="nav-item">
              <a href="table_static_basic.html" class="nav-link">
                <i class="icon-wrench font-white" />
                <span class="title font-white"> Parámetros </span>
              </a>
            </li>
          </ul>
        </li>
      </ul>
      <!-- END SIDEBAR MENU -->
    </div>
  </div>
  <!-- END SIDEBAR -->
</template>

<script>
export default {
  name: "Side_Bar",
  methods: {
    change_route(route) {
      if (route !== this.$router.currentRoute.fullPath) {
        this.$router.push(route);
        return this.$emit("change_route", this.$router.currentRoute.fullPath);
      }
    },
  },
};
</script>

<style>
.page-sidebar-menu > li > a {
  border-top: none !important;
}
.nav-item {
  border-bottom: 1px solid white !important;
}
.sub-menu .nav-item {
  border: none !important;
}
.router-link-exact-active {
  background: rgba(255, 255, 255, 0.2) !important;
  color: #e4e9f2 !important;
}
.page-sidebar,
.navbar-collapse,
.collapse {
  background-color: rgb(115, 25, 84, 0.2) !important;
}
</style>

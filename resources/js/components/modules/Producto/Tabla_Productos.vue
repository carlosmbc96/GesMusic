<template>
  <div id="tabla_productos">
    <!-- Inicio Sección de Tabla de datos -->
    <!-- Tabla -->
    <a-spin :spinning="spinning">
      <ejs-grid
        id="datatable"
        ref="gridObj"
        :dataSource="products_list"
        locale="es-ES"
        :toolbar="toolbar"
        :toolbarClick="click_toolbar"
        :allowPaging="true"
        :pageSettings="page_settings"
        :allowFiltering="true"
        :filterSettings="filter_settings"
        :allowSelection="false"
        :allowTextWrap="true"
        :allowSorting="true"
        :queryCellInfo="customise_cell"
        :allowExcelExport="true"
        :allowPdfExport="true"
      >
        <e-columns>
          <e-column
            field="codigProd"
            headerText="Código"
            width="110"
            textAlign="Left"
          />
          <e-column
            field="tituloProd"
            headerText="Título"
            width="110"
            textAlign="Left"
          />

          <e-column
            field="añoProd"
            headerText="Año"
            width="95"
            textAlign="Left"
          />
          <e-column
            field="estadodigProd"
            headerText="Estado de Digitalización"
            width="145"
            textAlign="Left"
          />
          <e-column
            field="statusComProd"
            headerText="Estatus Comercial"
            width="125"
            textAlign="Left"
          />
          <e-column
            field="genMusicPro"
            headerText="Género Musical"
            width="114"
            textAlign="Left"
          />
          <e-column
            headerText="Estado"
            :template="status"
            width="115"
            textAlign="Center"
          />
          <e-column
            headerText="Acciones"
            width="140"
            :template="actions_template"
            :visible="true"
            textAlign="Center"
          />
        </e-columns>
      </ejs-grid>
    </a-spin>
    <!-- Fin Sección de Tabla de datos -->

    <!-- Inicio Sección de Modals -->
    <modal_management
      v-if="visible_management"
      :action="action_management"
      @actualizar="refresh_table"
      :product="row_selected"
      @close_modal="visible_management = $event"
      :products_list="all_products"
    />
    <!-- Fin Sección de Modals -->
  </div>
</template>

<script>
/*
 *Importaciones
 */
import Vue from "vue";
import axios from "../../../config/axios/axios";
import modal_management from "./Modal_Gestionar_Producto";
import {
  GridPlugin,
  Edit,
  Filter,
  Group,
  Page,
  Selection,
  CommandColumn,
  Freeze,
  Sort,
  Toolbar,
  Reorder,
  PdfExport,
  ExcelExport,
  PdfExportProperties,
} from "@syncfusion/ej2-vue-grids";
import {
  ChartPlugin,
  ColumnSeries,
  Category,
  DataLabel,
  Tooltip,
  Legend,
} from "@syncfusion/ej2-vue-charts";
import { ButtonPlugin } from "@syncfusion/ej2-vue-buttons";
import { loadCldr, L10n, setCulture, Browser } from "@syncfusion/ej2-base";
import * as numberingSystems from "cldr-data/supplemental/numberingSystems.json";
import * as weekData from "cldr-data/supplemental/weekData.json";
import * as timeZoneNames from "cldr-data/main/es/timeZoneNames.json";
import * as numbers from "cldr-data/main/es/numbers.json";
import * as gregorian from "cldr-data/main/es/ca-gregorian.json";
import { image } from "../../../../../public/assets/logo_base64";
Vue.use(GridPlugin);
Vue.use(ButtonPlugin);
Vue.use(ChartPlugin);
var moment = require("moment");
moment.locale("es");
/*
 *  Código para poner el lenguaje de los elementos de syncfusion en español
 */
loadCldr(numberingSystems, weekData, timeZoneNames, numbers, gregorian);
L10n.load({
  "es-ES": {
    grid: {
      EmptyRecord: "No existen datos para mostrar",
      InvalidFilterMessage: "Datos de filtrado errados",
      NoResult: "Sin resultados",
      Search: "Buscar",
      Matchs: "Sin resultados",
      AND: "Y",
      OR: "O",
      StartsWith: "Comienza con..",
      EndsWith: "Termina con..",
      Contains: "Contiene..",
      Equal: "Igual a..",
      NotEqual: "Diferente de..",
      LessThan: "Menor que..",
      LessThanOrEqual: "Menor o igual que..",
      GreaterThan: "Mayor que..",
      GreaterThanOrEqual: "Mayor o igual que..",
      ChooseDate: "..Fecha",
      EnterValue: "..Valor",
      FilterButton: "Filtrar",
      ClearButton: "Limpiar",
      SelectAll: "Todo",
      Add: "Añadir",
      Edit: "Editar",
      Cancel: "Cancelar",
      Update: "Modificar",
      Delete: "Eliminar",
      Item: "elemento",
      Items: "elementos",
    },
    pager: {
      currentPageInfo: "{0} de {1} páginas",
      totalItemInfo: "({0} elemento)",
      totalItemsInfo: "({0} elementos)",
      firstPageTooltip: "Primera página",
      lastPageTooltip: "Última página",
      nextPageTooltip: "Página siguiente",
      previousPageTooltip: "Página anterior",
      pagerDropDown: "Elementos por página",
      pagerAllDropDown: "Elementos",
      All: "Todo",
    },
    calendar: {
      today: "Hoy",
    },
  },
});
setCulture("es-ES");
export default {
  name: "Proyecto_Index",
  props: ["proyecto", "vista_editar", "detalles_prop"],
  data() {
    return {
      //* Variables de configuración de la tabla
      page_settings: {
        pageSizes: [5, 10, 20, 30],
        pageCount: 5,
        pageSize: 10,
      },
      filter_settings: { type: "Menu" },
      toolbar: this.detalles_prop
        ? ["Search"]
        : [
            {
              text: "Añadir Producto",
              tooltipText: "Añadir Producto",
              prefixIcon: "e-add-icon",
              id: "add",
            },
            "Search",
          ],
      status_template: () => {
        return {
          template: Vue.component("columnTemplate", {
            template: `
              <div>
                <a-popconfirm
                    :disabled="$parent.$parent.$parent.detalles"
                    :placement="position"
                    @confirm="confirm_change_status"
										ok-text="Si"
										cancel-text="No"
								>
								<a-icon v-if="action === 'inactivar'" slot="icon" type="close-circle" theme="filled" style="color: #731954;" />
								<a-icon v-else slot="icon" type="check-circle" theme="filled" style="color: #BCC821 ;" />
                    <template slot="title">
                      <p>¿Desea {{ action }} el Producto?</p>
                    </template>
                    <a-tooltip :title="project.deleted_at ? 'No es posible modificar estado, el proyecto al que está asociado este producto se encuentra inactivo' : 'Cambiar estado'" placement="left">
                      <a-switch :disabled="project.deleted_at || $parent.$parent.$parent.detalles" :style="color_status" :checked="checked" :loading="loading">
                         <span slot="checkedChildren">Activo</span>
                         <span slot="unCheckedChildren">Inactivo</span>
                      </a-switch>
                    </a-tooltip>
                </a-popconfirm>
              </div>`,
            data: function(axios) {
              return {
                action: "",
                position: "",
                data: {},
                axios: axios,
                checked: false,
                loading: false,
                visible_pop: false,
                project: "",
              };
            },
            created() {
              this.checked = this.data.deleted_at === null;
              axios
                .post("proyectos/listar", {
                  atributo: "id",
                  valorbuscado: this.data.proyecto_id,
                })
                .then((response) => {
                  this.project = response.data;
                })
                .catch((error) => console.log(error));
              if (this.visible_management) {
                this.commands.push({
                  type: "Borrado Físico",
                  buttonOption: {
                    iconCss: "e-icons e-delete-physical-icon",
                    click: this.del_physical_btn_click,
                    cssClass: "e-commands-custom",
                  },
                });
              }
              this.position = this.checked ? "top" : "bottom";
              this.action = this.checked ? "inactivar" : "activar";
            },
            computed: {
              color_status() {
                return !this.checked
                  ? "background: rgb(243, 107, 100)!important"
                  : "background: rgb(76, 196, 177)!important; border-color: transparent!important";
              },
            },
            methods: {
              handle_visible_pop_change(visible) {
                if (this.project.deleted_at === null) this.visible_pop = false;
                else this.visible_pop = visible;
              },
              confirm_change_status() {
                let error = false;
                if (this.checked) {
                  this.$toast.question(
                    "¿Esta acción inactivará el Producto?",
                    "Confirmación",
                    {
                      timeout: 5000,
                      close: false,
                      overlay: true,
                      displayMode: "once",
                      color: "#AB7598",
                      zindex: 999999,
                      title: "Hey",
                      position: "center",
                      buttons: [
                        [
                          "<button>Si</button>",
                          (instance, toast) => {
                            this.$toast.question(
                              "¿Desea inactivar el Producto?",
                              "Confirmación",
                              {
                                timeout: 5000,
                                close: false,
                                color: "#8F4776",
                                overlay: true,
                                displayMode: "once",
                                zindex: 999999999,
                                title: "Hey",
                                position: "center",
                                buttons: [
                                  [
                                    "<button>Si</button>",
                                    (instance, toast) => {
                                      this.loading = true;
                                      axios
                                        .delete(
                                          "productos/desactivar/" + this.data.id
                                        )
                                        .catch((errors) => {
                                          error = true;
                                        })
                                        .finally(() => {
                                          this.finally_method(
                                            "inactivó",
                                            error
                                          );
                                        });
                                      instance.hide(
                                        { transitionOut: "fadeOut" },
                                        toast,
                                        "button"
                                      );
                                    },
                                    true,
                                  ],
                                  [
                                    "<button>No</button>",
                                    function(instance, toast) {
                                      instance.hide(
                                        { transitionOut: "fadeOut" },
                                        toast,
                                        "button"
                                      );
                                    },
                                  ],
                                ],
                              }
                            );
                            instance.hide(
                              { transitionOut: "fadeOut" },
                              toast,
                              "button"
                            );
                          },
                          true,
                        ],
                        [
                          "<button>No</button>",
                          function(instance, toast) {
                            instance.hide(
                              { transitionOut: "fadeOut" },
                              toast,
                              "button"
                            );
                          },
                        ],
                      ],
                    }
                  );
                } else {
                  this.$toast.question(
                    "¿Esta acción ativará el Producto?",
                    "Confirmación",
                    {
                      timeout: 5000,
                      close: false,
                      overlay: true,
                      displayMode: "once",
                      color: "#D7DE7A",
                      zindex: 999999,
                      title: "Hey",
                      position: "center",
                      buttons: [
                        [
                          "<button>Si</button>",
                          (instance, toast) => {
                            this.$toast.question(
                              "¿Desea activar el Producto?",
                              "Confirmación",
                              {
                                timeout: 5000,
                                close: false,
                                color: "#C9D34D",
                                overlay: true,
                                displayMode: "once",
                                zindex: 999999999,
                                title: "Hey",
                                position: "center",
                                buttons: [
                                  [
                                    "<button>Si</button>",
                                    (instance, toast) => {
                                      this.loading = true;
                                      axios
                                        .get(
                                          "productos/restaurar/" + this.data.id
                                        )
                                        .catch((errors) => {
                                          error = true;
                                        })
                                        .finally(() => {
                                          this.finally_method("activó", error);
                                        });
                                      instance.hide(
                                        { transitionOut: "fadeOut" },
                                        toast,
                                        "button"
                                      );
                                    },
                                    true,
                                  ],
                                  [
                                    "<button>No</button>",
                                    function(instance, toast) {
                                      instance.hide(
                                        { transitionOut: "fadeOut" },
                                        toast,
                                        "button"
                                      );
                                    },
                                  ],
                                ],
                              }
                            );
                            instance.hide(
                              { transitionOut: "fadeOut" },
                              toast,
                              "button"
                            );
                          },
                          true,
                        ],
                        [
                          "<button>No</button>",
                          function(instance, toast) {
                            instance.hide(
                              { transitionOut: "fadeOut" },
                              toast,
                              "button"
                            );
                          },
                        ],
                      ],
                    }
                  );
                }
              },
              finally_method(action, error) {
                this.loading = false;
                if (!error) {
                  this.$parent.$parent.$parent.load_products();
                  this.checked = !this.checked;
                  this.$toast.success(
                    `El Producto se ${action} correctamente`,
                    "¡Éxito!",
                    {
                      timeout: 1000,
                      color: action === "inactivó" ? "blue" : "grey",
                    }
                  );
                } else {
                  this.$toast.error("Ha ocurrido un error", "¡Error!", {
                    timeout: 1000,
                  });
                }
              },
            },
          }),
        };
      },
      actions_template: () => {
        return {
          template: Vue.component("columnTemplate", {
            template: `
                <div>
                <a-tooltip title="Detalles" placement="bottom">
							  <a-button size="small" :disabled="data.deleted_at !== null" @click="detail_btn_click" style="--antd-wave-shadow-color:  transparent ;box-shadow: none; background: bottom; border-radius: 100px"><a-icon type="eye" theme="filled" style="color: rgb(115, 25, 84); font-size: 20px;" /></a-icon></a-button>
                </a-tooltip>
                <a-tooltip title="Editar" placement="bottom">
                <a-button v-if="!$parent.$parent.$parent.detalles" size="small" :disabled="data.deleted_at !== null" @click ="edit_btn_click" style="--antd-wave-shadow-color:  transparent ;box-shadow: none; background: bottom; border-radius: 100px"><a-icon type="edit" theme="filled" style="color: rgb(115, 25, 84); font-size: 20px;" /></a-icon></a-button>
                </a-tooltip>
                </div>`,
            data: function(axios) {
              return {
                data: {},
              };
            },
            methods: {
              /*
               * Método con la lógica del botón detalles
               */
              detail_btn_click(args) {
                this.$parent.$parent.$parent.row_selected = this.data;
                if (this.data.deleted_at === null)
                  this.$parent.$parent.$parent.action_management = "detalles";
                this.$parent.$parent.$parent.visible_management = true;
              },
              /*
               * Método con la lógica del botón editar
               */
              edit_btn_click(args) {
                this.$parent.$parent.$parent.row_selected = this.data;
                this.$parent.$parent.$parent.row_selected.tabla = true;
                if (this.data.deleted_at === null) {
                  this.$parent.$parent.$parent.action_management = "editar";
                  this.$parent.$parent.$parent.visible_management = true;
                }
              },
            },
          }),
        };
      },
      status_child_template: () => {
        return {
          template: Vue.component("columnTemplate", {
            template: `<div>
                <span style="font-size: 12px!important; border-radius: 20px!important;" class="e-badge" :class="class_badge">{{ status }}</span>
                </div>`,
            data: function() {
              return {
                data: {},
              };
            },
            computed: {
              status() {
                return this.data.deleted_at == null ? "Activo" : "Inactivo";
              },
              class_badge() {
                return this.data.deleted_at == null
                  ? "e-badge-success"
                  : "e-badge-warning";
              },
            },
          }),
        };
      },
      status: "",
      spinning: false,
      detalles: this.detalles_prop,
      export_view: false, //* Vista del panel de exportaciones
      products_list: [], //* Lista de productos que es cargada en la tabla
      row_selected: {}, //* Fila de la tabla seleccionada | producto seleccionado
      visible_management: false, //* variable para visualizar el modal de gestión del producto
      action_management: "", //* variable contiene la acción a realizar en el modal de gestión | Insertar o Editar
      proyecto_id: "",
      all_products: [],
    };
  },
  created() {
    this.status = this.detalles_prop
      ? this.status_child_template
      : this.status_template;
    this.load_products();
  },
  methods: {
    /*
     * Método para modificar el estilo de las filas de la tabla
     */
    customise_cell(args) {
      if (args.data["deleted_at"] != null) {
        args.cell.classList.add("inactive_row");
      }
    },
    /*
     * Método que carga los productos de la bd
     */
    load_products() {
      this.$emit("reload");
      this.spinning = true;
      if (this.vista_editar) {
        axios
          .post("/productos/listar", {
            atributo: "proyecto_id",
            valorbuscado: this.proyecto.id,
          })
          .then((response) => {
            this.products_list = response.data;
            this.spinning = false;
          });
        axios
          .post("/productos/listar", { relations: ["proyecto"] })
          .then((response) => {
            this.all_products = response.data;
          });
      }
    },
    /*
     * Método que actualiza los datos de la tabla
     */
    refresh_table() {
      this.load_products();
    },
    /*
     * Método con la lógica de los botones del toolbar de la tabla
     */
    click_toolbar(args) {
      if (args.item.id === "add") {
        this.action_management = "crear";
        this.visible_management = true;
        this.row_selected = {
          tabla: true,
          proyecto_id: this.proyecto.id,
        };
      }
    },
  },
  components: {
    modal_management,
  },
  provide: {
    grid: [
      Edit,
      Group,
      Filter,
      Page,
      Selection,
      CommandColumn,
      Freeze,
      Sort,
      Reorder,
      Toolbar,
      PdfExport,
      ExcelExport,
    ],
    chart: [ColumnSeries, Category, Legend, DataLabel, Tooltip],
  },
};
</script>

<style>
#tabla_productos .e-headercontent,
#tabla_productos .e-sortfilter,
#tabla_productos thead,
#tabla_productos tr,
#tabla_productos td,
#tabla_productos th,
#tabla_productos .e-pagercontainer,
#tabla_productos .e-pagerdropdown,
#tabla_productos .e-first,
#tabla_productos .e-prev,
#tabla_productos .e-numericcontainer,
#tabla_productos .e-next,
#tabla_productos .e-last,
#tabla_productos .e-table,
#tabla_productos .e-input-group,
#tabla_productos .e-content,
#tabla_productos .e-toolbar-items,
#tabla_productos .e-tbar-btn,
#tabla_productos .e-toolbar-item,
#tabla_productos .e-gridheader,
#tabla_productos .e-gridcontent,
#tabla_productos .e-gridpager,
#tabla_productos .e-toolbar {
  background-color: transparent !important;
}
#tabla_productos .e-grid {
  background-color: rgba(255, 255, 255, 0.8) !important;
}
#tabla_productos .e-grid {
  border-radius: 5px !important;
}
#tabla_productos .e-gridheader {
  border-bottom-color: rgba(115, 25, 84, 0.7) !important;
  border-top-color: transparent !important;
}
#tabla_productos td {
  border-color: lightgrey !important;
}
#tabla_productos .e-grid,
#tabla_productos .e-toolbar,
#tabla_productos .e-grid .e-headercontent {
  border-color: transparent !important;
}
#tabla_productos .e-row:hover {
  background-color: rgba(115, 25, 84, 0.1) !important;
}
#tabla_productos thead span,
#tabla_productos .e-icon-filter {
  color: rgb(115, 25, 84) !important;
  font-weight: bold !important;
}
#tabla_productos .ant-switch-inner {
  width: auto !important;
}
#tabla_productos .e-badge.e-badge-success:not(.e-badge-ghost):not([href]),
.e-badge.e-badge-success[href]:not(.e-badge-ghost) {
  color: white !important;
}
</style>

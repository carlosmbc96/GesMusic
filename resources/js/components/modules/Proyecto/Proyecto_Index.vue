<template>
  <div id="proyecto_index">
    <h1 style="color: white !important">Proyectos</h1>
    <hr style="border-color: white !important" />

    <!-- Inicio Sección de Analítica | Gráficas -->
    <ejs-chart
      style="display: block; margin: 20px"
      :theme="theme"
      align="center"
      id="chartcontainer"
      ref="chartObj"
      :background="background_chart"
      :primaryXAxis="primary_x_axis"
      :primaryYAxis="primary_y_axis"
      :chartArea="chart_area"
      width="50%"
      height="60%"
      :tooltip="tooltip"
      :load="load"
      :legendSettings="{ visible: false }"
      v-if="projects_list.length !== 0"
    >
      <e-series-collection>
        <e-series
          :dataSource="series_data"
          type="Column"
          xName="years"
          yName="proyects"
          name="Año"
          :marker="marker"
          :animation="animation_series"
        />
      </e-series-collection>
    </ejs-chart>
    <!-- Fin Sección de Analítica | Gráficas -->

    <!-- Inicio Sección de Tabla de datos -->
    <!-- Seccion Panel de exportaciones -->
    <div id="exportPanelContainer">
      <div id="arrowDropUpExports">
        <a-tooltip :title="export_view ? 'Ocultar panel' : 'Mostrar Panel'"
          ><span
            class="e-icons export-icons"
            :class="export_view ? 'e-down-arrow-export' : 'e-up-arrow-export'"
            @click="
              () => {
                export_view = !export_view;
              }
            "
        /></a-tooltip>
        <span><a-icon class="e-icon-export" type="export" /></span>
      </div>
      <transition
        enter-active-class="animate__animated animate__slideInUp"
        leave-active-class="animate__animated animate__slideOutDown"
      >
        <div id="dropUpExports" v-if="export_view">
          <a-tooltip title="Imprimir"
            ><span
              @click="panel_export_click('print')"
              class="e-icons export-icons e-print-export"
            ></span
          ></a-tooltip>
          <a-tooltip title="Exportar a PDF"
            ><span
              @click="panel_export_click('pdf')"
              class="e-icons export-icons e-pdf-export"
            ></span
          ></a-tooltip>
          <a-tooltip title="Exportar a Excel"
            ><span
              @click="panel_export_click('excel')"
              class="e-icons export-icons e-excel-export"
            ></span
          ></a-tooltip>
          <a-tooltip title="Exportar a CSV"
            ><span
              @click="panel_export_click('csv')"
              class="e-icons export-icons e-csv-export"
            ></span
          ></a-tooltip>
        </div>
      </transition>
    </div>
    <div class="clearfix"></div>
    <!-- Tabla -->
    <a-spin :spinning="spinning">
      <ejs-grid
        id="datatable"
        ref="gridObj"
        locale="es-ES"
        :childGrid="products_childs"
        :dataSource="projects_list"
        :toolbar="toolbar"
        :toolbarClick="click_toolbar"
        :allowPaging="true"
        :pageSettings="page_settings"
        :allowFiltering="true"
        :filterSettings="filter_settings"
        :allowTextWrap="true"
        :allowSorting="true"
        :pdfExportComplete="pdf_export_complete"
        :excelExportComplete="excel_export_complete"
        :queryCellInfo="customise_cell"
        :pdfQueryCellInfo="pdf_customise_cell"
        :excelQueryCellInfo="excel_customise_cell"
        :allowExcelExport="true"
        :allowPdfExport="true"
      >
        <e-columns>
          <e-column
            field="codigProy"
            headerText="Código"
            width="110"
            textAlign="Left"
          />
          <e-column
            field="nombreProy"
            headerText="Nombre"
            width="150"
            textAlign="Left"
          />
          <e-column
            field="añoProy"
            headerText="Año"
            width="90"
            textAlign="Left"
          />
          <e-column
            headerText="Estado"
            width="115"
            :template="status_template"
            :visible="true"
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

    <modal_management
      v-if="visible_management"
      :action="action_management"
      @actualizar="refresh_table"
      :project="row_selected"
      @close_modal="visible_management = $event"
      :projects_list="projects_list"
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
import modal_management from "./Modal_Gestionar_Proyecto";
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
  DetailRow,
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
/*
 *  Código para configurar el tema del gráfico
 */
let selected_theme = location.hash.split("/")[1];
selected_theme = selected_theme ? selected_theme : "Material";
let theme = (
  selected_theme.charAt(0).toUpperCase() + selected_theme.slice(1)
).replace(/-dark/i, "Dark");
export default {
  name: "Proyecto_Index",
  data() {
    return {
      //* Variables de configuración del gráfico
      theme: theme,
      chart_area: { border: { width: 0 } },
      width: Browser.isDevice ? "100%" : "60%",
      marker: {
        dataLabel: {
          visible: true,
          position: "Top",
          font: { fontWeight: "600", color: "#ffffff" },
        },
      },
      tooltip: {
        enable: true,
        header: "Proyectos por Año",
        format: "${point.x} : ${point.y} Proyectos",
        fill: "rgba(115, 25, 84, 0.9)",
        border: { width: 0 },
      },
      animation_series: { enable: true, duration: 1000, delay: 50 },
      palettes: ["#E94649"],
      background_chart: "transparent",
      series_data: [],
      primary_x_axis: {
        valueType: "Category",
        title: "Años",
        titleStyle: {
          color: "white",
          size: "16px",
          fontWeight: "bold",
        },
        interval: 1,
        majorGridLines: { width: 0 },
        majorTickLines: { width: 1, color: "white" },
        lineStyle: { color: "white" },
        labelStyle: { color: "white" },
      },
      primary_y_axis: {
        title: "Proyectos",
        titleStyle: {
          color: "white",
          size: "16px",
          fontWeight: "bold",
        },
        interval: 5,
        majorGridLines: { width: 0 },
        majorTickLines: { width: 1, color: "white" },
        lineStyle: { color: "white" },
        labelStyle: { color: "white" },
      },
      //* Variables de configuración de la tabla
      page_settings: { pageSizes: [5, 10, 20, 30], pageCount: 5, pageSize: 10 },
      filter_settings: { type: "Menu" },
      toolbar: [
        {
          text: "Añadir Proyecto",
          tooltipText: "Añadir Proyecto",
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
                    :placement="position"
                    @confirm="confirm_change_status"
										ok-text="Si"
										cancel-text="No"
								>
								<a-icon v-if="action === 'inactivar'" slot="icon" type="close-circle" theme="filled" style="color: #731954;" />
								<a-icon v-else slot="icon" type="check-circle" theme="filled" style="color: #BCC821 ;" />
                    <template slot="title">
                      <p>¿Desea {{ action }} el Proyecto?</p>
                    </template>
                    <a-tooltip title="Cambiar estado" placement="left">
                      <a-switch :style="color_status" :checked="checked" :loading="loading">
                         <span slot="checkedChildren">Activo</span>
                         <span slot="unCheckedChildren">Inactivo</span>
                      </a-switch>
                    </a-tooltip>
                </a-popconfirm>
              </div>`,
            data: function (axios) {
              return {
                action: "",
                position: "",
                data: {},
                axios: axios,
                checked: false,
                loading: false,
              };
            },
            created() {
              this.checked = this.data.deleted_at == null;
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
              confirm_change_status() {
                let error = false;
                if (this.checked) {
                  this.$toast.question(
                    "¿Esta acción inactivará el Proyecto y <br> los Productos asociados a este?",
                    "Confirmación",
                    {
                      timeout: 5000,
                      close: false,
                      overlay: true,
                      displayMode: "once",
                      color: "#AB7598",
                      id: "question",
                      zindex: 999,
                      title: "Hey",
                      position: "center",
                      buttons: [
                        [
                          "<button>Si</button>",
                          (instance, toast) => {
                            this.$toast.question(
                              "¿Desea inactivar el Proyecto?",
                              "Confirmación",
                              {
                                timeout: 5000,
                                close: false,
                                color: "#8F4776",
                                overlay: true,
                                displayMode: "once",
                                zindex: 9999,
                                title: "Hey",
                                position: "center",
                                buttons: [
                                  [
                                    "<button>Si</button>",
                                    (instance, toast) => {
                                      this.loading = true;
                                      axios
                                        .delete(
                                          "proyectos/desactivar/" + this.data.id
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
                                    function (instance, toast) {
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
                          function (instance, toast) {
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
                    "¿Esta acción ativará el Proyecto y <br> los Productos asociados a este?",
                    "Confirmación",
                    {
                      timeout: 5000,
                      close: false,
                      overlay: true,
                      displayMode: "once",
                      id: "question",
                      color: "#D7DE7A",
                      zindex: 999,
                      title: "Hey",
                      position: "center",
                      buttons: [
                        [
                          "<button>Si</button>",
                          (instance, toast) => {
                            this.$toast.question(
                              "¿Desea activar el Proyecto?",
                              "Confirmación",
                              {
                                timeout: 5000,
                                close: false,
                                color: "#C9D34D",
                                overlay: true,
                                displayMode: "once",
                                zindex: 9999,
                                title: "Hey",
                                position: "center",
                                buttons: [
                                  [
                                    "<button>Si</button>",
                                    (instance, toast) => {
                                      this.loading = true;
                                      axios
                                        .get(
                                          "proyectos/restaurar/" + this.data.id
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
                                    function (instance, toast) {
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
                          function (instance, toast) {
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
                  this.$parent.$parent.$parent.load_projects();
                  this.checked = !this.checked;
                  this.$toast.success(
                    `El Proyecto se ${action} correctamente`,
                    "¡Éxito!",
                    {
                      timeout: 2000,
                      color: action === "inactivó" ? "blue" : "grey",
                    }
                  );
                } else {
                  this.$toast.error("Ha ocurrido un error", "¡Error!", {
                    timeout: 2000,
                  });
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
            data: function () {
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
      actions_template: () => {
        return {
          template: Vue.component("columnTemplate", {
            template: `
                <div>
                <a-tooltip title="Detalles" placement="bottom">
							  <a-button size="small" :disabled="data.deleted_at !== null" @click="detail_btn_click" style="--antd-wave-shadow-color:  transparent ;box-shadow: none; background: bottom; border-radius: 100px"><a-icon type="eye" theme="filled" style="color: rgb(115, 25, 84); font-size: 20px;" /></a-button>
                </a-tooltip>
                <a-tooltip title="Editar" placement="bottom">
                <a-button size="small" :disabled="data.deleted_at !== null" @click ="edit_btn_click" style="--antd-wave-shadow-color:  transparent ;box-shadow: none; background: bottom; border-radius: 100px"><a-icon type="edit" theme="filled" style="color: rgb(115, 25, 84); font-size: 20px;" /></a-button>
                </a-tooltip>
                <a-popconfirm
                    placement="leftBottom"
                    @confirm="del_physical_btn_click"
										ok-text="Si"
                    cancel-text="No"
                    title="¿Desea eliminar el Proyecto?"
                >
                <a-icon slot="icon" type="close-circle" theme="filled" style="color: #F36B64;" />
                <a-tooltip title="Eliminar" placement="bottom">
                <a-button size="small" style="--antd-wave-shadow-color:  transparent ;box-shadow: none; background: bottom; border-radius: 100px"><a-icon type="delete" theme="filled" style="color: black; font-size: 20px;" /></a-button>
                </a-tooltip>
                </a-popconfirm>
                </div>`,
            data: function (axios) {
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
                if (this.data.deleted_at === null) {
                  this.$parent.$parent.$parent.action_management = "editar";
                  this.$parent.$parent.$parent.visible_management = true;
                }
              },
              /*
               * Método con la lógica del botón borrado físico
               */
              del_physical_btn_click(args) {
                this.$toast.question(
                  "¿Esta acción de eliminación es irrevercible, si elimina este Proyecto, <br> se eliminaránn también los Productos asociados a este?",
                  "Confirmación",
                  {
                    timeout: 5000,
                    close: false,
                    overlay: true,
                    displayMode: "once",
                    id: "question",
                    color: "#F8A6A2",
                    zindex: 999,
                    title: "Hey",
                    position: "center",
                    buttons: [
                      [
                        "<button>Si</button>",
                        (instance, toast) => {
                          this.$toast.question(
                            "¿Desea eliminar el Proyecto?",
                            "Confirmación",
                            {
                              timeout: 5000,
                              close: false,
                              color: "#F58983",
                              overlay: true,
                              displayMode: "once",
                              zindex: 9999,
                              title: "Hey",
                              position: "center",
                              buttons: [
                                [
                                  "<button>Si</button>",
                                  (instance, toast) => {
                                    this.$parent.$parent.$parent.change_spin();
                                    axios
                                      .delete(
                                        `proyectos/eliminar/${this.data.id}`
                                      )
                                      .then((ress) => {
                                        this.$parent.$parent.$parent.refresh_table();
                                        this.$toast.success(
                                          "El Proyecto ha sido eliminado correctamente",
                                          "¡Éxito!",
                                          { timeout: 2000, color: "red" }
                                        );
                                        this.$parent.$parent.$parent.change_spin();
                                      })
                                      .catch((err) => {
                                        console.log(err);
                                        this.$toast.error(
                                          "Ha ocurrido un error",
                                          "¡Error!",
                                          {
                                            timeout: 2000,
                                          }
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
                                  function (instance, toast) {
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
                        function (instance, toast) {
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
              },
            },
          }),
        };
      },
      spinning: false,
      export_view: false, //* Vista del panel de exportaciones
      projects_list: [], //* Lista de proyectos que es cargada en la tabla
      products_childs: {}, //* Objeto de productos hijos de los proyectos
      row_selected: {}, //* Fila de la tabla seleccionada | proyecto seleccionado
      visible_management: false, //* variable para visualizar el modal de gestión del proyecto
      action_management: "", //* variable contiene la acción a realizar en el modal de gestión | Insertar o Editar
    };
  },
  created() {
    this.load_projects();
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
     * Método para modificar el estilo de las filas de la tabla para el pdf
     */
    pdf_customise_cell(args) {
      if (args.column.headerText == "Estado") {
        if (args.data["deleted_at"] != null) {
          args.style = { backgroundColor: "#f36b64" };
          args.value = "Incativo";
        } else {
          args.style = { backgroundColor: "#4cc4b1" };
          args.value = "Activo";
        }
      }
      if (args.column.field == "created_at") {
        args.value = moment(args.value).format("LLL");
      }
    },
    /*
     * Método para modificar el estilo de las filas de la tabla para el excel
     */
    excel_customise_cell(args) {
      if (args.column.headerText == "Estado") {
        if (args.data["deleted_at"] != null) {
          args.style = { backColor: "#f36b64" };
          args.value = "Incativo";
        } else {
          args.style = { backColor: "#4cc4b1" };
          args.value = "Activo";
        }
      }
      if (args.column.field == "created_at") {
        args.value = moment(args.value).format("ll");
      }
    },
    /*
     * Método que carga los proyectos de la bd
     */
    load_projects() {
      if (this.action_management !== "detalles") {
        this.change_spin();
      }
      axios
        .post("/proyectos/listar", { relations: ["productos"] })
        .then((response) => {
          this.projects_list = response.data;
          this.series_data = [];
          this.projects_list.forEach((project) => {
            let index = this.series_data.findIndex(
              (serie) => serie.years === parseInt(project.añoProy.toString())
            );
            if (index != -1) {
              this.series_data[index].proyects += 1;
            } else {
              this.series_data.push({
                years: parseInt(project.añoProy.toString()),
                proyects: 1,
              });
            }
          });
          this.series_data.sort((x, y) => {
            return parseInt(x.years) - parseInt(y.years);
          });
          axios.post("/productos/listar").then((res) => {
            this.products_childs = {
              dataSource: res.data,
              queryString: "proyecto_id",
              ref: "childGrid",
              columns: [
                {
                  field: "tituloProd",
                  headerText: "Título",
                  width: "110",
                  textAlign: "Left",
                },
                {
                  field: "codigProd",
                  headerText: "Código",
                  width: "110",
                  textAlign: "Left",
                },
                {
                  field: "estadodigProd",
                  headerText: "Estado de Digitalización",
                  width: "135",
                  textAlign: "Left",
                },
                {
                  field: "añoProd",
                  headerText: "Año",
                  width: "90",
                  textAlign: "Left",
                },
                {
                  field: "statusComProd",
                  headerText: "Estatus Comercial",
                  width: "120",
                  textAlign: "Left",
                },
                {
                  field: "genMusicPro",
                  headerText: "Género Musical",
                  width: "110",
                  textAlign: "Left",
                },
                {
                  headerText: "Estado",
                  template: this.status_child_template,
                  width: "105",
                  visible: true,
                  textAlign: "Center",
                },
              ],
              load: function () {
                this.parentDetails.parentKeyFieldValue = this.parentDetails.parentRowData[
                  "id"
                ];
              },
            };
            this.$refs.gridObj.refresh();
          });
          if (this.action_management !== "detalles") {
            this.change_spin();
          }
        });
    },
    /*
     * Método de configuración del gráfico
     */
    load(args) {
      let selected_theme = location.hash.split("/")[1];
      selected_theme = selected_theme ? selected_theme : "Material";
      if (selected_theme === "highcontrast") {
        args.chart.series[0].marker.dataLabel.font.color = "#000000";
        args.chart.series[1].marker.dataLabel.font.color = "#000000";
        args.chart.series[2].marker.dataLabel.font.color = "#000000";
      }
    },
    /*
     * Método que actualiza los datos de la tabla
     */
    refresh_table() {
      this.load_projects();
    },
    /*
     * Método que activa y desactiva el spin
     */
    change_spin() {
      this.spinning = !this.spinning;
    },
    /*
     * Método con la lógica de los botones del toolbar de la tabla
     */
    click_toolbar(args) {
      if (args.item.id === "add") {
        this.action_management = "crear";
        this.visible_management = true;
        this.row_selected = {};
      }
    },
    /*
     * Método con la lógica de los botones del panel de exportación
     */
    panel_export_click(args) {
      let pdfExportProperties = {
        hierarchyExportMode: "Expanded",
        fileName: "Reporte_Proyectos.pdf",
        pageOrientation: "Landscape",
        header: {
          fromTop: 0,
          height: 120,
          contents: [
            {
              type: "PageNumber",
              pageNumberType: "Number",
              format: "Página {$current} de {$total}", //optional
              position: { x: 0, y: 0 },
              style: {
                textBrushColor: "#a9a9a9",
                fontSize: 10,
                hAlign: "Center",
                fontFamily: "Calibri",
              },
            },
            {
              type: "Text",
              value: "Reporte de Proyectos",
              position: { x: 0, y: 40 },
              style: {
                textBrushColor: "#731954",
                fontSize: 20,
                fontFamily: "Calibri",
              },
            },
            {
              type: "Line",
              style: { penColor: "#731954", penSize: 1, dashStyle: "Solid" },
              points: { x1: 0, y1: 70, x2: 280, y2: 70 },
            },
            {
              type: "Text",
              value: "Fecha del reporte: " + new moment().format("LLL"),
              position: { x: 0, y: 75 },
              style: {
                textBrushColor: "#808080",
                fontSize: 12,
                fontFamily: "Calibri",
              },
            },
            {
              type: "Image",
              src: image,
              position: { x: 775, y: 0 },
              size: { height: 110, width: 250 },
            },
          ],
        },
        theme: {
          header: {
            fontColor: "#731954",
            bold: true,
            borders: { color: "#731954", lineStyle: "Thin" },
          },
        },
      };
      let excelExportProperties = {
        hierarchyExportMode: "Expanded",
        fileName: "",
        header: {
          headerRows: 3,
          rows: [
            {
              cells: [
                {
                  colSpan: 4,
                  value: "Reporte de Proyectos",
                  style: {
                    fontColor: "#731954",
                    fontSize: 20,
                    fontFamily: "Calibri",
                    hAlign: "Center",
                    bold: true,
                  },
                },
              ],
            },
            {
              cells: [
                {
                  colSpan: 4,
                  value: "Fecha del reporte: " + new moment().format("LLL"),
                  style: {
                    fontColor: "#808080",
                    fontSize: 12,
                    hAlign: "Center",
                    fontFamily: "Calibri",
                    bold: true,
                  },
                },
              ],
            },
          ],
        },
        theme: {
          header: {
            fontColor: "#731954",
            bold: true,
          },
        },
      };
      if (args === "pdf") {
        this.$refs.gridObj.getColumns()[4].visible = false;
        this.$refs.gridObj.pdfExport(pdfExportProperties);
      } else if (args === "excel") {
        excelExportProperties.fileName = "Reporte_Proyectos.xlsx";
        this.$refs.gridObj.getColumns()[4].visible = false;
        this.$refs.gridObj.excelExport(excelExportProperties);
      } else if (args === "csv") {
        excelExportProperties.fileName = "Reporte_Proyectos.csv";
        this.$refs.gridObj.getColumns()[4].visible = false;
        this.$refs.gridObj.csvExport(excelExportProperties);
      } else if (args === "print") {
        this.$refs.gridObj.getColumns()[4].visible = false;
        this.$refs.gridObj.print(pdfExportProperties);
      }
    },
    /*
     * Métodos para volver a mostrar las columnas 3 y 4 luego de exportar
     */
    pdf_export_complete(args) {
      this.$refs.gridObj.getColumns()[4].visible = true;
    },
    excel_export_complete(args) {
      this.$refs.gridObj.getColumns()[4].visible = true;
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
      DetailRow,
      PdfExport,
      ExcelExport,
    ],
    chart: [ColumnSeries, Category, Legend, DataLabel, Tooltip],
  },
};
</script>

<style>
#proyecto_index .e-headercontent,
#proyecto_index .e-sortfilter,
#proyecto_index thead,
#proyecto_index tr,
#proyecto_index td,
#proyecto_index th,
#proyecto_index .e-pagercontainer,
#proyecto_index .e-pagerdropdown,
#proyecto_index .e-first,
#proyecto_index .e-prev,
#proyecto_index .e-numericcontainer,
#proyecto_index .e-next,
#proyecto_index .e-last,
#proyecto_index .e-table,
#proyecto_index .e-input-group,
#proyecto_index .e-content,
#proyecto_index .e-toolbar-items,
#proyecto_index .e-tbar-btn,
#proyecto_index .e-toolbar-item,
#proyecto_index .e-gridheader,
#proyecto_index .e-gridcontent,
#proyecto_index .e-gridpager,
#proyecto_index .e-toolbar {
  background-color: transparent !important;
}
#proyecto_index .e-grid {
  background-color: rgba(255, 255, 255, 0.8) !important;
}
#proyecto_index .e-gridheader {
  border-bottom-color: rgba(115, 25, 84, 0.7) !important;
  border-top-color: transparent !important;
}
#proyecto_index td {
  border-color: lightgrey !important;
}
#proyecto_index .e-grid,
#proyecto_index .e-toolbar,
#proyecto_index .e-grid .e-headercontent {
  border-color: transparent !important;
}
#proyecto_index .e-row:hover {
  background-color: rgba(115, 25, 84, 0.1) !important;
}
#proyecto_index .e-detailrowcollapse .e-icon-grightarrow,
#proyecto_index .e-detailrowexpand .e-icon-gdownarrow,
#proyecto_index thead span,
#proyecto_index .e-icon-filter {
  color: rgb(115, 25, 84) !important;
  font-weight: bold !important;
}
#proyecto_index .ant-switch-inner {
  width: auto !important;
}
#proyecto_index .e-badge.e-badge-success:not(.e-badge-ghost):not([href]),
.e-badge.e-badge-success[href]:not(.e-badge-ghost) {
  color: white !important;
}
</style>

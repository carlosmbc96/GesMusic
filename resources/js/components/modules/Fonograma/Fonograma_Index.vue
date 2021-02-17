<template>
  <div class="col-md-12">
    <div
      class="portlet light"
      style="background-color: rgba(255, 255, 255, 0.4)"
    >
      <div style="min-height: 600px">
        <div id="fonograma_index">
          <h1 style="color: white !important">Fonogramas</h1>
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
          >
            <e-series-collection>
              <e-series
                :dataSource="series_data"
                type="Column"
                xName="years"
                yName="fonograms"
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
              <a-tooltip
                :title="export_view ? 'Ocultar panel' : 'Mostrar Panel'"
                ><span
                  class="e-icons export-icons"
                  :class="
                    export_view ? 'e-down-arrow-export' : 'e-up-arrow-export'
                  "
                  @click="
                    () => {
                      export_view = !export_view;
                    }
                  "
                ></span
              ></a-tooltip>
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
              :dataSource="fonograms_list"
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
                  field="codigFong"
                  headerText="Código"
                  width="110"
                  textAlign="Left"
                />
                <e-column
                  field="tituloFong"
                  headerText="Título"
                  width="150"
                  textAlign="Left"
                />
                <e-column
                  field="añoFong"
                  headerText="Año"
                  width="110"
                  textAlign="Left"
                />
                <e-column
                  field="clasficacionFong"
                  headerText="Clasificación"
                  width="150"
                  textAlign="Left"
                />
                <e-column
                  headerText="Estado"
                  width="120"
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

          <!-- Inicio Sección de Modals -->
          <modal_management
            v-if="visible_management"
            :action="action_management"
            @actualizar="refresh_table"
            :fonogram="row_selected"
            @close_modal="visible_management = $event"
            :fonograms_list="fonograms_list"
          />
          <!-- Fin Sección de Modals -->
        </div>
      </div>
    </div>
  </div>
</template>

<script>
/*
 *Importaciones
 */
import modal_management from "./Modal_Gestionar_Fonograma";
import Vue from "vue";
import axios from "../../../config/axios/axios";
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
  name: "Fonograma_Index",
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
        header: "Fonogramas por Año",
        format: "${point.x} : ${point.y} Fonogramas",
        fill: "rgba(115, 25, 84, 0.9)",
        border: { width: 0 },
      },
      animation_series: { enable: true, duration: 1000, delay: 50 },
      palettes: ["#E94649", "#F6B53F", "#6FAAB0", "#C4C24A"],
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
        title: "Fonogramas",
        titleStyle: {
          color: "white",
          size: "16px",
          fontWeight: "bold",
        },
        interval: 10,
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
          text: "Añadir Fonograma",
          tooltipText: "Añadir Fonograma",
          prefixIcon: "e-add-icon",
          id: "ad",
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
                      <p>¿Desea {{ action }} el Fonograma?</p>
                    </template>
                    <a-tooltip title="Cambiar estado" placement="left">
                      <a-switch class="hover-switch" :style="color_status" :checked="checked" :loading="loading">
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
                    "¿Esta acción inactivará el Fonograma?",
                    "Confirmación",
                    {
                      timeout: 5000,
                      close: false,
                      overlay: true,
                      displayMode: "once",
                      color: "#AB7598",
                      zindex: 999,
                      title: "Hey",
                      position: "center",
                      buttons: [
                        [
                          "<button>Si</button>",
                          (instance, toast) => {
                            this.$toast.question(
                              "¿Desea inactivar el Fonograma?",
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
                                          "fonogramas/desactivar/" +
                                            this.data.id
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
                    "¿Esta acción ativará el Fonograma?",
                    "Confirmación",
                    {
                      timeout: 5000,
                      close: false,
                      overlay: true,
                      displayMode: "once",
                      color: "#D7DE7A",
                      zindex: 999,
                      title: "Hey",
                      position: "center",
                      buttons: [
                        [
                          "<button>Si</button>",
                          (instance, toast) => {
                            this.$toast.question(
                              "¿Desea activar el Fonograma?",
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
                                          "fonogramas/restaurar/" + this.data.id
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
                  this.$parent.$parent.$parent.load_fonograms();
                  this.checked = !this.checked;
                  this.$toast.success(
                    `El Fonograma se ${action} correctamente`,
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
      actions_template: () => {
        return {
          template: Vue.component("columnTemplate", {
            template: `
                <div>
                <a-tooltip title="Detalles" placement="bottom">
							  <a-button class="hover" size="small" :disabled="data.deleted_at !== null" @click="detail_btn_click" style="--antd-wave-shadow-color:  transparent ;box-shadow: none; background: bottom; border-radius: 100px"><a-icon type="eye" theme="filled" style="color: rgb(115, 25, 84); font-size: 20px;" /></a-button>
                </a-tooltip>
                <a-tooltip title="Editar" placement="bottom">
                <a-button class="hover" size="small" :disabled="data.deleted_at !== null" @click ="edit_btn_click" style="--antd-wave-shadow-color:  transparent ;box-shadow: none; background: bottom; border-radius: 100px"><a-icon type="edit" theme="filled" style="color: rgb(115, 25, 84); font-size: 20px;" /></a-button>
                </a-tooltip>
                <a-popconfirm
                    placement="leftBottom"
                    @confirm="del_physical_btn_click"
										ok-text="Si"
                    cancel-text="No"
                    title="¿Desea eliminar el Fonograma?"
                >
                <a-icon slot="icon" type="close-circle" theme="filled" style="color: #F36B64;" />
                <a-tooltip title="Eliminar" placement="bottom">
                <a-button class="hover" size="small" style="--antd-wave-shadow-color:  transparent ;box-shadow: none; background: bottom; border-radius: 100px"><a-icon type="delete" theme="filled" style="color: black; font-size: 20px;" /></a-button>
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
                  "¿Esta acción de eliminación es irrevercible?",
                  "Confirmación",
                  {
                    timeout: 5000,
                    close: false,
                    overlay: true,
                    displayMode: "once",
                    color: "#F8A6A2",
                    zindex: 999,
                    title: "Hey",
                    position: "center",
                    buttons: [
                      [
                        "<button>Si</button>",
                        (instance, toast) => {
                          this.$toast.question(
                            "¿Desea eliminar el Fonograma?",
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
                                        `fonogramas/eliminar/${this.data.id}`
                                      )
                                      .then((ress) => {
                                        this.$parent.$parent.$parent.refresh_table();
                                        this.$toast.success(
                                          "El Fonograma ha sido eliminado correctamente",
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
      fonograms_list: [], //* Lista de Fonogramas que es cargada en la tabla
      row_selected: {}, //* Fila de la tabla seleccionada | Fonograma seleccionado
      fonograms_list: [],
      visible_details: false, //* variable para visualizar el modal de detalles del Fonograma
      visible_management: false, //* variable para visualizar el modal de gestión del Fonograma
      action_management: "", //* variable contiene la acción a realizar en el modal de gestión | Insertar o Editar
    };
  },
  created() {
    this.load_fonograms();
  },
  methods: {
    /*
     * Método que activa y desactiva el spin
     */
    change_spin() {
      this.spinning = !this.spinning;
    },
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
     * Método que carga los Fonogramas de la bd
     */
    /*
     * Método que carga los Audiovisuales de la bd
     */
    load_fonograms() {
      if (this.action_management !== "detalles") {
        this.change_spin();
      }
      axios
        .post("/fonogramas/listar", { relations: ["productos"] })
        .then((response) => {
          this.fonograms_list = response.data;
          this.series_data = [];
          this.fonograms_list.forEach((fonograma) => {
            let index = this.series_data.findIndex(
              (serie) => serie.years === parseInt(fonograma.añoFong.toString())
            );
            if (index != -1) {
              this.series_data[index].fonograms += 1;
            } else {
              this.series_data.push({
                years: parseInt(fonograma.añoFong.toString()),
                fonograms: 1,
              });
            }
          });
          this.series_data.sort((x, y) => {
            return x.years - y.years;
          });
          if (
            this.series_data.length > 0 &&
            this.series_data[this.series_data.length - 1].fonograms < 5
          ) {
            this.primary_y_axis.interval = 5;
          }
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
      this.load_fonograms();
    },
    /*
     * Método con la lógica de los botones del toolbar de la tabla
     */
    click_toolbar(args) {
      if (args.item.id === "ad") {
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
        fileName: "Reporte_Fonogramas.pdf",
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
              value: "Reporte de Fonogramas",
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
                  value: "Reporte de Fonogramas",
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
        excelExportProperties.fileName = "Reporte_Fonograma.xlsx";
        this.$refs.gridObj.getColumns()[4].visible = false;
        this.$refs.gridObj.excelExport(excelExportProperties);
      } else if (args === "csv") {
        excelExportProperties.fileName = "Reporte_Fonogramas.csv";
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
    //modal_detail,
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
#fonograma_index .e-headercontent,
#fonograma_index .e-sortfilter,
#fonograma_index thead,
#fonograma_index tr,
#fonograma_index td,
#fonograma_index th,
#fonograma_index .e-pagercontainer,
#fonograma_index .e-pagerdropdown,
#fonograma_index .e-first,
#fonograma_index .e-prev,
#fonograma_index .e-numericcontainer,
#fonograma_index .e-next,
#fonograma_index .e-last,
#fonograma_index .e-table,
#fonograma_index .e-input-group,
#fonograma_index .e-content,
#fonograma_index .e-toolbar-items,
#fonograma_index .e-tbar-btn,
#fonograma_index .e-toolbar-item,
#fonograma_index .e-gridheader,
#fonograma_index .e-gridcontent,
#fonograma_index .e-gridpager,
#fonograma_index .e-toolbar {
  background-color: transparent !important;
}
#fonograma_index .e-grid {
  background-color: rgba(255, 255, 255, 0.8) !important;
}
#fonograma_index .e-gridheader {
  border-bottom-color: rgba(115, 25, 84, 0.7) !important;
  border-top-color: transparent !important;
}
#fonograma_index td {
  border-color: lightgrey !important;
}
#fonograma_index .e-grid,
#fonograma_index .e-toolbar,
#fonograma_index .e-grid .e-headercontent {
  border-color: transparent !important;
}
#fonograma_index .e-row:hover {
  background-color: rgba(115, 25, 84, 0.1) !important;
}
#fonograma_index .e-detailrowcollapse .e-icon-grightarrow,
#fonograma_index .e-detailrowexpand .e-icon-gdownarrow,
#fonograma_index thead span,
#fonograma_index .e-icon-filter {
  color: rgb(115, 25, 84) !important;
  font-weight: bold !important;
}
#fonograma_index .ant-switch-inner {
  width: auto !important;
}
#fonograma_index .e-badge.e-badge-success:not(.e-badge-ghost):not([href]),
.e-badge.e-badge-success[href]:not(.e-badge-ghost) {
  color: white !important;
}
</style>

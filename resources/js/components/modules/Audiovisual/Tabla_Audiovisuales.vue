<template>
  <div id="tabla_audiovisuales">
    <!-- Inicio Sección de Tabla de datos -->
    <!-- Tabla -->
    <a-spin :spinning="spinning">
      <ejs-grid
        id="datatable"
        ref="gridObj"
        :dataSource="audiovisuals_list"
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
      >
        <e-columns>
          <e-column
            field="codigAud"
            headerText="Código"
            width="110"
            textAlign="Left"
          />
          <e-column
            field="isrcAud"
            headerText="ISRC"
            width="105"
            textAlign="Left"
          />
          <e-column
            field="tituloAud"
            headerText="Título"
            width="100"
            textAlign="Left"
          />
          <e-column
            field="añoFinAud"
            headerText="Año"
            width="90"
            textAlign="Left"
          />
          <e-column
            field="clasifAud"
            headerText="Clasificación"
            width="130"
            textAlign="Left"
          />
          <e-column
            field="paisGrabAud"
            headerText="País"
            width="100"
            textAlign="Left"
          />
          <e-column
            field="idiomaAud"
            headerText="Idioma"
            width="105"
            textAlign="Left"
          />
          <e-column
            headerText="Estado"
            width="90"
            :template="status"
            :visible="true"
            textAlign="Center"
          />
          <e-column
            headerText="Acciones"
            width="160"
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
      :audiovisual="row_selected"
      @close_modal="visible_management = $event"
      :audiovisuals_list="all_audiovisuals"
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
import modal_management from "./Modal_Gestionar_Audiovisual";
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
  name: "Tabla_Audiovisuales",
  props: ["producto", "vista_editar", "detalles_prop"],
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
              text: "Añadir Audiovisual",
              tooltipText: "Añadir Audiovisual",
              prefixIcon: "e-add-icon",
              id: "add",
            },
            "Search",
          ],
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
                      <p>¿Desea {{ action }} el Audiovisual?</p>
                    </template>
                    <a-tooltip title="Cambiar estado" placement="left">
                      <a-switch :disabled="$parent.$parent.$parent.detalles" :style="color_status" :checked="checked" :loading="loading">
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
                    "¿Esta acción inactivará el Audiovisual?",
                    "Confirmación",
                    {
                      timeout: 5000,
                      close: false,
                      overlay: true,
                      displayMode: "once",
                      color: "#AB7598",
                      zindex: 9999,
                      title: "Hey",
                      position: "center",
                      buttons: [
                        [
                          "<button>Si</button>",
                          (instance, toast) => {
                            this.$toast.question(
                              "¿Desea inactivar el Audiovisual?",
                              "Confirmación",
                              {
                                timeout: 5000,
                                close: false,
                                color: "#8F4776",
                                overlay: true,
                                displayMode: "once",
                                zindex: 99999,
                                title: "Hey",
                                position: "center",
                                buttons: [
                                  [
                                    "<button>Si</button>",
                                    (instance, toast) => {
                                      this.loading = true;
                                      axios
                                        .delete(
                                          "audiovisuales/desactivar/" +
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
                    "¿Esta acción ativará el Audiovisual?",
                    "Confirmación",
                    {
                      timeout: 5000,
                      close: false,
                      overlay: true,
                      displayMode: "once",
                      color: "#D7DE7A",
                      zindex: 9999,
                      title: "Hey",
                      position: "center",
                      buttons: [
                        [
                          "<button>Si</button>",
                          (instance, toast) => {
                            this.$toast.question(
                              "¿Desea activar el Audiovisual?",
                              "Confirmación",
                              {
                                timeout: 5000,
                                close: false,
                                color: "#C9D34D",
                                overlay: true,
                                displayMode: "once",
                                zindex: 99999,
                                title: "Hey",
                                position: "center",
                                buttons: [
                                  [
                                    "<button>Si</button>",
                                    (instance, toast) => {
                                      this.loading = true;
                                      axios
                                        .get(
                                          "audiovisuales/restaurar/" +
                                            this.data.id
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
                  this.$parent.$parent.$parent.load_audiovisuals();
                  this.checked = !this.checked;
                  this.$toast.success(
                    `El Audiovisual se ${action} correctamente`,
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
        const ChainBroken = {
          template: `
    <svg width="1em" height="1em" fill="currentColor" viewBox="0 0 24 24">
      <path d="M 5 7 C 2.2 7 0 9.2 0 12 C 0 14.8 2.2 17 5 17 L 8 17 C 9.9 17 11.5125 16.00625 12.3125 14.40625 L 10.09375 14.1875 C 9.49375 14.6875 8.8 15 8 15 L 5 15 C 3.3 15 2 13.7 2 12 C 2 10.3 3.3 9 5 9 L 8 9 C 9 9 9.80625 9.4875 10.40625 10.1875 L 12.8125 10.5 C 12.1125 8.5 10.2 7 8 7 L 5 7 z M 16.34375 7.84375 C 16.112563 7.8440581 15.880748 7.8741789 15.65625 7.90625 L 15.9375 9.875 C 16.43951 9.803284 17.073353 9.901973 17.59375 10.125 C 18.293583 10.531306 19.652462 11.150726 20.28125 11.40625 C 21.765569 12.074193 22.373644 13.850236 21.6875 15.375 C 21.019557 16.859319 19.243514 17.467394 17.71875 16.78125 C 17.002493 16.365409 15.574624 15.7111 14.96875 15.46875 L 14.21875 17.34375 C 14.590684 17.492524 16.466199 18.37347 16.78125 18.5625 L 16.84375 18.59375 L 16.875 18.625 C 19.350236 19.738856 22.367943 18.734431 23.5 16.21875 L 23.53125 16.15625 C 24.599844 13.693188 23.6192 10.716139 21.125 9.59375 L 21.09375 9.59375 L 21.0625 9.5625 C 20.690566 9.4137263 18.815051 8.5327803 18.5 8.34375 L 18.46875 8.3125 L 18.40625 8.28125 C 17.746548 7.9985205 17.03731 7.8428258 16.34375 7.84375 z M 8.71875 11 A 1.0004882 1.0004882 0 0 0 8.875 13 L 16.875 14 A 1.0077822 1.0077822 0 0 0 17.125 12 L 9.125 11 A 1.0001 1.0001 0 0 0 8.8125 11 A 1.0004882 1.0004882 0 0 0 8.71875 11 z" />
    </svg>
  `,
        };
        const ChainBrokenIcon = {
          template: `
    <a-icon :component="ChainBroken" />
  `,
          data() {
            return {
              ChainBroken,
            };
          },
        };
        return {
          template: Vue.component("columnTemplate", {
            template: `
                <div>
                <a-tooltip title="Detalles" placement="bottom">
							  <a-button size="small" :disabled="data.deleted_at !== null" @click="detail_btn_click" style="--antd-wave-shadow-color:  transparent ;box-shadow: none; background: bottom; border-radius: 100px"><a-icon type="eye" theme="filled" style="color: rgb(115, 25, 84); font-size: 20px;" /></a-icon></a-button>
                </a-tooltip>
                <a-tooltip title="Editar" placement="bottom">
                <a-button v-if="!$parent.$parent.$parent.detalles"size="small" :disabled="data.deleted_at !== null" @click ="edit_btn_click" style="--antd-wave-shadow-color:  transparent ;box-shadow: none; background: bottom; border-radius: 100px"><a-icon type="edit" theme="filled" style="color: rgb(115, 25, 84); font-size: 20px;" /></a-icon></a-button>
                </a-tooltip>

                <a-popconfirm
                    placement="leftBottom"
                    @confirm="del_relation_btn_click"
										ok-text="Si"
                    cancel-text="No"
                    title="¿Desea desvincular el Audiovisual de este Producto?"
                >
                <a-icon slot="icon" type="close-circle" theme="filled" style="color: #F36B64;" />
                <a-tooltip title="Desvincular" placement="bottom">
                <a-button @mouseenter="changeIcon" v-if="!broken_relation" size="small" style="--antd-wave-shadow-color:  transparent ;box-shadow: none; background: bottom; border-radius: 100px"><a-icon :rotate="45" type="link" :style="{ fontSize: '25px', color: 'black' }"/></a-icon></a-button>
                <a-button @mouseleave="changeIcon" v-else size="small" style="--antd-wave-shadow-color:  transparent ;box-shadow: none; background: bottom; border-radius: 100px"><chain-broken-icon :style="{ fontSize: '25px', color: 'black' }" /></a-icon></a-button>
                </a-tooltip>
                </a-popconfirm>

                </div>`,
            data: function (axios) {
              return {
                data: {},
                broken_relation: false,
              };
            },
            methods: {
              changeIcon() {
                this.broken_relation = !this.broken_relation;
              },
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
              /*
               * Método con la lógica del botón desvincular
               */
              del_relation_btn_click(args) {
                this.$toast.question(
                  "¿Esta acción eliminará el vínculo del Audiovisual en este Producto?",
                  "Confirmación",
                  {
                    timeout: 5000,
                    close: false,
                    overlay: true,
                    displayMode: "once",
                    color: "#F8A6A2",
                    zindex: 9999,
                    title: "Hey",
                    position: "center",
                    buttons: [
                      [
                        "<button>Si</button>",
                        (instance, toast) => {
                          this.$toast.question(
                            "¿Desea desvincular el Audiovisual?",
                            "Confirmación",
                            {
                              timeout: 5000,
                              close: false,
                              color: "#F58983",
                              overlay: true,
                              displayMode: "once",
                              zindex: 99999,
                              title: "Hey",
                              position: "center",
                              buttons: [
                                [
                                  "<button>Si</button>",
                                  (instance, toast) => {
                                    this.$parent.$parent.$parent.change_spin();
                                    axios
                                      .post(`productos/desvincularAud`, {
                                        idProd: this.$parent.$parent.$parent
                                          .producto.id,
                                        idAud: this.data.id,
                                      })
                                      .then((ress) => {
                                        this.$parent.$parent.$parent.refresh_table();
                                        this.$toast.success(
                                          "El Audiovisual ha sido desvinculado correctamente",
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
            components: {
              ChainBrokenIcon,
            },
          }),
        };
      },
      export_view: false, //* Vista del panel de exportaciones
      spinning: false,
      status: "",
      detalles: this.detalles_prop,
      audiovisuals_list: [], //* Lista de Audiovisuales que es cargada en la tabla
      row_selected: {}, //* Fila de la tabla seleccionada | Audiovisual seleccionado
      visible_details: false, //* variable para visualizar el modal de detalles del Audiovisual
      visible_management: false, //* variable para visualizar el modal de gestión del Audiovisual
      all_audiovisuals: [],
      action_management: "", //* variable contiene la acción a realizar en el modal de gestión | Insertar o Editar
    };
  },
  created() {
    this.status = this.detalles_prop
      ? this.status_child_template
      : this.status_template;
    this.load_audiovisuals();
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
     * Método que carga los productos de la bd
     */
    load_audiovisuals() {
      this.$emit("reload");
      this.change_spin()
      if (this.vista_editar) {
        axios
          .post("/audiovisuales/listar", { relations: ["productos"] })
          .then((response) => {
            this.audiovisuals_list = [];
            let pertenece = false;
            response.data.forEach((audiovisual) => {
              audiovisual.productos.forEach((producto) => {
                if (producto.id === this.producto.id) {
                  pertenece = true;
                }
              });
              if (pertenece) {
                this.audiovisuals_list.push(audiovisual);
              }
              pertenece = false;
            });
            this.change_spin()
          });
        axios.post("/audiovisuales/listar").then((response) => {
          this.all_audiovisuals = response.data;
        });
      }
    },
    /*
     * Método que actualiza los datos de la tabla
     */
    refresh_table() {
      this.audiovisuals_list = null;
      this.load_audiovisuals();
    },
    /*
     * Método con la lógica de los botones del toolbar de la tabla
     */
    click_toolbar(args) {
      if (args.item.id === "add") {
        this.action_management = "crear";
        this.visible_management = true;
        this.row_selected = {
          modal_detalles: true,
          productos_audvs: this.producto.id,
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
#tabla_audiovisuales .e-headercontent,
#tabla_audiovisuales .e-sortfilter,
#tabla_audiovisuales thead,
#tabla_audiovisuales tr,
#tabla_audiovisuales td,
#tabla_audiovisuales th,
#tabla_audiovisuales .e-pagercontainer,
#tabla_audiovisuales .e-pagerdropdown,
#tabla_audiovisuales .e-first,
#tabla_audiovisuales .e-prev,
#tabla_audiovisuales .e-numericcontainer,
#tabla_audiovisuales .e-next,
#tabla_audiovisuales .e-last,
#tabla_audiovisuales .e-table,
#tabla_audiovisuales .e-input-group,
#tabla_audiovisuales .e-content,
#tabla_audiovisuales .e-toolbar-items,
#tabla_audiovisuales .e-tbar-btn,
#tabla_audiovisuales .e-toolbar-item,
#tabla_audiovisuales .e-gridheader,
#tabla_audiovisuales .e-gridcontent,
#tabla_audiovisuales .e-gridpager,
#tabla_audiovisuales .e-toolbar {
  background-color: transparent !important;
}
#tabla_audiovisuales .e-grid {
  background-color: rgba(255, 255, 255, 0.8) !important;
}
#tabla_audiovisuales .e-grid {
  border-radius: 5px !important;
}
#tabla_audiovisuales .e-gridheader {
  border-bottom-color: rgba(115, 25, 84, 0.7) !important;
  border-top-color: transparent !important;
}
#tabla_audiovisuales td {
  border-color: lightgrey !important;
}
#tabla_audiovisuales .e-grid,
#tabla_audiovisuales .e-toolbar,
#tabla_audiovisuales .e-grid .e-headercontent {
  border-color: transparent !important;
}
#tabla_audiovisuales .e-row:hover {
  background-color: rgba(115, 25, 84, 0.1) !important;
}
#tabla_audiovisuales thead span,
#tabla_audiovisuales .e-icon-filter {
  color: rgb(115, 25, 84) !important;
  font-weight: bold !important;
}
#tabla_audiovisuales .ant-switch-inner {
  width: auto !important;
}
#tabla_audiovisuales .e-badge.e-badge-success:not(.e-badge-ghost):not([href]),
.e-badge.e-badge-success[href]:not(.e-badge-ghost) {
  color: white !important;
}
</style>

<template>
  <div id="tabla_tracks">
    <!-- Inicio Sección de Tabla de datos -->
    <!-- Tabla -->
    <a-spin :spinning="spinning">
      <ejs-grid
        id="datatable"
        ref="gridObj"
        :dataSource="tracks_list"
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
            field="ordenTrk"
            headerText="Orden"
            width="100"
            textAlign="Left"
          />
          <e-column
            field="isrcTrk"
            headerText="ISRC"
            width="105"
            textAlign="Left"
          />
          <e-column
            field="tituloTrk"
            headerText="Título"
            width="98"
            textAlign="Left"
          />
          <e-column
            field="duracionTrk"
            headerText="Duración"
            width="115"
            textAlign="Left"
          />
          <e-column
            field="generoTrk"
            headerText="Género"
            width="105"
            textAlign="Left"
          />
          <e-column
            field="subgeneroTrk"
            headerText="Subgénero"
            width="123"
            textAlign="Left"
          />
          <e-column
            :displayAsCheckBox="true"
            field="bonusTrk"
            headerText="Bonus"
            width="100"
            textAlign="Center"
            type="boolean"
          />
          <e-column
            headerText="Estado"
            width="115"
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
      :track="row_selected"
      @close_modal="visible_management = $event"
      :tracks_list="all_tracks"
    />
    <!-- Fin Sección de Modals -->
    <transfer_modal
      v-if="visible_transfer"
      @actualizar="refresh_table"
      :entity_id="fonograma.id"
      @close_modal="visible_transfer = $event"
    ></transfer_modal>
  </div>
</template>

<script>
/*
 *Importaciones
 */
import Vue from "vue";
import axios from "../../../config/axios/axios";
import modal_management from "./Modal_Gestionar_Track";
import transfer_modal from "./Transfer_Modal_Track";
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
  name: "tabla_tracks",
  props: ["fonograma", "vista_editar", "detalles_prop"],
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
              text: "Añadir Track",
              tooltipText: "Añadir Track",
              prefixIcon: "e-add-icon",
              id: "add",
            },
            "Search",
            {
              text: "Gestionar Relaciones",
              tooltipText: "Gestionar Relaciones",
              prefixIcon: "e-transfer-icon",
              id: "vinc_desvinc",
            },
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
                      <p>¿Desea {{ action }} el Track?</p>
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
                    "¿Esta acción inactivará el Track?",
                    "Confirmación",
                    {
                      timeout: 5000,
                      close: false,
                      overlay: true,
                      displayMode: "once",
                      color: "#AB7598",
                      zindex: 9999999,
                      title: "Hey",
                      position: "center",
                      buttons: [
                        [
                          "<button>Si</button>",
                          (instance, toast) => {
                            this.$toast.question(
                              "¿Desea inactivar el Track?",
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
                                          "tracks/desactivar/" + this.data.id
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
                    "¿Esta acción ativará el Track?",
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
                              "¿Desea activar el Track?",
                              "Confirmación",
                              {
                                timeout: 5000,
                                close: false,
                                color: "#C9D34D",
                                overlay: true,
                                displayMode: "once",
                                zindex: 999999,
                                title: "Hey",
                                position: "center",
                                buttons: [
                                  [
                                    "<button>Si</button>",
                                    (instance, toast) => {
                                      this.loading = true;
                                      axios
                                        .get("tracks/restaurar/" + this.data.id)
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
                  this.$parent.$parent.$parent.load_tracks();
                  this.checked = !this.checked;
                  this.$toast.success(
                    `El Track se ${action} correctamente`,
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
							  <a-button size="small" :disabled="data.deleted_at !== null" @click="detail_btn_click" style="--antd-wave-shadow-color:  transparent ;box-shadow: none; background: bottom; border-radius: 100px"><a-icon type="eye" theme="filled" style="color: rgb(115, 25, 84); font-size: 20px;" /></a-icon></a-button>
                </a-tooltip>
                <a-tooltip title="Editar" placement="bottom">
                <a-button  v-if="!$parent.$parent.$parent.detalles" size="small" :disabled="data.deleted_at !== null" @click ="edit_btn_click" style="--antd-wave-shadow-color:  transparent ;box-shadow: none; background: bottom; border-radius: 100px"><a-icon type="edit" theme="filled" style="color: rgb(115, 25, 84); font-size: 20px;" /></a-icon></a-button>
                </a-tooltip>
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
      status: "",
      export_view: false, //* Vista del panel de exportaciones
      tracks_list: [], //* Lista de tracks que es cargada en la tabla
      row_selected: {}, //* Fila de la tabla seleccionada | fonograma seleccionado
      visible_details: false, //* variable para visualizar el modal de detalles del fonograma
      visible_management: false, //* variable para visualizar el modal de gestión del fonograma
      all_tracks: [],
      visible_transfer: false,
      spinning: false,
      detalles: this.detalles_prop,
      action_management: "", //* variable contiene la acción a realizar en el modal de gestión | Insertar o Editar
    };
  },
  created() {
    this.status = this.detalles_prop
      ? this.status_child_template
      : this.status_template;
    this.load_tracks();
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
    load_tracks() {
      this.change_spin();
      this.$emit("reload");
      if (this.vista_editar) {
        axios
          .post("/tracks/listar", { relations: ["fonogramas"] })
          .then((response) => {
            this.tracks_list = [];
            let pertenece = false;
            response.data.forEach((track) => {
              track.fonogramas.forEach((fonograma) => {
                if (fonograma.id === this.fonograma.id) {
                  pertenece = true;
                }
              });
              if (pertenece) {
                this.tracks_list.push(track);
              }
              pertenece = false;
            });
            this.change_spin();
          });
        axios.post("/tracks/listar").then((response) => {
          this.all_tracks = response.data;
        });
      }
    },
    /*
     * Método que actualiza los datos de la tabla
     */
    refresh_table() {
      this.tracks_list = null;
      this.load_tracks();
    },
    /*
     * Método con la lógica de crear
     */
    click_toolbar(args) {
      if (args.item.id === "add") {
        this.action_management = "crear";
        this.visible_management = true;
        this.row_selected = {
          modal_detalles: true,
          fonogramas_tracks: this.fonograma.id,
        };
      } else if (args.item.id === "vinc_desvinc") {
        this.visible_transfer = true;
      }
    },
  },
  components: {
    modal_management,
    transfer_modal,
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
#tabla_tracks .e-headercontent,
#tabla_tracks .e-sortfilter,
#tabla_tracks thead,
#tabla_tracks tr,
#tabla_tracks td,
#tabla_tracks th,
#tabla_tracks .e-pagercontainer,
#tabla_tracks .e-pagerdropdown,
#tabla_tracks .e-first,
#tabla_tracks .e-prev,
#tabla_tracks .e-numericcontainer,
#tabla_tracks .e-next,
#tabla_tracks .e-last,
#tabla_tracks .e-table,
#tabla_tracks .e-input-group,
#tabla_tracks .e-content,
#tabla_tracks .e-toolbar-items,
#tabla_tracks .e-tbar-btn,
#tabla_tracks .e-toolbar-item,
#tabla_tracks .e-gridheader,
#tabla_tracks .e-gridcontent,
#tabla_tracks .e-gridpager,
#tabla_tracks .e-toolbar {
  background-color: transparent !important;
}
#tabla_tracks .e-grid {
  background-color: rgba(255, 255, 255, 0.8) !important;
}
#tabla_tracks .e-grid {
  border-radius: 5px !important;
}
#tabla_tracks .e-gridheader {
  border-bottom-color: rgba(115, 25, 84, 0.7) !important;
  border-top-color: transparent !important;
}
#tabla_tracks td {
  border-color: lightgrey !important;
}
#tabla_tracks .e-grid,
#tabla_tracks .e-toolbar,
#tabla_tracks .e-grid .e-headercontent {
  border-color: transparent !important;
}
#tabla_tracks .e-row:hover {
  background-color: rgba(115, 25, 84, 0.1) !important;
}
#tabla_tracks thead span,
#tabla_tracks .e-icon-filter {
  color: rgb(115, 25, 84) !important;
  font-weight: bold !important;
}
#tabla_tracks .ant-switch-inner {
  width: auto !important;
}
#tabla_tracks .e-badge.e-badge-success:not(.e-badge-ghost):not([href]),
.e-badge.e-badge-success[href]:not(.e-badge-ghost) {
  color: white !important;
}
</style>

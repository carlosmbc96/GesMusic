<template>
  <div id="tabla_temas">
    <!-- Inicio Sección de Tabla de datos -->
    <!-- Tabla -->
    <a-spin :spinning="spinning">
      <ejs-grid
        id="datatable"
        ref="gridObj"
        :dataSource="temas_list"
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
            field="codigTema"
            headerText="Código"
            width="110"
            textAlign="Left"
          />
          <e-column
            field="tituloTem"
            headerText="Título"
            width="105"
            textAlign="Left"
          />
          <e-column
            field="sociedadGestionTem"
            headerText="Sociedad de Acciones"
            width="110"
            textAlign="Left"
          />
          <e-column
            headerText="Estado"
            width="118"
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
      :tema="row_selected"
      @close_modal="visible_management = $event"
      :temas_list="all_temas"
    />
    <!-- Fin Sección de Modals -->
    <transfer_modal
      v-if="visible_transfer"
      @actualizar="refresh_table"
      :entity_id="entity.id"
      :entity_relation="entity_relation"
      @close_modal="visible_transfer = $event"
    />
  </div>
</template>

<script>
/*
 *Importaciones
 */
import Vue from "vue";
import modal_management from "./Modal_Gestionar_Tema";
import transfer_modal from "./Transfer_Modal_Temas";
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
  name: "Tabla_Autores",
  props: ["entity", "vista_editar", "detalles_prop", "entity_relation"],
  data() {
    return {
      //* Variables de configuración de la tabla
      page_settings: {
        pageSizes: [5, 10, 20, 30],
        pageCount: 5,
        pageSize: 10,
      },
      filter_settings: { type: "Menu" },
      /* toolbar: this.detalles_prop
        ? ["Search"]
        : [
            {
              text: "Añadir Tema",
              tooltipText: "Añadir Tema",
              prefixIcon: "e-add-icon",
              id: `ad-${this.entity_relation}`,
            },
            "Search",
            {
              text: "Gestionar Relaciones",
              tooltipText: "Gestionar Relaciones",
              prefixIcon: "e-transfer-icon",
              id: `vinc_desvinc`,
            },
          ], */
      toolbar: this.menuToolbar(),
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
                      <p>¿Desea {{ action }} el Tema?</p>
                    </template>
                    <a-tooltip title="Cambiar estado" placement="left">
                      <a-switch class="hover-switch" :disabled="$parent.$parent.$parent.detalles" :style="color_status" :checked="checked" :loading="loading">
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
                    "¿Esta acción inactivará el Tema?",
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
                              "¿Desea inactivar el Tema?",
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
                                          "temas/desactivar/" + this.data.id
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
                    "¿Esta acción ativará el Tema?",
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
                              "¿Desea activar el Tema?",
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
                                        .get("temas/restaurar/" + this.data.id)
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
                  this.$parent.$parent.$parent.load_temas();
                  this.checked = !this.checked;
                  this.$toast.success(
                    `El Tema se ${action} correctamente`,
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
                <a-button class="hover" v-if="!$parent.$parent.$parent.detalles"size="small" :disabled="data.deleted_at !== null" @click ="edit_btn_click" style="--antd-wave-shadow-color:  transparent ;box-shadow: none; background: bottom; border-radius: 100px"><a-icon type="edit" theme="filled" style="color: rgb(115, 25, 84); font-size: 20px;" /></a-button>
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
      export_view: false, //* Vista del panel de exportaciones
      spinning: false,
      visible_transfer: false,
      status: "",
      detalles: this.detalles_prop,
      temas_list: [], //* Lista de Audiovisuales que es cargada en la tabla
      row_selected: {}, //* Fila de la tabla seleccionada | Audiovisual seleccionado
      visible_details: false, //* variable para visualizar el modal de detalles del Audiovisual
      visible_management: false, //* variable para visualizar el modal de gestión del Audiovisual
      all_temas: [],
      action_management: "", //* variable contiene la acción a realizar en el modal de gestión | Insertar o Editar
    };
  },
  created() {
    this.status = this.detalles_prop
      ? this.status_child_template
      : this.status_template;
    this.load_temas();
  },
  methods: {
    menuToolbar() {
      if (this.entity_relation === "tracks" && !this.detalles_prop) {
        return [
          {
            text: "Añadir Tema",
            tooltipText: "Añadir Tema",
            prefixIcon: "e-add-icon",
            id: `ad-${this.entity_relation}`,
          },
          "Search",
        ];
      } else if (this.entity_relation === "autores") {
        return [
          {
            text: "Añadir Tema",
            tooltipText: "Añadir Tema",
            prefixIcon: "e-add-icon",
            id: `ad-${this.entity_relation}`,
          },
          "Search",
          {
            text: "Gestionar Relaciones",
            tooltipText: "Gestionar Relaciones",
            prefixIcon: "e-transfer-icon",
            id: `vinc_desvinc`,
          },
        ];
      } else if (this.detalles_prop) {
        return ["Search"];
      }
    },
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
    load_temas() {
      this.$emit("reload");
      this.change_spin();
      if (this.vista_editar) {
        if (this.entity_relation === "autores") {
          axios
            .post("/temas/listar", { relations: [this.entity_relation] })
            .then((response) => {
              this.temas_list = [];
              let pertenece = false;
              response.data.forEach((tema) => {
                if (this.entity_relation === "autores") {
                  tema.autores.forEach((autor) => {
                    if (autor.id === this.entity.id) {
                      pertenece = true;
                    }
                  });
                }
                if (pertenece) {
                  this.temas_list.push(tema);
                }
                pertenece = false;
              });
              this.change_spin();
            });
        } else {
          axios
            .post("/temas/listar", {
              atributo: "track_id",
              valorbuscado: this.entity.id,
            })
            .then((response) => {
              this.temas_list = response.data;
              this.change_spin();
            });
        }
        axios.post("/temas/listar").then((response) => {
          this.all_temas = response.data;
        });
      }
    },
    /*
     * Método que actualiza los datos de la tabla
     */
    refresh_table() {
      this.temas_list = null;
      this.load_temas();
    },
    /*
     * Método con la lógica de los botones del toolbar de la tabla
     */
    click_toolbar(args) {
      switch (args.item.id) {
        case "ad-autores": {
          this.action_management = "crear";
          this.visible_management = true;
          this.row_selected = {
            modal_detalles: true,
            autores_temas: this.entity.id,
          };
          break;
        }
        case "ad-tracks": {
          this.action_management = "crear";
          this.visible_management = true;
          this.row_selected = {
            modal_detalles: true,
            tracks_temas: this.entity.id,
          };
          break;
        }
        case "vinc_desvinc": {
          this.visible_transfer = true;
        }
        default:
          break;
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
#tabla_temas .e-headercontent,
#tabla_temas .e-sortfilter,
#tabla_temas thead,
#tabla_temas tr,
#tabla_temas td,
#tabla_temas th,
#tabla_temas .e-pagercontainer,
#tabla_temas .e-pagerdropdown,
#tabla_temas .e-first,
#tabla_temas .e-prev,
#tabla_temas .e-numericcontainer,
#tabla_temas .e-next,
#tabla_temas .e-last,
#tabla_temas .e-table,
#tabla_temas .e-input-group,
#tabla_temas .e-content,
#tabla_temas .e-toolbar-items,
#tabla_temas .e-tbar-btn,
#tabla_temas .e-toolbar-item,
#tabla_temas .e-gridheader,
#tabla_temas .e-gridcontent,
#tabla_temas .e-gridpager,
#tabla_temas .e-toolbar {
  background-color: transparent !important;
}
#tabla_temas .e-grid {
  background-color: rgba(255, 255, 255, 0.8) !important;
}
#tabla_temas .e-grid {
  border-radius: 5px !important;
}
#tabla_temas .e-gridheader {
  border-bottom-color: rgba(115, 25, 84, 0.7) !important;
  border-top-color: transparent !important;
}
#tabla_temas td {
  border-color: lightgrey !important;
}
#tabla_temas .e-grid,
#tabla_temas .e-toolbar,
#tabla_temas .e-grid .e-headercontent {
  border-color: transparent !important;
}
#tabla_temas .e-row:hover {
  background-color: rgba(115, 25, 84, 0.1) !important;
}
#tabla_temas thead span,
#tabla_temas .e-icon-filter {
  color: rgb(115, 25, 84) !important;
  font-weight: bold !important;
}
#tabla_temas .ant-switch-inner {
  width: auto !important;
}
#tabla_temas .e-badge.e-badge-success:not(.e-badge-ghost):not([href]),
.e-badge.e-badge-success[href]:not(.e-badge-ghost) {
  color: white !important;
}
</style>

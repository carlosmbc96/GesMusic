<template>
  <div id="tabla_autores">
    <!-- Inicio Sección de Tabla de datos -->
    <!-- Tabla -->
    <a-spin :spinning="spinning">
      <ejs-grid
        id="datatable"
        ref="gridObj"
        :dataSource="autores_list"
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
            field="codigAutr"
            headerText="Código"
            width="115"
            textAlign="Left"
          />
          <e-column
            field="ciAutr"
            headerText="Carnet de Identidad"
            width="130"
            textAlign="Left"
          />
          <e-column
            field="nombresAutr"
            headerText="Nombre"
            width="120"
            textAlign="Left"
          />
          <e-column
            field="apellidosAutr"
            headerText="Apellidos"
            width="128"
            textAlign="Left"
          />
          <e-column
            field="sexoAutr"
            headerText="Sexo"
            width="110"
            textAlign="Left"
          />
          <e-column
            :displayAsCheckBox="true"
            field="fallecidoAutr"
            headerText="Fallecido"
            width="125"
            textAlign="Center"
            type="boolean"
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
            width="155"
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
      :author="row_selected"
      @close_modal="visible_management = $event"
      :autors_list="all_autores"
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
import modal_management from "./Modal_Gestionar_Autor";
import transfer_modal from "./Transfer_Modal_Autores";
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
import { image } from "../../../../../../public/assets/logo_base64";
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
      toolbar: this.detalles_prop
        ? ["Search"]
        : [
            {
              text: "Añadir Autor",
              tooltipText: "Añadir Autor",
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
                      <p>¿Desea {{ action }} el Autor?</p>
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
                    "¿Esta acción inactivará el Autor?",
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
                              "¿Desea inactivar el Autor?",
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
                                          "autores/desactivar/" + this.data.id
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
                    "¿Esta acción ativará el Autor?",
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
                              "¿Desea activar el Autor?",
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
                                          "autores/restaurar/" + this.data.id
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
                  this.$parent.$parent.$parent.load_autores();
                  this.checked = !this.checked;
                  this.$toast.success(
                    `El Autor se ${action} correctamente`,
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
      autores_list: [], //* Lista de Audiovisuales que es cargada en la tabla
      row_selected: {}, //* Fila de la tabla seleccionada | Audiovisual seleccionado
      visible_details: false, //* variable para visualizar el modal de detalles del Audiovisual
      visible_management: false, //* variable para visualizar el modal de gestión del Audiovisual
      all_autores: [],
      action_management: "", //* variable contiene la acción a realizar en el modal de gestión | Insertar o Editar
    };
  },
  created() {
    this.status = this.detalles_prop
      ? this.status_child_template
      : this.status_template;
    this.load_autores();
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
    load_autores() {
      this.$emit("reload");
      this.change_spin();
      if (this.vista_editar) {
        axios
          .post("/autores/listar", { relations: [this.entity_relation] })
          .then((response) => {
            this.autores_list = [];
            let pertenece = false;
            response.data.forEach((autor) => {
              if (this.entity_relation === "audiovisuales") {
                autor.audiovisuales.forEach((audiovisual) => {
                  if (audiovisual.id === this.entity.id) {
                    pertenece = true;
                  }
                });
              } else if (this.entity_relation === "tracks") {
                autor.tracks.forEach((track) => {
                  if (track.id === this.entity.id) {
                    pertenece = true;
                  }
                });
              } else if (this.entity_relation === "temas") {
                autor.temas.forEach((tema) => {
                  if (tema.id === this.entity.id) {
                    pertenece = true;
                  }
                });
              }
              if (pertenece) {
                this.autores_list.push(autor);
              }
              pertenece = false;
            });
            this.change_spin();
          });
        axios.post("/autores/listar").then((response) => {
          this.all_autores = response.data;
        });
      }
    },
    /*
     * Método que actualiza los datos de la tabla
     */
    refresh_table() {
      this.autores_list = null;
      this.load_autores();
    },
    /*
     * Método con la lógica de los botones del toolbar de la tabla
     */
    click_toolbar(args) {
      switch (args.item.id) {
        case "ad-temas": {
          this.action_management = "crear";
          this.visible_management = true;
          this.row_selected = {
            modal_detalles: true,
            temas_autrs: this.entity.id,
          };
          break;
        }
        case "ad-audiovisuales": {
          this.action_management = "crear";
          this.visible_management = true;
          this.row_selected = {
            modal_detalles: true,
            audiovisuales_autrs: this.entity.id,
          };
          break;
        }
        case "ad-tracks": {
          this.action_management = "crear";
          this.visible_management = true;
          this.row_selected = {
            modal_detalles: true,
            tracks_autrs: this.entity.id,
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
#tabla_autores .e-headercontent,
#tabla_autores .e-sortfilter,
#tabla_autores thead,
#tabla_autores tr,
#tabla_autores td,
#tabla_autores th,
#tabla_autores .e-pagercontainer,
#tabla_autores .e-pagerdropdown,
#tabla_autores .e-first,
#tabla_autores .e-prev,
#tabla_autores .e-numericcontainer,
#tabla_autores .e-next,
#tabla_autores .e-last,
#tabla_autores .e-table,
#tabla_autores .e-input-group,
#tabla_autores .e-content,
#tabla_autores .e-toolbar-items,
#tabla_autores .e-tbar-btn,
#tabla_autores .e-toolbar-item,
#tabla_autores .e-gridheader,
#tabla_autores .e-gridcontent,
#tabla_autores .e-gridpager,
#tabla_autores .e-toolbar {
  background-color: transparent !important;
}
#tabla_autores .e-grid {
  background-color: rgba(255, 255, 255, 0.8) !important;
}
#tabla_autores .e-grid {
  border-radius: 5px !important;
}
#tabla_autores .e-gridheader {
  border-bottom-color: rgba(115, 25, 84, 0.7) !important;
  border-top-color: transparent !important;
}
#tabla_autores td {
  border-color: lightgrey !important;
}
#tabla_autores .e-grid,
#tabla_autores .e-toolbar,
#tabla_autores .e-grid .e-headercontent {
  border-color: transparent !important;
}
#tabla_autores .e-row:hover {
  background-color: rgba(115, 25, 84, 0.1) !important;
}
#tabla_autores thead span,
#tabla_autores .e-icon-filter {
  color: rgb(115, 25, 84) !important;
  font-weight: bold !important;
}
#tabla_autores .ant-switch-inner {
  width: auto !important;
}
#tabla_autores .e-badge.e-badge-success:not(.e-badge-ghost):not([href]),
.e-badge.e-badge-success[href]:not(.e-badge-ghost) {
  color: white !important;
}
</style>

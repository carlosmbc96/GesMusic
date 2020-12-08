<template>
  <div>
    <a-modal
      width="80.4%"
      okText="Guardar"
      cancelText="Cancelar"
      cancelType="danger"
      :closable="false"
      :visible="show"
      :destroyOnClose="true"
      @cancel="handleCancel()"
      id="modal_detalles_productos"
    >
      <template slot="footer">
        <a-button key="back" type="primary" @click="handleCancel()">
          Aceptar</a-button
        >
      </template>
      <a-tabs default-active-key="1">
        <div slot="tabBarExtraContent">Detalles Producto</div>
        <!-- <a-tab-pane key="1">
          <span slot="tab"> Generales </span>
          <a-row>
            <a-col span="12">
              <div class="section-title">
                <h4>Detalles generales</h4>
              </div>
            </a-col>
          </a-row>
          <div class="row profile" style="padding-bottom: 0 !important">
            <div class="col-md-3" style="padding-left: 0 !important">
              <a-upload
                @preview="handle_preview"
                :file-list="file_list"
                list-type="picture-card"
              >
                <div v-if="file_list.length < 1">
                  <img />
                </div>
              </a-upload>
              <br />
              <a-modal
                :visible="preview_visible"
                :footer="null"
                @cancel="preview_cancel"
              >
                <img style="width: 100%" :src="preview_image" />
              </a-modal>
              <br>
              <strong>Código: </strong>{{ producto.codigProd }}
              <br>
              <span style="font-size: 17px"
                ><strong> Proyecto: </strong>{{ nombre_proy }}</span
              >
            </div>
            <div class="col-md-9">
              <div class="row">
                <div class="col-md-6 profile-info">
                  <span
                    style="
                      font-weight: bold;
                      text-justify: distribute;
                      font-size: 30px;
                    "
                  >
                    {{ producto.tituloProd }}
                  </span>
                  <br /><br />
                  <div class="row">
                    <div class="col-md-6">
                      <span
                        ><strong> Género Musical</strong><br />{{
                          producto.genMusicPro
                        }}
                      </span>
                    </div>
                    <div class="col-md-6">
                      <span
                        ><strong> Año: </strong>{{ producto.añoProd }}
                      </span>
                    </div>
                  </div>
                  <hr />
                  <a-row>
                    <a-col span="12">
                      <h5 style="font-weight: bold !important">Intérpretes</h5>
                      <p
                        v-for="interprete in producto.interpretesProd"
                        :key="interprete"
                      >
                        {{ interprete }}
                      </p>
                    </a-col>
                    <a-col span="12">
                      <h5 style="font-weight: bold !important">Autores</h5>
                      <p v-for="autor in producto.autoresProd" :key="autor">
                        {{ autor }}
                      </p>
                    </a-col>
                  </a-row>
                </div>
                <div
                  class="col-md-6"
                  style="
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                  "
                >
                  <div
                    class="portlet sale-summary"
                    style="margin-bottom: 0 !important"
                  >
                    <div class="portlet-body">
                      <table
                        class="table table-striped table-bordered table-advance table-hover"
                        style="border-color: transparent !important"
                      >
                        <tbody>
                          <tr>
                            <td
                              style="
                                border-right-color: transparent !important;
                                width: 1%;
                              "
                            ></td>
                            <td style="width: 146px !important">
                              <span style="font-weight: bold">
                                Estado de digitalización</span
                              >
                            </td>
                            <td class="hidden-xs" style="text-align: right">
                              {{ producto.estadodigProd }}
                            </td>
                          </tr>
                          <tr>
                            <td
                              style="
                                border-right-color: transparent !important;
                                width: 1%;
                              "
                            ></td>
                            <td style="width: 146px !important">
                              <span style="font-weight: bold"
                                >Código de barra</span
                              >
                            </td>
                            <td class="hidden-xs" style="text-align: right">
                              {{ producto.codigBarProd }}
                            </td>
                          </tr>
                          <tr>
                            <td
                              style="
                                border-right-color: transparent !important;
                                width: 1%;
                              "
                            ></td>
                            <td style="width: 146px !important">
                              <span style="font-weight: bold"
                                >Estatus comercial</span
                              >
                            </td>
                            <td class="hidden-xs" style="text-align: right">
                              {{ producto.statusComProd }}
                            </td>
                          </tr>
                          <tr>
                            <td
                              style="
                                border-right-color: transparent !important;
                                width: 1%;
                              "
                            ></td>
                            <td style="width: 146px !important">
                              <span style="font-weight: bold"
                                >Sello discográfico</span
                              >
                            </td>
                            <td class="hidden-xs" style="text-align: right">
                              {{ producto.sellodiscProd }}
                            </td>
                          </tr>
                          <tr>
                            <td
                              style="
                                border-right-color: transparent !important;
                                width: 1%;
                              "
                            ></td>
                            <td style="width: 146px !important">
                              <span style="font-weight: bold"
                                >Destinos comerciales</span
                              >
                            </td>
                            <td class="hidden-xs" style="text-align: right">
                              {{ producto.destinosComerPro }}
                            </td>
                          </tr>
                          <tr>
                            <td
                              style="
                                border-right-color: transparent !important;
                                width: 1%;
                              "
                            ></td>
                            <td style="width: 146px !important">
                              <span style="font-weight: bold"
                                >Producto principal</span
                              >
                            </td>
                            <td
                              class="hidden-xs"
                              style="
                                text-align: center;
                                font-size: 20px !important;
                                padding: 5px !important;
                              "
                            >
                              <i
                                class="fa fa-check-square-o"
                                v-if="producto.producPrincProd === 1"
                              />
                              <i class="fa fa-square-o" v-else />
                            </td>
                          </tr>
                          <tr>
                            <td
                              style="
                                border-right-color: transparent !important;
                                width: 1%;
                              "
                            ></td>
                            <td style="width: 146px !important">
                              <span style="font-weight: bold"
                                >Catálogo de Bismusic</span
                              >
                            </td>
                            <td
                              class="hidden-xs"
                              style="
                                text-align: center;
                                font-size: 20px !important;
                                padding: 5px !important;
                              "
                            >
                              <i
                                class="fa fa-check-square-o"
                                v-if="producto.activoCatalbisPro === 1"
                              />
                              <i class="fa fa-square-o" v-else />
                            </td>
                          </tr>
                          <tr>
                            <td
                              style="
                                border-right-color: transparent !important;
                                width: 1%;
                              "
                            ></td>
                            <td style="width: 146px !important">
                              <span style="font-weight: bold"
                                >Catálogo digital</span
                              >
                            </td>
                            <td
                              class="hidden-xs"
                              style="
                                text-align: center;
                                font-size: 20px !important;
                                padding: 5px !important;
                              "
                            >
                              <i
                                class="fa fa-check-square-o"
                                v-if="producto.catalDigitalPro === 1"
                              />
                              <i class="fa fa-square-o" v-else />
                            </td>
                          </tr>
                          <tr>
                            <td
                              style="
                                border-right-color: transparent !important;
                                width: 1%;
                              "
                            ></td>
                            <td style="width: 146px !important">
                              <span style="font-weight: bold"
                                >Primera pantalla</span
                              >
                            </td>
                            <td
                              class="hidden-xs"
                              style="
                                text-align: center;
                                font-size: 20px !important;
                                padding: 5px !important;
                              "
                            >
                              <i
                                class="fa fa-check-square-o"
                                v-if="producto.primeraPantProd === 1"
                              />
                              <i class="fa fa-square-o" v-else />
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <p>
                <span style="font-weight: bold">Descripción en Español</span>
              </p>
              <p>{{ producto.descripEspPro }}</p>
            </div>
            <div class="col-md-6">
              <p>
                <span style="font-weight: bold">Descripción en Inglés</span>
              </p>
              <p>{{ producto.descripIngPro }}</p>
            </div>
          </div>
        </a-tab-pane> -->

        <!-- Tab 2 -->
        <a-tab-pane key="1">
          <span slot="tab"> Generales </span>
          <!-- Datos Generales -->
          <a-form-model>
            <a-row>
              <a-col span="11">
                <div class="section-title">
                  <h4>Datos Generales</h4>
                </div>
                <a-row>
                  <a-col span="11">
                    <a-upload
                      @preview="handle_preview"
                      :file-list="file_list"
                      list-type="picture-card"
                    >
                      <div v-if="file_list.length < 1">
                        <img />
                      </div>
                    </a-upload>
                    <br />
                    <a-modal
                      :visible="preview_visible"
                      :footer="null"
                      @cancel="preview_cancel"
                    >
                      <img style="width: 100%" :src="preview_image" />
                    </a-modal>
                    <a-form-model-item
                      style="width: 55% !important"
                      label="Año"
                    >
                      <a-mentions readonly :placeholder="producto.añoProd">
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item style="margin-top: 20px">
                      <i
                        class="fa fa-check-square-o hidden-xs"
                        v-if="producto.producPrincProd === 1"
                      />
                      <i class="fa fa-square-o" v-else />
                      ¿Producto principal del proyecto?
                    </a-form-model-item>
                  </a-col>
                  <a-col span="11" style="float: right">
                    <a-form-model-item label="Código">
                      <a-mentions readonly :placeholder="producto.codigProd">
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item label="Título">
                      <a-mentions readonly :placeholder="producto.tituloProd">
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item label="Género musical">
                      <a-mentions readonly :placeholder="producto.genMusicPro">
                      </a-mentions>
                    </a-form-model-item>
                  </a-col>
                </a-row>
                <div class="section-title">
                  <h4>Datos Gestión</h4>
                </div>
                <a-row style="margin-bottom: 30px">
                  <a-col span="12">
                    <a-form-model-item label="Estado de Digitalización">
                      <a-mentions
                        readonly
                        :placeholder="producto.estadodigProd"
                      >
                      </a-mentions>
                    </a-form-model-item>
                  </a-col>
                </a-row>
                <div class="section-title">
                  <h4>Datos Descripciones</h4>
                </div>
                <a-row>
                  <a-form-model-item
                    label="Descripción en español del producto"
                  >
                    <a-mentions
                      style="height: 100px !important"
                      readonly
                      :placeholder="producto.descripEspPro"
                    >
                    </a-mentions>
                  </a-form-model-item>
                  <a-form-model-item label="Descripción en inglés del producto">
                    <a-mentions
                      style="height: 100px !important"
                      readonly
                      :placeholder="producto.descripIngPro"
                    >
                    </a-mentions>
                  </a-form-model-item>
                </a-row>
              </a-col>
              <a-col span="11" style="float: right">
                <div class="section-title">
                  <h4>Datos Comerciales</h4>
                </div>
                <a-row style="margin-bottom: 30px">
                  <a-col span="11">
                    <a-form-model-item label="Código de barra">
                      <a-mentions readonly :placeholder="producto.codigBarProd">
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item label="Estatus comercial">
                      <a-mentions
                        readonly
                        :placeholder="producto.statusComProd"
                      >
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item style="margin-top: 20px">
                      <i
                        class="fa fa-check-square-o hidden-xs"
                        v-if="producto.activoCatalbisPro === 1"
                      />
                      <i class="fa fa-square-o" v-else />
                      ¿Activo en el Catálogo de Bismusic?
                    </a-form-model-item>
                    <a-form-model-item>
                     <i
                        class="fa fa-check-square-o hidden-xs"
                        v-if="producto.primeraPantProd === 1"
                      />
                      <i class="fa fa-square-o" v-else />
                      ¿Estará en la Primera Pantalla?
                    </a-form-model-item>
                  </a-col>
                  <a-col span="11" style="float: right">
                    <a-form-model-item label="Destinos comerciales">
                      <a-mentions
                        readonly
                        :placeholder="producto.destinosComerPro"
                      >
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item label="Sello discográfico">
                      <a-mentions
                        readonly
                        :placeholder="producto.sellodiscProd"
                      >
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item style="margin-top: 20px">
                      <i
                        class="fa fa-check-square-o hidden-xs"
                        v-if="producto.catalDigitalPro === 1"
                      />
                      <i class="fa fa-square-o" v-else />
                      ¿Estará en el Catálogo Digital?
                    </a-form-model-item>
                  </a-col>
                </a-row>
                <div class="section-title">
                  <h4>Gestión de Intérpretes</h4>
                </div>
                <a-form-model-item>
                  <i
                        class="fa fa-check-square-o hidden-xs"
                        v-if="producto.variosInterpretesProd === 1"
                      />
                      <i class="fa fa-square-o" v-else />
                      ¿El producto tiene varios intérpretes?
                </a-form-model-item>
                <a-form-model-item>
                  <a-mentions
                    v-for="interprete in producto.interpretesProd"
                    :key="interprete"
                    readonly
                    :placeholder="interprete"
                  >
                  </a-mentions>
                </a-form-model-item>
                <div class="section-title" style="margin-top: 30px">
                  <h4>Gestión de Autores</h4>
                </div>
                <a-form-model-item>
                  <a-mentions
                    v-for="autor in producto.autoresProd"
                    :key="autor"
                    readonly
                    :placeholder="autor"
                  >
                  </a-mentions>
                </a-form-model-item>
              </a-col>
            </a-row>
          </a-form-model>
        </a-tab-pane>
        <a-tab-pane key="3">
          <span slot="tab"> Multimedias </span>
          <h4>Multimedias</h4>
        </a-tab-pane>
        <!-- Tab 5 -->
        <a-tab-pane key="4">
          <span slot="tab"> Elementos </span>
          <h4>Elementos</h4>
        </a-tab-pane>
      </a-tabs>
    </a-modal>
  </div>
</template>

<script>
import Vue from "vue";
import axios from "../../../config/axios/axios";
import {
  GridPlugin,
  Filter,
  Page,
  Sort,
  Toolbar,
  Reorder,
} from "@syncfusion/ej2-vue-grids";
import { L10n, setCulture } from "@syncfusion/ej2-base";
setCulture("es-ES");
L10n.load({
  "es-ES": {
    grid: {
      EmptyRecord: "No existen datos para mostrar",
      InvalidFilterMessage: "Datos de filtrado errados",
      NoResult: "Sin resultados",
      Search: "Buscar",
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
  },
});
Vue.use(GridPlugin);
export default {
  name: "Modal_Detalles_Producto",
  props: ["visible", "producto_prop"],
  data() {
    return {
      file_list: [
        {
          uid: this.producto_prop.id,
          name: this.producto_prop.identificadorProd.split("/")[
            this.producto_prop.identificadorProd.split("/").length - 1
          ],
          url: this.producto_prop.identificadorProd,
        },
      ],
      preview_image: "",
      preview_visible: false,
      nombre_proy: "",
      producto: {
        proyecto: {},
      },
      show: true,
      tabActivo: "1",
      tab_2: true,
      listaTabs: [],
      products_list: [],
      toolbar: ["Search"],
      pageSettings: {
        pageSizes: [5, 10, 20, 30],
        pageCount: 5,
        pageSize: 10,
      },
    };
  },
  created() {
    axios
      .post("proyectos/listar", {
        valorbuscado: this.producto_prop.proyecto_id,
        atributo: "id",
      })
      .then((response) => {
        this.nombre_proy = response.data[0].nombreProy;
      });
    this.loadProducts();
  },
  mounted() {
    this.producto = { ...this.producto_prop };
    if (this.producto.interpretesProd !== null) {
      let list_help = this.producto.interpretesProd.split(".");
      this.producto.interpretesProd = list_help[0];
      this.producto.interpretesProd = this.producto.interpretesProd.split(" ");
      this.producto.interpretesProd.pop();
    }
    if (this.producto.autoresProd !== null) {
      this.producto.autoresProd = this.producto.autoresProd.split(" ");
    }
  },
  methods: {
    preview_cancel() {
      this.preview_visible = false;
    },
    handle_preview(file) {
      this.preview_image = file.url || file.thumbUrl;
      this.preview_visible = true;
    },
    loadProducts() {
      axios.get("/productos").then((res) => {
        this.products_list = res.data;
      });
    },
    siguiente(tab, siguienteTab) {
      this.tabActivo = siguienteTab;
    },
    atras(tabAnterior) {
      this.tabActivo = tabAnterior;
    },
    handleCancel(e) {
      this.listaTabs = [];
      this.tabActivo = "1";
      this.show = false;
      this.$emit("close_modal", this.show);
    },
  },
  computed: {},
  provide: {
    grid: [Filter, Page, Sort, Reorder, Toolbar],
  },
};
</script>

<style>
.profile {
  background-color: transparent !important;
}
#modal_detalles_productos .ant-tabs-nav {
  float: right !important;
}
#modal_detalles_productos .ant-tabs-extra-content {
  float: left !important;
  color: rgba(0, 0, 0, 0.75);
  font-weight: 500;
  font-size: 20px;
}
#modal_detalles_productos .ant-divider-horizontal:before,
#modal_detalles_productos .ant-divider-horizontal:after {
  border-color: rgb(145, 140, 140) !important;
}
#modal_detalles_productos .e-unboundcelldiv .e-control {
  background: transparent !important;
  border: none !important;
}
#modal_detalles_productos .e-headercontent,
#modal_detalles_productos .e-sortfilter,
#modal_detalles_productos thead,
#modal_detalles_productos tr,
#modal_detalles_productos td,
#modal_detalles_productos th,
#modal_detalles_productos .e-content,
#modal_detalles_productos .e-table,
#modal_detalles_productos .e-pagercontainer,
#modal_detalles_productos .e-toolbar-items,
#modal_detalles_productos .e-pagerdropdown,
#modal_detalles_productos .e-input-group,
#modal_detalles_productos .e-first,
#modal_detalles_productos .e-prev,
#modal_detalles_productos .e-numericcontainer,
#modal_detalles_productos .e-next,
#modal_detalles_productos .e-last,
#modal_detalles_productos .e-tbar-btn,
#modal_detalles_productos .e-toolbar-item {
  background-color: transparent !important;
}
#modal_detalles_productos .e-gridheader,
#modal_detalles_productos .e-gridpager,
#modal_detalles_productos .e-gridcontent,
#modal_detalles_productos .e-toolbar {
  background-color: transparent !important;
}
#modal_detalles_productos .e-gridheader {
  border-bottom-color: darkgrey !important;
  border-top-color: darkgrey !important;
}
#modal_detalles_productos .e-grid td {
  border-color: darkgrey !important;
}
#modal_detalles_productos .e-pager,
#modal_detalles_productos .e-toolbar {
  border-color: darkgrey !important;
}
#modal_detalles_productos .e-grid,
#modal_detalles_productos .e-grid .e-headercontent,
#modal_detalles_productos .e-grid .e-headercell,
#modal_detalles_productos .e-search {
  border-color: darkgrey !important;
}
#modal_detalles_productos thead span,
#modal_detalles_productos .e-icon-filter {
  color: #333 !important;
  font-weight: normal !important;
}
#modal_detalles_productos
  .ant-upload-list-picture-card
  .ant-upload-list-item-actions
  .anticon-delete {
  display: none !important;
}
.profile .table-bordered,
.profile .table-bordered td,
.profile .table-bordered th {
  border-color: #e5eff6 !important;
}
</style>
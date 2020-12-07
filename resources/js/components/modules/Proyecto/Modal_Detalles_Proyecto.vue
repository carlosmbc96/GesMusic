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
      id="modal_detalles_proyecto"
    >
      <template slot="footer">
        <a-button key="back" type="primary" @click="handleCancel()">
          Aceptar
        </a-button>
      </template>
      <!-- Tabs -->
      <a-tabs default-active-key="1">
        <div slot="tabBarExtraContent">Detalles Proyecto</div>
        <a-tab-pane key="1">
          <span slot="tab"> Generales </span>
          <a-row>
            <a-col span="12">
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
            </a-col>
            <a-col span="12" style="margin-left: -15%">
              <h1 style="color: rgba(0, 0, 0, 0.85)">
                {{ proyecto.nombreProy }}
              </h1>
              <h5 style="font-weight: bold">Código:</h5>
              {{ proyecto.codigProy }} <br /><br />
              <a-row>
                <a-col span="6">
                  <br />
                  <h5 style="font-weight: bold">Descripción en español:</h5>
                  <p>{{ proyecto.descripEsp }}</p>
                </a-col>
                <a-col span="6" style="margin-left: 30%">
                  <br />
                  <h5 style="font-weight: bold">Descripción en inglés:</h5>
                  <p>{{ proyecto.descripIng }}</p>
                </a-col>
              </a-row>
            </a-col>
          </a-row>
          <br />
          <br />
          <br />
          <a-col span="12">
            <div class="section-title">
              <h4>Productos del proyecto</h4>
            </div>
          </a-col>

          <br />
          <tabla_productos
            @reload="reload_parent"
            :proyecto="proyecto"
            :vista_detalles="vista_detalles"
            @close_modal="show = $event"
          />
        </a-tab-pane>
      </a-tabs>
    </a-modal>
  </div>
</template>

<script>
import Vue from "vue";
import axios from "../../../config/axios/axios";
import tabla_productos from "../Producto/Tabla_Productos";
export default {
  name: "Modal_detail_project",
  props: ["visible", "proyecto_prop"],
  data() {
    return {
      file_list: [
        {
          uid: this.proyecto_prop.id,
          name: this.proyecto_prop.identificadorProy.split("/")[
            this.proyecto_prop.identificadorProy.split("/").length - 1
          ],
          url: this.proyecto_prop.identificadorProy,
        },
      ],
      preview_visible: false,
      preview_image: "",
      vista_detalles: true,
      proyecto: {},
      show: true,
      tabActivo: "1",
      tab_2: true,
      listaTabs: [],
    };
  },
  mounted() {
    this.proyecto = this.proyecto_prop;
  },
  methods: {
    preview_cancel() {
      this.preview_visible = false;
    },
    handle_preview(file) {
      this.preview_image = file.url || file.thumbUrl;
      this.preview_visible = true;
    },
    reload_parent() {
      this.$emit("refresh");
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
  components: {
    tabla_productos,
  },
};
</script>

<style>
.profile {
  background-color: transparent !important;
}

#modal_detalles_proyecto .ant-tabs-nav {
  float: right !important;
}

#modal_detalles_proyecto .ant-tabs-extra-content {
  float: left !important;
  color: rgba(0, 0, 0, 0.75);
  font-weight: 500;
  font-size: 20px;
}

#modal_detalles_proyecto .ant-divider-horizontal:before,
#modal_detalles_proyecto .ant-divider-horizontal:after {
  border-color: rgb(145, 140, 140) !important;
}

#modal_detalles_proyecto .e-unboundcelldiv .e-control {
  background: transparent !important;
  border: none !important;
}

#modal_detalles_proyecto .e-headercontent,
#modal_detalles_proyecto .e-sortfilter,
#modal_detalles_proyecto thead,
#modal_detalles_proyecto tr,
#modal_detalles_proyecto td,
#modal_detalles_proyecto th,
#modal_detalles_proyecto .e-content,
#modal_detalles_proyecto .e-table,
#modal_detalles_proyecto .e-pagercontainer,
#modal_detalles_proyecto .e-toolbar-items,
#modal_detalles_proyecto .e-pagerdropdown,
#modal_detalles_proyecto .e-input-group,
#modal_detalles_proyecto .e-first,
#modal_detalles_proyecto .e-prev,
#modal_detalles_proyecto .e-numericcontainer,
#modal_detalles_proyecto .e-next,
#modal_detalles_proyecto .e-last,
#modal_detalles_proyecto .e-tbar-btn,
#modal_detalles_proyecto .e-toolbar-item {
  background-color: transparent !important;
}

#modal_detalles_proyecto .e-gridheader,
#modal_detalles_proyecto .e-gridpager,
#modal_detalles_proyecto .e-gridcontent,
#modal_detalles_proyecto .e-toolbar {
  background-color: transparent !important;
}
#modal_detalles_proyecto
  .ant-upload-list-picture-card
  .ant-upload-list-item-actions
  .anticon-delete {
  display: none !important;
}
</style>

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
      id="modal_detalles_authors"
    >
      <template slot="footer">
        <a-button key="back" type="primary" @click="handleCancel()">
          Aceptar</a-button
        >
      </template>
      <a-tabs default-active-key="1">
        <div slot="tabBarExtraContent">Detalles Autor</div>
        <!-- Tab 1 -->
        <a-tab-pane key="1">
          <span slot="tab"> Generales </span>
          <!-- Datos Generales -->
          <a-form-model>
            <a-row>
              <a-col span="6">
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
              <a-col span="6">
                <a-form-model-item label="Código">
                  <a-mentions readonly :placeholder="author.codigAutr">
                  </a-mentions>
                </a-form-model-item>
                <a-form-model-item label="Carnet de identidad(CI)">
                  <a-mentions readonly :placeholder="author.ciAutr">
                  </a-mentions>
                </a-form-model-item>
              </a-col>
              <a-col span="6">
                <div id="nombresAutr">
                  <a-form-model-item label="Nombre">
                    <a-mentions readonly :placeholder="author.nombresAutr">
                    </a-mentions>
                  </a-form-model-item>
                </div>
                <a-form-model-item label="Sexo">
                  <a-mentions readonly :placeholder="author.sexoAutr">
                  </a-mentions>
                </a-form-model-item>
              </a-col>
              <a-col span="6">
                <div id="apellidosAutr">
                  <a-form-model-item label="Apellidos">
                    <a-mentions readonly :placeholder="author.apellidosAutr">
                    </a-mentions>
                  </a-form-model-item>
                </div>
              </a-col>
            </a-row>
            <a-row>
              <a-col span="11">
                <div id="resenha">
                  <a-form-model-item label="Reseña biográfica del autor">
                    <div id="esp">
                      <a-mentions readonly :placeholder="author.reseñaBiogAutr">
                      </a-mentions>
                    </div>
                  </a-form-model-item>
                </div>
              </a-col>
              <a-col span="1"></a-col>
              <a-col span="11">
                <div id="checkbox" style="margin-top: 50px">
                  <a-form-model-item>
                    <i
                      class="fa fa-check-square-o hidden-xs"
                      v-if="author.fallecidoAutr === 1"
                    />
                    <i class="fa fa-square-o" v-else />
                    ¿El Autor es Fallecido?
                  </a-form-model-item>
                </div>

                <a-form-model-item>
                  <i
                    class="fa fa-check-square-o hidden-xs"
                    v-if="author.obrasCatEditAutr === 1"
                  />
                  <i class="fa fa-square-o" v-else />
                  ¿El Autor tiene Obras en el Catalgo Editorial de Bismusic?
                </a-form-model-item>
              </a-col>
            </a-row>
          </a-form-model>
        </a-tab-pane>
      </a-tabs>
    </a-modal>
  </div>
</template>

<script>
import Vue from "vue";
export default {
  name: "Modal_Detalles_Autor",
  props: ["visible", "author_prop"],
  data() {
    return {
      file_list: [
        {
          uid: this.author_prop.id,
          name: this.author_prop.fotoAutr.split("/")[
            this.author_prop.fotoAutr.split("/").length - 1
          ],
          url: this.author_prop.fotoAutr,
        },
      ],
      preview_image: "",
      preview_visible: false,
      author: {},
      show: true,
    };
  },
  mounted() {
    this.author = { ...this.author_prop };
  },
  methods: {
    preview_cancel() {
      this.preview_visible = false;
    },
    handle_preview(file) {
      this.preview_image = file.url || file.thumbUrl;
      this.preview_visible = true;
    },
    handleCancel(e) {
      this.show = false;
      this.$emit("close_modal", this.show);
    },
  },
};
</script>

<style>
.profile {
  background-color: transparent !important;
}
#modal_detalles_authors .ant-tabs-nav {
  float: right !important;
}
#modal_detalles_authors .ant-mentions {
  width: 85% !important;
}
#modal_detalles_authors .ant-tabs-extra-content {
  float: left !important;
  color: rgba(0, 0, 0, 0.75);
  font-weight: 500;
  font-size: 20px;
}
#modal_detalles_authors .ant-divider-horizontal:before,
#modal_detalles_authors .ant-divider-horizontal:after {
  border-color: rgb(145, 140, 140) !important;
}
#modal_detalles_authors .e-unboundcelldiv .e-control {
  background: transparent !important;
  border: none !important;
}
#modal_detalles_authors .e-headercontent,
#modal_detalles_authors .e-sortfilter,
#modal_detalles_authors thead,
#modal_detalles_authors tr,
#modal_detalles_authors td,
#modal_detalles_authors th,
#modal_detalles_authors .e-content,
#modal_detalles_authors .e-table,
#modal_detalles_authors .e-pagercontainer,
#modal_detalles_authors .e-toolbar-items,
#modal_detalles_authors .e-pagerdropdown,
#modal_detalles_authors .e-input-group,
#modal_detalles_authors .e-first,
#modal_detalles_authors .e-prev,
#modal_detalles_authors .e-numericcontainer,
#modal_detalles_authors .e-next,
#modal_detalles_authors .e-last,
#modal_detalles_authors .e-tbar-btn,
#modal_detalles_authors .e-toolbar-item {
  background-color: transparent !important;
}
#esp textarea {
  width: 100% !important;
  height: 150px !important;
}
#modal_detalles_authors #resenha .ant-mentions {
  width: 100% !important;
}
#modal_detalles_authors .e-gridheader,
#modal_detalles_authors .e-gridpager,
#modal_detalles_authors .e-gridcontent,
#modal_detalles_authors .e-toolbar {
  background-color: transparent !important;
}
#modal_detalles_authors .e-gridheader {
  border-bottom-color: darkgrey !important;
  border-top-color: darkgrey !important;
}
#modal_detalles_authors .e-grid td {
  border-color: darkgrey !important;
}
#modal_detalles_authors .e-pager,
#modal_detalles_authors .e-toolbar {
  border-color: darkgrey !important;
}
#modal_detalles_authors .e-grid,
#modal_detalles_authors .e-grid .e-headercontent,
#modal_detalles_authors .e-grid .e-headercell,
#modal_detalles_authors .e-search {
  border-color: darkgrey !important;
}
#modal_detalles_authors thead span,
#modal_detalles_authors .e-icon-filter {
  color: #333 !important;
  font-weight: normal !important;
}
#modal_detalles_authors
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
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
      id="modal_detalles_interp"
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
          <a-form-model
            >
              <a-row>
                <a-col span="12">
                  <a-form-model-item
                    label="Código"
                  >
                    <a-mentions readonly :placeholder="interp.codigInterp">
                  </a-mentions>
                  </a-form-model-item>
                  <a-form-model-item
                    label="Nombre"
                  >
                    <a-mentions readonly :placeholder="interp.nombreInterp">
                  </a-mentions>
                  </a-form-model-item>
                  <a-form-model-item
                    label="Reseña biográfica del Interprete"
                  >
                  <div id="esp">
                    <a-mentions readonly :placeholder="interp.reseñaBiogInterp">
                  </a-mentions>
                  </div>
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
  name: "Modal_Detalles_Interprete",
  props: ["visible", "interp_prop"],
  data() {
    return {
      interp: {},
      show: true,
    };
  },
  mounted() {
    this.interp = { ...this.interp_prop };
  },
  methods: {
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
#modal_detalles_interp .ant-tabs-nav {
  float: right !important;
}
#modal_detalles_interp .ant-mentions {
  width: 85% !important;
}
#modal_detalles_interp .ant-tabs-extra-content {
  float: left !important;
  color: rgba(0, 0, 0, 0.75);
  font-weight: 500;
  font-size: 20px;
}
#modal_detalles_interp .ant-divider-horizontal:before,
#modal_detalles_interp .ant-divider-horizontal:after {
  border-color: rgb(145, 140, 140) !important;
}
#modal_detalles_interp .e-unboundcelldiv .e-control {
  background: transparent !important;
  border: none !important;
}
#modal_detalles_interp .e-headercontent,
#modal_detalles_interp .e-sortfilter,
#modal_detalles_interp thead,
#modal_detalles_interp tr,
#modal_detalles_interp td,
#modal_detalles_interp th,
#modal_detalles_interp .e-content,
#modal_detalles_interp .e-table,
#modal_detalles_interp .e-pagercontainer,
#modal_detalles_interp .e-toolbar-items,
#modal_detalles_interp .e-pagerdropdown,
#modal_detalles_interp .e-input-group,
#modal_detalles_interp .e-first,
#modal_detalles_interp .e-prev,
#modal_detalles_interp .e-numericcontainer,
#modal_detalles_interp .e-next,
#modal_detalles_interp .e-last,
#modal_detalles_interp .e-tbar-btn,
#modal_detalles_interp .e-toolbar-item {
  background-color: transparent !important;
}
#esp textarea {
  width: 100% !important;
  height: 150px !important;
}
#modal_detalles_interp #resenha .ant-mentions {
  width: 100% !important;
}
#modal_detalles_interp .e-gridheader,
#modal_detalles_interp .e-gridpager,
#modal_detalles_interp .e-gridcontent,
#modal_detalles_interp .e-toolbar {
  background-color: transparent !important;
}
#modal_detalles_interp .e-gridheader {
  border-bottom-color: darkgrey !important;
  border-top-color: darkgrey !important;
}
#modal_detalles_interp .e-grid td {
  border-color: darkgrey !important;
}
#modal_detalles_interp .e-pager,
#modal_detalles_interp .e-toolbar {
  border-color: darkgrey !important;
}
#modal_detalles_interp .e-grid,
#modal_detalles_interp .e-grid .e-headercontent,
#modal_detalles_interp .e-grid .e-headercell,
#modal_detalles_interp .e-search {
  border-color: darkgrey !important;
}
#modal_detalles_interps thead span,
#modal_detalles_interp .e-icon-filter {
  color: #333 !important;
  font-weight: normal !important;
}
#modal_detalles_interp
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
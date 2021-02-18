<template>
  <div>
    <a-modal :closable="false" width="60%" :visible="show" id="transfer_modal_interpretes">
      <template slot="footer">
        <a-popconfirm
          :getPopupContainer="(trigger) => trigger.parentNode"
          placement="left"
          @confirm="handle_cancel('cancelar')"
          ok-text="Si"
          cancel-text="No"
          title="Desea cancelar la gestión de Relaciones?"
        >
          <a-icon
            slot="icon"
            type="exclamation-circle"
            theme="filled"
            style="color: #f0b727"
          />
          <a-button type="danger" key="back"> Cancelar </a-button>
        </a-popconfirm>
        <a-popconfirm
          :getPopupContainer="(trigger) => trigger.parentNode"
          placement="topRight"
          @confirm="submit()"
          ok-text="Si"
          cancel-text="No"
          title="Desea guardar estos cambios?"
        >
          <a-icon
            slot="icon"
            theme="filled"
            type="check-circle"
            style="color: #4cc4b1"
          />
          <a-button v-if="!visible_btn" type="primary"> Guardar </a-button>
        </a-popconfirm>
      </template>
      <a-row>
        <a-col span="12">
          <div class="section-title">
            <h4>Gestión de Relaciones</h4>
          </div>
        </a-col>
      </a-row>
      <a-spin :spinning="spinning">
        <a-transfer
          :locale="config"
          :titles="['Todos', 'Relacionados']"
          :data-source="mockData"
          :target-keys="new_relations"
          :disabled="disabled"
          :show-search="true"
          :filter-option="
            (inputValue, item) => item.title.indexOf(inputValue) !== -1
          "
          :show-select-all="false"
          @change="onChange"
        >
          <template
            slot="children"
            slot-scope="{
              props: {
                direction,
                filteredItems,
                selectedKeys,
                disabled: listDisabled,
              },
              on: { itemSelectAll, itemSelect },
            }"
          >
            <a-table
              :row-selection="
                getRowSelection({
                  disabled: listDisabled,
                  selectedKeys,
                  itemSelectAll,
                  itemSelect,
                })
              "
              :columns="direction === 'left' ? leftColumns : rightColumns"
              :data-source="filteredItems"
              size="small"
              :style="{ pointerEvents: listDisabled ? 'none' : null }"
              :custom-row="
                ({ key, disabled: itemDisabled }) => ({
                  on: {
                    click: () => {
                      if (itemDisabled || listDisabled) return;
                      itemSelect(key, !selectedKeys.includes(key));
                    },
                  },
                })
              "
            />
          </template>
        </a-transfer>
      </a-spin>
    </a-modal>
  </div>
</template>

<script>
import difference from "lodash/difference";

export default {
  props: ["entity_id", "entity_relation"],
  data() {
    return {
      config: {
        itemUnit: "autores",
        itemsUnit: "autores",
        notFoundContent: "La lista está vacía",
        searchPlaceholder: "Buscar",
      },
      mockData: [],
      new_relations_copy: [],
      old_relations: [],
      new_relations: [],
      disabled: false,
      spinning: false,
      leftColumns: [],
      rightColumns: [],
      show: true,
    };
  },
  created() {
    this.change_spin();
    axios
      .post("/interpretes/listar", { relations: [this.entity_relation] })
      .then((response) => {
        for (let i = 0; i < response.data.length; i++) {
          this.mockData.push({
            key: response.data[i].id.toString(),
            codig: response.data[i].codigInterp,
            title: response.data[i].nombreInterp,
          });
          if (this.entity_relation === "audiovisuales") {
            for (let j = 0; j < response.data[i].audiovisuales.length; j++) {
              if (response.data[i].audiovisuales[j].id === this.entity_id) {
                this.old_relations.push(response.data[i].id.toString());
              }
            }
          } else if (this.entity_relation === "tracks") {
            for (let j = 0; j < response.data[i].tracks.length; j++) {
              if (response.data[i].tracks[j].id === this.entity_id) {
                this.old_relations.push(response.data[i].id.toString());
              }
            }
          }
        }
        this.new_relations = this.old_relations;
        this.new_relations_copy = this.new_relations;
        this.change_spin();
      });
    this.leftColumns = [
      {
        dataIndex: "codig",
        title: "Código",
      },
      {
        dataIndex: "title",
        title: "Nombre",
      },
    ];
    this.rightColumns = [
      {
        dataIndex: "codig",
        title: "Código",
      },
      {
        dataIndex: "title",
        title: "Nombre",
      },
    ];
  },
  computed: {
    visible_btn() {
      return this.compareArrays(this.new_relations_copy, this.new_relations);
    },
  },
  methods: {
    compareArrays(old_array, new_array) {
      let igual_cont = 0;
      if (new_array.length === old_array.length) {
        for (let i = 0; i < old_array.length; i++) {
          if (old_array[i] === new_array[i]) {
            igual_cont++;
          }
        }
        if (igual_cont !== new_array.length) {
          return false;
        } else return true;
      } else return false;
    },
    submit() {
      this.change_spin();
      axios
        .post("interpretes/actualizarRelacionesInterp", {
          relation: this.entity_relation,
          id: this.entity_id,
          interpretes: this.new_relations,
        })
        .then((response) => {
          this.$emit("actualizar");
          this.change_spin();
          this.handle_cancel();
          this.$toast.success(
            `Las Relaciones se actualizaron correctamente`,
            "¡Éxito!",
            {
              timeout: 2000,
            }
          );
        })
        .catch((error) => {
          this.$toast.error("Ha ocurrido un error", "¡Error!", {
            timeout: 2000,
          });
        });
    },
    /*
     * Método que activa y desactiva el spin
     */
    change_spin() {
      this.spinning = !this.spinning;
    },
    handle_cancel(e) {
      this.show = false;
      this.$emit("close_modal", this.show);
    },
    onChange(nextTargetKeys) {
      this.new_relations = nextTargetKeys;
    },

    triggerDisable(disabled) {
      this.disabled = disabled;
    },
    getRowSelection({ disabled, selectedKeys, itemSelectAll, itemSelect }) {
      return {
        getCheckboxProps: (item) => ({
          props: { disabled: disabled || item.disabled },
        }),
        onSelectAll(selected, selectedRows) {
          const treeSelectedKeys = selectedRows
            .filter((item) => !item.disabled)
            .map(({ key }) => key);
          const diffKeys = selected
            ? difference(treeSelectedKeys, selectedKeys)
            : difference(selectedKeys, treeSelectedKeys);
          itemSelectAll(diffKeys, selected);
        },
        onSelect({ key }, selected) {
          itemSelect(key, selected);
        },
        selectedRowKeys: selectedKeys,
      };
    },
  },
};
</script>

<style>
#transfer_modal_interpretes .ant-modal {
  margin-left: auto !important;
  margin-top: -31px !important;
}
#transfer_modal_interpretes .ant-modal-content {
  background-color: rgba(255, 255, 255, 0.95) !important;
}
#transfer_modal_interpretes .ant-btn-primary-disabled,
.ant-btn-primary.disabled,
.ant-btn-primary[disabled],
.ant-btn-primary-disabled:hover,
.ant-btn-primary.disabled:hover,
.ant-btn-primary[disabled]:hover,
.ant-btn-primary-disabled:focus,
.ant-btn-primary.disabled:focus,
.ant-btn-primary[disabled]:focus,
.ant-btn-primary-disabled:active,
.ant-btn-primary.disabled:active,
.ant-btn-primary[disabled]:active,
.ant-btn-primary-disabled.active,
.ant-btn-primary.disabled.active,
.ant-btn-primary[disabled].active {
  color: rgba(0, 0, 0, 0.25) !important;
  background-color: #f5f5f5 !important;
  border-color: #d9d9d9 !important;
  text-shadow: none !important;
  box-shadow: none !important;
}
</style>

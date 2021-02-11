<template>
  <div>
    <a-modal :closable="false" width="50%" :visible="show" id="transfer_modal">
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
          title="Desea guardar estos cambios"
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
import axios from "axios";
import moment from "../../../../../node_modules/moment";

const leftTableColumns = [
  {
    dataIndex: "duracion",
    title: "Duración",
  },
  {
    dataIndex: "title",
    title: "Título",
  },
];
const rightTableColumns = [
  {
    dataIndex: "duracion",
    title: "Duración",
  },
  {
    dataIndex: "title",
    title: "Título",
  },
];

export default {
  props: ["entity_id"],
  data() {
    return {
      config: {
        itemUnit: "track",
        itemsUnit: "tracks",
        notFoundContent: "La lista está vacía",
        searchPlaceholder: "Buscar",
      },
      mockData: [],
      new_relations_copy: [],
      old_relations: [],
      new_relations: [],
      disabled: false,
      spinning: false,
      leftColumns: leftTableColumns,
      rightColumns: rightTableColumns,
      show: true,
    };
  },
  created() {
    this.change_spin();
    axios
      .post("/tracks/listar", { relations: ["fonogramas"] })
      .then((response) => {
        for (let i = 0; i < response.data.length; i++) {
          this.mockData.push({
            key: response.data[i].id.toString(),
            duracion: response.data[i].duracionTrk,
            title: response.data[i].tituloTrk,
          });
          for (let j = 0; j < response.data[i].fonogramas.length; j++) {
            if (response.data[i].fonogramas[j].id === this.entity_id) {
              this.old_relations.push(response.data[i].id.toString());
            }
          }
        }
        this.new_relations = this.old_relations;
        this.new_relations_copy = this.new_relations;
        this.change_spin();
      });
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
			this.edit_duration_fong(this.entity_id, this.new_relations);
      axios
        .post("fonogramas/actualizarRelacionesTrk", {
          id: this.entity_id,
          tracks: this.new_relations,
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
    edit_duration_fong(fonograma, tracks) {
      let durationFong = moment("00:00:00", "HH:mm:ss");
      let form_data = new FormData();
      for (let i = 0; i < tracks.length; i++) {
        durationFong.add(moment.duration(tracks[i].duracionTrk)).format("HH:mm:ss");
        durationFong = moment(durationFong, "HH:mm:ss");
			}
      form_data.append("id", fonograma);
      form_data.append("duracionFong", durationFong.format("HH:mm:ss"));
      axios
        .post(`/fonogramas/editarDuracion`, form_data, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((response) => {})
        .catch((error) => {
          this.$toast.error("Ha ocurrido un error", "¡Error!", {
            timeout: 2000,
          });
        });
    },
  },
};
</script>

<style>
#transfer_modal .ant-modal {
  margin-left: auto !important;
  margin-top: -31px !important;
}
#transfer_modal .ant-modal-content {
  background-color: rgba(255, 255, 255, 0.95) !important;
}
#transfer_modal .ant-btn-primary-disabled,
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

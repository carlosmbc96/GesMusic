<template>
  <div>
    <a-modal
      :closable="false"
      width="80.4%"
      okText="Guardar"
      cancelText="Cancelar"
      cancelType="danger"
      :visible="show"
      id="modal_gestionar_artisticos"
    >
      <template slot="footer">
        <a-popconfirm
          :getPopupContainer="(trigger) => trigger.parentNode"
          placement="left"
          @confirm="handle_cancel('cancelar')"
          ok-text="Si"
          cancel-text="No"
          :title="action_cancel_title"
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
          @confirm="validate()"
          ok-text="Si"
          cancel-text="No"
          :title="action_title"
        >
          <a-icon
            slot="icon"
            theme="filled"
            type="check-circle"
            style="color: #4cc4b1"
          />
          <a-button
            :disabled="disabled"
            :loading="waiting"
            v-if="active"
            type="primary"
          >
            {{ text_button }}
          </a-button>
        </a-popconfirm>
      </template>
      <!-- Aqui comienzan los tabs -->
      <a-tabs>
        <div slot="tabBarExtraContent">
          {{ text_header_button }} Nombre Artístico
        </div>
        <a-tab-pane key="1">
          <span slot="tab">Generales</span>
          <a-row>
            <a-col span="12">
              <div class="section-title">
                <h4>Datos Generales</h4>
              </div>
            </a-col>
          </a-row>
          <a-spin :spinning="spinning">
            <a-form-model
              ref="general_form"
              layout="horizontal"
              :model="artisticos_modal"
              :rules="rules"
            >
              <a-row>
                <a-col span="12">
                  <a-form-model-item
                    v-if="action_modal !== 'editar'"
                    :validate-status="show_error"
                    prop="codigArts"
                    has-feedback
                    label="Código"
                    :help="show_used_error"
                  >
                    <a-input
                      addon-before="ARTS-"
                      placeholder="0001"
                      :disabled="action_modal === 'editar'"
                      v-model="artisticos_modal.codigArts"
                    />
                  </a-form-model-item>
                  <a-form-model-item v-else label="Código">
                    <a-input
                      addon-before="ARTS-"
                      placeholder="0001"
                      :disabled="action_modal === 'editar'"
                      v-model="artisticos_modal.codigArts"
                    />
                  </a-form-model-item>
                  <a-form-model-item
                    has-feedback
                    label="Nombre Artístico"
                    prop="NombreArts"
                  >
                    <a-input
                      :disabled="disabled"
                      v-model="artisticos_modal.NombreArts"
                    />
                  </a-form-model-item>
                  <a-form-model-item>
                    <a-checkbox
                      :disabled="disabled"
                      v-model="actualNombreArts"
                      :value="actualNombreArts"
                    >
                      ¿Es el actual Nombre Artístico?
                    </a-checkbox>
                  </a-form-model-item>
                </a-col>
                <a-col span="12">
                  <a-form-model-item
                    has-feedback
                    label="Descripción del Nombre Artístico"
                    prop="descripNombreArts"
                  >
                    <a-input
                      :disabled="disabled"
                      style="width: 100%; height: 150px"
                      v-model="artisticos_modal.descripNombreArts"
                      type="textarea"
                    />
                  </a-form-model-item>
                </a-col>
              </a-row>
              <a-row>
                <a-col span="12">
                  <div class="section-title">
                    <h4>Selección del Intérprete</h4>
                  </div>
                </a-col>
              </a-row>
              <a-col span="12">
                <a-form-model-item
                  label="Intérprete"
                  prop="interprete_id"
                  has-feedback
                >
                  <a-select
                    option-filter-prop="children"
                    :filter-option="filter_option"
                    show-search
                    v-model="artisticos_modal.interprete_id"
                    :disabled="disabled"
                  >
                    <a-select-option
                      v-for="interprete in interpretes"
                      :key="interprete.id"
                      :value="interprete.id"
                    >
                      {{ interprete.nombreInterp }}
                    </a-select-option>
                  </a-select>
                </a-form-model-item>
              </a-col>
            </a-form-model>
          </a-spin>
        </a-tab-pane>
      </a-tabs>
    </a-modal>
  </div>
</template>

<script>
export default {
  props: ["action", "artistico", "artisticos_list"],
  data() {
    let validate_codig_unique = (rule, value, callback) => {
      this.artisticos_list.forEach((element) => {
        if (element.codigArts.substr(5) === value.replace(/ /g, "")) {
          callback(new Error("Código usado"));
        }
      });
      callback();
    };
    return {
      interpretes: [],
      action_cancel_title: "",
      action_title: "",
      show: true,
      used: false,
      disabled: false,
      waiting: false,
      spinning: false,
      text_button: "",
      text_header_button: "",
      artisticos_modal: {},
      actualNombreArts: false,
      disabled: false,
      activated: true,
      show_error: "",
      show_used_error: "",
      action_modal: this.action,
      rules: {
        codigArts: [
          {
            required: true,
            message: "Campo requerido",
            trigger: "change",
          },
          {
            whitespace: true,
            message: "Espacio no válido",
            trigger: "change",
          },
          {
            pattern: "^[0-9]*$",
            message: "Solo dígitos",
            trigger: "change",
          },
          {
            validator: validate_codig_unique,
            trigger: "change",
          },
          {
            len: 4,
            message: "Formato de 4 dígitos",
            trigger: "change",
          },
        ],
        NombreArts: [
          {
            required: true,
            message: "Campo requerido",
            trigger: "change",
          },
          {
            whitespace: true,
            message: "Inserte el nombre",
            trigger: "change",
          },
          {
            pattern: "^[üáéíóúÁÉÍÓÚñÑa-zA-Z0-9 ]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
        descripNombreArts: [
          {
            whitespace: true,
            message: "Inserte una descripción",
            trigger: "change",
          },
          {
            pattern: "^[ a-zA-Z0-9üáéíóúÁÉÍÓÚñÑ,.;:¿?!¡()\n]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
        interprete_id: [
          {
            required: true,
            message: "Campo requerido",
            trigger: "change",
          },
        ],
      },
    };
  },
  created() {
    this.set_action();
  },
  computed: {
    active() {
      if (this.text_button === "Editar") {
        return !this.compare_object;
      } else
        return (
          this.artisticos_modal.codigArts && this.artisticos_modal.NombreArts
        );
    },
  },
  methods: {
    /*
     *Métodoo usado para filtrar la búsqueda del select de los años
     */
    filter_option(input, option) {
      return (
        option.componentOptions.children[0].text
          .toLowerCase()
          .indexOf(input.toLowerCase()) >= 0
      );
    },
    handle_cancel(e) {
      if (e === "cancelar") {
        this.$refs.general_form.resetFields();
        this.show = false;
        this.$emit("close_modal", this.show);
        this.$emit("actualizar");
        this.$toast.success(this.action_close, "¡Éxito!", {
          timeout: 1000,
          color: "orange",
        });
      } else {
        this.$refs.general_form.resetFields();
        this.show = false;
        this.$emit("close_modal", this.show);
      }
    },
    validate() {
      if (!this.used) {
        this.$refs.general_form.validate((valid) => {
          if (valid) {
            return this.confirm();
          }
        });
      }
    },
    set_action() {
      axios
        .post("/interpretes/listar")
        .then((response) => {
          let i = 0;
          const length = response.data.length;
          for (i; i < length; i++) {
            this.interpretes.push(response.data[i]);
          }
        })
        .catch((error) => {
          this.$toast.error("Ha ocurrido un error", "¡Error!", {
            timeout: 1000,
          });
        });
      if (this.action_modal === "editar") {
        if (this.artistico.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.actualNombreArts =
          this.artistico.actualNombreArts === 0 ? false : true;
        this.artistico.descripNombreArts =
          this.artistico.descripNombreArts === null
            ? ""
            : this.artistico.descripNombreArts;
        this.text_header_button = "Editar";
        this.text_button = "Editar";
        this.action_cancel_title =
          "¿Desea cancelar la edición del Nombre Artístico?";
        this.action_title =
          "¿Desea guardar los cambios en el Nombre Artístico?";
        this.action_close =
          "La edición del Nombre Artístico se canceló correctamente";
        this.artistico.codigArts = this.artistico.codigArts.substr(5);
        this.artisticos_modal = { ...this.artistico };
      } else {
        this.text_button = "Crear";
        this.text_header_button = "Crear";
        this.action_cancel_title =
          "¿Desea cancelar la creación del Nombre Artísrico?";
        this.action_title = "¿Desea crear el Nombre Artísrico?";
        this.action_close =
          "La creación del Nombre Artísrico se canceló correctamente";
      }
    },
    confirm() {
      this.spinning = true;
      this.waiting = true;
      let form_data = this.prepare_create();
      if (this.action_modal === "editar") {
        this.text_button = "Editando...";
        axios
          .post(`/artisticos/editar`, form_data, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((response) => {
            this.text_button = "Editar";
            this.spinning = false;
            this.waiting = false;
            this.$emit("actualizar");
            this.$toast.success(
              "Se ha modificado el Nombre Artístico correctamente",
              "¡Éxito!",
              { timeout: 1000 }
            );
            this.handle_cancel();
          })
          .catch((error) => {
            this.text_button = "Editar";
            this.spinning = false;
            this.waiting = false;
            this.handle_cancel();
            this.$toast.error("Ha ocurrido un error", "¡Error!", {
              timeout: 1000,
            });
          });
      } else {
        axios
          .post("/artisticos", form_data, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((res) => {
            this.text_button = "Creando...";
            this.spinning = false;
            this.waiting = false;
            this.$emit("actualizar");
            this.$toast.success(
              "Se ha creado el Nombre Artístico correctamente",
              "¡Éxito!",
              { timeout: 1000 }
            );
            this.handle_cancel();
          })
          .catch((err) => {
            this.text_button = "Crear";
            this.spinning = false;
            this.waiting = false;
            this.handle_cancel();
            this.$toast.error("Ha ocurrido un error", "¡Error!", {
              timeout: 1000,
            });
          });
      }
    },
    prepare_create() {
      this.artisticos_modal.actualNombreArts =
        this.actualNombreArts === false ? 0 : 1;
      let form_data = new FormData();
      if (this.action_modal === "editar") {
        form_data.append("id", this.artisticos_modal.id);
      }
      if (this.artisticos_modal.descripNombreArts === undefined) {
        this.artisticos_modal.descripNombreArts = "";
      }
      this.artisticos_modal.codigArts =
        "ARTS-" + this.artisticos_modal.codigArts;
      form_data.append("codigArts", this.artisticos_modal.codigArts);
      form_data.append(
        "actualNombreArts",
        this.artisticos_modal.actualNombreArts
      );
      form_data.append("NombreArts", this.artisticos_modal.NombreArts);
      form_data.append("interprete_id", this.artisticos_modal.interprete_id);
      form_data.append(
        "descripNombreArts",
        this.artisticos_modal.descripNombreArts
      );
      this.text_button = "Creando...";
      return form_data;
    },
  },
};
</script>

<style>
#modal_gestionar_artisticos .ant-form-item-control {
  width: 80% !important;
}
</style>

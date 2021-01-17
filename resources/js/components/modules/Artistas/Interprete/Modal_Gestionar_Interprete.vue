<template>
  <div>
    <a-modal
      :closable="false"
      width="80.4%"
      okText="Guardar"
      cancelText="Cancelar"
      cancelType="danger"
      :visible="show"
      id="modal_gestionar_interpretes"
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
        <div slot="tabBarExtraContent">{{ text_header_button }} Intérprete</div>
        <a-tab-pane key="1">
          <span slot="tab">Generales</span>
          <a-row>
            <a-col span="12">
              <div class="section-title">
                <h4>Datos Generales</h4>
              </div>
            </a-col>
          </a-row>
          <a-form-model
            ref="general_form"
            layout="horizontal"
            :model="interp_modal"
            :rules="rules"
          >
            <a-col span="12">
              <a-row>
                <a-form-model-item
                  v-if="action_modal !== 'editar'"
                  :validate-status="show_error"
                  prop="codigInterp"
                  has-feedback
                  label="Código"
                  :help="show_used_error"
                >
                  <a-input
                    addon-before="INTR-"
                    placeholder="0001"
                    :disabled="action_modal === 'editar'"
                    v-model="interp_modal.codigInterp"
                  />
                </a-form-model-item>
                <a-form-model-item v-else label="Código">
                  <a-input
                    addon-before="INTR-"
                    placeholder="0001"
                    :disabled="action_modal === 'editar'"
                    v-model="interp_modal.codigInterp"
                  />
                </a-form-model-item>
                <a-form-model-item
                  prop="nombreInterp"
                  has-feedback
                  label="Nombre"
                >
                  <a-input
                    :disabled="disabled"
                    v-model="interp_modal.nombreInterp"
                  />
                </a-form-model-item>
                <a-form-model-item
                  has-feedback
                  label="Reseña biográfica del Interprete"
                  prop="biogInterp"
                >
                  <a-input
                    :disabled="disabled"
                    style="width: 100%; height: 150px"
                    v-model="interp_modal.reseñaBiogInterp"
                    type="textarea"
                  />
                </a-form-model-item>
              </a-row>
            </a-col>
            <a-col span="12">
              <a-row>
                <a-form-model-item
                  style="margin-top: 2.5px !important"
                  prop="NombreArts"
                  has-feedback
                  label="Nombre Artístico"
                >
                  <a-select
                    :getPopupContainer="(trigger) => trigger.parentNode"
                    option-filter-prop="children"
                    :disabled="disabled"
                    v-model="nombreArts"
                  >
                    <a-select-option
                      v-for="nombreArtistico in nombresArtisticos"
                      :key="nombreArtistico.id"
                      :value="nombreArtistico.NombreArts"
                    >
                      {{ nombreArtistico.NombreArts }}
                    </a-select-option>
                  </a-select>
                </a-form-model-item>
              </a-row>
              <a-row>
                <a-form-model-item
                  style="margin-top: 12px !important"
                  v-if="create_artisticos"
                >
                  <a-checkbox
                    :disabled="disabled"
                    v-model="interp_modal.artisticos.actualNombreArts"
                    :value="actualNombreArts"
                    style="margin-top: 20px"
                  >
                    ¿Es el actual Nombre Artístico?
                  </a-checkbox>
                </a-form-model-item>
              </a-row>
              <a-row>
                <a-form-model-item
                  v-if="create_artisticos"
                  has-feedback
                  label="Descripción del Nombre Artístico"
                  prop="descripNombreArts"
                >
                  <a-input
                    :disabled="disabled"
                    style="width: 100%; height: 150px"
                    v-model="interp_modal.artisticos.descripNombreArts"
                    type="textarea"
                  />
                </a-form-model-item>
              </a-row>
            </a-col>
          </a-form-model>
        </a-tab-pane>
      </a-tabs>
    </a-modal>
  </div>
</template>

<script>
export default {
  props: ["action", "interp", "interp_list"],
  data() {
    let validate_codig_unique = (rule, value, callback) => {
      this.interp_list.forEach((element) => {
        if (element.codigInterp.substr(5) === value.replace(/ /g, "")) {
          callback(new Error("Código ya usado"));
        }
      });
      callback();
    };
    return {
      create_artisticos: false,
      action_cancel_title: "",
      action_title: "",
      show: true,
      used: false,
      disabled: false,
      waiting: false,
      text_button: "",
      nombreArts: "",
      text_header_button: "",
      interp_modal: {},
      actualNombreArts: false,
      disabled: false,
      nombresArtisticos: [],
      activated: true,
      show_error: "",
      show_used_error: "",
      action_modal: this.action,
      rules: {
        codigInterp: [
          {
            required: true,
            message: "Inserte el código",
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
        nombreInterp: [
          {
            required: true,
            message: "Inserte el nombre",
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
        biogInterp: [
          {
            pattern: "^[ a-zA-Z0-9 üáéíóúÁÉÍÓÚñÑ,.;:¿?!¡()]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
      },
    };
  },
  created() {
    axios
      .post("/artisticos/listar")
      .then((response) => {
        this.nombresArtisticos = response.data;
      })
      .catch((error) => console.log(error));
    this.set_action();
  },
  computed: {
    active() {
      if (this.text_button === "Editar") {
        return !this.compare_object;
      } else
        return this.interp_modal.codigInterp && this.interp_modal.nombreInterp;
    },
  },
  methods: {
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
      if (this.action_modal === "editar") {
        if (this.interp.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.text_header_button = "Editar";
        this.text_button = "Editar";
        this.action_cancel_title = "¿Desea cancelar la edición del Intérprete?";
        this.action_title = "¿Desea guardar los cambios en el Intérprete?";
        this.action_close =
          "La edición del Intérprete se canceló correctamente";
        this.interp.codigInterp = this.interp.codigInterp.substr(5);
        this.interp_modal = { ...this.interp };
      } else {
        this.text_button = "Crear";
        this.text_header_button = "Crear";
        this.action_cancel_title =
          "¿Desea cancelar la creación del Intérprete?";
        this.action_title = "¿Desea crear el Intérprete?";
        this.action_close =
          "La creación del Intérprete se canceló correctamente";
      }
    },
    confirm() {
      this.waiting = true;
      let form_data = this.prepare_create();
      if (this.action_modal === "editar") {
        this.text_button = "Editando...";
        axios
          .post(`/interpretes/editar`, form_data, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((response) => {
            this.text_button = "Editar";
            this.waiting = false;
            this.$emit("actualizar");
            this.$toast.success(
              "Se ha modificado el interprete correctamente",
              "¡Éxito!",
              { timeout: 1000 }
            );
            this.handle_cancel();
          })
          .catch((error) => {
            this.text_button = "Editar";
            this.waiting = false;
            this.$toast.error("Ha ocurrido un error", "¡Error!", {
              timeout: 1000,
            });
          });
      } else {
        axios
          .post("/interpretes", form_data, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((res) => {
            this.text_button = "Creando...";
            this.waiting = false;
            this.$emit("actualizar");
            this.$toast.success(
              "Se ha creado el interprete correctamente",
              "¡Éxito!",
              { timeout: 1000 }
            );
            this.handle_cancel();
          })
          .catch((err) => {
            this.text_button = "Crear";
            this.waiting = false;
            this.$toast.error("Ha ocurrido un error", "¡Error!", {
              timeout: 1000,
            });
          });
      }
    },
    prepare_create() {
      this.interp_modal.actualNombreArts =
        this.actualNombreArts === false ? 0 : 1;
      let form_data = new FormData();
      if (this.action_modal === "editar") {
        form_data.append("id", this.interp_modal.id);
      }
      if (this.interp_modal.reseñaBiogInterp === undefined) {
        this.interp_modal.reseñaBiogInterp = "";
      }
      this.interp_modal.codigInterp = "INTR-" + this.interp_modal.codigInterp;
      form_data.append("codigInterp", this.interp_modal.codigInterp);
      form_data.append("nombreInterp", this.interp_modal.nombreInterp);
      form_data.append("reseñaBiogInterp", this.interp_modal.reseñaBiogInterp);
      this.text_button = "Creando...";
      return form_data;
    },
  },
};
</script>

<style>
#modal_gestionar_interpretes .ant-form-item-control {
  width: 80% !important;
}
</style>

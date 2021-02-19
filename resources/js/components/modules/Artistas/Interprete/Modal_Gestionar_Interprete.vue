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
        <a-button
          v-if="action_modal === 'detalles'"
          key="back"
          type="primary"
          @click="handle_cancel('cancelar')"
        >
          Aceptar</a-button
        >
        <a-popconfirm
          v-if="action_modal !== 'detalles'"
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
          v-if="action_modal !== 'detalles'"
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
          <a-spin :spinning="spinning">
            <a-form-model
              ref="general_form"
              layout="horizontal"
              :model="interp_modal"
              :rules="rules"
            >
              <a-row>
                <a-col span="12">
                  <a-form-model-item
                    v-if="
                      action_modal === 'crear' ||
                      action_modal === 'crear_interprete'
                    "
                    :validate-status="show_error"
                    prop="codigInterp"
                    has-feedback
                    label="Código"
                    :help="show_used_error"
                  >
                    <a-input
                      addon-before="INTR-"
                      :default-value="codigo"
                      :disabled="action_modal === 'editar'"
                      v-model="interp_modal.codigInterp"
                    />
                  </a-form-model-item>
                  <a-form-model-item
                    v-if="action_modal === 'editar'"
                    label="Código"
                  >
                    <a-input
                      addon-before="INTR-"
                      :disabled="action_modal === 'editar'"
                      v-model="interp_modal.codigInterp"
                    />
                  </a-form-model-item>
                  <a-form-model-item
                    v-if="action_modal === 'detalles'"
                    label="Código"
                  >
                    <a-mentions
                      readonly
                      :placeholder="interp_modal.codigInterp"
                    >
                    </a-mentions>
                  </a-form-model-item>
                  <a-form-model-item
                    v-if="action_modal !== 'detalles'"
                    prop="nombreInterp"
                    has-feedback
                    label="Nombre"
                  >
                    <a-input
                      :disabled="disabled"
                      v-model="interp_modal.nombreInterp"
                    />
                  </a-form-model-item>
                  <a-form-model-item v-else label="Nombre">
                    <a-mentions
                      readonly
                      :placeholder="interp_modal.nombreInterp"
                    >
                    </a-mentions>
                  </a-form-model-item>
                  <a-form-model-item
                    v-if="action_modal !== 'detalles' && interp_modal.tabla"
                    label="Roles"
                  >
                    <a-select
                      :getPopupContainer="(trigger) => trigger.parentNode"
                      mode="multiple"
                      :disabled="disabled"
                      v-model="roles_interp_modal"
                    >
                      <a-select-option
                        v-for="nomenclator in roles"
                        :key="nomenclator.id"
                        :value="nomenclator.nombreTer"
                      >
                        {{ nomenclator.nombreTer }}
                      </a-select-option>
                    </a-select>
                  </a-form-model-item>
                  <a-form-model-item
                    label="Roles"
                    v-if="action_modal === 'detalles' && interp_modal.tabla"
                  >
                    <a-mentions readonly :placeholder="interp_modal.rolInterp">
                    </a-mentions>
                  </a-form-model-item>
                </a-col>
                <a-col span="12">
                  <a-form-model-item
                    v-if="action_modal !== 'detalles'"
                    has-feedback
                    label="Reseña biográfica del Intérprete"
                    prop="reseñaBiogInterp"
                  >
                    <a-input
                      :disabled="disabled"
                      style="width: 100%; height: 150px"
                      v-model="interp_modal.reseñaBiogInterp"
                      type="textarea"
                    />
                  </a-form-model-item>
                  <a-form-model-item
                    v-else
                    label="Reseña biográfica del Interprete"
                  >
                    <div class="description">
                      <a-mentions
                        readonly
                        :placeholder="interp_modal.reseñaBiogInterp"
                      >
                      </a-mentions>
                    </div>
                  </a-form-model-item>
                </a-col>
              </a-row>
            </a-form-model>
          </a-spin>
        </a-tab-pane>
        <a-tab-pane key="2" v-if="action_modal !== 'crear'">
          <span slot="tab"> Audiovisuales </span>
          <a-row>
            <a-col span="12">
              <div class="section-title">
                <h4>Audiovisuales</h4>
              </div>
            </a-col>
          </a-row>
          <br />
          <div>
            <tabla_audiovisuales
              :detalles_prop="detalles"
              @reload="reload_parent"
              :entity="interp_modal"
              entity_relation="interpretes"
              :vista_editar="vista_editar"
              @close_modal="show = $event"
            />
            <br />
          </div>
        </a-tab-pane>
      </a-tabs>
    </a-modal>
  </div>
</template>

<script>
import tabla_audiovisuales from "../../Audiovisual/Tabla_Audiovisuales";
export default {
  props: ["action", "interp", "interp_list"],
  data() {
    let validate_codig_unique = (rule, value, callback) => {
      this.interp_list.forEach((element) => {
        if (element.codigInterp.substr(5) === value.replace(/ /g, "")) {
          callback(new Error("Código usado"));
        }
      });
      callback();
    };
    let code_required = (rule, value, callback) => {
      if (value === "") {
        callback(new Error("Inserte el código"));
      } else callback();
    };
    return {
      roles_interp_modal: [],
      roles: [],
      detalles: true,
      vista_editar: true,
      action_cancel_title: "",
      action_title: "",
      show: true,
      used: false,
      disabled: false,
      waiting: false,
      spinning: false,
      text_button: "",
      nombreArts: "",
      text_header_button: "",
      interp_modal: {},
      disabled: false,
      activated: true,
      show_error: "",
      show_used_error: "",
      action_modal: this.action,
      codigo: "",
      rules: {
        codigInterp: [
          {
            validator: code_required,
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
            message: "Campo requerido",
            trigger: "change",
          },
          {
            whitespace: true,
            message: "Espacio no válido",
            trigger: "change",
          },
          {
            pattern: "^[-üáéíóúÁÉÍÓÚñÑa-zA-Z0-9 ]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
        reseñaBiogInterp: [
          {
            whitespace: true,
            message: "Espacio no válido",
            trigger: "change",
          },
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
    if (this.action !== "crear_interprete") {
      if (this.interp.tabla) {
        this.load_nomenclators();
      }
    }
    this.set_action();
    if (
      this.action_modal === "crear" ||
      this.action_modal === "crear_interprete"
    ) {
      this.codigo = this.generar_codigo(this.interp_list);
    }
  },
  computed: {
    active() {
      if (this.text_button === "Editar") {
        return !this.compare_object;
      } else return this.interp_modal.nombreInterp;
    },
  },
  methods: {
    load_nomenclators() {
      axios
        .post("/interpretes/nomencladores")
        .then((response) => {
          this.list_nomenclators = response.data;
          this.roles = this.list_nomenclators;
        })
        .catch((error) => {
          this.$toast.error("Ha ocurrido un error", "¡Error!", {
            timeout: 2000,
          });
        });
    },
    reload_parent() {
      this.$emit("refresh");
    },
    handle_cancel(e) {
      if (e === "cancelar") {
        this.$refs.general_form.resetFields();
        this.show = false;
        this.$emit("close_modal", this.show);
        if (this.action_modal !== "detalles") {
          this.$toast.success(this.action_close, "¡Éxito!", {
            timeout: 2000,
            color: "orange",
          });
        }
      } else {
        this.$refs.general_form.resetFields();
        this.show = false;
        this.$emit("close_modal", this.show);
      }
    },
    validate() {
      if (this.interp_modal.codigInterp === undefined) {
        this.interp_modal.codigInterp = this.codigo;
      }
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
        if (this.interp.tabla) {
          if (this.interp.rolInterp) {
            this.roles_interp_modal = this.interp.rolInterp;
          }
        }
        if (this.interp.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.interp.reseñaBiogInterp =
          this.interp.reseñaBiogInterp === null
            ? ""
            : this.interp.reseñaBiogInterp;
        this.text_header_button = "Editar";
        this.text_button = "Editar";
        this.action_cancel_title = "¿Desea cancelar la edición del Intérprete?";
        this.action_title = "¿Desea guardar los cambios en el Intérprete?";
        this.action_close =
          "La edición del Intérprete se canceló correctamente";
        this.interp_modal = { ...this.interp };
        this.interp_modal.codigInterp = this.interp.codigInterp.substr(5);
      } else if (this.action_modal === "detalles") {
        if (this.interp.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.text_header_button = "Detalles";
        this.text_button = "Detalles";
        this.interp_modal = { ...this.interp };
      } else {
        this.interp_modal = { ...this.interp };
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
      this.spinning = true;
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
            this.spinning = false;
            this.waiting = false;
            this.$emit("actualizar");
            this.$toast.success(
              "Se ha modificado el interprete correctamente",
              "¡Éxito!",
              { timeout: 2000 }
            );
            this.handle_cancel();
          })
          .catch((error) => {
            this.text_button = "Editar";
            this.spinning = false;
            this.waiting = false;
            this.$toast.error("Ha ocurrido un error", "¡Error!", {
              timeout: 2000,
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
            if (this.action_modal === "crear_interprete") {
              let interpretes = [];
              axios
                .post("/interpretes/listar")
                .then((response) => {
                  let prod = response.data;
                  prod.forEach((element) => {
                    if (!element.deleted_at) {
                      interpretes.push(element);
                    }
                  });
                  this.$store.state["interpretes"].push(
                    interpretes[interpretes.length - 1]
                  );
                  this.$store.state["created_interpretes"].push(
                    interpretes[interpretes.length - 1]
                  );
                  this.$store.state["all_interpretes_statics"].push(
                    interpretes[interpretes.length - 1]
                  );
                })
                .catch((error) => {
                  console.log(error);
                });
            }
            this.text_button = "Creando...";
            this.spinning = false;
            this.waiting = false;
            this.$emit("actualizar");
            this.$toast.success(
              "Se ha creado el interprete correctamente",
              "¡Éxito!",
              { timeout: 2000 }
            );
            this.handle_cancel();
          })
          .catch((err) => {
            this.text_button = "Crear";
            this.spinning = false;
            this.waiting = false;
            this.$toast.error("Ha ocurrido un error", "¡Error!", {
              timeout: 2000,
            });
          });
      }
    },
    prepare_create() {
      this.interp_modal.actualNombreArts =
        this.actualNombreArts === false ? 0 : 1;
      let form_data = new FormData();
      if (this.action_modal === "editar" || this.action_modal === "detalles") {
        form_data.append("id", this.interp_modal.id);
      }
      if (this.interp_modal.reseñaBiogInterp === undefined) {
        this.interp_modal.reseñaBiogInterp = "";
      }
      if (this.interp_modal.codigInterp === undefined) {
        this.interp_modal.codigInterp = this.codigo;
      }
      if (this.action_modal !== "detalles") {
        let roles_interp = "";
        this.roles_interp_modal.forEach((element) => {
          roles_interp += element + ",";
        });

        this.interp_modal.rolInterp = roles_interp;
      }
      this.interp_modal.codigInterp = "INTR-" + this.interp_modal.codigInterp;
      form_data.append("codigInterp", this.interp_modal.codigInterp);
      form_data.append("nombreInterp", this.interp_modal.nombreInterp);
      form_data.append("reseñaBiogInterp", this.interp_modal.reseñaBiogInterp);
      if (this.interp_modal.audiovisuales_interps) {
        this.relation = "audiovisuales";
        form_data.append("type_relation", this.relation);
        form_data.append("roles", this.interp_modal.rolInterp);
        form_data.append(
          "audiovisual_id",
          this.interp_modal.audiovisuales_interps
        );
      } else if (this.interp_modal.tracks_interps) {
        this.relation = "tracks";
        form_data.append("roles", this.interp_modal.rolInterp);
        form_data.append("type_relation", this.relation);
        form_data.append("track_id", this.interp_modal.tracks_interps);
      }

      this.text_button = "Creando...";
      return form_data;
    },
    //Metodos para generar el codigo
    //Este es el único método que varia de un módulo a otro
    crear_arr_codig(arr) {
      let answer = [];
      for (let i = 0; i < arr.length; i++) {
        answer.push(parseInt(arr[i].codigInterp.substr(5, 8)));
      }
      return answer;
    },
    //Método de ordenamiento en burbuja
    ordenamiento_burbuja(arr) {
      const l = arr.length;
      for (let i = 0; i < l; i++) {
        for (let j = 0; j < l - 1 - i; j++) {
          if (arr[j] > arr[j + 1]) {
            [arr[j], arr[j + 1]] = [arr[j + 1], arr[j]];
          }
        }
      }
      return arr;
    },
    generar_codigo(arr) {
      let list = this.ordenamiento_burbuja(this.crear_arr_codig(arr));
      let answer = 1;
      for (let i = 0; i < list.length; i++) {
        if (list[0] !== 1) {
          answer = 1;
          break;
        }
        if (i === list.length - 1) {
          answer = list[i] + 1;
          break;
        }
        if (!(list[i] + 1 === list[i + 1])) {
          answer = list[i] + 1;
          break;
        }
      }
      return this.crear_codigo(answer);
    },
    crear_codigo(number) {
      switch (number.toString().length) {
        case 1:
          return "000" + number;
        case 2:
          return "00" + number;
        case 3:
          return "0" + number;
        case 4:
          return number.toString();
        default:
          break;
      }
    },
    //Fin de metodos para generar el codigo
  },
  components: {
    tabla_audiovisuales,
  },
};
</script>

<style>
#modal_gestionar_interpretes .ant-form-item-control {
  width: 80% !important;
}
#modal_gestionar_interpretes .ant-mentions textarea {
  height: 32px !important;
}
#modal_gestionar_interpretes .description textarea {
  height: 150px !important;
}
</style>

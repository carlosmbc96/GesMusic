<template>
  <div>
    <a-modal
      :closable="false"
      width="80.4%"
      okText="Guardar"
      cancelText="Cancelar"
      cancelType="danger"
      :visible="show"
      id="modal_gestionar_entrevistados"
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
        <div slot="tabBarExtraContent">
          {{ text_header_button }} Entrevistado
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
              :model="entrevistados_modal"
              :rules="rules"
            >
              <a-row>
                <a-col span="11">
                  <a-form-model-item
                    v-if="action_modal === 'crear'"
                    :validate-status="show_error"
                    prop="codigEntrv"
                    has-feedback
                    label="Código"
                    :help="show_used_error"
                  >
                    <a-input
                      addon-before="ENTR-"
                      :default-value="codigo"
                      :disabled="action_modal === 'editar'"
                      v-model="entrevistados_modal.codigEntrv"
                    />
                  </a-form-model-item>
                  <a-form-model-item
                    v-if="action_modal === 'editar'"
                    label="Código"
                  >
                    <a-input
                      addon-before="ENTR-"
                      :disabled="action_modal === 'editar'"
                      v-model="entrevistados_modal.codigEntrv"
                    />
                  </a-form-model-item>
                  <a-form-model-item
                    v-if="action_modal === 'detalles'"
                    label="Código"
                  >
                    <a-mentions
                      readonly
                      :placeholder="entrevistados_modal.codigEntrv"
                    >
                    </a-mentions>
                  </a-form-model-item>
                  <a-form-model-item
                    v-if="action_modal !== 'detalles'"
                    has-feedback
                    label="Nombre Completo"
                    prop="nombreApellidosEntrv"
                  >
                    <a-input
                      :disabled="disabled"
                      v-model="entrevistados_modal.nombreApellidosEntrv"
                    />
                  </a-form-model-item>
                  <a-form-model-item v-else label="Nombre Completo">
                    <a-mentions
                      readonly
                      :placeholder="entrevistados_modal.nombreApellidosEntrv"
                    >
                    </a-mentions>
                  </a-form-model-item>
                  <a-form-model-item
                    v-if="action_modal !== 'detalles'"
                    has-feedback
                    label="Descripción del Entrevistado"
                    prop="descripEspEntrv"
                  >
                    <a-input
                      :disabled="disabled"
                      style="width: 100%; height: 150px"
                      v-model="entrevistados_modal.descripEspEntrv"
                      type="textarea"
                    />
                  </a-form-model-item>
                  <a-form-model-item
                    label="Descripción del Entrevistado"
                    v-if="action_modal === 'detalles'"
                  >
                    <div class="description">
                      <a-mentions
                        readonly
                        :placeholder="entrevistados_modal.descripEspEntrv"
                      >
                      </a-mentions>
                    </div>
                  </a-form-model-item>
                </a-col>
                <a-col span="11" style="float: right">
                  <a-form-model-item
                    v-if="action_modal !== 'detalles'"
                    has-feedback
                    label="Sexo"
                    prop="sexoEntrv"
                  >
                    <a-select
                      :getPopupContainer="(trigger) => trigger.parentNode"
                      option-filter-prop="children"
                      show-search
                      :disabled="disabled"
                      v-model="entrevistados_modal.sexoEntrv"
                    >
                      <a-select-option
                        v-for="nomenclator in list_nomenclators"
                        :key="nomenclator.id"
                        :value="nomenclator.nombreTer"
                      >
                        {{ nomenclator.nombreTer }}
                      </a-select-option>
                    </a-select>
                  </a-form-model-item>
                  <a-form-model-item v-else label="Sexo">
                    <a-mentions
                      readonly
                      :placeholder="entrevistados_modal.sexoEntrv"
                    >
                    </a-mentions>
                  </a-form-model-item>
                  <a-form-model-item
                    v-if="action_modal !== 'detalles'"
                    label="Audiovisual"
                    prop="audiovisual_id"
                    has-feedback
                  >
                    <a-select
                      option-filter-prop="children"
                      :filter-option="filter_option"
                      show-search
                      v-model="entrevistados_modal.audiovisual_id"
                      :disabled="disabled"
                    >
                      <a-select-option
                        v-for="audiovisual in audiovisuals"
                        :key="audiovisual.id"
                        :value="audiovisual.id"
                      >
                        {{ audiovisual.tituloAud }}
                      </a-select-option>
                    </a-select>
                  </a-form-model-item>
                  <a-form-model-item v-else label="Audiovisual">
                    <a-mentions
                      readonly
                      :placeholder="
                        get_audiovisual(entrevistados_modal.audiovisual_id)
                      "
                    >
                    </a-mentions>
                  </a-form-model-item>
                </a-col>
              </a-row>
            </a-form-model>
          </a-spin>
        </a-tab-pane>
      </a-tabs>
    </a-modal>
  </div>
</template>

<script>
export default {
  props: ["action", "entrevistado", "entrevistados_list"],
  data() {
    let validate_codig_unique = (rule, value, callback) => {
      this.entrevistados_list.forEach((element) => {
        if (element.codigEntrv.substr(5) === value.replace(/ /g, "")) {
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
      audiovisuals: [],
      action_cancel_title: "",
      action_title: "",
      show: true,
      used: false,
      disabled: false,
      waiting: false,
      spinning: false,
      text_button: "",
      text_header_button: "",
      entrevistados_modal: {},
      disabled: false,
      activated: true,
      show_error: "",
      show_used_error: "",
      action_modal: this.action,
      codigo: "",
      list_nomenclators: [],
      rules: {
        codigEntrv: [
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
        nombreApellidosEntrv: [
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
            pattern: "^[üáéíóúÁÉÍÓÚñÑa-zA-Z0-9 ]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
        sexoEntrv: [
          {
            required: true,
            message: "Campo requerido",
            trigger: "change",
          },
        ],
        audiovisual_id: [
          {
            required: true,
            message: "Campo requerido",
            trigger: "change",
          },
        ],
        descripEspEntrv: [
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
      },
    };
  },
  created() {
    this.load_nomenclators();
    this.set_action();
    if (this.action_modal === "crear") {
      this.codigo = this.generar_codigo(this.entrevistados_list);
    }
  },
  computed: {
    active() {
      if (this.text_button === "Editar") {
        return !this.compare_object;
      } else
        return (
          this.entrevistados_modal.nombreApellidosEntrv &&
          this.entrevistados_modal.sexoEntrv &&
          this.entrevistados_modal.audiovisual_id
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
      if (this.entrevistados_modal.codigEntrv === undefined) {
        this.entrevistados_modal.codigEntrv = this.codigo;
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
      axios
        .post("/audiovisuales/listar")
        .then((response) => {
          let i = 0;
          const length = response.data.length;
          for (i; i < length; i++) {
            this.audiovisuals.push(response.data[i]);
          }
        })
        .catch((error) => {
          this.$toast.error("Ha ocurrido un error", "¡Error!", {
            timeout: 2000,
          });
        });
      if (this.action_modal === "editar") {
        if (this.entrevistado.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.text_header_button = "Editar";
        this.text_button = "Editar";
        this.entrevistado.descripEspEntrv =
          this.entrevistado.descripEspEntrv === null
            ? ""
            : this.entrevistado.descripEspEntrv;
        this.action_cancel_title =
          "¿Desea cancelar la edición del Entrevistado?";
        this.action_title = "¿Desea guardar los cambios en el Entrevistado?";
        this.action_close =
          "La edición del Entrevistado se canceló correctamente";
        this.entrevistados_modal = { ...this.entrevistado };
        this.entrevistados_modal.codigEntrv = this.entrevistado.codigEntrv.substr(
          5
        );
      } else if (this.action_modal === "detalles") {
        if (this.entrevistado.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.text_header_button = "Detalles";
        this.text_button = "Detalles";
        this.entrevistado.descripEspEntrv =
          this.entrevistado.descripEspEntrv === null
            ? ""
            : this.entrevistado.descripEspEntrv;
        this.entrevistados_modal = { ...this.entrevistado };
      } else {
        this.text_button = "Crear";
        this.text_header_button = "Crear";
        this.action_cancel_title =
          "¿Desea cancelar la creación del Entrevistado?";
        this.action_title = "¿Desea crear el Entrevistado?";
        this.action_close =
          "La creación del Entrevistado se canceló correctamente";
      }
    },
    confirm() {
      this.spinning = true;
      this.waiting = true;
      let form_data = this.prepare_create();
      if (this.action_modal === "editar") {
        axios
          .post(`/entrevistados/editar`, form_data, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((response) => {
            this.text_button = "Editando...";
            this.text_button = "Editar";
            this.spinning = false;
            this.waiting = false;
            this.$emit("actualizar");
            this.$toast.success(
              "Se ha modificado el Entrevistado correctamente",
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
          .post("/entrevistados", form_data, {
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
              "Se ha creado el Entrevistado correctamente",
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
      if (this.entrevistados_modal.descripEspEntrv === undefined) {
        this.entrevistados_modal.descripEspEntrv = "";
      } else if (this.entrevistados_modal.descripEspEntrv === null) {
        this.entrevistados_modal.descripEspEntrv = "";
      }
      let form_data = new FormData();
      if (this.action_modal === "editar" || this.action_modal === "detalles") {
        form_data.append("id", this.entrevistados_modal.id);
      }
      if (this.entrevistados_modal.codigEntrv === undefined) {
        this.entrevistados_modal.codigEntrv = this.codigo;
      }
      this.entrevistados_modal.codigEntrv =
        "ENTR-" + this.entrevistados_modal.codigEntrv;
      form_data.append("codigEntrv", this.entrevistados_modal.codigEntrv);
      form_data.append(
        "nombreApellidosEntrv",
        this.entrevistados_modal.nombreApellidosEntrv
      );
      form_data.append(
        "descripEspEntrv",
        this.entrevistados_modal.descripEspEntrv
      );
      form_data.append("sexoEntrv", this.entrevistados_modal.sexoEntrv);
      form_data.append(
        "audiovisual_id",
        this.entrevistados_modal.audiovisual_id
      );
      this.text_button = "Creando...";
      return form_data;
    },
    //Metodos para generar el codigo
    //Este es el único método que varia de un módulo a otro
    crear_arr_codig(arr) {
      let answer = [];
      for (let i = 0; i < arr.length; i++) {
        answer.push(parseInt(arr[i].codigEntrv.substr(5, 8)));
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
    get_audiovisual(id) {
      for (let index = 0; index < this.audiovisuals.length; index++) {
        if (this.audiovisuals[index].id === id)
          return this.audiovisuals[index].tituloAud;
      }
      return -1;
    },
    load_nomenclators() {
      axios
        .post("/entrevistados/nomencladores")
        .then((response) => {
          let i = 0;
          const length = response.data.length;
          for (i; i < length; i++) {
            this.list_nomenclators.push(response.data[i]);
          }
        })
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
#modal_gestionar_entrevistados .ant-mentions textarea {
  height: 32px !important;
}
#modal_gestionar_entrevistados .description textarea {
  height: 150px !important;
}
</style>

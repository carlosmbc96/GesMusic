<template>
  <div>
    <a-modal
      :closable="false"
      width="80.4%"
      okText="Guardar"
      cancelText="Cancelar"
      cancelType="danger"
      :visible="show"
      id="modal_gestionar_temas"
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
        <div slot="tabBarExtraContent">{{ text_header_button }} Tema</div>
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
              :model="tema_modal"
              :rules="rules"
            >
              <a-row>
                <a-col span="11">
                  <a-form-model-item
                    v-if="action_modal === 'crear'"
                    :validate-status="show_error"
                    prop="codigTema"
                    has-feedback
                    label="Código"
                    :help="show_used_error"
                  >
                    <a-input
                      addon-before="TEMA-"
                      :default-value="codigo"
                      :disabled="action_modal === 'editar'"
                      v-model="tema_modal.codigTema"
                    />
                  </a-form-model-item>
                  <a-form-model-item
                    v-if="action_modal === 'editar'"
                    label="Código"
                  >
                    <a-input
                      addon-before="TEMA-"
                      :disabled="action_modal === 'editar'"
                      v-model="tema_modal.codigTema"
                    />
                  </a-form-model-item>
                  <a-form-model-item
                    v-if="action_modal === 'detalles'"
                    label="Código"
                  >
                    <a-mentions readonly :placeholder="tema_modal.codigTema">
                    </a-mentions>
                  </a-form-model-item>
                  <a-form-model-item
                    v-if="action_modal !== 'detalles'"
                    has-feedback
                    label="Título"
                    prop="tituloTem"
                  >
                    <a-input
                      :disabled="disabled"
                      v-model="tema_modal.tituloTem"
                    />
                  </a-form-model-item>
                  <a-form-model-item v-else label="Título">
                    <a-mentions readonly :placeholder="tema_modal.tituloTem">
                    </a-mentions>
                  </a-form-model-item>
                  <a-form-model-item
                    v-if="action_modal !== 'detalles'"
                    has-feedback
                    label="Sociedad de Gestión"
                    prop="sociedadGestionTem"
                  >
                    <a-select
                      :getPopupContainer="(trigger) => trigger.parentNode"
                      option-filter-prop="children"
                      show-search
                      :disabled="disabled"
                      v-model="tema_modal.sociedadGestionTem"
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
                  <a-form-model-item v-else label="Sociedad de Gestión">
                    <a-mentions
                      readonly
                      :placeholder="tema_modal.sociedadGestionTem"
                    >
                    </a-mentions>
                  </a-form-model-item>
                  <a-form-model-item v-if="action_modal !== 'detalles'">
                    <a-checkbox
                      :disabled="disabled"
                      v-model="catalDigitalTem"
                      :value="catalDigitalTem"
                      style="margin-top: 20px"
                    >
                      ¿Estará Tema en el Catálogo Digital?
                    </a-checkbox>
                  </a-form-model-item>
                  <a-form-model-item style="margin-top: 20px" v-else>
                    <i
                      class="fa fa-check-square-o hidden-xs"
                      v-if="tema.catalDigitalTem === 1"
                    />
                    <i class="fa fa-square-o" v-else />
                    ¿Estará Tema en el Catálogo Digital?
                  </a-form-model-item>
                </a-col>
                <a-col span="11" style="float: right">
                  <a-form-model-item
                    v-if="action_modal !== 'detalles'"
                    has-feedback
                    label="Descripción del Tema"
                    prop="descripTem"
                  >
                    <a-input
                      :disabled="disabled"
                      style="width: 100%; height: 150px"
                      v-model="tema_modal.descripTem"
                      type="textarea"
                    />
                  </a-form-model-item>
                  <a-form-model-item
                    label="Descripción del Tema"
                    v-if="action_modal === 'detalles'"
                  >
                    <div class="description">
                      <a-mentions
                        readonly
                        :placeholder="tema_modal.descripTem"
                      >
                      </a-mentions>
                    </div>
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
  props: ["action", "tema", "temas_list"],
  data() {
    let validate_codig_unique = (rule, value, callback) => {
      this.temas_list.forEach((element) => {
        if (element.codigTema.substr(5) === value.replace(/ /g, "")) {
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
      vista_editar: true,
      detalles: true,
      action_cancel_title: "",
      action_title: "",
      show: true,
      used: false,
      disabled: false,
      waiting: false,
      spinning: false,
      text_button: "",
      text_header_button: "",
      tema_modal: {},
      disabled: false,
      activated: true,
      show_error: "",
      show_used_error: "",
      action_modal: this.action,
      codigo: "",
      list_nomenclators: [],
			catalDigitalTem: false,
      rules: {
        codigTema: [
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
        tituloTem: [
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
        descripTem: [
          {
            whitespace: true,
            message: "Espacio no válido",
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
      this.codigo = this.generar_codigo(this.temas_list);
    }
  },
  computed: {
    active() {
      if (this.text_button === "Editar") {
        return !this.compare_object;
      } else return this.tema_modal.tituloTem;
    },
  },
  methods: {
    reload_parent() {
      this.$emit("refresh");
    },
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
      if (this.tema_modal.codigTema === undefined) {
        this.tema_modal.codigTema = this.codigo;
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
        if (this.tema.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.text_header_button = "Editar";
        this.text_button = "Editar";
        this.tema.descripTem =
          this.tema.descripTem === null ? "" : this.tema.descripTem;
        this.action_cancel_title = "¿Desea cancelar la edición del Tema?";
        this.action_title = "¿Desea guardar los cambios en el Tema?";
        this.action_close = "La edición del Tema se canceló correctamente";
        this.tema_modal = { ...this.tema };
				this.catalDigitalTem =
          this.tema.catalDigitalTem === 0 ? false : true;
        this.tema_modal.codigTema = this.tema.codigTema.substr(5);
      } else if (this.action_modal === "detalles") {
        if (this.tema.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.text_header_button = "Detalles";
        this.text_button = "Detalles";
        this.tema.descripTem =
          this.tema.descripTem === null ? "" : this.tema.descripTem;
        this.tema_modal = { ...this.tema };
      } else {
        this.text_button = "Crear";
        this.text_header_button = "Crear";
        this.action_cancel_title = "¿Desea cancelar la creación del Tema?";
        this.action_title = "¿Desea crear el Tema?";
        this.action_close = "La creación del tema se canceló correctamente";
      }
    },
    confirm() {
      this.spinning = true;
      this.waiting = true;
      let form_data = this.prepare_create();
      if (this.action_modal === "editar") {
        axios
          .post(`/temas/editar`, form_data, {
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
              "Se ha modificado el Tema correctamente",
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
          .post("/temas", form_data, {
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
              "Se ha creado el Tema correctamente",
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
			console.log(this.tema_modal);
			this.tema_modal.catalDigitalTem = this.catalDigitalTem === false ? 0 : 1;
      if (this.tema_modal.descripTem === undefined) {
        this.tema_modal.descripTem = "";
      } else if (this.tema_modal.descripTem === null) {
        this.tema_modal.descripTem = "";
      }
      let form_data = new FormData();
      if (this.action_modal === "editar" || this.action_modal === "detalles") {
        form_data.append("id", this.tema_modal.id);
      }
      if (this.tema_modal.codigTema === undefined) {
        this.tema_modal.codigTema = this.codigo;
      }
      this.tema_modal.codigTema = "TEMA-" + this.tema_modal.codigTema;
      form_data.append("codigTema", this.tema_modal.codigTema);
      form_data.append("tituloTem", this.tema_modal.tituloTem);
			form_data.append("catalDigitalTem", this.tema_modal.catalDigitalTem);
      form_data.append("descripTem", this.tema_modal.descripTem);
      form_data.append(
        "sociedadGestionTem",
        this.tema_modal.sociedadGestionTem
      );
      this.text_button = "Creando...";
      return form_data;
    },
    //Metodos para generar el codigo
    //Este es el único método que varia de un módulo a otro
    crear_arr_codig(arr) {
      let answer = [];
      for (let i = 0; i < arr.length; i++) {
        answer.push(parseInt(arr[i].codigTema.substr(5, 8)));
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
    load_nomenclators() {
      axios
        .post("/temas/nomencladores")
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
#modal_gestionar_temas .ant-mentions textarea {
  height: 32px !important;
}
#modal_gestionar_temas .description textarea {
  height: 150px !important;
}
</style>

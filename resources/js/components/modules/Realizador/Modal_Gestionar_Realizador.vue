<template>
  <div>
    <a-modal
      :closable="false"
      width="80.4%"
      okText="Guardar"
      cancelText="Cancelar"
      cancelType="danger"
      :visible="show"
      id="modal_gestionar_realizadores"
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
      <a-tabs :activeKey="active_tab">
        <div slot="tabBarExtraContent">{{ text_header_button }} Realizador</div>
        <a-tab-pane key="1" :disabled="tab_1">
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
              :model="realizadores_modal"
              :rules="rules"
            >
              <a-row>
                <a-col span="6">
                  <a-upload
                    v-if="action_modal !== 'detalles'"
                    :disabled="disabled"
                    :remove="remove_image"
                    @preview="handle_preview"
                    :file-list="file_list"
                    list-type="picture-card"
                    :before-upload="before_upload"
                    @change="handle_change"
                  >
                    <div v-if="file_list.length < 1">
                      <img v-if="preview_image" />
                      <div v-else>
                        <a-icon type="upload" />
                        <div class="ant-upload-text">Cargar foto</div>
                      </div>
                    </div>
                  </a-upload>
                  <div class="detalles-img" v-else>
                    <a-upload
                      @preview="handle_preview"
                      :file-list="file_list"
                      list-type="picture-card"
                    >
                      <div v-if="file_list.length < 1">
                        <img />
                      </div>
                    </a-upload>
                  </div>
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
                  <a-form-model-item
                    v-if="
                      action_modal === 'crear' ||
                      action_modal === 'crear_realizador'
                    "
                    :validate-status="show_error"
                    prop="codigRealiz"
                    has-feedback
                    label="Código:"
                    :help="show_used_error"
                  >
                    <a-input
                      addon-before="REAL-"
                      :default-value="codigo"
                      :disabled="action_modal === 'editar'"
                      v-model="realizadores_modal.codigRealiz"
                    />
                  </a-form-model-item>
                  <a-form-model-item
                    v-if="action_modal === 'editar'"
                    label="Código:"
                  >
                    <a-input
                      addon-before="REAL-"
                      :disabled="action_modal === 'editar'"
                      v-model="realizadores_modal.codigRealiz"
                    />
                  </a-form-model-item>
                  <a-form-model-item
                    v-if="action_modal === 'detalles'"
                    label="Código:"
                  >
                    <a-mentions
                      readonly
                      :placeholder="realizadores_modal.codigRealiz"
                    >
                    </a-mentions>
                  </a-form-model-item>
                  <a-form-model-item
                    v-if="action_modal !== 'detalles'"
                    has-feedback
                    label="Nombres y Apellidos:"
                    prop="nombreApellidosRealiz"
                  >
                    <a-input
                      :disabled="disabled"
                      v-model="realizadores_modal.nombreApellidosRealiz"
                    />
                  </a-form-model-item>
                  <a-form-model-item v-else label="Nombres y Apellidos:">
                    <a-mentions
                      readonly
                      :placeholder="realizadores_modal.nombreApellidosRealiz"
                    >
                    </a-mentions>
                  </a-form-model-item>
                  <a-form-model-item
                    v-if="action_modal !== 'detalles'"
                    has-feedback
                    label="Sexo:"
                    prop="sexoRealiz"
                  >
                    <a-select
                      :loading="loading_nomenclators"
                      :getPopupContainer="(trigger) => trigger.parentNode"
                      option-filter-prop="children"
                      show-search
                      :disabled="disabled"
                      v-model="realizadores_modal.sexoRealiz"
                    >
                      <template slot="notFoundContent">
                        <a-empty description="Sin resultados" />
                      </template>
                      <a-select-option
                        v-for="nomenclator in list_nomenclators"
                        :key="nomenclator.id"
                        :value="nomenclator.nombreTer"
                      >
                        {{ nomenclator.nombreTer }}
                      </a-select-option>
                    </a-select>
                  </a-form-model-item>
                  <a-form-model-item v-else label="Sexo:">
                    <a-mentions
                      readonly
                      :placeholder="realizadores_modal.sexoRealiz"
                    >
                    </a-mentions>
                  </a-form-model-item>
                </a-col>
                <a-col span="11" style="float: right">
                  <a-form-model-item
                    v-if="action_modal !== 'detalles'"
                    has-feedback
                    label="Descripción del Realizador:"
                    prop="descripEspRealiz"
                  >
                    <a-input
                      :disabled="disabled"
                      style="width: 100%; height: 150px; margin-top: 4px"
                      v-model="realizadores_modal.descripEspRealiz"
                      type="textarea"
                    />
                  </a-form-model-item>
                  <a-form-model-item
                    label="Descripción del Realizador:"
                    v-if="action_modal === 'detalles'"
                  >
                    <div class="description">
                      <a-mentions
                        style="margin-top: 2px"
                        readonly
                        :placeholder="realizadores_modal.descripEspRealiz"
                      >
                      </a-mentions>
                    </div>
                  </a-form-model-item>
                </a-col>
              </a-row>
            </a-form-model>
            <a-row>
              <a-button
                v-if="
                  action_modal !== 'crear' &&
                  action_modal !== 'crear_realizador'
                "
                :disabled="disabled"
                style="float: right"
                type="default"
                @click="siguiente('tab_1', '2')"
              >
                Siguiente
                <a-icon type="right" />
              </a-button>
            </a-row>
          </a-spin>
        </a-tab-pane>
        <a-tab-pane
          key="2"
          v-if="action_modal !== 'crear' && action_modal !== 'crear_realizador'"
          :disabled="tab_2"
        >
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
              :entity="realizadores_modal"
              entity_relation="realizadores"
              :vista_editar="vista_editar"
              @close_modal="show = $event"
            />
            <br />
          </div>
          <a-row>
            <a-button
              :disabled="disabled"
              style="float: left"
              type="default"
              @click="atras('1')"
            >
              <a-icon type="left" />
              Atrás
            </a-button>
          </a-row>
        </a-tab-pane>
      </a-tabs>
    </a-modal>
  </div>
</template>

<script>
export default {
  name: "modal_management_realizadores",
  props: ["action", "realizador", "realizadores_list"],
  data() {
    let validate_codig_unique = (rule, value, callback) => {
      this.realizadores_list.forEach((element) => {
        if (element.codigRealiz.substr(5) === value.replace(/ /g, "")) {
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
    let code_0000 = (rule, value, callback) => {
      if (value === "0000") {
        callback(new Error("Código inválido"));
      } else callback();
    };
    return {
      active_tab: "1",
      tab_1: false,
      tab_2: true,
      tabs_list: [],
      file_list: [],
      preview_image: "",
      valid_image: true,
      preview_visible: false,
      vista_editar: true,
      loading_nomenclators: true,
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
      realizadores_modal: {},
      disabled: false,
      activated: true,
      show_error: "",
      show_used_error: "",
      action_modal: this.action,
      codigo: "",
      list_nomenclators: [],
      rules: {
        codigRealiz: [
          {
            validator: code_required,
            trigger: "change",
          },
          {
            validator: code_0000,
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
        nombreApellidosRealiz: [
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
            pattern: "^[-üáéíóúÁÉÍÓÚñÑa-zA-Z0-9# ]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
        sexoRealiz: [
          {
            required: true,
            message: "Campo requerido",
            trigger: "change",
          },
        ],
        descripEspRealiz: [
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
    if (
      this.action_modal === "crear" ||
      this.action_modal === "crear_realizador"
    ) {
      this.codigo = this.generar_codigo(this.realizadores_list);
    }
  },
  computed: {
    active() {
      if (this.text_button === "Editar") {
        let same_photo = false;
        if (this.file_list[0]) {
          same_photo = this.file_list[0].uid !== this.realizadores_modal.id;
        }
        return (
          (same_photo || this.file_list.length === 0 || !this.compare_object) &&
          this.valid_image
        );
      } else
        return (
          this.realizadores_modal.nombreApellidosRealiz &&
          this.realizadores_modal.sexoRealiz &&
          this.valid_image
        );
    },
    /*
     *Método que compara los campos editables del producto para saber si se ha modificado
     */
    compare_object() {
      if (this.active_tab === "2") {
        return false;
      } else {
        return (
          this.realizadores_modal.nombreApellidosRealiz ===
            this.realizador.nombreApellidosRealiz &&
          this.realizadores_modal.sexoRealiz === this.realizador.sexoRealiz &&
          this.realizadores_modal.descripEspRealiz ===
            this.realizador.descripEspRealiz
        );
      }
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
      if (this.realizadores_modal.codigRealiz === undefined) {
        this.realizadores_modal.codigRealiz = this.codigo;
      }
      if (!this.used) {
        this.$refs.general_form.validate((valid) => {
          if (valid) {
            return this.confirm();
          } else {
            this.$toast.warning(
              "Hay problemas en la pestaña Generales,<br> por favor antes de continuar revísela!",
              "¡Atención!",
              {
                timeout: 4000,
                id: "question",
              }
            );
          }
        });
      }
    },
    set_action() {
      if (this.action_modal === "editar") {
        if (this.realizador.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.text_header_button = "Editar";
        this.text_button = "Editar";
        this.realizador.descripEspRealiz =
          this.realizador.descripEspRealiz === null
            ? ""
            : this.realizador.descripEspRealiz;
        this.action_cancel_title = "¿Desea cancelar la edición del Realizador?";
        this.action_title = "¿Desea guardar los cambios en el Realizador?";
        this.action_close =
          "La edición del Realizador se canceló correctamente";
        this.realizadores_modal = { ...this.realizador };
        this.realizadores_modal.codigRealiz = this.realizador.codigRealiz.substr(
          5
        );
        if (this.realizadores_modal.fotoReal !== null) {
          if (
            this.realizadores_modal.fotoReal !==
            "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png"
          ) {
            this.file_list.push({
              uid: this.realizadores_modal.id,
              name: this.realizadores_modal.fotoReal.split("/")[
                this.realizadores_modal.fotoReal.split("/").length - 1
              ],
              url: this.realizadores_modal.fotoReal,
            });
          } else
            this.file_list.push({
              uid: this.realizadores_modal.id,
              name: "Logo ver vertical_Ltr Negras.png",
              url: "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png",
            });
        }
      } else if (this.action_modal === "detalles") {
        if (this.realizador.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.text_header_button = "Detalles";
        this.text_button = "Detalles";
        this.realizador.descripEspRealiz =
          this.realizador.descripEspRealiz === null
            ? ""
            : this.realizador.descripEspRealiz;
        this.realizadores_modal = { ...this.realizador };
        if (this.realizadores_modal.fotoReal !== null) {
          if (
            this.realizadores_modal.fotoReal !==
            "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png"
          ) {
            this.file_list.push({
              uid: this.realizadores_modal.id,
              name: this.realizadores_modal.fotoReal.split("/")[
                this.realizadores_modal.fotoReal.split("/").length - 1
              ],
              url: this.realizadores_modal.fotoReal,
            });
          } else
            this.file_list.push({
              uid: this.realizadores_modal.id,
              name: "Logo ver vertical_Ltr Negras.png",
              url: "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png",
            });
        }
      } else {
        this.file_list.push({
          uid: 1,
          name: "Logo ver vertical_Ltr Negras.png",
          url: "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png",
        });
        this.text_button = "Crear";
        this.text_header_button = "Crear";
        this.action_cancel_title =
          "¿Desea cancelar la creación del Realizador?";
        this.action_title = "¿Desea crear el Realizador?";
        this.action_close =
          "La creación del Realizador se canceló correctamente";
      }
    },
    remove_image() {
      this.file_list.pop();
      this.preview_image = "";
      this.valid_image = true;
      this.$toast.success("Foto eliminada correctamente!", "¡Éxito!", {
        timeout: 2000,
      });
    },
    preview_cancel() {
      this.preview_visible = false;
    },
    handle_preview(file) {
      this.preview_image = file.url || file.thumbUrl;
      this.preview_visible = true;
    },
    handle_change({ fileList }) {
      this.file_list = fileList;
    },
    before_upload(file) {
      const isJpgOrPng =
        file.type === "image/jpeg" ||
        file.type === "image/png" ||
        file.type === "image/jpg";
      if (!isJpgOrPng) {
        this.valid_image = false;
        this.$toast.warning("Solo se pueden subir imágenes!", "¡Atención!", {
          timeout: 2000,
        });
      } else
        this.$toast.success("Foto cargado correctamente!", "¡Éxito!", {
          timeout: 2000,
        });
      return false;
    },
    confirm() {
      this.spinning = true;
      this.waiting = true;
      let form_data = this.prepare_create();
      if (this.action_modal === "editar") {
        axios
          .post(`/realizadores/editar`, form_data, {
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
              "Se ha modificado el Realizador correctamente",
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
          .post("/realizadores", form_data, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((res) => {
            this.text_button = "Creando...";
            this.spinning = false;
            this.waiting = false;
            if (this.action_modal === "crear_realizador") {
              let realizadores = [];
              axios
                .post("/realizadores/listar")
                .then((response) => {
                  let prod = response.data;
                  prod.forEach((element) => {
                    if (!element.deleted_at) {
                      realizadores.push(element);
                    }
                  });
                  this.$store.state["realizadores"].push(
                    realizadores[realizadores.length - 1]
                  );
                  this.$store.state["created_realizadores"].push(
                    realizadores[realizadores.length - 1]
                  );
                  this.$store.state["all_realizadores_statics"].push(
                    realizadores[realizadores.length - 1]
                  );
                })
                .catch((error) => {
                  console.log(error);
                });
            }
            this.$emit("actualizar");
            this.$toast.success(
              "Se ha creado el Realizador correctamente",
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
    siguiente(tab, siguienteTab) {
      if (tab === "tab_1") {
        this.$refs.general_form.validate((valid) => {
          if (valid) {
            this.tab_2 = false;
            this.tab_1 = true;
            if (this.tabs_list.indexOf(tab) === -1) {
              this.tabs_list.push(tab);
            }
            this.active_tab = siguienteTab;
          } else {
            this.$toast.warning(
              "Hay problemas en la pestaña Generales,<br> por favor antes de continuar revísela!",
              "¡Atención!",
              {
                timeout: 4000,
                id: "question",
              }
            );
          }
        });
      }
    },
    atras(tabAnterior) {
      if (this.active_tab === "2") {
        this.tab_2 = true;
        this.tab_1 = false;
        this.active_tab = tabAnterior;
      }
    },
    prepare_create() {
      if (this.realizadores_modal.descripEspRealiz === undefined) {
        this.realizadores_modal.descripEspRealiz = "";
      } else if (this.realizadores_modal.descripEspRealiz === null) {
        this.realizadores_modal.descripEspRealiz = "";
      }
      let form_data = new FormData();
      if (this.action_modal === "editar" || this.action_modal === "detalles") {
        form_data.append("id", this.realizadores_modal.id);
      }
      if (this.realizadores_modal.codigRealiz === undefined) {
        this.realizadores_modal.codigRealiz = this.codigo;
      }
      this.realizadores_modal.codigRealiz =
        "REAL-" + this.realizadores_modal.codigRealiz;
      form_data.append("codigRealiz", this.realizadores_modal.codigRealiz);
      form_data.append(
        "nombreApellidosRealiz",
        this.realizadores_modal.nombreApellidosRealiz
      );
      form_data.append(
        "descripEspRealiz",
        this.realizadores_modal.descripEspRealiz
      );
      form_data.append("sexoRealiz", this.realizadores_modal.sexoRealiz);

      if (this.file_list.length !== 0) {
        if (this.file_list[0].uid !== this.realizadores_modal.id) {
          if (this.file_list[0].name !== "Logo ver vertical_Ltr Negras.png") {
            form_data.append("fotoReal", this.file_list[0].originFileObj);
          }
        }
      } else form_data.append("img_default", true);

      this.text_button = "Creando...";
      return form_data;
    },
    //Metodos para generar el codigo
    //Este es el único método que varia de un módulo a otro
    crear_arr_codig(arr) {
      let answer = [];
      for (let i = 0; i < arr.length; i++) {
        answer.push(parseInt(arr[i].codigRealiz.substr(5, 8)));
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
        .post("/realizadores/nomencladores")
        .then((response) => {
          let i = 0;
          const length = response.data.length;
          for (i; i < length; i++) {
            this.list_nomenclators.push(response.data[i]);
          }
          this.loading_nomenclators = false;
        })
        .catch((error) => {
          this.$toast.error("Ha ocurrido un error", "¡Error!", {
            timeout: 2000,
          });
        });
    },
  },
  components: {
    tabla_audiovisuales: () => import("../Audiovisual/Tabla_Audiovisuales"),
  },
};
</script>

<style>
#modal_gestionar_realizadores .ant-mentions textarea {
  height: 32px !important;
}
#modal_gestionar_realizadores .description textarea {
  height: 150px !important;
}
</style>

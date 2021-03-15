<template>
  <div>
    <a-modal
      :closable="false"
      width="80.4%"
      :visible="show"
      id="modal_gestionar_proyectos"
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
      <!-- Aquí comienza los tabs -->
      <a-tabs :activeKey="active_tab">
        <div slot="tabBarExtraContent">{{ text_header_button }} Proyecto</div>
        <!-- Contenido del tab Generales -->
        <a-tab-pane key="1" :disabled="tab_1">
          <span slot="tab"> Generales </span>
          <!-- <span v-else slot="tab"> Generales </span> -->
          <div>
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
                :model="project_modal"
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
                          <div class="ant-upload-text">
                            Cargar Identificador
                          </div>
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
                      v-if="action_modal !== 'detalles'"
                      style="margin-right: 28% !important"
                      prop="añoProy"
                      has-feedback
                      label="Año del Proyecto"
                    >
                      <a-select
                        :loading="loading_nomenclators"
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        :disabled="disabled"
                        v-model="project_modal.añoProy"
                        option-filter-prop="children"
                        :filter-option="filter_option"
                        show-search
                      >
                        <template slot="notFoundContent">
                          <a-empty id="no-empty" description="Sin resultados" />
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
                    <a-form-model-item label="Año del Proyecto" v-else>
                      <a-mentions readonly :placeholder="project_modal.añoProy">
                      </a-mentions>
                    </a-form-model-item>
                  </a-col>
                  <a-col span="12">
                    <a-form-model-item
                      label="Código"
                      v-if="action_modal === 'detalles'"
                    >
                      <a-mentions
                        readonly
                        :placeholder="project_modal.codigProy"
                      >
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="action_modal === 'crear'"
                      :validate-status="show_error"
                      prop="codigProy"
                      has-feedback
                      label="Código"
                      :help="show_used_error"
                    >
                      <a-input
                        addon-before="PROY-"
                        :default-value="codigo"
                        :disabled="action_modal === 'editar'"
                        v-model="project_modal.codigProy"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="action_modal === 'editar'"
                      label="Código"
                    >
                      <a-input
                        addon-before="PROY-"
                        :disabled="action_modal === 'editar'"
                        :default-value="codigo"
                        v-model="project_modal.codigProy"
                      />
                    </a-form-model-item>
                    <div id="nombreProy">
                      <a-form-model-item
                        v-if="action_modal !== 'detalles'"
                        prop="nombreProy"
                        has-feedback
                        label="Nombre Completo"
                      >
                        <a-input
                          :disabled="disabled"
                          v-model="project_modal.nombreProy"
                        />
                      </a-form-model-item>
                      <a-form-model-item label="Nombre Completo" v-else>
                        <a-mentions
                          readonly
                          :placeholder="project_modal.nombreProy"
                        >
                        </a-mentions>
                      </a-form-model-item>
                    </div>
                  </a-col>
                </a-row>
                <a-row>
                  <a-col span="12">
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
                      has-feedback
                      label="Descripción en Español del Proyecto"
                      prop="descripEsp"
                    >
                      <a-input
                        style="width: 100%; height: 150px; margin-top: 4px"
                        :disabled="disabled"
                        v-model="project_modal.descripEsp"
                        type="textarea"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      label="Descripción en Español del Proyecto"
                      v-else
                    >
                      <div class="description">
                        <a-mentions
                          readonly
                          :placeholder="project_modal.descripEsp"
                        >
                        </a-mentions>
                      </div>
                    </a-form-model-item>
                  </a-col>
                  <a-col span="12">
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
                      has-feedback
                      label="Descripción en Inglés del Proyecto"
                      prop="descripIng"
                    >
                      <a-input
                        style="width: 100%; height: 150px; margin-top: 4px"
                        :disabled="disabled"
                        v-model="project_modal.descripIng"
                        type="textarea"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      label="Descripción en Inglés del Proyecto"
                      v-else
                    >
                      <div class="description">
                        <a-mentions
                          readonly
                          :placeholder="project_modal.descripIng"
                        >
                        </a-mentions>
                      </div>
                    </a-form-model-item>
                  </a-col>
                </a-row>
              </a-form-model>
              <a-row>
                <a-button
                  v-if="action_modal !== 'crear'"
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
          </div>
        </a-tab-pane>
        <!-- Fin del contenido del tab Generales -->
        <a-tab-pane key="2" v-if="action_modal !== 'crear'" :disabled="tab_2">
          <span slot="tab"> Productos </span>
          <a-col span="12">
            <div class="section-title">
              <h4>Productos del Proyecto</h4>
            </div>
          </a-col>
          <br /><br />
          <tabla_productos
            :detalles_prop="detalles"
            @reload="reload_parent"
            :entity="project_modal"
            entity_relation="proyecto"
            :vista_editar="vista_editar"
            @close_modal="show = $event"
          />
          <br />
          <a-row>
            <a-button
              v-if="action_modal !== 'crear'"
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
      <!-- Fin de los tabs -->
    </a-modal>
  </div>
</template>

<script>
import axios from "../../../config/axios/axios";
import tabla_productos from "../Producto/Tabla_Productos";
export default {
  name: "Modal_management_project",
  /*
   *action: indica si el formulario se emplea para crear o editar
   *project: es el proyecto que se pasa para editar
   */
  props: ["action", "project", "projects_list"],
  data() {
    let validate_codig_unique = (rule, value, callback) => {
      if (value !== undefined) {
        this.projects_list.forEach((element) => {
          if (element.codigProy.substr(5) === value.replace(/ /g, "")) {
            callback(new Error("Código usado"));
          }
        });
        callback();
      } else callback();
    };
    let code_required = (rule, value, callback) => {
      if (value !== undefined) {
        if (value === "") {
          callback(new Error("Campo requerido"));
        } else callback();
      } else callback();
    };
    let code_0000 = (rule, value, callback) => {
      if (value !== undefined) {
        if (value === "0000") {
          callback(new Error("El código no puede ser 0000"));
        } else callback();
      } else callback();
    };
    return {
      loading_nomenclators: true,
      active_tab: "1",
      detalles: false,
      action_cancel_title: "",
      action_title: "",
      show_used_error: "",
      used: false,
      show_error: "",
      text_header_button: "",
      valid_image: true,
      image_url: "", //*
      spinning: false, //*
      disabled: false, //*
      activated: true, //*Variable que almacena si se ejecuta o no el borrado lógico
      file_list: [], //*Variable usada para guardar la imagen subida
      tabs_list: [],
      preview_image: "", //*Variable usada para saber si ya se ha subido una imagen
      preview_visible: false, //*Variable usada para previsualizar la imagen subida a través de un modal
      list_nomenclators: [], //*Contiene los nomencladores usados en la creación de un proyecto
      text_button: "", //*Toma los valores "Crear" o "Editar" en dependencia de action
      action_modal: this.action, //*Guarda el valor de la variable action recibida por props
      project_modal: {}, //*Contiene los datos del proyecto que serán guardados en la bd
      show: true, //*Variable para mostrar u ocultar el modal
      waiting: false, //*Variable que indica que el proceso de guardar o crear no ha terminado
      vista_editar: true,
      codigo: "",
      tab_1: false,
      tab_2: true,
      rules: {
        //*reglas de validacion de los campos del formulario
        codigProy: [
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
        descripEsp: [
          {
            whitespace: true,
            message: "Espacio no válido",
            trigger: "change",
          },
          {
            pattern: "^[a-zA-Z0-9 üáéíóúÁÉÍÓÚñÑ\n,.;:¿?!¡()]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
        descripIng: [
          {
            whitespace: true,
            message: "Espacio no válido",
            trigger: "change",
          },
          {
            pattern: "^[a-zA-Z0-9'(?!),.;: \n]*$",
            message: "Caracter no válido",
            trigger: "",
          },
        ],
        nombreProy: [
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
        añoProy: {
          required: true,
          message: "Campo requerido",
          trigger: "change",
        },
      },
    };
  },
  created() {
    this.load_nomenclators(); //*Método que carga los nomencladores usados en la gestión de proyectos
    this.set_action(); //*Método que devuelve el tipo de acción que se realizará
    if (this.action_modal === "crear") {
      this.codigo = this.generar_codigo(this.projects_list);
    }
  },
  computed: {
    /*
     *Muestra u oculta el botón de crear/editar
     */
    active(tab) {
      if (this.text_button === "Editar") {
        let same_photo = false;
        if (this.file_list[0]) {
          same_photo = this.file_list[0].uid !== this.project_modal.id;
        }
        return (
          (same_photo || this.file_list.length === 0 || !this.compare_object) &&
          this.valid_image
        );
      } else
        return (
          this.project_modal.añoProy &&
          this.project_modal.nombreProy &&
          this.valid_image
        );
    },
    /*
     *Método que compara los campos editables del proyecto para saber si se ha modificado
     */
    compare_object() {
      if (this.active_tab === "2") {
        return false;
      } else {
        return (
          this.project_modal.nombreProy === this.project.nombreProy &&
          this.project_modal.añoProy === this.project.añoProy &&
          this.project_modal.descripEsp === this.project.descripEsp &&
          this.project_modal.descripIng === this.project.descripIng
        );
      }
    },
  },
  methods: {
    validate() {
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
    /*
     *Métodoo usado para cargar los nomencladores usados en la gestión de proyectos
     */
    load_nomenclators() {
      axios
        .post("/proyectos/nomencladores")
        .then((response) => {
          let i = 0;
          const length = response.data.length;
          for (i; i < length; i++) {
            this.list_nomenclators.push(response.data[i]);
          }
          this.list_nomenclators.push(response.data);
          this.loading_nomenclators = false;
        })
        .catch((error) => {
          this.$toast.error("Ha ocurrido un error", "¡Error!", {
            timeout: 2000,
          });
        });
    },
    /*
     *Realiza la creación o modificación (en dependencia
     *del valor de la variable action_modal) del proyecto guardando
     *los datos en la bd
     */
    confirm() {
      this.spinning = true;
      this.waiting = true;
      let form_data = this.prepare_create();
      if (this.action_modal === "editar") {
        this.text_button = "Editando...";
        axios
          .post(`/proyectos/editar`, form_data, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((response) => {
            this.text_button = "Editar";
            this.spinning = false;
            this.waiting = false;
            this.handle_cancel();
            this.$emit("actualizar");
            this.$toast.success(
              "El Proyecto se modificó correctamente",
              "¡Éxito!",
              { timeout: 2000 }
            );
          })
          .catch((error) => {
            this.text_button = "Editar";
            this.spinning = false;
            this.waiting = false;
            this.handle_cancel();
            this.$toast.error("Ha ocurrido un error", "¡Error!", {
              timeout: 2000,
            });
          });
      } else {
        axios
          .post("/proyectos", form_data, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((res) => {
            this.text_button = "Creando...";
            this.spinning = false;
            this.waiting = false;
            this.handle_cancel();
            this.$emit("actualizar");
            this.$toast.success(
              "El Proyecto se creó correctamente",
              "¡Éxito!",
              { timeout: 2000 }
            );
          })
          .catch((err) => {
            this.text_button = "Crear";
            this.spinning = false;
            this.waiting = false;
            this.handle_cancel();
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
            if (this.tabs_list.indexOf(tab) == -1) {
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
      if (this.project_modal.descripIng === undefined) {
        this.project_modal.descripIng = "";
      }
      if (this.project_modal.descripEsp === undefined) {
        this.project_modal.descripEsp = "";
      }
      let form_data = new FormData();
      if (this.action_modal === "editar" || this.action_modal === "detalles") {
        form_data.append("id", this.project_modal.id);
      }
      if (this.project_modal.codigProy === undefined) {
        form_data.append("codigProy", "PROY-" + this.codigo);
      } else {
        this.project_modal.codigProy = "PROY-" + this.project_modal.codigProy;
        form_data.append("codigProy", this.project_modal.codigProy);
      }
      form_data.append("añoProy", this.project_modal.añoProy);
      form_data.append("descripEsp", this.project_modal.descripEsp);
      form_data.append("descripIng", this.project_modal.descripIng);
      form_data.append("nombreProy", this.project_modal.nombreProy);
      form_data.append("empresa_id", this.project_modal.empresa_id);
      form_data.append("id", this.project_modal.id);
      form_data.append(
        "dirArbolProy",
        `/BisMusic/Proyectos/${this.project_modal.codigProy}`
      );
      if (this.file_list.length !== 0) {
        if (this.file_list[0].uid !== this.project_modal.id) {
          if (this.file_list[0].name !== "Logo ver vertical_Ltr Negras.png") {
            form_data.append(
              "identificadorProy",
              this.file_list[0].originFileObj
            );
          }
        }
      } else form_data.append("img_default", true);
      this.text_button = "Creando...";
      return form_data;
    },
    /*
     *Maneja el cierre del modal reestableciendo los valores de las variables
     *de configuración y los campos del formulario del modal a su estado inicial
     */
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
    reload_parent() {
      this.$emit("actualizar");
    },
    /*
     *Método que asigna el texto del botón y llena o no la varable project_modal en dependencia de la acción recivida por props
     */
    set_action() {
      if (this.action_modal === "editar") {
        if (this.project.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.text_header_button = "Editar";
        this.text_button = "Editar";
        this.action_cancel_title = "¿Desea cancelar la edición del Proyecto?";
        this.action_title = "¿Desea guardar los cambios en el Proyecto?";
        this.action_close = "La edición del Proyecto se canceló correctamente";
        this.project.descripEsp =
          this.project.descripEsp === null ? "" : this.project.descripEsp;
        this.project.descripIng =
          this.project.descripIng === null ? "" : this.project.descripIng;
        this.project_modal = { ...this.project };
        this.project_modal.codigProy = this.project.codigProy.substr(5);
        if (this.project_modal.identificadorProy !== null) {
          if (
            this.project_modal.identificadorProy !==
            "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png"
          ) {
            this.file_list.push({
              uid: this.project_modal.id,
              name: this.project_modal.identificadorProy.split("/")[
                this.project_modal.identificadorProy.split("/").length - 1
              ],
              url: this.project_modal.identificadorProy,
            });
          } else
            this.file_list.push({
              uid: this.project_modal.id,
              name: "Logo ver vertical_Ltr Negras.png",
              url: "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png",
            });
        }
      } else if (this.action_modal === "detalles") {
        this.detalles = true;
        if (this.project.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.text_header_button = "Detalles";
        this.project.descripEsp =
          this.project.descripEsp === null ? "" : this.project.descripEsp;
        this.project.descripIng =
          this.project.descripIng === null ? "" : this.project.descripIng;
        this.project_modal = { ...this.project };
        if (this.project_modal.identificadorProy !== null) {
          if (
            this.project_modal.identificadorProy !==
            "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png"
          ) {
            this.file_list.push({
              uid: this.project_modal.id,
              name: this.project_modal.identificadorProy.split("/")[
                this.project_modal.identificadorProy.split("/").length - 1
              ],
              url: this.project_modal.identificadorProy,
            });
          } else
            this.file_list.push({
              uid: this.project_modal.id,
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
        const date = new Date();
        this.project.añoProy = date.getFullYear();
        this.project.empresa_id = 1;
        this.project_modal = { ...this.project };
        this.text_button = "Crear";
        this.text_header_button = "Crear";
        this.action_cancel_title = "¿Desea cancelar la creación del Proyecto?";
        this.action_title = "¿Desea crear el Proyecto?";
        this.action_close = "La creación del Proyecto se canceló correctamente";
      }
    },
    remove_image() {
      this.file_list.pop();
      this.preview_image = "";
      this.valid_image = true;
      this.$toast.success("Identificador eliminado correctamente!", "¡Éxito!", {
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
        this.$toast.success("Identificador cargado correctamente!", "¡Éxito!", {
          timeout: 2000,
        });
      return false;
    },
    //Metodos para generar el codigo
    //Este es el único método que varia de un módulo a otro
    crear_arr_codig(arr) {
      let answer = [];
      for (let i = 0; i < arr.length; i++) {
        answer.push(parseInt(arr[i].codigProy.substr(5, 8)));
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
    tabla_productos,
  },
};
</script>

<style>
#no-empty .ant-empty-image svg {
  height: 100%;
  margin: auto;
  width: auto;
}
#modal_gestionar_proyectos .ant-upload-list-item,
.ant-upload-list-item-undefined,
.ant-upload-list-item-list-type-picture-card,
.ant-upload,
.ant-upload-select,
.ant-upload-select-picture-card,
.ant-upload-list-picture-card-container {
  width: 170px !important;
  height: 170px !important;
}
#modal_gestionar_proyectos .ant-select-search input {
  border-color: rgb(255, 255, 255) !important;
}
#modal_gestionar_proyectos .ant-mentions textarea {
  height: 32px !important;
}
#modal_gestionar_proyectos .description textarea {
  height: 150px !important;
}
#modal_gestionar_proyectos textarea {
  height: 150px !important;
}
#modal_gestionar_proyectos .ant-form-item-control {
  width: 80% !important;
}
</style>

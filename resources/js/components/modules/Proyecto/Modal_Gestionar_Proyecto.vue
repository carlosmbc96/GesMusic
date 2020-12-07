<template>
  <div>
    <a-modal
      :closable="false"
      width="80.4%"
      okText="Guardar"
      cancelText="Cancelar"
      cancelType="danger"
      :visible="show"
      @cancel="handle_cancel()"
      id="modal_gestionar_proyectos"
    >
      <template slot="footer">
        <a-button type="danger" key="back" @click="handle_cancel()">
          Cancelar
        </a-button>
        <a-popconfirm
          placement="bottomRight"
          @confirm="validate()"
          ok-text="Si"
          title="¿Seguro que desea guardar esta información?"
        >
          <a-button slot="cancelText" class="ant-btn ant-btn-sm cancel-text">
            No
          </a-button>
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
      <a-tabs>
        <div slot="tabBarExtraContent">{{ text_header_button }} Proyecto</div>
        <!-- Contenido del tab Generales -->
        <a-tab-pane key="1">
          <span slot="tab"> Generales </span>
          <a-spin :spinning="spinning">
            <a-icon
              slot="indicator"
              style="font-size: 30px"
              type="loading"
              spin
            />
            <div>
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
                :model="project_modal"
                :rules="rules"
              >
                <a-row>
                  <a-col span="6">
                    <a-upload
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
                      style="margin-right: 28% !important"
                      prop="añoProy"
                      has-feedback
                      label="Año del proyecto"
                    >
                      <a-select
                        :disabled="disabled"
                        v-model="project_modal.añoProy"
                        option-filter-prop="children"
                        :filter-option="filter_option"
                        show-search
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
                  </a-col>
                  <a-col span="12">
                    <a-form-model-item
                      v-if="action_modal !== 'editar'"
                      :validate-status="show_error"
                      prop="codigProy"
                      has-feedback
                      label="Código"
                      :help="show_used_error"
                    >
                      <a-input
                        :disabled="action_modal === 'editar'"
                        v-model="project_modal.codigProy"
                      />
                    </a-form-model-item>
                    <a-form-model-item v-else label="Código">
                      <a-input
                        :disabled="action_modal === 'editar'"
                        v-model="project_modal.codigProy"
                      />
                    </a-form-model-item>
                    <div id="nombreProy">
                      <a-form-model-item
                        prop="nombreProy"
                        has-feedback
                        label="Nombre Completo"
                      >
                        <a-input
                          :disabled="disabled"
                          v-model="project_modal.nombreProy"
                        />
                      </a-form-model-item>
                    </div>
                  </a-col>
                </a-row>
                <a-row>
                  <a-col span="12">
                    <a-form-model-item
                      has-feedback
                      label="Descripción en Español del proyecto"
                      prop="descripEsp"
                    >
                      <a-input
                        :disabled="disabled"
                        v-model="project_modal.descripEsp"
                        type="textarea"
                      />
                    </a-form-model-item>
                  </a-col>
                  <a-col span="12">
                    <a-form-model-item
                      has-feedback
                      label="Descripción en Inglés del proyecto"
                      prop="descripIng"
                    >
                      <a-input
                        :disabled="disabled"
                        v-model="project_modal.descripIng"
                        type="textarea"
                      />
                    </a-form-model-item>
                  </a-col>
                </a-row>
              </a-form-model>
            </div>
          </a-spin>
        </a-tab-pane>
        <!-- Fin del contenido del tab Generales -->
      </a-tabs>
      <!-- Fin de los tabs -->
    </a-modal>
  </div>
</template>

<script>
import axios from "../../../config/axios/axios";
export default {
  name: "Modal_management_project",
  /*
   *action: indica si el formulario se emplea para crear o editar
   *project: es el proyecto que se pasa para editar
   */
  props: ["action", "project", "projects_list"],
  data() {
    let validate_length = (rule, value, callback) => {
      if (value.replace(/ /g, "").length > 9) {
        callback(new Error("Máximo 9 caracteres"));
      } else callback();
    };
    let validate_codig_unique = (rule, value, callback) => {
      this.projects_list.forEach((element) => {
        if (element.codigProy === value.replace(/ /g, "")) {
          callback(new Error("Código ya usado"));
        }
      });
      callback();
    };
    return {
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
      preview_image: "", //*Variable usada para saber si ya se ha subido una imagen
      preview_visible: false, //*Variable usada para previsualizar la imagen subida a través de un modal
      list_nomenclators: [], //*Contiene los nomencladores usados en la creación de un proyecto
      text_button: "", //*Toma los valores "Crear" o "Editar" en dependencia de action
      action_modal: this.action, //*Guarda el valor de la variable action recibida por props
      project_modal: {
        empresa_id: 1,
      }, //*Contiene los datos del proyecto que serán guardados en la bd
      show: true, //*Variable para mostrar u ocultar el modal
      waiting: false, //*Variable que indica que el proceso de guardar o crear no ha terminado
      rules: {
        //*reglas de validacion de los campos del formulario
        codigProy: [
          {
            required: true,
            message: "Inserte el código",
            trigger: "change",
          },
          {
            whitespace: true,
            message: "Inserte el código",
            trigger: "change",
          },
          {
            pattern: "^[-a-zA-Z0-9 ]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
          {
            validator: validate_codig_unique,
            trigger: "change",
          },
          {
            validator: validate_length,
            trigger: "change",
          },
        ],
        descripEsp: [
          {
            whitespace: true,
            message: "Inserte una descripción",
            trigger: "change",
          },
          {
            pattern: "^[a-zA-Z0-9 üáéíóúÁÉÍÓÚñÑ,.;:]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
        descripIng: [
          {
            whitespace: true,
            message: "Inserte una descripción",
            trigger: "change",
          },
          {
            pattern: "^[a-zA-Z0-9',.;: ]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
        nombreProy: [
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
        añoProy: {
          required: true,
          message: "Seleccione un año",
          trigger: "change",
        },
      },
    };
  },
  created() {
    this.load_nomenclators(); //*Método que carga los nomencladores usados en la gestión de proyectos
    this.set_action(); //*Método que devuelve el tipo de acción que se realizará
  },
  computed: {
    /*
     *Muestra u oculta el botón de guardar
     */
    active() {
      if (this.text_button === "Editar") {
        return (
          !this.compare_object ||
          (this.valid_image && this.file_list.length !== 0 && this.file_list[0].uid !== this.project_modal.id)
        );
      } else
        return (
          this.project_modal.codigProy &&
          this.project_modal.añoProy &&
          this.project_modal.nombreProy &&
          this.valid_image
        );
    },
    /*
     *Método que compara los campos editables del proyecto para saber si se ha modificado
     */
    compare_object() {
      return (
        this.project_modal.nombreProy === this.project.nombreProy &&
        this.project_modal.añoProy === this.project.añoProy &&
        this.project_modal.descripEsp === this.project.descripEsp &&
        this.project_modal.descripIng === this.project.descripIng 
      );
    },
  },
  methods: {
    validate() {
      if (!this.used) {
        this.$refs.general_form.validate((valid) => {
          if (valid) {
            return this.confirm();
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
            this.waiting = false;
            this.$emit("actualizar");
            this.$toast.success(
              "Se ha modificado el proyecto correctamente",
              "¡Éxito!",
              { timeout: 2000 }
            );

            this.handle_cancel();
          })
          .catch((error) => {
            this.text_button = "Editar";
            this.waiting = false;
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
            this.waiting = false;
            this.$emit("actualizar");
            this.$toast.success(
              "Se ha creado el proyecto correctamente",
              "¡Éxito!",
              { timeout: 2000 }
            );
            this.handle_cancel();
          })
          .catch((err) => {
            this.text_button = "Crear";
            this.waiting = false;
            this.$toast.error("Ha ocurrido un error", "¡Error!", {
              timeout: 2000,
            });
          });
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
      if (this.action_modal === "editar") {
        form_data.append("id", this.project_modal.id);
      }
      form_data.append("codigProy", this.project_modal.codigProy);
      form_data.append("añoProy", this.project_modal.añoProy);
      form_data.append("descripEsp", this.project_modal.descripEsp);
      form_data.append("descripIng", this.project_modal.descripIng);
      form_data.append("nombreProy", this.project_modal.nombreProy);
      form_data.append("empresa_id", this.project_modal.empresa_id);
      form_data.append("id", this.project_modal.id);
      form_data.append(
        "dirArbolProy",
        `/BisMusic/Proyectos/${this.project_modal.nombreProy}`
      );
      if (this.file_list.length !== 0) {
          if (this.file_list[0].uid !== this.project_modal.id) {
            if (
              this.file_list[0].name !==
              "Logo ver vertical_Ltr Negras.png"
            ) {
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
      this.$refs.general_form.resetFields();
      this.show = false;
      this.$emit("close_modal", this.show);
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
        this.project.descripEsp =
          this.project.descripEsp === null ? "" : this.project.descripEsp;
        this.project.descripIng =
          this.project.descripIng === null ? "" : this.project.descripIng;
        this.project_modal = { ...this.project };
        if (this.project_modal.identificadorProy !== null) {
          if (
            this.project_modal.identificadorProy !==
            "/BisMusic/Proyectos/Logo ver vertical_Ltr Negras.png"
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
              uid: 1,
              name:
                "Logo ver vertical_Ltr Negras.png",
              url:
                "/BisMusic/Proyectos/Logo ver vertical_Ltr Negras.png",
            });
        }
      } else {
        this.file_list.push({
          uid: 1,
          name:
            "Logo ver vertical_Ltr Negras.png",
          url:
            "/BisMusic/Proyectos/Logo ver vertical_Ltr Negras.png",
        });
        this.text_button = "Crear";
        this.text_header_button = "Crear";
      }
    },
    remove_image() {
      this.file_list.pop();
      this.preview_image = "";
      this.valid_image = true;
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
        this.$message.error(
          "Sólo puedes subir imágenes como identificador del proyecto"
        );
      } else this.$message.success("Identificador cargado correctamente");
      return false;
    },
  },
};
</script>

<style>
#modal_gestionar_proyectos textarea {
  height: 150px !important;
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
#modal_gestionar_proyectos .ant-form-item-control {
  width: 80% !important;
}
</style>

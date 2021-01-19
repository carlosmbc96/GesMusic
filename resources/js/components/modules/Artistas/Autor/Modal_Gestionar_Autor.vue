<template>
  <div>
    <a-modal
      :closable="false"
      width="80.4%"
      okText="Guardar"
      cancelText="Cancelar"
      cancelType="danger"
      :visible="show"
      id="modal_gestionar_autores"
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
      <!-- Aquí comienzan los tabs -->
      <a-tabs>
        <div slot="tabBarExtraContent">{{ text_header_button }} Autor</div>
        <a-tab-pane key="1">
          <span slot="tab">Generales</span>
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
                :model="author_modal"
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
                          <div class="ant-upload-text">Cargar foto</div>
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
                      v-if="action_modal !== 'editar'"
                      :validate-status="show_error"
                      prop="codigAutr"
                      has-feedback
                      label="Código"
                      :help="show_used_error"
                    >
                      <a-input
                        addon-before="AUTR-"
                        placeholder="0001"
                        :disabled="action_modal === 'editar'"
                        v-model="author_modal.codigAutr"
                      />
                    </a-form-model-item>
                    <a-form-model-item v-else label="Código">
                      <a-input
                        addon-before="AUTR-"
                        placeholder="0001"
                        :disabled="action_modal === 'editar'"
                        v-model="author_modal.codigAutr"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="action_modal !== 'editar'"
                      :validate-status="show_error"
                      prop="ciAutr"
                      has-feedback
                      label="Carnet de identidad(CI)"
                      :help="show_used_error"
                    >
                      <a-input
                        :disabled="action_modal === 'editar'"
                        v-model="author_modal.ciAutr"
                      />
                    </a-form-model-item>
                    <a-form-model-item v-else label="Carnet de identidad(CI)">
                      <a-input
                        :disabled="action_modal === 'editar'"
                        v-model="author_modal.ciAutr"
                      />
                    </a-form-model-item>
                  </a-col>
                  <a-col span="6">
                    <div id="nombresAutr">
                      <a-form-model-item
                        prop="nombresAutr"
                        has-feedback
                        label="Nombre"
                      >
                        <a-input
                          :disabled="disabled"
                          v-model="author_modal.nombresAutr"
                        />
                      </a-form-model-item>
                    </div>
                    <a-form-model-item
                      has-feedback
                      label="Sexo"
                      prop="sexoAutr"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        option-filter-prop="children"
                        :disabled="disabled"
                        v-model="author_modal.sexoAutr"
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
                  <a-col span="6">
                    <div id="apellidosAutr">
                      <a-form-model-item
                        prop="apellidosAutr"
                        has-feedback
                        label="Apellidos"
                      >
                        <a-input
                          :disabled="disabled"
                          v-model="author_modal.apellidosAutr"
                        />
                      </a-form-model-item>
                    </div>
                  </a-col>
                </a-row>
                <a-row>
                  <a-col span="11">
                    <div id="resenha">
                      <a-form-model-item
                        has-feedback
                        label="Reseña biográfica del autor"
                        prop="biogAutr"
                        id="resenha"
                      >
                        <a-input
                          :disabled="disabled"
                          style="width: 100%; height: 100px"
                          v-model="author_modal.biogAutr"
                          type="textarea"
                        />
                      </a-form-model-item>
                    </div>
                  </a-col>
                  <a-col span="1"></a-col>
                  <a-col span="11">
                    <div id="checkbox" style="margin-top: 30px">
                      <a-form-model-item>
                        <a-checkbox
                          :disabled="disabled"
                          v-model="fallecidoAutr"
                          :value="fallecidoAutr"
                          style="margin-top: 20px"
                        >
                          ¿El Autor es Fallecido?
                        </a-checkbox>
                      </a-form-model-item>
                    </div>

                    <a-form-model-item>
                      <a-checkbox
                        :disabled="disabled"
                        v-model="obrasCatEditAutr"
                        :value="obrasCatEditAutr"
                      >
                        ¿El Autor tiene Obras en el Catalgo Editorial de
                        Bismusic?
                      </a-checkbox>
                    </a-form-model-item>
                  </a-col>
                </a-row>
              </a-form-model>
            </a-spin>
          </div>
        </a-tab-pane>
      </a-tabs>
    </a-modal>
  </div>
</template>

<script>
import axios from "../../../../config/axios/axios";
export default {
  props: ["action", "author", "autors_list"],
  data() {
    let validate_codig_unique = (rule, value, callback) => {
      this.autors_list.forEach((element) => {
        if (element.codigAutr.substr(5) === value.replace(/ /g, "")) {
          callback(new Error("Código ya usado"));
        }
      });
      callback();
    };
    let validate_ci_unique = (rule, value, callback) => {
      this.autors_list.forEach((element) => {
        if (element.ciAutr === value.replace(/ /g, "")) {
          callback(new Error("CI duplicado"));
        }
      });
      callback();
    };
    return {
      action_cancel_title: "",
      action_title: "",
      show: true,
      used: false,
      disabled: false,
      waiting: false,
      text_button: "",
      text_header_button: "",
      spinning: false,
      author_modal: {},
      disabled: false,
      activated: true,
      file_list: [],
      preview_image: "",
      valid_image: true,
      preview_visible: false,
      show_error: "",
      show_used_error: "",
      action_modal: this.action,
      fallecidoAutr: false,
      obrasCatEditAutr: false,
      list_nomenclators: [],
      rules: {
        codigAutr: [
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
            /* validator: validate_length,
            trigger: "change", */
          },
        ],
        ciAutr: [
          {
            required: true,
            message: "Inserte el carnet de identidad",
            trigger: "change",
          },
          {
            whitespace: true,
            message: "Inserte el carnet de identidad",
            trigger: "change",
          },
          {
            validator: validate_ci_unique,
            trigger: "change",
          },
          {
            len: 11,
            message: "Formato de 11 dígitos",
            trigger: "change",
          },
          {
            pattern: "^[0-9]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
        nombresAutr: [
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
        apellidosAutr: [
          {
            required: true,
            message: "Inserte los apellidos",
            trigger: "change",
          },
          {
            whitespace: true,
            message: "Inserte los apellidos",
            trigger: "change",
          },
          {
            pattern: "^[a-zA-Z0-9 üáéíóúÁÉÍÓÚñÑ\n,.;:¿?!¡()]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
        sexoAutr: [
          {
            required: true,
            message: "Seleccione el sexo",
            trigger: "change",
          },
        ],
        biogAutr: [
          {
            whitespace: true,
            message: "Inserte una reseña biográfica",
            trigger: "change",
          },
          {
            pattern: "^[a-zA-Z0-9 üáéíóúÁÉÍÓÚñÑ,.;:¿?!¡()]*$",
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
  },
  computed: {
    active() {
      if (this.text_button === "Editar") {
        return (
          !this.compare_object ||
          (this.valid_image &&
            this.file_list.length !== 0 &&
            this.file_list[0].uid !== this.author_modal.id)
        );
      } else
        return (
          this.author_modal.codigAutr &&
          this.author_modal.ciAutr &&
          this.author_modal.nombresAutr &&
          this.author_modal.apellidosAutr &&
          this.author_modal.sexoAutr &&
          this.valid_image
        );
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
    confirm() {
      this.spinning=true
      this.waiting = true;
      let form_data = this.prepare_create();
      if (this.action_modal === "editar") {
        this.text_button = "Editando...";
        axios
          .post(`/autores/editar`, form_data, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((response) => {
            this.text_button = "Editar";
            this.spinning=false
            this.waiting = false;
            this.handle_cancel();
            this.$emit("actualizar");
            this.$toast.success(
              "Se ha modificado el autor correctamente",
              "¡Éxito!",
              { timeout: 1000 }
            );
          })
          .catch((error) => {
            this.text_button = "Editar";
            this.spinning = false;
            this.waiting = false;
            this.$toast.error("Ha ocurrido un error", "¡Error!", {
              timeout: 1000,
            });
          });
      } else {
        axios
          .post("/autores", form_data, {
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
              "Se ha creado el autor correctamente",
              "¡Éxito!",
              { timeout: 1000 }
            );
          })
          .catch((err) => {
            this.text_button = "Crear";
            this.spinning = false;
            this.waiting = false;
            this.$toast.error("Ha ocurrido un error", "¡Error!", {
              timeout: 1000,
            });
          });
      }
    },
    prepare_create() {
      this.author_modal.fallecidoAutr = this.fallecidoAutr === false ? 0 : 1;
      this.author_modal.obrasCatEditAutr =
        this.obrasCatEditAutr === false ? 0 : 1;
      if (this.author_modal.biogAutr === undefined) {
        this.author_modal.biogAutr = "";
      }
      let form_data = new FormData();
      if (this.action_modal === "editar") {
        form_data.append("id", this.author_modal.id);
      }
      this.author_modal.codigAutr = "AUTR-" + this.author_modal.codigAutr;
      form_data.append("codigAutr", this.author_modal.codigAutr);
      form_data.append("ciAutr", this.author_modal.ciAutr);
      form_data.append("nombresAutr", this.author_modal.nombresAutr);
      form_data.append("apellidosAutr", this.author_modal.apellidosAutr);
      form_data.append("sexoAutr", this.author_modal.sexoAutr);
      form_data.append("reseñaBiogAutr", this.author_modal.biogAutr);
      form_data.append("fallecidoAutr", this.author_modal.fallecidoAutr);
      form_data.append("obrasCatEditAutr", this.author_modal.obrasCatEditAutr);
      if (this.file_list.length !== 0) {
        if (this.file_list[0].uid !== this.author_modal.id) {
          if (this.file_list[0].name !== "Logo ver vertical_Ltr Negras.png") {
            form_data.append("fotoAutr", this.file_list[0].originFileObj);
          }
        }
      } else form_data.append("img_default", true);
      this.text_button = "Creando...";
      return form_data;
    },
    set_action() {
      if (this.action_modal === "editar") {
        if (this.author.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.text_header_button = "Editar";
        this.text_button = "Editar";
        this.action_cancel_title = "¿Desea cancelar la edición del Autor?";
        this.action_title = "¿Desea guardar los cambios en el Autor?";
        this.action_close = "La edición del Autor se canceló correctamente";
        this.author.biogAutr =
          this.author.biogAutr === null ? "" : this.author.biogAutr;
        this.author.codigAutr = this.author.codigAutr.substr(5);
        this.author_modal = { ...this.author };
        if (this.author_modal.fotoAutr !== null) {
          if (
            this.author_modal.fotoAutr !==
            "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png"
          ) {
            this.file_list.push({
              uid: this.author_modal.id,
              name: this.author_modal.fotoAutr.split("/")[
                this.author_modal.fotoAutr.split("/").length - 1
              ],
              url: this.author_modal.fotoAutr,
            });
          } else
            this.file_list.push({
              uid: 1,
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
        this.action_cancel_title = "¿Desea cancelar la creación del Autor?";
        this.action_title = "¿Desea crear el Autor?";
        this.action_close = "La creación del Autor se canceló correctamente";
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
        this.$message.error("Sólo puedes subir imágenes como foto del artísta");
      } else this.$message.success("Foto cargada correctamente");
      return false;
    },
    load_nomenclators() {
      axios
        .post("/autores/nomencladores")
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
            timeout: 1000,
          });
        });
    },
  },
};
</script>

<style>
#modal_gestionar_autores .ant-upload-list-item,
.ant-upload-list-item-undefined,
.ant-upload-list-item-list-type-picture-card,
.ant-upload,
.ant-upload-select,
.ant-upload-select-picture-card,
.ant-upload-list-picture-card-container {
  width: 170px !important;
  height: 170px !important;
}
#modal_gestionar_autores .ant-select-search input {
  border-color: rgb(255, 255, 255) !important;
}
#modal_gestionar_autores textarea {
  height: 150px !important;
}
#modal_gestionar_autores .ant-form-item-control {
  width: 85%;
}
#modal_gestionar_autores #resenha .ant-form-item-control {
  width: 100% !important;
}
</style>

<template>
  <div>
    <a-modal
      :closable="false"
      width="80.4%"
      okText="Guardar"
      cancelText="Cancelar"
      cancelType="danger"
      :visible="show"
      id="modal_gestionar_audiovisuales"
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
      <a-tabs :activeKey="active_tab">
        <div slot="tabBarExtraContent">
          {{ text_header_button }} Audiovisual
        </div>
        <a-tab-pane key="1" v-if="tab_visibility">
          <span slot="tab"> Producto </span>
          <div>
            <a-form-model
              ref="formularioProduct"
              :model="audiovisual_modal"
              :rules="rules"
            >
              <a-row>
                <a-col span="12">
                  <div class="section-title">
                    <h4>Selector de Productos</h4>
                  </div>
                </a-col>
              </a-row>
              <a-col span="12">
                <a-form-model-item
                  label="Código"
                  has-feedback
                  prop="productos_audvs"
                >
                  <a-select
                    mode="multiple"
                    v-model="audiovisual_modal.productos_audvs"
                    style="width: 50% !important"
                    :disabled="disabled"
                  >
                    <a-select-option
                      v-for="product in products"
                      :key="product.codigProd"
                      :value="product.id"
                    >
                      {{ product.codigProd }}
                    </a-select-option>
                  </a-select>
                </a-form-model-item>
                <a-form-model-item label="Nombre">
                  <a-select
                    mode="multiple"
                    v-model="audiovisual_modal.productos_audvs"
                    :disabled="disabled"
                  >
                    <a-select-option
                      v-for="product in products"
                      :key="product.id"
                      :value="product.id"
                    >
                      {{ product.tituloProd }}
                    </a-select-option>
                  </a-select>
                </a-form-model-item>
              </a-col>
              <a-col span="24">
                <a-button
                  :disabled="disabled"
                  style="float: right"
                  type="default"
                  @click="siguiente('tab_1', '2')"
                >
                  Siguiente
                  <a-icon type="right" />
                </a-button>
              </a-col>
            </a-form-model>
          </div>
        </a-tab-pane>
        <a-tab-pane key="2" :disabled="tab_2">
          <span slot="tab">Generales</span>
          <a-row>
            <div>
              <a-form-model
                ref="general_form"
                layout="horizontal"
                :model="audiovisual_modal"
                :rules="rules"
              >
                <a-col span="11">
                  <div class="section-title">
                    <h4>Datos Generales</h4>
                  </div>
                  <a-col span="6" id="small">
                    <a-upload
                      :disabled="disabled"
                      :remove="remove_image"
                      @preview="handle_preview"
                      :file-list="file_list"
                      list-type="picture-card"
                      :before-upload="before_upload"
                      @change="handle_change"
                      style="margin-top: 3px"
                    >
                      <div v-if="file_list.length < 1">
                        <img v-if="preview_image" />
                        <div v-else>
                          <a-icon type="upload" />
                          <div class="ant-upload-text">
                            Cargar identificador
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
                    <a-form-model-item
                      has-feedback
                      label="Etiquetas"
                      prop="etiquetasAud"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        mode="multiple"
                        :disabled="disabled"
                        v-model="audiovisual_modal.etiquetasAud"
                      >
                        <a-select-option
                          v-for="nomenclator in etiquetas"
                          :key="nomenclator.id"
                          :value="nomenclator.nombreTer"
                        >
                          {{ nomenclator.nombreTer }}
                        </a-select-option>
                      </a-select>
                    </a-form-model-item>
                    <a-form-model-item
                      prop="fenomRefAud"
                      has-feedback
                      label="Fenómeno de Referencia"
                    >
                      <a-input
                        :disabled="disabled"
                        v-model="audiovisual_modal.fenomRefAud"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      prop="tituloAud"
                      has-feedback
                      label="Título"
                    >
                      <a-input
                        :disabled="disabled"
                        v-model="audiovisual_modal.tituloAud"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      has-feedback
                      label="Clasificación"
                      prop="clasifAud"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        option-filter-prop="children"
                        :disabled="disabled"
                        v-model="audiovisual_modal.clasifAud"
                      >
                        <a-select-option
                          v-for="nomenclator in clasificaciones"
                          :key="nomenclator.id"
                          :value="nomenclator.nombreTer"
                        >
                          {{ nomenclator.nombreTer }}
                        </a-select-option>
                      </a-select>
                    </a-form-model-item>
                    <a-form-model-item
                      has-feedback
                      label="Idiomas"
                      prop="idiomaAud"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        mode="multiple"
                        :disabled="disabled"
                        v-model="audiovisual_modal.idiomaAud"
                      >
                        <a-select-option
                          v-for="nomenclator in idiomas"
                          :key="nomenclator.id"
                          :value="nomenclator.nombreTer"
                        >
                          {{ nomenclator.nombreTer }}
                        </a-select-option>
                      </a-select>
                    </a-form-model-item>
                    <a-form-model-item
                      prop="territorioAud"
                      has-feedback
                      label="Territorio"
                    >
                      <a-input
                        :disabled="disabled"
                        v-model="audiovisual_modal.territorioAud"
                      />
                    </a-form-model-item>
                  </a-col>
                  <a-col span="6">
                    <a-form-model-item
                      v-if="action_modal !== 'editar'"
                      :validate-status="show_error"
                      prop="codigAud"
                      has-feedback
                      label="Código"
                      :help="show_used_error"
                    >
                      <a-input
                        addon-before="AUDV-"
                        placeholder="0001"
                        :disabled="action_modal === 'editar'"
                        v-model="audiovisual_modal.codigAud"
                      />
                    </a-form-model-item>
                    <a-form-model-item v-else label="Código">
                      <a-input
                        addon-before="AUDV-"
                        placeholder="0001"
                        :disabled="action_modal === 'editar'"
                        v-model="audiovisual_modal.codigAud"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="action_modal !== 'editar'"
                      :validate-status="show_error"
                      prop="isrcAud"
                      has-feedback
                      label="ISRC"
                      :help="show_used_error"
                    >
                      <a-input
                        :disabled="action_modal === 'editar'"
                        v-model="audiovisual_modal.isrcAud"
                      />
                    </a-form-model-item>
                    <a-form-model-item v-else label="ISRC">
                      <a-input
                        :disabled="action_modal === 'editar'"
                        v-model="audiovisual_modal.isrcAud"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      has-feedback
                      label="País de Grabación"
                      prop="paisGrabAud"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        option-filter-prop="children"
                        :disabled="disabled"
                        v-model="audiovisual_modal.paisGrabAud"
                      >
                        <a-select-option
                          v-for="nomenclator in paises"
                          :key="nomenclator.id"
                          :value="nomenclator.nombreTer"
                        >
                          {{ nomenclator.nombreTer }}
                        </a-select-option>
                      </a-select>
                    </a-form-model-item>
                    <a-form-model-item
                      prop="duracionAud"
                      has-feedback
                      label="Duración"
                    >
                      <a-input
                        placeholder="00:00:00"
                        :disabled="disabled"
                        v-model="audiovisual_modal.duracionAud"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      has-feedback
                      label="Género Audiovisual"
                      prop="generoAud"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        option-filter-prop="children"
                        :disabled="disabled"
                        v-model="audiovisual_modal.generoAud"
                      >
                        <a-select-option
                          v-for="nomenclator in generos"
                          :key="nomenclator.id"
                          :value="nomenclator.nombreTer"
                        >
                          {{ nomenclator.nombreTer }}
                        </a-select-option>
                      </a-select>
                    </a-form-model-item>
                    <a-form-model-item
                      has-feedback
                      label="Año de finalización"
                      prop="añoFinAud"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        option-filter-prop="children"
                        :disabled="disabled"
                        v-model="audiovisual_modal.añoFinAud"
                      >
                        <a-select-option
                          v-for="nomenclator in anhos"
                          :key="nomenclator.id"
                          :value="nomenclator.nombreTer"
                        >
                          {{ nomenclator.nombreTer }}
                        </a-select-option>
                      </a-select>
                    </a-form-model-item>
                    <a-form-model-item
                      has-feedback
                      label="Subtítulos"
                      prop="subtitulosAud"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        mode="multiple"
                        :disabled="disabled"
                        v-model="audiovisual_modal.subtitulosAud"
                      >
                        <a-select-option
                          v-for="nomenclator in subtitulos"
                          :key="nomenclator.id"
                          :value="nomenclator.nombreTer"
                        >
                          {{ nomenclator.nombreTer }}
                        </a-select-option>
                      </a-select>
                    </a-form-model-item>
                  </a-col>
                </a-col>
                <a-col span="11" style="float: right">
                  <div class="section-title">
                    <h4>Datos del Autor</h4>
                  </div>
                  <a-form-model-item
                    prop="dueñoDerchAud"
                    has-feedback
                    label="Nombre y Apellidos"
                  >
                    <a-input
                      :disabled="disabled"
                      v-model="audiovisual_modal.dueñoDerchAud"
                    />
                  </a-form-model-item>
                  <a-form-model-item
                    prop="nacioDueñoDerchAud"
                    has-feedback
                    label="Nacionalidad del Dueño de los Derechos de Audiovisual"
                  >
                    <a-select
                      :getPopupContainer="(trigger) => trigger.parentNode"
                      option-filter-prop="children"
                      :disabled="disabled"
                      v-model="audiovisual_modal.nacioDueñoDerchAud"
                    >
                      <a-select-option
                        v-for="nomenclator in nacionalidades"
                        :key="nomenclator.id"
                        :value="nomenclator.nombreTer"
                      >
                        {{ nomenclator.nombreTer }}
                      </a-select-option>
                    </a-select>
                  </a-form-model-item>
                  <a-form-model-item
                    has-feedback
                    label="Nacionalidad Dueño del Producto"
                    prop="propiedadAud"
                  >
                    <a-select
                      :getPopupContainer="(trigger) => trigger.parentNode"
                      option-filter-prop="children"
                      :disabled="disabled"
                      v-model="audiovisual_modal.propiedadAud"
                    >
                      <a-select-option
                        v-for="nomenclator in nacionalidades"
                        :key="nomenclator.id"
                        :value="nomenclator.nombreTer"
                      >
                        {{ nomenclator.nombreTer }}
                      </a-select-option>
                    </a-select>
                  </a-form-model-item>
                  <a-form-model-item>
                    <a-checkbox
                      :disabled="disabled"
                      v-model="makingOfAud"
                      :value="makingOfAud"
                    >
                      ¿El Audiovisual tiene Making-Of?
                    </a-checkbox>
                  </a-form-model-item>
                  <div class="section-title">
                    <h4>Datos Descripciones</h4>
                  </div>
                  <a-form-model-item
                    has-feedback
                    label="Descripción en español del audiovisual"
                    prop="descripEspAud"
                  >
                    <a-input
                      :disabled="disabled"
                      style="width: 100%; height: 100px"
                      v-model="audiovisual_modal.descripEspAud"
                      type="textarea"
                    />
                  </a-form-model-item>
                  <a-form-model-item
                    has-feedback
                    label="Descripción en inglés del audiovisual"
                    prop="descripIngAud"
                  >
                    <a-input
                      :disabled="disabled"
                      style="width: 100%; height: 100px"
                      v-model="audiovisual_modal.descripIngAud"
                      type="textarea"
                    />
                  </a-form-model-item>
                </a-col>
              </a-form-model>
            </div>
          </a-row>
          <a-row>
            <a-button
              :disabled="disabled"
              style="float: right"
              type="default"
              @click="siguiente('tab_2', '3')"
            >
              Siguiente
              <a-icon type="right" />
            </a-button>
            <a-button
              v-if="tab_visibility"
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
        <a-tab-pane key="3" :disabled="tab_3">
          <span slot="tab"> Elementos </span>
          <h4>Elementos</h4>
          <a-button
            :disabled="disabled"
            style="float: left"
            type="default"
            @click="atras('2')"
          >
            <a-icon type="left" />
            Atrás
          </a-button>
        </a-tab-pane>
      </a-tabs>
    </a-modal>
  </div>
</template>

<script>
import axios from "../../../config/axios/axios";
export default {
  props: ["action", "audiovisual", "audiovisuals_list"],
  data() {
    let validate_codig_unique = (rule, value, callback) => {
      this.audiovisuals_list.forEach((element) => {
        if (element.codigAud.substr(5) === value.replace(/ /g, "")) {
          callback(new Error("Código ya usado"));
        }
      });
      callback();
    };
    let validate_ISRC_unique = (rule, value, callback) => {
      this.audiovisuals_list.forEach((element) => {
        if (element.isrcAud === value) {
          callback(new Error("ISRC ya usado"));
        }
      });
      callback();
    };
    return {
      tab_2: true,
      tab_3: true,
      tabs_list: [],
      product_audiovisual_modal: {},
      active_tab: "1",
      tab_visibility: true,
      clasificaciones: [],
      generos: [],
      anhos: [],
      paises: [],
      territorios: [],
      idiomas: [],
      subtitulos: [],
      etiquetas: [],
      nacionalidades: [],
      action_cancel_title: "",
      products: [],
      action_title: "",
      show: true,
      used: false,
      disabled: false,
      waiting: false,
      text_button: "",
      text_header_button: "",
      spinning: false,
      audiovisual_modal: {},
      disabled: false,
      activated: true,
      file_list: [],
      preview_image: "",
      valid_image: true,
      preview_visible: false,
      show_error: "",
      show_used_error: "",
      action_modal: this.action,
      makingOfAud: false,
      list_nomenclators: [],
      rules: {
        codigAud: [
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
        isrcAud: [
          {
            required: true,
            message: "Campo requerido",
            trigger: "change",
          },
          {
            whitespace: true,
            message: "Inserte el ISRC",
            trigger: "change",
          },
          {
            validator: validate_ISRC_unique,
            trigger: "change",
          },
          {
            len: 12,
            message: "Formato de 12 dígitos",
            trigger: "change",
          },
          {
            pattern: "^[a-zA-Z0-9]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
        tituloAud: [
          {
            required: true,
            message: "Campo requerido",
            trigger: "change",
          },
          {
            pattern: "^[üáéíóúÁÉÍÓÚñÑa-zA-Z0-9 ]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
        clasifAud: [
          {
            required: true,
            message: "Campo requerido",
            trigger: "change",
          },
        ],
        productos_audvs: [
          {
            required: true,
            message: "Campo requerido",
            trigger: "change",
          },
        ],
        generoAud: [
          {
            required: true,
            message: "Campo requerido",
            trigger: "change",
          },
        ],
        añoFinAud: [
          {
            required: true,
            message: "Campo requerido",
            trigger: "change",
          },
        ],
        duracionAud: [
          {
            pattern: "^[0-9:]*$",
            message: "Formato 00:00:00",
            trigger: "change",
          },
        ],
        etiquetasAud: [
          {
            trigger: "change",
          },
        ],
        paisGrabAud: [
          {
            trigger: "change",
          },
        ],
        fenomRefAud: [
          {
            pattern: "^[ a-zA-ZüáéíóúÁÉÍÓÚñÑ]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
        /* idiomaAud: [
          {
            trigger: "change",
          },
        ],
        subtitulosAud: [
          {
            trigger: "change",
          },
        ], */
        territorioAud: [
          {
            pattern: "^[ a-zA-ZüáéíóúÁÉÍÓÚñÑ]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
        dueñoDerchAud: [
          {
            pattern: "^[ a-zA-Z-üáéíóúÁÉÍÓÚñÑ]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
        nacioDueñoDerchAud: [
          {
            trigger: "change",
          },
        ],
        propiedadAud: [
          {
            trigger: "change",
          },
        ],
        descripEspAud: [
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
        descripIngAud: [
          {
            whitespace: true,
            message: "Inserte una descripción",
            trigger: "change",
          },
          {
            pattern: "^[ a-zA-Z0-9',.;:\n]*$",
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
            this.file_list[0].uid !== this.audiovisual_modal.id)
        );
      } else
        return (
          this.audiovisual_modal.isrcAud &&
          this.audiovisual_modal.tituloAud &&
          this.audiovisual_modal.clasifAud &&
          this.audiovisual_modal.generoAud &&
          this.audiovisual_modal.codigAud &&
          this.audiovisual_modal.añoFinAud &&
          this.valid_image
        );
    },
  },
  methods: {
    siguiente(tab, siguienteTab) {
      if (tab === "tab_1") {
        this.$refs.formularioProduct.validate((valid) => {
          if (valid) {
            this.tab_2 = false;
            if (this.tabs_list.indexOf(tab) === -1) {
              this.tabs_list.push(tab);
            }
            this.active_tab = siguienteTab;
          }
        });
      } else if (tab === "tab_2") {
        this.$refs.general_form.validate((valid) => {
          if (valid) {
            this.tab_3 = false;
            if (this.tabs_list.indexOf(tab) == -1) {
              this.tabs_list.push(tab);
            }
            this.active_tab = siguienteTab;
          }
        });
      }
    },
    handle_cancel(e) {
      if (e === "cancelar") {
        /* if (!this.product.proyecto_id) {
          this.$refs.formularioproject.resetFields();
        } */
        if (this.tabs_list.indexOf("tab_1") !== -1) {
          this.$refs.general_form.resetFields();
        }
        this.tabs_list = [];
        this.active_tab = "1";
        this.tab_visibility = true;
        this.show = false;
        this.$emit("close_modal", this.show);
        this.$toast.success(this.action_close, "¡Éxito!", {
          timeout: 1000,
          color: "orange",
        });
      } else {
        /* if (!this.product.proyecto_id) {
          this.$refs.formularioproject.resetFields();
        } */
        if (this.tabs_list.indexOf("tab_1") !== -1) {
          this.$refs.general_form.resetFields();
        }
        this.tabs_list = [];
        this.active_tab = "1";
        this.tab_visibility = true;
        this.show = false;
        this.$emit("close_modal", this.show);
      }
    },
    validate() {
      if (!this.used) {
        if (this.tabs_list.indexOf("tab_2") !== -1) {
          this.$refs.general_form.validate((valid) => {
            if (valid) {
              if (this.file_list.length !== 0) {
                return this.confirm();
              }
            }
          });
        } else return this.confirm();
      }
    },
    atras(tabAnterior) {
      this.active_tab = tabAnterior;
    },
    confirm() {
      this.waiting = true;
      let form_data = this.prepare_create();
      if (this.action_modal === "editar") {
        this.text_button = "Editando...";
        axios
          .post(`/audiovisuales/editar`, form_data, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((response) => {
            this.text_button = "Editar";
            this.waiting = false;
            this.handle_cancel();
            this.$emit("actualizar");
            this.$toast.success(
              "Se ha modificado el audiovisual correctamente",
              "¡Éxito!",
              { timeout: 1000 }
            );
          })
          .catch((error) => {
            this.text_button = "Editar";
            this.waiting = false;
            this.$toast.error("Ha ocurrido un error", "¡Error!", {
              timeout: 1000,
            });
          });
      } else {
        this.text_button = "Creando...";
        axios
          .post("/audiovisuales", form_data, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((res) => {
            this.text_button = "Crear";
            this.waiting = false;
            this.handle_cancel();
            this.$emit("actualizar");
            this.$toast.success(
              "Se ha creado el audiovisual correctamente",
              "¡Éxito!",
              { timeout: 1000 }
            );
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
    /*
     *Métodoo usado para filtrar la búsqueda de los select
     */
    filter_option(input, option) {
      return (
        option.componentOptions.children[0].text
          .toLowerCase()
          .indexOf(input.toLowerCase()) >= 0
      );
    },
    prepare_create() {
      this.audiovisual_modal.makingOfAud = this.makingOfAud === false ? 0 : 1;
      if (this.audiovisual_modal.descripEspAud === undefined) {
        this.audiovisual_modal.descripEspAud = "";
      } else if (this.audiovisual_modal.descripEspAud === null) {
        this.audiovisual_modal.descripEspAud = "";
      }
      if (this.audiovisual_modal.descripIngAud === undefined) {
        this.audiovisual_modal.descripIngAud = "";
      } else if (this.audiovisual_modal.descripIngAud === null) {
        this.audiovisual_modal.descripIngAud = "";
      }
      if (this.audiovisual_modal.duracionAud === undefined) {
        this.audiovisual_modal.duracionAud = "";
      } else if (this.audiovisual_modal.duracionAud === null) {
        this.audiovisual_modal.duracionAud = "";
      }
      if (this.audiovisual_modal.paisGrabAud === undefined) {
        this.audiovisual_modal.paisGrabAud = "";
      } else if (this.audiovisual_modal.paisGrabAud === null) {
        this.audiovisual_modal.paisGrabAud = "";
      }
      if (this.audiovisual_modal.territorioAud === undefined) {
        this.audiovisual_modal.territorioAud = "";
      } else if (this.audiovisual_modal.territorioAud === null) {
        this.audiovisual_modal.territorioAud = "";
      }
      if (this.audiovisual_modal.idiomaAud === undefined) {
        this.audiovisual_modal.idiomaAud = "";
      } else if (this.audiovisual_modal.idiomaAud === null) {
        this.audiovisual_modal.idiomaAud = "";
      }
      if (this.audiovisual_modal.subtitulosAud === undefined) {
        this.audiovisual_modal.subtitulosAud = "";
      } else if (this.audiovisual_modal.subtitulosAud === null) {
        this.audiovisual_modal.subtitulosAud = "";
      }
      if (this.audiovisual_modal.fenomRefAud === undefined) {
        this.audiovisual_modal.fenomRefAud = "";
      } else if (this.audiovisual_modal.fenomRefAud === null) {
        this.audiovisual_modal.fenomRefAud = "";
      }
      if (this.audiovisual_modal.etiquetasAud === undefined) {
        this.audiovisual_modal.etiquetasAud = "";
      } else if (this.audiovisual_modal.etiquetasAud === null) {
        this.audiovisual_modal.etiquetasAud = "";
      }
      if (this.audiovisual_modal.dueñoDerchAud === undefined) {
        this.audiovisual_modal.dueñoDerchAud = "";
      } else if (this.audiovisual_modal.dueñoDerchAud === null) {
        this.audiovisual_modal.dueñoDerchAud = "";
      }
      if (this.audiovisual_modal.propiedadAud === undefined) {
        this.audiovisual_modal.propiedadAud = "";
      } else if (this.audiovisual_modal.propiedadAud === null) {
        this.audiovisual_modal.propiedadAud = "";
      }
      let etiquetas = "";
      let idiomas = "";
      let subtitulos = "";
      if (this.audiovisual_modal.etiquetasAud !== "") {
        this.audiovisual_modal.etiquetasAud.forEach((item) => {
          etiquetas += item + " ";
        });
        this.audiovisual_modal.etiquetasAud = etiquetas;
      }
      if (this.audiovisual_modal.idiomaAud !== "") {
        this.audiovisual_modal.idiomaAud.forEach((item) => {
          idiomas += item + " ";
        });
        this.audiovisual_modal.idiomaAud = idiomas;
      }
      if (this.audiovisual_modal.subtitulosAud !== "") {
        this.audiovisual_modal.subtitulosAud.forEach((item) => {
          subtitulos += item + " ";
        });
        this.audiovisual_modal.subtitulosAud = subtitulos;
      }
      let form_data = new FormData();
      if (this.action_modal === "editar") {
        form_data.append("id", this.audiovisual_modal.id);
      }
      this.audiovisual_modal.codigAud =
        "AUDV-" + this.audiovisual_modal.codigAud;
      form_data.append("codigAud", this.audiovisual_modal.codigAud);
      form_data.append("isrcAud", this.audiovisual_modal.isrcAud);
      form_data.append("tituloAud", this.audiovisual_modal.tituloAud);
      form_data.append("clasifAud", this.audiovisual_modal.clasifAud);
      form_data.append("generoAud", this.audiovisual_modal.generoAud);
      form_data.append("duracionAud", this.audiovisual_modal.duracionAud);
      form_data.append("añoFinAud", this.audiovisual_modal.añoFinAud);
      form_data.append("territorioAud", this.audiovisual_modal.territorioAud);
      form_data.append("idiomaAud", idiomas);
      form_data.append("subtitulosAud", subtitulos);
      form_data.append("fenomRefAud", this.audiovisual_modal.fenomRefAud);
      form_data.append("etiquetasAud", etiquetas);
      form_data.append("dueñoDerchAud", this.audiovisual_modal.dueñoDerchAud);
      form_data.append(
        "nacioDueñoDerchAud",
        this.audiovisual_modal.nacioDueñoDerchAud
      );
      form_data.append("propiedadAud", this.audiovisual_modal.propiedadAud);
      form_data.append("makingOfAud", this.audiovisual_modal.makingOfAud);
      form_data.append("descripEspAud", this.audiovisual_modal.descripEspAud);
      form_data.append("descripIngAud", this.audiovisual_modal.descripIngAud);
      form_data.append("paisGrabAud", this.audiovisual_modal.paisGrabAud);
      form_data.append("product_id", this.audiovisual_modal.productos_audvs);
      if (this.file_list.length !== 0) {
        if (this.file_list[0].uid !== this.audiovisual_modal.id) {
          if (this.file_list[0].name !== "Logo ver vertical_Ltr Negras.png") {
            form_data.append("portadillaAud", this.file_list[0].originFileObj);
          }
        }
      } else form_data.append("img_default", true);
      this.text_button = "Creando...";
      return form_data;
    },
    set_action() {
      if (this.action === "editar") {
        if (this.audiovisual.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.text_header_button = "Editar";
        this.text_button = "Editar";
        this.action_cancel_title =
          "¿Desea cancelar la edición del Audiovisual?";
        this.action_title = "¿Desea guardar los cambios en el Audiovisual?";
        this.action_close =
          "La edición del Audiovisual se canceló correctamente";
        this.audiovisual.descripEspAud =
          this.audiovisual.descripEspAud === null
            ? ""
            : this.audiovisual.descripEspAud;
        this.audiovisual.descripIngAud =
          this.audiovisual.descripIngAud === null
            ? ""
            : this.audiovisual.descripIngAud;
        this.audiovisual.productos_audvs = [];
        this.audiovisual.codigAud = this.audiovisual.codigAud.substr(5);
        this.audiovisual.productos.forEach((element) => {
          this.audiovisual.productos_audvs.push(element.id);
        });
        this.audiovisual_modal = { ...this.audiovisual };
        this.makingOfAud =
          this.audiovisual_modal.makingOfAud === 0 ? false : true;
        if (this.audiovisual_modal.etiquetasAud !== null) {
          this.audiovisual_modal.etiquetasAud = this.audiovisual_modal.etiquetasAud.split(
            " "
          );
        } else delete this.audiovisual_modal.etiquetasAud;
        if (this.audiovisual_modal.idiomaAud !== null) {
          this.audiovisual_modal.idiomaAud = this.audiovisual_modal.idiomaAud.split(
            " "
          );
        } else delete this.audiovisual_modal.idiomaAud;
        if (this.audiovisual_modal.subtitulosAud !== null) {
          this.audiovisual_modal.subtitulosAud = this.audiovisual_modal.subtitulosAud.split(
            " "
          );
        } else delete this.audiovisual_modal.subtitulosAud;
        if (this.audiovisual_modal.portadillaAud !== null) {
          if (
            this.audiovisual_modal.portadillaAud !==
            "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png"
          ) {
            this.file_list.push({
              uid: this.audiovisual_modal.id,
              name: this.audiovisual_modal.portadillaAud.split("/")[
                this.audiovisual_modal.portadillaAud.split("/").length - 1
              ],
              url: this.audiovisual_modal.portadillaAud,
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
        this.action_cancel_title =
          "¿Desea cancelar la creación del Audiovisual?";
        this.action_title = "¿Desea crear el Audiovisual?";
        this.action_close =
          "La creación del Audiovisual se canceló correctamente";
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
          "Sólo puedes subir imágenes como portadilla del audiovisual"
        );
      } else this.$message.success("Portadilla cargada correctamente");
      return false;
    },
    load_nomenclators() {
      //* Carga de productos
      axios
        .post("/productos/listar")
        .then((response) => {
          let proy = response.data;
          proy.forEach((element) => {
            if (!element.deleted_at) {
              this.products.push(element);
            }
          });
        })
        .catch((error) => {
          console.log(error);
        });
      axios
        .post("/audiovisuales/nomencladores")
        .then((response) => {
          this.list_nomenclators = response.data;
          this.clasificaciones = this.list_nomenclators[0][0];
          this.generos = this.list_nomenclators[1][0];
          this.anhos = this.list_nomenclators[2][0];
          this.paises = this.list_nomenclators[3][0];
          this.territorios = this.list_nomenclators[4][0];
          this.idiomas = this.list_nomenclators[5][0];
          this.subtitulos = this.list_nomenclators[6][0];
          this.etiquetas = this.list_nomenclators[7][0];
          this.nacionalidades = this.list_nomenclators[8][0];
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
#modal_gestionar_audiovisuales .ant-col-6 {
  width: 50% !important;
}
#modal_gestionar_audiovisuales .ant-upload-list-item,
.ant-upload-list-item-undefined,
.ant-upload-list-item-list-type-picture-card,
.ant-upload,
.ant-upload-select,
.ant-upload-select-picture-card,
.ant-upload-list-picture-card-container {
  width: 170px !important;
  height: 170px !important;
}
#modal_gestionar_audiovisuales .ant-select-search input {
  border-color: rgb(255, 255, 255) !important;
}
#modal_gestionar_audiovisuales textarea {
  height: 150px !important;
}
#small .ant-form-item-control {
  width: 85%;
}
</style>

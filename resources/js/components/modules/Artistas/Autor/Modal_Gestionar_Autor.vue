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
      <!-- Aquí comienzan los tabs -->
      <a-tabs :activeKey="active_tab">
        <div slot="tabBarExtraContent">{{ text_header_button }} Autor</div>
        <a-tab-pane key="1" :disabled="tab_1">
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
                        action_modal === 'crear_autor'
                      "
                      :validate-status="show_error"
                      prop="codigAutr"
                      has-feedback
                      label="Código"
                      :help="show_used_error"
                    >
                      <a-input
                        addon-before="AUTR-"
                        :default-value="codigo"
                        :disabled="action_modal === 'editar'"
                        v-model="author_modal.codigAutr"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="action_modal === 'editar'"
                      label="Código"
                    >
                      <a-input
                        addon-before="AUTR-"
                        :disabled="action_modal === 'editar'"
                        v-model="author_modal.codigAutr"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="action_modal === 'detalles'"
                      label="Código"
                    >
                      <a-mentions
                        readonly
                        :placeholder="author_modal.codigAutr"
                      >
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="
                        action_modal === 'crear' ||
                        action_modal === 'crear_autor'
                      "
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
                    <a-form-model-item
                      v-if="action_modal === 'editar'"
                      label="Carnet de identidad(CI)"
                    >
                      <a-input
                        :disabled="action_modal === 'editar'"
                        v-model="author_modal.ciAutr"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="action_modal === 'detalles'"
                      label="Carnet de identidad(CI)"
                    >
                      <a-mentions readonly :placeholder="author_modal.ciAutr">
                      </a-mentions>
                    </a-form-model-item>
                  </a-col>
                  <a-col span="6">
                    <div id="nombresAutr">
                      <a-form-model-item
                        v-if="action_modal !== 'detalles'"
                        prop="nombresAutr"
                        has-feedback
                        label="Nombre"
                      >
                        <a-input
                          :disabled="disabled"
                          v-model="author_modal.nombresAutr"
                        />
                      </a-form-model-item>
                      <a-form-model-item v-else label="Nombre">
                        <a-mentions
                          readonly
                          :placeholder="author_modal.nombresAutr"
                        >
                        </a-mentions>
                      </a-form-model-item>
                    </div>
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
                      has-feedback
                      label="Sexo"
                      prop="sexoAutr"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        option-filter-prop="children"
                        show-search
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
                    <a-form-model-item v-else label="Sexo">
                      <a-mentions readonly :placeholder="author_modal.sexoAutr">
                      </a-mentions>
                    </a-form-model-item>
                  </a-col>
                  <a-col span="6">
                    <div id="apellidosAutr">
                      <a-form-model-item
                        v-if="action_modal !== 'detalles'"
                        prop="apellidosAutr"
                        has-feedback
                        label="Apellidos"
                      >
                        <a-input
                          :disabled="disabled"
                          v-model="author_modal.apellidosAutr"
                        />
                      </a-form-model-item>
                      <a-form-model-item v-else label="Apellidos">
                        <a-mentions
                          readonly
                          :placeholder="author_modal.apellidosAutr"
                        >
                        </a-mentions>
                      </a-form-model-item>
                    </div>
                  </a-col>
                </a-row>
                <a-row>
                  <a-col span="11">
                    <div id="resenha">
                      <a-form-model-item
                        v-if="action_modal !== 'detalles'"
                        has-feedback
                        label="Reseña biográfica del Autor"
                        prop="reseñaBiogAutr"
                        id="resenha"
                      >
                        <a-input
                          :disabled="disabled"
                          style="width: 100%; height: 150px"
                          v-model="author_modal.reseñaBiogAutr"
                          type="textarea"
                        />
                      </a-form-model-item>
                      <a-form-model-item
                        v-else
                        label="Reseña biográfica del Autor"
                      >
                        <div class="description">
                          <a-mentions
                            readonly
                            :placeholder="author_modal.reseñaBiogAutr"
                          >
                          </a-mentions>
                        </div>
                      </a-form-model-item>
                      <div id="checkbox">
                        <a-form-model-item v-if="action_modal !== 'detalles'">
                          <a-checkbox
                            :disabled="disabled"
                            v-model="fallecidoAutr"
                            :value="fallecidoAutr"
                            style="margin-top: 20px"
                          >
                            ¿El Autor es Fallecido?
                          </a-checkbox>
                        </a-form-model-item>
                        <a-form-model-item v-else>
                          <i
                            class="fa fa-check-square-o hidden-xs"
                            v-if="author.fallecidoAutr === 1"
                          />
                          <i class="fa fa-square-o" v-else />
                          ¿El Autor es Fallecido?
                        </a-form-model-item>
                      </div>
                      <a-form-model-item v-if="action_modal !== 'detalles'">
                        <a-checkbox
                          :disabled="disabled"
                          v-model="obrasCatEditAutr"
                          :value="obrasCatEditAutr"
                        >
                          ¿El Autor tiene Obras en el Catalgo Editorial de
                          Bismusic?
                        </a-checkbox>
                      </a-form-model-item>
                      <a-form-model-item v-else>
                        <i
                          class="fa fa-check-square-o hidden-xs"
                          v-if="author.obrasCatEditAutr === 1"
                        />
                        <i class="fa fa-square-o" v-else />
                        ¿El Autor tiene Obras en el Catálgo Editorial de
                        Bismusic?
                      </a-form-model-item>
                    </div>
                  </a-col>
                  <a-col span="1"></a-col>
                  <a-col span="11">
                    <a-row
                      style="margin-top: 20px"
                      v-if="
                        action_modal === 'crear' ||
                        action_modal === 'crear_autor'
                      "
                    >
                      <a-col span="24">
                        <a-row>
                          <a-col span="24">
                            <div class="section-title">
                              <h4>Temas</h4>
                            </div>
                          </a-col>
                        </a-row>
                        <a-row>
                          <a-col span="24">
                            <a-mentions
                              readonly
                              v-if="
                                $store.getters.getTemasFormGetters.length === 0
                              "
                            ></a-mentions>
                            <div class="custom-form-item">
                              <transition-group name="list">
                                <a-form-model-item
                                  v-for="(tema, index) in $store.getters
                                    .getTemasFormGetters"
                                  :key="tema.id"
                                  v-bind="index === 0 ? formItemLayout : {}"
                                  class="list-item"
                                >
                                  <a-row>
                                    <a-col span="22">
                                      <a-mentions
                                        style="margin-top: 3px"
                                        readonly
                                        :placeholder="tema.tituloTem"
                                      ></a-mentions>
                                    </a-col>
                                    <a-col span="2" style="float: right">
                                      <a-button
                                        class="dynamic-delete-button"
                                        @click="remove_tema(tema)"
                                      >
                                        <small>
                                          <b style="vertical-align: top"> x </b>
                                        </small>
                                      </a-button>
                                    </a-col>
                                  </a-row>
                                </a-form-model-item>
                              </transition-group>
                            </div>
                            <a-row style="margin-top: 20px">
                              <a-col span="24">
                                <div class="section-title">
                                  <h5>Selector de Temas</h5>
                                </div>
                                <div class="custom-form-item">
                                  <a-form-model
                                    ref="formularioAgregarTema"
                                    :layout="'horizontal'"
                                    :model="author_modal"
                                  >
                                    <a-form-model-item>
                                      <a-select
                                        placeholder="Titulo"
                                        option-filter-prop="children"
                                        :filter-option="filter_option"
                                        show-search
                                        v-model="author_modal.temas"
                                        :disabled="disabled"
                                      >
                                        <a-select-option
                                          v-for="tema in $store.getters
                                            .getAllTemasFormGetters"
                                          :key="tema.id"
                                          :value="tema.id"
                                        >
                                          {{ tema.tituloTem }}
                                        </a-select-option>
                                      </a-select>
                                    </a-form-model-item>
                                    <a-row>
                                      <a-col span="12">
                                        <a-form-model-item
                                          v-bind="formItemLayout"
                                        >
                                          <a-button
                                            :disabled="disabled"
                                            type="primary"
                                            @click="add_tema"
                                          >
                                            <a-icon type="plus" />
                                            Agregar Tema
                                          </a-button>
                                        </a-form-model-item>
                                      </a-col>
                                      <a-col span="12">
                                        <a-form-model-item
                                          v-bind="formItemLayout"
                                          style="float: right"
                                        >
                                          <a-button
                                            :disabled="disabled"
                                            type="primary"
                                            @click="new_tema"
                                          >
                                            <a-icon type="plus" />
                                            Crear Tema
                                          </a-button>
                                        </a-form-model-item>
                                      </a-col>
                                    </a-row>
                                  </a-form-model>
                                </div>
                              </a-col>
                            </a-row>
                          </a-col>
                        </a-row>
                      </a-col>
                    </a-row>
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
        <a-tab-pane
          key="2"
          v-if="action_modal !== 'crear' && action_modal !== 'crear_autor'"
          :disabled="tab_2"
        >
          <span slot="tab"> Audiovisuales/Temas </span>
          <a-row>
            <a-col span="12">
              <div class="section-title">
                <h4>Audiovisuales/Temas</h4>
              </div>
            </a-col>
          </a-row>

          <div>
            <a-steps :current="current" @change="onChange">
              <a-step
                v-for="item in steps"
                :key="item.title"
                :title="item.title"
              />
            </a-steps>
            <br />
            <div>
              <tabla_audiovisuales
                v-if="current === 0"
                :detalles_prop="detalles_autor"
                @reload="reload_parent"
                :entity="author_modal"
                entity_relation="autores"
                :vista_editar="vista_editar"
                @close_modal="show = $event"
              />
              <tabla_temas
                v-else
                :detalles_prop="detalles"
                @reload="reload_parent"
                :entity="author_modal"
                entity_relation="autores"
                :vista_editar="vista_editar"
                @close_modal="show = $event"
              />
            </div>
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

    <modal_management_temas
      v-if="visible_management_tema && tracks_not_empty"
      :action="action_management_temas"
      @close_modal="visible_management_tema = $event"
      :temas_list="$store.getters.getAllTemasStaticsFormGetters"
    />
    <help
      @close="reset"
      v-if="show_help"
      :content="content"
      :type="type"
    ></help>
  </div>
</template>

<script>
import help from "../../Help";
export default {
  props: ["action", "author", "autors_list"],
  data() {
    let validate_codig_unique = (rule, value, callback) => {
      this.autors_list.forEach((element) => {
        if (element.codigAutr.substr(5) === value.replace(/ /g, "")) {
          callback(new Error("Código usado"));
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
    let code_required = (rule, value, callback) => {
      if (value === "") {
        callback(new Error("Inserte el código"));
      } else callback();
    };
    return {
      steps: [
        {
          title: "Audiovisuales",
        },
        {
          title: "Temas",
        },
      ],
      tracks_not_empty: true,
      show_help: false,
      active_tab: "1",
      tab_1: false,
      tab_2: true,
      content: "",
      type: "",
      current: 0,
      detalles: false,
      detalles_autor: true,
      vista_editar: true,
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
      tabs_list: [],
      preview_image: "",
      valid_image: true,
      preview_visible: false,
      show_error: "",
      show_used_error: "",
      action_modal: this.action,
      fallecidoAutr: false,
      obrasCatEditAutr: false,
      list_nomenclators: [],
      action_management_temas: "crear_tema",
      visible_management_tema: false,
      codigo: "",
      formItemLayout: {
        wrapperCol: {
          xs: { span: 24, offset: 0 },
          sm: { span: 20, offset: 4 },
        },
      },
      rules: {
        codigAutr: [
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
        ciAutr: [
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
            message: "Campo requerido",
            trigger: "change",
          },
          {
            whitespace: true,
            message: "Espacio no válido",
            trigger: "change",
          },
          {
            pattern: "^[-üáéíóúÁÉÍÓÚñÑa-zA-Z ]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
        apellidosAutr: [
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
            pattern: "^[-üáéíóúÁÉÍÓÚñÑa-zA-Z ]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
        ],
        sexoAutr: [
          {
            required: true,
            message: "Campo reuqerido",
            trigger: "change",
          },
        ],
        reseñaBiogAutr: [
          {
            whitespace: true,
            message: "Espacio no válido",
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
    if (this.action_modal === "crear" || this.action_modal === "crear_autor") {
      this.codigo = this.generar_codigo(this.autors_list);
    }
  },
  computed: {
    active() {
      if (this.text_button === "Editar") {
        let same_photo = false;
        if (this.file_list[0]) {
          same_photo = this.file_list[0].uid !== this.author_modal.id;
        }
        return (
          (same_photo || this.file_list.length === 0 || !this.compare_object) &&
          this.valid_image
        );
      } else
        return (
          this.author_modal.ciAutr &&
          this.author_modal.nombresAutr &&
          this.author_modal.apellidosAutr &&
          this.author_modal.sexoAutr &&
          this.valid_image
        );
    },
    /*
     *Método que compara los campos editables del producto para saber si se ha modificado
     */
    compare_object() {
      this.author_modal.fallecidoAutr = this.fallecidoAutr === true ? 1 : 0;
      this.author_modal.obrasCatEditAutr =
        this.obrasCatEditAutr === true ? 1 : 0;
      if (this.active_tab === "2") {
        return false;
      } else {
        return (
          this.author_modal.nombresAutr === this.author.nombresAutr &&
          this.author_modal.apellidosAutr === this.author.apellidosAutr &&
          this.author_modal.sexoAutr === this.author.sexoAutr &&
          this.author_modal.fallecidoAutr === this.author.fallecidoAutr &&
          this.author_modal.obrasCatEditAutr === this.author.obrasCatEditAutr &&
          this.author_modal.reseñaBiogAutr === this.author.reseñaBiogAutr
        );
      }
    },
  },
  methods: {
    reset() {
      this.show_help = false;
      this.content = "";
      this.type = "";
    },
    filter_option(input, option) {
      return (
        option.componentOptions.children[0].text
          .toLowerCase()
          .indexOf(input.toLowerCase()) >= 0
      );
    },
    onChange(current) {
      this.current = current;
    },
    reload_parent() {
      this.$emit("refresh");
    },
    handle_cancel(e) {
      if (e === "cancelar") {
        this.$refs.general_form.resetFields();
        this.show = false;
        if (this.$store.getters.getCreatedTemasFormGetters.length !== 0) {
          for (
            let index = 0;
            index < this.$store.getters.getCreatedTemasFormGetters.length;
            index++
          ) {
            axios
              .delete(
                `temas/eliminar/${this.$store.getters.getCreatedTemasFormGetters[index].id}`
              )
              .then((ress) => {})
              .catch((err) => {
                console.log(err);
                this.$toast.error("Ha ocurrido un error", "¡Error!", {
                  timeout: 2000,
                });
              });
          }
        }
        this.$store.state["temas"] = [];
        this.$store.state["created_temas"] = [];
        this.$store.state["all_temas"] = [];
        this.$store.state["all_temas_statics"] = [];
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
      if (this.author_modal.codigAutr === undefined) {
        this.author_modal.codigAutr = this.codigo;
      }
      if (!this.used) {
        this.$refs.general_form.validate((valid) => {
          if (valid) {
            return this.confirm();
          } else {
            this.$message.warning(
              "Hay problemas en la pestaña Generales, por favor antes de continuar revísela!",
              4
            );
          }
        });
      }
    },
    confirm() {
      this.spinning = true;
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
            this.spinning = false;
            this.waiting = false;
            this.handle_cancel();
            this.$emit("actualizar");
            this.$toast.success(
              "Se ha modificado el Autor correctamente",
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
          .post("/autores", form_data, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((res) => {
            axios
              .post("/autores/temas", {
                temas: this.getTemasID(),
                id: res.data.id,
              })
              .then((res) => {
                this.$store.state["temas"] = [];
                this.$store.state["all_temas_statics"] = [];
                this.$store.state["all_temas"] = [];
                this.$store.state["created_temas"] = [];
              })
              .catch((error) => {
                this.$toast.error("Ha ocurrido un error", "¡Error!", {
                  timeout: 2000,
                });
              });
            if (this.action_modal === "crear_autor") {
              let autores = [];
              axios
                .post("/autores/listar")
                .then((response) => {
                  let prod = response.data;
                  prod.forEach((element) => {
                    if (!element.deleted_at) {
                      autores.push(element);
                    }
                  });
                  this.$store.state["autores"].push(
                    autores[autores.length - 1]
                  );
                  this.$store.state["created_autores"].push(
                    autores[autores.length - 1]
                  );
                  this.$store.state["all_autores_statics"].push(
                    autores[autores.length - 1]
                  );
                })
                .catch((error) => {
                  console.log(error);
                });
            }
            this.text_button = "Creando...";
            this.spinning = false;
            this.waiting = false;
            this.handle_cancel();
            this.$emit("actualizar");
            this.$toast.success(
              "Se ha creado el Autor correctamente",
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
    atras(tabAnterior) {
      if (this.active_tab === "2") {
        this.tab_2 = true;
        this.tab_1 = false;
        this.active_tab = tabAnterior;
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
            this.$message.warning(
              "Hay problemas en la pestaña Generales, por favor antes de continuar revísela!",
              4
            );
          }
        });
      }
    },
    prepare_create() {
      this.author_modal.fallecidoAutr = this.fallecidoAutr === false ? 0 : 1;
      this.author_modal.obrasCatEditAutr =
        this.obrasCatEditAutr === false ? 0 : 1;
      if (this.author_modal.reseñaBiogAutr === undefined) {
        this.author_modal.reseñaBiogAutr = "";
      }
      let form_data = new FormData();
      if (this.action_modal === "editar" || this.action_modal === "detalles") {
        form_data.append("id", this.author_modal.id);
      }
      if (this.author_modal.codigAutr === undefined) {
        this.author_modal.codigAutr = this.codigo;
      }
      this.author_modal.codigAutr = "AUTR-" + this.author_modal.codigAutr;
      form_data.append("codigAutr", this.author_modal.codigAutr);
      form_data.append("ciAutr", this.author_modal.ciAutr);
      form_data.append("nombresAutr", this.author_modal.nombresAutr);
      form_data.append("apellidosAutr", this.author_modal.apellidosAutr);
      form_data.append("sexoAutr", this.author_modal.sexoAutr);
      form_data.append("reseñaBiogAutr", this.author_modal.reseñaBiogAutr);
      form_data.append("fallecidoAutr", this.author_modal.fallecidoAutr);
      form_data.append("obrasCatEditAutr", this.author_modal.obrasCatEditAutr);
      if (this.author_modal.audiovisuales_autrs) {
        this.relation = "audiovisuales";
        form_data.append("type_relation", this.relation);
        form_data.append(
          "audiovisual_id",
          this.author_modal.audiovisuales_autrs
        );
      } else if (this.author_modal.tracks_autrs) {
        this.relation = "tracks";
        form_data.append("type_relation", this.relation);
        form_data.append("track_id", this.author_modal.tracks_autrs);
      } else if (this.author_modal.temas_autrs) {
        this.relation = "temas";
        form_data.append("type_relation", this.relation);
        form_data.append("tema_id", this.author_modal.temas_autrs);
      }

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
        this.author.reseñaBiogAutr =
          this.author.reseñaBiogAutr === null ? "" : this.author.reseñaBiogAutr;
        this.author_modal = { ...this.author };
        this.author_modal.codigAutr = this.author.codigAutr.substr(5);
        this.fallecidoAutr =
          this.author_modal.fallecidoAutr === 0 ? false : true;
        this.obrasCatEditAutr =
          this.author_modal.obrasCatEditAutr === 0 ? false : true;
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
              uid: this.author_modal.id,
              name: "Logo ver vertical_Ltr Negras.png",
              url: "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png",
            });
        }
      } else if (this.action_modal === "detalles") {
        this.detalles = true;
        if (this.author.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.text_header_button = "Detalles";
        this.text_button = "Detalles";
        this.author.reseñaBiogAutr =
          this.author.reseñaBiogAutr === null ? "" : this.author.reseñaBiogAutr;
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
              uid: this.author_modal.id,
              name: "Logo ver vertical_Ltr Negras.png",
              url: "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png",
            });
        }
      } else {
        this.author_modal = { ...this.author };
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
        })
        .catch((error) => {
          this.$toast.error("Ha ocurrido un error", "¡Error!", {
            timeout: 2000,
          });
        });
      axios
        .post("/temas/listar")
        .then((response) => {
          let prod = response.data;
          prod.forEach((element) => {
            if (!element.deleted_at) {
              if (this.action_modal === "crear") {
                this.$store.state["all_temas"].push(element);
              }
              this.$store.state["all_temas_statics"].push(element);
            }
          });
        })
        .catch((error) => {
          console.log(error);
        });
    },
    //Metodos para generar el codigo
    //Este es el único método que varia de un módulo a otro
    crear_arr_codig(arr) {
      let answer = [];
      for (let i = 0; i < arr.length; i++) {
        answer.push(parseInt(arr[i].codigAutr.substr(5, 8)));
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

    add_tema() {
      if (this.author_modal.temas !== undefined) {
        this.$store.state["temas"].push(
          this.get_tema(this.author_modal.temas).tema
        );
        this.$store.state["all_temas"].splice(
          this.get_tema(this.author_modal.temas).index,
          1
        );
        this.author_modal.temas = undefined;
      }
    },

    remove_tema(item) {
      let index = this.$store.getters.getTemasFormGetters.indexOf(item);
      this.$store.state["temas"].splice(index, 1);
      this.$store.state["all_temas"].push(item);
    },

    new_tema() {
      axios.post("/tracks/listar").then((response) => {
        if (response.data.length === 0) {
          this.content =
            "No se puede crear Temas sin Tracks existentes, vaya al módulo de Tracks y cree al menos un Track!";
          this.type = "info";
          this.show_help = true;
          this.tracks_not_empty = false;
        } else this.visible_management_tema = true;
      });
    },

    get_tema(id) {
      for (
        let index = 0;
        index < this.$store.getters.getAllTemasFormGetters.length;
        index++
      ) {
        if (this.$store.getters.getAllTemasFormGetters[index].id === id) {
          return {
            tema: this.$store.getters.getAllTemasFormGetters[index],
            index: index,
          };
        }
      }
      return -1;
    },

    getTemasID() {
      let answer = [];
      let all_temas = this.$store.getters.getTemasFormGetters;
      for (let index = 0; index < all_temas.length; index++) {
        answer.push(all_temas[index].id);
      }
      return answer;
    },
  },
  components: {
    help,
    tabla_audiovisuales: () => import("../../Audiovisual/Tabla_Audiovisuales"),
    tabla_temas: () => import("../../Tema/Tabla_Temas"),
    modal_management_temas: () => import("../../Tema/Modal_Gestionar_Tema"),
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
#modal_gestionar_autores .custom-form-item .ant-form-item-control {
  width: 100% !important;
}
#modal_gestionar_autores #resenha .ant-form-item-control {
  width: 100% !important;
}
#modal_gestionar_autores .ant-mentions textarea {
  height: 32px !important;
}
#modal_gestionar_autores .description textarea {
  height: 150px !important;
}
#modal_gestionar_autores .ant-col-sm-offset-4 {
  margin-left: 0px !important;
}
#modal_gestionar_autores .dynamic-delete-button {
  cursor: pointer;
  position: relative;
  margin-left: 4px;
  padding: 0 8px;
  top: 2px;
  font-size: 18px;
  color: white;
  background-color: rgb(243, 107, 100);
  transition: all 0.3s;
}
#modal_gestionar_autores .ant-col-sm-20 {
  width: 100%;
}
</style>

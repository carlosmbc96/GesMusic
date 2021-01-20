<template>
  <div>
    <a-modal
      :closable="false"
      width="80.4%"
      okText="Guardar"
      cancelText="Cancelar"
      :visible="show"
      id="modal_gestionar_fonogramas"
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
          <a-button type="danger" key="back" v-if="action_modal !== 'detalles'">
            Cancelar
          </a-button>
          <a-button type="primary" key="back" v-else> Aceptar </a-button>
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
      <!-- Tabs -->
      <a-tabs :activeKey="active_tab">
        <div slot="tabBarExtraContent">{{ text_header_button }} Fonograma</div>
        <!-- Tab 1 -->
        <a-tab-pane
          key="1"
          v-if="tab_visibility && action_modal !== 'detalles'"
        >
          <span slot="tab"> Producto </span>
          <div>
            <a-spin :spinning="spinning">
              <a-form-model
                ref="formularioProducto"
                :model="fonogram_modal"
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
                    prop="productos_fongs"
                  >
                    <a-select
                      mode="multiple"
                      v-model="fonogram_modal.productos_fongs"
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
                      v-model="fonogram_modal.productos_fongs"
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
            </a-spin>
          </div>
        </a-tab-pane>
        <!-- Tab 2 -->
        <a-tab-pane key="2" :disabled="tab_2">
          <span slot="tab"> Generales </span>
          <!-- Datos Generales -->
          <a-spin :spinning="spinning">
            <a-form-model
              layout="vertical"
              ref="formularioGenerales"
              :model="fonogram_modal"
              :rules="rules"
            >
              <a-row>
                <a-col span="11">
                  <div class="section-title">
                    <h4>Datos Generales</h4>
                  </div>
                  <a-row>
                    <a-col span="11">
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
                    <a-col span="11" style="float: right">
                      <a-form-model-item
                        v-if="action_modal !== 'editar'"
                        :validate-status="show_error"
                        :help="show_used_error"
                        prop="codigFong"
                        has-feedback
                        label="Código"
                      >
                        <a-input
                          addon-before="FONG-"
                          :default-value="codigo"
                          :disabled="
                            action_modal === 'editar' ||
                              action_modal === 'detalles'
                          "
                          v-model="fonogram_modal.codigFong"
                        />
                      </a-form-model-item>
                      <a-form-model-item v-else label="Código">
                        <a-input
                          addon-before="Fong-"
                          :disabled="action_modal === 'editar'"
                          v-model="fonogram_modal.codigFong"
                        />
                      </a-form-model-item>
                      <a-form-model-item
                        prop="tituloFong"
                        has-feedback
                        label="Título"
                      >
                        <a-input
                          :disabled="disabled"
                          v-model="fonogram_modal.tituloFong"
                        />
                      </a-form-model-item>
                      <a-form-model-item
                        has-feedback
                        label="Clasificación del Fonograma"
                        prop="clasficacionFong"
                      >
                        <a-select
                          :getPopupContainer="(trigger) => trigger.parentNode"
                          option-filter-prop="children"
                          :filter-option="filter_option"
                          show-search
                          :disabled="disabled"
                          v-model="fonogram_modal.clasficacionFong"
                        >
                          <a-select-option
                            v-for="nomenclator in clasficaciones"
                            :key="nomenclator.id"
                            :value="nomenclator.nombreTer"
                          >
                            {{ nomenclator.nombreTer }}
                          </a-select-option>
                        </a-select>
                      </a-form-model-item>
                      <a-form-model-item
                        has-feedback
                        label="Nacionalidad del dueño del producto"
                        prop="propiedadFong"
                      >
                        <a-select
                          :getPopupContainer="(trigger) => trigger.parentNode"
                          option-filter-prop="children"
                          :filter-option="filter_option"
                          show-search
                          :disabled="disabled"
                          v-model="fonogram_modal.propiedadFong"
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
                    </a-col>
                  </a-row>
                </a-col>
                <a-col span="11" style="float: right">
                  <div class="section-title">
                    <h4>Datos del dueño de los derechos del fonograma</h4>
                  </div>
                  <a-row style="margin-bottom: 30px">
                    <a-form-model-item
                      prop="dueñoDerchFong"
                      has-feedback
                      label="Nombre completo"
                    >
                      <a-input
                        :disabled="disabled"
                        v-model="fonogram_modal.dueñoDerchFong"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      has-feedback
                      label="Nacionalidad"
                      prop="nacioDueñoDerchFong"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        option-filter-prop="children"
                        :filter-option="filter_option"
                        show-search
                        :disabled="disabled"
                        v-model="fonogram_modal.nacioDueñoDerchFong"
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
                      has-feedback
                      label="Territorio del fonograma"
                      prop="territorioFong"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        option-filter-prop="children"
                        :filter-option="filter_option"
                        show-search
                        :disabled="disabled"
                        v-model="fonogram_modal.territorioFong"
                      >
                        <a-select-option
                          v-for="nomenclator in territorios"
                          :key="nomenclator.id"
                          :value="nomenclator.nombreTer"
                        >
                          {{ nomenclator.nombreTer }}
                        </a-select-option>
                      </a-select>
                    </a-form-model-item>
                  </a-row>
                </a-col>
              </a-row>
              <div>
                <a-row>
                  <div class="section-title">
                    <h4>Datos Descripciones</h4>
                  </div>
                  <a-row>
                    <a-col span="11">
                      <a-form-model-item
                        has-feedback
                        label="Descripción en español del fonograma"
                        prop="descripEspFong"
                      >
                        <a-input
                          :disabled="disabled"
                          style="width: 100%; height: 100px"
                          v-model="fonogram_modal.descripEspFong"
                          type="textarea"
                        />
                      </a-form-model-item>
                    </a-col>
                    <a-col span="11" style="float: right">
                      <a-form-model-item
                        has-feedback
                        label="Descripción en inglés del fonograma"
                        prop="descripIngFong"
                      >
                        <a-input
                          :disabled="disabled"
                          style="width: 100%; height: 100px"
                          v-model="fonogram_modal.descripIngFong"
                          type="textarea"
                        />
                      </a-form-model-item>
                    </a-col>
                  </a-row>
                </a-row>
              </div>
              <a-button
                style="float: right"
                type="default"
                @click="siguiente('tab_2', '3')"
              >
                Siguiente
                <a-icon type="right" />
              </a-button>
              <a-button
                v-if="tab_visibility && action_modal !== 'detalles'"
                style="float: left"
                type="default"
                @click="atras('1')"
              >
                <a-icon type="left" />
                Atrás
              </a-button>
            </a-form-model>
          </a-spin>
        </a-tab-pane>
        <!-- Tab 3 -->
        <a-tab-pane key="3" :disabled="tab_3">
          <span slot="tab"> Repertorio </span>
          <!-- <a-button
            :disabled="disabled"
            style="float: right"
            type="default"
            @click="siguiente('tab_3', '4')"
          >
            Siguiente
            <a-icon type="right" />
          </a-button>
          <a-button
            :disabled="disabled"
            style="float: left"
            type="default"
            @click="atras('2')"
          >
            <a-icon type="left" />
            Atrás
          </a-button> -->
        </a-tab-pane>
        <!-- Tab 4 -->
        <a-tab-pane key="4" :disabled="tab_4">
          <span slot="tab"> Elementos </span>
          <h4>Elementos</h4>
          <a-button
            :disabled="disabled"
            style="float: left"
            type="default"
            @click="atras('3')"
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
  export default {
    props: ['action', 'fonogram', 'fonograms_list'],
    data() {
      let validate_codig_unique = (rule, value, callback) => {
        if (value !== undefined) {
          this.fonograms_list.forEach((element) => {
            if (element.codigFong.substr(5) === value.replace(/ /g, '')) {
              callback(new Error('Código ya usado'));
            }
          });
        }
        callback();
      };
      let code_required = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('Inserte el código'));
        } else callback();
      };
      return {
        tab_2: true,
        tab_3: true,
        tab_4: true,
        tabs_list: [],
        active_tab: '1',
        tab_visibility: true,
        clasficaciones: [],
        paises: [],
        territorios: [],
        action_cancel_title: '',
        products: [],
        action_title: '',
        show: true,
        used: false,
        disabled: false,
        waiting: false,
        text_button: '',
        text_header_button: '',
        spinning: false,
        fonogram_modal: {},
        disabled: false,
        activated: true,
        file_list: [],
        preview_image: '',
        valid_image: true,
        preview_visible: false,
        show_error: '',
        show_used_error: '',
        action_modal: this.action,
        list_nomenclators: [],
        codigo: '',
        rules: {
          codigFong: [
            {
              validator: code_required,
              trigger: 'change',
            },
            {
              whitespace: true,
              message: 'Espacio no válido',
              trigger: 'change',
            },
            {
              pattern: '^[0-9]*$',
              message: 'Solo dígitos',
              trigger: 'change',
            },
            {
              validator: validate_codig_unique,
              trigger: 'change',
            },
            {
              len: 4,
              message: 'Formato de 4 dígitos',
              trigger: 'change',
            },
          ],
          tituloFong: [
            {
              required: true,
              message: 'Campo requerido',
              trigger: 'change',
            },
            {
              pattern: '^[üáéíóúÁÉÍÓÚñÑa-zA-Z0-9 ]*$',
              message: 'Caracter no válido',
              trigger: 'change',
            },
          ],
          productos_fongs: [
            {
              required: true,
              message: 'Campo requerido',
              trigger: 'change',
            },
          ],
          descripEspFong: [
            {
              whitespace: true,
              message: 'Inserte una descripción',
              trigger: 'change',
            },
            {
              pattern: '^[ a-zA-Z0-9üáéíóúÁÉÍÓÚñÑ,.;:¿?!¡()\n]*$',
              message: 'Caracter no válido',
              trigger: 'change',
            },
          ],
          descripIngFong: [
            {
              whitespace: true,
              message: 'Inserte una descripción',
              trigger: 'change',
            },
            {
              pattern: "^[ a-zA-Z0-9',.;:\n]*$",
              message: 'Caracter no válido',
              trigger: 'change',
            },
          ],
        },
      };
    },
    created() {
      this.load_nomenclators();
      this.set_action();
      if (this.action_modal === 'crear') {
        this.codigo = this.generar_codigo(this.fonograms_list);
      } else if (this.action_modal === 'detalles') {
        this.active_tab = '2';
        this.disabled = true;
      }
    },
    computed: {
      active() {
        if (this.text_button === 'Editar') {
          return (
            !this.compare_object ||
            (this.valid_image &&
              this.file_list.length !== 0 &&
              this.file_list[0].uid !== this.fonogram_modal.id)
          );
        } else return this.fonogram_modal.tituloFong && this.valid_image;
      },
    },
    methods: {
      siguiente(tab, siguienteTab) {
        if (tab === 'tab_1') {
          this.$refs.formularioProducto.validate((valid) => {
            if (valid) {
              this.tab_2 = false;
              if (this.tabs_list.indexOf(tab) === -1) {
                this.tabs_list.push(tab);
              }
              this.active_tab = siguienteTab;
            }
          });
        } else if (tab === 'tab_2') {
          if (this.action_modal !== 'detalles') {
            this.$refs.formularioGenerales.validate((valid) => {
              if (valid) {
                this.tab_3 = false;
                if (this.tabs_list.indexOf(tab) == -1) {
                  this.tabs_list.push(tab);
                }
                this.active_tab = siguienteTab;
              }
            });
          } else {
            this.tab_3 = false;
            if (this.tabs_list.indexOf(tab) == -1) {
              this.tabs_list.push(tab);
            }
            this.active_tab = siguienteTab;
          }
        }
      },
      handle_cancel(e) {
				if (this.action_modal === "detalles") {
					this.fonogram.codigFong = "FONG-" + this.fonogram.codigFong;
				}
        if (e === 'cancelar') {
          if (this.tabs_list.indexOf('tab_1') !== -1) {
            this.$refs.formularioGenerales.resetFields();
          }
          this.tabs_list = [];
          this.active_tab = '1';
          this.tab_visibility = true;
          this.show = false;
          this.$emit('close_modal', this.show);
          this.$toast.success(this.action_close, '¡Éxito!', {
            timeout: 1000,
            color: 'orange',
          });
        } else {
          if (this.tabs_list.indexOf('tab_1') !== -1) {
            this.$refs.formularioGenerales.resetFields();
          }
          this.tabs_list = [];
          this.active_tab = '1';
          this.tab_visibility = true;
          this.show = false;
          this.$emit('close_modal', this.show);
        }
      },
      validate() {
        if (this.fonogram_modal.codigFong === undefined) {
          this.fonogram_modal.codigFong = this.codigo;
        }
        if (!this.used) {
          if (this.tabs_list.indexOf('tab_1') !== -1) {
            this.$refs.formularioGenerales.validate((valid) => {
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
        this.spinning = true;
        this.waiting = true;
        let form_data = this.prepare_create();
        if (this.action_modal === 'editar') {
          this.text_button = 'Editando...';
          axios
            .post(`/fonogramas/editar`, form_data, {
              headers: {
                'Content-Type': 'multipart/form-data',
              },
            })
            .then((response) => {
              this.text_button = 'Editar';
              this.spinning = false;
              this.waiting = false;
              this.handle_cancel();
              this.$emit('actualizar');
              this.$toast.success(
                'Se ha modificado el fonograma correctamente',
                '¡Éxito!',
                { timeout: 1000 }
              );
            })
            .catch((error) => {
              this.text_button = 'Editar';
              this.spinning = false;
              this.waiting = false;
              this.$toast.error('Ha ocurrido un error', '¡Error!', {
                timeout: 1000,
              });
            });
        } else {
          this.text_button = 'Creando...';
          axios
            .post('/fonogramas', form_data, {
              headers: {
                'Content-Type': 'multipart/form-data',
              },
            })
            .then((res) => {
              this.text_button = 'Crear';
              this.spinning = false;
              this.waiting = false;
              this.handle_cancel();
              this.$emit('actualizar');
              this.$toast.success(
                'Se ha creado el fonograma correctamente',
                '¡Éxito!',
                { timeout: 1000 }
              );
            })
            .catch((err) => {
              this.text_button = 'Crear';
              this.spinning = false;
              this.waiting = false;
              this.$toast.error('Ha ocurrido un error', '¡Error!', {
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
        if (this.fonogram_modal.descripEspFong === undefined) {
          this.fonogram_modal.descripEspFong = '';
        } else if (this.fonogram_modal.descripEspFong === null) {
          this.fonogram_modal.descripEspFong = '';
        }
        if (this.fonogram_modal.descripIngFong === undefined) {
          this.fonogram_modal.descripIngFong = '';
        } else if (this.fonogram_modal.descripIngFong === null) {
          this.fonogram_modal.descripIngFong = '';
        }
        if (this.fonogram_modal.territorioFong === undefined) {
          this.fonogram_modal.territorioFong = '';
        } else if (this.fonogram_modal.territorioFong === null) {
          this.fonogram_modal.territorioFong = '';
        }
        if (this.fonogram_modal.dueñoDerchFong === undefined) {
          this.fonogram_modal.dueñoDerchFong = '';
        } else if (this.fonogram_modal.dueñoDerchFong === null) {
          this.fonogram_modal.dueñoDerchFong = '';
        }
        if (this.fonogram_modal.propiedadFong === undefined) {
          this.fonogram_modal.propiedadFong = '';
        } else if (this.fonogram_modal.propiedadFong === null) {
          this.fonogram_modal.propiedadFong = '';
        }
        if (this.fonogram_modal.clasficacionFong === undefined) {
          this.fonogram_modal.clasficacionFong = '';
        } else if (this.fonogram_modal.clasficacionFong === null) {
          this.fonogram_modal.clasficacionFong = '';
        }
        if (this.fonogram_modal.nacioDueñoDerchFong === undefined) {
          this.fonogram_modal.nacioDueñoDerchFong = '';
        } else if (this.fonogram_modal.nacioDueñoDerchFong === null) {
          this.fonogram_modal.nacioDueñoDerchFong = '';
        }
        let form_data = new FormData();
        if (
          this.action_modal === 'editar' ||
          this.action_modal === 'detalles'
        ) {
          form_data.append('id', this.fonogram_modal.id);
        }
        if (this.fonogram_modal.codigFong === undefined) {
          this.fonogram_modal.codigFong = this.codigo;
        }
        this.fonogram_modal.codigFong = 'FONG-' + this.fonogram_modal.codigFong;
        form_data.append('codigFong', this.fonogram_modal.codigFong);
        form_data.append('tituloFong', this.fonogram_modal.tituloFong);
        form_data.append(
          'clasficacionFong',
          this.fonogram_modal.clasficacionFong
        );
        form_data.append('territorioFong', this.fonogram_modal.territorioFong);
        form_data.append('dueñoDerchFong', this.fonogram_modal.dueñoDerchFong);
        form_data.append(
          'nacioDueñoDerchFong',
          this.fonogram_modal.nacioDueñoDerchFong
        );
        form_data.append('propiedadFong', this.fonogram_modal.propiedadFong);
        form_data.append('descripEspFong', this.fonogram_modal.descripEspFong);
        form_data.append('descripIngFong', this.fonogram_modal.descripIngFong);
        form_data.append('product_id', this.fonogram_modal.productos_fongs);
        if (this.file_list.length !== 0) {
          if (this.file_list[0].uid !== this.fonogram_modal.id) {
            if (this.file_list[0].name !== 'Logo ver vertical_Ltr Negras.png') {
              form_data.append(
                'portadillaFong',
                this.file_list[0].originFileObj
              );
            }
          }
        } else form_data.append('img_default', true);
        this.text_button = 'Creando...';
        return form_data;
      },
      set_action() {
        if (this.action === 'editar') {
          if (this.fonogram.deleted_at !== null) {
            this.disabled = true;
            this.activated = false;
          }
          this.text_header_button = 'Editar';
          this.text_button = 'Editar';
          this.action_cancel_title =
            '¿Desea cancelar la edición del Fonograma?';
          this.action_title = '¿Desea guardar los cambios en el Fonograma?';
          this.action_close =
            'La edición del Fonograma se canceló correctamente';
          this.fonogram.descripEspFong =
            this.fonogram.descripEspFong === null
              ? ''
              : this.fonogram.descripEspFong;
          this.fonogram.descripIngFong =
            this.fonogram.descripIngFong === null
              ? ''
              : this.fonogram.descripIngFong;
          this.fonogram.productos_fongs = [];
          this.fonogram.codigFong = this.fonogram.codigFong.substr(5);
          this.fonogram.productos.forEach((element) => {
            this.fonogram.productos_fongs.push(element.id);
          });
          this.fonogram_modal = { ...this.fonogram };
          if (this.fonogram_modal.portadillaFong !== null) {
            if (
              this.fonogram_modal.portadillaFong !==
              '/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png'
            ) {
              this.file_list.push({
                uid: this.fonogram_modal.id,
                name: this.fonogram_modal.portadillaFong.split('/')[
                  this.fonogram_modal.portadillaFong.split('/').length - 1
                ],
                url: this.fonogram_modal.portadillaFong,
              });
            } else
              this.file_list.push({
                uid: 1,
                name: 'Logo ver vertical_Ltr Negras.png',
                url: '/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png',
              });
          }
        } else if (this.action_modal === 'detalles') {
          this.action_cancel_title = '¿Desea cerrar la vista de detalles?';
          this.action_title = '¿Desea guardar los cambios en el Fonograma?';
          this.action_close = 'La vista de detalles fue cerrada correctamente';
          this.fonogram.descripEspFong =
            this.fonogram.descripEspFong === null
              ? ''
              : this.fonogram.descripEspFong;
          this.fonogram.descripIngFong =
            this.fonogram.descripIngFong === null
              ? ''
              : this.fonogram.descripIngFong;
					this.fonogram.productos_fongs = [];
					console.log(this.fonogram.codigFong);
          this.fonogram.codigFong = this.fonogram.codigFong.substr(5);
          this.fonogram.productos.forEach((element) => {
            this.fonogram.productos_fongs.push(element.id);
          });
          this.fonogram_modal = { ...this.fonogram };
          if (this.fonogram_modal.portadillaFong !== null) {
            if (
              this.fonogram_modal.portadillaFong !==
              '/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png'
            ) {
              this.file_list.push({
                uid: this.fonogram_modal.id,
                name: this.fonogram_modal.portadillaFong.split('/')[
                  this.fonogram_modal.portadillaFong.split('/').length - 1
                ],
                url: this.fonogram_modal.portadillaFong,
              });
            } else
              this.file_list.push({
                uid: 1,
                name: 'Logo ver vertical_Ltr Negras.png',
                url: '/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png',
              });
          }
        } else {
          this.file_list.push({
            uid: 1,
            name: 'Logo ver vertical_Ltr Negras.png',
            url: '/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png',
          });
          this.text_button = 'Crear';
          this.text_header_button = 'Crear';
          this.action_cancel_title =
            '¿Desea cancelar la creación del Fonograma?';
          this.action_title = '¿Desea crear el Fonograma?';
          this.action_close =
            'La creación del Fonograma se canceló correctamente';
        }
      },
      remove_image() {
        this.file_list.pop();
        this.preview_image = '';
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
          file.type === 'image/jpeg' ||
          file.type === 'image/png' ||
          file.type === 'image/jpg';
        if (!isJpgOrPng) {
          this.valid_image = false;
          this.$message.error(
            'Sólo puedes subir imágenes como portadilla del fonograma'
          );
        } else this.$message.success('Portadilla cargada correctamente');
        return false;
      },
      load_nomenclators() {
        //* Carga de productos
        axios
          .post('/productos/listar')
          .then((response) => {
            let prod = response.data;
            prod.forEach((element) => {
              if (!element.deleted_at) {
                this.products.push(element);
              }
            });
          })
          .catch((error) => {
            console.log(error);
          });
        axios
          .post('/fonogramas/nomencladores')
          .then((response) => {
            this.list_nomenclators = response.data;
            this.clasficaciones = this.list_nomenclators[0][0];
            this.paises = this.list_nomenclators[2][0];
            this.territorios = this.list_nomenclators[1][0];
          })
          .catch((error) => {
            this.$toast.error('Ha ocurrido un error', '¡Error!', {
              timeout: 1000,
            });
          });
      },

      //Metodos para generar el codigo
      //Este es el único método que varia de un módulo a otro
      crear_arr_codig(arr) {
        let answer = [];
        for (let i = 0; i < arr.length; i++) {
          answer.push(parseInt(arr[i].codigFong.substr(5, 8)));
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
            return '000' + number;
          case 2:
            return '00' + number;
          case 3:
            return '0' + number;
          case 4:
            return number.toString();
          default:
            break;
        }
      },
      //Fin de metodos para generar el codigo
    },
  };
</script>

<style>
  #modal_gestionar_fonogramas .ant-col-6 {
    width: 50% !important;
  }
  #modal_gestionar_fonogramas .pull-left {
    float: left !important;
  }
  #modal_gestionar_fonogramas .ant-upload-list-item,
  .ant-upload-list-item-undefined,
  .ant-upload-list-item-list-type-picture-card,
  .ant-upload,
  .ant-upload-select,
  .ant-upload-select-picture-card,
  .ant-upload-list-picture-card-container {
    width: 170px !important;
    height: 170px !important;
  }
  #modal_gestionar_fonogramas .ant-upload,
  .ant-upload-select,
  .ant-upload-select-picture-card,
  .ant-upload-list-picture-card-container {
    margin-left: 0 !important;
  }
  #modal_gestionar_fonogramas .dynamic-delete-button {
    cursor: pointer;
    position: relative;
    top: 4px;
    font-size: 24px;
    color: #999;
    transition: all 0.3s;
  }
  #modal_gestionar_fonogramas .dynamic-delete-button[disabled] {
    cursor: not-allowed;
    opacity: 0.5;
  }
  #upload .ant-upload,
  .ant-upload-select,
  .ant-upload-select-text {
    height: 0px !important;
  }
  #modal_gestionar_fonogramas .add-field-button {
    width: 60% !important;
    background-color: rgb(46, 171, 229) !important;
    color: white !important;
  }
  #modal_gestionar_fonogramas .ant-col-sm-offset-4 {
    margin-left: 0 !important;
  }
  #modal_gestionar_fonogramas .ant-col-sm-20 {
    width: 100% !important;
  }
  #modal_gestionar_fonogramas .autores-select {
    width: 85% !important;
    margin-right: 3px !important;
  }
  #modal_gestionar_fonogramas .interpretes-select {
    width: 80% !important;
  }
  #modal_gestionar_fonogramas textarea {
    height: 150px !important;
  }
  #modal_gestionar_fonogramas .dynamic-delete-button {
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
  #autor {
    margin-bottom: 0 !important;
  }
</style>

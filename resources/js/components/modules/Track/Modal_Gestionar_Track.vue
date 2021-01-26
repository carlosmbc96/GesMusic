<template>
  <div>
    <a-modal
      :closable="false"
      width="80.4%"
      okText="Guardar"
      cancelText="Cancelar"
      cancelType="danger"
      :visible="show"
      id="modal_gestionar_tracks"
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
        <div slot="tabBarExtraContent">{{ text_header_button }} Track</div>
        <a-tab-pane
          key="1"
          v-if="tab_visibility && action_modal !== 'detalles'"
        >
          <span slot="tab"> Fonograma </span>
          <a-spin :spinning="spinning">
            <div>
              <a-form-model
                ref="formularioFonograma"
                :model="track_modal"
                :rules="rules"
              >
                <a-row>
                  <a-col span="12">
                    <div class="section-title">
                      <h4>Selector de Fonogramas</h4>
                    </div>
                  </a-col>
                </a-row>
                <a-col span="12">
                  <a-form-model-item
                    label="Código"
                    has-feedback
                    prop="fonogramas"
                  >
                    <a-select
                      mode="multiple"
                      v-model="track_modal.fonogramas_tracks"
                      style="width: 50% !important"
                      :disabled="disabled"
                    >
                      <a-select-option
                        v-for="fonogram in fonograms"
                        :key="fonogram.codigFong"
                        :value="fonogram.id"
                      >
                        {{ fonogram.codigFong }}
                      </a-select-option>
                    </a-select>
                  </a-form-model-item>
                  <a-form-model-item label="Título">
                    <a-select
                      mode="multiple"
                      v-model="track_modal.fonogramas_tracks"
                      :disabled="disabled"
                    >
                      <a-select-option
                        v-for="fonogram in fonograms"
                        :key="fonogram.id"
                        :value="fonogram.id"
                      >
                        {{ fonogram.tituloFong }}
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
          </a-spin>
        </a-tab-pane>
        <a-tab-pane key="2" :disabled="tab_2">
          <span slot="tab">Generales</span>
          <a-row>
            <a-col span="11">
              <div class="section-title">
                <h4>Datos Generales</h4>
              </div>
            </a-col>
          </a-row>
          <a-row>
            <a-spin :spinning="spinning">
              <div>
                <a-form-model
                  ref="formularioGenerales"
                  layout="horizontal"
                  :model="track_modal"
                  :rules="rules"
                >
                  <a-col span="11">
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
                      prop="ordenTrk"
                      has-feedback
                      label="Orden del repertorio"
                    >
                      <a-input
                        :disabled="disabled"
                        v-model="track_modal.ordenTrk"
                      />
                    </a-form-model-item>
                    <a-form-model-item label="Orden del repertorio" v-else>
                      <a-mentions readonly :placeholder="track_modal.ordenTrk">
                      </a-mentions>
                    </a-form-model-item>
                    <a-row v-if="action_modal === 'crear'">
                      <a-col span="4">
                        <a-tooltip
                          placement="bottom"
                          title="Código de dos letras que representan el país Ej: BR (Brazil)"
                        >
                          <a-form-model-item
                            :validate-status="show_error"
                            prop="codigPais"
                            has-feedback
                            label="ISRC"
                            :help="show_used_error"
                          >
                            <a-input
                              placeholder="CC"
                              :disabled="action_modal === 'editar'"
                              v-model="track_modal.codigPais"
                            />
                          </a-form-model-item>
                        </a-tooltip>
                      </a-col>
                      <a-col
                        span="1"
                        style="margin-top: 34px; text-align: center"
                        >-</a-col
                      >
                      <a-col span="5">
                        <a-tooltip
                          placement="bottom"
                          title="Código de registro que consta de tres letras Ej: MBR"
                        >
                          <a-form-model-item
                            :validate-status="show_error"
                            prop="codigRegistro"
                            has-feedback
                          >
                            <a-input
                              style="margin-top: 33.2px"
                              placeholder="XXX"
                              :disabled="action_modal === 'editar'"
                              v-model="track_modal.codigRegistro"
                            />
                          </a-form-model-item>
                        </a-tooltip>
                      </a-col>
                      <a-col
                        span="1"
                        style="margin-top: 34px; text-align: center"
                        >-</a-col
                      >
                      <a-col span="4">
                        <a-tooltip
                          placement="bottom"
                          title="Dos últimos dígitos del año de registro Ej: 14 (2014)"
                        >
                          <a-form-model-item
                            :validate-status="show_error"
                            prop="anhoRegistro"
                            has-feedback
                          >
                            <a-input
                              style="margin-top: 33.2px"
                              placeholder="00"
                              :disabled="action_modal === 'editar'"
                              v-model="track_modal.anhoRegistro"
                            />
                          </a-form-model-item>
                        </a-tooltip>
                      </a-col>
                      <a-col
                        span="1"
                        style="margin-top: 34px; text-align: center"
                        >-</a-col
                      >
                      <a-col span="7">
                        <a-form-model-item has-feedback>
                          <a-mentions
                            style="margin-top: 33.2px"
                            readonly
                            :placeholder="codigo"
                          >
                          </a-mentions>
                        </a-form-model-item>
                      </a-col>
                    </a-row>
                    <a-form-model-item
                      v-if="action_modal === 'editar'"
                      label="ISRC"
                    >
                      <a-input
                        :disabled="action_modal === 'editar'"
                        v-model="track_modal.isrcTrk"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      label="ISRC"
                      v-if="action_modal === 'detalles'"
                    >
                      <a-mentions readonly :placeholder="track_modal.isrcTrk">
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
                      prop="tituloTrk"
                      has-feedback
                      label="Título"
                    >
                      <a-input
                        :disabled="disabled"
                        v-model="track_modal.tituloTrk"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      label="Título"
                      v-if="action_modal === 'detalles'"
                    >
                      <a-mentions readonly :placeholder="track_modal.tituloTrk">
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
                      prop="duracionTrk"
                      has-feedback
                      label="Duración"
                    >
                      <a-time-picker
                        :default-open-value="moment('00:00:00', 'HH:mm:ss')"
                        :disabled="disabled"
												:valueFormat="'HH:mm:ss'"
                        v-model="track_modal.duracionTrk"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      label="Duración"
                      v-if="action_modal === 'detalles'"
                    >
                      <a-mentions
                        readonly
                        :placeholder="track_modal.duracionTrk"
                      >
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
                      has-feedback
                      label="País de grabación"
                      prop="paisgrabTrk"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        option-filter-prop="children"
                        :filter-option="filter_option"
                        show-search
                        :disabled="disabled"
                        v-model="track_modal.paisgrabTrk"
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
                      label="País de grabación"
                      v-if="action_modal === 'detalles'"
                    >
                      <a-mentions
                        readonly
                        :placeholder="track_modal.paisgrabTrk"
                      >
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item v-if="action_modal !== 'detalles'">
                      <a-checkbox
                        :disabled="disabled"
                        v-model="muestraTrk"
                        :value="muestraTrk"
                      >
                        ¿El track tiene una pista de muestra?
                      </a-checkbox>
                    </a-form-model-item>
                    <a-form-model-item style="margin-top: 20px" v-else>
                      <i
                        class="fa fa-check-square-o hidden-xs"
                        v-if="track.muestraTrk === 1"
                      />
                      <i class="fa fa-square-o" v-else />
                      ¿El track tiene una pista de muestra?
                    </a-form-model-item>
                  </a-col>
                  <a-col span="11" style="float: right">
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
                      has-feedback
                      label="Género musical"
                      prop="generoTrk"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        option-filter-prop="children"
                        :filter-option="filter_option"
                        show-search
                        :disabled="disabled"
                        v-model="track_modal.generoTrk"
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
                      label="Género musical"
                      v-if="action_modal === 'detalles'"
                    >
                      <a-mentions readonly :placeholder="track_modal.generoTrk">
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
                      has-feedback
                      label="Subgénero musical"
                      prop="subgeneroTrk"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        option-filter-prop="children"
                        :filter-option="filter_option"
                        show-search
                        :disabled="disabled"
                        v-model="track_modal.subgeneroTrk"
                      >
                        <a-select-option
                          v-for="nomenclator in subgeneros"
                          :key="nomenclator.id"
                          :value="nomenclator.nombreTer"
                        >
                          {{ nomenclator.nombreTer }}
                        </a-select-option>
                      </a-select>
                    </a-form-model-item>
                    <a-form-model-item
                      label="Subgénero musical"
                      v-if="action_modal === 'detalles'"
                    >
                      <a-mentions
                        readonly
                        :placeholder="track_modal.subgeneroTrk"
                      >
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
                      has-feedback
                      label="Estados de ánimo del track"
                      prop="moodTrk"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        mode="multiple"
                        :disabled="disabled"
                        v-model="track_modal.moodTrk"
                      >
                        <a-select-option
                          v-for="nomenclator in moods"
                          :key="nomenclator.id"
                          :value="nomenclator.nombreTer"
                        >
                          {{ nomenclator.nombreTer }}
                        </a-select-option>
                      </a-select>
                    </a-form-model-item>
                    <a-form-model-item
                      label="Estados de ánimo del track"
                      v-if="action_modal === 'detalles'"
                    >
                      <a-mentions readonly :placeholder="track_modal.moodTrk">
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
                      has-feedback
                      label="Gestión"
                      prop="gestionTrk"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        :disabled="disabled"
                        v-model="track_modal.gestionTrk"
                      >
                        <a-select-option
                          v-for="nomenclator in gestiones"
                          :key="nomenclator.id"
                          :value="nomenclator.nombreTer"
                        >
                          {{ nomenclator.nombreTer }}
                        </a-select-option>
                      </a-select>
                    </a-form-model-item>
                    <a-form-model-item
                      label="Gestión"
                      v-if="action_modal === 'detalles'"
                    >
                      <a-mentions
                        readonly
                        :placeholder="track_modal.gestionTrk"
                      >
                      </a-mentions>
                    </a-form-model-item>
                    <a-row>
                      <a-col span="11">
                        <a-form-model-item v-if="action_modal !== 'detalles'">
                          <a-checkbox
                            :disabled="disabled"
                            v-model="bonusTrk"
                            :value="bonusTrk"
                          >
                            ¿Es un bonus Track?
                          </a-checkbox>
                        </a-form-model-item>
                        <a-form-model-item style="margin-top: 20px" v-else>
                          <i
                            class="fa fa-check-square-o hidden-xs"
                            v-if="track.bonusTrk === 1"
                          />
                          <i class="fa fa-square-o" v-else />
                          ¿Es un bonus Track?
                        </a-form-model-item>
                      </a-col>
                      <a-col span="11" style="float: right">
                        <a-form-model-item v-if="action_modal !== 'detalles'">
                          <a-checkbox
                            :disabled="disabled"
                            v-model="envivoTrk"
                            :value="envivoTrk"
                          >
                            ¿El Track se Grabó en Vivo?
                          </a-checkbox>
                        </a-form-model-item>
                        <a-form-model-item style="margin-top: 20px" v-else>
                          <i
                            class="fa fa-check-square-o hidden-xs"
                            v-if="track.envivoTrk === 1"
                          />
                          <i class="fa fa-square-o" v-else />
                          ¿El Track se Grabó en Vivo?
                        </a-form-model-item>
                      </a-col>
                    </a-row>
                  </a-col>
                </a-form-model>
              </div>
            </a-spin>
          </a-row>
          <a-row>
            <a-button
              v-if="tab_visibility && action_modal !== 'detalles'"
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
  import axios from '../../../config/axios/axios';
  import moment from '../../../../../node_modules/moment';
  export default {
    props: ['action', 'track', 'tracks_list'],
    data() {
      let validate_ISRC_unique = (rule, value, callback) => {
        this.tracks_list.forEach((element) => {
          if (element.isrcTrk === value) {
            callback(new Error('ISRC ya usado'));
          }
        });
        callback();
      };
      return {
        tab_2: true,
        tabs_list: [],
        active_tab: '1',
        tab_visibility: true,
        action_cancel_title: '',
        fonograms: [],
        action_title: '',
        show: true,
        used: false,
        disabled: false,
        waiting: false,
        text_button: '',
        text_header_button: '',
        spinning: false,
        track_modal: {},
        paises: [],
        generos: [],
        subgeneros: [],
        moods: [],
        gestiones: [],
        muestraTrk: false,
        bonusTrk: false,
        envivoTrk: false,
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
          codigPais: [
            {
              required: true,
              message: ' ',
              trigger: 'change',
            },
            {
              pattern: '^[a-zA-Z]*$',
              message: ' ',
              trigger: 'change',
            },
            {
              len: 2,
              message: ' ',
              trigger: 'change',
            },
          ],
          codigRegistro: [
            {
              required: true,
              message: ' ',
              trigger: 'change',
            },
            {
              pattern: '^[a-zA-Z]*$',
              message: ' ',
              trigger: 'change',
            },
            {
              len: 3,
              message: ' ',
              trigger: 'change',
            },
          ],
          anhoRegistro: [
            {
              required: true,
              message: ' ',
              trigger: 'change',
            },
            {
              pattern: '^[0-9]*$',
              message: ' ',
              trigger: 'change',
            },
            {
              len: 2,
              message: ' ',
              trigger: 'change',
            },
          ],
          tituloTrk: [
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
          duracionTrk: [
            {
              required: true,
              message: 'Campo requerido',
              trigger: 'change',
            },
          ],
          generoTrk: [
            {
              required: true,
              message: 'Campo requerido',
              trigger: 'change',
            },
          ],
          gestionTrk: [
            {
              required: true,
              message: 'Campo requerido',
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
        this.codigo = this.generar_codigo(this.tracks_list);
      } else if (this.action_modal === 'detalles') {
        this.active_tab = '2';
      }
    },
    computed: {
      active() {
        if (this.text_button === 'Editar') {
          return !this.compare_object;
        } else
          return (
            this.track_modal.tituloTrk &&
            this.track_modal.duracionTrk &&
            this.track_modal.generoTrk &&
            this.track_modal.gestionTrk
          );
      },
    },
    methods: {
      moment,
      siguiente(tab, siguienteTab) {
        if (tab === 'tab_1') {
          this.$refs.formularioFonograma.validate((valid) => {
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
          }
        }
      },
      handle_cancel(e) {
        if (e === 'cancelar') {
          if (this.tabs_list.indexOf('tab_1') !== -1) {
            this.$refs.formularioGenerales.resetFields();
          }
          this.tabs_list = [];
          this.active_tab = '1';
          this.tab_visibility = true;
          this.show = false;
          this.$emit('close_modal', this.show);
          if (this.action_modal !== 'detalles') {
            this.$toast.success(this.action_close, '¡Éxito!', {
              timeout: 1000,
              color: 'orange',
            });
          }
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
        if (this.track_modal.identificador === undefined) {
          this.track_modal.identificador = this.codigo;
        }
        if (!this.used) {
          if (this.tabs_list.indexOf('tab_1') !== -1) {
            this.$refs.formularioGenerales.validate((valid) => {
              if (valid) {
                return this.confirm();
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
            .post(`/tracks/editar`, form_data, {
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
                'Se ha modificado el track correctamente',
                '¡Éxito!',
                { timeout: 1000 }
              );
            })
            .catch((error) => {
              this.text_button = 'Editar';
              this.spinning = false;
              this.waiting = false;
              this.handle_cancel();
              this.$toast.error('Ha ocurrido un error', '¡Error!', {
                timeout: 1000,
              });
            });
        } else {
          this.text_button = 'Creando...';
          axios
            .post('/tracks', form_data, {
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
                'Se ha creado el track correctamente',
                '¡Éxito!',
                { timeout: 1000 }
              );
            })
            .catch((err) => {
              this.text_button = 'Crear';
              this.spinning = false;
              this.waiting = false;
              this.handle_cancel();
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
        this.track_modal.muestraTrk = this.muestraTrk === false ? 0 : 1;
        this.track_modal.bonusTrk = this.bonusTrk === false ? 0 : 1;
        this.track_modal.envivoTrk = this.envivoTrk === false ? 0 : 1;
        if (this.track_modal.ordenTrk === undefined) {
          this.track_modal.ordenTrk = '';
        } else if (this.track_modal.ordenTrk === null) {
          this.track_modal.ordenTrk = '';
        }
        if (this.track_modal.subgeneroTrk === undefined) {
          this.track_modal.subgeneroTrk = '';
        } else if (this.track_modal.subgeneroTrk === null) {
          this.track_modal.subgeneroTrk = '';
        }
        if (this.track_modal.moodTrk === undefined) {
          this.track_modal.moodTrk = '';
        } else if (this.track_modal.moodTrk === null) {
          this.track_modal.moodTrk = '';
        }
        if (this.track_modal.paisgrabTrk === undefined) {
          this.track_modal.paisgrabTrk = '';
        } else if (this.track_modal.paisgrabTrk === null) {
          this.track_modal.paisgrabTrk = '';
        }
        let moods = '';
        if (this.track_modal.moodTrk !== '') {
          this.track_modal.moodTrk.forEach((item) => {
            moods += item + ' ';
          });
          this.track_modal.moodTrk = moods;
        }
        let form_data = new FormData();
        if (
          this.action_modal === 'editar' ||
          this.action_modal === 'detalles'
        ) {
          form_data.append('id', this.track_modal.id);
        }
        if (this.track_modal.identificador === undefined) {
          this.track_modal.identificador = this.codigo;
				}
				if(this.action_modal === "crear") {
					this.track_modal.isrcTrk =
          '' +
          this.track_modal.codigPais.toUpperCase() +
          this.track_modal.codigRegistro.toUpperCase() +
          this.track_modal.anhoRegistro +
					this.track_modal.identificador;
				}
        form_data.append('isrcTrk', this.track_modal.isrcTrk);
        form_data.append('tituloTrk', this.track_modal.tituloTrk);
        form_data.append('ordenTrk', this.track_modal.ordenTrk);
        form_data.append('duracionTrk', this.track_modal.duracionTrk);
        form_data.append('muestraTrk', this.track_modal.muestraTrk);
        form_data.append('envivoTrk', this.track_modal.envivoTrk);
        form_data.append('generoTrk', this.track_modal.generoTrk);
        form_data.append('moodTrk', moods);
        form_data.append('subgeneroTrk', this.track_modal.subgeneroTrk);
        form_data.append('bonusTrk', this.track_modal.bonusTrk);
        form_data.append('gestionTrk', this.track_modal.gestionTrk);
        form_data.append('paisgrabTrk', this.track_modal.paisgrabTrk);
        form_data.append('fonograma_id', this.track_modal.fonogramas_tracks);
        this.text_button = 'Creando...';
        return form_data;
      },
      set_action() {
        if (this.action === 'editar') {
          if (this.track.deleted_at !== null) {
            this.disabled = true;
            this.activated = false;
          }
          this.text_header_button = 'Editar';
          this.text_button = 'Editar';
          this.action_cancel_title = '¿Desea cancelar la edición del Track?';
          this.action_title = '¿Desea guardar los cambios en el Track?';
          this.action_close = 'La edición del Track se canceló correctamente';
          this.track.fonogramas_tracks = [];
          this.track.fonogramas.forEach((element) => {
            this.track.fonogramas_tracks.push(element.id);
          });
          this.track_modal = { ...this.track };
          this.muestraTrk = this.track_modal.muestraTrk === 0 ? false : true;
          this.envivoTrk = this.track_modal.envivoTrk === 0 ? false : true;
          this.bonusTrk = this.track_modal.bonusTrk === 0 ? false : true;
          if (this.track_modal.moodTrk !== null) {
            this.track_modal.moodTrk = this.track_modal.moodTrk.split(' ');
          } else delete this.track_modal.moodTrk;
        } else if (this.action_modal === 'detalles') {
          if (this.track.deleted_at !== null) {
            this.disabled = true;
            this.activated = false;
          }
          this.text_header_button = 'Detalles';
          this.text_button = 'Detalles';
          this.track.fonogramas_tracks = [];
          this.track.fonogramas.forEach((element) => {
            this.track.fonogramas_tracks.push(element.id);
          });
          this.track_modal = { ...this.track };
          this.muestraTrk = this.track_modal.muestraTrk === 0 ? false : true;
          this.envivoTrk = this.track_modal.envivoTrk === 0 ? false : true;
          this.bonusTrk = this.track_modal.bonusTrk === 0 ? false : true;
          if (this.track_modal.moodTrk !== null) {
            this.track_modal.moodTrk = this.track_modal.moodTrk.split(' ');
          } else delete this.track_modal.moodTrk;
        } else {
          this.text_button = 'Crear';
          this.text_header_button = 'Crear';
          this.action_cancel_title = '¿Desea cancelar la creación del track?';
          this.action_title = '¿Desea crear el track?';
          this.action_close = 'La creación del track se canceló correctamente';
        }
      },
      load_nomenclators() {
        //* Carga de productos
        axios
          .post('/fonogramas/listar')
          .then((response) => {
            let fong = response.data;
            fong.forEach((element) => {
              if (!element.deleted_at) {
                this.fonograms.push(element);
              }
            });
          })
          .catch((error) => {
            console.log(error);
          });
        axios
          .post('/tracks/nomencladores')
          .then((response) => {
            this.list_nomenclators = response.data;
            this.moods = this.list_nomenclators[2][0];
            this.generos = this.list_nomenclators[0][0];
            this.subgeneros = this.list_nomenclators[1][0];
            this.paises = this.list_nomenclators[4][0];
            this.gestiones = this.list_nomenclators[3][0];
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
          answer.push(parseInt(arr[i].isrcTrk.substr(7)));
				}
				console.log(answer);
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
            return '0000' + number;
          case 2:
            return '000' + number;
          case 3:
            return '00' + number;
          case 4:
            return '0' + number;
          case 5:
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
  #modal_gestionar_tracks .ant-col-6 {
    width: 50% !important;
  }
  #modal_gestionar_tracks .ant-upload-list-item,
  .ant-upload-list-item-undefined,
  .ant-upload-list-item-list-type-picture-card,
  .ant-upload,
  .ant-upload-select,
  .ant-upload-select-picture-card,
  .ant-upload-list-picture-card-container {
    width: 170px !important;
    height: 170px !important;
  }
  #modal_gestionar_tracks .ant-select-search input {
    border-color: rgb(255, 255, 255) !important;
  }
  #modal_gestionar_tracks .ant-mentions textarea {
    height: 32px !important;
  }
  #modal_gestionar_tracks .description textarea {
    height: 150px !important;
  }
  #small .ant-form-item-control {
    width: 85%;
  }
</style>

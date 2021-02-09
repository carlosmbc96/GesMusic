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
        <div slot="tabBarExtraContent">
          {{ text_header_button }} Audiovisual
        </div>
        <a-tab-pane
          key="1"
          v-if="tab_visibility && action_modal !== 'detalles'"
        >
          <span slot="tab"> Producto </span>
          <a-spin :spinning="spinning">
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
          </a-spin>
        </a-tab-pane>
        <a-tab-pane key="2" :disabled="tab_2">
          <span slot="tab">Generales</span>
          <a-row>
            <a-spin :spinning="spinning">
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
                    <a-row>
                      <a-col span="4" id="small">
                        <a-upload
                          v-if="action_modal !== 'detalles'"
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
                        <div v-else class="detalles-img">
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
                      <a-col span="14" style="float: right">
                        <a-row>
                          <a-col span="24">
                            <a-form-model-item
                              v-if="action_modal !== 'detalles'"
                              has-feedback
                              label="Género Audiovisual"
                              prop="generoAud"
                              @change="edit_code"
                            >
                              <a-select
                                :getPopupContainer="
                                  (trigger) => trigger.parentNode
                                "
                                option-filter-prop="children"
                                :filter-option="filter_option"
                                show-search
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
                              label="Género Audiovisual"
                              v-if="action_modal === 'detalles'"
                            >
                              <a-mentions
                                readonly
                                :placeholder="audiovisual_modal.generoAud"
                              >
                              </a-mentions>
                            </a-form-model-item>
                            <a-form-model-item
                              v-if="
                                action_modal === 'crear' &&
                                  (audiovisual_modal.generoAud ===
                                    'Entrevista' ||
                                    audiovisual_modal.generoAud ===
                                      'Making of' ||
                                    audiovisual_modal.generoAud === 'Trailers')
                              "
                              :validate-status="show_error"
                              prop="codigAud"
                              has-feedback
                              label="Código"
                              :help="show_used_error"
                            >
                              <a-input
                                addon-before="AUDV-"
                                :default-value="codigo"
                                :disabled="action_modal === 'editar'"
                                v-model="audiovisual_modal.codigAud"
                              />
                            </a-form-model-item>
                            <a-form-model-item
                              v-if="
                                action_modal === 'editar' &&
                                (audiovisual_modal.generoAud === 'Entrevista' ||
                                  audiovisual_modal.generoAud === 'Making of' ||
                                  audiovisual_modal.generoAud === 'Trailers')
                              "
                              label="Código"
                              prop="codigAud"
                            >
                              <a-input
                                v-if="
                                  audiovisual_modal.generoAud === genero_pivot
                                "
                                addon-before="AUDV-"
                                :disabled="true"
                                v-model="codig_pivot"
                              />
                              <a-input
                                v-else
                                addon-before="AUDV-"
                                :dafault-value="codig_pivot"
                                :disabled="false"
                                v-model="audiovisual_modal.codigAud"
                              />
                            </a-form-model-item>
                            <a-form-model-item
                              label="Código"
                              v-if="
                                action_modal === 'detalles' &&
                                (audiovisual_modal.generoAud === 'Entrevista' ||
                                  audiovisual_modal.generoAud === 'Making of' ||
                                  audiovisual_modal.generoAud === 'Trailers')
                              "
                            >
                              <a-mentions
                                readonly
                                :placeholder="audiovisual_modal.codigAud"
                              >
                              </a-mentions>
                            </a-form-model-item>
                          </a-col>
                        </a-row>
                        <a-row
                          v-if="
                            action_modal === 'crear' &&
                            (audiovisual_modal.generoAud === 'Concierto' ||
                              audiovisual_modal.generoAud === 'Video Clip' ||
                              audiovisual_modal.generoAud === 'Documental')
                          "
                        >
                          <a-col span="24">
                            <a-row>
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
                                    class="isrc"
                                  >
                                    <a-input
                                      default-value="CU"
                                      :disabled="action_modal === 'editar'"
                                      v-model="audiovisual_modal.codigPais"
                                      :class="isrc_validation"
                                    />
                                  </a-form-model-item>
                                </a-tooltip>
                              </a-col>
                              <a-col
                                span="1"
                                style="margin-top: 34px; text-align: center"
                              >
                              </a-col>
                              <a-col span="6">
                                <a-tooltip
                                  placement="bottom"
                                  title="Código de registro que consta de tres caracteres alfanuméricos Ej: MB4"
                                >
                                  <a-form-model-item
                                    :validate-status="show_error"
                                    prop="codigRegistro"
                                    has-feedback
                                    class="isrc"
                                  >
                                    <a-input
                                      style="margin-top: 33.2px"
                                      placeholder="XXX"
                                      :disabled="action_modal === 'editar'"
                                      v-model="audiovisual_modal.codigRegistro"
                                      :class="isrc_validation"
                                    />
                                  </a-form-model-item>
                                </a-tooltip>
                              </a-col>
                              <a-col
                                span="1"
                                style="margin-top: 34px; text-align: center"
                              >
                              </a-col>
                              <a-col span="4">
                                <a-tooltip
                                  placement="bottom"
                                  title="Dos últimos dígitos del año de registro Ej: 14 (2014)"
                                >
                                  <a-form-model-item
                                    :validate-status="show_error"
                                    prop="anhoRegistro"
                                    has-feedback
                                    class="isrc"
                                  >
                                    <a-input
                                      style="margin-top: 33.2px"
                                      placeholder="00"
                                      :disabled="action_modal === 'editar'"
                                      v-model="audiovisual_modal.anhoRegistro"
                                      :class="isrc_validation"
                                    />
                                  </a-form-model-item>
                                </a-tooltip>
                              </a-col>
                              <a-col
                                span="1"
                                style="margin-top: 34px; text-align: center"
                              >
                              </a-col>
                              <a-col span="7">
                                <a-tooltip
                                  placement="bottom"
                                  title="Código identificador de cinco dígitos Ej: 00001"
                                >
                                  <a-form-model-item
                                    :validate-status="show_error"
                                    prop="identificador"
                                    has-feedback
                                    class="isrc"
                                  >
                                    <a-input
                                      style="margin-top: 33.2px"
                                      :default-value="codigoIsrc"
                                      :disabled="action_modal === 'editar'"
                                      v-model="audiovisual_modal.identificador"
                                      :class="isrc_validation"
                                    />
                                  </a-form-model-item>
                                </a-tooltip>
                              </a-col>
                            </a-row>
                          </a-col>
                        </a-row>
                        <a-row
                          v-if="
                            action_modal === 'editar' &&
                              (audiovisual_modal.generoAud === 'Concierto' ||
                                audiovisual_modal.generoAud === 'Video Clip' ||
                                audiovisual_modal.generoAud === 'Documental') &&
                              audiovisual_modal.generoAud !== genero_pivot
                          "
                        >
                          <a-col span="24">
                            <a-row>
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
                                    class="isrc"
                                  >
                                    <a-input
                                      :default-value="codigPais"
                                      :disabled="false"
                                      v-model="audiovisual_modal.codigPais"
                                      :class="isrc_validation"
                                    />
                                  </a-form-model-item>
                                </a-tooltip>
                              </a-col>
                              <a-col
                                span="1"
                                style="margin-top: 34px; text-align: center"
                              >
                              </a-col>
                              <a-col span="6">
                                <a-tooltip
                                  placement="bottom"
                                  title="Código de registro que consta de tres caracteres alfanuméricos Ej: MB4"
                                >
                                  <a-form-model-item
                                    v-if="codig_to_isrc"
                                    :validate-status="show_error"
                                    prop="codigRegistro"
                                    has-feedback
                                    class="isrc"
                                  >
                                    <a-input
                                      style="margin-top: 33.2px"
                                      placeholder="XXX"
                                      :disabled="false"
                                      v-model="audiovisual_modal.codigRegistro"
                                      :class="isrc_validation"
                                    />
                                  </a-form-model-item>
                                  <a-form-model-item
                                    v-else
                                    :validate-status="show_error"
                                    prop="codigRegistro"
                                    has-feedback
                                    class="isrc"
                                  >
                                    <a-input
                                      style="margin-top: 33.2px"
                                      :default-value="codigRegistro"
                                      :disabled="false"
                                      v-model="audiovisual_modal.codigRegistro"
                                      :class="isrc_validation"
                                    />
                                  </a-form-model-item>
                                </a-tooltip>
                              </a-col>
                              <a-col
                                span="1"
                                style="margin-top: 34px; text-align: center"
                              >
                              </a-col>
                              <a-col span="4">
                                <a-tooltip
                                  placement="bottom"
                                  title="Dos últimos dígitos del año de registro Ej: 14 (2014)"
                                >
                                  <a-form-model-item
                                    :validate-status="show_error"
                                    prop="anhoRegistro"
                                    has-feedback
                                    class="isrc"
                                  >
                                    <a-input
                                      v-if="codig_to_isrc"
                                      style="margin-top: 33.2px"
                                      placeholder="00"
                                      :disabled="false"
                                      v-model="audiovisual_modal.anhoRegistro"
                                      :class="isrc_validation"
                                    />
                                    <a-input
                                      v-else
                                      style="margin-top: 33.2px"
                                      :default-value="anhoRegistro"
                                      :disabled="false"
                                      v-model="audiovisual_modal.anhoRegistro"
                                      :class="isrc_validation"
                                    />
                                  </a-form-model-item>
                                </a-tooltip>
                              </a-col>
                              <a-col
                                span="1"
                                style="margin-top: 34px; text-align: center"
                              >
                              </a-col>
                              <a-col span="7">
                                <a-tooltip
                                  placement="bottom"
                                  title="Código identificador de cinco dígitos Ej: 00001"
                                >
                                  <a-form-model-item
                                    :validate-status="show_error"
                                    prop="identificador"
                                    has-feedback
                                    class="isrc"
                                  >
                                    <a-input
                                      style="margin-top: 33.2px"
                                      :default-value="identificador"
                                      :disabled="false"
                                      v-model="audiovisual_modal.identificador"
                                      :class="isrc_validation"
                                    />
                                  </a-form-model-item>
                                </a-tooltip>
                              </a-col>
                            </a-row>
                          </a-col>
                        </a-row>
                        <a-form-model-item
                          v-if="
                            action_modal === 'editar' &&
                              (audiovisual_modal.generoAud === 'Concierto' ||
                                audiovisual_modal.generoAud === 'Video Clip' ||
                                audiovisual_modal.generoAud === 'Documental') &&
                              audiovisual_modal.generoAud === genero_pivot
                          "
                          label="ISRC"
                        >
                          <a-input :disabled="true" v-model="isrc_pivot" />
                        </a-form-model-item>
                        <a-form-model-item
                          label="ISRC"
                          v-if="
                            action_modal === 'detalles' &&
                            (audiovisual_modal.generoAud === 'Concierto' ||
                              audiovisual_modal.generoAud === 'Video Clip' ||
                              audiovisual_modal.generoAud === 'Documental')
                          "
                        >
                          <a-mentions
                            readonly
                            :placeholder="audiovisual_modal.isrcAud"
                          >
                          </a-mentions>
                        </a-form-model-item>
                      </a-col>
                    </a-row>
                    <a-row>
                      <a-col span="11">
                        <a-form-model-item
                          v-if="action_modal !== 'detalles'"
                          has-feedback
                          label="Clasificación"
                          prop="clasifAud"
                        >
                          <a-select
                            :getPopupContainer="(trigger) => trigger.parentNode"
                            option-filter-prop="children"
                            :filter-option="filter_option"
                            show-search
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
                          label="Clasificación"
                          v-if="action_modal === 'detalles'"
                        >
                          <a-mentions
                            readonly
                            :placeholder="audiovisual_modal.clasifAud"
                          >
                          </a-mentions>
                        </a-form-model-item>
                        <a-form-model-item
                          v-if="action_modal !== 'detalles'"
                          has-feedback
                          label="País de Grabación"
                          prop="paisGrabAud"
                        >
                          <a-select
                            :getPopupContainer="(trigger) => trigger.parentNode"
                            option-filter-prop="children"
                            :filter-option="filter_option"
                            show-search
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
                          label="País de Grabación"
                          v-if="action_modal === 'detalles'"
                        >
                          <a-mentions
                            readonly
                            :placeholder="audiovisual_modal.paisGrabAud"
                          >
                          </a-mentions>
                        </a-form-model-item>
                        <a-form-model-item
                          v-if="action_modal !== 'detalles'"
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
                          label="Idiomas"
                          v-if="action_modal === 'detalles'"
                        >
                          <a-mentions
                            readonly
                            :placeholder="audiovisual_modal.idiomaAud"
                          >
                          </a-mentions>
                        </a-form-model-item>
                        <a-form-model-item
                          v-if="action_modal !== 'detalles'"
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
                          label="Fenómeno de Referencia"
                          v-if="action_modal === 'detalles'"
                        >
                          <a-mentions
                            readonly
                            :placeholder="audiovisual_modal.fenomRefAud"
                          >
                          </a-mentions>
                        </a-form-model-item>
                        <div id="etiqueta">
                          <a-form-model-item
                            v-if="action_modal !== 'detalles'"
                            has-feedback
                            label="Etiquetas"
                            prop="etiquetasAud"
                          >
                            <a-select
                              :getPopupContainer="
                                (trigger) => trigger.parentNode
                              "
                              mode="tags"
                              :disabled="disabled"
                              v-model="audiovisual_modal.etiquetasAud"
                            >
                            </a-select>
                          </a-form-model-item>
                        </div>
                        <a-form-model-item
                          label="Etiquetas"
                          v-if="action_modal === 'detalles'"
                        >
                          <a-mentions
                            readonly
                            :placeholder="audiovisual_modal.etiquetasAud"
                          >
                          </a-mentions>
                        </a-form-model-item>
                      </a-col>
                      <a-col span="11" style="float: right">
                        <a-form-model-item
                          v-if="action_modal !== 'detalles'"
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
                          label="Título"
                          v-if="action_modal === 'detalles'"
                        >
                          <a-mentions
                            readonly
                            :placeholder="audiovisual_modal.tituloAud"
                          >
                          </a-mentions>
                        </a-form-model-item>
                        <a-form-model-item
                          v-if="action_modal !== 'detalles'"
                          prop="duracionAud"
                          has-feedback
                          label="Duración"
                        >
                          <a-time-picker
                            :default-open-value="moment('00:00:00', 'HH:mm:ss')"
                            :disabled="disabled"
                            :valueFormat="'HH:mm:ss'"
                            placeholder="00:00"
                            v-model="audiovisual_modal.duracionAud"
                          />
                        </a-form-model-item>
                        <a-form-model-item
                          label="Duración"
                          v-if="action_modal === 'detalles'"
                        >
                          <a-mentions
                            readonly
                            :placeholder="audiovisual_modal.duracionAud"
                          >
                          </a-mentions>
                        </a-form-model-item>
                        <a-form-model-item
                          v-if="action_modal !== 'detalles'"
                          has-feedback
                          label="Año de finalización"
                          prop="añoFinAud"
                        >
                          <a-select
                            :getPopupContainer="(trigger) => trigger.parentNode"
                            option-filter-prop="children"
                            :filter-option="filter_option"
                            show-search
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
                          label="Año de finalización"
                          v-if="action_modal === 'detalles'"
                        >
                          <a-mentions
                            readonly
                            :placeholder="audiovisual_modal.añoFinAud"
                          >
                          </a-mentions>
                        </a-form-model-item>
                        <a-form-model-item
                          v-if="action_modal !== 'detalles'"
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
                        <a-form-model-item
                          label="Subtítulos"
                          v-if="action_modal === 'detalles'"
                        >
                          <a-mentions
                            readonly
                            :placeholder="audiovisual_modal.subtitulosAud"
                          >
                          </a-mentions>
                        </a-form-model-item>
                        <a-form-model-item v-if="action_modal !== 'detalles'">
                          <a-checkbox
                            :disabled="disabled"
                            v-model="makingOfAud"
                            :value="makingOfAud"
                            style="margin-top: 20px"
                          >
                            ¿Tiene Making-Of?
                          </a-checkbox>
                        </a-form-model-item>
                        <a-form-model-item style="margin-top: 20px" v-else>
                          <i
                            class="fa fa-check-square-o hidden-xs"
                            v-if="audiovisual.makingOfAud === 1"
                          />
                          <i class="fa fa-square-o" v-else />
                          ¿Activo en el Catálogo de Bismusic?
                        </a-form-model-item>
                      </a-col>
                    </a-row>
                  </a-col>
                  <a-col span="11" style="float: right">
                    <div class="section-title">
                      <h4>Datos del Autor</h4>
                    </div>
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
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
                      label="Nombre y Apellidos"
                      v-if="action_modal === 'detalles'"
                    >
                      <a-mentions
                        readonly
                        :placeholder="audiovisual_modal.dueñoDerchAud"
                      >
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
                      prop="nacioDueñoDerchAud"
                      has-feedback
                      label="Nacionalidad del Dueño de los Derechos del Audiovisual"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        option-filter-prop="children"
                        :filter-option="filter_option"
                        show-search
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
                      label="Nacionalidad del dueño de los derechos de audiovisual"
                      v-if="action_modal === 'detalles'"
                    >
                      <a-mentions
                        readonly
                        :placeholder="audiovisual_modal.nacioDueñoDerchAud"
                      >
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
                      has-feedback
                      label="Derechos sobre el Audiovisual"
                      prop="derechosAud"
                    >
                      <a-select
                        :getPopupContainer="(trigger) => trigger.parentNode"
                        option-filter-prop="children"
                        :filter-option="filter_option"
                        show-search
                        :disabled="disabled"
                        v-model="audiovisual_modal.derechosAud"
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
                      label="Derechos sobre el Audiovisual"
                      v-if="action_modal === 'detalles'"
                    >
                      <a-mentions
                        readonly
                        :placeholder="audiovisual_modal.derechosAud"
                      >
                      </a-mentions>
                    </a-form-model-item>
                    <div class="section-title">
                      <h4>Datos Descripciones</h4>
                    </div>
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
                      has-feedback
                      label="Descripción en español del Audiovisual"
                      prop="descripEspAud"
                    >
                      <a-input
                        :disabled="disabled"
                        style="width: 100%; height: 150px"
                        v-model="audiovisual_modal.descripEspAud"
                        type="textarea"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      label="Descripción en español del Audiovisual"
                      v-if="action_modal === 'detalles'"
                    >
                      <div class="description">
                        <a-mentions
                          readonly
                          :placeholder="audiovisual_modal.descripEspAud"
                        >
                        </a-mentions>
                      </div>
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
                      has-feedback
                      label="Descripción en inglés del Audiovisual"
                      prop="descripIngAud"
                    >
                      <a-input
                        :disabled="disabled"
                        style="width: 100%; height: 150px"
                        v-model="audiovisual_modal.descripIngAud"
                        type="textarea"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      label="Descripción en inglés del Audiovisual"
                      v-if="action_modal === 'detalles'"
                    >
                      <div class="description">
                        <a-mentions
                          readonly
                          :placeholder="audiovisual_modal.descripIngAud"
                        >
                        </a-mentions>
                      </div>
                    </a-form-model-item>
                  </a-col>
                </a-form-model>
              </div>
            </a-spin>
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
import moment from "../../../../../node_modules/moment";
import axios from "../../../config/axios/axios";
export default {
  props: ["action", "audiovisual", "audiovisuals_list"],
  data() {
    let validate_codig_unique = (rule, value, callback) => {
      if (value !== undefined) {
        this.lista_dividida(this.audiovisuals_list).code.forEach((element) => {
          if (element.codigAud.substr(5) !== this.codig_pivot) {
            if (element.codigAud.substr(5) === value.replace(/ /g, "")) {
              callback(new Error("Código ya usado"));
            }
          }
        });
      }
      callback();
    };
    let validate_ISRC_unique = (rule, value, callback) => {
      this.lista_dividida(this.audiovisuals_list).isrc.forEach((element) => {
        if (element.isrcAud === value) {
          callback(new Error("ISRC ya usado"));
        }
      });
      callback();
    };
    let code_required = (rule, value, callback) => {
      if (value === "") {
        callback(new Error("Inserte el código"));
      } else callback();
    };
    let custom_code_required = (rule, value, callback) => {
      if (this.codig_to_isrc || this.action_modal === "crear") {
        if (value === undefined) {
          callback(new Error("Inserte el código"));
        } else callback();
      } else {
        if (value === "") {
          callback(new Error("Inserte el código"));
        } else callback();
      }
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
      codigo: "",
      codigoIsrc: "",
      isrc_validation: "",
      genero_pivot: "",
      codig_pivot: "",
      isrc_pivot: "",
      codigPais: "",
      codigRegistro: "",
      anhoRegistro: "",
      identificador: "",
      codig_to_isrc: false,
      rules: {
        codigAud: [
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
        codigPais: [
          {
            validator: code_required,
            trigger: "change",
          },
          {
            pattern: "^[a-zA-Z]*$",
            message: " ",
            trigger: "change",
          },
          {
            len: 2,
            message: " ",
            trigger: "change",
          },
        ],
        codigRegistro: [
          {
            validator: custom_code_required,
            trigger: "change",
          },
          {
            pattern: "^[a-zA-Z0-9]*$",
            message: " ",
            trigger: "change",
          },
          {
            len: 3,
            message: " ",
            trigger: "change",
          },
        ],
        anhoRegistro: [
          {
            validator: custom_code_required,
            trigger: "change",
          },
          {
            pattern: "^[0-9]*$",
            message: " ",
            trigger: "change",
          },
          {
            len: 2,
            message: " ",
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
        identificador: [
          {
            validator: code_required,
            trigger: "change",
          },
          {
            pattern: "^[0-9]*$",
            message: " ",
            trigger: "change",
          },
          {
            len: 5,
            message: " ",
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
          {
            whitespace: true,
            message: "Espacio no válido",
            trigger: "change",
          },
        ],
        dueñoDerchAud: [
          {
            pattern: "^[ a-zA-Z-üáéíóúÁÉÍÓÚñÑ]*$",
            message: "Caracter no válido",
            trigger: "change",
          },
          {
            whitespace: true,
            message: "Espacio no válido",
            trigger: "change",
          },
        ],
        nacioDueñoDerchAud: [
          {
            trigger: "change",
          },
        ],
        derechosAud: [
          {
            trigger: "change",
          },
        ],
        descripEspAud: [
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
        descripIngAud: [
          {
            whitespace: true,
            message: "Espacio no válido",
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
    if (this.action_modal === "crear") {
      this.codigo = this.generar_codigo(
        this.lista_dividida(this.audiovisuals_list).code,
        "codigo"
      );
      this.codigoIsrc = this.generar_codigo(
        this.lista_dividida(this.audiovisuals_list).isrc,
        "isrc"
      );
    } else if (this.action_modal === "detalles") {
      this.active_tab = "2";
    }
    if (this.action_modal === "editar" && !this.is_isrc()) {
      this.codigPais = "CU";
      this.identificador = this.generar_codigo(
        this.lista_dividida(this.audiovisuals_list).isrc,
        "isrc"
      );
      this.codig_to_isrc = true;
    }
  },
  computed: {
    active() {
      if (this.action_modal === "editar") {
        console.log("as");
        return !this
          .compare_object /* ||
          (this.valid_image &&
            this.file_list.length !== 0 &&
            this.file_list[0].uid !== this.audiovisual_modal.id) */;
      } else
        return (
          this.audiovisual_modal.tituloAud &&
          this.audiovisual_modal.clasifAud &&
          this.audiovisual_modal.generoAud &&
          this.audiovisual_modal.añoFinAud &&
          this.valid_image
        );
    },
  },
  methods: {
    /*
     *Método que compara los campos editables del producto para saber si se ha modificado
     */
    compare_object() {
      console.log("wds");
      this.audiovisual_modal.makingOfAud = this.makingOfAud === true ? 1 : 0;
      console.log(this.audiovisual_modal.paisGrabAud === this.audiovisual.paisGrabAud);
      return (
        this.audiovisual_modal.paisGrabAud === this.audiovisual.paisGrabAud/*  &&
        this.audiovisual_modal.makingOfAud === this.audiovisual.makingOfAud &&
        this.audiovisual_modal.fenomRefAud === this.audiovisual.fenomRefAud &&
        this.audiovisual_modal.duracionAud === this.audiovisual.duracionAud &&
        this.audiovisual_modal.tituloAud === this.audiovisual.tituloAud &&
        this.audiovisual_modal.generoAud === this.audiovisual.generoAud &&
        this.audiovisual_modal.clasifAud === this.audiovisual.clasifAud &&
        this.audiovisual_modal.añoFinAud === this.audiovisual.añoFinAud &&
        this.audiovisual_modal.dueñoDerchAud ===
          this.audiovisual.dueñoDerchAud &&
        this.audiovisual_modal.nacioDueñoDerchAud ===
          this.audiovisual.nacioDueñoDerchAud &&
        this.audiovisual_modal.derechosAud === this.audiovisual.derechosAud &&
        this.audiovisual_modal.descripEspAud ===
          this.audiovisual.descripEspAud &&
        this.audiovisual_modal.descripIngAud ===
          this.audiovisual.descripIngAud &&
        this.compareArrays(
          this.audiovisual_modal.idiomaAud,
          this.audiovisual.idiomaAud
        ) &&
        this.compareArrays(
          this.audiovisual_modal.subtitulosAud,
          this.audiovisual.subtitulosAud
        ) &&
        this.compareArrays(
          this.audiovisual_modal.etiquetasAud,
          this.audiovisual.etiquetasAud
        ) */
      );
    },
    moment,
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
        if (this.action_modal !== "detalles") {
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
      }
    },
    handle_cancel(e) {
      if (e === "cancelar") {
        if (this.$refs.general_form !== undefined) {
          this.$refs.general_form.resetFields();
        }
        this.tabs_list = [];
        this.active_tab = "1";
        this.tab_visibility = true;
        this.show = false;
        this.codig_to_isrc = false;
        this.$emit("close_modal", this.show);
        if (this.action_modal !== "detalles") {
          this.$toast.success(this.action_close, "¡Éxito!", {
            timeout: 2000,
            color: "orange",
          });
        }
      } else {
        this.$refs.general_form.resetFields();
        this.tabs_list = [];
        this.active_tab = "1";
        this.tab_visibility = true;
        this.show = false;
        this.$emit("close_modal", this.show);
      }
    },
    validate() {
      if (
        this.action_modal === "editar" &&
        this.is_isrc() &&
        this.audiovisual_modal.generoAud !== this.genero_pivot
      ) {
        if (this.audiovisual_modal.identificador === undefined) {
          this.audiovisual_modal.identificador = this.identificador;
        }
        if (this.audiovisual_modal.codigPais === undefined) {
          this.audiovisual_modal.codigPais = this.codigPais;
        }
        if (!this.codig_to_isrc) {
          if (this.audiovisual_modal.codigRegistro === undefined) {
            this.audiovisual_modal.codigRegistro = this.codigRegistro;
          }
          if (this.audiovisual_modal.anhoRegistro === undefined) {
            this.audiovisual_modal.anhoRegistro = this.anhoRegistro;
          }
        }
      }
      if (this.audiovisual_modal.codigAud === undefined) {
        this.audiovisual_modal.codigAud = this.codigo;
      }
      if (this.audiovisual_modal.identificador === undefined) {
        this.audiovisual_modal.identificador = this.codigoIsrc;
      }
      if (this.audiovisual_modal.codigPais === undefined) {
        this.audiovisual_modal.codigPais = "CU";
      }
      if (!this.used) {
        if (this.tabs_list.indexOf("tab_1") !== -1) {
          this.$refs.general_form.validate((valid) => {
            if (valid) {
              if (this.file_list.length !== 0) {
                return this.confirm();
              }
            } else
              this.$message.warning(
                "Hay problemas en la pestaña Generales, por favor antes de continuar revísela!",
                5
              );
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
      if (this.action_modal === "editar") {
        if (
          this.validate_isrc(
            this.audiovisual_modal.isrcAud,
            this.audiovisuals_list
          )
        ) {
          this.text_button = "Editando...";
          axios
            .post(`/audiovisuales/editar`, form_data, {
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
                "Se ha modificado el audiovisual correctamente",
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
          this.spinning = false;
          this.waiting = false;
          this.isrc_validation = "duplicated-isrc-error";
          this.$message.error("El código ISRC utilizado ya existe.");
          this.audiovisual_modal.codigAud = this.codigo;
        }
      } else {
        if (
          this.validate_isrc(
            this.audiovisual_modal.isrcAud,
            this.audiovisuals_list
          )
        ) {
          this.text_button = "Creando...";
          axios
            .post("/audiovisuales", form_data, {
              headers: {
                "Content-Type": "multipart/form-data",
              },
            })
            .then((res) => {
              this.text_button = "Crear";
              this.spinning = false;
              this.waiting = false;
              this.handle_cancel();
              this.$emit("actualizar");
              this.$toast.success(
                "Se ha creado el audiovisual correctamente",
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
        } else {
          this.spinning = false;
          this.waiting = false;
          this.isrc_validation = "duplicated-isrc-error";
          this.$message.error("El código ISRC utilizado ya existe.");
          this.audiovisual_modal.codigAud = this.codigo;
        }
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
      if (this.audiovisual_modal.derechosAud === undefined) {
        this.audiovisual_modal.derechosAud = "";
      } else if (this.audiovisual_modal.derechosAud === null) {
        this.audiovisual_modal.derechosAud = "";
      }
      if (this.audiovisual_modal.nacioDueñoDerchAud === undefined) {
        this.audiovisual_modal.nacioDueñoDerchAud = "";
      } else if (this.audiovisual_modal.nacioDueñoDerchAud === null) {
        this.audiovisual_modal.nacioDueñoDerchAud = "";
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
      if (this.action_modal === "editar" || this.action_modal === "detalles") {
        form_data.append("id", this.audiovisual_modal.id);
      }
      if (
        this.action_modal === "crear" ||
        this.audiovisual_modal.generoAud !== this.genero_pivot
      ) {
        if (this.is_isrc()) {
          console.log(this.audiovisual_modal.codigPais);
          this.audiovisual_modal.isrcAud =
            "" +
            this.audiovisual_modal.codigPais.toUpperCase() +
            this.audiovisual_modal.codigRegistro.toUpperCase() +
            this.audiovisual_modal.anhoRegistro +
            this.audiovisual_modal.identificador;
        }
      }
      if (this.is_isrc()) {
        if (this.audiovisual_modal.identificador === undefined) {
          this.audiovisual_modal.identificador = this.codigoIsrc;
        }
        if (this.audiovisual_modal.codigPais === undefined) {
          this.audiovisual_modal.codigPais = "CU";
        }
        form_data.append("isrcAud", this.audiovisual_modal.isrcAud);
      } else {
        if (this.audiovisual_modal.codigAud === undefined) {
          this.audiovisual_modal.codigAud = this.codigo;
        }
        this.codigo = this.audiovisual_modal.codigAud;
        this.audiovisual_modal.codigAud =
          "AUDV-" + this.audiovisual_modal.codigAud;
        form_data.append("codigAud", this.audiovisual_modal.codigAud);
      }
      form_data.append("tituloAud", this.audiovisual_modal.tituloAud);
      form_data.append("clasifAud", this.audiovisual_modal.clasifAud);
      form_data.append("generoAud", this.audiovisual_modal.generoAud);
      form_data.append("duracionAud", this.audiovisual_modal.duracionAud);
      form_data.append("añoFinAud", this.audiovisual_modal.añoFinAud);
      form_data.append("idiomaAud", idiomas);
      form_data.append("subtitulosAud", subtitulos);
      form_data.append("fenomRefAud", this.audiovisual_modal.fenomRefAud);
      form_data.append("etiquetasAud", etiquetas);
      form_data.append("dueñoDerchAud", this.audiovisual_modal.dueñoDerchAud);
      form_data.append(
        "nacioDueñoDerchAud",
        this.audiovisual_modal.nacioDueñoDerchAud
      );
      form_data.append("derechosAud", this.audiovisual_modal.derechosAud);
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
      return form_data;
    },
    set_action() {
      if (this.audiovisual.productos_audvs) {
        this.tab_visibility = false;
        this.active_tab = "2";
        this.tabs_list.push("tab_1");
      }
      if (this.action_modal === "editar") {
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
        this.audiovisual.codigAud = this.audiovisual.codigAud;
        this.audiovisual.productos.forEach((element) => {
          this.audiovisual.productos_audvs.push(element.id);
        });
        this.audiovisual_modal = { ...this.audiovisual };
        if (!this.is_isrc()) {
          this.audiovisual_modal.codigAud = this.audiovisual.codigAud.substr(5);
          this.codigoIsrc = this.generar_codigo(
            this.lista_dividida(this.audiovisuals_list).isrc,
            "isrc"
          );
          this.codig_pivot = this.audiovisual_modal.codigAud;
        } else {
          this.audiovisual_modal.codigAud = this.codigo = this.generar_codigo(
            this.lista_dividida(this.audiovisuals_list).code,
            "codigo"
          );
          this.isrc_pivot = this.audiovisual_modal.isrcAud;
          this.codigPais = this.isrc_pivot.substr(0, 2);
          this.codigRegistro = this.isrc_pivot.substr(2, 3);
          this.anhoRegistro = this.isrc_pivot.substr(5, 2);
          this.identificador = this.isrc_pivot.substr(7);
        }
        this.makingOfAud =
          this.audiovisual_modal.makingOfAud === 0 ? false : true;
        if (this.audiovisual.etiquetasAud !== null) {
          this.audiovisual.etiquetasAud = this.audiovisual.etiquetasAud.split(
            " "
          );
        } else this.audiovisual.etiquetasAud = [];
        if (this.audiovisual_modal.etiquetasAud !== null) {
          this.audiovisual_modal.etiquetasAud = this.audiovisual_modal.etiquetasAud.split(
            " "
          );
        } else this.audiovisual_modal.etiquetasAud = [];
        if (this.audiovisual.idiomaAud !== null) {
          this.audiovisual.idiomaAud = this.audiovisual.idiomaAud.split(" ");
        } else this.audiovisual.idiomaAud = [];
        if (this.audiovisual_modal.idiomaAud !== null) {
          this.audiovisual_modal.idiomaAud = this.audiovisual_modal.idiomaAud.split(
            " "
          );
        } else this.audiovisual_modal.idiomaAud = [];
        if (this.audiovisual.subtitulosAud !== null) {
          this.audiovisual.subtitulosAud = this.audiovisual.subtitulosAud.split(
            " "
          );
        } else this.audiovisual.subtitulosAud = [];
        if (this.audiovisual_modal.subtitulosAud !== null) {
          this.audiovisual_modal.subtitulosAud = this.audiovisual_modal.subtitulosAud.split(
            " "
          );
        } else this.audiovisual_modal.subtitulosAud = [];
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
        this.genero_pivot = this.audiovisual_modal.generoAud;
      } else if (this.action_modal === "detalles") {
        this.action_cancel_title = "¿Desea cerrar la vista de detalles?";
        this.action_title = "¿Desea guardar los cambios en el Audiovisual?";
        this.action_close = "La vista de detalles fue cerrada correctamente";
        this.audiovisual.descripEspAud =
          this.audiovisual.descripEspAud === null
            ? ""
            : this.audiovisual.descripEspAud;
        this.audiovisual.descripIngAud =
          this.audiovisual.descripIngAud === null
            ? ""
            : this.audiovisual.descripIngAud;
        this.audiovisual.productos_audvs = [];
        this.audiovisual.codigAud = this.audiovisual.codigAud;
        this.audiovisual.productos.forEach((element) => {
          this.audiovisual.productos_audvs.push(element.id);
        });
        this.audiovisual_modal = { ...this.audiovisual };
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
        this.audiovisual_modal = { ...this.audiovisual };
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
          this.idiomas = this.list_nomenclators[4][0];
          this.subtitulos = this.list_nomenclators[5][0];
          this.nacionalidades = this.list_nomenclators[6][0];
        })
        .catch((error) => {
          this.$toast.error("Ha ocurrido un error", "¡Error!", {
            timeout: 2000,
          });
        });
    },

    //Metodos para generar el codigo
    //Este es el único método que varia de un módulo a otro
    crear_arr_codig(arr, type) {
      let answer = [];
      for (let i = 0; i < arr.length; i++) {
        if (type === "codigo") {
          answer.push(parseInt(arr[i].codigAud.substr(5, 8)));
        } else {
          answer.push(parseInt(arr[i].isrcAud.substr(7)));
        }
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
    generar_codigo(arr, type) {
      let list = this.ordenamiento_burbuja(this.crear_arr_codig(arr, type));
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
      return this.crear_codigo(answer, type);
    },
    crear_codigo(number, type) {
      switch (number.toString().length) {
        case 1:
          return type === "codigo" ? "000" + number : "0000" + number;
        case 2:
          return type === "codigo" ? "00" + number : "000" + number;
        case 3:
          return type === "codigo" ? "0" + number : "00" + number;
        case 4:
          return type === "isrc" ? "0" + number : number.toString();
        case 4:
          return number.toString();
        default:
          break;
      }
    },
    //Fin de metodos para generar el codigo

    validate_isrc(isrc, list) {
      if (this.is_isrc()) {
        for (let index = 0; index < list.length; index++) {
          if (list[index].isrcAud === isrc) {
            return false;
          }
        }
      }
      return true;
    },

    is_isrc() {
      return (
        this.audiovisual_modal.generoAud === "Concierto" ||
        this.audiovisual_modal.generoAud === "Documental" ||
        this.audiovisual_modal.generoAud === "Video Clip"
      );
    },

    lista_dividida(array) {
      let isrc = [];
      let code = [];
      for (let index = 0; index < array.length; index++) {
        if (
          array[index].generoAud === "Concierto" ||
          array[index].generoAud === "Documental" ||
          array[index].generoAud === "Video Clip"
        ) {
          isrc.push(array[index]);
        } else {
          code.push(array[index]);
        }
      }
      return { isrc: isrc, code: code };
    },
  },
};
</script>

<style>
/* #modal_gestionar_audiovisuales .ant-col-6 {
  width: 50% !important;
} */
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
#modal_gestionar_audiovisuales .ant-mentions textarea {
  height: 32px !important;
}
#modal_gestionar_audiovisuales .description textarea {
  height: 150px !important;
}
#small .ant-form-item-control {
  width: 70%;
}
#etiqueta .ant-select-dropdown {
  display: none !important;
}
.isrc .ant-form-item-children-icon {
  display: none !important;
}
.isrc .ant-input {
  padding-right: 0px !important;
}
.ant-time-picker {
  width: 100% !important;
}
#modal_gestionar_audiovisuales .duplicated-isrc-error {
  border-color: rgb(243, 107, 100) !important;
}
</style>

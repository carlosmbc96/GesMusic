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
          :disabled="tab_1"
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
                      <h4>Selector de Productos:</h4>
                    </div>
                  </a-col>
                </a-row>
                <a-col span="12">
                  <a-form-model-item
                    label="Código:"
                    has-feedback
                    prop="productos_audvs"
                  >
                    <a-select
                      :loading="loading_nomenclators"
                      mode="multiple"
                      :showArrow="true"
                      v-model="audiovisual_modal.productos_audvs"
                      style="width: 50% !important"
                      :disabled="disabled"
                    >
                      <template slot="notFoundContent">
                        <a-empty description="Sin resultados" />
                      </template>
                      <a-select-option
                        v-for="product in products"
                        :key="product.codigProd"
                        :value="product.id"
                      >
                        {{ product.codigProd }}
                      </a-select-option>
                    </a-select>
                  </a-form-model-item>
                  <a-form-model-item label="Nombre:">
                    <a-select
                      :loading="loading_nomenclators"
                      mode="multiple"
                      :showArrow="true"
                      v-model="audiovisual_modal.productos_audvs"
                      :disabled="disabled"
                    >
                      <template slot="notFoundContent">
                        <a-empty description="Sin resultados" />
                      </template>
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
                  <a-row>
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
                                label="Género Audiovisual:"
                                prop="generoAud"
                              >
                                <a-select
                                  :loading="loading_nomenclators"
                                  :getPopupContainer="
                                    (trigger) => trigger.parentNode
                                  "
                                  option-filter-prop="children"
                                  :filter-option="filter_option"
                                  show-search
                                  :disabled="disabled"
                                  v-model="audiovisual_modal.generoAud"
                                >
                                  <template slot="notFoundContent">
                                    <a-empty description="Sin resultados" />
                                  </template>
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
                                label="Género Audiovisual:"
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
                                label="Código:"
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
                                  (audiovisual_modal.generoAud ===
                                    'Entrevista' ||
                                    audiovisual_modal.generoAud ===
                                      'Making of' ||
                                    audiovisual_modal.generoAud === 'Trailers')
                                "
                                label="Código:"
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
                                label="Código:"
                                v-if="
                                  action_modal === 'detalles' &&
                                  (audiovisual_modal.generoAud ===
                                    'Entrevista' ||
                                    audiovisual_modal.generoAud ===
                                      'Making of' ||
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
                                    title="Código de dos letras que representan el país Ej: CU (Cuba)"
                                  >
                                    <a-form-model-item
                                      :validate-status="show_error"
                                      prop="codigPais"
                                      has-feedback
                                      label="ISRC:"
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
                                        v-model="
                                          audiovisual_modal.codigRegistro
                                        "
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
                                    title="Dos últimos dígitos del año de registro Ej: 21 (2021)"
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
                                        v-model="
                                          audiovisual_modal.identificador
                                        "
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
                                    title="Código de dos letras que representan el país Ej: CU (Cuba)"
                                  >
                                    <a-form-model-item
                                      :validate-status="show_error"
                                      prop="codigPais"
                                      has-feedback
                                      label="ISRC:"
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
                                        v-model="
                                          audiovisual_modal.codigRegistro
                                        "
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
                                        v-model="
                                          audiovisual_modal.codigRegistro
                                        "
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
                                    title="Dos últimos dígitos del año de registro Ej: 21 (2021)"
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
                                        v-model="
                                          audiovisual_modal.identificador
                                        "
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
                            label="ISRC:"
                          >
                            <a-input :disabled="true" v-model="pretty_isrc" />
                          </a-form-model-item>
                          <a-form-model-item
                            label="ISRC:"
                            v-if="
                              action_modal === 'detalles' &&
                              (audiovisual_modal.generoAud === 'Concierto' ||
                                audiovisual_modal.generoAud === 'Video Clip' ||
                                audiovisual_modal.generoAud === 'Documental')
                            "
                          >
                            <a-mentions readonly :placeholder="pretty_isrc">
                            </a-mentions>
                          </a-form-model-item>
                        </a-col>
                      </a-row>
                      <a-row>
                        <a-col span="11">
                          <a-form-model-item
                            v-if="action_modal !== 'detalles'"
                            has-feedback
                            label="Clasificación Audiovisual:"
                            prop="clasifAud"
                          >
                            <a-select
                              :loading="loading_nomenclators"
                              :getPopupContainer="
                                (trigger) => trigger.parentNode
                              "
                              option-filter-prop="children"
                              :filter-option="filter_option"
                              show-search
                              :disabled="disabled"
                              v-model="audiovisual_modal.clasifAud"
                            >
                              <template slot="notFoundContent">
                                <a-empty description="Sin resultados" />
                              </template>
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
                            label="Clasificación Audiovisual:"
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
                            label="País de Grabación:"
                            prop="paisGrabAud"
                          >
                            <a-select
                              :loading="loading_nomenclators"
                              :getPopupContainer="
                                (trigger) => trigger.parentNode
                              "
                              option-filter-prop="children"
                              :filter-option="filter_option"
                              show-search
                              :disabled="disabled"
                              v-model="audiovisual_modal.paisGrabAud"
                            >
                              <template slot="notFoundContent">
                                <a-empty description="Sin resultados" />
                              </template>
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
                            label="País de Grabación:"
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
                            label="Idiomas:"
                            prop="idiomaAud"
                          >
                            <a-select
                              :loading="loading_nomenclators"
                              :showArrow="true"
                              :getPopupContainer="
                                (trigger) => trigger.parentNode
                              "
                              mode="multiple"
                              :disabled="disabled"
                              v-model="audiovisual_modal.idiomaAud"
                            >
                              <template slot="notFoundContent">
                                <a-empty description="Sin resultados" />
                              </template>
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
                            label="Idiomas:"
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
                            label="Fenómeno de Referencia:"
                          >
                            <a-input
                              :disabled="disabled"
                              v-model="audiovisual_modal.fenomRefAud"
                            />
                          </a-form-model-item>
                          <a-form-model-item
                            label="Fenómeno de Referencia:"
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
                              label="Etiquetas:"
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
                            label="Etiquetas:"
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
                            label="Título:"
                          >
                            <a-input
                              :disabled="disabled"
                              v-model="audiovisual_modal.tituloAud"
                            />
                          </a-form-model-item>
                          <a-form-model-item
                            label="Título:"
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
                            label="Duración:"
                          >
                            <a-time-picker
                              :getPopupContainer="
                                (trigger) => trigger.parentNode
                              "
                              :default-open-value="
                                moment('00:00:00', 'HH:mm:ss')
                              "
                              :disabled="disabled"
                              :valueFormat="'HH:mm:ss'"
                              placeholder="hh:mm:ss"
                              v-model="audiovisual_modal.duracionAud"
                            />
                          </a-form-model-item>
                          <a-form-model-item
                            label="Duración:"
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
                            label="Año de Finalización:"
                            prop="añoFinAud"
                          >
                            <a-select
                              :loading="loading_nomenclators"
                              :getPopupContainer="
                                (trigger) => trigger.parentNode
                              "
                              option-filter-prop="children"
                              :filter-option="filter_option"
                              show-search
                              :disabled="disabled"
                              v-model="audiovisual_modal.añoFinAud"
                            >
                              <template slot="notFoundContent">
                                <a-empty description="Sin resultados" />
                              </template>
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
                            label="Año de Finalización:"
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
                            label="Subtítulos:"
                            prop="subtitulosAud"
                          >
                            <a-select
                              :loading="loading_nomenclators"
                              :showArrow="true"
                              :getPopupContainer="
                                (trigger) => trigger.parentNode
                              "
                              mode="multiple"
                              :disabled="disabled"
                              v-model="audiovisual_modal.subtitulosAud"
                            >
                              <template slot="notFoundContent">
                                <a-empty description="Sin resultados" />
                              </template>
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
                            label="Subtítulos:"
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
                        <h4>Datos Dueño de los Derechos</h4>
                      </div>
                      <a-form-model-item
                        v-if="action_modal !== 'detalles'"
                        prop="dueñoDerchAud"
                        has-feedback
                        label="Nombres y Apellidos:"
                      >
                        <a-input
                          :disabled="disabled"
                          v-model="audiovisual_modal.dueñoDerchAud"
                        />
                      </a-form-model-item>
                      <a-form-model-item
                        label="Nombres y Apellidos:"
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
                        label="Nacionalidad:"
                      >
                        <a-select
                          :loading="loading_nomenclators"
                          :getPopupContainer="(trigger) => trigger.parentNode"
                          option-filter-prop="children"
                          :filter-option="filter_option"
                          show-search
                          :disabled="disabled"
                          v-model="audiovisual_modal.nacioDueñoDerchAud"
                        >
                          <template slot="notFoundContent">
                            <a-empty description="Sin resultados" />
                          </template>
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
                        label="Nacionalidad:"
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
                        label="Derechos sobre el Audiovisual:"
                        prop="derechosAud"
                      >
                        <a-select
                          :loading="loading_nomenclators"
                          :getPopupContainer="(trigger) => trigger.parentNode"
                          option-filter-prop="children"
                          :filter-option="filter_option"
                          show-search
                          :disabled="disabled"
                          v-model="audiovisual_modal.derechosAud"
                        >
                          <template slot="notFoundContent">
                            <a-empty description="Sin resultados" />
                          </template>
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
                        label="Derechos sobre el Audiovisual:"
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
                        label="Descripción en Español del Audiovisual:"
                        prop="descripEspAud"
                      >
                        <a-input
                          :disabled="disabled"
                          style="width: 100%; height: 120px; margin-top: 4px"
                          v-model="audiovisual_modal.descripEspAud"
                          type="textarea"
                        />
                      </a-form-model-item>
                      <a-form-model-item
                        label="Descripción en Español del Audiovisual:"
                        v-if="action_modal === 'detalles'"
                      >
                        <div class="description">
                          <a-mentions
                            style="margin-top: 3px"
                            readonly
                            :placeholder="audiovisual_modal.descripEspAud"
                          >
                          </a-mentions>
                        </div>
                      </a-form-model-item>
                      <a-form-model-item
                        v-if="action_modal !== 'detalles'"
                        has-feedback
                        label="Descripción en Inglés del Audiovisual:"
                        prop="descripIngAud"
                      >
                        <a-input
                          :disabled="disabled"
                          style="width: 100%; height: 120px; margin-top: 4px"
                          v-model="audiovisual_modal.descripIngAud"
                          type="textarea"
                        />
                      </a-form-model-item>
                      <a-form-model-item
                        label="Descripción en Inglés del Audiovisual:"
                        v-if="action_modal === 'detalles'"
                      >
                        <div class="description">
                          <a-mentions
                            style="margin-top: 3px"
                            readonly
                            :placeholder="audiovisual_modal.descripIngAud"
                          >
                          </a-mentions>
                        </div>
                      </a-form-model-item>
                    </a-col>
                  </a-row>
                  <a-row style="margin-top: 20px; margin-bottom: 20px">
                    <a-col span="11">
                      <a-row>
                        <a-col span="24">
                          <div class="section-title">
                            <h4>Realizadores</h4>
                          </div>
                          <a-alert
                            v-if="
                              action_modal === 'detalles' &&
                              $store.getters.getRealizadoresFormGetters
                                .length === 0
                            "
                            message="El audiovisual no posee realizadores"
                            type="info"
                            show-icon
                          />
                        </a-col>
                      </a-row>
                      <a-row v-if="action_modal !== 'detalles'">
                        <a-col span="24">
                          <a-mentions
                            readonly
                            v-if="
                              $store.getters.getRealizadoresFormGetters
                                .length === 0
                            "
                          ></a-mentions>
                          <transition-group name="list">
                            <a-form-model-item
                              v-for="(realizador, index) in $store.getters
                                .getRealizadoresFormGetters"
                              :key="realizador.id"
                              v-bind="index === 0 ? formItemLayout : {}"
                              class="list-item"
                              style="margin-bottom: 0px !important"
                            >
                              <a-row>
                                <a-col span="22">
                                  <a-mentions
                                    style="margin-top: 3px"
                                    readonly
                                    :placeholder="
                                      realizador.nombreApellidosRealiz
                                    "
                                  ></a-mentions>
                                </a-col>
                                <a-col span="2" style="float: right">
                                  <a-button
                                    class="dynamic-delete-button"
                                    @click="remove_realizador(realizador)"
                                  >
                                    <small>
                                      <b style="vertical-align: top"> x </b>
                                    </small>
                                  </a-button>
                                </a-col>
                              </a-row>
                            </a-form-model-item>
                          </transition-group>
                          <a-row style="margin-top: 20px">
                            <a-col span="24">
                              <div class="section-title">
                                <h5>Selector de Realizadores:</h5>
                              </div>
                              <a-form-model
                                ref="formularioAgregarRealizador"
                                :layout="'horizontal'"
                                :model="audiovisual_modal"
                              >
                                <a-form-model-item>
                                  <a-select
                                    :loading="loading_nomenclators"
                                    :getPopupContainer="
                                      (trigger) => trigger.parentNode
                                    "
                                    placeholder="Nombres y Apellidos"
                                    option-filter-prop="children"
                                    :filter-option="filter_option"
                                    show-search
                                    v-model="audiovisual_modal.realizadores"
                                    :disabled="disabled"
                                  >
                                    <template slot="notFoundContent">
                                      <a-empty description="Sin resultados" />
                                    </template>
                                    <a-select-option
                                      v-for="realizador in $store.getters
                                        .getAllRealizadoresFormGetters"
                                      :key="realizador.id"
                                      :value="realizador.id"
                                    >
                                      {{ realizador.nombreApellidosRealiz }}
                                    </a-select-option>
                                  </a-select>
                                </a-form-model-item>
                                <a-row>
                                  <a-col span="12">
                                    <a-form-model-item v-bind="formItemLayout">
                                      <a-button
                                        :disabled="disabled"
                                        type="primary"
                                        @click="add_realizador"
                                      >
                                        <a-icon type="plus" />
                                        Agregar Realizador
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
                                        @click="new_realizador"
                                      >
                                        <a-icon type="plus" />
                                        Crear Realizador
                                      </a-button>
                                    </a-form-model-item>
                                  </a-col>
                                </a-row>
                              </a-form-model>
                            </a-col>
                          </a-row>
                        </a-col>
                      </a-row>
                      <a-row v-else>
                        <a-col span="24">
                          <a-form-model-item
                            v-for="(realizador, index) in $store.getters
                              .getRealizadoresFormGetters"
                            :key="realizador.id"
                            v-bind="index === 0 ? formItemLayout : {}"
                          >
                            <a-mentions
                              readonly
                              :placeholder="realizador.nombreApellidosRealiz"
                            ></a-mentions>
                          </a-form-model-item>
                        </a-col>
                      </a-row>
                    </a-col>
                    <a-col span="11" style="float: right">
                      <a-row>
                        <a-col span="24">
                          <div class="section-title">
                            <h4>Entrevistados</h4>
                          </div>
                          <a-alert
                            v-if="
                              action_modal === 'detalles' &&
                              $store.getters.getEntrevistadosFormGetters
                                .length === 0
                            "
                            message="El audiovisual no posee entrevistados"
                            type="info"
                            show-icon
                          />
                        </a-col>
                      </a-row>
                      <a-row v-if="action_modal !== 'detalles'">
                        <a-col span="24">
                          <a-mentions
                            readonly
                            v-if="
                              $store.getters.getEntrevistadosFormGetters
                                .length === 0
                            "
                          ></a-mentions>
                          <transition-group name="list">
                            <a-form-model-item
                              v-for="(entrevistado, index) in $store.getters
                                .getEntrevistadosFormGetters"
                              :key="entrevistado.id"
                              v-bind="index === 0 ? formItemLayout : {}"
                              class="list-item"
                              style="margin-bottom: 0px !important"
                            >
                              <a-row>
                                <a-col span="22">
                                  <a-mentions
                                    readonly
                                    style="margin-top: 3px"
                                    :placeholder="
                                      entrevistado.nombreApellidosEntrv
                                    "
                                  ></a-mentions>
                                </a-col>
                                <a-col span="2" style="float: right">
                                  <a-button
                                    class="dynamic-delete-button"
                                    @click="remove_entrevistado(entrevistado)"
                                  >
                                    <small>
                                      <b style="vertical-align: top"> x </b>
                                    </small>
                                  </a-button>
                                </a-col>
                              </a-row>
                            </a-form-model-item>
                          </transition-group>
                          <a-row style="margin-top: 20px">
                            <a-col span="24">
                              <div class="section-title">
                                <h5>Selector de Entrevistados:</h5>
                              </div>
                              <a-form-model
                                ref="formularioAgregarEntrevistado"
                                :layout="'horizontal'"
                                :model="audiovisual_modal"
                              >
                                <a-form-model-item has-feedback>
                                  <a-select
                                    :loading="loading_nomenclators"
                                    :getPopupContainer="
                                      (trigger) => trigger.parentNode
                                    "
                                    placeholder="Nombres y Apellidos"
                                    option-filter-prop="children"
                                    :filter-option="filter_option"
                                    show-search
                                    v-model="audiovisual_modal.entrevistados"
                                    :disabled="disabled"
                                  >
                                    <template slot="notFoundContent">
                                      <a-empty description="Sin resultados" />
                                    </template>
                                    <a-select-option
                                      v-for="entrevistado in $store.getters
                                        .getAllEntrevistadosFormGetters"
                                      :key="entrevistado.id"
                                      :value="entrevistado.id"
                                    >
                                      {{ entrevistado.nombreApellidosEntrv }}
                                    </a-select-option>
                                  </a-select>
                                </a-form-model-item>
                                <a-row>
                                  <a-col span="12">
                                    <a-form-model-item v-bind="formItemLayout">
                                      <a-button
                                        :disabled="disabled"
                                        type="primary"
                                        @click="add_entrevistado"
                                      >
                                        <a-icon type="plus" />
                                        Agregar Entrevistado
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
                                        @click="new_entrevistado"
                                      >
                                        <a-icon type="plus" />
                                        Crear Entrevistado
                                      </a-button>
                                    </a-form-model-item>
                                  </a-col>
                                </a-row>
                              </a-form-model>
                            </a-col>
                          </a-row>
                        </a-col>
                      </a-row>
                      <a-row v-else>
                        <a-col span="24">
                          <a-form-model-item
                            v-for="(entrevistado, index) in $store.getters
                              .getEntrevistadosFormGetters"
                            :key="entrevistado.id"
                            v-bind="index === 0 ? formItemLayout : {}"
                          >
                            <a-mentions
                              readonly
                              :placeholder="entrevistado.nombreApellidosEntrv"
                            ></a-mentions>
                          </a-form-model-item>
                        </a-col>
                      </a-row>
                    </a-col>
                  </a-row>
                  <a-row
                    style="margin-top: 20px"
                    v-if="action_modal === 'crear'"
                  >
                    <a-col span="11">
                      <a-row>
                        <a-col span="24">
                          <div class="section-title">
                            <h4>Autores</h4>
                          </div>
                        </a-col>
                      </a-row>
                      <a-row>
                        <a-col span="24">
                          <a-mentions
                            readonly
                            v-if="
                              $store.getters.getAutoresFormGetters.length === 0
                            "
                          ></a-mentions>
                          <transition-group name="list">
                            <a-form-model-item
                              v-for="(autor, index) in $store.getters
                                .getAutoresFormGetters"
                              :key="autor.id"
                              v-bind="index === 0 ? formItemLayout : {}"
                              class="list-item"
                              style="margin-bottom: 0px !important"
                            >
                              <a-row>
                                <a-col span="22">
                                  <a-mentions
                                    style="margin-top: 3px"
                                    readonly
                                    :placeholder="
                                      autor.nombresAutr +
                                      ' ' +
                                      autor.apellidosAutr
                                    "
                                  ></a-mentions>
                                </a-col>
                                <a-col span="2" style="float: right">
                                  <a-button
                                    class="dynamic-delete-button"
                                    @click="remove_autor(autor)"
                                  >
                                    <small>
                                      <b style="vertical-align: top"> x </b>
                                    </small>
                                  </a-button>
                                </a-col>
                              </a-row>
                            </a-form-model-item>
                          </transition-group>
                          <a-row style="margin-top: 20px">
                            <a-col span="24">
                              <div class="section-title">
                                <h5>Selector de Autores:</h5>
                              </div>
                              <a-form-model
                                ref="formularioAgregarAutor"
                                :layout="'horizontal'"
                                :model="audiovisual_modal"
                              >
                                <a-form-model-item>
                                  <a-select
                                    :loading="loading_nomenclators"
                                    :getPopupContainer="
                                      (trigger) => trigger.parentNode
                                    "
                                    placeholder="Nombres y Apellidos"
                                    option-filter-prop="children"
                                    :filter-option="filter_option"
                                    show-search
                                    v-model="audiovisual_modal.autores"
                                    :disabled="disabled"
                                  >
                                    <template slot="notFoundContent">
                                      <a-empty description="Sin resultados" />
                                    </template>
                                    <a-select-option
                                      v-for="autor in $store.getters
                                        .getAllAutoresFormGetters"
                                      :key="autor.id"
                                      :value="autor.id"
                                    >
                                      {{
                                        autor.nombresAutr +
                                        " " +
                                        autor.apellidosAutr
                                      }}
                                    </a-select-option>
                                  </a-select>
                                </a-form-model-item>
                                <a-row>
                                  <a-col span="12">
                                    <a-form-model-item v-bind="formItemLayout">
                                      <a-button
                                        :disabled="disabled"
                                        type="primary"
                                        @click="add_autor"
                                      >
                                        <a-icon type="plus" />
                                        Agregar Autor
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
                                        @click="new_autor"
                                      >
                                        <a-icon type="plus" />
                                        Crear Autor
                                      </a-button>
                                    </a-form-model-item>
                                  </a-col>
                                </a-row>
                              </a-form-model>
                            </a-col>
                          </a-row>
                        </a-col>
                      </a-row>
                    </a-col>
                    <a-col span="11" style="float: right">
                      <a-row>
                        <a-col span="24">
                          <div class="section-title">
                            <h4>Intérpretes</h4>
                          </div>
                        </a-col>
                      </a-row>
                      <a-row v-if="action_modal !== 'detalles'">
                        <a-col span="24">
                          <a-mentions
                            readonly
                            v-if="
                              $store.getters.getInterpretesFormGetters
                                .length === 0
                            "
                          ></a-mentions>
                          <transition-group name="list">
                            <a-form-model-item
                              v-for="(interprete, index) in $store.getters
                                .getInterpretesFormGetters"
                              :key="interprete.id"
                              v-bind="index === 0 ? formItemLayout : {}"
                              class="list-item"
                              style="margin-bottom: 0px !important"
                            >
                              <a-row>
                                <a-col span="11">
                                  <div class="ant-form-item-label">
                                    <label>Nombre</label>
                                  </div>
                                  <a-mentions
                                    readonly
                                    style="margin-top: 3px"
                                    :placeholder="interprete.nombreInterp"
                                  ></a-mentions>
                                </a-col>
                                <a-col span="10" style="margin-left: 10px">
                                  <div class="ant-form-item-label">
                                    <label>Roles</label>
                                  </div>
                                  <a-select
                                    :loading="loading_nomenclators"
                                    :getPopupContainer="
                                      (trigger) => trigger.parentNode
                                    "
                                    :disabled="disabled"
                                    mode="multiple"
                                    v-model="interprete.role"
                                    class="interpretes-select"
                                  >
                                    <a-select-option
                                      v-for="rol in roles_interp"
                                      :key="rol.id"
                                      :value="rol.nombreTer"
                                    >
                                      {{ rol.nombreTer }}
                                    </a-select-option>
                                  </a-select>
                                </a-col>
                                <a-col
                                  span="2"
                                  style="float: right; margin-top: 40px"
                                >
                                  <a-button
                                    class="dynamic-delete-button"
                                    @click="remove_interprete(interprete)"
                                  >
                                    <small>
                                      <b style="vertical-align: top"> x </b>
                                    </small>
                                  </a-button>
                                </a-col>
                              </a-row>
                            </a-form-model-item>
                          </transition-group>
                          <a-row style="margin-top: 20px">
                            <a-col span="24">
                              <div class="section-title">
                                <h5>Selector de Intérpretes:</h5>
                              </div>
                              <a-form-model
                                ref="formularioAgregarInterprete"
                                :layout="'horizontal'"
                                :model="audiovisual_modal"
                              >
                                <a-form-model-item has-feedback>
                                  <a-select
                                    :loading="loading_nomenclators"
                                    :getPopupContainer="
                                      (trigger) => trigger.parentNode
                                    "
                                    placeholder="Nombres y Apellidos"
                                    option-filter-prop="children"
                                    :filter-option="filter_option"
                                    show-search
                                    v-model="audiovisual_modal.interpretes"
                                    :disabled="disabled"
                                  >
                                    <template slot="notFoundContent">
                                      <a-empty description="Sin resultados" />
                                    </template>
                                    <a-select-option
                                      v-for="interprete in $store.getters
                                        .getAllInterpretesFormGetters"
                                      :key="interprete.id"
                                      :value="interprete.id"
                                    >
                                      {{ interprete.nombreInterp }}
                                    </a-select-option>
                                  </a-select>
                                </a-form-model-item>
                                <a-row>
                                  <a-col span="12">
                                    <a-form-model-item v-bind="formItemLayout">
                                      <a-button
                                        :disabled="disabled"
                                        type="primary"
                                        @click="add_interprete"
                                      >
                                        <a-icon type="plus" />
                                        Agregar Interprete
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
                                        @click="new_interprete"
                                      >
                                        <a-icon type="plus" />
                                        Crear Interprete
                                      </a-button>
                                    </a-form-model-item>
                                  </a-col>
                                </a-row>
                              </a-form-model>
                            </a-col>
                          </a-row>
                        </a-col>
                      </a-row>
                    </a-col>
                  </a-row>
                </a-form-model>
              </div>
            </a-spin>
          </a-row>
          <a-row>
            <a-button
              v-if="action_modal !== 'crear'"
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
        <a-tab-pane key="3" :disabled="tab_3" v-if="action_modal !== 'crear'">
          <span slot="tab"> Autores/Intérptetes/Productos </span>
          <a-row>
            <a-col span="12">
              <div class="section-title">
                <h4>Autores/Intérptetes/Productos</h4>
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
              <tabla_autores
                v-if="current === 0"
                :detalles_prop="detalles"
                @reload="reload_parent"
                :entity="audiovisual_modal"
                entity_relation="audiovisuales"
                :vista_editar="vista_editar"
                @close_modal="show = $event"
              />
              <tabla_interpretes
                v-else-if="current === 1"
                :detalles_prop="detalles"
                @reload="reload_parent"
                :entity="audiovisual_modal"
                entity_relation="audiovisuales"
                :vista_editar="vista_editar"
                @close_modal="show = $event"
              />
              <tabla_productos
                v-else
                :detalles_prop="detalles_prod"
                @reload="reload_parent"
                :entity="audiovisual_modal"
                entity_relation="audiovisuales"
                :vista_editar="vista_editar"
                @close_modal="show = $event"
              />
            </div>
            <br />
          </div>

          <a-button
            v-if="active_tab !== '3'"
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
          </a-button>
        </a-tab-pane>
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
    <modal_management_realizadores
      v-if="visible_management_realizador"
      :action="action_management_realizadores"
      @close_modal="visible_management_realizador = $event"
      :realizadores_list="$store.getters.getAllRealizadoresStaticsFormGetters"
    />
    <modal_management_entrevistados
      v-if="visible_management_entrevistado"
      :action="action_management_entrevistados"
      @close_modal="visible_management_entrevistado = $event"
      :entrevistados_list="$store.getters.getAllEntrevistadosStaticsFormGetters"
    />
    <modal_management_autores
      v-if="visible_management_autor"
      :action="action_management_autores"
      @close_modal="visible_management_autor = $event"
      :autors_list="$store.getters.getAllAutoresStaticsFormGetters"
    />
    <modal_management_interpretes
      v-if="visible_management_interprete"
      :action="action_management_interpretes"
      @close_modal="visible_management_interprete = $event"
      :interp_list="$store.getters.getAllInterpretesStaticsFormGetters"
    />
  </div>
</template>

<script>
import moment from "../../../../../node_modules/moment";
import tabla_autores from "../Artistas/Autor/Tabla_Autores";
import tabla_interpretes from "../Artistas/Interprete/Tabla_Interpretes";
import modal_management_realizadores from "../Realizador/Modal_Gestionar_Realizador";
import modal_management_entrevistados from "../Entrevistado/Modal_Gestionar_Entrevistado";
import modal_management_autores from "../Artistas/Autor/Modal_Gestionar_Autor";
import modal_management_interpretes from "../Artistas/Interprete/Modal_Gestionar_Interprete";
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
        if (element.isrcAud.substr(7) === value) {
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
    let code_0000 = (rule, value, callback) => {
      if (value === "0000") {
        callback(new Error("El código no puede ser 0000"));
      } else callback();
    };
    let code_00000 = (rule, value, callback) => {
      if (value === "00000") {
        callback(new Error(" "));
      } else callback();
    };
    let custom_code_required = (rule, value, callback) => {
      console.log(value);
      if (this.codig_to_isrc || this.action_modal === "crear") {
        if (value === undefined) {
          callback(new Error(" "));
        } else callback();
      } else {
        if (value === "") {
          callback(new Error(" "));
        } else callback();
      }
    };
    return {
      steps: [
        {
          title: "Autores",
        },
        {
          title: "Intérpretes",
        },
        {
          title: "Productos",
        },
      ],
      loading_nomenclators: true,
      compare_realizadores: [],
      compare_entrevistados: [],
      vista_editar: true,
      current: 0,
      detalles_prod: true,
      detalles: false,
      action_management_entrevistados: "crear_entrevistado",
      action_management_realizadores: "crear_realizador",
      action_management_autores: "crear_autor",
      action_management_interpretes: "crear_interprete",
      relation: "",
      tab_1: false,
      tab_2: true,
      tab_3: true,
      tab_4: true,
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
      visible_management_entrevistado: false,
      visible_management_realizador: false,
      visible_management_autor: false,
      visible_management_interprete: false,
      pretty_isrc: "",
      roles_interp: [],
      formItemLayout: {
        wrapperCol: {
          xs: { span: 24, offset: 0 },
          sm: { span: 20, offset: 4 },
        },
      },
      rules: {
        codigAud: [
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
            validator: code_00000,
            trigger: "change",
          },
          {
            validator: validate_ISRC_unique,
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
        /* etiquetasAud: [
          {
            whitespace: true,
            message: "Espacio no válido",
            trigger: "change",
          },
        ], */
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
    if (this.audiovisual.tabla) {
      this.tabs_list.push("tab_1");
      this.tab_visibility = false;
      this.active_tab = "2";
    }
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
      this.detalles = true;
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
    if (this.is_isrc()) {
      this.pretty_isrc = this.build_pretty_isrc(this.audiovisual_modal.isrcAud);
    }
  },
  computed: {
    active() {
      if (this.action_modal === "editar") {
        let same_photo = false;
        if (this.file_list[0]) {
          same_photo = this.file_list[0].uid !== this.audiovisual_modal.id;
        }
        return (
          (same_photo || this.file_list.length === 0 || !this.compare_object) &&
          this.valid_image
        );
      } else
        return (
          this.audiovisual_modal.tituloAud &&
          this.audiovisual_modal.clasifAud &&
          this.audiovisual_modal.generoAud &&
          this.audiovisual_modal.añoFinAud &&
          this.valid_image
        );
    },
    /*
     *Método que compara los campos editables del producto para saber si se ha modificado
     */
    compare_object() {
      this.audiovisual_modal.makingOfAud = this.makingOfAud === true ? 1 : 0;
      if (this.active_tab === "3") {
        return false;
      } else {
        return (
          this.compareArrays(
            this.audiovisual_modal.productos_audvs,
            this.audiovisual.productos_audvs
          ) &&
          this.audiovisual_modal.paisGrabAud === this.audiovisual.paisGrabAud &&
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
          this.compareArraysComponents(
            this.compare_realizadores,
            this.$store.getters.getRealizadoresFormGetters
          ) &&
          this.compareArraysComponents(
            this.compare_entrevistados,
            this.$store.getters.getEntrevistadosFormGetters
          ) &&
          this.compareArrays(
            this.audiovisual_modal.etiquetasAud,
            this.audiovisual.etiquetasAud
          )
        );
      }
    },
  },
  methods: {
    compareArrays(old_array, new_array) {
      let igual_cont = 0;
      if (new_array.length === old_array.length) {
        for (let i = 0; i < old_array.length; i++) {
          if (old_array[i] === new_array[i]) {
            igual_cont++;
          }
        }
        if (igual_cont !== new_array.length) {
          return false;
        } else return true;
      } else return false;
    },
    compareArraysComponents(old_array, new_array) {
      let igual_cont = 0;
      if (new_array.length === old_array.length) {
        for (let i = 0; i < old_array.length; i++) {
          if (old_array[i].id === new_array[i].id) {
            igual_cont++;
          }
        }
        if (igual_cont !== new_array.length) {
          return false;
        } else return true;
      } else return false;
    },
    moment,
    onChange(current) {
      this.current = current;
    },
    siguiente(tab, siguienteTab) {
      if (tab === "tab_1") {
        this.$refs.formularioProduct.validate((valid) => {
          if (valid) {
            this.tab_2 = false;
            this.tab_1 = true;
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
            this.tab_2 = true;
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
      } else if (tab === "tab_3") {
        this.tab_4 = false;
        this.tab_3 = true;
        if (this.tabs_list.indexOf(tab) == -1) {
          this.tabs_list.push(tab);
        }
        this.active_tab = siguienteTab;
      }
    },
    reload_parent() {
      this.$emit("refresh");
    },
    handle_cancel(e) {
      if (e === "cancelar") {
        this.$emit("actualizar");
        if (this.$refs.general_form !== undefined) {
          this.$refs.general_form.resetFields();
        }
        if (
          this.$store.getters.getCreatedRealizadoresFormGetters.length !== 0
        ) {
          for (
            let index = 0;
            index <
            this.$store.getters.getCreatedRealizadoresFormGetters.length;
            index++
          ) {
            axios
              .delete(
                `realizadores/eliminar/${this.$store.getters.getCreatedRealizadoresFormGetters[index].id}`
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
        if (
          this.$store.getters.getCreatedEntrevistadosFormGetters.length !== 0
        ) {
          for (
            let index = 0;
            index <
            this.$store.getters.getCreatedEntrevistadosFormGetters.length;
            index++
          ) {
            axios
              .delete(
                `entrevistados/eliminar/${this.$store.getters.getCreatedEntrevistadosFormGetters[index].id}`
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
        if (this.$store.getters.getCreatedAutoresFormGetters.length !== 0) {
          for (
            let index = 0;
            index < this.$store.getters.getCreatedAutoresFormGetters.length;
            index++
          ) {
            axios
              .delete(
                `autores/eliminar/${this.$store.getters.getCreatedAutoresFormGetters[index].id}`
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
        if (this.$store.getters.getCreatedInterpretesFormGetters.length !== 0) {
          for (
            let index = 0;
            index < this.$store.getters.getCreatedInterpretesFormGetters.length;
            index++
          ) {
            axios
              .delete(
                `interpretes/eliminar/${this.$store.getters.getCreatedInterpretesFormGetters[index].id}`
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
        this.$store.state["realizadores"] = [];
        this.$store.state["created_realizadores"] = [];
        this.$store.state["all_realizadores"] = [];
        this.$store.state["all_realizadores_statics"] = [];
        this.$store.state["entrevistados"] = [];
        this.$store.state["created_entrevistados"] = [];
        this.$store.state["all_entrevistados"] = [];
        this.$store.state["all_entrevistados_statics"] = [];
        this.$store.state["autores"] = [];
        this.$store.state["created_autores"] = [];
        this.$store.state["all_autores"] = [];
        this.$store.state["all_autores_statics"] = [];
        this.$store.state["interpretes"] = [];
        this.$store.state["created_interpretes"] = [];
        this.$store.state["all_interpretes"] = [];
        this.$store.state["all_interpretes_statics"] = [];
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
        if (this.$refs.general_form !== undefined) {
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
      if (
        this.action_modal === "editar" &&
        this.is_isrc() &&
        this.audiovisual_modal.generoAud !== this.genero_pivot
      ) {
        if (!this.codig_to_isrc) {
          if (this.audiovisual_modal.codigRegistro === undefined) {
            this.audiovisual_modal.codigRegistro = this.codigRegistro;
          }
          if (this.audiovisual_modal.anhoRegistro === undefined) {
            this.audiovisual_modal.anhoRegistro = this.anhoRegistro;
          }
        }
      }
      if (!this.used) {
        if (this.active_tab !== "1") {
          if (this.tabs_list.indexOf("tab_1") !== -1) {
            this.$refs.general_form.validate((valid) => {
              if (valid) {
                return this.confirm();
              } else
                this.$toast.warning(
                  "Hay problemas en la pestaña Generales,<br> por favor antes de continuar revísela!",
                  "¡Atención!",
                  {
                    timeout: 4000,
                    id: "question",
                  }
                );
            });
          } else return this.confirm();
        } else return this.confirm();
      }
    },
    atras(tabAnterior) {
      if (this.active_tab === "2") {
        this.$refs.general_form.validate((valid) => {
          if (valid) {
            this.tab_2 = true;
            this.tab_1 = false;
            this.active_tab = tabAnterior;
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
      } else if (this.active_tab === "3") {
        this.tab_3 = true;
        this.tab_2 = false;
        this.active_tab = tabAnterior;
      } else if (this.active_tab === "4") {
        this.tab_4 = true;
        this.tab_3 = false;
        this.active_tab = tabAnterior;
      }
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
              axios
                .post("/audiovisuales/realizadores", {
                  realizadores: this.getRealizadoresID(),
                  id: response.data.id,
                })
                .then((res) => {
                  this.$store.state["realizadores"] = [];
                  this.$store.state["all_realizadores_statics"] = [];
                  this.$store.state["all_realizadores"] = [];
                  this.$store.state["created_realizadores"] = [];
                })
                .catch((error) => {
                  this.$toast.error("Ha ocurrido un error", "¡Error!", {
                    timeout: 2000,
                  });
                });
              axios
                .post("/audiovisuales/entrevistados", {
                  entrevistados: this.getEntrevistadosID(),
                  id: response.data.id,
                })
                .then((res) => {
                  this.$store.state["entrevistados"] = [];
                  this.$store.state["all_entrevistados_statics"] = [];
                  this.$store.state["all_entrevistados"] = [];
                  this.$store.state["created_entrevistados"] = [];
                })
                .catch((error) => {
                  this.$toast.error("Ha ocurrido un error", "¡Error!", {
                    timeout: 2000,
                  });
                });
              this.text_button = "Editar";
              this.spinning = false;
              this.waiting = false;
              this.handle_cancel();
              this.$emit("actualizar");
              this.$toast.success(
                "Se ha modificado el Audiovisual correctamente",
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
          this.$toast.warning(
            "El código ISRC utilizado ya existe!",
            "¡Atención!",
            {
              timeout: 4000,
              id: "question",
            }
          );
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
              axios
                .post("/audiovisuales/realizadores", {
                  realizadores: this.getRealizadoresID(),
                  id: res.data.id,
                })
                .then((res) => {
                  this.$store.state["realizadores"] = [];
                  this.$store.state["all_realizadores_statics"] = [];
                  this.$store.state["all_realizadores"] = [];
                  this.$store.state["created_realizadores"] = [];
                })
                .catch((error) => {
                  this.$toast.error("Ha ocurrido un error", "¡Error!", {
                    timeout: 2000,
                  });
                });
              axios
                .post("/audiovisuales/entrevistados", {
                  entrevistados: this.getEntrevistadosID(),
                  id: res.data.id,
                })
                .then((res) => {
                  this.$store.state["entrevistados"] = [];
                  this.$store.state["all_entrevistados_statics"] = [];
                  this.$store.state["all_entrevistados"] = [];
                  this.$store.state["created_entrevistados"] = [];
                })
                .catch((error) => {
                  this.$toast.error("Ha ocurrido un error", "¡Error!", {
                    timeout: 2000,
                  });
                });
              axios
                .post("/audiovisuales/autores", {
                  autores: this.getAutoresID(),
                  id: res.data.id,
                })
                .then((res) => {
                  this.$store.state["autores"] = [];
                  this.$store.state["all_autores_statics"] = [];
                  this.$store.state["all_autores"] = [];
                  this.$store.state["created_autores"] = [];
                })
                .catch((error) => {
                  this.$toast.error("Ha ocurrido un error", "¡Error!", {
                    timeout: 2000,
                  });
                });
              axios
                .post("/audiovisuales/interpretes", {
                  interpretes: this.getInterpretesID(),
                  id: res.data.id,
                })
                .then((res) => {
                  this.$store.state["interpretes"] = [];
                  this.$store.state["all_interpretes_statics"] = [];
                  this.$store.state["all_interpretes"] = [];
                  this.$store.state["created_interpretes"] = [];
                })
                .catch((error) => {
                  this.$toast.error("Ha ocurrido un error", "¡Error!", {
                    timeout: 2000,
                  });
                });
              this.text_button = "Crear";
              this.spinning = false;
              this.waiting = false;
              this.handle_cancel();
              this.$emit("actualizar");
              this.$toast.success(
                "Se ha creado el Audiovisual correctamente",
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
          this.$toast.warning(
            "El código ISRC utilizado ya existe!",
            "¡Atención!",
            {
              timeout: 4000,
            }
          );
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
          etiquetas += item + ",";
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
          if (
            this.audiovisual_modal.identificador === undefined &&
            this.audiovisual_modal.codigPais === undefined
          ) {
            this.audiovisual_modal.isrcAud =
              "" +
              "CU" +
              this.audiovisual_modal.codigRegistro.toUpperCase() +
              this.audiovisual_modal.anhoRegistro +
              this.codigo;
          } else if (this.audiovisual_modal.identificador === undefined) {
            this.audiovisual_modal.isrcAud =
              "" +
              this.audiovisual_modal.codigPais.toUpperCase() +
              this.audiovisual_modal.codigRegistro.toUpperCase() +
              this.audiovisual_modal.anhoRegistro +
              this.codigo;
          } else if (this.audiovisual_modal.codigPais === undefined) {
            this.audiovisual_modal.isrcAud =
              "" +
              "CU" +
              this.audiovisual_modal.codigRegistro.toUpperCase() +
              this.audiovisual_modal.anhoRegistro +
              this.audiovisual_modal.identificador;
          } else {
            this.audiovisual_modal.isrcAud =
              "" +
              this.audiovisual_modal.codigPais.toUpperCase() +
              this.audiovisual_modal.codigRegistro.toUpperCase() +
              this.audiovisual_modal.anhoRegistro +
              this.audiovisual_modal.identificador;
          }
        }
      }
      if (this.is_isrc()) {
        form_data.append("isrcAud", this.audiovisual_modal.isrcAud);
      } else {
        if (this.audiovisual_modal.codigAud === undefined) {
          this.codigo = "AUDV-" + this.codigo;
          form_data.append("codigAud", this.codigo);
        } else {
          this.audiovisual_modal.codigAud =
            "AUDV-" + this.audiovisual_modal.codigAud;
          form_data.append("codigAud", this.audiovisual_modal.codigAud);
        }
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
      if (this.audiovisual_modal.productos_audvs) {
        if (this.audiovisual_modal.productos_audvs.length !== 0) {
          form_data.append(
            "product_id",
            this.audiovisual_modal.productos_audvs
          );
        }
      } else
        form_data.append("product_id", this.audiovisual_modal.productos_audvs);
      if (this.audiovisual.autores_audvs) {
        form_data.append("autores_id", this.audiovisual_modal.autores_audvs);
        this.relation = "autores";
        form_data.append("type_relation", this.relation);
      } else if (this.audiovisual.interpretes_audvs) {
        form_data.append(
          "interpretes_id",
          this.audiovisual_modal.interpretes_audvs
        );
        this.relation = "interpretes";
        form_data.append("type_relation", this.relation);
      } else if (this.audiovisual.realizadores_audvs) {
        form_data.append(
          "realizadores_id",
          this.audiovisual_modal.realizadores_audvs
        );
        this.relation = "realizadores";
        form_data.append("type_relation", this.relation);
      } else if (this.audiovisual.entrevistados_audvs) {
        form_data.append(
          "entrevistados_id",
          this.audiovisual_modal.entrevistados_audvs
        );
        this.relation = "entrevistados";
        form_data.append("type_relation", this.relation);
      }
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
      } else if (this.audiovisual.autores_audvs) {
        this.tab_visibility = false;
        this.active_tab = "2";
        this.tabs_list.push("tab_1");
      } else if (this.audiovisual.interpretes_audvs) {
        this.tab_visibility = false;
        this.active_tab = "2";
        this.tabs_list.push("tab_1");
      } else if (this.audiovisual.realizadores_audvs) {
        this.tab_visibility = false;
        this.active_tab = "2";
        this.tabs_list.push("tab_1");
      } else if (this.audiovisual.entrevistados_audvs) {
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
        if (this.audiovisual.productos) {
          this.audiovisual.productos.forEach((element) => {
            this.audiovisual.productos_audvs.push(element.id);
          });
        }
        if (this.audiovisual.fenomRefAud === null) {
          this.audiovisual.fenomRefAud = "";
        }
        if (this.audiovisual.dueñoDerchAud === null) {
          this.audiovisual.dueñoDerchAud = "";
        }
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
            ","
          );
          this.audiovisual.etiquetasAud.pop();
        } else this.audiovisual.etiquetasAud = [];
        if (this.audiovisual_modal.etiquetasAud !== null) {
          this.audiovisual_modal.etiquetasAud = this.audiovisual_modal.etiquetasAud.split(
            ","
          );
          this.audiovisual_modal.etiquetasAud.pop();
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
              uid: this.audiovisual_modal.id,
              name: "Logo ver vertical_Ltr Negras.png",
              url: "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png",
            });
        }
        this.genero_pivot = this.audiovisual_modal.generoAud;
        this.$store.state["realizadores"] = this.audiovisual_modal.realizadores;
        this.audiovisual.realizadores.forEach((element) => {
          this.compare_realizadores.push(element);
        });
        this.$store.state[
          "entrevistados"
        ] = this.audiovisual_modal.entrevistados;
        this.audiovisual.entrevistados.forEach((element) => {
          this.compare_entrevistados.push(element);
        });
        this.audiovisual_modal.realizadores = undefined;
        this.audiovisual_modal.entrevistados = undefined;
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
        if (this.audiovisual.productos) {
          this.audiovisual.productos.forEach((element) => {
            this.audiovisual.productos_audvs.push(element.id);
          });
        }
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
              uid: this.audiovisual_modal.id,
              name: "Logo ver vertical_Ltr Negras.png",
              url: "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png",
            });
        }
        this.$store.state["realizadores"] = this.audiovisual_modal.realizadores;
        this.$store.state[
          "entrevistados"
        ] = this.audiovisual_modal.entrevistados;
      } else {
        const date = new Date();
        this.audiovisual.añoFinAud = date.getFullYear();
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
        .post("/realizadores/listar")
        .then((response) => {
          let prod = response.data;
          prod.forEach((element) => {
            if (!element.deleted_at) {
              this.$store.state["all_realizadores"].push(element);
              this.$store.state["all_realizadores_statics"].push(element);
            }
          });
          if (this.action_modal === "editar") {
            this.load_all_free_realizadores();
          }
        })
        .catch((error) => {
          console.log(error);
        });
      axios
        .post("/entrevistados/listar")
        .then((response) => {
          let prod = response.data;
          prod.forEach((element) => {
            if (!element.deleted_at) {
              if (this.action_modal === "crear") {
                this.$store.state["all_entrevistados"].push(element);
              }
              this.$store.state["all_entrevistados_statics"].push(element);
            }
          });
          if (this.action_modal === "editar") {
            this.load_all_free_entrevistados();
          }
        })
        .catch((error) => {
          console.log(error);
        });
      axios
        .post("/autores/listar")
        .then((response) => {
          let prod = response.data;
          prod.forEach((element) => {
            if (!element.deleted_at) {
              if (this.action_modal === "crear") {
                this.$store.state["all_autores"].push(element);
              }
              this.$store.state["all_autores_statics"].push(element);
            }
          });
        })
        .catch((error) => {
          console.log(error);
        });
      axios
        .post("/interpretes/listar")
        .then((response) => {
          let prod = response.data;
          prod.forEach((element) => {
            if (!element.deleted_at) {
              if (this.action_modal === "crear") {
                this.$store.state["all_interpretes"].push(element);
              }
              this.$store.state["all_interpretes_statics"].push(element);
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
          this.roles_interp = this.list_nomenclators[7][0];
          this.loading_nomenclators = false;
        })
        .catch((error) => {
          this.$toast.error("Ha ocurrido un error", "¡Error!", {
            timeout: 2000,
          });
        });
    },

    add_realizador() {
      if (this.audiovisual_modal.realizadores !== undefined) {
        this.$store.state["realizadores"].push(
          this.get_realizador(this.audiovisual_modal.realizadores).realizador
        );
        this.$store.state["all_realizadores"].splice(
          this.get_realizador(this.audiovisual_modal.realizadores).index,
          1
        );
        this.audiovisual_modal.realizadores = undefined;
      }
    },

    remove_realizador(item) {
      let index = this.$store.getters.getRealizadoresFormGetters.indexOf(item);
      this.$store.state["realizadores"].splice(index, 1);
      this.$store.state["all_realizadores"].push(item);
    },

    add_entrevistado() {
      if (this.audiovisual_modal.entrevistados !== undefined) {
        this.$store.state["entrevistados"].push(
          this.get_entrevistado(this.audiovisual_modal.entrevistados)
            .entrevistado
        );
        this.$store.state["all_entrevistados"].splice(
          this.get_entrevistado(this.audiovisual_modal.entrevistados).index,
          1
        );
        this.audiovisual_modal.entrevistados = undefined;
      }
    },

    remove_entrevistado(item) {
      let index = this.$store.getters.getEntrevistadosFormGetters.indexOf(item);
      this.$store.state["entrevistados"].splice(index, 1);
      this.$store.state["all_entrevistados"].push(item);
    },

    add_autor() {
      if (this.audiovisual_modal.autores !== undefined) {
        this.$store.state["autores"].push(
          this.get_autor(this.audiovisual_modal.autores).autor
        );
        this.$store.state["all_autores"].splice(
          this.get_autor(this.audiovisual_modal.autores).index,
          1
        );
        this.audiovisual_modal.autores = undefined;
      }
    },

    remove_autor(item) {
      let index = this.$store.getters.getAutoresFormGetters.indexOf(item);
      this.$store.state["autores"].splice(index, 1);
      this.$store.state["all_autores"].push(item);
    },

    add_interprete() {
      if (this.audiovisual_modal.interpretes !== undefined) {
        this.$store.state["interpretes"].push(
          this.get_interprete(this.audiovisual_modal.interpretes).interprete
        );
        this.$store.state["all_interpretes"].splice(
          this.get_interprete(this.audiovisual_modal.interpretes).index,
          1
        );
        this.audiovisual_modal.interpretes = undefined;
      }
    },

    remove_interprete(item) {
      let index = this.$store.getters.getInterpretesFormGetters.indexOf(item);
      this.$store.state["interpretes"].splice(index, 1);
      this.$store.state["all_interpretes"].push(item);
    },

    new_realizador() {
      this.visible_management_realizador = true;
    },

    get_realizador(id) {
      for (
        let index = 0;
        index < this.$store.getters.getAllRealizadoresFormGetters.length;
        index++
      ) {
        if (
          this.$store.getters.getAllRealizadoresFormGetters[index].id === id
        ) {
          return {
            realizador: this.$store.getters.getAllRealizadoresFormGetters[
              index
            ],
            index: index,
          };
        }
      }
      return -1;
    },

    new_entrevistado() {
      this.visible_management_entrevistado = true;
    },

    get_entrevistado(id) {
      for (
        let index = 0;
        index < this.$store.getters.getAllEntrevistadosFormGetters.length;
        index++
      ) {
        if (
          this.$store.getters.getAllEntrevistadosFormGetters[index].id === id
        ) {
          return {
            entrevistado: this.$store.getters.getAllEntrevistadosFormGetters[
              index
            ],
            index: index,
          };
        }
      }
      return -1;
    },

    new_autor() {
      this.visible_management_autor = true;
    },

    get_autor(id) {
      for (
        let index = 0;
        index < this.$store.getters.getAllAutoresFormGetters.length;
        index++
      ) {
        if (this.$store.getters.getAllAutoresFormGetters[index].id === id) {
          return {
            autor: this.$store.getters.getAllAutoresFormGetters[index],
            index: index,
          };
        }
      }
      return -1;
    },

    new_interprete() {
      this.visible_management_interprete = true;
    },

    get_interprete(id) {
      for (
        let index = 0;
        index < this.$store.getters.getAllInterpretesFormGetters.length;
        index++
      ) {
        if (this.$store.getters.getAllInterpretesFormGetters[index].id === id) {
          return {
            interprete: this.$store.getters.getAllInterpretesFormGetters[index],
            index: index,
          };
        }
      }
      return -1;
    },

    getRealizadoresID() {
      let answer = [];
      let all_realizadores = this.$store.getters.getRealizadoresFormGetters;
      for (let index = 0; index < all_realizadores.length; index++) {
        answer.push(all_realizadores[index].id);
      }
      return answer;
    },

    getEntrevistadosID() {
      let answer = [];
      let all_entrevistados = this.$store.getters.getEntrevistadosFormGetters;
      for (let index = 0; index < all_entrevistados.length; index++) {
        answer.push(all_entrevistados[index].id);
      }
      return answer;
    },

    getAutoresID() {
      let answer = [];
      let all_autores = this.$store.getters.getAutoresFormGetters;
      for (let index = 0; index < all_autores.length; index++) {
        answer.push(all_autores[index].id);
      }
      return answer;
    },

    getInterpretesID() {
      let answer = [];
      let all_interpretes = this.$store.getters.getInterpretesFormGetters;
      let roles = [];
      for (let index = 0; index < all_interpretes.length; index++) {
        if (all_interpretes[index].role !== undefined) {
          roles = all_interpretes[index].role;
        }
        answer.push([
          all_interpretes[index].id,
          this.create_roles_string(roles),
        ]);
        roles = [];
      }
      return answer;
    },

    create_roles_string(array) {
      let answer = "";
      for (let index = 0; index < array.length; index++) {
        if (index === 0) {
          answer += array[index];
        } else {
          answer += "," + array[index];
        }
      }
      return answer;
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
        if (this.audiovisual_modal.generoAud === this.genero_pivot) {
          return true;
        }
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

    load_all_free_realizadores() {
      let all_realizadores = this.clone_arr(
        this.$store.getters.getAllRealizadoresStaticsFormGetters
      );
      let temp = 0;
      for (
        let index = 0;
        index < this.audiovisual.realizadores.length;
        index++
      ) {
        temp = this.compare_arr_elements(
          all_realizadores,
          this.audiovisual.realizadores[index].id
        );
        all_realizadores.splice(temp, 1);
      }
      this.$store.state["all_realizadores"] = all_realizadores;
    },

    load_all_free_entrevistados() {
      let all_entrevistados = this.clone_arr(
        this.$store.getters.getAllEntrevistadosStaticsFormGetters
      );
      let temp = 0;
      for (
        let index = 0;
        index < this.audiovisual.entrevistados.length;
        index++
      ) {
        temp = this.compare_arr_elements(
          all_entrevistados,
          this.audiovisual.entrevistados[index].id
        );
        all_entrevistados.splice(temp, 1);
      }
      this.$store.state["all_entrevistados"] = all_entrevistados;
    },

    clone_arr(arr) {
      let answer = [];
      for (let index = 0; index < arr.length; index++) {
        answer.push(arr[index]);
      }
      return answer;
    },

    compare_arr_elements(array, id) {
      for (let index = 0; index < array.length; index++) {
        if (array[index].id === id) {
          return index;
        }
      }
      return -1;
    },

    build_pretty_isrc(isrc) {
      return (
        isrc.substr(0, 2) +
        "-" +
        isrc.substr(2, 3) +
        "-" +
        isrc.substr(5, 2) +
        "-" +
        isrc.substr(7)
      );
    },
  },
  components: {
    modal_management_realizadores,
    modal_management_entrevistados,
    modal_management_autores,
    modal_management_interpretes,
    tabla_autores,
    tabla_interpretes,
    tabla_productos: () => import("../Producto/Tabla_Productos"),
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
  height: 120px !important;
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
#modal_gestionar_audiovisuales .ant-col-sm-offset-4 {
  margin-left: 0px !important;
}
#modal_gestionar_audiovisuales .dynamic-delete-button {
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

#modal_gestionar_audiovisuales .ant-col-sm-20 {
  width: 100%;
}
</style>

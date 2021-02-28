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
          v-if="
            action_modal !== 'detalles' &&
            action_modal !== 'crear_track_tabla_component' &&
            !track.tabla
          "
          :disabled="tab_1"
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
                    <a-row
                      v-if="
                        action_modal === 'crear' ||
                        action_modal === 'crear_track' ||
                        action_modal === 'crear_track_tabla_component'
                      "
                      style="margin-top: 31px"
                    >
                      <a-col span="5" style="margin-top: 8px">
                        <label id="isrc">Código ISRC:</label>
                      </a-col>
                      <a-col span="19" style="float: right">
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
                                :help="show_used_error"
                                class="isrc"
                              >
                                <a-input
                                  default-value="CU"
                                  :disabled="action_modal === 'editar'"
                                  v-model="track_modal.codigPais"
                                  :class="isrc_validation"
                                />
                              </a-form-model-item>
                            </a-tooltip>
                          </a-col>
                          <a-col
                            span="1"
                            style="margin-top: 8px; text-align: center"
                            >-</a-col
                          >
                          <a-col span="5">
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
                                  placeholder="XXX"
                                  :disabled="action_modal === 'editar'"
                                  v-model="track_modal.codigRegistro"
                                  :class="isrc_validation"
                                />
                              </a-form-model-item>
                            </a-tooltip>
                          </a-col>
                          <a-col
                            span="1"
                            style="margin-top: 8px; text-align: center"
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
                                class="isrc"
                              >
                                <a-input
                                  placeholder="00"
                                  :disabled="action_modal === 'editar'"
                                  v-model="track_modal.anhoRegistro"
                                  :class="isrc_validation"
                                />
                              </a-form-model-item>
                            </a-tooltip>
                          </a-col>
                          <a-col
                            span="1"
                            style="margin-top: 8px; text-align: center"
                            >-</a-col
                          >
                          <a-col span="8" style="float: right">
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
                                  :default-value="codigo"
                                  :disabled="action_modal === 'editar'"
                                  v-model="track_modal.identificador"
                                  :class="isrc_validation"
                                />
                              </a-form-model-item>
                            </a-tooltip>
                          </a-col>
                        </a-row>
                      </a-col>
                    </a-row>
                    <a-form-model-item
                      v-if="action_modal === 'editar'"
                      label="ISRC"
                    >
                      <a-input
                        :disabled="action_modal === 'editar'"
                        v-model="pretty_isrc"
                      />
                    </a-form-model-item>
                    <a-form-model-item
                      label="ISRC"
                      v-if="action_modal === 'detalles'"
                    >
                      <a-mentions readonly :placeholder="pretty_isrc">
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
                        placeholder="hh:mm:ss"
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
                        ¿El Track tiene una pista de muestra?
                      </a-checkbox>
                    </a-form-model-item>
                    <a-form-model-item style="margin-top: 20px" v-else>
                      <i
                        class="fa fa-check-square-o hidden-xs"
                        v-if="track.muestraTrk === 1"
                      />
                      <i class="fa fa-square-o" v-else />
                      ¿El Track tiene una pista de muestra?
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
                      label="Estados de ánimo del Track"
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
                      label="Estados de ánimo del Track"
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
          <a-row
            style="margin-top: 20px"
            v-if="
              action_modal === 'crear' ||
              action_modal === 'crear_track' ||
              action_modal === 'crear_track_tabla_component'
            "
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
                    v-if="$store.getters.getAutoresFormGetters.length === 0"
                  ></a-mentions>
                  <transition-group name="list">
                    <a-form-model-item
                      v-for="(autor, index) in $store.getters
                        .getAutoresFormGetters"
                      :key="autor.id"
                      v-bind="index === 0 ? formItemLayout : {}"
                      class="list-item"
                    >
                      <a-row>
                        <a-col span="22">
                          <a-mentions
                            style="margin-top: 3px"
                            readonly
                            :placeholder="
                              autor.nombresAutr + ' ' + autor.apellidosAutr
                            "
                          ></a-mentions>
                        </a-col>
                        <a-col span="2" style="float: right; margin-top: 2px">
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
                        <h5>Selector de Autores</h5>
                      </div>
                      <a-form-model
                        ref="formularioAgregarAutor"
                        :layout="'horizontal'"
                        :model="track_modal"
                      >
                        <a-form-model-item>
                          <a-select
                            placeholder="Nombre completo"
                            option-filter-prop="children"
                            :filter-option="filter_option"
                            show-search
                            v-model="track_modal.autores"
                            :disabled="disabled"
                          >
                            <a-select-option
                              v-for="autor in $store.getters
                                .getAllAutoresFormGetters"
                              :key="autor.id"
                              :value="autor.id"
                            >
                              {{
                                autor.nombresAutr + " " + autor.apellidosAutr
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
                    <h4>Interpretes</h4>
                  </div>
                </a-col>
              </a-row>
              <a-row v-if="action_modal !== 'detalles'">
                <a-col span="24">
                  <a-mentions
                    readonly
                    v-if="$store.getters.getInterpretesFormGetters.length === 0"
                  ></a-mentions>
                  <transition-group name="list">
                    <a-form-model-item
                      v-for="(interprete, index) in $store.getters
                        .getInterpretesFormGetters"
                      :key="interprete.id"
                      v-bind="index === 0 ? formItemLayout : {}"
                      class="list-item"
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
                            style="width: 100%"
                            :getPopupContainer="(trigger) => trigger.parentNode"
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
                        <a-col span="2" style="float: right; margin-top: 40px">
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
                        <h5>Selector de Interpretes</h5>
                      </div>
                      <a-form-model
                        ref="formularioAgregarInterprete"
                        :layout="'horizontal'"
                        :model="track_modal"
                      >
                        <a-form-model-item has-feedback>
                          <a-select
                            placeholder="Nombre completo"
                            option-filter-prop="children"
                            :filter-option="filter_option"
                            show-search
                            v-model="track_modal.interpretes"
                            :disabled="disabled"
                          >
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
          <a-row>
            <a-button
              v-if="action_modal === 'editar' || action_modal === 'detalles'"
              :disabled="disabled"
              style="float: right"
              type="default"
              @click="siguiente('tab_2', '3')"
            >
              Siguiente
              <a-icon type="right" />
            </a-button>
            <a-button
              v-if="action_modal === 'crear' || action_modal === 'editar'"
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
        <a-tab-pane
          key="3"
          :disabled="tab_3"
          v-if="
            action_modal !== 'crear' &&
            action_modal !== 'crear_track_tabla_component' &&
            action_modal !== 'crear_track'
          "
        >
          <span slot="tab"> Autores/Intérptetes/Temas </span>
          <a-row>
            <a-col span="12">
              <div class="section-title">
                <h4>Autores/Intérptetes/Temas</h4>
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
                :entity="track_modal"
                entity_relation="tracks"
                :vista_editar="vista_editar"
                @close_modal="show = $event"
              />
              <tabla_interpretes
                v-else-if="current === 1"
                :detalles_prop="detalles"
                @reload="reload_parent"
                :entity="track_modal"
                entity_relation="tracks"
                :vista_editar="vista_editar"
                @close_modal="show = $event"
              />
              <tabla_temas
                v-else
                :detalles_prop="detalles"
                @reload="reload_parent"
                :entity="track_modal"
                entity_relation="tracks"
                :vista_editar="vista_editar"
                @close_modal="show = $event"
              />
            </div>
            <br />
          </div>

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
import modal_management_autores from "../Artistas/Autor/Modal_Gestionar_Autor";
import modal_management_interpretes from "../Artistas/Interprete/Modal_Gestionar_Interprete";
import tabla_autores from "../Artistas/Autor/Tabla_Autores";
import tabla_interpretes from "../Artistas/Interprete/Tabla_Interpretes";
import tabla_temas from "../Tema/Tabla_Temas";
export default {
  props: ["action", "track", "tracks_list"],
  data() {
    let validate_ISRC_unique = (rule, value, callback) => {
      this.tracks_list.forEach((element) => {
        if (element.isrcTrk === value) {
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
    return {
      action_management_autores: "crear_autor",
      action_management_interpretes: "crear_interprete",
      action_management_temas: "crear_tema",
      steps: [
        {
          title: "Autores",
        },
        {
          title: "Intérpretes",
        },
        {
          title: "Temas",
        },
      ],
      vista_editar: true,
      current: 0,
      detalles: false,
      tab_1: false,
      tab_2: true,
      tab_3: true,
      tabs_list: [],
      active_tab: "1",
      tab_visibility: true,
      action_cancel_title: "",
      fonograms: [],
      action_title: "",
      show: true,
      used: false,
      disabled: false,
      waiting: false,
      text_button: "",
      text_header_button: "",
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
      preview_image: "",
      valid_image: true,
      preview_visible: false,
      show_error: "",
      show_used_error: "",
      action_modal: this.action,
      list_nomenclators: [],
      isrc_validation: "",
      codigPais: "",
      codigRegistro: "",
      anhoRegistro: "",
      identificador: "",
      codigo: "",
      oldDuration: "",
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
            required: true,
            message: " ",
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
            required: true,
            message: " ",
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
        tituloTrk: [
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
        duracionTrk: [
          {
            required: true,
            message: "Campo requerido",
            trigger: "change",
          },
        ],
        generoTrk: [
          {
            required: true,
            message: "Campo requerido",
            trigger: "change",
          },
        ],
        gestionTrk: [
          {
            required: true,
            message: "Campo requerido",
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
      this.action_modal === "crear_track" ||
      this.action_modal === "crear_track_tabla_component"
    ) {
      this.codigo = this.generar_codigo(this.tracks_list);
    }
    if (
      this.action_modal === "detalles" ||
      this.action_modal === "crear_track"
    ) {
      this.active_tab = "2";
      this.tabs_list.push("tab_1");
      this.tab_2 = false;
    }
  },
  computed: {
    active() {
      if (this.text_button === "Editar") {
        return !this.compare_object;
      } else
        return (
          this.track_modal.tituloTrk &&
          this.track_modal.duracionTrk &&
          this.track_modal.generoTrk &&
          this.track_modal.gestionTrk
        );
    },
    /*
     *Método que compara los campos editables del producto para saber si se ha modificado
     */
    compare_object() {
      this.track_modal.muestraTrk = this.muestraTrk === true ? 1 : 0;
      this.track_modal.bonusTrk = this.bonusTrk === true ? 1 : 0;
      this.track_modal.envivoTrk = this.envivoTrk === true ? 1 : 0;
      if (this.active_tab === "3") {
        return false;
      } else {
        return (
          this.track_modal.tituloTrk === this.track.tituloTrk &&
          this.track_modal.duracionTrk === this.track.duracionTrk &&
          this.track_modal.paisgrabTrk === this.track.paisgrabTrk &&
          this.track_modal.muestraTrk === this.track.muestraTrk &&
          this.track_modal.generoTrk === this.track.generoTrk &&
          this.track_modal.subgeneroTrk === this.track.subgeneroTrk &&
          this.compareArrays(this.track_modal.moodTrk, this.track.moodTrk) &&
          this.compareArrays(
            this.track_modal.fonogramas_tracks,
            this.track.fonogramas_tracks
          ) &&
          this.track_modal.gestionTrk === this.track.gestionTrk &&
          this.track_modal.bonusTrk === this.track.bonusTrk &&
          this.track_modal.envivoTrk === this.track.envivoTrk
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
    moment,
    onChange(current) {
      this.current = current;
    },
    siguiente(tab, siguienteTab) {
      if (tab === "tab_1") {
        this.$refs.formularioFonograma.validate((valid) => {
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
        if (this.action_modal !== "detalles") {
          this.$refs.formularioGenerales.validate((valid) => {
            if (valid) {
              this.tab_3 = false;
              this.tab_2 = true;
              if (this.tabs_list.indexOf(tab) == -1) {
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
      }
    },
    reload_parent() {
      this.$emit("refresh");
    },
    handle_cancel(e) {
      if (e === "cancelar") {
        this.$emit("actualizar");
        if (this.tabs_list.indexOf("tab_1") !== -1) {
          this.$refs.formularioGenerales.resetFields();
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
        this.$store.state["autores"] = [];
        this.$store.state["created_autores"] = [];
        this.$store.state["all_autores"] = [];
        this.$store.state["interpretes"] = [];
        this.$store.state["created_interpretes"] = [];
        this.$store.state["all_interpretes"] = [];
        this.tabs_list = [];
        this.active_tab = "1";
        this.tab_visibility = true;
        this.show = false;
        this.$emit("close_modal", this.show);
        if (this.action_modal !== "detalles") {
          this.$toast.success(this.action_close, "¡Éxito!", {
            timeout: 2000,
            color: "orange",
          });
        }
      } else {
        if (this.tabs_list.indexOf("tab_1") !== -1) {
          this.$refs.formularioGenerales.resetFields();
        }
        this.tabs_list = [];
        this.active_tab = "1";
        this.tab_visibility = true;
        this.show = false;
        this.$emit("close_modal", this.show);
      }
    },
    validate() {
      if (this.track_modal.identificador === undefined) {
        this.track_modal.identificador = this.codigo;
      }
      if (this.track_modal.codigPais === undefined) {
        this.track_modal.codigPais = "CU";
      }
      if (!this.used) {
        if (this.active_tab !== "1") {
          if (this.$refs.formularioGenerales !== undefined) {
            this.$refs.formularioGenerales.validate((valid) => {
              if (valid) {
                return this.confirm();
              } else
                this.$message.warning(
                  "Hay problemas en la pestaña Generales, por favor antes de continuar revísela!",
                  4
                );
            });
          } else return this.confirm();
        } else return this.confirm();
      }
    },
    atras(tabAnterior) {
      if (this.active_tab === "2") {
        this.$refs.formularioGenerales.validate((valid) => {
          if (valid) {
            this.tab_2 = true;
            this.tab_1 = false;
            this.active_tab = tabAnterior;
          } else {
            this.$message.warning(
              "Hay problemas en la pestaña Generales, por favor antes de continuar revísela!",
              4
            );
          }
        });
      } else if (this.active_tab === "3") {
        this.tab_3 = true;
        this.tab_2 = false;
        this.active_tab = tabAnterior;
      }
    },
    confirm() {
      this.spinning = true;
      this.waiting = true;
      let form_data = this.prepare_create();
      if (this.action_modal === "editar") {
        this.text_button = "Editando...";
        this.edit_duration_fong(
          this.track_modal.fonogramas,
          this.oldDuration,
          this.track_modal.duracionTrk
        );
        axios
          .post(`/tracks/editar`, form_data, {
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
              "Se ha modificado el Track correctamente",
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
        if (this.validate_isrc(this.track_modal.isrcTrk, this.tracks_list)) {
          this.text_button = "Creando...";
          axios
            .post("/tracks", form_data, {
              headers: {
                "Content-Type": "multipart/form-data",
              },
            })
            .then((res) => {
              axios
                .post("/tracks/autores", {
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
                .post("/tracks/interpretes", {
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
              if (
                this.action_modal === "crear_track" ||
                this.action_modal === "crear_track_tabla_component"
              ) {
                let tracks = [];
                axios
                  .post("/tracks/listar")
                  .then((response) => {
                    let prod = response.data;
                    prod.forEach((element) => {
                      if (!element.deleted_at) {
                        tracks.push(element);
                      }
                    });
                    this.$store.state["tracks"].push(tracks[tracks.length - 1]);
                    this.$store.state["created_tracks"].push(
                      tracks[tracks.length - 1]
                    );
                    this.$store.state["all_tracks_statics"].push(
                      tracks[tracks.length - 1]
                    );
                    this.$store.state.duration = moment(
                      this.$store.getters.getDurationFormGetters,
                      "HH:mm:ss"
                    );
                    this.$store.state.duration
                      .add(
                        moment.duration(
                          this.$store.getters.getTracksFormGetters[
                            this.$store.getters.getTracksFormGetters.length - 1
                          ].duracionTrk
                        )
                      )
                      .format("HH:mm:ss");
                    this.$store.state.duration = moment(
                      this.$store.getters.getDurationFormGetters,
                      "HH:mm:ss"
                    ).format("HH:mm:ss");
                  })
                  .catch((error) => {
                    console.log(error);
                  });
              } else if (this.action_modal === "crear_track_tabla_component") {
                this.$store.state.duration = moment(
                  this.$store.getters.getDurationFormGetters,
                  "HH:mm:ss"
                );
                this.$store.state.duration
                  .add(moment.duration(this.track_modal.duracionTrk))
                  .format("HH:mm:ss");
                this.$store.state.duration = moment(
                  this.$store.getters.getDurationFormGetters,
                  "HH:mm:ss"
                ).format("HH:mm:ss");
              }
              this.text_button = "Crear";
              this.spinning = false;
              this.waiting = false;
              this.handle_cancel();
              this.$emit("actualizar");
              this.$toast.success(
                "Se ha creado el Track correctamente",
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
      this.track_modal.muestraTrk = this.muestraTrk === false ? 0 : 1;
      this.track_modal.bonusTrk = this.bonusTrk === false ? 0 : 1;
      this.track_modal.envivoTrk = this.envivoTrk === false ? 0 : 1;
      if (this.track_modal.subgeneroTrk === undefined) {
        this.track_modal.subgeneroTrk = "";
      } else if (this.track_modal.subgeneroTrk === null) {
        this.track_modal.subgeneroTrk = "";
      }
      if (this.track_modal.moodTrk === undefined) {
        this.track_modal.moodTrk = "";
      } else if (this.track_modal.moodTrk === null) {
        this.track_modal.moodTrk = "";
      }
      if (this.track_modal.paisgrabTrk === undefined) {
        this.track_modal.paisgrabTrk = "";
      } else if (this.track_modal.paisgrabTrk === null) {
        this.track_modal.paisgrabTrk = "";
      }
      let moods = "";
      if (this.track_modal.moodTrk !== "") {
        this.track_modal.moodTrk.forEach((item) => {
          moods += item + " ";
        });
        this.track_modal.moodTrk = moods;
      }
      let form_data = new FormData();
      if (this.action_modal === "editar" || this.action_modal === "detalles") {
        form_data.append("id", this.track_modal.id);
      }
      if (this.track_modal.identificador === undefined) {
        this.track_modal.identificador = this.codigo;
      }
      if (this.track_modal.codigPais === undefined) {
        this.track_modal.codigPais = "CU";
      }
      if (
        this.action_modal === "crear" ||
        this.action_modal === "crear_track_tabla_component" ||
        this.action_modal === "crear_track"
      ) {
        this.track_modal.isrcTrk =
          "" +
          this.track_modal.codigPais.toUpperCase() +
          this.track_modal.codigRegistro.toUpperCase() +
          this.track_modal.anhoRegistro +
          this.track_modal.identificador;
      }
      form_data.append("isrcTrk", this.track_modal.isrcTrk);
      form_data.append("tituloTrk", this.track_modal.tituloTrk);
      form_data.append("duracionTrk", this.track_modal.duracionTrk);
      form_data.append("muestraTrk", this.track_modal.muestraTrk);
      form_data.append("envivoTrk", this.track_modal.envivoTrk);
      form_data.append("generoTrk", this.track_modal.generoTrk);
      form_data.append("moodTrk", moods);
      form_data.append("subgeneroTrk", this.track_modal.subgeneroTrk);
      form_data.append("bonusTrk", this.track_modal.bonusTrk);
      form_data.append("gestionTrk", this.track_modal.gestionTrk);
      form_data.append("paisgrabTrk", this.track_modal.paisgrabTrk);
      form_data.append("fonograma_id", this.track_modal.fonogramas_tracks);
      return form_data;
    },
    set_action() {
      if (this.track && (this.track.fonogramas_tracks || this.track.tabla)) {
        this.tab_visibility = false;
        this.active_tab = "2";
      }
      if (this.action === "editar") {
        if (this.track.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.text_header_button = "Editar";
        this.text_button = "Editar";
        this.action_cancel_title = "¿Desea cancelar la edición del Track?";
        this.action_title = "¿Desea guardar los cambios en el Track?";
        this.action_close = "La edición del Track se canceló correctamente";
        this.track.fonogramas_tracks = [];
        this.track.fonogramas.forEach((element) => {
          this.track.fonogramas_tracks.push(element.id);
        });
        this.track_modal = { ...this.track };
        this.oldDuration = this.track_modal.duracionTrk;
        this.muestraTrk = this.track_modal.muestraTrk === 0 ? false : true;
        this.envivoTrk = this.track_modal.envivoTrk === 0 ? false : true;
        this.bonusTrk = this.track_modal.bonusTrk === 0 ? false : true;
        if (this.track.moodTrk !== null) {
          this.track.moodTrk = this.track.moodTrk.split(" ");
          this.track.moodTrk.pop();
        } else this.track.moodTrk = [];
        if (this.track_modal.moodTrk !== null) {
          this.track_modal.moodTrk = this.track_modal.moodTrk.split(" ");
          this.track_modal.moodTrk.pop();
        } else this.track_modal.moodTrk = [];
        this.pretty_isrc = this.build_pretty_isrc(this.track_modal.isrcTrk);
      } else if (this.action_modal === "detalles") {
        this.detalles = true;
        if (this.track.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.text_header_button = "Detalles";
        this.text_button = "Detalles";
        this.track.fonogramas_tracks = [];
        this.track.fonogramas_tracks.forEach((element) => {
          this.track.fonogramas_tracks.push(element.id);
        });

        this.track_modal = { ...this.track };
        this.muestraTrk = this.track_modal.muestraTrk === 0 ? false : true;
        this.envivoTrk = this.track_modal.envivoTrk === 0 ? false : true;
        this.bonusTrk = this.track_modal.bonusTrk === 0 ? false : true;
        if (this.track_modal.moodTrk !== null) {
          this.track_modal.moodTrk = this.track_modal.moodTrk.split(" ");
        } else delete this.track_modal.moodTrk;
        this.pretty_isrc = this.build_pretty_isrc(this.track_modal.isrcTrk);
      } else {
        this.track_modal = { ...this.track };
        this.text_button = "Crear";
        this.text_header_button = "Crear";
        this.action_cancel_title = "¿Desea cancelar la creación del Track?";
        this.action_title = "¿Desea crear el Track?";
        this.action_close = "La creación del Track se canceló correctamente";
      }
    },
    load_nomenclators() {
      //* Carga de productos
      axios
        .post("/fonogramas/listar")
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
        .post("/autores/listar")
        .then((response) => {
          let prod = response.data;
          prod.forEach((element) => {
            if (!element.deleted_at) {
              if (
                this.action_modal === "crear" ||
                this.action_modal === "crear_track" ||
                this.action_modal === "crear_track_tabla_component"
              ) {
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
              if (
                this.action_modal === "crear" ||
                this.action_modal === "crear_track" ||
                this.action_modal === "crear_track_tabla_component"
              ) {
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
        .post("/tracks/nomencladores")
        .then((response) => {
          this.list_nomenclators = response.data;
          this.moods = this.list_nomenclators[2][0];
          this.generos = this.list_nomenclators[0][0];
          this.subgeneros = this.list_nomenclators[1][0];
          this.paises = this.list_nomenclators[4][0];
          this.gestiones = this.list_nomenclators[3][0];
          this.roles_interp = this.list_nomenclators[5][0];
        })
        .catch((error) => {
          this.$toast.error("Ha ocurrido un error", "¡Error!", {
            timeout: 2000,
          });
        });
    },

    add_autor() {
      if (this.track_modal.autores !== undefined) {
        this.$store.state["autores"].push(
          this.get_autor(this.track_modal.autores).autor
        );
        this.$store.state["all_autores"].splice(
          this.get_autor(this.track_modal.autores).index,
          1
        );
        this.track_modal.autores = undefined;
      }
    },

    remove_autor(item) {
      let index = this.$store.getters.getAutoresFormGetters.indexOf(item);
      this.$store.state["autores"].splice(index, 1);
      this.$store.state["all_autores"].push(item);
    },

    add_interprete() {
      if (this.track_modal.interpretes !== undefined) {
        this.$store.state["interpretes"].push(
          this.get_interprete(this.track_modal.interpretes).interprete
        );
        this.$store.state["all_interpretes"].splice(
          this.get_interprete(this.track_modal.interpretes).index,
          1
        );
        this.track_modal.interpretes = undefined;
      }
    },

    remove_interprete(item) {
      let index = this.$store.getters.getInterpretesFormGetters.indexOf(item);
      this.$store.state["interpretes"].splice(index, 1);
      this.$store.state["all_interpretes"].push(item);
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
    crear_arr_codig(arr) {
      let answer = [];
      for (let i = 0; i < arr.length; i++) {
        answer.push(parseInt(arr[i].isrcTrk.substr(7)));
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
          return "0000" + number;
        case 2:
          return "000" + number;
        case 3:
          return "00" + number;
        case 4:
          return "0" + number;
        case 5:
          return number.toString();
        default:
          break;
      }
    },
    //Fin de metodos para generar el codigo

    validate_isrc(isrc, list) {
      for (let index = 0; index < list.length; index++) {
        if (list[index].isrcTrk === isrc) {
          return false;
        }
      }
      return true;
    },

    edit_duration_fong(fonogramas, oldDuration, currentDuration) {
      if (oldDuration !== currentDuration) {
        let durationFong;
        let form_data = new FormData();
        for (let i = 0; i < fonogramas.length; i++) {
          durationFong = moment(fonogramas[i].duracionFong, "HH:mm:ss");
          durationFong
            .subtract(moment.duration(oldDuration))
            .format("HH:mm:ss");
          durationFong = moment(durationFong, "HH:mm:ss");
          durationFong.add(moment.duration(currentDuration)).format("HH:mm:ss");
          durationFong = moment(durationFong, "HH:mm:ss").format("HH:mm:ss");
          form_data.append("id", fonogramas[i].id);
          form_data.append("duracionFong", durationFong);
          axios
            .post(`/fonogramas/editarDuracion`, form_data, {
              headers: {
                "Content-Type": "multipart/form-data",
              },
            })
            .then((response) => {})
            .catch((error) => {
              this.$toast.error("Ha ocurrido un error", "¡Error!", {
                timeout: 2000,
              });
            });
          form_data = new FormData();
        }
      }
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
    modal_management_interpretes,
    modal_management_autores,
    tabla_autores,
    tabla_temas,
    tabla_interpretes,
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
#modal_gestionar_tracks #isrc {
  color: rgba(0, 0, 0, 0.85);
  font-size: 14px;
  font-weight: 400;
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
#modal_gestionar_tracks .duplicated-isrc-error {
  border-color: rgb(243, 107, 100) !important;
}
#modal_gestionar_tracks .ant-col-sm-offset-4 {
  margin-left: 0px !important;
}
#modal_gestionar_tracks .dynamic-delete-button {
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
.ant-select-selection--multiple {
  min-height: 35px !important;
}
#modal_gestionar_tracks .ant-col-sm-20 {
  width: 100%;
}
</style>

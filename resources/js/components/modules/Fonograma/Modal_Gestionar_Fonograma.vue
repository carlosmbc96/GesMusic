<template>
  <div>
    <a-modal
      :closable="false"
      width="80.4%"
      :visible="show"
      id="modal_gestionar_fonogramas"
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
      <!-- Tabs -->
      <a-tabs :activeKey="active_tab">
        <div slot="tabBarExtraContent">{{ text_header_button }} Fonograma</div>
        <!-- Tab 1 -->
        <a-tab-pane
          key="1"
          v-if="tab_visibility && action_modal !== 'detalles'"
          :disabled="tab_1"
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
                  <a-form-model-item label="Código" has-feedback>
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
                          v-if="action_modal === 'detalles'"
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
                    <a-col span="11" style="float: right">
                      <a-form-model-item
                        label="Código"
                        v-if="action_modal === 'detalles'"
                      >
                        <a-mentions
                          readonly
                          :placeholder="fonogram_modal.codigFong"
                        >
                        </a-mentions>
                      </a-form-model-item>
                      <a-form-model-item
                        v-if="action_modal === 'crear'"
                        :validate-status="show_error"
                        :help="show_used_error"
                        prop="codigFong"
                        has-feedback
                        label="Código"
                      >
                        <a-input
                          addon-before="FONG-"
                          :default-value="codigo"
                          :disabled="action_modal === 'editar'"
                          v-model="fonogram_modal.codigFong"
                        />
                      </a-form-model-item>
                      <a-form-model-item
                        v-if="action_modal === 'editar'"
                        label="Código"
                      >
                        <a-input
                          addon-before="Fong-"
                          :disabled="action_modal === 'editar'"
                          v-model="fonogram_modal.codigFong"
                        />
                      </a-form-model-item>
                      <a-form-model-item
                        v-if="action_modal !== 'detalles'"
                        prop="tituloFong"
                        has-feedback
                        label="Título"
                      >
                        <a-input
                          :disabled="disabled"
                          v-model="fonogram_modal.tituloFong"
                        />
                      </a-form-model-item>
                      <a-form-model-item label="Título" v-else>
                        <a-mentions
                          readonly
                          :placeholder="fonogram_modal.tituloFong"
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
                        label="Año de creación"
                        prop="añoFong"
                      >
                        <a-select
                          :getPopupContainer="(trigger) => trigger.parentNode"
                          option-filter-prop="children"
                          :filter-option="filter_option"
                          show-search
                          :disabled="disabled"
                          v-model="fonogram_modal.añoFong"
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
                      <a-form-model-item label="Año de creación" v-else>
                        <a-mentions
                          readonly
                          :placeholder="fonogram_modal.añoFong"
                        >
                        </a-mentions>
                      </a-form-model-item>
                      <a-form-model-item
                        v-if="action_modal !== 'detalles'"
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
                        label="Clasificación del Fonograma"
                        v-else
                      >
                        <a-mentions
                          readonly
                          :placeholder="fonogram_modal.clasficacionFong"
                        >
                        </a-mentions>
                      </a-form-model-item>
                    </a-col>
                    <a-col span="11" style="float: right">
                      <a-form-model-item
                        label="Duración total"
                        v-if="action_modal === 'crear'"
                      >
                        <a-mentions
                          readonly
                          :placeholder="$store.getters.getDurationFormGetters"
                        >
                        </a-mentions>
                      </a-form-model-item>
                      <a-form-model-item label="Duración total" v-else>
                        <a-mentions
                          readonly
                          :placeholder="$store.getters.getDurationFormGetters"
                        >
                        </a-mentions>
                      </a-form-model-item>
                    </a-col>
                  </a-row>
                </a-col>
                <a-col span="11" style="float: right">
                  <div class="section-title">
                    <h4>Datos del dueño de los derechos del Fonograma</h4>
                  </div>
                  <a-row style="margin-bottom: 30px">
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
                      prop="dueñoDerchFong"
                      has-feedback
                      label="Nombre completo"
                    >
                      <a-input
                        :disabled="disabled"
                        v-model="fonogram_modal.dueñoDerchFong"
                      />
                    </a-form-model-item>
                    <a-form-model-item label="Nombre completo" v-else>
                      <a-mentions
                        readonly
                        :placeholder="fonogram_modal.dueñoDerchFong"
                      >
                      </a-mentions>
                    </a-form-model-item>
                    <a-form-model-item
                      v-if="action_modal !== 'detalles'"
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
                    <a-form-model-item label="Nacionalidad" v-else>
                      <a-mentions
                        readonly
                        :placeholder="fonogram_modal.nacioDueñoDerchFong"
                      >
                      </a-mentions>
                    </a-form-model-item>
                    <div class="section-title">
                      <h4>Datos Descripciones</h4>
                    </div>
                    <div id="desciption-problem-icon">
                      <a-form-model-item
                        v-if="action_modal !== 'detalles'"
                        has-feedback
                        label="Descripción en español del Fonograma"
                        prop="descripEspFong"
                      >
                        <a-input
                          :disabled="disabled"
                          style="width: 100%; height: 150px"
                          v-model="fonogram_modal.descripEspFong"
                          type="textarea"
                        />
                      </a-form-model-item>
                      <a-form-model-item
                        label="Descripción en español del Fonograma"
                        v-else
                      >
                        <div class="description">
                          <a-mentions
                            readonly
                            :placeholder="fonogram_modal.descripEspFong"
                          >
                          </a-mentions>
                        </div>
                      </a-form-model-item>
                      <a-form-model-item
                        v-if="action_modal !== 'detalles'"
                        has-feedback
                        label="Descripción en inglés del Fonograma"
                        prop="descripIngFong"
                      >
                        <a-input
                          :disabled="disabled"
                          style="width: 100%; height: 150px"
                          v-model="fonogram_modal.descripIngFong"
                          type="textarea"
                        />
                      </a-form-model-item>
                      <a-form-model-item
                        label="Descripción en inglés del Fonograma"
                        v-else
                      >
                        <div class="description">
                          <a-mentions
                            readonly
                            :placeholder="fonogram_modal.descripIngFong"
                          >
                          </a-mentions>
                        </div>
                      </a-form-model-item>
                    </div>
                  </a-row>
                </a-col>
              </a-row>
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
          <a-row>
            <a-col span="12" v-if="action_modal !== 'crear'">
              <div class="section-title">
                <h4>Tracks del Fonograma</h4>
              </div>
            </a-col>
          </a-row>
          <tabla_tracks
            v-if="action_modal !== 'crear'"
            :detalles_prop="detalles"
            @reload="reload_parent"
            :entity="fonogram_modal"
            entity_relation="fonogramas"
            :vista_editar="vista_editar"
            @close_modal="show = $event"
          />
          <br />
          <a-row v-if="action_modal === 'crear'">
            <div id="tracks">
              <a-col span="1"></a-col>
              <a-col span="20">
                <div class="section-title">
                  <h4>Tracks</h4>
                </div>
                <a-row>
                  <a-col span="2" style="margin-top: 0px !important">
                    <h5>Orden</h5>
                  </a-col>
                  <a-col span="1"></a-col>
                  <a-col span="7">
                    <h5>Título</h5>
                    <a-mentions
                      v-if="$store.getters.getTracksFormGetters.length === 0"
                      readonly
                    >
                    </a-mentions>
                  </a-col>
                  <a-col span="1"></a-col>
                  <a-col span="5">
                    <h5>Duración</h5>
                    <a-mentions
                      v-if="$store.getters.getTracksFormGetters.length === 0"
                      readonly
                    >
                    </a-mentions>
                  </a-col>
                  <a-col span="1"></a-col>
                  <a-col span="5">
                    <h5>Género</h5>
                    <a-mentions
                      v-if="$store.getters.getTracksFormGetters.length === 0"
                      readonly
                    >
                    </a-mentions>
                  </a-col>
                  <a-col span="1"></a-col>
                </a-row>
                <div>
                  <transition-group name="list">
                    <a-form-model-item
                      v-for="(track, index) in $store.getters
                        .getTracksFormGetters"
                      :key="track.id"
                      v-bind="index === 0 ? formItemLayout : {}"
                      class="list-item"
                    >
                      <a-row align="middle">
                        <a-col span="2">
                          <div style="margin-left: 14px">
                            <a v-if="index !== 0" @click="order_up(index)"
                              ><a-icon
                                class="icon-up"
                                type="caret-up"
                                style="
                                  color: rgb(76, 196, 177, 0.5);
                                  display: flex;
                                  margin-bottom: -10px;
                                  font-size: 25px;
                                "
                            /></a>
                            <span style="margin-left: 9px">
                              {{ index + 1 }}
                            </span>
                            <a
                              @click="order_down(index)"
                              v-if="
                                index !==
                                $store.getters.getTracksFormGetters.length - 1
                              "
                              ><a-icon
                                class="icon-down"
                                type="caret-down"
                                style="
                                  color: rgb(76, 196, 177, 0.6);
                                  display: flex;
                                  margin-top: -10px;
                                  font-size: 25px;
                                "
                            /></a>
                          </div>
                        </a-col>
                        <a-col span="1"></a-col>
                        <a-col span="7">
                          <a-mentions readonly :placeholder="track.tituloTrk">
                          </a-mentions>
                        </a-col>
                        <a-col span="1"></a-col>
                        <a-col span="5">
                          <a-mentions readonly :placeholder="track.duracionTrk">
                          </a-mentions>
                        </a-col>
                        <a-col span="1"></a-col>
                        <a-col span="5">
                          <a-mentions readonly :placeholder="track.generoTrk">
                          </a-mentions>
                        </a-col>
                        <a-col span="1"></a-col>
                        <a-col span="1">
                          <a-button
                            style="margin-top: 4px"
                            class="dynamic-delete-button"
                            @click="remove_track(track)"
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

                <a-row style="margin-top: 40px">
                  <a-col span="12">
                    <div class="section-title">
                      <h4>Selector de Tracks</h4>
                    </div>
                    <a-form-model
                      ref="formularioAgregarTrack"
                      :layout="'horizontal'"
                      :model="fonogram_modal"
                    >
                      <a-form-model-item has-feedback>
                        <a-select
                          placeholder="ISRC"
                          option-filter-prop="children"
                          :filter-option="filter_option"
                          show-search
                          v-model="fonogram_modal.tracks"
                          :disabled="disabled"
                        >
                          <a-select-option
                            v-for="track in $store.getters
                              .getAllTracksFormGetters"
                            :key="track.isrcTrk"
                            :value="track.id"
                          >
                            {{ track.isrcTrk }}
                          </a-select-option>
                        </a-select>
                      </a-form-model-item>
                      <a-form-model-item>
                        <a-select
                          placeholder="Título"
                          option-filter-prop="children"
                          :filter-option="filter_option"
                          show-search
                          v-model="fonogram_modal.tracks"
                          :disabled="disabled"
                        >
                          <a-select-option
                            v-for="track in $store.getters
                              .getAllTracksFormGetters"
                            :key="track.tituloTrk"
                            :value="track.id"
                          >
                            {{ track.tituloTrk }}
                          </a-select-option>
                        </a-select>
                      </a-form-model-item>
                      <a-row>
                        <a-col span="12">
                          <a-form-model-item v-bind="formItemLayout">
                            <a-button
                              :disabled="disabled"
                              style="
                                color: white;
                                background-color: rgb(45, 171, 229) !important;
                              "
                              @click="add_track"
                            >
                              <a-icon type="plus" />
                              Agregar Track
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
                              style="
                                color: white;
                                background-color: rgb(45, 171, 229) !important;
                              "
                              @click="new_track"
                            >
                              <a-icon type="plus" />
                              Crear Track
                            </a-button>
                          </a-form-model-item>
                        </a-col>
                      </a-row>
                    </a-form-model>
                  </a-col>
                  <a-col span="10" style="float: right">
                    <div class="section-title">
                      <h4>Duración total</h4>
                    </div>
                    <a-mentions
                      readonly
                      :placeholder="$store.getters.getDurationFormGetters"
                    >
                    </a-mentions>
                  </a-col>
                </a-row>
              </a-col>
              <a-col span="1"></a-col>
            </div>
          </a-row>
          <a-col span="24">
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
          </a-col>
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
    <modal_management
      v-if="visible_management"
      :action="action_management"
      @close_modal="visible_management = $event"
      :tracks_list="$store.getters.getAllTracksStaticsFormGetters"
    />
  </div>
</template>

<script>
import tabla_tracks from "../Track/Tabla_Tracks";
import modal_management from "../Track/Modal_Gestionar_Track";
import moment from "../../../../../node_modules/moment";
export default {
  props: ["action", "fonogram", "fonograms_list"],
  data() {
    let validate_codig_unique = (rule, value, callback) => {
      if (value !== undefined) {
        this.fonograms_list.forEach((element) => {
          if (element.codigFong.substr(5) === value.replace(/ /g, "")) {
            callback(new Error("Código ya usado"));
          }
        });
      }
      callback();
    };
    let code_required = (rule, value, callback) => {
      if (value === "") {
        callback(new Error("Inserte el código"));
      } else callback();
    };
    return {
      origin: true,
      detalles: false,
      vista_editar: true,
      tab_1: false,
      tab_2: true,
      tab_3: true,
      tab_4: true,
      visible_management: false,
      action_management: "crear_track",
      tabs_list: [],
      active_tab: "1",
      tab_visibility: true,
      clasficaciones: [],
      paises: [],
      action_cancel_title: "",
      products: [],
      anhos: [],
      action_title: "",
      show: true,
      used: false,
      disabled: false,
      waiting: false,
      text_button: "",
      text_header_button: "",
      spinning: false,
      fonogram_modal: {},
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
      codigo: "",
      tracksFong: [],
      formItemLayout: {
        wrapperCol: {
          xs: { span: 24, offset: 0 },
          sm: { span: 20, offset: 4 },
        },
      },
      rules: {
        codigFong: [
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
        tituloFong: [
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
          {
            whitespace: true,
            message: "Espacio no válido",
            trigger: "change",
          },
        ],
        dueñoDerchFong: [
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
        añoFong: [
          {
            required: true,
            message: "Campo requerido",
            trigger: "change",
          },
        ],
        descripEspFong: [
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
        descripIngFong: [
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
      this.codigo = this.generar_codigo(this.fonograms_list);
      this.$store.state.duration = moment("00:00:00", "HH:mm:ss").format(
        "HH:mm:ss"
      );
    } else if (this.action_modal === "detalles") {
      this.active_tab = "2";
      this.fonogram_modal.codigFong = this.fonogram.codigFong;
    }
  },
  computed: {
    active() {
      if (this.text_button === "Editar") {
        let same_photo = false;
        if (this.file_list[0]) {
          same_photo = this.file_list[0].uid !== this.fonogram_modal.id;
        }
        return (
          (same_photo || this.file_list.length === 0 || !this.compare_object) &&
          this.valid_image
        );
      } else
        return (
          this.fonogram_modal.tituloFong &&
          this.fonogram_modal.añoFong &&
          this.valid_image
        );
    },
    /*
     *Método que compara los campos editables del producto para saber si se ha modificado
     */
    compare_object() {
      if (this.active_tab === "3") {
        return false;
      } else {
        return (
          this.fonogram_modal.tituloFong === this.fonogram.tituloFong &&
          this.compareArrays(
            this.fonogram_modal.productos_fongs,
            this.fonogram.productos_fongs
          ) &&
          this.fonogram_modal.añoFong === this.fonogram.añoFong &&
          this.fonogram_modal.clasficacionFong ===
            this.fonogram.clasficacionFong &&
          this.fonogram_modal.dueñoDerchFong === this.fonogram.dueñoDerchFong &&
          this.fonogram_modal.nacioDueñoDerchFong ===
            this.fonogram.nacioDueñoDerchFong &&
          this.fonogram_modal.descripEspFong === this.fonogram.descripEspFong &&
          this.fonogram_modal.descripIngFong === this.fonogram.descripIngFong
        );
      }
    },
  },
  methods: {
    siguiente(tab, siguienteTab) {
      if (tab === "tab_1") {
        this.$refs.formularioProducto.validate((valid) => {
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
        } else if (tab === "tab_3") {
          this.tab_4 = false;
          this.tab_3 = true;
          if (this.tabs_list.indexOf(tab) == -1) {
            this.tabs_list.push(tab);
          }
          this.active_tab = siguienteTab;
        }
      }
    },
    handle_cancel(e) {
      if (e === "cancelar") {
        this.$emit("actualizar");
        if (this.$refs.formularioGenerales !== undefined) {
          this.$refs.formularioGenerales.resetFields();
        }
        if (this.$store.getters.getCreatedTracksFormGetters.length !== 0) {
          for (
            let index = 0;
            index < this.$store.getters.getCreatedTracksFormGetters.length;
            index++
          ) {
            axios
              .delete(
                `tracks/eliminar/${this.$store.getters.getCreatedTracksFormGetters[index].id}`
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
        this.$store.state["tracks"] = [];
        this.$store.state["created_tracks"] = [];
        this.$store.state["all_tracks"] = [];
        this.$store.state.duration = 0;
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
        if (this.$refs.formularioGenerales !== undefined) {
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
      if (!this.used) {
        if (this.active_tab !== "1") {
          if (this.tabs_list.indexOf("tab_1") !== -1) {
            this.$refs.formularioGenerales.validate((valid) => {
              if (valid) {
                return this.confirm();
              } else {
                this.$message.warning(
                  "Hay problemas en la pestaña Generales, por favor antes de continuar revísela!",
                  4
                );
              }
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
        this.text_button = "Editando...";
        axios
          .post(`/fonogramas/editar`, form_data, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((response) => {
            axios
              .post("/fonogramas/tracks", {
                tracks: this.getTracksID(),
                idFong: response.data.id,
              })
              .then((res) => {
                this.$store.state["tracks"] = [];
                this.$store.state["all_tracks_statics"] = [];
                this.$store.state["all_tracks"] = [];
                this.$store.state["created_tracks"] = [];
              })
              .catch((error) => {
                console.log(error);
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
              "Se ha modificado el Fonograma correctamente",
              "¡Éxito!",
              { timeout: 2000 }
            );
          })
          .catch((error) => {
            console.log(error);
            this.text_button = "Editar";
            this.spinning = false;
            this.waiting = false;
            this.$toast.error("Ha ocurrido un error", "¡Error!", {
              timeout: 2000,
            });
          });
      } else {
        this.text_button = "Creando...";
        axios
          .post("/fonogramas", form_data, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((res) => {
            axios
              .post("/fonogramas/tracks", {
                tracks: this.getTracksID(),
                idFong: res.data.id,
              })
              .then((res) => {
                this.$store.state["tracks"] = [];
                this.$store.state["all_tracks_statics"] = [];
                this.$store.state["all_tracks"] = [];
                this.$store.state["created_tracks"] = [];
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
              "Se ha creado el Fonograma correctamente",
              "¡Éxito!",
              { timeout: 2000 }
            );
          })
          .catch((err) => {
            this.text_button = "Crear";
            this.spinning = false;
            this.waiting = false;
            this.$toast.error("Ha ocurrido un error", "¡Error!", {
              timeout: 2000,
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
    reload_parent() {
      this.$emit("refresh");
    },
    prepare_create() {
      if (this.fonogram_modal.descripEspFong === undefined) {
        this.fonogram_modal.descripEspFong = "";
      } else if (this.fonogram_modal.descripEspFong === null) {
        this.fonogram_modal.descripEspFong = "";
      }
      if (this.fonogram_modal.descripIngFong === undefined) {
        this.fonogram_modal.descripIngFong = "";
      } else if (this.fonogram_modal.descripIngFong === null) {
        this.fonogram_modal.descripIngFong = "";
      }
      if (this.fonogram_modal.dueñoDerchFong === undefined) {
        this.fonogram_modal.dueñoDerchFong = "";
      } else if (this.fonogram_modal.dueñoDerchFong === null) {
        this.fonogram_modal.dueñoDerchFong = "";
      }
      if (this.fonogram_modal.clasficacionFong === undefined) {
        this.fonogram_modal.clasficacionFong = "";
      } else if (this.fonogram_modal.clasficacionFong === null) {
        this.fonogram_modal.clasficacionFong = "";
      }
      if (this.fonogram_modal.añoFong === undefined) {
        this.fonogram_modal.añoFong = "";
      } else if (this.fonogram_modal.añoFong === null) {
        this.fonogram_modal.añoFong = "";
      }
      if (this.fonogram_modal.nacioDueñoDerchFong === undefined) {
        this.fonogram_modal.nacioDueñoDerchFong = "";
      } else if (this.fonogram_modal.nacioDueñoDerchFong === null) {
        this.fonogram_modal.nacioDueñoDerchFong = "";
      }
      let form_data = new FormData();
      if (this.action_modal === "editar" || this.action_modal === "detalles") {
        form_data.append("id", this.fonogram_modal.id);
      }
      if (this.fonogram_modal.codigFong === undefined) {
        this.fonogram_modal.codigFong = this.codigo;
      }
      this.fonogram_modal.duracionFong = this.$store.getters.getDurationFormGetters;
      this.fonogram_modal.codigFong = "FONG-" + this.fonogram_modal.codigFong;
      form_data.append("codigFong", this.fonogram_modal.codigFong);
      form_data.append("tituloFong", this.fonogram_modal.tituloFong);
      form_data.append("duracionFong", this.fonogram_modal.duracionFong);
      form_data.append(
        "clasficacionFong",
        this.fonogram_modal.clasficacionFong
      );
      form_data.append("añoFong", this.fonogram_modal.añoFong);
      form_data.append("dueñoDerchFong", this.fonogram_modal.dueñoDerchFong);
      form_data.append(
        "nacioDueñoDerchFong",
        this.fonogram_modal.nacioDueñoDerchFong
      );
      form_data.append("descripEspFong", this.fonogram_modal.descripEspFong);
      form_data.append("descripIngFong", this.fonogram_modal.descripIngFong);
      form_data.append("product_id", this.fonogram_modal.productos_fongs);
      if (this.file_list.length !== 0) {
        if (this.file_list[0].uid !== this.fonogram_modal.id) {
          if (this.file_list[0].name !== "Logo ver vertical_Ltr Negras.png") {
            form_data.append("portadillaFong", this.file_list[0].originFileObj);
          }
        }
      } else form_data.append("img_default", true);
      this.text_button = "Creando...";
      return form_data;
    },
    set_action() {
      if (this.fonogram.productos_fongs || this.fonogram.tabla) {
        this.tab_visibility = false;
        this.active_tab = "2";
        this.tabs_list.push("tab_1");
      }
      if (this.action === "editar") {
        this.$store.state.duration = this.fonogram.duracionFong;
        if (this.fonogram.deleted_at !== null) {
          this.disabled = true;
          this.activated = false;
        }
        this.text_header_button = "Editar";
        this.text_button = "Editar";
        this.action_cancel_title = "¿Desea cancelar la edición del Fonograma?";
        this.action_title = "¿Desea guardar los cambios en el Fonograma?";
        this.action_close = "La edición del Fonograma se canceló correctamente";
        this.fonogram.descripEspFong =
          this.fonogram.descripEspFong === null
            ? ""
            : this.fonogram.descripEspFong;
        this.fonogram.dueñoDerchFong =
          this.fonogram.dueñoDerchFong === null
            ? ""
            : this.fonogram.dueñoDerchFong;
        this.fonogram.descripIngFong =
          this.fonogram.descripIngFong === null
            ? ""
            : this.fonogram.descripIngFong;
        this.fonogram.productos_fongs = [];
        this.fonogram.codigFong = this.fonogram.codigFong;
        this.fonogram.productos.forEach((element) => {
          this.fonogram.productos_fongs.push(element.id);
        });
        this.fonogram_modal = { ...this.fonogram };
        this.fonogram_modal.codigFong = this.fonogram.codigFong.substr(5);
        if (this.fonogram_modal.portadillaFong !== null) {
          if (
            this.fonogram_modal.portadillaFong !==
            "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png"
          ) {
            this.file_list.push({
              uid: this.fonogram_modal.id,
              name: this.fonogram_modal.portadillaFong.split("/")[
                this.fonogram_modal.portadillaFong.split("/").length - 1
              ],
              url: this.fonogram_modal.portadillaFong,
            });
          } else
            this.file_list.push({
              uid: this.fonogram_modal.id,
              name: "Logo ver vertical_Ltr Negras.png",
              url: "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png",
            });
        }
        this.$store.state["tracks"] = this.fonogram_modal.tracks;
      } else if (this.action_modal === "detalles") {
        this.$store.state.duration = this.fonogram.duracionFong;
        this.active_tab = "2";
        this.detalles = true;
        this.action_cancel_title = "¿Desea cerrar la vista de detalles?";
        this.action_title = "¿Desea guardar los cambios en el Fonograma?";
        this.action_close = "La vista de detalles fue cerrada correctamente";
        this.fonogram.descripEspFong =
          this.fonogram.descripEspFong === null
            ? ""
            : this.fonogram.descripEspFong;
        this.fonogram.descripIngFong =
          this.fonogram.descripIngFong === null
            ? ""
            : this.fonogram.descripIngFong;
        this.fonogram.productos_fongs = [];
        this.fonogram.productos.forEach((element) => {
          this.fonogram.productos_fongs.push(element.id);
        });
        this.fonogram_modal = { ...this.fonogram };
        if (this.fonogram_modal.portadillaFong !== null) {
          if (
            this.fonogram_modal.portadillaFong !==
            "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png"
          ) {
            this.file_list.push({
              uid: this.fonogram_modal.id,
              name: this.fonogram_modal.portadillaFong.split("/")[
                this.fonogram_modal.portadillaFong.split("/").length - 1
              ],
              url: this.fonogram_modal.portadillaFong,
            });
          } else
            this.file_list.push({
              uid: this.fonogram_modal.id,
              name: "Logo ver vertical_Ltr Negras.png",
              url: "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png",
            });
        }
      } else {
        const date = new Date();
        this.fonogram.añoFong = date.getFullYear();
        this.fonogram_modal = { ...this.fonogram };
        this.file_list.push({
          uid: -1,
          name: "Logo ver vertical_Ltr Negras.png",
          url: "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png",
        });
        this.text_button = "Crear";
        this.text_header_button = "Crear";
        this.action_cancel_title = "¿Desea cancelar la creación del Fonograma?";
        this.action_title = "¿Desea crear el Fonograma?";
        this.action_close =
          "La creación del Fonograma se canceló correctamente";
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
    before_upload(file) {
      const isJpgOrPng =
        file.type === "image/jpeg" ||
        file.type === "image/png" ||
        file.type === "image/jpg";
      if (!isJpgOrPng) {
        this.valid_image = false;
        this.$message.error(
          "Sólo puedes subir imágenes como portadilla del fonograma"
        );
      } else this.$message.success("Portadilla cargada correctamente");
      return false;
    },
    load_nomenclators() {
      //* Carga de productos
      axios
        .post("/productos/listar")
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
        .post("/tracks/listar")
        .then((response) => {
          let prod = response.data;
          prod.forEach((element) => {
            if (!element.deleted_at) {
              this.$store.state["all_tracks"].push(element);
              this.$store.state["all_tracks_statics"].push(element);
            }
          });
        })
        .catch((error) => {
          console.log(error);
        });
      axios
        .post("/fonogramas/nomencladores")
        .then((response) => {
          this.list_nomenclators = response.data;
          this.clasficaciones = this.list_nomenclators[0][0];
          this.paises = this.list_nomenclators[1][0];
          this.anhos = this.list_nomenclators[2][0];
        })
        .catch((error) => {
          this.$toast.error("Ha ocurrido un error", "¡Error!", {
            timeout: 2000,
          });
        });
    },

    add_track() {
      if (this.fonogram_modal.tracks !== undefined) {
        this.$store.state["tracks"].push(
          this.get_track(this.fonogram_modal.tracks).track
        );
        this.$store.state["all_tracks"].splice(
          this.get_track(this.fonogram_modal.tracks).index,
          1
        );
        this.fonogram_modal.tracks = undefined;
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
      }
    },

    remove_track(item) {
      let index = this.$store.getters.getTracksFormGetters.indexOf(item);
      this.$store.state.duration = moment(
        this.$store.getters.getDurationFormGetters,
        "HH:mm:ss"
      );
      this.$store.state.duration
        .subtract(
          moment.duration(
            this.$store.getters.getTracksFormGetters[index].duracionTrk
          )
        )
        .format("HH:mm:ss");
      this.$store.state.duration = moment(
        this.$store.getters.getDurationFormGetters,
        "HH:mm:ss"
      ).format("HH:mm:ss");
      this.$store.state["tracks"].splice(index, 1);
      this.$store.state["all_tracks"].push(item);
    },

    new_track() {
      this.visible_management = true;
    },

    get_track(id) {
      for (
        let index = 0;
        index < this.$store.getters.getAllTracksFormGetters.length;
        index++
      ) {
        if (this.$store.getters.getAllTracksFormGetters[index].id === id) {
          return {
            track: this.$store.getters.getAllTracksFormGetters[index],
            index: index,
          };
        }
      }
      return -1;
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

    getTracksID() {
      let answer = [];
      let all_tracks = this.$store.getters.getTracksFormGetters;
      if (all_tracks !== undefined) {
        for (let index = 0; index < all_tracks.length; index++) {
          answer.push([all_tracks[index].id, index + 1]);
        }
      }
      return answer;
    },

    order_up(id) {
      let temp = this.$store.getters.getTracksFormGetters[id];
      this.$store.state["tracks"][
        id
      ] = this.$store.getters.getTracksFormGetters[id - 1];
      this.$store.state["tracks"][id - 1] = temp;
      this.$forceUpdate();
    },

    order_down(id) {
      let temp = this.$store.getters.getTracksFormGetters[id];
      this.$store.state["tracks"][
        id
      ] = this.$store.getters.getTracksFormGetters[id + 1];
      this.$store.state["tracks"][id + 1] = temp;
      this.$forceUpdate();
    },
  },
  components: {
    tabla_tracks,
    modal_management,
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
#modal_gestionar_fonogramas .ant-mentions textarea {
  height: 32px !important;
}
#modal_gestionar_fonogramas .description textarea {
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

<template>
	<div>
		<a-modal
			:closable="false"
			width="80.4%"
			okText="Guardar"
			cancelText="Cancelar"
			cancelType="danger"
			:visible="show"
			id="modal_gestionar_empleados"
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
				<div slot="tabBarExtraContent">{{ text_header_button }} Empleado</div>
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
								:model="empleado_modal"
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
											v-if="action_modal === 'crear'"
											:validate-status="show_error"
											prop="codigEmpl"
											has-feedback
											label="Código"
											:help="show_used_error"
										>
											<a-input
												addon-before="EMPL-"
												:default-value="codigo"
												:disabled="action_modal === 'editar'"
												v-model="empleado_modal.codigEmpl"
											/>
										</a-form-model-item>
										<a-form-model-item
											v-if="action_modal === 'editar'"
											label="Código"
										>
											<a-input
												addon-before="EMPL-"
												:disabled="action_modal === 'editar'"
												v-model="empleado_modal.codigEmpl"
											/>
										</a-form-model-item>
										<a-form-model-item
											v-if="action_modal === 'detalles'"
											label="Código"
										>
											<a-mentions
												readonly
												:placeholder="empleado_modal.codigEmpl"
											>
											</a-mentions>
										</a-form-model-item>
										<a-form-model-item
											v-if="action_modal === 'crear'"
											:validate-status="show_error"
											prop="ciEmpl"
											has-feedback
											label="Carnet de identidad(CI)"
											:help="show_used_error"
										>
											<a-input
												:disabled="action_modal === 'editar'"
												v-model="empleado_modal.ciEmpl"
											/>
										</a-form-model-item>
										<a-form-model-item
											v-if="action_modal === 'editar'"
											label="Carnet de identidad(CI)"
										>
											<a-input
												:disabled="action_modal === 'editar'"
												v-model="empleado_modal.ciEmpl"
											/>
										</a-form-model-item>
										<a-form-model-item
											v-if="action_modal === 'detalles'"
											label="Carnet de identidad(CI)"
										>
											<a-mentions readonly :placeholder="empleado_modal.ciEmpl">
											</a-mentions>
										</a-form-model-item>
									</a-col>
									<a-col span="6">
										<div id="nombresEmpl">
											<a-form-model-item
												v-if="action_modal !== 'detalles'"
												prop="nombresEmpl"
												has-feedback
												label="Nombre"
											>
												<a-input
													:disabled="disabled"
													v-model="empleado_modal.nombresEmpl"
												/>
											</a-form-model-item>
											<a-form-model-item v-else label="Nombre">
												<a-mentions
													readonly
													:placeholder="empleado_modal.nombresEmpl"
												>
												</a-mentions>
											</a-form-model-item>
										</div>
										<a-form-model-item
											v-if="action_modal !== 'detalles'"
											has-feedback
											label="Sexo"
											prop="sexoEmpl"
										>
											<a-select
												:getPopupContainer="(trigger) => trigger.parentNode"
												option-filter-prop="children"
												show-search
												:disabled="disabled"
												v-model="empleado_modal.sexoEmpl"
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
											<a-mentions
												readonly
												:placeholder="empleado_modal.sexoEmpl"
											>
											</a-mentions>
										</a-form-model-item>
									</a-col>
									<a-col span="6">
										<div id="apellidosEmpl">
											<a-form-model-item
												v-if="action_modal !== 'detalles'"
												prop="apellidosEmpl"
												has-feedback
												label="Apellidos"
											>
												<a-input
													:disabled="disabled"
													v-model="empleado_modal.apellidosEmpl"
												/>
											</a-form-model-item>
											<a-form-model-item v-else label="Apellidos">
												<a-mentions
													readonly
													:placeholder="empleado_modal.apellidosEmpl"
												>
												</a-mentions>
											</a-form-model-item>
										</div>
										<a-form-model-item
											v-if="action_modal !== 'detalles'"
											label="Fecha de Nacimiento"
										>
											<a-date-picker
												v-model="empleado_modal.fechaNacEmpl"
												type="date"
												placeholder="aaaa-MM-DD"
												style="width: 100%;"
											/>
										</a-form-model-item>
										<a-form-model-item v-else label="Fecha de Nacimiento">
											<a-mentions
												readonly
												:placeholder="empleado_modal.fechaNacEmpl"
											>
											</a-mentions>
										</a-form-model-item>
									</a-col>
								</a-row>
								<a-row>
									<a-col span="6">
										<a-form-model-item
											v-if="action_modal !== 'detalles'"
											has-feedback
											label="Empresa"
											prop="empresaEmpl"
										>
											<a-select
												:getPopupContainer="(trigger) => trigger.parentNode"
												option-filter-prop="children"
												show-search
												:disabled="disabled"
												v-model="empleado_modal.empresaEmpl"
											>
												<a-select-option
													v-for="empresa in empresas"
													:key="empresa.id"
													:value="empresa.id"
												>
													{{ empresa.nombreEmpr }}
												</a-select-option>
											</a-select>
										</a-form-model-item>
										<a-form-model-item v-else label="Empresa">
											<a-mentions readonly :placeholder="empleado.empresaEmpl">
											</a-mentions>
										</a-form-model-item>
										<a-form-model-item
											v-if="action_modal !== 'detalles'"
											prop="cargoEmpl"
											has-feedback
											label="Cargo"
										>
											<a-input
												:disabled="disabled"
												v-model="empleado_modal.cargoEmpl"
											/>
										</a-form-model-item>
										<a-form-model-item v-else label="Cargo">
											<a-mentions
												readonly
												:placeholder="empleado_modal.cargoEmpl"
											>
											</a-mentions>
										</a-form-model-item>
										<a-form-model-item
											v-if="action_modal !== 'detalles'"
											prop="celEmpl"
											has-feedback
											label="Celular"
										>
											<a-input
												:disabled="disabled"
												v-model="empleado_modal.celEmpl"
											/>
										</a-form-model-item>
										<a-form-model-item v-else label="Celular">
											<a-mentions
												readonly
												:placeholder="empleado_modal.celEmpl"
											>
											</a-mentions>
										</a-form-model-item>
									</a-col>
									<a-col span="6">
										<a-form-model-item
											v-if="action_modal !== 'detalles'"
											prop="DptoEmpl"
											has-feedback
											label="Departamento"
										>
											<a-input
												:disabled="disabled"
												v-model="empleado_modal.DptoEmpl"
											/>
										</a-form-model-item>
										<a-form-model-item v-else label="Departamento">
											<a-mentions
												readonly
												:placeholder="empleado_modal.DptoEmpl"
											>
											</a-mentions>
										</a-form-model-item>
										<a-form-model-item
											v-if="action_modal !== 'detalles'"
											prop="emailEmpl"
											has-feedback
											label="Correo"
										>
											<a-input
												:disabled="disabled"
												v-model="empleado_modal.emailEmpl"
											/>
										</a-form-model-item>
										<a-form-model-item v-else label="Correo">
											<a-mentions
												readonly
												:placeholder="empleado_modal.emailEmpl"
											>
											</a-mentions>
										</a-form-model-item>
										<a-form-model-item
											v-if="action_modal !== 'detalles'"
											prop="telfEmpl"
											has-feedback
											label="Teléfono"
										>
											<a-input
												:disabled="disabled"
												v-model="empleado_modal.telfEmpl"
											/>
										</a-form-model-item>
										<a-form-model-item v-else label="Teléfono">
											<a-mentions
												readonly
												:placeholder="empleado_modal.telfEmpl"
											>
											</a-mentions>
										</a-form-model-item>
									</a-col>
									<a-col span="12">
										<div id="direccion">
											<a-form-model-item
												v-if="action_modal !== 'detalles'"
												has-feedback
												label="Dirección Particular"
												prop="direccionEmpl"
												id="direccion"
											>
												<a-input
													:disabled="disabled"
													style="width: 100%; height: 150px"
													v-model="empleado_modal.direccionEmpl"
													type="textarea"
												/>
											</a-form-model-item>
											<a-form-model-item v-else label="Dirección Particular">
												<div class="direccion">
													<a-mentions
														readonly
														:placeholder="empleado_modal.direccionEmpl"
													>
													</a-mentions>
												</div>
											</a-form-model-item>
										</div>
										<div id="description">
											<a-form-model-item
												v-if="action_modal !== 'detalles'"
												has-feedback
												label="Descripción del Empleado"
												prop="descripEmpl"
												id="description"
											>
												<a-input
													:disabled="disabled"
													style="width: 100%; height: 150px"
													v-model="empleado_modal.descripEmpl"
													type="textarea"
												/>
											</a-form-model-item>
											<a-form-model-item
												v-else
												label="Descripción del Empleado"
											>
												<div class="description">
													<a-mentions
														readonly
														:placeholder="empleado_modal.descripEmpl"
													>
													</a-mentions>
												</div>
											</a-form-model-item>
										</div>
									</a-col>
								</a-row>
								<a-row>
									<a-col span="11">
										<div class="web">
											<a-form-model-item
												v-if="action_modal !== 'detalles'"
												prop="webSocialEmpl"
												has-feedback
												label="Web Social"
											>
												<a-input
													:disabled="disabled"
													v-model="empleado_modal.webSocialEmpl"
												/>
											</a-form-model-item>
											<a-form-model-item v-else label="Web Social">
												<a-mentions
													readonly
													:placeholder="empleado_modal.webSocialEmpl"
												>
												</a-mentions>
											</a-form-model-item>
										</div>
									</a-col>
								</a-row>
							</a-form-model>
							<a-row>
								<a-button
									v-if="
										action_modal !== 'crear' &&
											action_modal !== 'crear_Empleado'
									"
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
				<a-tab-pane key="2" v-if="action_modal !== 'crear'" :disabled="tab_2">
					<span slot="tab"> Trazas </span>
					<a-row>
						<a-col span="12">
							<div class="section-title">
								<h4>Trazas</h4>
							</div>
						</a-col>
					</a-row>
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
	</div>
</template>

<script>
import moment from "../../../../../node_modules/moment";
import axios from "axios";
export default {
	props: ["action", "empleado", "empleados_list"],
	data() {
		let validate_codig_unique = (rule, value, callback) => {
			this.empleados_list.forEach((element) => {
				if (element.codigEmpl.substr(5) === value.replace(/ /g, "")) {
					callback(new Error("Código usado"));
				}
			});
			callback();
		};
		let validate_ci_unique = (rule, value, callback) => {
			this.empleados_list.forEach((element) => {
				if (element.ciEmpl === value.replace(/ /g, "")) {
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
			active_tab: "1",
			tab_1: false,
			tab_2: true,
			type: "",
			detalles: false,
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
			empleado_modal: {},
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
			list_nomenclators: [],
			empresas: [],
			empresa: {},
			codigo: "",
			formItemLayout: {
				wrapperCol: {
					xs: {span: 24, offset: 0},
					sm: {span: 20, offset: 4},
				},
			},
			rules: {
				codigEmpl: [
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
				ciEmpl: [
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
				empresaEmpl: [
					{
						required: true,
						message: "Campo requerido",
						trigger: "change",
					},
				],
				nombresEmpl: [
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
				apellidosEmpl: [
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
				sexoEmpl: [
					{
						required: true,
						message: "Campo reuqerido",
						trigger: "change",
					},
				],
				direccionEmpl: [
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
				telfEmpl: [
					{
						pattern: "^[0-9]*$",
						message: "Caracter no válido",
						trigger: "change",
					},
				],
				celEmpl: [
					{
						pattern: "^[0-9]*$",
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
			this.codigo = this.generar_codigo(this.empleados_list);
		}
	},
	computed: {
		active() {
			if (this.text_button === "Editar") {
				let same_photo = false;
				if (this.file_list[0]) {
					same_photo = this.file_list[0].uid !== this.empleado_modal.id;
				}
				return (
					(same_photo || this.file_list.length === 0 || !this.compare_object) &&
					this.valid_image
				);
			} else
				return (
					this.empleado_modal.ciEmpl &&
					this.empleado_modal.nombresEmpl &&
					this.empleado_modal.apellidosEmpl &&
					this.empleado_modal.sexoEmpl &&
					this.valid_image
				);
		},
		/*
		 *Método que compara los campos editables del producto para saber si se ha modificado
		 */
		compare_object() {
			if (this.active_tab === "2") {
				return false;
			} else {
				return (
					this.empleado_modal.nombresEmpl === this.empleado.nombresEmpl &&
					this.empleado_modal.apellidosEmpl === this.empleado.apellidosEmpl &&
					this.empleado_modal.sexoEmpl === this.empleado.sexoEmpl &&
					this.empleado_modal.direccionEmpl === this.empleado.direccionEmpl
				);
			}
		},
	},
	methods: {
		filter_option(input, option) {
			return (
				option.componentOptions.children[0].text
					.toLowerCase()
					.indexOf(input.toLowerCase()) >= 0
			);
		},
		moment,
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
			if (this.empleado_modal.codigEmpl === undefined) {
				this.empleado_modal.codigEmpl = this.codigo;
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
					.post(`/empleados/editar`, form_data, {
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
							"Se ha modificado el Empleado correctamente",
							"¡Éxito!",
							{timeout: 2000}
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
					.post("/empleados", form_data, {
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
							"Se ha creado el Empleado correctamente",
							"¡Éxito!",
							{timeout: 2000}
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
			if (this.empleado_modal.direccionEmpl === undefined) {
				this.empleado_modal.direccionEmpl = "";
			}
			if (this.empleado_modal.descripEmpl === undefined) {
				this.empleado_modal.descripEmpl = "";
			}
			if (this.empleado_modal.fechaNacEmpl === undefined) {
				this.empleado_modal.fechaNacEmpl = "";
			} else {
				this.empleado_modal.fechaNacEmpl = this.empleado_modal.fechaNacEmpl.format(
					"yyyy-MM-DD HH:mm:ss"
				);
			}
			if (this.empleado_modal.DptoEmpl === undefined) {
				this.empleado_modal.DptoEmpl = "";
			}
			if (this.empleado_modal.cargoEmpl === undefined) {
				this.empleado_modal.cargoEmpl = "";
			}
			if (this.empleado_modal.emailEmpl === undefined) {
				this.empleado_modal.emailEmpl = "";
			}
			if (this.empleado_modal.celEmpl === undefined) {
				this.empleado_modal.celEmpl = "";
			}
			if (this.empleado_modal.telfEmpl === undefined) {
				this.empleado_modal.telfEmpl = "";
			}
			if (this.empleado_modal.webSocialEmpl === undefined) {
				this.empleado_modal.webSocialEmpl = "";
			}
			let form_data = new FormData();
			if (this.action_modal === "editar" || this.action_modal === "detalles") {
				form_data.append("id", this.empleado_modal.id);
			}
			if (this.empleado_modal.codigEmpl === undefined) {
				this.empleado_modal.codigEmpl = this.codigo;
			}
			this.empleado_modal.codigEmpl = "EMPL-" + this.empleado_modal.codigEmpl;
			form_data.append("codigEmpl", this.empleado_modal.codigEmpl);
			form_data.append("ciEmpl", this.empleado_modal.ciEmpl);
			form_data.append("nombresEmpl", this.empleado_modal.nombresEmpl);
			form_data.append("apellidosEmpl", this.empleado_modal.apellidosEmpl);
			form_data.append("sexoEmpl", this.empleado_modal.sexoEmpl);
			form_data.append(
				"empresaEmpl",
				this.get_empresa(this.empleado_modal.empresaEmpl).nombreEmpr
			);
			form_data.append("empresa_id", this.empleado_modal.empresaEmpl);
			form_data.append("fechaNacEmpl", this.empleado_modal.fechaNacEmpl);
			form_data.append("direccionEmpl", this.empleado_modal.direccionEmpl);
			form_data.append("DptoEmpl", this.empleado_modal.DptoEmpl);
			form_data.append("cargoEmpl", this.empleado_modal.cargoEmpl);
			form_data.append("emailEmpl", this.empleado_modal.emailEmpl);
			form_data.append("celEmpl", this.empleado_modal.celEmpl);
			form_data.append("telfEmpl", this.empleado_modal.telfEmpl);
			form_data.append("webSocialEmpl", this.empleado_modal.webSocialEmpl);
			form_data.append("descripEmpl", this.empleado_modal.descripEmpl);
			if (this.file_list.length !== 0) {
				if (this.file_list[0].uid !== this.empleado_modal.id) {
					if (this.file_list[0].name !== "Logo ver vertical_Ltr Negras.png") {
						form_data.append("fotoEmpl", this.file_list[0].originFileObj);
					}
				}
			} else form_data.append("img_default", true);
			this.text_button = "Creando...";
			return form_data;
		},
		set_action() {
			if (this.action_modal === "editar") {
				if (this.empleado.deleted_at !== null) {
					this.disabled = true;
					this.activated = false;
				}
				this.text_header_button = "Editar";
				this.text_button = "Editar";
				this.action_cancel_title = "¿Desea cancelar la edición del Empleado?";
				this.action_title = "¿Desea guardar los cambios en el Empleado?";
				this.action_close = "La edición del Empleado se canceló correctamente";
				this.empleado_modal = {...this.empleado};
				this.empleado_modal.codigEmpl = this.empleado.codigEmpl.substr(5);
				this.empleado_modal.fechaNacEmpl = moment(
					this.empleado.fechaNacEmpl,
					"yyyy-MM-DD"
				);
				this.empleado_modal.empresaEmpl = this.empleado.empresa_id;
				if (this.empleado_modal.fotoEmpl !== null) {
					if (
						this.empleado_modal.fotoEmpl !==
						"/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png"
					) {
						this.file_list.push({
							uid: this.empleado_modal.id,
							name: this.empleado_modal.fotoEmpl.split("/")[
								this.empleado_modal.fotoEmpl.split("/").length - 1
							],
							url: this.empleado_modal.fotoEmpl,
						});
					} else
						this.file_list.push({
							uid: this.empleado_modal.id,
							name: "Logo ver vertical_Ltr Negras.png",
							url: "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png",
						});
				}
			} else if (this.action_modal === "detalles") {
				this.detalles = true;
				if (this.empleado.deleted_at !== null) {
					this.disabled = true;
					this.activated = false;
				}
				this.text_header_button = "Detalles";
				this.text_button = "Detalles";
				this.empleado.direccionEmpl =
					this.empleado.direccionEmpl === null
						? ""
						: this.empleado.direccionEmpl;
				this.empleado_modal = {...this.empleado};
				if (this.empleado_modal.fotoEmpl !== null) {
					if (
						this.empleado_modal.fotoEmpl !==
						"/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png"
					) {
						this.file_list.push({
							uid: this.empleado_modal.id,
							name: this.empleado_modal.fotoEmpl.split("/")[
								this.empleado_modal.fotoEmpl.split("/").length - 1
							],
							url: this.empleado_modal.fotoEmpl,
						});
					} else
						this.file_list.push({
							uid: this.empleado_modal.id,
							name: "Logo ver vertical_Ltr Negras.png",
							url: "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png",
						});
				}
			} else {
				this.empleado_modal = {...this.empleado};
				this.file_list.push({
					uid: 1,
					name: "Logo ver vertical_Ltr Negras.png",
					url: "/BisMusic/Imagenes/Logo ver vertical_Ltr Negras.png",
				});
				this.text_button = "Crear";
				this.text_header_button = "Crear";
				this.action_cancel_title = "¿Desea cancelar la creación del Empleado?";
				this.action_title = "¿Desea crear el Empleado?";
				this.action_close = "La creación del Empleado se canceló correctamente";
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
		handle_change({fileList}) {
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
					"Sólo puedes subir imágenes como foto del empleado"
				);
			} else this.$message.success("Foto cargada correctamente");
			return false;
		},
		load_nomenclators() {
			axios
				.post("/empleados/nomencladores")
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
				.post("/empresas/listar")
				.then((response) => {
					let prod = response.data;
					prod.forEach((element) => {
						if (!element.deleted_at) {
							this.empresas.push(element);
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
				answer.push(parseInt(arr[i].codigEmpl.substr(5, 8)));
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
		get_empresa(id) {
			for (let index = 0; index < this.empresas.length; index++) {
				if (this.empresas[index].id === id) {
					return this.empresas[index];
				}
			}
			return null;
		},
	},
	components: {},
};
</script>

<style>
#modal_gestionar_empleados .ant-upload-list-item,
.ant-upload-list-item-undefined,
.ant-upload-list-item-list-type-picture-card,
.ant-upload,
.ant-upload-select,
.ant-upload-select-picture-card,
.ant-upload-list-picture-card-container {
	width: 170px !important;
	height: 170px !important;
}
#modal_gestionar_empleados .ant-select-search input {
	border-color: rgb(255, 255, 255) !important;
}
#modal_gestionar_empleados textarea {
	height: 150px !important;
}
#modal_gestionar_empleados .ant-form-item-control {
	width: 85%;
}
#modal_gestionar_empleados .web .ant-form-item-control {
	width: 100% !important;
}
#modal_gestionar_empleados .custom-form-item .ant-form-item-control {
	width: 100% !important;
}
#modal_gestionar_empleados #direccion .ant-form-item-control {
	width: 92% !important;
}
#modal_gestionar_empleados #description .ant-form-item-control {
	width: 92% !important;
}
#modal_gestionar_empleados .ant-mentions textarea {
	height: 32px !important;
}
#modal_gestionar_empleados #description textarea {
	height: 110px !important;
}
#modal_gestionar_empleados #direccion textarea {
	height: 50px !important;
}
#modal_gestionar_empleados .ant-col-sm-offset-4 {
	margin-left: 0px !important;
}
#modal_gestionar_empleados .dynamic-delete-button {
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
#modal_gestionar_empleados .ant-col-sm-20 {
	width: 100%;
}
</style>

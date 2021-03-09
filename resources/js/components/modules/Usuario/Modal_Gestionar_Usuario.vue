<template>
	<div>
		<a-modal
			:closable="false"
			width="80.4%"
			okText="Guardar"
			cancelText="Cancelar"
			cancelType="danger"
			:visible="show"
			id="modal_gestionar_usuarios"
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
				<div slot="tabBarExtraContent">{{ text_header_button }} Usuario</div>
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
								:model="usuario_modal"
								:rules="rules"
							>
								<a-row>
									<a-col span="11">
										<a-form-model-item
											v-if="action_modal !== 'detalles'"
											prop="nombreUser"
											has-feedback
											label="Nombre de Usuario"
										>
											<a-input
												:disabled="disabled"
												v-model="usuario_modal.nombreUser"
											/>
										</a-form-model-item>
										<a-form-model-item v-else label="Nombre de Usuario">
											<a-mentions
												readonly
												:placeholder="usuario_modal.nombreUser"
											>
											</a-mentions>
										</a-form-model-item>
										<a-form-model-item
											v-if="action_modal !== 'detalles'"
											prop="emailUser"
											has-feedback
											label="Correo"
										>
											<a-input
												:disabled="disabled"
												v-model="usuario_modal.emailUser"
											/>
										</a-form-model-item>
										<a-form-model-item v-else label="Correo">
											<a-mentions
												readonly
												:placeholder="usuario_modal.emailUser"
											>
											</a-mentions>
										</a-form-model-item>
										<a-form-model-item
											v-if="action_modal !== 'detalles'"
											has-feedback
											label="Empleado"
											prop="empleado_id"
										>
											<a-select
												:getPopupContainer="(trigger) => trigger.parentNode"
												option-filter-prop="children"
												show-search
												:disabled="disabled"
												v-model="usuario_modal.empleado_id"
											>
												<a-select-option
													v-for="empleado in empleados"
													:key="empleado.id"
													:value="empleado.id"
												>
													{{
														empleado.nombresEmpl + " " + empleado.apellidosEmpl
													}}
												</a-select-option>
											</a-select>
										</a-form-model-item>
										<a-form-model-item v-else label="Empleado">
											<a-mentions readonly :placeholder="nombreEmpleado">
											</a-mentions>
										</a-form-model-item>
									</a-col>
									<a-col span="11">
										<a-form-model-item
											v-if="action_modal === 'crear'"
											prop="contraseñaUser"
											has-feedback
											label="Contraseña"
										>
											<a-input-password
												:disabled="disabled"
												v-model="usuario_modal.contraseñaUser"
											/>
										</a-form-model-item>
										<a-form-model-item
											v-if="action_modal === 'crear'"
											prop="confirmPass"
											has-feedback
											label="Repetir Contraseña"
										>
											<a-input-password
												:disabled="disabled"
												v-model="confirmPass"
											/>
										</a-form-model-item>
										<a-form-model-item
											v-if="action_modal !== 'detalles'"
											label="Fecha de Caducidad"
										>
											<a-date-picker
												v-model="usuario_modal.fechaCaduc"
												type="date"
												placeholder="aaaa-MM-DD"
												style="width: 100%;"
											/>
										</a-form-model-item>
										<a-form-model-item v-else label="Fecha de Caducidad">
											<a-mentions
												readonly
												:placeholder="usuario_modal.fechaCaduc"
											>
											</a-mentions>
										</a-form-model-item>
									</a-col>
								</a-row>
							</a-form-model>
							<a-row>
								<a-button
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
	props: ["action", "usuario", "usuarios_list"],
	data() {
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
			usuario_modal: {},
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
			empleados: [],
			confirmPass: "",
			empresa: {},
			nombreEmpleado: "",
			formItemLayout: {
				wrapperCol: {
					xs: {span: 24, offset: 0},
					sm: {span: 20, offset: 4},
				},
			},
			rules: {
				empleado_id: [
					{
						required: true,
						message: "Campo requerido",
						trigger: "change",
					},
				],
				nombreUser: [
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
	},
	mounted() {
		if (this.action_modal === "detalles") {
			for (let index = 0; index < this.empleados.length; index++) {
				if (this.empleados[index].id === this.empleado.id) {
					this.nombreEmpleado =
						this.empleados[index].nombresEmpl +
						" " +
						this.empleados[index].apellidosEmpl;
				}
			}
		}
	},
	computed: {
		active() {
			return (
				this.usuario_modal.nombreUser &&
				this.usuario_modal.empleado_id &&
				this.usuario_modal.contraseñaUser &&
				this.usuario_modal.fechaCaduc &&
				this.usuario_modal.emailUser
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
					this.usuario_modal.nombreUser === this.usuario.nombreUser &&
					this.usuario_modal.emailUser === this.usuario.emailUser &&
					this.usuario_modal.contraseñaUser === this.usuario.contraseñaUser &&
					this.usuario_modal.fechaCaduc === this.usuario.fechaCaduc
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
					.post(`/usuarios/editar`, form_data, {
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
							"Se ha modificado el Usuario correctamente",
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
					.post("/usuarios", form_data, {
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
							"Se ha creado el Usuario correctamente",
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
			if (this.usuario_modal.fechaCaduc === undefined) {
				this.usuario_modal.fechaCaduc = "";
			} else {
				this.usuario_modal.fechaCaduc = this.usuario_modal.fechaCaduc.format(
					"yyyy-MM-DD HH:mm:ss"
				);
			}
			if (this.usuario_modal.emailUser === undefined) {
				this.usuario_modal.emailUser = "";
			}
			let form_data = new FormData();
			if (this.action_modal === "editar" || this.action_modal === "detalles") {
				form_data.append("id", this.usuario_modal.id);
			}
			form_data.append("nombreUser", this.usuario_modal.nombreUser);
			form_data.append("empleado_id", this.usuario_modal.empleado_id);
			form_data.append("fechaCaduc", this.usuario_modal.fechaCaduc);
			form_data.append("emailUser", this.usuario_modal.emailUser);
			form_data.append("contraseñaUser", this.usuario_modal.contraseñaUser);
			this.text_button = "Creando...";
			return form_data;
		},
		set_action() {
			if (this.action_modal === "editar") {
				if (this.usuario.deleted_at !== null) {
					this.disabled = true;
					this.activated = false;
				}
				this.text_header_button = "Editar";
				this.text_button = "Editar";
				this.action_cancel_title = "¿Desea cancelar la edición del Usuario?";
				this.action_title = "¿Desea guardar los cambios en el Usuario?";
				this.action_close = "La edición del Usuario se canceló correctamente";
				this.usuario_modal = {...this.usuario};
				this.usuario_modal.fechaCaduc = moment(
					this.usuario.fechaCaduc,
					"yyyy-MM-DD"
				);
				this.usuario_modal.empleado_id = this.usuario.empleado_id;
			} else if (this.action_modal === "detalles") {
				this.detalles = true;
				if (this.usuario.deleted_at !== null) {
					this.disabled = true;
					this.activated = false;
				}
				this.text_header_button = "Detalles";
				this.text_button = "Detalles";
				this.usuario_modal = {...this.usuario};
			} else {
				this.usuario_modal = {...this.usuario};
				this.text_button = "Crear";
				this.text_header_button = "Crear";
				this.action_cancel_title = "¿Desea cancelar la creación del Usuario?";
				this.action_title = "¿Desea crear el Usuario?";
				this.action_close = "La creación del Usuario se canceló correctamente";
			}
		},
		load_nomenclators() {
			axios
				.post("/usuarios/nomencladores")
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
				.post("/usuarios/listar")
				.then((response) => {
					let prod = response.data;
					prod.forEach((element) => {
						if (!element.deleted_at) {
							this.usuarios.push(element);
						}
					});
				})
				.catch((error) => {
					console.log(error);
				});
			axios
				.post("/empleados/listar")
				.then((response) => {
					console.log(response.data);
					let prod = response.data;
					prod.forEach((element) => {
						if (!element.deleted_at) {
							this.empleados.push(element);
						}
					});
				})
				.catch((error) => {
					console.log(error);
				});
		},
		get_empleado(id) {
			for (let index = 0; index < this.empleados.length; index++) {
				if (this.empleados[index].id === id) {
					return this.empleados[index];
				}
			}
			return null;
		},
	},
	components: {},
};
</script>

<style>
#modal_gestionar_usuarios .ant-upload-list-item,
.ant-upload-list-item-undefined,
.ant-upload-list-item-list-type-picture-card,
.ant-upload,
.ant-upload-select,
.ant-upload-select-picture-card,
.ant-upload-list-picture-card-container {
	width: 170px !important;
	height: 170px !important;
}
#modal_gestionar_usuarios .ant-select-search input {
	border-color: rgb(255, 255, 255) !important;
}
#modal_gestionar_usuarios textarea {
	height: 150px !important;
}
#modal_gestionar_usuarios .ant-form-item-control {
	width: 85%;
}
#modal_gestionar_usuarios .web .ant-form-item-control {
	width: 100% !important;
}
#modal_gestionar_usuarios .custom-form-item .ant-form-item-control {
	width: 100% !important;
}
#modal_gestionar_usuarios #direccion .ant-form-item-control {
	width: 92% !important;
}
#modal_gestionar_usuarios #description .ant-form-item-control {
	width: 92% !important;
}
#modal_gestionar_usuarios .ant-mentions textarea {
	height: 32px !important;
}
#modal_gestionar_usuarios #description textarea {
	height: 110px !important;
}
#modal_gestionar_usuarios #direccion textarea {
	height: 50px !important;
}
#modal_gestionar_usuarios .ant-col-sm-offset-4 {
	margin-left: 0px !important;
}
#modal_gestionar_usuarios .dynamic-delete-button {
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
#modal_gestionar_usuarios .ant-col-sm-20 {
	width: 100%;
}
</style>

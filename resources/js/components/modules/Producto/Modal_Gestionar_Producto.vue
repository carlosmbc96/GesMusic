<template>
	<div>
		<a-modal
			:closable="false"
			width="80.4%"
			okText="Guardar"
			cancelText="Cancelar"
			:visible="show"
			@cancel="handle_cancel()"
			id="modal_gestionar_productos"
		>
			<template slot="footer">
				<a-button type="danger" key="back" @click="handle_cancel()">
					Cancelar
				</a-button>
				<a-popconfirm
					placement="bottomRight"
					@confirm="confirmar()"
					ok-text="Si"
					title="¿Seguro que desea guardar esta información?"
				>
					<a-button slot="cancelText" class="ant-btn ant-btn-sm cancel-text">
						No
					</a-button>
					<a-button :loading="waiting" v-if="activar" type="primary">
						{{ text_button }}
					</a-button>
				</a-popconfirm>
			</template>
			<!-- Tabs -->
			<a-tabs :activeKey="active_tab">
				<div slot="tabBarExtraContent">{{ text_header_button }} Producto</div>
				<!-- Tab 1 -->
				<a-tab-pane key="1" v-if="tab_visibility">
					<span slot="tab"> Proyecto </span>
					<a-spin :spinning="spinning">
						<a-icon
							slot="indicator"
							style="font-size: 30px"
							type="loading"
							spin
						/>
						<div>
							<a-form-model
								ref="formularioproject"
								:model="product_modal"
								:rules="rules"
							>
								<a-row>
									<a-col span="12">
										<div class="section-title">
											<h4>Selector de Proyecto</h4>
										</div>
									</a-col>
								</a-row>
								<a-col span="12">
									<a-form-model-item
										label="Código"
										prop="proyecto_id"
										has-feedback
									>
										<a-select
											option-filter-prop="children"
											:filter-option="filter_option"
											show-search
											v-model="product_modal.proyecto_id"
											style="width: 50% !important"
											:disabled="disabled"
										>
											<a-select-option
												v-for="project in projects"
												:key="project.id"
												:value="project.id"
											>
												{{ project.codigProy }}
											</a-select-option>
										</a-select>
									</a-form-model-item>
									<a-form-model-item has-feedback label="Nombre">
										<a-select
											option-filter-prop="children"
											:filter-option="filter_option"
											show-search
											v-model="product_modal.proyecto_id"
											:disabled="disabled"
										>
											<a-select-option
												v-for="project in projects"
												:key="project.id"
												:value="project.id"
											>
												{{ project.nombreProy }}
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
				<!-- Tab 2 -->
				<a-tab-pane key="2" :disabled="tab_2">
					<span slot="tab"> Generales </span>
					<!-- Datos Generales -->
					<a-form-model
						layout="vertical"
						ref="formularioGenerales"
						:model="product_modal"
						:rules="rules"
					>
						<a-row>
							<a-col span="11">
								<div class="section-title">
									<h4>Datos Generales</h4>
								</div>
								<a-row style="margin-bottom: -18px !important">
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
										<a-form-model-item
											style="width: 55% !important"
											has-feedback
											label="Año"
											prop="añoProd"
										>
											<a-select
												option-filter-prop="children"
												:filter-option="filter_option"
												show-search
												:disabled="disabled"
												v-model="product_modal.añoProd"
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
										<a-form-model-item style="margin-top: 20px">
											<a-checkbox
												:disabled="disabled"
												v-model="producPrincProd"
												:value="producPrincProd"
											>
												¿Producto principal del proyecto?
											</a-checkbox>
										</a-form-model-item>
									</a-col>
									<a-col span="11" style="float: right">
										<a-form-model-item
											v-if="action_modal !== 'editar'"
											:validate-status="show_error"
											:help="show_used_error"
											prop="codigProd"
											has-feedback
											label="Código"
										>
											<a-input
												:disabled="action_modal === 'editar'"
												v-model="product_modal.codigProd"
											/>
										</a-form-model-item>
										<a-form-model-item v-else label="Código">
											<a-input
												:disabled="action_modal === 'editar'"
												v-model="product_modal.codigProd"
											/>
										</a-form-model-item>
										<a-form-model-item
											prop="tituloProd"
											has-feedback
											label="Título"
										>
											<a-input
												:disabled="disabled"
												v-model="product_modal.tituloProd"
											/>
										</a-form-model-item>
										<a-form-model-item
											has-feedback
											label="Género musical"
											prop="genMusicPro"
										>
											<a-select
												option-filter-prop="children"
												:filter-option="filter_option"
												show-search
												:disabled="disabled"
												v-model="product_modal.genMusicPro"
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
									</a-col>
								</a-row>
								<div v-if="action_modal === 'editar'">
									<div class="section-title">
										<h4>Datos Descripciones</h4>
									</div>
									<a-row>
										<a-form-model-item
											has-feedback
											label="Descripción en español del producto"
											prop="descripEspPro"
										>
											<a-input
												:disabled="disabled"
												style="width: 100%; height: 100px"
												v-model="product_modal.descripEspPro"
												type="textarea"
											/>
										</a-form-model-item>
										<a-form-model-item
											has-feedback
											label="Descripción en inglés del producto"
											prop="descripIngPro"
										>
											<a-input
												:disabled="disabled"
												style="width: 100%; height: 100px"
												v-model="product_modal.descripIngPro"
												type="textarea"
											/>
										</a-form-model-item>
									</a-row>
									<div class="section-title">
										<h4>Datos Gestión</h4>
									</div>
									<a-row style="margin-bottom: 30px">
										<a-col span="12">
											<a-form-model-item
												has-feedback
												label="Estado de Digitalización"
												prop="estadodigProd"
											>
												<a-select
													option-filter-prop="children"
													:filter-option="filter_option"
													show-search
													:disabled="disabled"
													v-model="product_modal.estadodigProd"
												>
													<a-select-option
														v-for="nomenclator in estados"
														:key="nomenclator.id"
														:value="nomenclator.nombreTer"
													>
														{{ nomenclator.nombreTer }}
													</a-select-option>
												</a-select>
											</a-form-model-item>
										</a-col>
									</a-row>
								</div>
							</a-col>
							<a-col span="11" style="float: right">
								<div class="section-title">
									<h4>Datos Comerciales</h4>
								</div>
								<a-row style="margin-bottom: 30px">
									<a-col span="11">
										<a-form-model-item
											prop="codigBarProd"
											has-feedback
											label="Código de barra"
										>
											<a-input
												:disabled="disabled"
												v-model="product_modal.codigBarProd"
											/>
										</a-form-model-item>
										<a-form-model-item
											has-feedback
											label="Estatus comercial"
											prop="statusComProd"
										>
											<a-select
												option-filter-prop="children"
												:filter-option="filter_option"
												show-search
												:disabled="disabled"
												v-model="product_modal.statusComProd"
											>
												<a-select-option
													v-for="nomenclator in status"
													:key="nomenclator.id"
													:value="nomenclator.nombreTer"
												>
													{{ nomenclator.nombreTer }}
												</a-select-option>
											</a-select>
										</a-form-model-item>
										<a-form-model-item style="margin-top: 20px">
											<a-checkbox
												:disabled="disabled"
												v-model="activoCatalbisPro"
												:value="activoCatalbisPro"
											>
												¿Activo en el Catálogo de Bismusic?
											</a-checkbox>
										</a-form-model-item>
										<a-form-model-item>
											<a-checkbox
												:disabled="disabled"
												v-model="primeraPantProd"
												:value="primeraPantProd"
											>
												¿Estará en la Primera Pantalla?
											</a-checkbox>
										</a-form-model-item>
									</a-col>
									<a-col span="11" style="float: right">
										<a-form-model-item label="Destinos comerciales">
											<a-select
												:disabled="disabled"
												mode="multiple"
												v-model="product_modal.destinosComerPro"
											>
												<a-select-option
													v-for="nomenclator in destinos"
													:key="nomenclator.id"
													:value="nomenclator.nombreTer"
												>
													{{ nomenclator.nombreTer }}
												</a-select-option>
											</a-select>
										</a-form-model-item>
										<a-form-model-item
											has-feedback
											label="Sello discográfico"
											prop="sellodiscProd"
										>
											<a-select
												option-filter-prop="children"
												:filter-option="filter_option"
												show-search
												:disabled="disabled"
												v-model="product_modal.sellodiscProd"
											>
												<a-select-option
													v-for="nomenclator in sellos"
													:key="nomenclator.id"
													:value="nomenclator.nombreTer"
												>
													{{ nomenclator.nombreTer }}
												</a-select-option>
											</a-select>
										</a-form-model-item>
										<a-form-model-item style="margin-top: 20px">
											<a-checkbox
												:disabled="disabled"
												v-model="catalDigitalPro"
												:value="catalDigitalPro"
											>
												¿Estará en el catálogo digital?
											</a-checkbox>
										</a-form-model-item>
									</a-col>
								</a-row>
								<div v-if="action_modal === 'editar'">
									<div class="section-title">
										<h4>Gestión de Intérpretes</h4>
									</div>
									<a-form-model-item>
										<a-checkbox
											:disabled="disabled"
											v-model="variosInterpretesProd"
											:value="variosInterpretesProd"
											style="padding-bottom: 20px"
											@change="cambiar_varios_interpretes"
										>
											¿El producto tiene varios intérpretes?
										</a-checkbox>
									</a-form-model-item>
									<a-form-model-item
										v-for="(interprete, index) in interpretesProd"
										:key="interprete.key"
										v-bind="index === 0 ? formItemLayout : {}"
									>
										<a-row>
											<a-col span="12">
												<div class="ant-form-item-label">
													<label>Intérprete</label>
												</div>
												<a-select
													option-filter-prop="children"
													:filter-option="filter_option"
													show-search
													:disabled="disabled"
													mode="single"
													v-model="interprete.value"
													:id="interprete.key"
													class="interpretes-select"
													@focus="last_interprete(interprete)"
													@change="change_interprete(interprete)"
												>
													<a-select-option
														v-for="_interprete in copia_interpretes"
														:key="_interprete.id"
														:value="_interprete.nombreInterp"
													>
														{{ _interprete.nombreInterp }}
													</a-select-option>
												</a-select>
											</a-col>
											<a-col span="12">
												<div class="ant-form-item-label">
													<label>Roles</label>
												</div>
												<a-select
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
												<a-button
													v-if="interpretesProd.length > 1"
													class="dynamic-delete-button"
													:disabled="interpretesProd.length === 1"
													@click="remove_interprete(interprete)"
												>
													<small>
														<b style="vertical-align: top"> x </b>
													</small>
												</a-button>
											</a-col>
										</a-row>
									</a-form-model-item>
									<a-form-model-item v-bind="formItemLayout" v-if="varios_int">
										<a-button
											:disabled="disabled"
											style="
												color: white;
												background-color: rgb(45, 171, 229) !important;
											"
											@click="add_interprete"
										>
											<a-icon type="plus" />
											Agregar Intérprete
										</a-button>
									</a-form-model-item>
									<div class="section-title" style="margin-top: 30px">
										<h4>Gestión de Autores</h4>
									</div>
									<a-form-model-item
										v-for="(autor, index) in autoresProd"
										:key="autor.key"
										v-bind="index === 0 ? formItemLayout : {}"
									>
										<div class="ant-form-item-label">
											<label class="ant-form-item">Autor</label>
										</div>
										<a-select
											option-filter-prop="children"
											:filter-option="filter_option"
											show-search
											:disabled="disabled"
											mode="single"
											v-model="autor.value"
											:id="autor.key"
											class="autores-select"
											@focus="last_autor(autor)"
											@change="change_autor(autor)"
										>
											<a-select-option
												v-for="(_autor, i) in copia_autores"
												:key="i"
												:value="`${_autor.nombresAutr}`"
											>
												{{ _autor.nombresAutr }}
											</a-select-option>
										</a-select>
										<a-button
											v-if="autoresProd.length > 1"
											class="dynamic-delete-button"
											:disabled="autoresProd.length === 1"
											@click="remove_autor(autor)"
										>
											<small>
												<b style="vertical-align: top"> x </b>
											</small>
										</a-button>
									</a-form-model-item>
									<a-form-model-item v-bind="formItemLayout">
										<a-button
											:disabled="disabled"
											style="
												color: white;
												background-color: rgb(45, 171, 229) !important;
											"
											@click="add_autor"
										>
											<a-icon type="plus" />
											Agregar Autor
										</a-button>
									</a-form-model-item>
								</div>
							</a-col>
						</a-row>
						<div v-if="action_modal === 'crear'">
							<a-row>
								<div class="section-title">
									<h4>Datos Descripciones</h4>
								</div>
								<a-row>
									<a-col span="11">
										<a-form-model-item
											has-feedback
											label="Descripción en español del producto"
											prop="descripEspPro"
										>
											<a-input
												:disabled="disabled"
												style="width: 100%; height: 100px"
												v-model="product_modal.descripEspPro"
												type="textarea"
											/>
										</a-form-model-item>
									</a-col>
									<a-col span="11" style="float: right">
										<a-form-model-item
											has-feedback
											label="Descripción en inglés del producto"
											prop="descripIngPro"
										>
											<a-input
												:disabled="disabled"
												style="width: 100%; height: 100px"
												v-model="product_modal.descripIngPro"
												type="textarea"
											/>
										</a-form-model-item>
									</a-col>
								</a-row>
							</a-row>
							<div class="section-title">
								<h4>Datos Gestión</h4>
							</div>
							<a-row style="margin-bottom: 30px">
								<a-col span="12">
									<a-form-model-item
										has-feedback
										label="Estado de Digitalización"
										prop="estadodigProd"
									>
										<a-select
											option-filter-prop="children"
											:filter-option="filter_option"
											show-search
											:disabled="disabled"
											v-model="product_modal.estadodigProd"
										>
											<a-select-option
												v-for="nomenclator in estados"
												:key="nomenclator.id"
												:value="nomenclator.nombreTer"
											>
												{{ nomenclator.nombreTer }}
											</a-select-option>
										</a-select>
									</a-form-model-item>
								</a-col>
							</a-row>
						</div>
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
					</a-form-model>
				</a-tab-pane>
				<a-tab-pane key="3" :disabled="tab_3">
					<span slot="tab"> Multimedias </span>
					<h4>Multimedias</h4>
					<a-button
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
				<!-- Tab 5 -->
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
	name: 'modal_product_managment',
	props: ['action', 'product', 'products_list'],
	data() {
		let validate_lentgh = (rule, value, callback) => {
			if (value.replace(/ /g, '').length > 8) {
				callback(new Error('Máximo 8 caracteres'));
			} else callback();
		};
		let validate_cod_bar = (rule, value, callback) => {
			if (value.replace(/ /g, '').length > 13) {
				callback(new Error('Máximo 13 caracteres'));
			} else callback();
		};
		let validate_codig_unique = (rule, value, callback) => {
			/* this.show_error = "validating"; */
			this.products_list.forEach((element) => {
				if (element.codigProd === value.replace(/ /g, '')) {
					/*  this.show_error = "error"; */
					callback(new Error('Código ya usado'));
				}
			});
			callback();
		};
		return {
			tab_visibility: true,
			roles_interp: [],
			disabled_option: false,
			list_compare_autores: '',
			list_compare_interpretes: '',
			valid_image: true,
			used: false,
			show_error: '',
			show_used_error: '',
			text_header_button: '',
			producPrincProd: '',
			catalDigitalPro: '',
			activoCatalbisPro: '',
			variosInterpretesProd: '',
			primeraPantProd: '',
			product_image: '',
			autoresProd: [],
			interpretesProd: [],
			list_autores: [],
			copia_autores: [],
			list_interpretes: [],
			copia_interpretes: [],
			pivot: '',
			spinning: false, //*
			activated: true,
			anhos: [],
			estados: [],
			status: [],
			generos: [],
			destinos: [],
			sellos: [],
			disabled: false,
			list_nomenclators: [],
			text_button: '', //* variable que muestra el texto del input submit del formulario en dependencia de la acción que sea(editar o crear).
			action_modal: this.action, //* variable que define la acción que se va a realizar, editar o crear.
			preview_visible: false, //* variable usada para previsualizar la imagen subida a través de un modal.
			preview_image: '', //* variable usada para saber si ya se ha subido una imagen.
			file_list: [], //* variable usada para guardar la imagen subida.
			loading: false, //* variable usada para alternar el ícono del componente uploat.
			product_modal: {}, //* variable que almacena el producto pasado por prop, con el cual se va a trabajar.
			projects: [], //*
			show: true, //* variable usada para mostrar u ocultar el modal.
			active_tab: '1', //* variable usada para almacenar el tab que esta activo.
			tabs_list: [], //* variable usada para almacenar los tabs por los que se ha pasado.
			waiting: false, //* variable usada para poner el ícono de cargando.
			tab_2: true, //*
			tab_3: true, //*
			tab_4: true, //*
			tab_5: true, //*
			varios_int: false,
			formItemLayout: {
				wrapperCol: {
					xs: { span: 24, offset: 0 },
					sm: { span: 20, offset: 4 },
				},
			},
			rules: {
				//*
				proyecto_id: [
					{
						required: true,
						message: 'Seleccione el código',
						trigger: 'change',
					},
				],
				descripEspPro: [
					{
						whitespace: true,
						message: 'Inserte una descripción',
						trigger: 'change',
					},
					{
						pattern: '^[ a-zA-Z0-9üáéíóúÁÉÍÓÚñÑ,.;:]*$',
						message: 'Caracter no válido',
						trigger: 'change',
					},
				],
				descripIngPro: [
					{
						whitespace: true,
						message: 'Inserte una descripción',
						trigger: 'change',
					},
					{
						pattern: "^[ a-zA-Z0-9',.;:]*$",
						message: 'Caracter no válido',
						trigger: 'change',
					},
				],
				nombreProy: [
					{
						required: true,
						message: 'Seleccione el nombre',
						trigger: 'change',
					},
				],
				codigProd: [
					{
						required: true,
						message: 'Inserte el código',
						trigger: 'change',
					},

					{
						whitespace: true,
						message: 'Inserte el código',
						trigger: 'change',
					},
					{
						pattern: '^[ a-zA-Z0-9]*$',
						message: 'Caracter no válido',
						trigger: 'change',
					},
					{
						validator: validate_codig_unique,
						trigger: 'change',
					},
					{
						validator: validate_lentgh,
						trigger: 'change',
					},
				],
				tituloProd: [
					{
						required: true,
						message: 'Inserte el titulo',
						trigger: 'change',
					},
					{
						pattern: '^[ a-zA-Z0-9]*$',
						message: 'Caracter no válido',
						trigger: 'change',
					},
					{
						whitespace: true,
						message: 'Inserte el titulo',
						trigger: 'change',
					},
				],
				añoProd: [
					{
						required: true,
						message: 'Seleccione el año',
						trigger: 'change',
					},
				],
				sellodiscProd: [
					{
						trigger: 'change',
					},
				],
				estadodigProd: [
					{
						trigger: 'change',
					},
				],
				statusComProd: [
					{
						trigger: 'change',
					},
				],
				genMusicPro: [
					{
						trigger: 'change',
					},
				],
				catalDigitalPro: [
					{
						trigger: 'change',
					},
				],
				value: [
					{
						trigger: 'change',
					},
				],
				codigBarProd: [
					{
						validator: validate_cod_bar,
						trigger: 'change',
					},
					{
						whitespace: true,
						message: 'Inserte el código',
						trigger: 'change',
					},
					{
						pattern: '^[ a-zA-Z0-9]*$',
						message: 'Caracter no válido',
						trigger: 'change',
					},
				],
			},
		};
	},
	created() {
		//* Carga de proyectos
		axios
			.post('/proyectos/listar')
			.then((response) => {
				let proy = response.data;
				proy.forEach((element) => {
					if (!element.deleted_at) {
						this.projects.push(element);
					}
				});
			})
			.catch((error) => {});
		//* Carga de autores
		axios
			.post('/autores/listar')
			.then((response) => {
				this.list_autores = response.data;
				this.copia_autores = this.clone_arr(this.list_autores);
			})
			.catch((error) => {});
		//* Carga de interpretes
		axios
			.post('/interpretes/listar')
			.then((response) => {
				this.list_interpretes = response.data;
				this.copia_interpretes = this.clone_arr(this.list_interpretes);
			})
			.catch((error) => {});

		//* Carga de nomencladores
		axios
			.post('/productos/nomencladores')
			.then((response) => {
				this.list_nomenclators = response.data;
				this.anhos = this.list_nomenclators[0][0];
				this.generos = this.list_nomenclators[1][0];
				this.estados = this.list_nomenclators[2][0];
				this.status = this.list_nomenclators[3][0];
				this.destinos = this.list_nomenclators[4][0];
				this.sellos = this.list_nomenclators[5][0];
				this.roles_interp = this.list_nomenclators[6][0];
			})
			.catch((error) => {});
		if (this.product.modal_detalles) {
			this.tab_visibility = false;
			this.tab_2 = false;
			this.active_tab = '2';
		}
		if (this.action_modal === 'editar') {
			this.list_compare_autores =
				this.product.autoresProd === null ? [] : this.product.autoresProd;
			this.list_compare_interpretes =
				this.product.interpretesProd === null
					? []
					: this.product.interpretesProd;
			this.product.descripEspPro =
				this.product.descripEspPro === null ? '' : this.product.descripEspPro;
			this.product.descripIngPro =
				this.product.descripIngPro === null ? '' : this.product.descripIngPro;
			if (this.product.deleted_at !== null) {
				this.disabled = true;
				this.activated = false;
			}
			this.product_modal = { ...this.product };
			if (this.product_modal.identificadorProd !== null) {
				if (
					this.product_modal.identificadorProd !==
					'/BisMusic/Proyectos/logo Vertical Full Color de Bismusic_Logo Vertical full color.png'
				) {
					this.file_list.push({
						uid: this.product_modal.id,
						name: this.product_modal.identificadorProd.split('/')[
							this.product_modal.identificadorProd.split('/').length - 1
						],
						url: this.product_modal.identificadorProd,
					});
				} else
					this.file_list.push({
						uid: 1,
						name:
							'logo Vertical Full Color de Bismusic_Logo Vertical full color.png',
						url:
							'/BisMusic/Proyectos/logo Vertical Full Color de Bismusic_Logo Vertical full color.png',
					});
			}
			this.varios_int = this.product_modal.variosInterpretesProd;
			this.producPrincProd =
				this.product_modal.producPrincProd === 0 ? false : true;
			this.primeraPantProd =
				this.product_modal.primeraPantProd === 0 ? false : true;
			this.activoCatalbisPro =
				this.product_modal.activoCatalbisPro === 0 ? false : true;
			this.catalDigitalPro =
				this.product_modal.catalDigitalPro === 0 ? false : true;
			this.variosInterpretesProd =
				this.product_modal.variosInterpretesProd === 0 ? false : true;
			this.text_button = 'Editar';
			this.text_header_button = 'Editar';
			let list_help = [];
			let list_roles = [];
			let list_roles_help = [];
			if (this.product_modal.interpretesProd !== null) {
				list_help = this.product_modal.interpretesProd.split('.');
				this.product_modal.interpretesProd = list_help[0];
				list_roles = list_help[1];
				if (list_roles !== undefined) {
					list_roles = list_roles.split('|');
					list_roles.forEach((element) => {
						list_roles_help.push(element.split(','));
					});
				}
				this.product_modal.interpretesProd = this.product_modal.interpretesProd.split(
					' '
				);
				if (list_roles !== undefined) {
					this.product_modal.interpretesProd.pop();
				}
				let roles = [];
				for (let i = 0; i < this.product_modal.interpretesProd.length; i++) {
					if (list_roles_help.length !== 0) {
						list_roles_help[i].forEach((element) => {
							if (element === '') {
								roles = [];
							} else roles = list_roles_help[i];
							return roles;
						});
					}
					this.interpretesProd.push({
						id: this.product_modal.interpretesProd[i].id,
						key: i + 1,
						role: roles,
						value: this.product_modal.interpretesProd[i],
					});
				}
				this.product_modal.interpretesProd = [];
			} else {
				this.product_modal.interpretesProd = [];
				this.interpretesProd = [
					{
						value: '',
						role: [],
						key: 2,
					},
				];
			}
			if (this.product_modal.autoresProd !== null) {
				this.product_modal.autoresProd = this.product_modal.autoresProd.split(
					' '
				);
				for (let i = 0; i < this.product_modal.autoresProd.length; i++) {
					this.autoresProd.push({
						id: this.product_modal.autoresProd[i].id, //esto está mal
						key: i - 1,
						value: this.product_modal.autoresProd[i],
					});
				}
				this.product_modal.autoresProd = [];
			} else {
				this.product_modal.autoresProd = [];
				this.autoresProd = [
					{
						value: '',
						key: 1,
					},
				];
			}
			if (this.product_modal.destinosComerPro !== null) {
				this.product_modal.destinosComerPro = this.product_modal.destinosComerPro.split(
					' '
				);
			} else delete this.product_modal.destinosComerPro;
		} else {
			this.text_button = 'Crear';
			this.text_header_button = 'Crear';
			this.product_modal = {};
			this.producPrincProd = false;
			this.activoCatalbisPro = false;
			this.catalDigitalPro = false;
			this.primeraPantProd = false;
			this.variosInterpretesProd = false;
			this.product_modal.autoresProd = [];
			this.product_modal.interpretesProd = [];
			this.file_list.push({
				uid: 1,
				name:
					'logo Vertical Full Color de Bismusic_Logo Vertical full color.png',
				url:
					'/BisMusic/Proyectos/logo Vertical Full Color de Bismusic_Logo Vertical full color.png',
			});
			this.autoresProd = [
				{
					id: -1,
					value: '',
					key: 1,
				},
			];
			this.interpretesProd = [
				{
					id: -1,
					value: '',
					role: [],
					key: 2,
				},
			];
		}
	},
	computed: {
		activar() {
			if (this.text_button === 'Editar') {
				return (
					!this.compare_object ||
					(this.valid_image && this.file_list.length !== 0)
				);
			} else
				return (
					this.product_modal.codigProd &&
					this.product_modal.añoProd &&
					this.product_modal.tituloProd &&
					this.valid_image
				);
		},
		/*
		 *Método que compara los campos editables del producto para saber si se ha modificado
		 */
		compare_object() {
			return (
				this.product_modal.tituloProd === this.product.tituloProd &&
				this.product_modal.añoProd === this.product.añoProd &&
				this.product_modal.descripEspPro === this.product.descripEspPro &&
				this.product_modal.descripIngPro === this.product.descripIngPro &&
				this.product_modal.producPrincProd === this.product.producPrincProd &&
				this.product_modal.codigBarProd === this.product.codigBarProd &&
				this.product_modal.destinosComerPro === undefined &&
				this.product_modal.statusComProd === this.product.statusComProd &&
				this.product_modal.sellodiscProd === this.product.sellodiscProd &&
				this.product_modal.genMusicPro === this.product.genMusicPro &&
				this.product_modal.activoCatalbisPro ===
					this.product.activoCatalbisPro &&
				this.product_modal.catalDigitalPro === this.product.catalDigitalPro &&
				this.product_modal.primeraPantProd === this.product.primeraPantProd &&
				this.product_modal.estadodigProd === this.product.estadodigProd &&
				this.product_modal.autoresProd === this.list_compare_autores &&
				this.product_modal.interpretesProd === this.list_compare_interpretes
			);
		},
	},
	methods: {
		validate() {
			if (!this.used) {
				this.$refs.formularioGenerales.validate((valid, obj) => {
					if (valid) {
						if (this.file_list.length !== 0) {
							return this.confirm();
						}
					}
					/*  let arr = [];
						Object.keys(obj).forEach(function (key) {
							arr.push(key);
						});
						if (arr.length === 1) {
							return this.confirm();
						} */
				});
			}
		},
		interpretes() {
			this.varios_int = !this.varios_int;
			if (this.varios_int) {
				this.multiplicidad = 'multiple';
			} else {
				this.multiplicidad = 'single';
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
					'Sólo puedes subir imágenes como identificador del proyecto'
				);
			} else this.$message.success('Identificador cargado correctamente');
			return false;
		},
		siguiente(tab, siguienteTab) {
			if (tab == 'tab_1') {
				if (this.action_modal === 'editar') {
					for (let i = 0; i < this.autoresProd.length; i++) {
						this.autoresProd[i].id = this.find_element_autor(
							this.autoresProd[i].value,
							this.list_autores
						);
						this.edit_copia_autores(this.autoresProd[i].id);
					}
					for (let i = 0; i < this.interpretesProd.length; i++) {
						this.interpretesProd[i].id = this.find_element_interprete(
							this.interpretesProd[i].value,
							this.list_interpretes
						);
						this.edit_copia_interpretes(this.interpretesProd[i].id);
					}
				}
				this.$refs.formularioproject.validate((valid) => {
					if (valid) {
						this.tab_2 = false;
						if (this.tabs_list.indexOf(tab) == -1) {
							this.tabs_list.push(tab);
						}
						this.active_tab = siguienteTab;
					}
				});
			} else if (tab == 'tab_2') {
				this.$refs.formularioGenerales.validate((valid) => {
					if (valid) {
						this.tab_3 = false;
						if (this.tabs_list.indexOf(tab) == -1) {
							this.tabs_list.push(tab);
						}
						this.active_tab = siguienteTab;
					}
				});
			} else if (tab == 'tab_3') {
				this.tab_4 = false;
				if (this.tabs_list.indexOf(tab) == -1) {
					this.tabs_list.push(tab);
				}
				this.active_tab = siguienteTab;
			}
		},
		atras(tabAnterior) {
			this.active_tab = tabAnterior;
		},
		confirmar() {
			this.product_modal.primeraPantProd =
				this.primeraPantProd === false ? 0 : 1;
			this.product_modal.producPrincProd =
				this.producPrincProd === false ? 0 : 1;
			this.product_modal.catalDigitalPro =
				this.catalDigitalPro === false ? 0 : 1;
			this.product_modal.activoCatalbisPro =
				this.activoCatalbisPro === false ? 0 : 1;
			this.product_modal.variosInterpretesProd =
				this.variosInterpretesProd === false ? 0 : 1;
			this.waiting = true;
			let destinos = '';
			let interpretes = '';
			let autores = '';
			let roles_interp = [];
			let roles = '';
			let interpretes_roles = '';
			if (this.action_modal === 'editar') {
				this.text_button = 'Editando...';
				if (this.product_modal.descripIngPro === undefined) {
					this.product_modal.descripIngPro = '';
				}
				if (this.product_modal.descripEspPro === undefined) {
					this.product_modal.descripEspPro = '';
				}
				if (this.product_modal.sellodiscProd === null) {
					this.product_modal.sellodiscProd = '';
				}
				if (this.product_modal.statusComProd === null) {
					this.product_modal.statusComProd = '';
				}
				if (this.product_modal.estadodigProd === null) {
					this.product_modal.estadodigProd = '';
				}
				if (this.product_modal.genMusicPro === null) {
					this.product_modal.genMusicPro = '';
				}
				if (this.product_modal.codigBarProd === null) {
					this.product_modal.codigBarProd = '';
				}
				if (this.product_modal.destinosComerPro === undefined) {
					this.product_modal.destinosComerPro = '';
				}
				this.autoresProd.forEach((item) => {
					this.product_modal.autoresProd.push(item.value);
				});
				this.product_modal.autoresProd.forEach((item) => {
					autores += item + ' ';
				});
				this.product_modal.autoresProd = autores;
				this.interpretesProd.forEach((item) => {
					if (item.value !== '') {
						this.product_modal.interpretesProd.push(item.value);
						if (item.role.length !== 0) {
							roles_interp.push(item.role);
						} else roles_interp.push('');
					}
				});
				if (roles_interp.length !== 0) {
					roles_interp.forEach((item) => {
						roles += item + '|';
					});
				}

				this.product_modal.interpretesProd.forEach((item) => {
					interpretes += item + ' ';
				});
				interpretes_roles =
					interpretes && roles
						? (interpretes_roles = interpretes + '.' + roles)
						: interpretes + roles;
				if (this.product_modal.destinosComerPro !== '') {
					this.product_modal.destinosComerPro.forEach((item) => {
						destinos += item + ' ';
					});
					this.product_modal.destinosComerPro = destinos;
				}
				let form_data = new FormData();
				form_data.append('codigProd', this.product_modal.codigProd);
				form_data.append('añoProd', this.product_modal.añoProd);
				form_data.append('descripEspPro', this.product_modal.descripEspPro);
				form_data.append('descripIngPro', this.product_modal.descripIngPro);
				form_data.append('tituloProd', this.product_modal.tituloProd);
				form_data.append('proyecto_id', this.product_modal.proyecto_id);
				form_data.append('id', this.product_modal.id);
				form_data.append('sellodiscProd', this.product_modal.sellodiscProd);
				form_data.append('estadodigProd', this.product_modal.estadodigProd);
				form_data.append('statusComProd', this.product_modal.statusComProd);
				form_data.append('producPrincProd', this.product_modal.producPrincProd);
				form_data.append('codigBarProd', this.product_modal.codigBarProd);
				form_data.append('genMusicPro', this.product_modal.genMusicPro);
				form_data.append('catalDigitalPro', this.product_modal.catalDigitalPro);
				form_data.append('destinosComerPro', destinos);
				form_data.append('autoresProd', autores);
				form_data.append('interpretesProd', interpretes_roles);
				form_data.append(
					'variosInterpretesProd',
					this.product_modal.variosInterpretesProd
				);
				form_data.append(
					'activoCatalbisPro',
					this.product_modal.activoCatalbisPro
				);
				form_data.append('primeraPantProd', this.product_modal.primeraPantProd);
				form_data.append(
					'dirArbolProd',
					`/BisMusic/Proyectos/Productos/${this.product_modal.nombreProy}`
				);
				if (this.file_list.length !== 0) {
					if (this.file_list[0].uid !== this.product_modal.id) {
						if (
							this.file_list[0].name !==
							'logo Vertical Full Color de Bismusic_Logo Vertical full color.png'
						) {
							form_data.append(
								'identificadorProd',
								this.file_list[0].originFileObj
							);
						}
					}
				} else form_data.append('img_default', true);
				axios
					.post(`/productos/editar`, form_data, {
						headers: {
							'Content-Type': 'multipart/form-data',
						},
					})
					.then((res) => {
						this.waiting = false;
						this.text_button = 'Editar';
						this.$emit('actualizar');
						this.$toast.success(
							'Se ha modificado el producto correctamente',
							'¡Éxito!',
							{ timeout: 2000 }
						);
						this.handle_cancel();
					})
					.catch((err) => {
						this.waiting = false;
						this.text_button = 'Editar';
						this.$toast.error('Ha ocurrido un error', '¡Error!', {
							timeout: 2000,
						});
					});
			} else {
				this.text_button = 'Creando...';
				if (this.product_modal.descripIngPro === undefined) {
					this.product_modal.descripIngPro = '';
				}
				if (this.product_modal.descripEspPro === undefined) {
					this.product_modal.descripEspPro = '';
				}
				if (this.product_modal.sellodiscProd === undefined) {
					this.product_modal.sellodiscProd = '';
				}
				if (this.product_modal.statusComProd === undefined) {
					this.product_modal.statusComProd = '';
				}
				if (this.product_modal.estadodigProd === undefined) {
					this.product_modal.estadodigProd = '';
				}
				if (this.product_modal.codigBarProd === undefined) {
					this.product_modal.codigBarProd = '';
				}
				if (this.product_modal.genMusicPro === undefined) {
					this.product_modal.genMusicPro = '';
				}
				if (this.product_modal.destinosComerPro === undefined) {
					this.product_modal.destinosComerPro = '';
				}
				this.autoresProd.forEach((item) => {
					this.product_modal.autoresProd.push(item.value);
				});

				this.interpretesProd.forEach((item) => {
					if (item.value !== '') {
						this.product_modal.interpretesProd.push(item.value);
						if (item.role !== undefined) {
							roles_interp.push(item.role);
						} else roles_interp.push('');
					}
				});
				roles_interp.forEach((item) => {
					roles += item + '|';
				});
				this.product_modal.interpretesProd.forEach((item) => {
					interpretes += item + ' ';
				});
				this.product_modal.autoresProd.forEach((item) => {
					autores += item + ' ';
				});
				if (this.product_modal.destinosComerPro !== '') {
					this.product_modal.destinosComerPro.forEach((item) => {
						destinos += item + ' ';
					});
				}
				if (interpretes === ' ') {
					roles = '';
					interpretes = '';
				} else if (roles === '|') {
					roles = '';
					interpretes_roles = interpretes;
				} else
					interpretes_roles =
						interpretes && roles
							? (interpretes_roles = interpretes + '.' + roles)
							: interpretes + roles;
				if (this.product.proyecto_id) {
					this.product_modal.proyecto_id = this.get_id_project(
						this.product.proyecto_id,
						this.product.nombre_proyecto
					);
					this.tab_visibility = false;
					this.tab_2 = false;
					this.active_tab = '2';
				}
				let form_data = new FormData();
				form_data.append('codigProd', this.product_modal.codigProd);
				form_data.append('añoProd', this.product_modal.añoProd);
				form_data.append('descripEspPro', this.product_modal.descripEspPro);
				form_data.append('descripIngPro', this.product_modal.descripIngPro);
				form_data.append('tituloProd', this.product_modal.tituloProd);
				form_data.append('proyecto_id', this.product_modal.proyecto_id);
				form_data.append('sellodiscProd', this.product_modal.sellodiscProd);
				form_data.append('estadodigProd', this.product_modal.estadodigProd);
				form_data.append('statusComProd', this.product_modal.statusComProd);
				form_data.append('producPrincProd', this.product_modal.producPrincProd);
				form_data.append('codigBarProd', this.product_modal.codigBarProd);
				form_data.append('genMusicPro', this.product_modal.genMusicPro);
				form_data.append('catalDigitalPro', this.product_modal.catalDigitalPro);
				form_data.append('destinosComerPro', destinos);
				form_data.append('autoresProd', autores);
				form_data.append('interpretesProd', interpretes_roles);
				form_data.append(
					'variosInterpretesProd',
					this.product_modal.variosInterpretesProd
				);
				form_data.append(
					'activoCatalbisPro',
					this.product_modal.activoCatalbisPro
				);
				form_data.append('primeraPantProd', this.product_modal.primeraPantProd);
				form_data.append(
					'dirArbolProd',
					`storage/BisMusic/Proyectos/Productos/${this.product_modal.nombreProy}`
				);
				if (this.file_list.length !== 0) {
					if (this.file_list[0].uid !== this.product_modal.id) {
						if (
							this.file_list[0].name !==
							'logo Vertical Full Color de Bismusic_Logo Vertical full color.png'
						) {
							form_data.append(
								'identificadorProd',
								this.file_list[0].originFileObj
							);
						}
					}
				} else form_data.append('img_default', true);
				axios
					.post('/productos', form_data, {
						headers: {
							'Content-Type': 'multipart/form-data',
						},
					})
					.then((res) => {
						this.waiting = false;
						this.text_button = 'Crear';
						this.$emit('actualizar');
						this.$toast.success(
							'Se ha creado el producto correctamente',
							'¡Éxito!',
							{ timeout: 2000 }
						);
						this.handle_cancel();
					})
					.catch((err) => {
						this.waiting = false;
						this.text_button = 'Crear';
						this.$toast.error('Ha ocurrido un error', '¡Error!', {
							timeout: 2000,
						});
					});
			}
		},
		get_id_project(id, name) {
			for (let index = 0; index < this.projects.length; index++) {
				if (
					this.projects[index].codigProy === id &&
					this.projects[index].nombreProy === name
				) {
					return this.projects[index].id;
				}
			}
		},
		/*
		 *Métodoo usado para filtrar la búsqueda del select de los años
		 */
		filter_option(input, option) {
			return (
				option.componentOptions.children[0].text
					.toLowerCase()
					.indexOf(input.toLowerCase()) >= 0
			);
		},
		handle_cancel(e) {
			if (!this.product.proyecto_id) {
				this.$refs.formularioproject.resetFields();
			}
			if (this.tabs_list.indexOf('tab_1') != -1) {
				this.$refs.formularioGenerales.resetFields();
			}
			this.tabs_list = [];
			this.active_tab = '1';
			this.tab_visibility = true;
			this.show = false;
			this.$emit('close_modal', this.show);
		},
		remove_autor(item) {
			let index = this.autoresProd.indexOf(item);
			let autor = this.autoresProd[index];
			if (autor.value !== '') {
				this.copia_autores.push(
					this.list_autores[
						this.find_element_autor(autor.value, this.list_autores)
					]
				);
			}
			if (index !== -1) {
				this.autoresProd.splice(index, 1);
			}
		},
		add_autor() {
			if (this.autoresProd.length < this.list_autores.length) {
				this.autoresProd.push({
					id: -1,
					value: '',
					key: Date.now(),
				});
			}
		},
		remove_interprete(item) {
			let index = this.interpretesProd.indexOf(item);
			let interprete = this.interpretesProd[index];
			if (interprete.value !== '') {
				this.copia_interpretes.push(
					this.list_interpretes[
						this.find_element_interprete(
							interprete.value,
							this.list_interpretes
						)
					]
				);
			}
			if (index !== -1) {
				this.interpretesProd.splice(index, 1);
			}
		},
		add_interprete() {
			if (this.interpretesProd.length < this.list_interpretes.length) {
				this.interpretesProd.push({
					id: -1,
					value: '',
					role: [],
					key: Date.now(),
				});
			}
		},
		cambiar_varios_interpretes() {
			this.varios_int = !this.varios_int;
			if (!this.varios_int) {
				this.interpretesProd.splice(1, this.interpretesProd.length - 1);
				this.copia_interpretes = this.clone_arr(this.list_interpretes);
				if (this.interpretesProd[0].value !== '') {
					this.copia_interpretes.splice(
						this.find_element_interprete(
							this.interpretesProd[0].value,
							this.copia_interpretes
						),
						1
					);
				}
			}
		},
		change_autor(item) {
			let index = this.autoresProd.indexOf(item);
			let autor = this.autoresProd[index];
			if (this.copia_autores.length === 1) {
				this.copia_autores.splice(0, this.copia_autores.length);
			} else {
				this.copia_autores.splice(
					this.find_element_autor(autor.value, this.copia_autores),
					1
				);
			}
			this.autoresProd[index].id = this.find_element_autor(
				autor.value,
				this.list_autores
			);
			if (this.pivot != '') {
				this.add_arr_elem_autor(this.pivot, this.copia_autores);
				this.pivot = '';
			}
			document.getElementById(autor.key).blur();
		},
		last_autor(item) {
			let index = this.autoresProd.indexOf(item);
			let autor = this.autoresProd[index];
			if (autor.id != -1) {
				this.pivot = autor.value;
			}
		},
		change_interprete(item) {
			let index = this.interpretesProd.indexOf(item);
			let interprete = this.interpretesProd[index];
			if (this.copia_interpretes.length === 1) {
				this.copia_interpretes.splice(0, this.copia_interpretes.length);
			} else {
				this.copia_interpretes.splice(
					this.find_element_interprete(
						interprete.value,
						this.copia_interpretes
					),
					1
				);
			}
			this.interpretesProd[index].id = this.find_element_interprete(
				interprete.value,
				this.list_interpretes
			);
			if (this.pivot != '') {
				this.add_arr_elem_interprete(this.pivot, this.copia_interpretes);
				this.pivot = '';
			}
			document.getElementById(interprete.key).blur();
		},
		last_interprete(item) {
			let index = this.interpretesProd.indexOf(item);
			let interprete = this.interpretesProd[index];
			if (interprete.id != -1) {
				this.pivot = interprete.value;
			}
		},
		find_element_autor(item, list) {
			for (let i = 0; i < list.length; i++) {
				if (list[i].nombresAutr === item) {
					return i;
				}
			}
			return -1;
		},
		add_arr_elem_autor(elem, arr) {
			let item = this.find_element_autor(elem, arr);
			if (item === -1) {
				arr.push(
					this.list_autores[
						this.find_element_autor(this.pivot, this.list_autores)
					]
				);
			}
		},
		find_element_interprete(item, list) {
			for (let i = 0; i < list.length; i++) {
				if (list[i].nombreInterp === item) {
					return i;
				}
			}
			return -1;
		},
		add_arr_elem_interprete(elem, arr) {
			let item = this.find_element_interprete(elem, arr);
			if (item === -1) {
				arr.push(
					this.list_interpretes[
						this.find_element_interprete(this.pivot, this.list_interpretes)
					]
				);
			}
		},
		clone_arr(arr) {
			let answer = [];
			for (let index = 0; index < arr.length; index++) {
				answer.push(arr[index]);
			}
			return answer;
		},
		edit_copia_autores(autor) {
			if (autor !== -1) {
				let index = this.find_element_autor(
					this.list_autores[autor].nombresAutr,
					this.copia_autores
				);
				this.copia_autores.splice(index, 1);
			}
		},
		edit_copia_interpretes(interprete) {
			if (interprete !== -1) {
				let index = this.find_element_interprete(
					this.list_interpretes[interprete].nombreInterp,
					this.copia_interpretes
				);
				this.copia_interpretes.splice(index, 1);
			}
		},
	},
};
</script>

<style>
#modal_gestionar_productos .ant-col-6 {
	width: 50% !important;
}

#modal_gestionar_productos .pull-left {
	float: left !important;
}

#modal_gestionar_productos .ant-upload-list-item,
.ant-upload-list-item-undefined,
.ant-upload-list-item-list-type-picture-card,
.ant-upload,
.ant-upload-select,
.ant-upload-select-picture-card,
.ant-upload-list-picture-card-container {
	width: 170px !important;
	height: 170px !important;
}

#modal_gestionar_productos .ant-upload,
.ant-upload-select,
.ant-upload-select-picture-card,
.ant-upload-list-picture-card-container {
	margin-left: 0 !important;
}

#modal_gestionar_productos .dynamic-delete-button {
	cursor: pointer;
	position: relative;
	top: 4px;
	font-size: 24px;
	color: #999;
	transition: all 0.3s;
}

#modal_gestionar_productos .dynamic-delete-button[disabled] {
	cursor: not-allowed;
	opacity: 0.5;
}

#upload .ant-upload,
.ant-upload-select,
.ant-upload-select-text {
	height: 0px !important;
}

#modal_gestionar_productos .add-field-button {
	width: 60% !important;
	background-color: rgb(46, 171, 229) !important;
	color: white !important;
}

#modal_gestionar_productos .ant-col-sm-offset-4 {
	margin-left: 0 !important;
}

#modal_gestionar_productos .ant-col-sm-20 {
	width: 100% !important;
}

#modal_gestionar_productos .autores-select {
	width: 85% !important;
	margin-right: 3px !important;
}

#modal_gestionar_productos .interpretes-select {
	width: 80% !important;
}

#modal_gestionar_productos .dynamic-delete-button {
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
</style>

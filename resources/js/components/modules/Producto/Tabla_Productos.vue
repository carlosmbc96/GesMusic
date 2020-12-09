<template>
	<div id="tabla_productos">
		<!-- Inicio Sección de Tabla de datos -->
		<div id="exportPanelContainer">
			<div id="arrowDropUpExports" v-if="visible_management">
				<a-tooltip :title="export_view ? 'Ocultar panel' : 'Mostrar Panel'"
					><span
						class="e-icons export-icons"
						:class="export_view ? 'e-down-arrow-export' : 'e-up-arrow-export'"
						@click="
							() => {
								export_view = !export_view;
							}
						"
					></span
				></a-tooltip>
				<span><a-icon class="e-icon-export" type="export"/></span>
			</div>
			<transition
				enter-active-class="animate__animated animate__slideInUp"
				leave-active-class="animate__animated animate__slideOutDown"
			>
				<div id="dropUpExports" v-if="export_view">
					<a-tooltip title="Imprimir"
						><span
							@click="panel_export_click('print')"
							class="e-icons export-icons e-print-export"
						></span
					></a-tooltip>
					<a-tooltip title="Exportar a PDF"
						><span
							@click="panel_export_click('pdf')"
							class="e-icons export-icons e-pdf-export"
						></span
					></a-tooltip>
					<a-tooltip title="Exportar a Excel"
						><span
							@click="panel_export_click('excel')"
							class="e-icons export-icons e-excel-export"
						></span
					></a-tooltip>
					<a-tooltip title="Exportar a CSV"
						><span
							@click="panel_export_click('csv')"
							class="e-icons export-icons e-csv-export"
						></span
					></a-tooltip>
				</div>
			</transition>
		</div>
		<div class="clearfix"></div>
		<!-- Tabla -->
		<ejs-grid
			id="datatable"
			ref="gridObj"
			:dataSource="products_list"
			locale="es-ES"
			:toolbar="toolbar"
			:toolbarClick="click_toolbar"
			:allowPaging="true"
			:pageSettings="page_settings"
			:allowFiltering="true"
			:filterSettings="filter_settings"
			:allowSelection="false"
			:allowTextWrap="true"
			:allowSorting="true"
			:pdfExportComplete="pdf_export_complete"
			:excelExportComplete="excel_export_complete"
			:queryCellInfo="customise_cell"
			:pdfQueryCellInfo="pdf_customise_cell"
			:excelQueryCellInfo="excel_customise_cell"
			:allowExcelExport="true"
			:allowPdfExport="true"
		>
			<e-columns>
				<e-column
					field="codigProd"
					headerText="Código"
					width="110"
					textAlign="Left"
				/>
				<e-column
					field="tituloProd"
					headerText="Título"
					width="110"
					textAlign="Left"
				/>

				<e-column
					field="añoProd"
					headerText="Año"
					width="95"
					textAlign="Left"
				/>
				<e-column
					field="estadodigProd"
					headerText="Estado de Digitalización"
					width="145"
					textAlign="Left"
				/>
				<e-column
					field="statusComProd"
					headerText="Estatus Comercial"
					width="125"
					textAlign="Left"
				/>
				<e-column
					field="genMusicPro"
					headerText="Género Musical"
					width="114"
					textAlign="Left"
				/>
				<e-column
					headerText="Estado"
					:template="status_template"
					width="115"
					textAlign="Center"
				/>
				<e-column
					headerText="Acciones"
					width="140"
					:commands="commands"
					:visible="true"
					textAlign="Center"
				/>
			</e-columns>
		</ejs-grid>
		<!-- Fin Sección de Tabla de datos -->

		<!-- Inicio Sección de Modals -->
		<modal_detail
			v-if="visible_details"
			:producto_prop="row_selected"
			@close_modal="visible_details = $event"
		/>
		<modal_management
			v-if="visible_management"
			:action="action_management"
			@actualizar="refresh_table"
			:product="row_selected"
			@close_modal="visible_management = $event"
			:products_list="all_products"
		/>
		<!-- Fin Sección de Modals -->
	</div>
</template>

<script>
/*
 *Importaciones
 */
import Vue from 'vue';
import axios from '../../../config/axios/axios';
import modal_detail from './Modal_Detalles_Producto';
import modal_management from './Modal_Gestionar_Producto';
import {
	GridPlugin,
	Edit,
	Filter,
	Group,
	Page,
	Selection,
	CommandColumn,
	Freeze,
	Sort,
	Toolbar,
	Reorder,
	PdfExport,
	ExcelExport,
	PdfExportProperties,
} from '@syncfusion/ej2-vue-grids';
import {
	ChartPlugin,
	ColumnSeries,
	Category,
	DataLabel,
	Tooltip,
	Legend,
} from '@syncfusion/ej2-vue-charts';
import { ButtonPlugin } from '@syncfusion/ej2-vue-buttons';
import { loadCldr, L10n, setCulture, Browser } from '@syncfusion/ej2-base';
import * as numberingSystems from 'cldr-data/supplemental/numberingSystems.json';
import * as weekData from 'cldr-data/supplemental/weekData.json';
import * as timeZoneNames from 'cldr-data/main/es/timeZoneNames.json';
import * as numbers from 'cldr-data/main/es/numbers.json';
import * as gregorian from 'cldr-data/main/es/ca-gregorian.json';
import { image } from '../../../../../public/assets/logo_base64';
Vue.use(GridPlugin);
Vue.use(ButtonPlugin);
Vue.use(ChartPlugin);
var moment = require('moment');
moment.locale('es');
/*
 *  Código para poner el lenguaje de los elementos de syncfusion en español
 */
loadCldr(numberingSystems, weekData, timeZoneNames, numbers, gregorian);
L10n.load({
	'es-ES': {
		grid: {
			EmptyRecord: 'No existen datos para mostrar',
			InvalidFilterMessage: 'Datos de filtrado errados',
			NoResult: 'Sin resultados',
			Search: 'Buscar',
			Matchs: 'Sin resultados',
			AND: 'Y',
			OR: 'O',
			StartsWith: 'Comienza con..',
			EndsWith: 'Termina con..',
			Contains: 'Contiene..',
			Equal: 'Igual a..',
			NotEqual: 'Diferente de..',
			LessThan: 'Menor que..',
			LessThanOrEqual: 'Menor o igual que..',
			GreaterThan: 'Mayor que..',
			GreaterThanOrEqual: 'Mayor o igual que..',
			ChooseDate: '..Fecha',
			EnterValue: '..Valor',
			FilterButton: 'Filtrar',
			ClearButton: 'Limpiar',
			SelectAll: 'Todo',
			Add: 'Añadir',
			Edit: 'Editar',
			Cancel: 'Cancelar',
			Update: 'Modificar',
			Delete: 'Eliminar',
			Item: 'elemento',
			Items: 'elementos',
		},
		pager: {
			currentPageInfo: '{0} de {1} páginas',
			totalItemInfo: '({0} elemento)',
			totalItemsInfo: '({0} elementos)',
			firstPageTooltip: 'Primera página',
			lastPageTooltip: 'Última página',
			nextPageTooltip: 'Página siguiente',
			previousPageTooltip: 'Página anterior',
			pagerDropDown: 'Elementos por página',
			pagerAllDropDown: 'Elementos',
			All: 'Todo',
		},
		calendar: {
			today: 'Hoy',
		},
	},
});
setCulture('es-ES');
/*
 *  Código para configurar el tema del gráfico
 */
let selected_theme = location.hash.split('/')[1];
selected_theme = selected_theme ? selected_theme : 'Material';
let theme = (
	selected_theme.charAt(0).toUpperCase() + selected_theme.slice(1)
).replace(/-dark/i, 'Dark');
export default {
	name: 'Proyecto_Index',
	props: ['proyecto', 'vista_editar'],
	data() {
		return {
			//* Variables de configuración de la tabla
			page_settings: {
				pageSizes: [5, 10, 20, 30],
				pageCount: 5,
				pageSize: 10,
			},
			filter_settings: { type: 'Menu' },
			commands: [
				{
					type: 'Detalles',
					buttonOption: {
						iconCss: 'e-icons e-eye-icon',
						click: this.detail_btn_click,
						cssClass: 'e-commands-custom',
					},
				},
				{
					type: 'Editar',
					buttonOption: {
						iconCss: 'e-icons e-edit-icon',
						click: this.edit_btn_click,
						cssClass: 'e-commands-custom',
					},
				},
			],
			toolbar: [
				{
					text: 'Añadir Producto',
					click: this.add_btn_click,
					tooltipText: 'Añadir Producto',
					prefixIcon: 'e-add-icon',
					id: 'add',
				},
				'Search',
			],
			status_template: () => {
				return {
					template: Vue.component('columnTemplate', {
						template: `
              <div>
                <a-popconfirm
                    placement="top"
                    @confirm="confirm_change_status"
                    ok-text="Si"
                    :visible="visible_pop"
                    @visibleChange="handle_visible_pop_change"
                >
                    <template slot="title">
                      <p style="margin-bottom: 0!important">Confirme que desea cambiar <br> el estado de este producto a
                      <strong v-if="!checked" style="color: rgb(76, 196, 177)">Activo</strong>
                      <strong v-else style="color: rgb(243, 107, 100)">Inactivo</strong>
                      </p>
                    </template>
                    <a-button
                      slot="cancelText"
                      class="ant-btn ant-btn-sm cancel-text"
                    >No</a-button>
                    <a-tooltip :title="project.deleted_at ? 'No es posible modificar estado, el proyecto al que está asociado este producto se encuentra inactivo' : 'Cambiar estado'" placement="left">
                      <a-switch id="switch" style="width: 100% !important" :style="color_status" :checked="checked" :loading="loading"
                      :disabled = "project.deleted_at">
                         <span slot="checkedChildren">Activo</span>
                         <span slot="unCheckedChildren">Inactivo</span>
                      </a-switch>
                    </a-tooltip>
                </a-popconfirm>
              </div>`,
						data: function(axios) {
							return {
								data: {},
								axios: axios,
								checked: false,
								loading: false,
								visible_pop: false,
								project: '',
							};
						},
						created() {
							this.checked = this.data.deleted_at === null;
							axios
								.post('proyectos/listar', {
									atributo: 'id',
									valorbuscado: this.data.proyecto_id,
								})
								.then((response) => {
									this.project = response.data;
								})
								.catch((error) => console.log(error));
							if (this.visible_management) {
								this.commands.push({
									type: 'Borrado Físico',
									buttonOption: {
										iconCss: 'e-icons e-delete-physical-icon',
										click: this.del_physical_btn_click,
										cssClass: 'e-commands-custom',
									},
								});
							}
						},
						computed: {
							color_status() {
								return !this.checked
									? 'background: rgb(243, 107, 100)!important'
									: 'background: rgb(76, 196, 177)!important; border-color: transparent!important';
							},
						},
						methods: {
							handle_visible_pop_change(visible) {
								if (this.project.deleted_at === null) this.visible_pop = false;
								else this.visible_pop = visible;
							},
							confirm_change_status() {
								this.loading = true;
								let error = false;
								if (this.checked) {
									axios
										.delete('productos/desactivar/ ' + this.data.id)
										.catch((errors) => {
											error = true;
										})
										.finally(() => {
											this.finally_method('Inactivo', error);
										});
								} else {
									axios
										.get('productos/restaurar/' + this.data.id)
										.catch((errors) => {
											error = true;
										})
										.finally(() => {
											this.finally_method('Activo', error);
										});
								}
							},
							finally_method(action, error) {
								this.loading = false;
								if (!error) {
									this.checked=!this.checked
									this.$toast.success(
										'Se ha cambiado el estado del Producto a ' + action,
										'¡Éxito!',
										{ timeout: 2000 }
									);
									this.$parent.$parent.load_products();
									this.$parent.$parent.$emit('reload');
								} else {
									this.$toast.error('Ha ocurrido un error', '¡Error!', {
										timeout: 2000,
									});
								}
							},
						},
					}),
				};
			},
			export_view: false, //* Vista del panel de exportaciones
			products_list: [], //* Lista de productos que es cargada en la tabla
			row_selected: {}, //* Fila de la tabla seleccionada | producto seleccionado
			visible_details: false, //* variable para visualizar el modal de detalles del producto
			visible_management: false, //* variable para visualizar el modal de gestión del producto
			action_management: '', //* variable contiene la acción a realizar en el modal de gestión | Insertar o Editar
			proyecto_id: '',
			nombre_proyecto: '',
			all_products: [],
		};
	},
	created() {
		this.load_products();
	},
	methods: {
		/*
		 * Método para modificar el estilo de las filas de la tabla
		 */
		customise_cell(args) {
			if (args.data['deleted_at'] != null) {
				args.cell.classList.add('inactive_row');
			}
		},
		/*
		 * Método para modificar el estilo de las filas de la tabla para el pdf
		 */
		pdf_customise_cell(args) {
			if (args.column.headerText == 'Estado') {
				if (args.data['deleted_at'] != null) {
					args.style = { backgroundColor: '#f36b64' };
					args.value = 'Incativo';
				} else {
					args.style = { backgroundColor: '#4cc4b1' };
					args.value = 'Activo';
				}
			}
			if (args.column.field == 'created_at') {
				args.value = moment(args.value).format('LLL');
			}
		},
		/*
		 * Método para modificar el estilo de las filas de la tabla para el excel
		 */
		excel_customise_cell(args) {
			if (args.column.headerText == 'Estado') {
				if (args.data['deleted_at'] != null) {
					args.style = { backColor: '#f36b64' };
					args.value = 'Incativo';
				} else {
					args.style = { backColor: '#4cc4b1' };
					args.value = 'Activo';
				}
			}
			if (args.column.field == 'created_at') {
				args.value = moment(args.value).format('ll');
			}
		},
		/*
		 * Método que carga los productos de la bd
		 */
		load_products() {
			this.$emit('reload');
			if (this.vista_editar) {
				axios
					.post('/productos/listar', {
						atributo: 'proyecto_id',
						valorbuscado: this.proyecto.id,
					})
					.then((response) => {
						this.products_list = response.data;
						this.series_data = [];
						this.products_list.forEach((product) => {
							let index = this.series_data.findIndex(
								(serie) => serie.years === parseInt(product.añoProd.toString())
							);
							if (index != -1) {
								this.series_data[index].products += 1;
							} else {
								this.series_data.push({
									years: parseInt(product.añoProd.toString()),
									products: 1,
								});
							}
						});
						this.series_data.sort((x, y) => {
							return x.years - y.years;
						});
						this.$refs.gridObj.refresh();
					});
				axios
					.post('/productos/listar', { relations: ['proyecto'] })
					.then((response) => {
						this.all_products = response.data;
					});
			} else {
				axios
					.post('/productos/listar', { relations: ['proyecto'] })
					.then((response) => {
						this.products_list = response.data;
						this.series_data = [];
						this.products_list.forEach((product) => {
							let index = this.series_data.findIndex(
								(serie) => serie.years === parseInt(product.añoProd.toString())
							);
							if (index != -1) {
								this.series_data[index].products += 1;
							} else {
								this.series_data.push({
									years: parseInt(product.añoProd.toString()),
									products: 1,
								});
							}
						});
						this.series_data.sort((x, y) => {
							return x.years - y.years;
						});
						if (
							this.series_data.length > 0 &&
							this.series_data[this.series_data.length - 1].products < 5
						) {
							this.primary_y_axis.interval = 5;
						}
						this.$refs.gridObj.refresh();
					});
			}
		},
		/*
		 * Método de configuración del gráfico
		 */
		load(args) {
			let selected_theme = location.hash.split('/')[1];
			selected_theme = selected_theme ? selected_theme : 'Material';
			if (selected_theme === 'highcontrast') {
				args.chart.series[0].marker.dataLabel.font.color = '#000000';
				args.chart.series[1].marker.dataLabel.font.color = '#000000';
				args.chart.series[2].marker.dataLabel.font.color = '#000000';
			}
		},
		/*
		 * Método que actualiza los datos de la tabla
		 */
		refresh_table() {
			this.load_products();
		},
		/*
		 * Método con la lógica de los botones del toolbar de la tabla
		 */
		click_toolbar(args) {
			if (args.item.id === 'add') {
				this.action_management = 'crear';
				this.visible_management = true;
				this.row_selected = {
					modal_detalles: true,
					proyecto_id: this.proyecto.codigProy,
					nombre_proyecto: this.proyecto.nombreProy,
				};
			}
		},
		panel_export_click(args) {
			let pdfExportProperties = {
				fileName: 'Reporte_Productos.pdf',
				pageOrientation: 'Landscape',
          pageSize: 'Note',
				header: {
					fromTop: 0,
					height: 120,
					contents: [
						{
							type: 'PageNumber',
							pageNumberType: 'Number',
							format: 'Página {$current} de {$total}', //optional
							position: { x: 0, y: 0 },
							style: {
								textBrushColor: '#a9a9a9',
								fontSize: 10,
								hAlign: 'Center',
								fontFamily: 'Calibri',
							},
						},
						{
							type: 'Text',
							value: 'Reporte de Productos',
							position: { x: 0, y: 40 },
							style: {
								textBrushColor: '#731954',
								fontSize: 20,
								fontFamily: 'Calibri',
							},
						},
						{
							type: 'Line',
							style: { penColor: '#731954', penSize: 1, dashStyle: 'Solid' },
							points: { x1: 0, y1: 70, x2: 435, y2: 70 },
						},
						{
							type: 'Text',
							value: 'Fecha del reporte: ' + new moment().format('LLL'),
							position: { x: 0, y: 75 },
							style: {
								textBrushColor: '#808080',
								fontSize: 12,
								fontFamily: 'Calibri',
							},
						},
						{
							type: 'Image',
							src: image,
							position: { x: 445, y: 0 },
							size: { height: 110, width: 250 },
						},
					],
				},
				theme: {
					header: {
						fontColor: '#731954',
						bold: true,
						borders: { color: '#731954', lineStyle: 'Thin' },
					},
				},
			};
			let excelExportProperties = {
				fileName: '',
				//   pageSize: 'Letter',
				header: {
					headerRows: 3,
					rows: [
						{
							cells: [
								{
									colSpan: 6,
									value: 'Reporte de Productos',
									style: {
										fontColor: '#731954',
										fontSize: 20,
										fontFamily: 'Calibri',
										hAlign: 'Center',
										bold: true,
									},
								},
							],
						},
						{
							cells: [
								{
									colSpan: 6,
									value: 'Fecha del reporte: ' + new moment().format('LLL'),
									style: {
										fontColor: '#808080',
										fontSize: 12,
										hAlign: 'Center',
										fontFamily: 'Calibri',
										bold: true,
									},
								},
							],
						},
					],
				},
				theme: {
					header: {
						fontColor: '#731954',
						bold: true,
					},
				},
			};
			if (args === 'pdf') {
				this.$refs.gridObj.getColumns()[4].visible = false;
				this.$refs.gridObj.pdfExport(pdfExportProperties);
			} else if (args === 'excel') {
				excelExportProperties.fileName = 'Reporte_Proyectos.xlsx';
				this.$refs.gridObj.getColumns()[4].visible = false;
				this.$refs.gridObj.excelExport(excelExportProperties);
			} else if (args === 'csv') {
				excelExportProperties.fileName = 'Reporte_Proyectos.csv';
				this.$refs.gridObj.getColumns()[4].visible = false;
				this.$refs.gridObj.csvExport(excelExportProperties);
			} else if (args === 'print') {
				this.$refs.gridObj.getColumns()[4].visible = false;
				this.$refs.gridObj.print(pdfExportProperties);
			}
		},
		/*
		 * Métodos para volver a mostrar las columnas 3 y 4 luego de exportar
		 */
		pdf_export_complete(args) {
			this.$refs.gridObj.getColumns()[4].visible = true;
		},
		excel_export_complete(args) {
			this.$refs.gridObj.getColumns()[4].visible = true;
		},
		/*
		 * Método con la lógica del botón borrado físico
		 */
		del_physical_btn_click(args) {
			let target = args.target;
			if (target.classList.contains('e-delete-physical-icon')) {
				target = target.parentElement;
			}
			let row_obj = this.$refs.gridObj.ej2Instances.getRowObjectFromUID(
				target.parentElement.parentElement.parentElement.getAttribute(
					'data-uid'
				)
			);
			this.$toast.question(
				'¿Seguro, esta acción es irrevercible, eliminará de forma definitiva este producto del sistema?',
				'Confirmar',
				{
					timeout: 5000,
					close: false,
					overlay: true,
					displayMode: 'once',
					id: 'question',
					zindex: 999999999,
					title: 'Hey',
					position: 'center',
					buttons: [
						[
							'<button><b>YES</b></button>',
							(instance, toast) => {
								axios
									.delete(`productos/eliminar/${row_obj.data.id}`)
									.then((ress) => {
										this.refresh_table();
										this.$emit('reload');
										this.$toast.success(
											'El producto ha sido eliminado correctamente',
											'¡Éxito!',
											{ timeout: 3000 }
										);
									})
									.catch((err) => {
										this.$toast.error('Ha ocurrido un error', '¡Error!', {
											timeout: 3000,
										});
									});
								instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
							},
							true,
						],
						[
							'<button>NO</button>',
							function(instance, toast) {
								instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
							},
						],
					],
				}
			);
		},
		/*
		 * Método con la lógica del botón detalles
		 */
		detail_btn_click(args) {
			var target = args.target;
			if (target.classList.contains('e-eye-icon')) {
				target = target.parentElement;
			}
			let row_obj = this.$refs.gridObj.ej2Instances.getRowObjectFromUID(
				target.parentElement.parentElement.parentElement.getAttribute(
					'data-uid'
				)
			);
			this.row_selected = row_obj.data;
			if (this.row_selected.deleted_at == null) this.visible_details = true;
		},
		/*
		 * Método con la lógica del botón editar
		 */
		edit_btn_click(args) {
			var target = args.target;
			if (target.classList.contains('e-edit-icon')) {
				target = target.parentElement;
			}
			let row_obj = this.$refs.gridObj.ej2Instances.getRowObjectFromUID(
				target.parentElement.parentElement.parentElement.getAttribute(
					'data-uid'
				)
			);
			this.row_selected = row_obj.data;
			this.row_selected.modal_detalles = true;
			if (this.row_selected.deleted_at == null) {
				this.action_management = 'editar';
				this.visible_management = true;
			}
		},
		add_btn_click(args) {
			this.proyecto_id = this.proyecto.codigProy;
			this.nombre_proyecto = this.proyecto.nombreProy;
		},
	},
	components: {
		modal_detail,
		modal_management,
	},
	provide: {
		grid: [
			Edit,
			Group,
			Filter,
			Page,
			Selection,
			CommandColumn,
			Freeze,
			Sort,
			Reorder,
			Toolbar,
			PdfExport,
			ExcelExport,
		],
		chart: [ColumnSeries, Category, Legend, DataLabel, Tooltip],
	},
};
</script>

<style>
#tabla_productos .e-headercontent,
#tabla_productos .e-sortfilter,
#tabla_productos thead,
#tabla_productos tr,
#tabla_productos td,
#tabla_productos th,
#tabla_productos .e-pagercontainer,
#tabla_productos .e-pagerdropdown,
#tabla_productos .e-first,
#tabla_productos .e-prev,
#tabla_productos .e-numericcontainer,
#tabla_productos .e-next,
#tabla_productos .e-last,
#tabla_productos .e-table,
#tabla_productos .e-input-group,
#tabla_productos .e-content,
#tabla_productos .e-toolbar-items,
#tabla_productos .e-tbar-btn,
#tabla_productos .e-toolbar-item,
#tabla_productos .e-gridheader,
#tabla_productos .e-gridcontent,
#tabla_productos .e-gridpager,
#tabla_productos .e-toolbar {
	background-color: transparent !important;
}
#tabla_productos .e-grid {
	background-color: rgba(255, 255, 255, 0.8) !important;
}
#tabla_productos .e-gridheader {
	border-bottom-color: rgba(115, 25, 84, 0.7) !important;
	border-top-color: transparent !important;
}
#tabla_productos td {
	border-color: lightgrey !important;
}
#tabla_productos .e-grid,
#tabla_productos .e-toolbar,
#tabla_productos .e-grid .e-headercontent {
	border-color: transparent !important;
}
#tabla_productos .e-row:hover {
	background-color: rgba(115, 25, 84, 0.1) !important;
}
#tabla_productos thead span,
#tabla_productos .e-icon-filter {
	color: rgb(115, 25, 84) !important;
	font-weight: bold !important;
}
#tabla_productos .ant-switch-inner {
	width: auto !important;
}
#tabla_productos span {
	display: initial !important;
}
</style>
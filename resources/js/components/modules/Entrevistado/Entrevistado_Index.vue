<template>
	<div>
		<h1>Entrevistados</h1>
		<button @click="create">Crear</button>
		<button @click="edit">Editar</button>
		<modal_management
			v-if="visible_management"
			:action="action_management"
			@close_modal="visible_management = $event"
		/>
	</div>
</template>

<script>
import modal_management from './Modal_Gestionar_Entrevistado';
export default {
	data() {
		return {
			visible_management: false,
			action_management: '',
		};
	},
	components: {
		modal_management,
	},
	methods: {
		create() {
			this.visible_management = true;
			this.action_management = 'crear';
		},
		edit() {
			this.visible_management = true;
			this.action_management = 'editar';
		},
	},
};
</script>

<style></style>

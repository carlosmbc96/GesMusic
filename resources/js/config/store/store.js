import moment from "moment";

export default {
  state: {
    all_tracks: [],
    all_tracks_statics: [],
    tracks: [],
		created_tracks: [],
		duration: moment().format("HH:mm:ss"),
		all_realizadores: [],
    all_realizadores_statics: [],
    realizadores: [],
		created_realizadores: [],
		all_entrevistados: [],
    all_entrevistados_statics: [],
    entrevistados: [],
		created_entrevistados: [],
		all_autores: [],
    all_autores_statics: [],
    autores: [],
		created_autores: [],
		all_interpretes: [],
    all_interpretes_statics: [],
    interpretes: [],
		created_interpretes: [],
		all_temas: [],
    all_temas_statics: [],
    temas: [],
		created_temas: [],
  },
  getters: {
    getTracksFormGetters(state) {
      return state.tracks;
    },
    getCreatedTracksFormGetters(state) {
      return state.created_tracks;
    },
    getAllTracksFormGetters(state) {
      return state.all_tracks;
    },
    getAllTracksStaticsFormGetters(state) {
      return state.all_tracks_statics;
    },
    getDurationFormGetters(state) {
      return state.duration;
		},
		getRealizadoresFormGetters(state) {
      return state.realizadores;
    },
    getCreatedRealizadoresFormGetters(state) {
      return state.created_realizadores;
    },
    getAllRealizadoresFormGetters(state) {
      return state.all_realizadores;
    },
    getAllRealizadoresStaticsFormGetters(state) {
      return state.all_realizadores_statics;
    },
		getEntrevistadosFormGetters(state) {
      return state.entrevistados;
    },
    getCreatedEntrevistadosFormGetters(state) {
      return state.created_entrevistados;
    },
    getAllEntrevistadosFormGetters(state) {
      return state.all_entrevistados;
    },
    getAllEntrevistadosStaticsFormGetters(state) {
      return state.all_entrevistados_statics;
    },
		getAutoresFormGetters(state) {
      return state.autores;
    },
    getCreatedAutoresFormGetters(state) {
      return state.created_autores;
    },
    getAllAutoresFormGetters(state) {
      return state.all_autores;
    },
    getAllAutoresStaticsFormGetters(state) {
      return state.all_autores_statics;
    },
		getInterpretesFormGetters(state) {
      return state.interpretes;
    },
    getCreatedInterpretesFormGetters(state) {
      return state.created_interpretes;
    },
    getAllInterpretesFormGetters(state) {
      return state.all_interpretes;
    },
    getAllInterpretesStaticsFormGetters(state) {
      return state.all_interpretes_statics;
    },
		getTemasFormGetters(state) {
      return state.temas;
    },
    getCreatedTemasFormGetters(state) {
      return state.created_temas;
    },
    getAllTemasFormGetters(state) {
      return state.all_temas;
    },
    getAllTemasStaticsFormGetters(state) {
      return state.all_temas_statics;
    },
  },
};

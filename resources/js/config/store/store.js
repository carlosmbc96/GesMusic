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
  },
};

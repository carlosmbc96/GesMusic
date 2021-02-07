import moment from "moment";

export default {
  state: {
    all_tracks: [],
    all_tracks_statics: [],
    tracks: [],
		created_tracks: [],
		duration: moment().format("HH:mm:ss"),
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
  },
};

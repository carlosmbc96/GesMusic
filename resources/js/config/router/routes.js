import proyecto_index from '../../components/modules/Proyecto/Proyecto_Index';
import producto_index from '../../components/modules/Producto/Producto_Index';
import autor_index from '../../components/modules/Artistas/Autor/Autor_Index';
import interprete_index from '../../components/modules/Artistas/Interprete/Interprete_Index';
import fonograma_index from '../../components/modules/Fonograma/Fonograma_Index';
import track_index from '../../components/modules/Track/Track_Index';
import audiovisual_index from '../../components/modules/Audiovisual/Audiovisual_Index.vue';
import artisticos_index from '../../components/modules/Artistico/Artisticos_Index.vue';

export const routes = [
	{ path: '/proyecto_index', component: proyecto_index },
	{ path: '/producto_index', component: producto_index },
	{ path: '/autor_index', component: autor_index },
	{ path: '/interprete_index', component: interprete_index },
	{ path: '/fonograma_index', component: fonograma_index },
	{ path: '/track_index', component: track_index },
	{ path: '/audiovisual_index', component: audiovisual_index },
	{ path: '/artisticos_index', component: artisticos_index },
];

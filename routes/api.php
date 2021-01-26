<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
	return $request->user();
});



// RUTAS DEL RECURSO: EMPRESA
//Route::resource('empresas','EmpresaController'); // Ruta que Lista todos los Recursos de la Clase Empresas
/* Route::get('/empresas','EmpresaController@index')->name('empresas.listar'); // Ruta que Lista todos los Registros del Modelo Empresa
Route::get('/empresas/create','EmpresaController@create')->name('empresas.crear'); // Ruta que Muestra un Formulario para Crear un Registro del Modelo Empresa
Route::post('/empresas','EmpresaController@store')->name('empresas.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Empresa
Route::get('/empresas/{id}','EmpresaController@show')->name('empresas.mostrar'); // Ruta que Muestra un Registro Específico del Modelo Empresa
Route::get('/empresas/{id}/edit','EmpresaController@edit')->name('empresas.editar'); // Ruta que Edita un Registro Específico del Modelo Empresa
Route::put('/empresas/{id}','EmpresaController@update')->name('empresas.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Empresa
Route::delete('/empresas/{id}','EmpresaController@destroyLog')->name('empresas.eliminar'); */ // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Empresa

// RUTAS DEL RECURSO: PROYECTO
Route::get('/proyectos/restaurar/{id}', 'ProyectoController@restoreLog');
Route::post('/proyectos/nomencladores', 'ProyectoController@nomenclators');
Route::post('/proyectos/listar', 'ProyectoController@index')->name('proyectos.listar'); // Ruta que Lista todos los Registros del Modelo Proyecto
Route::post('/proyectos', 'ProyectoController@store')->name('proyectos.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Proyecto
Route::post('/proyectos/editar', 'ProyectoController@update')->name('proyectos.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Proyecto
Route::delete('/proyectos/desactivar/{id}', 'ProyectoController@destroyLog')->name('proyectos.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Proyecto
Route::delete('/proyectos/eliminar/{id}', 'ProyectoController@destroyFis'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Proyecto


// RUTAS DEL RECURSO: PRODUCTO
Route::get('/productos/restaurar/{id}', 'ProductoController@restoreLog');
Route::post('/productos/listar', 'ProductoController@index')->name('productos.listar'); // Ruta que Lista todos los Registros del Modelo Producto
Route::post('/productos', 'ProductoController@store')->name('productos.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Producto
Route::post('/productos/editar', 'ProductoController@update')->name('productos.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Producto
Route::delete('/productos/desactivar/{id}', 'ProductoController@destroyLog')->name('productos.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto
Route::delete('/productos/eliminar/{id}', 'ProductoController@destroyFis'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto
Route::post('/productos/nomencladores', 'ProductoController@nomenclators');
Route::get('/productos/fonogramas/{id}', 'ProductoController@fonogramasRelation');


// RUTAS DEL RECURSO: FONOGRAMA
Route::get('/fonogramas/restaurar/{id}', 'FonogramaController@restoreLog');
Route::post('/fonogramas/listar', 'FonogramaController@index'); // Ruta que Lista todos los Registros del Modelo Producto
Route::post('/fonogramas', 'FonogramaController@store');
Route::post('/fonogramas/editar', 'FonogramaController@update'); // Ruta que Guarda un Registro Específico del Modelo Producto
Route::delete('/fonogramas/desactivar/{id}', 'FonogramaController@destroyLog'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto
Route::delete('/fonogramas/eliminar/{id}', 'FonogramaController@destroyFis'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto
Route::post('/fonogramas/nomencladores', 'FonogramaController@nomenclators');

// RUTAS DEL RECURSO: AUDIOVISUAL
Route::get('/audiovisuales/restaurar/{id}', 'AudiovisualController@restoreLog');
Route::post('/audiovisuales/listar', 'AudiovisualController@index');
Route::post('/audiovisuales', 'AudiovisualController@store');
Route::post('/audiovisuales/editar', 'AudiovisualController@update');
Route::delete('/audiovisuales/desactivar/{id}', 'AudiovisualController@destroyLog');
Route::delete('/audiovisuales/eliminar/{id}', 'AudiovisualController@destroyFis');
Route::post('/audiovisuales/nomencladores', 'AudiovisualController@nomenclators');

// RUTAS DEL RECURSO: ENTREVISTADO
//Route::resource('entrevistados','EntrevistadoController'); // Ruta que Lista todos los Recursos de la Clase Entrevistados
Route::get('/entrevistados/restaurar/{id}', 'EntrevistadoController@restoreLog');
Route::post('/entrevistados/listar', 'EntrevistadoController@index');
Route::post('/entrevistados', 'EntrevistadoController@store');
Route::post('/entrevistados/editar', 'EntrevistadoController@update');
Route::delete('/entrevistados/desactivar/{id}', 'EntrevistadoController@destroyLog');
Route::delete('/entrevistados/eliminar/{id}', 'EntrevistadoController@destroyFis');
Route::post('/entrevistados/nomencladores', 'EntrevistadoController@nomenclators');

// RUTAS DEL RECURSO: REALIZADOR
//Route::resource('realizadores','RealizadorController'); // Ruta que Lista todos los Recursos de la Clase Realizadores
Route::get('/realizadores/restaurar/{id}', 'RealizadorController@restoreLog');
Route::post('/realizadores/listar', 'RealizadorController@index');
Route::post('/realizadores', 'RealizadorController@store');
Route::post('/realizadores/editar', 'RealizadorController@update');
Route::delete('/realizadores/desactivar/{id}', 'RealizadorController@destroyLog');
Route::delete('/realizadores/eliminar/{id}', 'RealizadorController@destroyFis');
Route::post('/realizadores/nomencladores', 'RealizadorController@nomenclators');

// RUTAS DEL RECURSO: TRACK
Route::post('/tracks/listar', 'TrackController@index')->name('tracks.listar'); // Ruta que Lista todos los Registros del Modelo Track
Route::post('/tracks', 'TrackController@store')->name('tracks.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Track
Route::delete('/tracks/desactivar/{id}', 'TrackController@destroyLog')->name('tracks.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Track
Route::post('/tracks/nomencladores','TrackController@nomenclators');
Route::delete('/tracks/eliminar/{id}','TrackController@destroyFis');
Route::post('/tracks/editar','TrackController@update');
Route::get('/tracks/restaurar/{id}', 'TrackController@restoreLog');

// RUTAS DEL RECURSO: AUTOR
Route::get('/autores/restaurar/{id}', 'AutorController@restoreLog');
Route::post('/autores/listar', 'AutorController@index'); // Ruta que Lista todos los Registros del Modelo Producto
Route::post('/autores', 'AutorController@store'); // Ruta que Guarda el Nuevo Registro del Modelo Producto
Route::post('/autores/editar', 'AutorController@update'); // Ruta que Guarda un Registro Específico del Modelo Producto
Route::delete('/autores/desactivar/{id}', 'AutorController@destroyLog'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto
Route::delete('/autores/eliminar/{id}', 'AutorController@destroyFis'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto
Route::post('/autores/nomencladores', 'AutorController@nomenclators');
// RUTAS DEL RECURSO: INTERPRETE
Route::get('/interpretes/restaurar/{id}', 'InterpreteController@restoreLog');
Route::post('/interpretes/listar', 'InterpreteController@index'); // Ruta que Lista todos los Registros del Modelo Producto
Route::post('/interpretes', 'InterpreteController@store'); // Ruta que Guarda el Nuevo Registro del Modelo Producto
Route::post('/interpretes/editar', 'InterpreteController@update'); // Ruta que Guarda un Registro Específico del Modelo Producto
Route::delete('/interpretes/desactivar/{id}', 'InterpreteController@destroyLog'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto
Route::delete('/interpretes/eliminar/{id}', 'InterpreteController@destroyFis'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto

// RUTAS DEL RECURSO: ARTISTICO
Route::get('/artisticos/restaurar/{id}', 'ArtisticoController@restoreLog');
Route::post('/artisticos/listar', 'ArtisticoController@index'); // Ruta que Lista todos los Registros del Modelo Artistico
Route::post('/artisticos', 'ArtisticoController@store'); // Ruta que Guarda el Nuevo Registro del Modelo Artistico
Route::post('/artisticos/editar', 'ArtisticoController@update'); // Ruta que Guarda un Registro Específico del Modelo Artistico
Route::delete('/artisticos/desactivar/{id}', 'ArtisticoController@destroyLog'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto
Route::delete('/artisticos/eliminar/{id}', 'ArtisticoController@destroyFis'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto
/*
// RUTAS DEL RECURSO: TEMA
//Route::resource('temas','TemaController'); // Ruta que Lista todos los Recursos de la Clase Temas
Route::get('/temas','TemaController@index')->name('temas.listar'); // Ruta que Lista todos los Registros del Modelo Tema
Route::get('/temas/create','TemaController@create')->name('temas.crear'); // Ruta que Muestra un Formulario para Crear un Registro del Modelo Tema
Route::post('/temas','TemaController@store')->name('temas.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Tema
Route::get('/temas/{id}','TemaController@show')->name('temas.mostrar'); // Ruta que Muestra un Registro Específico del Modelo Tema
Route::get('/temas/{id}/edit','TemaController@edit')->name('temas.editar'); // Ruta que Edita un Registro Específico del Modelo Tema
Route::put('/temas/{id}','TemaController@update')->name('temas.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Tema
Route::delete('/temas/{id}','TemaController@destroyLog')->name('temas.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Tema

// RUTAS DEL RECURSO: ELEMENTO
//Route::resource('elementos','ElementoController'); // Ruta que Lista todos los Recursos de la Clase Elementos
Route::get('/elementos','ElementoController@index')->name('elementos.listar'); // Ruta que Lista todos los Registros del Modelo Elemento
Route::get('/elementos/create','ElementoController@create')->name('elementos.crear'); // Ruta que Muestra un Formulario para Crear un Registro del Modelo Elemento
Route::post('/elementos','ElementoController@store')->name('elementos.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Elemento
Route::get('/elementos/{id}','ElementoController@show')->name('elementos.mostrar'); // Ruta que Muestra un Registro Específico del Modelo Elemento
Route::get('/elementos/{id}/edit','ElementoController@edit')->name('elementos.editar'); // Ruta que Edita un Registro Específico del Modelo Elemento
Route::put('/elementos/{id}','ElementoController@update')->name('elementos.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Elemento
Route::delete('/elementos/{id}','ElementoController@destroyLog')->name('elementos.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Elemento

// RUTAS DEL RECURSO: EMPLEADO
//Route::resource('empleados','EmpleadoController'); // Ruta que Lista todos los Recursos de la Clase Empleados
Route::get('/empleados','EmpleadoController@index')->name('empleados.listar'); // Ruta que Lista todos los Registros del Modelo Empleado
Route::get('/empleados/create','EmpleadoController@create')->name('empleados.crear'); // Ruta que Muestra un Formulario para Crear un Registro del Modelo Empleado
Route::post('/empleados','EmpleadoController@store')->name('empleados.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Empleado
Route::get('/empleados/{id}','EmpleadoController@show')->name('empleados.mostrar'); // Ruta que Muestra un Registro Específico del Modelo Empleado
Route::get('/empleados/{id}/edit','EmpleadoController@edit')->name('empleados.editar'); // Ruta que Edita un Registro Específico del Modelo Empleado
Route::put('/empleados/{id}','EmpleadoController@update')->name('empleados.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Empleado
Route::delete('/empleados/{id}','EmpleadoController@destroyLog')->name('empleados.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Empleado

// RUTAS DEL RECURSO: USUARIO
//Route::resource('usuarios','UsuarioController'); // Ruta que Lista todos los Recursos de la Clase Usuarios
Route::get('/usuarios','UsuarioController@index')->name('usuarios.listar'); // Ruta que Lista todos los Registros del Modelo Usuario
Route::get('/usuarios/create','UsuarioController@create')->name('usuarios.crear'); // Ruta que Muestra un Formulario para Crear un Registro del Modelo Usuario
Route::post('/usuarios','UsuarioController@store')->name('usuarios.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Usuario
Route::get('/usuarios/{id}','UsuarioController@show')->name('usuarios.mostrar'); // Ruta que Muestra un Registro Específico del Modelo Usuario
Route::get('/usuarios/{id}/edit','UsuarioController@edit')->name('usuarios.editar'); // Ruta que Edita un Registro Específico del Modelo Usuario
Route::put('/usuarios/{id}','UsuarioController@update')->name('usuarios.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Usuario
Route::delete('/usuarios/{id}','UsuarioController@destroyLog')->name('usuarios.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Usuario

// RUTAS DEL RECURSO: TRAZA
//Route::resource('trazas','TrazaController'); // Ruta que Lista todos los Recursos de la Clase Trazas
Route::get('/trazas','TrazaController@index')->name('trazas.listar'); // Ruta que Lista todos los Registros del Modelo Traza
Route::get('/trazas/create','TrazaController@create')->name('trazas.crear'); // Ruta que Muestra un Formulario para Crear un Registro del Modelo Traza
Route::post('/trazas','TrazaController@store')->name('trazas.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Traza
Route::get('/trazas/{id}','TrazaController@show')->name('trazas.mostrar'); // Ruta que Muestra un Registro Específico del Modelo Traza
Route::get('/trazas/{id}/edit','TrazaController@edit')->name('trazas.editar'); // Ruta que Edita un Registro Específico del Modelo Traza
Route::put('/trazas/{id}','TrazaController@update')->name('trazas.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Traza
Route::delete('/trazas/{id}','TrazaController@destroyLog')->name('trazas.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Traza

// RUTAS DEL RECURSO: ROL
//Route::resource('roles','RolController'); // Ruta que Lista todos los Recursos de la Clase Roles
Route::get('/roles','RolController@index')->name('roles.listar'); // Ruta que Lista todos los Registros del Modelo Rol
Route::get('/roles/create','RolController@create')->name('roles.crear'); // Ruta que Muestra un Formulario para Crear un Registro del Modelo Rol
Route::post('/roles','RolController@store')->name('roles.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Rol
Route::get('/roles/{id}','RolController@show')->name('roles.mostrar'); // Ruta que Muestra un Registro Específico del Modelo Rol
Route::get('/roles/{id}/edit','RolController@edit')->name('roles.editar'); // Ruta que Edita un Registro Específico del Modelo Rol
Route::put('/roles/{id}','RolController@update')->name('roles.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Rol
Route::delete('/roles/{id}','RolController@destroyLog')->name('roles.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Rol

// RUTAS DEL RECURSO: PERMISO
//Route::resource('permisos','PermisoController'); // Ruta que Lista todos los Recursos de la Clase Permisos
Route::get('/permisos','PermisoController@index')->name('permisos.listar'); // Ruta que Lista todos los Registros del Modelo Permiso
Route::get('/permisos/create','PermisoController@create')->name('permisos.crear'); // Ruta que Muestra un Formulario para Crear un Registro del Modelo Permiso
Route::post('/permisos','PermisoController@store')->name('permisos.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Permiso
Route::get('/permisos/{id}','PermisoController@show')->name('permisos.mostrar'); // Ruta que Muestra un Registro Específico del Modelo Permiso
Route::get('/permisos/{id}/edit','PermisoController@edit')->name('permisos.editar'); // Ruta que Edita un Registro Específico del Modelo Permiso
Route::put('/permisos/{id}','PermisoController@update')->name('permisos.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Permiso
Route::delete('/permisos/{id}','PermisoController@destroyLog')->name('permisos.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Permiso

// RUTAS DEL RECURSO: VOCABULARIO
//Route::resource('vocabularios','VocabularioController'); // Ruta que Lista todos los Recursos de la Clase Vocabularios
Route::get('/vocabularios','VocabularioController@index')->name('vocabularios.listar'); // Ruta que Lista todos los Registros del Modelo Vocabulario
Route::get('/vocabularios/create','VocabularioController@create')->name('vocabularios.crear'); // Ruta que Muestra un Formulario para Crear un Registro del Modelo Vocabulario
Route::post('/vocabularios','VocabularioController@store')->name('vocabularios.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Vocabulario
Route::get('/vocabularios/{id}','VocabularioController@show')->name('vocabularios.mostrar'); // Ruta que Muestra un Registro Específico del Modelo Vocabulario
Route::get('/vocabularios/{id}/edit','VocabularioController@edit')->name('vocabularios.editar'); // Ruta que Edita un Registro Específico del Modelo Vocabulario
Route::put('/vocabularios/{id}','VocabularioController@update')->name('vocabularios.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Vocabulario
Route::delete('/vocabularios/{id}','VocabularioController@destroyLog')->name('vocabularios.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Vocabulario

// RUTAS DEL RECURSO: TERMINO
//Route::resource('terminos','TerminoController'); // Ruta que Lista todos los Recursos de la Clase Terminos
Route::get('/terminos','TerminoController@index')->name('terminos.listar'); // Ruta que Lista todos los Registros del Modelo Termino
Route::get('/terminos/create','TerminoController@create')->name('terminos.crear'); // Ruta que Muestra un Formulario para Crear un Registro del Modelo Termino
Route::post('/terminos','TerminoController@store')->name('terminos.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Termino
Route::get('/terminos/{id}','TerminoController@show')->name('terminos.mostrar'); // Ruta que Muestra un Registro Específico del Modelo Termino
Route::get('/terminos/{id}/edit','TerminoController@edit')->name('terminos.editar'); // Ruta que Edita un Registro Específico del Modelo Termino
Route::put('/terminos/{id}','TerminoController@update')->name('terminos.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Termino
Route::delete('/terminos/{id}','TerminoController@destroyLog')->name('terminos.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Termino

// RUTAS DEL RECURSO: PARAMETRO
//Route::resource('parametros','ParametroController'); // Ruta que Lista todos los Recursos de la Clase Parametros
Route::get('/parametros','ParametroController@index')->name('parametros.listar'); // Ruta que Lista todos los Registros del Modelo Parametro
Route::get('/parametros/create','ParametroController@create')->name('parametros.crear'); // Ruta que Muestra un Formulario para Crear un Registro del Modelo Parametro
Route::post('/parametros','ParametroController@store')->name('parametros.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Parametro
Route::get('/parametros/{id}','ParametroController@show')->name('parametros.mostrar'); // Ruta que Muestra un Registro Específico del Modelo Parametro
Route::get('/parametros/{id}/edit','ParametroController@edit')->name('parametros.editar'); // Ruta que Edita un Registro Específico del Modelo Parametro
Route::put('/parametros/{id}','ParametroController@update')->name('parametros.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Parametro
Route::delete('/parametros/{id}','ParametroController@destroyLog')->name('parametros.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Parametro
 */

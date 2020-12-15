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
Route::get('/proyectos/restaurar/{id}','ProyectoController@restoreLog');
Route::post('/proyectos/nomencladores','ProyectoController@nomenclators');
Route::post('/proyectos/listar','ProyectoController@index')->name('proyectos.listar'); // Ruta que Lista todos los Registros del Modelo Proyecto
Route::post('/proyectos','ProyectoController@store')->name('proyectos.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Proyecto
//Route::get('/proyectos/{id}','ProyectoController@show'); // Ruta que Muestra un Registro Específico del Modelo Proyecto
Route::post('/proyectos/editar','ProyectoController@update')->name('proyectos.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Proyecto
Route::delete('/proyectos/desactivar/{id}','ProyectoController@destroyLog')->name('proyectos.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Proyecto
Route::delete('/proyectos/eliminar/{id}','ProyectoController@destroyFis'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Proyecto


// RUTAS DEL RECURSO: PRODUCTO
Route::get('/productos/restaurar/{id}','ProductoController@restoreLog');
Route::post('/productos/listar','ProductoController@index')->name('productos.listar'); // Ruta que Lista todos los Registros del Modelo Producto
Route::post('/productos','ProductoController@store')->name('productos.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Producto
//Route::get('/productos/{id}','ProductoController@show')->name('productos.mostrar'); // Ruta que Muestra un Registro Específico del Modelo Producto
Route::post('/productos/editar','ProductoController@update')->name('productos.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Producto
Route::delete('/productos/desactivar/{id}','ProductoController@destroyLog')->name('productos.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto
Route::delete('/productos/eliminar/{id}','ProductoController@destroyFis'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto
Route::post('/productos/nomencladores','ProductoController@nomenclators');


// RUTAS DEL RECURSO: FONOGRAMA
Route::get('/fonogramas/restaurar/{id}','FonogramaController@restoreLog');
Route::post('/fonogramas/listar','FonogramaController@index'); // Ruta que Lista todos los Registros del Modelo Producto
Route::post('/fonogramas','FonogramaController@store');
//Route::post('/fonogramas/{id}','FonogramaController@show'); // Ruta que Muestra un Registro Específico del Modelo Producto
Route::post('/fonogramas/editar','FonogramaController@update'); // Ruta que Guarda un Registro Específico del Modelo Producto
Route::delete('/fonogramas/desactivar/{id}','FonogramaController@destroyLog'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto
Route::delete('/fonogramas/eliminar/{id}','FonogramaController@destroyFis'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto
Route::post('/fonogramas/nomencladores','FonogramaController@nomenclators');
/*
// RUTAS DEL RECURSO: AUDIOVISUAL
//Route::resource('audiovisuales','AudiovisualController'); // Ruta que Lista todos los Recursos de la Clase Audiovisuales
Route::get('/audiovisuales','AudiovisualController@index')->name('audiovisuales.listar'); // Ruta que Lista todos los Registros del Modelo Audiovisual
Route::get('/audiovisuales/create','AudiovisualController@create')->name('audiovisuales.crear'); // Ruta que Muestra un Formulario para Crear un Registro del Modelo Audiovisual
Route::post('/audiovisuales','AudiovisualController@store')->name('audiovisuales.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Audiovisual
Route::get('/audiovisuales/{id}','AudiovisualController@show')->name('audiovisuales.mostrar'); // Ruta que Muestra un Registro Específico del Modelo Audiovisual
Route::get('/audiovisuales/{id}/edit','AudiovisualController@edit')->name('audiovisuales.editar'); // Ruta que Edita un Registro Específico del Modelo Audiovisual
Route::put('/audiovisuales/{id}','AudiovisualController@update')->name('audiovisuales.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Audiovisual
Route::delete('/audiovisuales/{id}','AudiovisualController@destroyLog')->name('audiovisuales.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Audiovisual

// RUTAS DEL RECURSO: ENTREVISTADO
//Route::resource('entrevistados','EntrevistadoController'); // Ruta que Lista todos los Recursos de la Clase Entrevistados
Route::get('/entrevistados','EntrevistadoController@index')->name('entrevistados.listar'); // Ruta que Lista todos los Registros del Modelo Entrevistado
Route::get('/entrevistados/create','EntrevistadoController@create')->name('entrevistados.crear'); // Ruta que Muestra un Formulario para Crear un Registro del Modelo Entrevistado
Route::post('/entrevistados','EntrevistadoController@store')->name('entrevistados.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Entrevistado
Route::get('/entrevistados/{id}','EntrevistadoController@show')->name('entrevistados.mostrar'); // Ruta que Muestra un Registro Específico del Modelo Entrevistado
Route::get('/entrevistados/{id}/edit','EntrevistadoController@edit')->name('entrevistados.editar'); // Ruta que Edita un Registro Específico del Modelo Entrevistado
Route::put('/entrevistados/{id}','EntrevistadoController@update')->name('entrevistados.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Entrevistado
Route::delete('/entrevistados/{id}','EntrevistadoController@destroyLog')->name('entrevistados.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Entrevistado

// RUTAS DEL RECURSO: REALIZADOR
//Route::resource('realizadores','RealizadorController'); // Ruta que Lista todos los Recursos de la Clase Realizadores
Route::get('/realizadores','RealizadorController@index')->name('realizadores.listar'); // Ruta que Lista todos los Registros del Modelo Realizador
Route::get('/realizadores/create','RealizadorController@create')->name('realizadores.crear'); // Ruta que Muestra un Formulario para Crear un Registro del Modelo Realizador
Route::post('/realizadores','RealizadorController@store')->name('realizadores.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Realizador
Route::get('/realizadores/{id}','RealizadorController@show')->name('realizadores.mostrar'); // Ruta que Muestra un Registro Específico del Modelo Realizador
Route::get('/realizadores/{id}/edit','RealizadorController@edit')->name('realizadores.editar'); // Ruta que Edita un Registro Específico del Modelo Realizador
Route::put('/realizadores/{id}','RealizadorController@update')->name('realizadores.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Realizador
Route::delete('/realizadores/{id}','RealizadorController@destroyLog')->name('realizadores.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Realizador

// RUTAS DEL RECURSO: TRACK
//Route::resource('tracks','TrackController'); // Ruta que Lista todos los Recursos de la Clase Tracks
Route::get('/tracks','TrackController@index')->name('tracks.listar'); // Ruta que Lista todos los Registros del Modelo Track
Route::get('/tracks/create','TrackController@create')->name('tracks.crear'); // Ruta que Muestra un Formulario para Crear un Registro del Modelo Track
Route::post('/tracks','TrackController@store')->name('tracks.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Track
Route::get('/tracks/{id}','TrackController@show')->name('tracks.mostrar'); // Ruta que Muestra un Registro Específico del Modelo Track
Route::get('/tracks/{id}/edit','TrackController@edit')->name('tracks.editar'); // Ruta que Edita un Registro Específico del Modelo Track
Route::put('/tracks/{id}','TrackController@update')->name('tracks.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Track
Route::delete('/tracks/{id}','TrackController@destroyLog')->name('tracks.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Track
 */
// RUTAS DEL RECURSO: AUTOR
//Route::resource('autores','AutorController'); // Ruta que Lista todos los Recursos de la Clase Autores
Route::get('/autores/restaurar/{id}','AutorController@restoreLog');
Route::post('/autores/listar','AutorController@index'); // Ruta que Lista todos los Registros del Modelo Producto
Route::post('/autores','AutorController@store'); // Ruta que Guarda el Nuevo Registro del Modelo Producto
//Route::get('/autores/{id}','AutorController@show'); // Ruta que Muestra un Registro Específico del Modelo Producto
Route::post('/autores/editar','AutorController@update'); // Ruta que Guarda un Registro Específico del Modelo Producto
Route::delete('/autores/desactivar/{id}','AutorController@destroyLog'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto
Route::delete('/autores/eliminar/{id}','AutorController@destroyFis'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto
Route::post('/autores/nomencladores','AutorController@nomenclators'); 
// RUTAS DEL RECURSO: INTERPRETE
//Route::resource('interpretes','InterpreteController'); // Ruta que Lista todos los Recursos de la Clase Interpretes
Route::get('/interpretes/restaurar/{id}','InterpreteController@restoreLog');
Route::post('/interpretes/listar','InterpreteController@index'); // Ruta que Lista todos los Registros del Modelo Producto
Route::post('/interpretes','InterpreteController@store'); // Ruta que Guarda el Nuevo Registro del Modelo Producto
//Route::get('/interpretes/{id}','InterpreteController@show'); // Ruta que Muestra un Registro Específico del Modelo Producto
Route::post('/interpretes/editar','InterpreteController@update'); // Ruta que Guarda un Registro Específico del Modelo Producto
Route::delete('/interpretes/desactivar/{id}','InterpreteController@destroyLog'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto
Route::delete('/interpretes/eliminar/{id}','InterpreteController@destroyFis'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Producto
/*
// RUTAS DEL RECURSO: ARTISTICO
//Route::resource('artisticos','ArtisticoController'); // Ruta que Lista todos los Recursos de la Clase Artisticos
Route::get('/artisticos','ArtisticoController@index')->name('artisticos.listar'); // Ruta que Lista todos los Registros del Modelo Artistico
Route::get('/artisticos/create','ArtisticoController@create')->name('artisticos.crear'); // Ruta que Muestra un Formulario para Crear un Registro del Modelo Artistico
Route::post('/artisticos','ArtisticoController@store')->name('artisticos.guardar'); // Ruta que Guarda el Nuevo Registro del Modelo Artistico
Route::get('/artisticos/{id}','ArtisticoController@show')->name('artisticos.mostrar'); // Ruta que Muestra un Registro Específico del Modelo Artistico
Route::get('/artisticos/{id}/edit','ArtisticoController@edit')->name('artisticos.editar'); // Ruta que Edita un Registro Específico del Modelo Artistico
Route::put('/artisticos/{id}','ArtisticoController@update')->name('artisticos.actualizar'); // Ruta que Guarda un Registro Específico del Modelo Artistico
Route::delete('/artisticos/{id}','ArtisticoController@destroyLog')->name('artisticos.eliminar'); // Ruta que Elimina un Registro Específico de forma Lógica del Modelo Artistico

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
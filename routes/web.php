<?php
// routes/web.php - Definición de rutas de la aplicación

// ---------- PÚBLICO ----------
$router->get('/', [HomeController::class, 'index']);
$router->get('/acerca', [HomeController::class, 'about']);
$router->get('/servicios', [HomeController::class, 'servicios']);
$router->get('/consultar-radicado', [RadicadoController::class, 'index']);
$router->post('/consultar-radicado', [RadicadoController::class, 'consultar']);

// ---------- AUTENTICACIÓN ----------
$router->get('/login', [AuthController::class, 'showLogin'], ['guest']);
$router->post('/login', [AuthController::class, 'login'], ['guest']);
$router->get('/registro', [AuthController::class, 'showRegister'], ['guest']);
$router->post('/registro', [AuthController::class, 'register'], ['guest']);
$router->get('/recuperar', [AuthController::class, 'showForgot'], ['guest']);
$router->post('/recuperar', [AuthController::class, 'forgot'], ['guest']);
$router->post('/logout', [AuthController::class, 'logout'], ['auth']);
$router->get('/logout', [AuthController::class, 'logout'], ['auth']);

// ---------- CIUDADANO ----------
$router->get('/ciudadano/dashboard', [CiudadanoController::class, 'dashboard'], ['role:ciudadano']);
$router->get('/ciudadano/perfil', [CiudadanoController::class, 'perfil'], ['role:ciudadano']);
$router->post('/ciudadano/perfil', [CiudadanoController::class, 'actualizarPerfil'], ['role:ciudadano']);
$router->get('/ciudadano/notificaciones', [CiudadanoController::class, 'notificaciones'], ['role:ciudadano']);
$router->post('/ciudadano/notificaciones/leer', [CiudadanoController::class, 'marcarLeida'], ['role:ciudadano']);
$router->post('/ciudadano/notificaciones/leer-todas', [CiudadanoController::class, 'marcarTodas'], ['role:ciudadano']);

$router->get('/ciudadano/pqrs', [PqrsCiudadanoController::class, 'index'], ['role:ciudadano']);
$router->get('/ciudadano/pqrs/nueva', [PqrsCiudadanoController::class, 'crear'], ['role:ciudadano']);
$router->post('/ciudadano/pqrs/nueva', [PqrsCiudadanoController::class, 'guardar'], ['role:ciudadano']);
$router->get('/ciudadano/pqrs/detalle/{id}', [PqrsCiudadanoController::class, 'detalle'], ['role:ciudadano']);

$router->get('/ciudadano/tramites', [TramiteCiudadanoController::class, 'index'], ['role:ciudadano']);
$router->get('/ciudadano/tramites/mis-solicitudes', [TramiteCiudadanoController::class, 'misSolicitudes'], ['role:ciudadano']);
$router->get('/ciudadano/tramites/solicitar/{id}', [TramiteCiudadanoController::class, 'solicitar'], ['role:ciudadano']);
$router->post('/ciudadano/tramites/solicitar/{id}', [TramiteCiudadanoController::class, 'guardar'], ['role:ciudadano']);
$router->get('/ciudadano/tramites/detalle/{id}', [TramiteCiudadanoController::class, 'detalle'], ['role:ciudadano']);

// ---------- FUNCIONARIO ----------
$router->get('/funcionario/dashboard', [FuncionarioController::class, 'dashboard'], ['role:funcionario']);
$router->get('/funcionario/pqrs', [PqrsFuncionarioController::class, 'index'], ['role:funcionario']);
$router->get('/funcionario/pqrs/pendientes', [PqrsFuncionarioController::class, 'pendientes'], ['role:funcionario']);
$router->get('/funcionario/pqrs/gestionar/{id}', [PqrsFuncionarioController::class, 'gestionar'], ['role:funcionario']);
$router->post('/funcionario/pqrs/estado/{id}', [PqrsFuncionarioController::class, 'cambiarEstado'], ['role:funcionario']);
$router->post('/funcionario/pqrs/responder/{id}', [PqrsFuncionarioController::class, 'responder'], ['role:funcionario']);
$router->post('/funcionario/pqrs/seguimiento/{id}', [PqrsFuncionarioController::class, 'agregarSeguimiento'], ['role:funcionario']);

$router->get('/funcionario/tramites', [TramiteFuncionarioController::class, 'index'], ['role:funcionario']);
$router->get('/funcionario/tramites/gestionar/{id}', [TramiteFuncionarioController::class, 'gestionar'], ['role:funcionario']);
$router->post('/funcionario/tramites/estado/{id}', [TramiteFuncionarioController::class, 'cambiarEstado'], ['role:funcionario']);
$router->post('/funcionario/tramites/seguimiento/{id}', [TramiteFuncionarioController::class, 'agregarSeguimiento'], ['role:funcionario']);
$router->get('/funcionario/historial', [FuncionarioController::class, 'historial'], ['role:funcionario']);

// ---------- ADMINISTRACIÓN ----------
$router->get('/admin/dashboard', [AdminController::class, 'dashboard'], ['role:admin']);

$router->get('/admin/usuarios', [AdminController::class, 'usuarios'], ['role:admin']);
$router->get('/admin/usuarios/crear', [AdminController::class, 'crearUsuario'], ['role:admin']);
$router->post('/admin/usuarios/crear', [AdminController::class, 'guardarUsuario'], ['role:admin']);
$router->get('/admin/usuarios/editar/{id}', [AdminController::class, 'editarUsuario'], ['role:admin']);
$router->post('/admin/usuarios/editar/{id}', [AdminController::class, 'actualizarUsuario'], ['role:admin']);
$router->post('/admin/usuarios/estado/{id}', [AdminController::class, 'cambiarEstadoUsuario'], ['role:admin']);

$router->get('/admin/ciudadanos', [AdminController::class, 'ciudadanos'], ['role:admin']);
$router->get('/admin/funcionarios', [AdminController::class, 'funcionarios'], ['role:admin']);
$router->get('/admin/dependencias', [AdminController::class, 'dependencias'], ['role:admin']);
$router->post('/admin/dependencias/crear', [AdminController::class, 'guardarDependencia'], ['role:admin']);
$router->post('/admin/dependencias/estado/{id}', [AdminController::class, 'cambiarEstadoDependencia'], ['role:admin']);

$router->get('/admin/pqrs', [AdminPqrsController::class, 'index'], ['role:admin']);
$router->get('/admin/pqrs/detalle/{id}', [AdminPqrsController::class, 'detalle'], ['role:admin']);
$router->post('/admin/pqrs/asignar/{id}', [AdminPqrsController::class, 'asignar'], ['role:admin']);
$router->post('/admin/pqrs/estado/{id}', [AdminPqrsController::class, 'cambiarEstado'], ['role:admin']);
$router->post('/admin/pqrs/seguimiento/{id}', [AdminPqrsController::class, 'agregarSeguimiento'], ['role:admin']);

$router->get('/admin/tramites', [AdminTramiteController::class, 'tipos'], ['role:admin']);
$router->get('/admin/tramites/crear', [AdminTramiteController::class, 'crearTipo'], ['role:admin']);
$router->post('/admin/tramites/crear', [AdminTramiteController::class, 'guardarTipo'], ['role:admin']);
$router->get('/admin/tramites/editar/{id}', [AdminTramiteController::class, 'editarTipo'], ['role:admin']);
$router->post('/admin/tramites/editar/{id}', [AdminTramiteController::class, 'actualizarTipo'], ['role:admin']);
$router->post('/admin/tramites/estado/{id}', [AdminTramiteController::class, 'cambiarEstadoTipo'], ['role:admin']);
$router->get('/admin/tramites/solicitudes', [AdminTramiteController::class, 'solicitudes'], ['role:admin']);
$router->get('/admin/tramites/solicitudes/detalle/{id}', [AdminTramiteController::class, 'detalleSolicitud'], ['role:admin']);
$router->post('/admin/tramites/solicitudes/estado/{id}', [AdminTramiteController::class, 'cambiarEstadoSolicitud'], ['role:admin']);
$router->post('/admin/tramites/solicitudes/asignar/{id}', [AdminTramiteController::class, 'asignarSolicitud'], ['role:admin']);

$router->get('/admin/reportes', [ReporteController::class, 'index'], ['role:admin']);
$router->get('/admin/reportes/csv', [ReporteController::class, 'exportarCsv'], ['role:admin']);
$router->get('/admin/auditoria', [AdminController::class, 'auditoria'], ['role:admin']);
$router->get('/admin/configuracion', [AdminController::class, 'configuracion'], ['role:admin']);
$router->post('/admin/configuracion/password', [AdminController::class, 'cambiarPasswordAdmin'], ['role:admin']);

// ---------- ARCHIVOS PRIVADOS ----------
$router->get('/archivos/descargar/{id}', [ArchivoController::class, 'descargar'], ['auth']);

# Covenciones definición DB

- Todos los nombres de tablas y campos seran en snake_case

- Todos los nombres de tablas seran en plural

- Todos los nombres de tablas deberan comenzar por el modulo/entidad que representan

- Todos los nombres de campos seran en singular (Excepto tablas tipo json)

- Todas las tablas deben contener un identificador numerico autoincremental iniciando con el modulo/entidad que representa y con sufijo  _id. Ej: usuario_id, ventas_cancelaciones_detalles

- Todas las tablas de catalogos deben comenzar con el prefijo cat_

- Todas las tablas de relaciones deben comenzar con el prefijo rel_

- Todas las tablas de auditoria y administracion interna (como gestion de usuarios) deben comenzar con el prefijo sys_

- Todos los elementos marcados con * de la lista siguiente son esenciales al inicio de cada proyecto

### Lista guia de nombres de tablas:

alumnos
alumnos_actividades
alumnos_archivos
alumnos_paquetes
alumnos_paquetes_actividades
asistencias
bajas_temporales
cajas
cajas_incrementos_fondo
cajas_operacion
cat_almacenes
cat_alumno_bajas
cat_bajas_motivos
cat_bancos
cat_cursos
cat_cursos_niveles
cat_documentacion_alumnos
cat_estados
cat_folios_globales *
clases
clases_detalle
colaboradores_actividades
colaboradores_antiguedad
colaboradores_faltas
colaboradores_retardos
configuraciones_metadatos *
convenios_escolares
convenios_escolares_detalle
convenios_escolares_logs
deducciones_conceptos
deducciones_nomina
deducciones_nomina_detalles
facturas
facturas_detalle
facturas_globales_detalle
gastos_administrativos
gastos_administrativos_detalles
gastos_administrativos_pagos
grupos
grupos_horarios
grupos_logs
horarios_acceso
horarios_acceso_detalle
inventario_cambios
inventario_movimientos
inventario_recepciones
inventario_recepciones_detalle
inventario_salidas
inventario_salidas_detalle
liquidaciones
liquidaciones_detalle
niveles_objetivos
nomina
nomina_detalle
paquetes_clases
percepciones_conceptos
percepciones_nomina
personal_access_tokens
rel_alumnos_familiares
rel_alumnos_grupos
rel_alumnos_responsables
rel_cargos_descuentos
rel_colaboradores_clases_duracion
rel_colaboradores_sucursales
rel_cupones_descuento_sucursales
rel_perfiles_permisos
rel_usuarios_perfiles
rel_usuarios_sucursales
rel_vacaciones_detalle_clases
rel_ventas_facturas
rel_ventas_pagos
sat_formas_pago
sat_metodos_pago
sat_tipos_relacion
sat_unidades
sat_uso_cfdi
sucursales_datos_fiscales
suscripciones
suscripciones_actualizaciones
suscripciones_planes
sys_perfiles *
sys_permisos *
sys_usuarios *
vacaciones
vacaciones_detalle
ventas
ventas_cancelaciones
ventas_cancelaciones_detalles
ventas_detalle
ventas_pagos


### Estructura de tablas de auditoria

##### USUARIOS 

Column Name	#	Data Type	Not Null	Auto Increment	Key	Default	Extra	Expression	Comment
usuario_id	1	bigint unsigned	true	true	PRI	[NULL]	auto_increment		
usuario	2	varchar(20)	true	false	[NULL]	[NULL]			
password	3	varchar(128)	true	false	[NULL]	[NULL]			
pin	4	int	false	false	[NULL]	[NULL]			
nombre_corto	5	varchar(100)	true	false	[NULL]	[NULL]			
email	6	varchar(200)	false	false	[NULL]	[NULL]			
telefono	7	varchar(25)	false	false	[NULL]	[NULL]			
ultimo_acceso_fecha	8	timestamp	false	false	[NULL]	[NULL]			
acceso_aplicacion	9	tinyint(1)	true	false	[NULL]	0			
status	10	varchar(255)	true	false	[NULL]	'ACTIVO'			ACTIVO,ELIMINADO
super_usuario_mantenimiento	11	tinyint(1)	true	false	[NULL]	0			Columna que sirve para indicar el usuario de system que no se mostrará en el sistema
super_usuario	12	tinyint(1)	true	false	[NULL]	0			
registro_fecha	13	timestamp	false	false	[NULL]	[NULL]			
registro_autor_id	14	bigint unsigned	false	false	[NULL]	[NULL]			
actualizacion_fecha	15	timestamp	false	false	[NULL]	[NULL]			
actualizacion_autor_id	16	bigint unsigned	false	false	[NULL]	[NULL]			
colaborador_id	17	bigint unsigned	false	false	UNI	[NULL]	


##### PERFILES

Column Name	#	Data Type	Not Null	Auto Increment	Key	Default	Extra	Expression	Comment
perfil_id	1	bigint unsigned	true	true	PRI	[NULL]	auto_increment		
clave	2	varchar(20)	true	false	[NULL]	[NULL]			
titulo	3	varchar(45)	true	false	[NULL]	[NULL]			
descripcion	4	varchar(250)	false	false	[NULL]	[NULL]			
status	5	varchar(255)	false	false	[NULL]	'ACTIVO'			ACTIVO, ELIMINADO
acceso_aplicacion	6	tinyint(1)	false	false	[NULL]	0			
super_usuario	7	tinyint(1)	true	false	[NULL]	0			
registro_autor_id	8	bigint unsigned	true	false	[NULL]	[NULL]			
registro_fecha	9	timestamp	true	false	[NULL]	[NULL]			
actualizacion_autor_id	10	bigint unsigned	false	false	[NULL]	[NULL]			
actualizacion_fecha	11	timestamp	false	false	[NULL]	[NULL]		


##### PERMISOS

Column Name	#	Data Type	Not Null	Auto Increment	Key	Default	Extra	Expression	Comment
permiso_id	1	bigint unsigned	true	true	PRI	[NULL]	auto_increment		
codigo	2	varchar(150)	true	false	[NULL]	[NULL]			
titulo	3	varchar(75)	true	false	[NULL]	[NULL]			
descripcion	4	varchar(350)	true	false	[NULL]	[NULL]			
seccion	5	varchar(350)	true	false	[NULL]	[NULL]			
orden	6	decimal(5,2)	true	false	[NULL]	[NULL]		


##### FOLIOS GLOBALES

Column Name	#	Data Type	Not Null	Auto Increment	Key	Default	Extra	Expression	Comment
key	1	varchar(255)	true	false	[NULL]	[NULL]			
folio	2	bigint	true	false	[NULL]	[NULL]	


##### METADATOS

Column Name	#	Data Type	Not Null	Auto Increment	Key	Default	Extra	Expression	Comment
clave	1	varchar(50)	true	false	[NULL]	[NULL]			
descripcion	2	varchar(100)	true	false	[NULL]	[NULL]			
valor	3	json	true	false	[NULL]	[NULL]			



create table tblClientePuerta
(
    idregistro                      int auto_increment
        primary key,
    idusuario                       int                                      not null,
    idestacion                      varchar(20)                              not null,
    codigo                          varchar(100)                             not null,
    fecha_hora                      datetime   default '1900-01-01 00:00:00' not null,
    vigencia                        datetime   default '1900-01-01 00:00:00' not null,
    permanente                      tinyint(1)                               not null,
    idevento                        int        default 0                     not null,
    idhorario                       int        default 0                     not null,
    nombre                          varchar(300)                             not null,
    fbid                            varchar(30)                              not null,
    email                           varchar(300)                             not null,
    telefono                        varchar(12)                              not null,
    confirmado                      tinyint(1) default 0                     not null,
    nfc                             tinyint(1) default 0                     not null,
    bloqueado                       tinyint(1) default 0                     not null,
    fraccionamientos_pago_corriente date       default '1900-01-01'          not null
)
    charset = latin1;

create table tblDistribuidor
(
    iddistribuidor int auto_increment
        primary key,
    nombre         varchar(300) not null,
    telefono       varchar(13)  not null,
    whatsapp       varchar(13)  not null,
    email          varchar(100) not null,
    direccion      varchar(500) not null,
    direccion2     varchar(500) not null,
    ciudad_estado  varchar(500) not null,
    latlng         varchar(100) not null,
    comision       tinyint      not null
);

create table tblEstacion
(
    idestacion                     varchar(20)                              not null
        primary key,
    ubicacion                      varchar(100)                             not null,
    fecha_install                  date       default '1900-01-01'          not null,
    ping                           datetime   default '1900-01-01 00:00:00' not null,
    hardware                       varchar(100)                             not null,
    version                        int                                      not null,
    nombre_estacion                varchar(100)                             not null,
    nombre_puerta                  varchar(100)                             not null,
    tiempo_apertura                int                                      null,
    pin_apertura                   tinyint                                  null,
    status                         tinyint(1)                               null,
    imgurl                         json                                     null,
    idplanpago                     int                                      not null,
    reset_wifi                     tinyint(1) default 0                     not null,
    es_puerta                      tinyint(1) default 0                     not null,
    fraccionamientos_pago_prorroga int        default 0                     not null,
    check_status                   tinyint(1) default 0                     null,
    wifi_signal                    int        default 0                     null,
    wifi_ssid                      varchar(100)                             not null,
    wifi_pass                      varchar(100)                             not null
)
    charset = latin1;

create table tblEstacionAdmin
(
    idestacion varchar(20) not null,
    idusuario  int         not null,
    primary key (idestacion, idusuario)
);

create table tblEstacionDistribuidor
(
    idestacion     varchar(20) not null
        primary key,
    iddistribuidor int         not null
);

create table tblEvento
(
    idregistro int auto_increment
        primary key,
    idusuario  int      null,
    idestacion tinytext null,
    nombre     tinytext null,
    fecha      date     null,
    hora       time     null
);

create table tblHorarioPuerta
(
    idhorario  int auto_increment
        primary key,
    lunes      varchar(19) default '00:00:00,00:00:00' not null,
    martes     varchar(19) default '00:00:00,00:00:00' not null,
    miercoles  varchar(19) default '00:00:00,00:00:00' not null,
    jueves     varchar(19) default '00:00:00,00:00:00' not null,
    viernes    varchar(19) default '00:00:00,00:00:00' not null,
    sabado     varchar(19) default '00:00:00,00:00:00' not null,
    domingo    varchar(19) default '00:00:00,00:00:00' not null,
    idestacion varchar(20)                             not null
)
    charset = latin1;

create table tblLog
(
    idregistro int auto_increment
        primary key,
    idestacion varchar(30)                            not null,
    fecha      datetime default '1900-01-01 00:00:00' not null,
    log        varchar(300)                           not null
);

create table tblLogin
(
    idusuario      int auto_increment
        primary key,
    correo         varchar(100)                             not null,
    nombre         varchar(100)                             not null,
    pass           varchar(250)                             not null,
    session        varchar(250)                             not null,
    conectado      tinyint(1) default 0                     not null,
    cancelado      tinyint(1) default 0                     not null,
    pass_renew     tinyint(1) default 1                     not null,
    fecha_acceso   datetime   default CURRENT_TIMESTAMP     not null,
    whatsapp       varchar(15)                              not null,
    fbid           varchar(20)                              not null,
    strkey         varchar(32)                              not null,
    strkey_timeout datetime   default '1900-01-01 00:00:00' not null,
    distribuidor   int        default 0                     not null
)
    engine = MyISAM
    charset = latin1;

create table tblLoginRegistro
(
    idregistro      int auto_increment
        primary key,
    idusuario       int                       not null,
    fecha           date default '1900-01-01' not null,
    hora            time default '00:00:00'   not null,
    datos_navegador varchar(1000)             not null
);

create table tblNfcTag
(
    mac             varchar(20) not null
        primary key,
    idclientepuerta int         not null
);

create table tblPagos
(
    idpago         int auto_increment
        primary key,
    idestacion     varchar(20)                            not null,
    idusuario      int                                    not null,
    fecha          datetime default '1900-01-01 00:00:00' not null,
    monto          double(10, 2)                          not null,
    payment_status varchar(50)                            not null,
    paypal_json    json                                   not null
);

create table tblPagosFraccionamientos
(
    idregistro      int auto_increment
        primary key,
    idestacion      varchar(20)                       not null,
    idclientepuerta int                               not null,
    fecha_pago      date         default '1900-01-01' not null,
    monto_pago      double(6, 2) default 0.00         not null,
    confirmado_por  int          default 0            not null
);

create table tblPagosServicios
(
    idpagoservicio int auto_increment
        primary key,
    idestacion     mediumtext null,
    idusuario      int        null,
    visitas        int        null,
    empleados      int        null,
    fecha          timestamp  null,
    monto          double     null,
    payment_status mediumtext null,
    paypal_json    longtext   null
);

create table tblPingEstaciones
(
    idestacion  varchar(20)                              not null
        primary key,
    ping        datetime   default '1900-01-01 00:00:00' not null,
    version     int                                      not null,
    reset_wifi  tinyint(1) default 0                     not null,
    relay_check tinyint(1) default 0                     not null,
    status      tinyint(1) default 0                     not null,
    tiposmart   tinyint(1) default 1                     not null,
    wifi_signal int        default 0                     not null
)
    charset = latin1;

create table tblPlan
(
    idplan                int auto_increment
        primary key,
    idtipo                int                        not null,
    nombre                varchar(100)               not null,
    descripcion           varchar(255)               not null,
    periodo_pago          int                        not null,
    monto                 double(15, 2)              not null,
    usuarios              int                        not null,
    administradores       int                        not null,
    empleados             int                        not null,
    pago_x_usuario        tinyint(1)                 not null,
    visitas_habilitado    int           default 0    not null,
    empleados_por_usuario int           default 0    not null,
    costo_empleado        double(15, 2) default 0.00 not null,
    costo_visitas         double(15, 2) default 0.00 not null
);

create table tblRegistro
(
    idregistro   int auto_increment
        primary key,
    idestacion   varchar(20)                            not null,
    idusuario    int                                    not null,
    hora         datetime default '1900-01-01 00:00:00' not null,
    sync         tinyint(1)                             not null,
    ubicacion    varchar(100)                           not null,
    fecha_update datetime default '1900-01-01 00:00:00' not null
)
    charset = latin1;

create table tblRegistroCasa
(
    idregistro   int auto_increment
        primary key,
    idestacion   varchar(20)                            not null,
    fecha_inicio datetime default '1900-01-01 00:00:00' not null,
    fecha_fin    datetime default '1900-01-01 00:00:00' not null,
    nombre       varchar(255)                           not null,
    telefono     varchar(13)                            not null,
    email        varchar(200)                           not null
);

create table tblTipo
(
    idtipo int auto_increment
        primary key,
    nombre varchar(100) not null
);

create table tblTransaccion
(
    idregistro  int auto_increment
        primary key,
    idestacion  varchar(20)                            not null,
    descripcion varchar(500)                           not null,
    fecha       datetime default '1900-01-01 00:00:00' not null
);

create table tblUsuarioEmpleado
(
    idregistro      int auto_increment
        primary key,
    idusuario       int not null,
    idclientepuerta int not null
);

use smartdoor;
INSERT INTO tblPlan (idplan, idtipo, nombre, descripcion, periodo_pago, monto, usuarios, administradores, empleados, pago_x_usuario, visitas_habilitado, empleados_por_usuario, costo_empleado, costo_visitas) VALUES (1, 1, 'Plan demo', 'Demostracion de producto', 0, 0, 1000, 10, -1, 0, -1, 0, 0, 0);
INSERT INTO tblTipo (idtipo, nombre) VALUES (1, 'Demo');
INSERT INTO tblLogin (idusuario, correo, nombre, pass, session, conectado, cancelado, pass_renew, fecha_acceso, whatsapp, fbid, strkey, strkey_timeout, distribuidor) VALUES (1, 'admin', 'Admin', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', '', 0, 0, 0, '1900-01-01 00:00:00', '1234567890', '', '', '2024-10-10 22:41:23', 1);


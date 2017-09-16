-- datos semilla
-- estado
-- tab_solici_estado
insert into tab_solici_estado values(1,'Abierta','0','sistema',now(),null,null);
insert into tab_solici_estado values(2,'Proceso','10','sistema',now(),null,null);
insert into tab_solici_estado values(3,'Finalizado','100','sistema',now(),null,null);

-- tab_solici_tiposx
insert into tab_solici_tiposx values(0,'default','sistema',now(),null,null);
insert into tab_solici_tiposx values(1,'Creación de rutas','sistema',now(),null,null);
insert into tab_solici_tiposx values(2,'Seguimiento especial','sistema',now(),null,null);
insert into tab_solici_tiposx values(3,'PQR','sistema',now(),null,null);
insert into tab_solici_tiposx values(4,'Otras solicitudes','sistema',now(),null,null);

-- tab_solici_subtip
insert into tab_solici_subtip values(0,'default',0,'sistema',now(),null,null);
insert into tab_solici_subtip values(1,'Toda la flota',2,'sistema',now(),null,null);
insert into tab_solici_subtip values(2,'Por vehículo',2,'sistema',now(),null,null);
insert into tab_solici_subtip values(3,'Petición',3,'sistema',now(),null,null);
insert into tab_solici_subtip values(4,'Queja',3,'sistema',now(),null,null);
insert into tab_solici_subtip values(5,'Sugerencia',3,'sistema',now(),null,null);
insert into tab_solici_subtip values(6,'Felicitación',3,'sistema',now(),null,null);


-- ajuste de tablas
alter table tab_solici_datosx modify num_usrcel bigint(10) not null;
alter table tab_solici_datosx modify fec_modifi datetime null;
alter table tab_solici_solici modify fec_modifi datetime null;
alter table tab_solici_solici modify dir_archiv varchar(255) null;



-- fue removido un constraint de la tabla tab_solici_datosx;

<?php 
// @session_start();
// $panno_m = $_SESSION['anno'];
// $nit_ut  = "SELECT t.numeroidentificacion, u.rol
// FROM gs_usuario u 
// LEFT JOIN gf_tercero t ON u.tercero = t.id_unico 
// WHERE t.id_unico =".$_SESSION['usuario_tercero'];
// $r = $mysqli->query($nit_ut);
// $r = mysqli_fetch_row($r);

?>

<link href="css/custom.min_menu.css" rel="stylesheet">
<style type="text/css">
    body{
        font-size: 12px;
        font-family:sans-serif;
    }
</style>

<div  class="col-sm-2 sidenav text-left" style="background:#002952;overflow-x:hidden; overflow-y:scroll;padding-left:0px">
    <div style="float:left;margin-top: -30px;" class=""> 
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu" >
            <div class="menu_section">
                <ul class="nav side-menu" >
                    

                    <li>
                        <a href="#" style="padding-left: 0%;"> GESTIÓN FINANCIERA <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu" style="padding-left: 0px">
                            <li>
                                <a onclick="javaScript:buscarFechas(1);">  PRESUPUESTO<span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu" style="padding-left: 10px">
                                    <li>
                                        <a> ARCHIVOS BÁSICOS<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px">
                                            <li>
                                                <a href="listar_GF_CLASE_PPTAL.php">Clase Presupuestal</a>
                                            </li>                                             
                                            <li>
                                                <a href="listar_GF_FUENTE.php">Fuente</a>
                                            </li>             
                                            <li>
                                                <a href="listar_GF_RUBRO_PPTAL.php">Rubro Presupuestal</a>
                                            </li>
                                            <li>
                                                 <a href="listar_GF_TIPO_COMPROBANTE_PPTAL.php">Tipo Comprobante Presupuestal</a>
                                            </li>
                                            <li>
                                                <a href="listar_GF_TIPO_CLASE_PPTAL.php">Tipo Clase Presupuestal</a>
                                            </li>
                                            <li>
                                                <a href="listar_GF_TIPO_PAC.php">Tipo PAC</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a>MOVIMIENTOS<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px">
                                            <li>
                                                <a href="ADICION_APROPIACION.php">Adición Apropiación</a>
                                            </li>                                          
                                            <li>
                                                <a href="registrar_GF_APROPIACION_INICIAL.php">Apropiación Inicial</a>
                                            </li> 
                                            <li>
                                                <a href="registrar_GF_COMPROBANTE_PPTAL.php">Solicitud</a> 
                                            </li> 
                                            <li>
                                                <a href="APROBAR_COMPROBANTE_PPTAL.php">Aprobar Solicitud</a>
                                            </li>
                                            <li>
                                                <a href="EXPEDIR_DISPONIBILIDAD_PPTAL.php">Disponibilidad Presupuestal</a>
                                            </li> 
                                            <li>
                                                <a href="EXPEDIR_REGISTRO_PPTAL.php">Registro Presupuestal</a>
                                            </li> 
                                            <li>
                                                <a href="APROBACION_ORDEN_PAGO.php">Aprobación Orden de Pago</a>
                                            </li>
                                            <li>
                                                <a href="REDUCCION_APROPIACION.php">Reducción Apropiación</a>
                                            </li>    
                                            <li>
                                                <a href="EXPEDIR_OBLIGACION_PPTAL.php">Obligación Presupuestal</a>
                                            </li> 
                                            <li>
                                                <a href="registrar_PAGO_PPTAL.php">Giro Presupuestal</a>
                                            </li> 
                                            <li>
                                                <a href="MODIFICACION_DISPONIBILIDAD_PPTAL.php">Modificación Disponibilidad</a>
                                            </li>  
                                            <li>
                                                <a href="MODIFICACION_REGISTRO_PPTAL.php">Modificación Registro</a>
                                            </li>  
                                            <li>
                                                <a href="trasladoPresupuestal.php">Traslado Presupuestal</a>
                                            </li> 
                                            <li>
                                                <a href="registrar_GF_RECAUDO_PPTAL.php">Recaudo Presupuestal</a>
                                            </li> 
                                            <li>
                                                <a href="GF_CIERRE_PPTAL.php">Cierre Presupuestal</a>
                                            </li> 
                                        </ul>    
                                    </li>
                                    <li>
                                        <a> INFORMES<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px">
                                            <li><a>LISTADOS<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                    <li> <a href="#" onclick="exportarPlanPresupuestalMenu()">Plan Presupuestal</a> </li>
                                                    <li> <a href="#" onclick="exportarApropiacionInicialMenu()">Apropiaciones Iniciales</a> </li>
                                                    <li> <a href="#" onclick="exportarConfiguracionMenu()">Configuración Concepto</a></li>
                                                    <li> <a href="#" onclick="exportarFuentesMenu()">Fuentes de Recursos</a> </li>
                                                </ul> 
                                            </li>
                                            <li><a> AUXILIARES<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                    <li> <a href="gf_AUX_COMP_PPTAL_GASTOS.php">Auxiliares Comprobantes Presupuestales Gastos</a> </li>
                                                    <li> <a href="gf_AUX_COMP_PPTAL_INGRESOS.php">Auxiliares Comprobantes Presupuestales Ingresos</a> </li>
                                                    <li> <a href="GF_SEGUIMIENTO_DIS.php">Seguimiento a Disponibilidad</a> </li>
                                                    <li><a href="generar_GF_INF_COMPROBANTES_TIPO.php?tipo=pptal">Listado Comprobantes Tipo</a></li>
                                                    <li><a href="GF_LIBRO_AUXILIAR.php">Libro Auxiliar Presupuestal</a> </li>
                                                 </ul>
                                            </li>
                                            <li><a> INFORMES GENERALES<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                    <li> <a href="gf_EJECUCION_PPTAL_GASTOS_INVERSION_ACUMULADO.php">Ejecución Presupuestal de Gastos e Inversión Acumulado</a> </li>
                                                    <li> <a href="gf_EJECUCION_PPTAL_RENTAS_INGRESOS.php">Ejecución Presupuestal de Rentas e Ingresos</a> </li>
                                                    <li> <a href="gf_EJECUCION_PPTAL_GASTOS_INVERSION.php">Ejecución Presupuestal de Gastos e Inversión Por Periodo</a> </li>
                                                    <li> <a href="gf_EJECUCION_PPTAL_RENTAS_INGRESOS_G.php">Ejecución Presupuestal de Rentas e Ingresos Por Periodo</a> </li>
                                                    <li> <a href="GF_EJECUCION_RESERVAS.php">Ejecución Presupuestal de Reservas</a> </li>
                                                    <li> <a href="GF_EJECUCION_CUENTAS_PAGAR.php">Ejecución Presupuestal de Cuentas Por Pagar Vigencia Anterior</a> </li>
                                                    <li> <a href="GF_CONCILIACION_INGRESOS_GASTOS.php">Conciliación Ingresos - Gastos Por Fuente</a> </li>
                                                </ul >
                                            </li>
                                            <li><a> INFORMES SIFSE<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                     <li><a href="GF_SIFSE_GASTOS.php"> Presupuestal de Gastos</a></li>
                                                     <li><a <a href="GF_SIFSE_INGRESOS.php"> Presupuestal de Ingresos</a></li>
                                                </ul>
                                            </li>
                                            <li><a> INFORMES GERENCIALES<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                    <li><a href="GF_INFORME_GERENCIAL_GASTOS.php"> Ejecución De Gastos</a></li>
                                                    <li><a href="GF_INFORME_GERENCIAL_INGRESOS.php"> Ejecución de Ingresos</a></li>
                                                </ul>
                                            </li>
                                            <li><a> COMPROBANTES EN LOTE<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                    <li> <a href="GF_LISTADOS_FORMATOS.php?t=1">Disponibilidades</a> </li>
                                                    <li> <a href="GF_LISTADOS_FORMATOS.php?t=2">Registros</a> </li>
                                                </ul>
                                            </li>
                                        </ul> 
                                    </li>
                                </ul> 
                            </li> 
                            <li>
                                <a onclick="javaScript:buscarFechas(2);"> CONTABILIDAD<span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu" style="padding-left: 10px">
                                    <li>
                                        <a> ARCHIVOS BÁSICOS<span class="fa fa-chevron-down"></span></a>
                                        <ul  class="nav child_menu" style="padding-left: 10px">
                                            <li>
                                                <a href="buscarCuenta.php">Cuenta</a>
                                            </li>
                                            <li>
                                                <a href="listar_GF_TIPO_COMPROBANTE.php">Tipo Comprobante</a>
                                            </li> 
                                            <li>
                                                <a href="LISTAR_GF_TIPO_RETENCION.php">Tipo Retención</a>
                                            </li>
                                            <li>
                                                <a href="registrar_GF_CIERRE_CONTABLE.php">Configuración Cierre Contable</a>
                                            </li>
                                            <li>
                                                <a href="LISTAR_GF_LIBROS.php">Libros Oficiales</a>
                                            </li>
                                             <li>
                                                <a href="LISTAR_GF_HOMOLOGACION_CUENTAS_VA.php">Homologar Cuentas Vigencia Anterior</a>
                                            </li>  
                                        </ul>    
                                    </li>
                                    <li>
                                        <a> MOVIMIENTOS<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px">
                                            <li>
                                                <a href="registrar_GF_SALDOS_INICIALES.php">Saldos Iniciales</a>
                                            </li>                                                                 
                                            <li>
                                                <a href="registrar_GF_COMPROBANTE_CONTABLE.php">Comprobante Contable</a>
                                            </li>  
                                            <li>
                                                <a href="GENERAR_CUENTA_PAGAR.php">Generar Cuenta por Pagar</a>
                                            </li> 
                                             <li>
                                                 <a href="registrar_GF_COMPROBANTE_CAUSACION.php">Comprobante de Causación</a>
                                            </li> 
                                            <li>
                                                <a>CARGUE ARCHIVO PLANO <span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                    <li>
                                                        <a>CONFIGURACIÓN<span class="fa fa-chevron-down"></span></a>
                                                        <ul class="nav child_menu" style="padding-left: 10px">
                                                            <li><a href="listar_GS_TIPO_ARCHIVO.php">Tipo Archivo</a></li>
                                                            <li><a href="listar_GS_CLASE_ARCHIVO.php">Clase Archivo</a></li>
                                                        </ul>
                                                    </li>
                                                    <li>
                                                        <a>GENERACIÓN<span class="fa fa-chevron-down"></span></a>
                                                        <ul class="nav child_menu" style="padding-left: 10px">
                                                            <li><a href="subirArchivoPredial.php">Cargue Archivo Predial</a></li>
                                                            <li><a href="subirArchivoFacturacion.php">Cargue Archivo Facturación</a></li>
                                                        </ul>
                                                    </li>
                                                    <li>
                                                        <a>VIASOFT<span class="fa fa-chevron-down"></span></a>
                                                        <ul class="nav child_menu" style="padding-left: 10px">
                                                            <li><a href="configuracion_viasoft_sigiep.php">Configuración</a></li>
                                                            <li><a href="subirArchivoViasoft.php">Cargue Archivo Recaudo Viasoft</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a href="GF_CIERRE_CONTABLE.php">Cierre Contable</a>
                                            </li>
                                            <li>
                                                <a href="GF_PASAR_SALDOS.php">Preparar Saldos Año Siguiente</a>
                                            </li>
                                            <li>
                                                <a href="GF_COMPARAR_PUC.php">Comparar Plan Presupuestal</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li><a> INFORMES<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px">
                                            <li><a>LISTADOS<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                   <li> <a href="#" onclick="plancuentasMenu()">Plan de Cuentas</a></li>
                                                   <li> <a href="informes/generar_INF_TIPO_RETENCIONES.php">Tipos de Retenciones</a> </li>
                                                </ul> 
                                            </li>
                                            <li>
                                                <a> AUXILIARES<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                    <li> <a href="gf_AUX_CONTABLES.php">Auxiliares Contables</a> </li>
                                                    <li> <a href="gf_AUX_CONTABLE_TERCERO.php">Auxiliares Contables Terceros</a> </li>
                                                    <li> <a href="gf_AUX_CONTABLE_CENTRO_COSTO.php">Auxiliares Contables Centro Costo</a> </li>
                                                    <li><a href="GF_INF_AUX_CON_RETENCIONES.php">Auxiliar Contable Retenciones</a></li>
                                                    <li><a href="generar_GF_INF_COMPROBANTES_TIPO.php?tipo=cnt">Listado Comprobantes Tipo</a></li>
                                                    <li><a href="informes/inf_comprobantes_descuadrados.php">Listado Comprobantes Descuadrados</a></li>
                                                    <li><a href="GF_DIARIO_CAJA.php">Boletín Diario De Caja</a></li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a>INFORMES GENERALES<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                    <li> <a href="gf_BALANCE_PRUEBA.php">Balance de Prueba</a> </li>
                                                    <li> <a href="gf_BALANCE_GENERAL.php">Balance General</a> </li>
                                                    <li> <a href="gf_BALANCE_ACT_ECON_SOC_FIN.php">Estado Actividad económica y social</a> </li>
                                                    <li> <a href="gf_BALANCE_TES_CAJA_BIENES.php">Estado de Tesorería, Caja y Bancos</a> </li>
                                                    <li> <a href="gf_relacion_ingresos.php?window=form">Relación de Ingresos</a></li>
                                                    <li><a href="generar_consolidado_ingresos">Informe Consolidado de Ingresos</a></li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a>LIBROS OFICIALES<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                    <li><a href="GF_FOLIAR_LIBROS.php">Foliar Libros Oficiales</a> </li>
                                                    <li><a href="GF_LIBRO_DIARIO.php">Libro Diario Oficial</a> </li>
                                                    <li><a href="GF_LIBRO_MAYOR.php">Libro Mayor y Balances</a> </li>
                                                    <li><a href="GF_LIBRO_INVENTARIOS.php">Libro Inventario y Balances</a> </li>
                                                    <li><a href="GF_ESTADO_ACTIVIDAD_ECONOMICA.php">Estado De La Actividad Economica, Social Y Financiera</a> </li>
                                                </ul>
                                            </li>
                                            <li><a> INFORMES EXÓGENAS<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                    <li><a>ARCHIVOS BÁSICOS<span class="fa fa-chevron-down"></span></a>
                                                        <ul class="nav child_menu" style="padding-left: 10px">
                                                            <li><a href="GF_FORMATOS_EXOGENAS.php">Formatos Exógenas</a></li>
                                                        </ul> 
                                                    </li>
                                                    <li>
                                                        <a> MOVIMIENTOS<span class="fa fa-chevron-down"></span></a>
                                                        <ul class="nav child_menu" style="padding-left: 10px">
                                                            <li> <a href="GF_CONFIGURACION_EXOGENAS.php">Configuración Exógenas</a> </li>
                                                            <li> <a href="GF_GENERAR_EXOGENAS.php">Generar Informes</a> </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li><a> COMPROBANTES EN LOTE<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                    <li> <a href="GF_LISTADOS_FORMATOS.php?t=3">Cuentas Por Pagar</a> </li>
                                                </ul>
                                            </li>
                                        </ul>    
                                    </li>
                                </ul> 
                            </li>    
                            <li>
                                <a> TESORERIA<span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu" style="padding-left: 10px">
                                    <li>
                                        <a> ARCHIVOS BÁSICOS<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px">
                                            <li>
                                                <a href="LISTAR_GF_CHEQUERA.php">Chequera</a>
                                            </li>
                                            <li>
                                                <a href="listar_GF_CUENTA_BANCARIA.php">Cuenta Bancaria</a>
                                            </li>
                                            <li >
                                                <a href="listar_GF_RECURSO_FINANCIERO.php">Recurso Financiero</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a> MOVIMIENTOS<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px">
                                            <li>
                                                <a href="registrar_GF_COMPROBANTE_INGRESO.php">Comprobante de Ingreso</a>
                                            </li> 
                                            <li>
                                                <a href="GENERAR_EGRESO.php">Generar Egreso</a>
                                            </li>
                                            <li>
                                                <a href="GF_EGRESO_PROVEEDOR.php">Egreso Por Proveedor</a>
                                            </li> 
                                            <li>
                                                <a href="registrar_GF_PARTIDA_CONCILIATORIA.php">Conciliación Bancaria</a>
                                            </li>
                                            <li>
                                                <a href="registrar_GF_EGRESO_TESORERIA.php">Egreso Tesorería </a>
                                            </li>
                                            <li>
                                                <a href="registrar_lote_causacion_ingresos.php?action=lote_ingresos_causacion">Generar Comprobantes de Causación Lote</a>
                                            </li>
                                            <li>
                                                <a href="registrar_lote_causacion_ingresos.php?action=lote_retenciones">Generar Comprobantes de Ingreso de Retención por Lote</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li><a> INFORMES<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px">
                                            <li>
                                                <a> AUXILIARES<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                    <li> <a href="GF_AUXILIAR_RETENCIONES.php">Auxiliar De Retenciones</a> </li>
                                                    <li> <a href="GF_BANCOS_SIN_CONCILIAR.php">Bancos Sin Conciliar</a> </li>
                                                    <li> <a href="GF_CERTIFICADO_RETENCION.php">Certificado de Retención</a> </li>
                                                    <li> <a href="GF_RELACION_EGRESOS.php">Relación de Egresos</a> </li>
                                                    <li> <a href="GF_RELACION_EGRESOS_RUBRO.php">Relación de Egresos Con Rubro</a> </li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a> INFORMES GENERALES<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                    <li> <a href="GF_ESTADO_TESORERIA.php">Estado De Tesorería</a> </li>
                                                </ul>
                                            </li>
                                            <li><a> COMPROBANTES EN LOTE<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                    <li> <a href="GF_LISTADOS_FORMATOS.php?t=4">Egresos</a> </li>
                                                </ul>
                                            </li>
                                        </ul>    
                                    </li>                        
                                </ul> 
                            </li>  
                            <li>
                                <a>FACTURACION<span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu" style="padding-left: 10px">
                                    <li><a> ARCHIVOS BÁSICOS<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px">
                                            <li>
                                               <a href="LISTAR_GP_CONCEPTO.php">Concepto</a>
                                            </li>
                                            <li>
                                                <a href="LISTAR_GP_TARIFA.php">Tarifa</a>
                                            </li>
                                            <li>
                                                <a href="listar_GP_TIPO_CONCEPTO.php">Tipo Concepto</a>
                                            </li>
                                            <li>
                                                <a href="listar_GP_TIPO_FACTURA.php">Tipo Factura</a>
                                            </li>
                                            <li>
                                                <a href="LISTAR_GP_TIPO_OPERACION.php">Tipo Operación</a>
                                            </li>
                                            <li>
                                                <a href="LISTAR_GP_TIPO_PAGO.php">Tipo Pago</a>
                                            </li>  
                                            <li>
                                                <a href="GP_TIPO_CARTERA.php">Tipo Cartera</a>
                                            </li>
                                            <li>
                                                <a href="GP_CONFIGURACION_CONCEPTO.php">Configuración Concepto</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a> MOVIMIENTOS<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px">
                                                <li>
                                                    <a href="registrar_GF_FACTURA.php">Factura Contable y Presupuestal</a>
                                                </li>
                                                <li>
                                                    <a href="registrar_GF_RECAUDO_FACTURACION_2.php">Recaudo de Facturación</a>
                                                </li>  
                                                <li>
                                                    <a href="GF_RECAUDO_CLIENTE.php">Recaudo Por Cliente</a>
                                                </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a> INFORMES<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px">
                                                <li>
                                                    <a href="gp_LISTADO_FACTURACION.php">Facturación</a>
                                                </li>
                                                <li>
                                                    <a href="gp_LISTADO_FACTURACION_RECAUDO.php">Recaudo de Facturación</a>
                                                </li>  
                                                <li><a href="relacion_comprobantes_factura.php">Relación de Facturación entre Comprobantes</a></li>
                                                <li>
                                                    <a href="GF_RELACION_FACTURACION.php">Relación Facturación - Recaudo - Contabilidad</a>
                                                </li>
                                                <li><a href="GF_SALDO_FACTURAS_TERCERO.php">Facturas Con Saldo Por Tercero</a></li>
                                        </ul>
                                    </li>
                                </ul> 
                            </li>  
                            <li>
                                <a> NIIF<span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu" style="padding-left: 10px">
                                    <li>
                                        <a> MOVIMIENTOS<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px">
                                            <li>
                                                <a href="LISTAR_GF_POLITICAS_CONTABLES.php">Políticas Contables</a>
                                            </li>
                                            <li>
                                                <a href="GF_MOVIMIENTO_NIIF.php">Movimientos Niif</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a> INFORMES<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px">
                                            <li>
                                                <li><a href="GF_ESTADO_APERTURA.php">Estado De Situación Financiera De Apertura</a> </li>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li><a href="#" style="padding-left: 0%;">GESTIÓN RECURSOS FÍSICOS <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu" style="padding-left: 0px">
                            <li>
                                <a href="#"> ALMACEN E INVENTARIO<span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu" style="padding-left: 10px">
                                    <li><a> ARCHIVOS BÁSICOS<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px">
                                            <li><a href="GF_ELEMENTO_FICHA.php">Elemento Ficha</a></li>
                                            <li><a href="listar_GF_FICHA.php">Ficha</a></li>
                                            <li><a href="GF_TIPO_ACTIVO.php"> Tipo Activo</a></li>
                                            <li><a href="LISTAR_GF_TIPO_MOVIMIENTO.php">Tipo Movimiento</a></li>
                                            <li><a href="GF_PLAN_INVENTARIO.php">Plan Inventario</a></li>
                                            <li><a href="GF_MODIFICAR_PRODUCTO.php">Modificar Especificaciones Producto</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a> MOVIMIENTOS<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px">
                                            <li><a href="RF_REQUISICION_ALMACEN.php">Requisión Almacén</a></li>
                                            <li><a href="registrar_RF_ORDEN_DE_COMPRA.php">Orden de Compra</a></li>
                                            <li><a href="RF_ENTRADA_ALMACEN.php">Entrada de Almacén</a></li>
                                            <li><a href="registrar_GR_SALIDA_ALMACEN.php">Salida Almacén</a><li>
                                            <li><a href="registrar_RF_TRANSLADO_ALMACEN.php">Traslado Almacén</a></li>
                                            <li><a href="registrar_RF_REINTEGRO.php">Reintegro Almacén</a></li>
                                            <li><a href="registrar_BAJA_DEVOLUTIVOS.php">Baja de Almacén</a></li>
                                            <li><a href="depreciacion_almacen_productos.php">Depreciación</a></il>
                                            <li><a href="RF_AVALUO_ALMACEN.php">Avaluo Almacén</a></il>
                                            <li><a href="registrar_RF_MOVIMIENTO_ALMACEN.php">Movimiento de Almacén</a></li>
                                            <li><a href="GF_SUBIR_DATAS.php">Subir Datas</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a> CONTABILIDAD<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px">
                                            <li>
                                                <a href="GF_INTERFAZ_TRAN.php">Configuración</a>
                                            </li>
                                            <li>
                                                <a href="GF_DETERIORO.php">Interfaz Depreciación</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li><a> INFORMES<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px">
                                            <li><a>LISTADOS<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                    <li><a href="informes/listado_catalogo_inventario.php" target="_blank">Listado de Plan Inventario</a> </li>
                                                    <li><a href="informes/listado_catalogo_inventario_excel.php" target="_blank">Listado de Plan Inventario Excel</a> </li>
                                                </ul>
                                            </li>
                                             <li><a>AUXILIARES<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left: 10px">
                                                    <li> <a href="gr_AUX_MOVIMIENTO_ALMACEN.php">Auxiliares Movimiento Almacén</a> </li>
                                                    <li> <a href="GF_AUXILIAR_ALMACEN_TC.php">Auxiliares Movimiento Almacén Por Tipo</a> </li>
                                                    <li> <a href="GF_LISTADO_DEPENDENCIA.php">Auxiliares Movimiento Por Dependencia </a> </li>
                                                    <li> <a href="GN_INFORME_MOVIMIENTO_PRODUCTOS.php">Informe Movimientos Producto</a> </li>
                                                </ul>
                                            </li>
                                            <li><a>INFORMES GENERALES<span class="fa fa-chevron-down"></span></a>
                                                <ul class="nav child_menu" style="padding-left:10px">
                                                    <li><a href="listados_inventarios.php">Listados de Inventario</a></li>
                                                    <li><a href="listados_deterioro.php">Informes de Depreciación Almacén</a></li>
                                                    <li> <a href="gr_EXISTENCIAS_INVENTARIO.php">Existencias de Inventario</a> </li>
                                                    <li> <a href="GF_CONSOLIDADO_ALMACEN.php">Informes Consolidado de Almacén</a> </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li><a href="#" style="padding-left: 0%;">GESTIÓN DE INFORMES<span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu" style="padding-left: 0px">
                            <li>
                                <a> ARCHIVOS BÁSICOS<span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu" style="padding-left: 10px">
                                    <li>
                                        <a href="LISTAR_GN_CLASE_INFORME.php">Clase Informe</a>
                                    </li>
                                    <li>
                                        <a href="GN_PERIODICIDAD.php">Periodicidad</a>
                                    </li>
                                    <li>
                                        <a href="GN_TIPO_INFORME.php">Tipo Informe</a>
                                    </li>       
                                </ul> 
                            </li> 
                            <li>
                                <a> MOVIMIENTOS<span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu" style="padding-left: 10px">
                                    <li><a href="GN_HOMOLOGACIONES.php">Homologaciones</a></li>
                                    <li><a href="generar_GN_INFORME.php">Generar Informe</a></li>
                                    <li> <a href="GF_INFORMES_SIA.php">Informes SIA</a> </li>
                                    <?php  if ($r[0]=='900849655'){ ?>
                                    <li><a href="crear_tabla.php"> Generar Tabla </a></li>
                                    <li><a href="cargar_archivo.php">Cargar Archivo</a></li>
                                    <li><a href="GN_TABLA_HOMOLOGACION.php">Configuración Base</a></li>
                                    <li><a href="registrar_GN_INFORME.php">Configuración de Informe</a></li>
                                    <?php } ?>
                                </ul> 
                            </li>  
                            <li>
                                <a> INFORMES<span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu" style="padding-left: 10px">
                                    <li> <a href="GN_INFORME_HOMOLOGACIONES.php">Informe Homologaciones</a> </li>
                                    <li> <a href="GN_INFORME_INCONSISTENCIAS.php">Informe Inconsistencias Homologaciones</a> </li>
                                </ul> 
                            </li>  
                        </ul>
                    </li>
                    <li><a href="#" style="padding-left: 0%;">ARCHIVOS GENERALES<span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu" style="padding-left: 0px">
                            <li>
                                <a> ARCHIVOS BÁSICOS<span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu" style="padding-left: 10px">
                                    <li>
                                        <a href="CENTRO_COSTO.php">Centro Costo</a>
                                    </li>
                                    <li>
                                        <a href="listar.php">Clase Contrato</a>
                                    </li> 
                                    <li>
                                        <a href="listar_GF_CONCEPTO.php">Concepto</a>
                                    </li>
                                    <li>
                                        <a href="listar_GF_CONCEPTO_RUBRO.php">Concepto Rubro</a>
                                    </li>                                                                                        
                                    <li>
                                        <a href="listar_GF_DESTINO.php">Destino</a>
                                    </li>
                                    <li>
                                        <a href="LISTAR_GF_DEPENDENCIA.php">Dependencia</a>
                                    </li>
                                    <li>
                                        <a href="listar_GF_PARAMETRIZACION_ANNO.php">Parametrización año</a>
                                    </li>
                                    <li>
                                        <a href="listar_GF_PROYECTO.php">Proyecto</a>
                                    </li>
                                    <li>
                                        <a href="listar_GF_SECTOR.php">Sector</a>
                                    </li>
                                    <li>
                                        <a href="listar_GF_TIPO_FUENTE.php">Tipo Fuente</a>
                                    </li>
                                    <li>
                                        <a href="listar_GF_TIPO_VIGENCIA.php">Tipo Vigencia</a>
                                    </li>
                                    <li><a onclick="javascript:abrirMTerceroMenu()">Buscar Terceros</a></li> 
                                </ul> 
                            </li> 
                            <li>
                                <a> INFORMES<span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu" style="padding-left: 10px">
                                    <li> <a href="informes/generar_INF_DEPENDENCIA_C.php">Dependencias</a> </li>
                                    <li> <a href="informes/generar_INF_CENTRO_COSTO.php">Centros de Costos</a> </li>
                                    <li> <a href="informes/generar_INF_TERCEROS.php">Listado Terceros</a> </li>
                                    <li> <a href="GF_INFORME_INCONSISTENCIAS.php">Informe Inconsistencias Terceros</a> </li>
                                </ul> 
                            </li>  
                        </ul>
                    </li>
                    <li>
                        <a href="#" style="padding-left: 0%;">ARCHIVOS BÁSICOS<span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu" style="padding-left: 10px;">
                            <li>
                                <a href="GF_ACTIVIDAD_MANTENIMIENTO.php">Actividad Mantenimiento</a>
                            </li>
                            <li>
                                <a href="GF_CARGO.php">Cargo</a>
                            </li>
                            <li>
                                <a href="listar_GF_CIUDAD.php">Ciudad</a>
                           </li>
                            <li>
                                <a href="listar_GF_CLASE.php">Clase</a>
                            </li>
                            <li>
                                <a href="LISTAR_GF_CLASE_CONTABLE.php">Clase Contable</a>
                            </li>
                            <li>
                                <a href="listar_GF_CLASE_CUENTA.php">Clase Cuenta</a>
                            </li>
                            <li>
                                <a href="LISTAR_GF_CLASE_RETENCION.php">Clase Retención</a>
                            </li>                            
                            <li>
                                <a href="listar_GF_CLASE_SERVICIO.php">Clase Servicio</a>
                            </li>
                            <li>
                                <a href="listar_GF_CONDICION.php"> Condición</a>
                            </li>
                            <li>
                                <a href="listar_GF_DEPARTAMENTO.php">Departamento</a>
                            </li>
                            <li>
                                <a href="LISTAR_GF_ESTADO_ANNO.php">Estado Año</a>
                            </li>
                            <li>
                                <a href="LISTAR_GF_ESTADO_CHEQUERA.php">Estado Chequera</a>
                            </li>
                            <li>
                                <a href="LISTAR_GF_ESTADO_COMPROBANTE_CNT.php"> Estado Comprobante Contable</a>
                            </li>
                            <li>
                                <a href="LISTAR_GF_ESTADO_MES.php">Estado Mes</a>
                            </li>
                            <li>
                                <a href="listar_GF_ESTADO_MOVIMIENTO.php">Estado Movimiento</a>
                            </li>
                            <li>
                                <a href="GF_FACTOR_APLICACION.php">Factor Aplicación Nivel</a>
                            </li>
                            <li>
                                <a href="LISTAR_GF_FESTIVOS.php">Festivos</a>
                            </li>
                            <li>
                                <a href="LISTAR_GG_FORMA_NOTIFICACION.php">Forma Notificación</a>
                            </li>
                            <li>
                                <a href="listar_GF_FORMA_PAGO.php">Forma Pago</a>
                            </li>
                            <li>
                                <a href="listar_GF_FORMATO.php">Formato</a>
                            </li>
                            <li>
                                <a href="listar_GF_MES.php">Mes</a>
                            </li>
                            <li>
                                <a href="listar_GF_NATURALEZA.php">Naturaleza </a>
                            </li> 
                            <li>
                                <a href="listar_GF_PAIS.php">Pais</a>
                            </li>
                            <li>
                                <a href="listar_GS_PARAMETROS_BASICOS.php">Parámetros Básicos</a>
                            </li> 
                            <li>
                                <a href="listar_GF_PERFIL.php">Perfil</a>
                            </li> 
                            <li>
                                <a href="listar_GF_SUCURSAL.php">Sucursal</a>
                            </li>
                            <li>
                                <a href="LISTAR_GF_TIPO_ACTIVIDAD.php">Tipo Actividad</a>
                            </li>
                            <li>
                                <a href="GF_TIPO_ACTIVO.php"> Tipo Activo</a>
                            </li>
                            <li>
                                <a href="GF_TIPO_BASE.php">Tipo Base</a>
                            </li>
                            <li>
                                <a href="listar_GF_TIPO_CENTRO_COSTO.php">Tipo Centro Costo</a>
                            </li>
                            <li>
                                <a href="LISTAR_GF_TIPO_CONDICION.php">Tipo Condición</a>
                            </li>
                            <li>
                                <a href="listar_GF_TIPO_CONTRATO.php">Tipo Contrato</a>
                            </li>
                            <li>
                                <a href="LISTAR_GF_TIPO_CUENTA.php">Tipo Cuenta</a>
                            </li>
                            <li>
                                <a href="GF_TIPO_CUENTA_CGN.php">Tipo Cuenta CGN</a>
                            </li>
                            <li>
                                <a href="GF_TIPO_DATO.php">Tipo Dato</a>
                            </li>
                            <li>
                                <a href="LISTAR_GG_TIPO_DIA.php">Tipo Día</a>
                            </li>
                             <li>
                                <a href="listar_GF_TIPO_DEPENDENCIA.php">Tipo Dependencia</a>
                            </li>
                            <li>
                                <a href="GF_TIPO_DIRECCION.php">Tipo Dirección</a>
                            </li>
                            <li>
                                <a href="listar_GF_TIPO_DOCUMENTO.php">Tipo Documento</a>
                            </li>
                            <li>
                                <a href="GS_TIPO_ELEMENTO.php">Tipo Elemento</a>
                            </li>
                            <li>
                                <a href="listar_GF_TIPO_ENTIDAD.php">Tipo Entidad</a>
                            </li>
                            <li>
                                <a href="listar_GF_TIPO_EMPRESA.php">Tipo Empresa</a>
                            </li>
                            <li>
                                <a href="listar_GF_TIPO_IDENTIFICACION.php">Tipo Identificación</a>
                            </li>
                            <li>
                                <a href="GF_TIPO_INVENTARIO.php">Tipo Inventario</a>
                            </li>
                            <li>
                                <a href="listar_GF_TIPO_OPERACION.php">Tipo Operación</a>
                            </li>
                            <li>
                                <a href="GS_TIPO_PERSONA.php">Tipo Persona</a>
                            </li>
                            <li>
                                <a href="GF_TIPO_PROGRAMACION.php">Tipo Programación</a>
                            </li>  
                            <li>
                                <a href="listar_GF_TIPO_RECURSO_FINANCIERO.php">Tipo Recurso Financiero</a>
                            </li>
                            <li>
                                <a href="listar_GF_TIPO_REGIMEN.php">Tipo Régimen</a>
                            </li>
                            <li>
                                <a href="listar_GF_TIPO_RESPONSABLE.php">Tipo Responsable</a>
                            </li>
                            <li>
                                <a href="listar_GF_TIPO_TERCERO.php">Tipo Tercero</a>
                            </li>
                            <li>
                                <a href="GF_UNIDAD_FACTOR.php">Unidad Factor </a>
                            </li>
                            <li>
                                <a href="GF_UNIDAD_PLAZO_ENTREGA.php">Unidad Plazo Entrega</a>
                            </li>
                            <li>
                                <a href="LISTAR_GG_UNIDAD_TIEMPO.php">Unidad Tiempo</a>
                            </li>
                            <li>
                                <a href="listar_GF_ZONA.php">Zona</a>
                            </li>    
                        </ul>
                    </li>
                    <li>
                        <a href="#" style="padding-left: 0%;"  >ADMINISTRACIÓN SISTEMA <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu" style="padding-left: 10px;">
                            <li>
                                <a href="DatosBasicos.php">Modificar Contraseña</a>
                            </li>
                            <?php  if ($r[1]==1 || $r[1]==2){ ?>
                            <li><a href="listar_GS_USUARIO.php">Usuario</a></li>
                            <li><a href="GS_ROL.php">Rol</a></li>
                            <li><a href="listar_GS_ESTADO_USUARIO.php">Estado Usuario</a></li>                            
                            <li><a href="LISTAR_GS_CIERRE_PERIODO.php">Cierre de Periodo</a></li>
                            <li>
                                <a href="#" style="padding-left: 0%;">COPIAR TABLAS<span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu" style="padding-left: 10px;">
                                    <li><a href="GF_COPIAR_TABLAS.php?t=1">De Compañia a Compañia</a></li>
                                    <li><a href="GF_COPIAR_TABLAS.php?t=2">De Año A Año</a></li>

                                </ul>
                            </li>
                            <li>
                                <a href="#" style="padding-left: 0%;">CARGAR TABLAS ARCHIVO PLANO<span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu" style="padding-left: 10px;">
                                    <li><a href="GF_CARGAR_TABLAS.php?t=1">Terceros</a></li>
                                    <li><a href="GF_CARGAR_TABLAS.php?t=2">Plan Contable</a></li>
                                    <li><a href="GF_CARGAR_TABLAS.php?t=3">Plan Presupuestal</a></li>
                                    <li><a href="GF_SUBIR_PRESUPUESTO.php">Presupuesto Inicial</a></li>
                                    <li><a href="GF_CARGAR_TABLAS.php?t=6">Plan Inventario</a></li>
                                    <li><a href="GF_SUBIR_MOVIMIENTOS_PPTAL.php">Subir Configuración Presupuestal</a></li>
                                </ul>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <li>
                        <a href="#" style="padding-left: 0%;"  >GUÍAS SIGIEP <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu" style="padding-left: 10px;">
                            <li><a href="LISTAR_GS_GUIAS.php">Ver Guías</a></li>
                            <li><a href="LISTAR_GS_VIDEOS.php">Ver Videos</a></li>
                        </ul>
                    </li>
                    <li><a href="#" style="padding-left: 0%;">MANTENIMIENTO <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu" style="padding-left: 10px">
                            <li><a href="Mantenimiento_Cuentas.php">Cuentas</a> </li>
                            <li><a href="Mantenimiento_Rubros.php">Rubro Presupuestal</a> </li>
                            <li> <a href="GF_DIGITOS_VERIFICACION.php">Actualizar Dígitos de Verificación</a> </li>
                             <li> <a href="Mantenimiento_Plan_Inventario.php">Plan Inventario</a> </li>
                            <li> <a href="Mantenimiento_Dependencias.php">Dependencias</a> </li>
                            <li><a href="GF_GENERAR_PLACAS.php?id=1">Placas</a> </li>
                        </ul>
                    </li>
                    <?php //if ($r[0]=='900849655'){ ?>
                    <li>
                        <a href="#" style="padding-left: 0%;">ADMINISTRACIÓN SIGIEP<span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu" style="padding-left: 10px;">
                            <li>
                                <a href="GS_CONFIGURACION_CONTRATO.php">Configuración Contrato</a>
                            </li>
                            <li><a href="#" style="padding-left: 0%;">GUIAS SIGIEP <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu" style="padding-left: 10px">
                                    <li><a href="listar_GS_MODULOS.php">Crear Módulos</a></li>
                                    <li><a href="SUBIR_GS_GUIAS.php">Subir Guías</a></li>
                                    <li><a href="SUBIR_GS_VIDEOS.php">Subir Videos</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <?php //} ?>
                </ul>
                <div style="margin-left:-20px">
                    <a onclick="salir()"  href="#" style="color:white; font-size: 14px;padding-left: 13%;">
                        <img src="Conexion/cerrar.png" style="width: 55px; height: 55px"/>
                        <i><strong>SALIDA SEGURA</strong></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--MODALES-->
<script src="js/bootstrap.min_menu.js"></script>
<script src="js/custom.min_menu.js"></script>
<script>
    function abrirMTerceroMenu(){
        $("#terceroMenu").modal('show');
    }
</script>
<div class="modal fade" id="terceroMenu" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Perfil Tercero</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="center">
                    <select style="font-size:15px;height: 40px;" name="tercer" id="tercer" class="form-control" title="Tipo Identificación" required>
                        <option >Perfil Tercero</option>
                        <option value="AsociadoJ">Asocíado Jurídica</option>
                        <option value="AsociadoN">Asocíado Natural</option>
                        <option value="BancoJ">Banco Jurídica</option>
                        <option value="Compania">Compañia</option>
                        <option value="ClienteJ">Cliente Juridica</option>
                        <option value="ClienteN">Cliente Natural</option>
                        <option value="ContactoN">Contacto Natural</option>
                        <option value="EmpleadoN">Empleado Natural</option>
                        <option value="ProveeNat">Proveedor Natural</option>
                        <option value="ProveeJur">Proveedor Jurídica</option>
                        <option value="EntAfil">Entidad Afiliación</option>
                        <option value="EntFinan">Entidad Financiera</option>
                        <option value="Todos">Todos los perfiles</option>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" class="btn" onclick="return terceroMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function terceroMenu(){
        var form = document.getElementById('tercer').value;
        window.location="terceros.php?tercero="+form;
    }
</script>
<script>
    function exportarConfiguracionMenu(){
            $("#modalConceptoMenu").modal('show');
    }
</script>
<div class="modal fade" id="modalConceptoMenu" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informe Configuración Concepto</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="center">
                    <select style="font-size:15px;height: 40px;" name="exportar" id="exportar" class="form-control" title="Exportar A" required>
                        <option >Exportar A:</option>
                        <option value="1">PDF</option>
                        <option value="2">Excel</option>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" class="btn" onclick="exportarConfiguracionCMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function exportarConfiguracionCMenu(){
        var exportar = document.getElementById('exportar').value;
        if(exportar ==1){
            window.open("informes/INFORMES_PPTAL/generar_INF_CONFIGURACION_CONCEPTO.php");
        } else {
            window.open("informes/INFORMES_PPTAL/generar_INF_CONFIGURACION_CONCEPTOEXCEL.php");
        }
    }
</script>
<script>
    function exportarPlanPresupuestalMenu(){
            $("#modalPlanPptalMenu").modal('show');
    }
</script>
<div class="modal fade" id="modalPlanPptalMenu" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Plan Presupuestal</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="center">
                    <select style="font-size:15px;height: 40px;" name="exportarPlan" id="exportarPlan" class="form-control" title="Exportar A" required>
                        <option >Exportar A:</option>
                        <option value="1">PDF</option>
                        <option value="2">Excel</option>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" class="btn" onclick="enviarPlanPptalMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function enviarPlanPptalMenu(){
        var exportar = document.getElementById('exportarPlan').value;
        if(exportar ==1){
            window.open("informes/generar_INF_PLAN_PPTAL.php?t=1");
        } else {
            window.open("informes/generar_INF_PLAN_PPTAL.php?t=2");
        }
    }
</script>
<script>
    function exportarApropiacionInicialMenu(){
            $("#modalApropInicialMenu").modal('show');
    }
</script>
<div class="modal fade" id="modalApropInicialMenu" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Apropiación Inicial</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="center">
                    <select style="font-size:15px;height: 40px;" name="exportarApropiacion" id="exportarApropiacion" class="form-control" title="Exportar A" required>
                        <option >Exportar A:</option>
                        <option value="1">PDF</option>
                        <option value="2">Excel</option>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" class="btn" onclick="enviarApropiacionMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function enviarApropiacionMenu(){
        var exportar = document.getElementById('exportarApropiacion').value;
        if(exportar ==1){
            window.open("informes/generar_INF_APR_INICIALES.php?t=1");
        } else {
            window.open("informes/generar_INF_APR_INICIALES.php?t=2");
        }
    }
</script>
<script>
    function exportarFuentesMenu(){
            $("#modalFuentesMenu").modal('show');
    }
</script>
<div class="modal fade" id="modalFuentesMenu" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Fuentes</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="center">
                    <select style="font-size:15px;height: 40px;" name="exportarFuentes" id="exportarFuentes" class="form-control" title="Exportar A" required>
                        <option >Exportar A:</option>
                        <option value="1">PDF</option>
                        <option value="2">Excel</option>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" class="btn" onclick="enviarFuentesMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function enviarFuentesMenu(){
        var exportar = document.getElementById('exportarFuentes').value;
        if(exportar ==1){
            window.open("informes/generar_INF_FUENTES_RECURSO.php?t=1");
        } else {
            window.open("informes/generar_INF_FUENTES_RECURSO.php?t=2");
        }
    }
</script>
<script>
function salir(){
    $("#mdlSalirMenu").modal('show');
}

</script>
<div class="modal fade" id="mdlSalirMenu" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmación</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>¿Desea salir de SIGIEP?.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnSalirCMenu" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
          <button type="button"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Cancelar
        </button>
      </div>
    </div>
  </div>
</div>
<script>
$('#btnSalirCMenu').click(function(){
    document.location='Conexion/cerrarSesiones.php';
  });
</script>
<script>
    $(document).ready(function()
{
   $("ul").on("click","li", function(){
     var form_data = {case: 2};
      $.ajax({
        type: "POST",
        url: "jsonSistema/consultas.php",
        data: form_data,
        success: function(response)
        {
          console.log(response);
        }
      });
  });
});
</script>
<script>
   function plancuentasMenu(){
       $("#modalPlanCMenu").modal("show");
   }
</script>
<div class="modal fade" id="modalPlanCMenu" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="center">
                    <select style="font-size:15px;height: 40px;" name="exportarplan" id="exportarplan" class="form-control" title="Exportar A" required>
                        <option >Exportar A:</option>
                        <option value="1">PDF</option>
                        <option value="2">Excel</option>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" class="btn" onclick="exportarPlanMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
   function exportarPlanMenu(){
       var tipo = $("#exportarplan").val();
       console.log(tipo);
      window.open("informes/generar_INF_PLAN_CUENTAS.php?id="+tipo);
   }
</script>
<!--*************************Modales Nómina**************************************-->
<script>
        function abrirLN(){
            $("#LiqNom").modal('show');
        }
</script>
<div class="modal fade" id="LiqNom" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 450px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Registrar Novedad</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="left">
                    <label style="display:inline-block; width:130px; font-size: 15px;"><strong style="color:#03C1FB; font-size: 20px;">*</strong>Periodo:</label>
                       
                        <select style="display:inline-block;width:270px;height: 40px;" id="perio" name="perio" id="perio" class="form-control" title="Periodo" required>
                            <option >Periodo</option>

                           
                        </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button"  class="btn" onclick="return guardarNovedadMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function guardarNovedadMenu(){
        //var proces = document.getElementById('proce').value;
        var period = document.getElementById('perio').value;
        window.location="registrar_GN_NOVEDAD.php?periodo="+period;
    }
</script>
<script>
    function abrirINF(){
       $("#InFSab").modal('show');
    }
</script>
<div class="modal fade" id="InFSab" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 450px;">
            <div id="forma-modal" class="modal-header">
                 <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Sabana de Nómina</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="left">
                    <label style="display:inline-block; width:130px; font-size: 15px;"><strong style="color:#03C1FB; font-size: 20px;">*</strong>Periodo:</label>
                   
                    <select style="display:inline-block;width:270px;height: 40px;" id="peri" name="peri"  class="form-control" title="Periodo" required>
                        <option >Periodo</option>

                   
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                    <button type="button" class="btn" onclick=" consultarSabanaMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function consultarSabanaMenu(){
        var period = document.getElementById('peri').value;
        window.location="informes/generar_INF_SABANA_NOMINA.php?periodo="+period;
    }
</script>

<script>
    function abrirInLi(){
       $("#IncapLic").modal('show');
    }
</script>
<div class="modal fade" id="IncapLic" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Incapacidad / Licencia</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="center">
                    <select style="font-size:15px;height: 40px;" name="inli" id="inli" class="form-control" title="Tipo Identificación" required>
                        <option >Seleccione una opción</option>
                      
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="inclic" class="btn" onclick="return elegiropMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button><button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function elegiropMenu(){
        var exportar = document.getElementById('inli').value;
        if(exportar ==4 || exportar == 5 || exportar == 6){
            var tipo = document.getElementById('inli').value;
            window.location="registrar_GN_INCAPACIDAD.php?tipo="+tipo;
        } else {
            var tipo = document.getElementById('inli').value;
            window.location="registrar_GN_LICENCIA.php?tipo="+tipo;
        }
    }
</script>
<script>
    function abrirCierreN(){
       $("#CierreNom").modal('show');
    }
</script>
<div class="modal fade" id="CierreNom" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 450px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Cierre de Nómina</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="left">
					<label style="display:inline-block; width:130px; font-size: 15px;"><strong style="color:#03C1FB; font-size: 20px;">*</strong>Periodo:</label>
                    
                    <select style="display:inline-block;width:270px;height: 40px;" id="period" name="period"  class="form-control" title="Periodo" required>
                        <option >Periodo</option>

                       
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" class="btn"  onclick="javascript:cierreNomMenu();"style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function cierreNomMenu(){
      var id = document.getElementById('period').value;
      window.location="json/cerrarNominaJson.php?id="+id;

    }
</script>
<!-----------------------**************Industria Y Comercio***************-------------------------------->
<script>
    function abrirLD(){
        $("#LiqDec").modal('show');
    }
</script>

<script src="js/jquery-ui.js"></script>
<script>

            $(function(){
                var fecha = new Date();
                var dia = fecha.getDate();
                var mes = fecha.getMonth() + 1;
                if(dia < 10){
                    dia = "0" + dia;
                }
                if(mes < 10){
                    mes = "0" + mes;
                }
                var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
                $.datepicker.regional['es'] = {
                    closeText: 'Cerrar',
                    prevText: 'Anterior',
                    nextText: 'Siguiente',
                    currentText: 'Hoy',
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
                    dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                    dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
                    dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
                    weekHeader: 'Sm',
                    dateFormat: 'dd/mm/yy',
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: false,
                    yearSuffix: '',
                    changeYear: true
                };
                $.datepicker.setDefaults($.datepicker.regional['es']);

                $("#sltFechaDec").datepicker({changeMonth: true,}).val();
                $("#sltFecha").datepicker({changeMonth: true,}).val();
            });
</script>
<div class="modal fade" id="LiqDec" role="dialog" align="center" >

    <div class="modal-dialog">
        <div class="modal-content" style="width: 450px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Registrar Declaración</h4>
            </div>
            <form method="POST" action="javascript:guardarDEC()">
                <div class="modal-body" style="margin-top: 8px">
                    <div class="form-group"  align="left">
                        <label style="display:inline-block; width:130px; font-size: 15px;"><strong style="color:#03C1FB; font-size: 20px;">*</strong>Tipo Periodo:</label>
                          
                            <select style="display:inline-block;width:270px;height: 40px;" id="peri1" name="peri1" class="form-control" title="Selecione el Tipo de Periodo" required>
                                <option value="">Tipo de Periodo</option>

                                
                            </select>
                    </div>
                    <div class="form-group"  align="left">
                        <label style="display:inline-block; width:130px; font-size: 15px;"><strong style="color:#03C1FB; font-size: 20px;">*</strong>Tipo Declaración:</label>
                           
                            <select style="display:inline-block;width:270px;height: 40px;" id="sltTipoD" name="sltTipoD" class="form-control" title="Selecione el Tipo de Declaración" required>
                                <option value="">Tipo Declaración</option>

                                
                            </select>
                    </div>
                    <script type="text/javascript">
                                $(document).ready(function() {
                                   $("#datepicker").datepicker();
                                });
                    </script>

                    <?php
                        $hoy = date('d-m-Y');
                        $hoy = trim($hoy, '"');
                        $fecha_div = explode("-", $hoy);
                        $anio1 = $fecha_div[2];
                        $mes1 = $fecha_div[1];
                        $dia1 = $fecha_div[0];
                        $hoy = ''.$dia1.'/'.$mes1.'/'.$anio1.'';
                    ?>
                    <div class="form-group"  align="left">
                        <label for="sltFechaDec" style="display:inline-block; width:130px; font-size: 15px;">Fecha:</label>
                        <input type="text" id="sltFechaDec" name="sltFechaDec" style="display:inline-block;width:270px;height: 40px;" class="form-control" value="<?php echo $hoy; ?>">
                    </div>
                </div>

                <div id="forma-modal" class="modal-footer">
                    <button type="submit" class="btn"  style="color: #000; margin-top: 2px"  title="Siguiente" ><li class="glyphicon glyphicon-forward"></li></button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" title="Cancelar"><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function guardarDEC(){
            //var proces = document.getElementById('proce').value;

            var perid  = document.getElementById('peri1').value;
            var TipoDE = document.getElementById('sltTipoD').value;
            var FecDEC = document.getElementById('sltFechaDec').value;
            //var pesas  = $("#pesas:checked").val();

            window.location='registrar_GC_DECLARACION.php?peri2='+perid+'&TipoD='+TipoDE+'&FD='+FecDEC;
        }
    </script>
    <script>
        function buscarFechas(tipo){
            let form_data = {case: 10, tipo:tipo};
            $.ajax({
                type: "POST",
                url: "jsonSistema/consultas.php",
                data: form_data,
                success: function(response)
                {
                    console.log(response+'CSF');
                    let resultado = JSON.parse(response);
                    let rta  = resultado["rta"];
                    let html = resultado["html"];
                    if (rta > 0) {
                        $("#fechas_modal").html(html);
                        $("#errorFechas").modal("show");
                    }
                }
              });
            
        }
    </script>
</div>

<?php require_once './gs_actualizacion.php'; ?>

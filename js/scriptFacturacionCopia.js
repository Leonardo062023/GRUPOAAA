//Solo Numeros
function justNumbers(e){
    var keynum = window.event ? window.event.keyCode : e.which;
    if ((keynum == 8) || (keynum == 46) || (keynum == 45))
        return true;
    return /\d/.test(String.fromCharCode(keynum));
}
//Fecha
$(function(){
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
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
    $("#fechaF").datepicker({changeMonth: true}).val();
    $("#fechaV").datepicker({changeMonth: true}).val();
    $("#txtFechaC").datepicker({changeMonth: true}).val();


});

//SELECT2
$("#sltMetodo, #sltTercero,#sltBanco,#sltVendedor,#sltBuscar,#sltUnidad,#sltTipoFactura,#sltCentroCosto,#sltTipoBuscar, #sltIngreso,  #sltUsuario" ).select2({placeholder:"Tercero",allowClear: true});

//Tipo Factura 
$("#sltTipoFactura").change(function(){
    let tipo = $("#sltTipoFactura").val();
    if(tipo.length > 0){
        let form_data = {
            tipo:$("#sltTipoFactura").val(),
            action:1
        };
        $.ajax({
            type: 'POST',
            url: "jsonPptal/gf_facturaJson.php",
            data: form_data,
            success: function (data) {
                $("#txtNumeroF").val(data);
                //Revisar Tipo Cambio
                let form_data = { tipo:$("#sltTipoFactura").val(),action:56};
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_facturaJson.php",
                    data: form_data,
                    success: function (data) {
                        console.log('DD'+data);
                        resultado = JSON.parse(data);
                        let rta = resultado["rta"];
                        if(rta==0){
                            $("#conversion").css("display", "none");
                        } else {
                            let msj = resultado["msj"];
                            let id  = resultado["id"];
                            $("#tipoc").html('Conversión en '+msj);
                            $("#tipo_cambio").val(id);
                            $("#conversion").css("display", "block");

                        }                        
                    }
                });

            }
        });
    }else{
        $("#txtNumeroF").val("");
        $(".herencia").fadeOut("fast");
    }
});
// Buscar Facturas Por Tipo
$("#sltTipoBuscar").change(function(){
    let form_data ={
        estruc:26,
        tipo: $("#sltTipoBuscar").val()
    }
    var option = '<option value="">Buscar Factura</option>';
    $.ajax({
        type:'POST',
        url:'jsonPptal/consultas.php',
        data:form_data,
        success: function(data){
            var option = option+data;
           $("#sltBuscar").html(option);
        }
    });
})

$("#sltBuscar").change(function(e){
    let factura = $("#sltBuscar").val();
    if(!isNaN(factura)){
        buscar(factura);
    }
});
function buscar(factura){
    var form_data = { action:42, factura:factura };
    $.ajax({
        type:'POST',
        url: "jsonPptal/gf_facturaJson.php?action=42",
        data:form_data,
        success: function(response)
        { 
            document.location ='registrar_GF_FACTURASPCOPIA.php?t='+$("#tipo").val()+response;
            
        }
    })
}
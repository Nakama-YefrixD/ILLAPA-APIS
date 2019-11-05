$(document).ready(function() {
    // var URLactual = window.location.href;
    $("#subirLista").on("change", function() {
        event.preventDefault();
        var formData = new FormData($("#insertarExcel")[0]);
        let url = $('#seleccionarTipoExcel').val();
        url = "/importar/mostrar/"+url
        $.confirm({
            icon: 'fa fa-check',
            title: 'Previsualización lista.!',
            theme: 'modern',
            type: 'green',
            buttons: {
                Cerrar: function() {

                }
            },
            content: function() {
                var self = this;
                return $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType:"json",
                        success:function(data)
                        {
                            if(data.estado != "correcto"){
                                $('#tabla').html(data.mensaje);
                                
                            }else{
                                $('#tb_excel tbody').html(data.mensaje);
                                // $('#tablasasd').html(data.mensaje);
                                toastr.success("El excel a sido agregada correctamente", "Acción Realizada");
                            }
                        }
                })
            }
        });
    });

    $('#insertarExcel').on('click', '.agregarExcel',function(event){
        event.preventDefault();
        var formData = new FormData($("#insertarExcel")[0]);
        var url = $('#seleccionarTipoExcel').val();

            $.confirm({
            title: 'Agregar excel',
            icon: 'ti-check-box',
            theme: 'modern',
            closeIcon: true,
            animation: 'scale',
            type: 'green',
            content: '¿Estas seguro de agregar este nuevo excel?',
            buttons: {
                editMenu: {
                    text: 'Agregar',
                    action: function () {
                        $.confirm({
                            icon: 'fa fa-check',
                            title: 'Los datos terminaron de agregarse.!',
                            theme: 'modern',
                            type: 'green',
                            buttons: {
                                Cerrar: function() {
                
                                }
                            },
                            content: function() {
                                var self = this;
                                return $.ajax({
                                    url: url,
                                    type: "POST",
                                    data: formData,
                                    contentType: false,
                                    processData: false,
                                    dataType:"json",
                                        success:function(data)
                                        {
                                            if(data.estado != "correcto"){
                                                toastr.warning("Ocurrio un error al momento de subir el excel.");
                                                
                                            }else{
                                                
                                                toastr.success("El excel a sido agregada correctamente", "Acción Realizada");
                                            }
                                        }
                                    })
                            }
                        });
                            
                    }
                },
                Cancelar: function () {
                    toastr.warning("La acción fue cancelada");
                }
            }
        });

     });

})
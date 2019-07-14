$(document).ready(function() {
    var URLactual = window.location.href;

    $('a.ejemplo').click(function() { 
        var id = $(this).attr('id');
        $(".descargar").attr("id",id);
        console.log(id);
        $.ajax({
            url: "ejemplos/mostrar/"+id,
            type: "GET",
            contentType: false,
            processData: false,
            dataType:"json",
                success:function(data)
                {
                    if(data.estado != "correcto"){
                        $('#tablasasd').html(data.mensaje);
                        // toastr.success("El excel a sido agregada correctamente", "Acción Realizada");
                    }else{
                        $('#tablasasd').html(data.mensaje);
                        // toastr.success("El excel a sido agregada correctamente", "Acción Realizada");
                    }
                }
        })
        
    });

    $('.descargar').on('click', function(e){
        var id = $(this).attr('id');
        
        window.location.href = 'ejemplos/descargar/'+id;

        console.log(id);
    })

})
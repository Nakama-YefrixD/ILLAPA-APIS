$(document).ready(function() {
    $('#tb_excel').on('click', 'td',function(event){
        let error = $(this).attr('error');
        let tipoError = $(this).attr('tipoError');
        $.dialog({
            title: tipoError,
            content: error,
        });

     });

})
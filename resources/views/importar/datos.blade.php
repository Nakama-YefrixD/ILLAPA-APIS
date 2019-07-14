@extends('layouts.blank')
@section('title')
    Importar
@endsection

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Importación de datos</h1>
            </div>
        </div>
    </div>
</div>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">
           Subir excel
        </h3>
        
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                </div>
            </div>
        </div>
        <form role="form" method="post" accept-charset="utf-8" id="insertarAlumnos" enctype="multipart/form-data">
        @csrf

            <div class="row">
                <div class="form-group col-3">
                    <label>Tipo de importe</label>
                    <select class="form-control" name="secciongrado" id="seleccionarTipoExcel" >
                        <option value="clientes" >SELECCIONAR TIPO DE EXCEL</option>
                        <option value="clientes" >Clientes</option>
                        <option value="correos" >Correos</option>
                        <option value="telefonos" >Telefonos</option>
                        <option value="direcciones" >Direcciones</option>
                        <option value="documentos" >Documentos</option>
                        <option value="pagos" >Pagos</option>
                    </select>
                </div>
                <div class="form-group col-9">
                    <label>SELECCIONAR EXCEL</label>
                    <input type="file" name="excel" class="form-control" id="subirLista">
                </div>
            </div>

            <div class="form-group">
                
                <button type="button" class="agregarExcel btn btn-success form-control">
                    Subir
                </button>
            </div>

            <div class="form-group">
                <div class="card-body table-responsive" style="height:50vh" >
                    <span id="tablasasd"></span>
                </div>
            </div>
            
        </form>
 
    </div>
</div>



@endsection

@section('script')
    <script type="text/javascript" src="{{ asset('js/importar/clientes.js') }}"></script>
@endsection
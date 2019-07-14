@extends('layouts.blank')
@section('title')
    Importar
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Lista de ejemplos</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="direct-chat-messages" style="height: 70vh">
                        <div id="ejemploSeleccionado">
                          <input type="hidden" value="" id="countProfesores">
                            
                            <div class="col-md-12">
                                <div class="card card-widget " id="">
                                    <a href="#" class="ejemplo widget-user-header bg-default" id="clientes"
                                        style="color: black">
                                        <h3 class="widget-user-username"> <i class="ion ion-person-add"></i> Clientes </h3>
                                        <h5 class="widget-user-desc">
                                            
                                        </h5>
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="card card-widget " id="">
                                    <a href="#" class="ejemplo widget-user-header bg-default" id="correos"
                                        style="color: black">
                                        <h3 class="widget-user-username"> <i class="far fa-envelope"></i> Correos     </h3>
                                        <h5 class="widget-user-desc">
                                            
                                        </h5>
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="card card-widget " id="">
                                    <a href="#" class="ejemplo widget-user-header bg-default" id="telefonos"
                                        style="color: black">
                                        <h3 class="widget-user-username"> <i class="fas fa-phone"></i> Telefonos </h3>
                                        <h5 class="widget-user-desc">
                                            
                                        </h5>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="card card-widget " id="">
                                    <a href="#" class="ejemplo widget-user-header bg-default" id="direcciones"
                                        style="color: black">
                                        <h3 class="widget-user-username"><i class="	fas fa-home"></i> Direcciones </h3>
                                        <h5 class="widget-user-desc">
                                            
                                        </h5>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="card card-widget " id="">
                                    <a href="#" class="ejemplo widget-user-header bg-default" id="documentos"
                                        style="color: black">
                                        <h3 class="widget-user-username"><i class="far fa-folder-open"></i> Documentos</h3>
                                        <h5 class="widget-user-desc">
                                            
                                        </h5>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="card card-widget " id="">
                                    <a href="#" class="ejemplo widget-user-header bg-default" id="pagos"
                                        style="color: black">
                                        <h3 class="widget-user-username"><i class="fas fa-american-sign-language-interpreting"></i> Pagos </h3>
                                        <h5 class="widget-user-desc">
                                            
                                        </h5>
                                    </a>
                                </div>
                            </div>
                                
                        </div>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9" id ="" >
            <div class="card card-primary card-outline">
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="settings">
                  <button type="button" class="descargar btn btn-block btn-primary" id="datos">Descargar <i class="ion ion-ios-cloud-download"></i></button>
                    <div class="form-group">
                        <div class="card-body table-responsive" style="height:68vh" >
                            <span id="tablasasd"></span>
                        </div>
                    </div>


                  </div>
                </div>
              </div><!-- /.card-body -->
            </div>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/importar/ejemplos.js') }}"></script>

@endsection
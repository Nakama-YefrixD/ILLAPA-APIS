
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="{{ asset('dist/img/logo.png')}}"
           alt="AdminLTE Logo"
           class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">ILLAPA</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
     


  




      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          
          <li class="nav-header">EJEMPLOS</li>
          <li class="nav-item">
            <a href="{{route ('importar.ejemplos')}}" class="nav-link">
              <i class="nav-icon fas fa-file"></i>
              <p>Archivos</p>
            </a>
          </li>
          
          <li class="nav-header">IMPORTAR</li>
          <li class="nav-item">
            <a href="{{route ('importar.datos')}}" class="nav-link">
              <i class="nav-icon far fa-file-excel"></i>
              <p>Importar Datos</p>
            </a>
          </li>

          

          
          
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
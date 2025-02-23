<?php
require_once './gestores/GestorUsuario.php';
require_once './gestores/GestorProyecto.php';
require_once './gestores/GestorTarea.php';

class Menu {
    protected $gestorUsuario;
    protected $gestorProyecto;
    protected $gestorTarea;

    public function __construct($gestorUsuario, $gestorProyecto, $gestorTarea) {
        $this->gestorUsuario = $gestorUsuario;
        $this->gestorProyecto = $gestorProyecto;
        $this->gestorTarea = $gestorTarea;
    }

    public function iniciar() {
        while (true) {
            echo "=== Bienvenido ===\n";
            echo "1. Ingresar \n";
            echo "2. Registrarse\n";
            echo "0. Salir\n";

            $eleccion = trim(fgets(STDIN));

            switch ($eleccion) {
                case '1':
                    if ($this->gestorUsuario->validarUsuario()) {
                        $this->menuPrincipal(); 
                    } else {
                        echo "Validación fallida. Intente nuevamente.\n";
                    }
                    break;

                case '2':
                    $this->gestorUsuario->crearUsuario();
                    break;

                case '0':
                    echo "Saliendo del sistema...\n";
                    return; 

                default:
                    echo "Opción no válida. Inténtelo de nuevo.\n";
                    break;
            }
        }
    }

    public function menuPrincipal() {
        echo "=== Menú principal ===\n";
        while (true) {
            echo "1. Menú Usuario\n";
            echo "2. Menú Proyecto\n";
            echo "0. Salir al Menú inicial\n";

            $eleccion = trim(fgets(STDIN));

            switch ($eleccion) {
                case '1':
                    $this->menuUsuario();
                    break;
                case '2':
                    $this->menuProyecto();
                    break;
                case '0':
                    return; 
                default:
                    echo "Opción no válida. Inténtelo de nuevo.\n";
                    break;
            }
        }
    }

    public function menuUsuario() {
        echo "=== Menú de Usuario ===\n";
        while (true) {
            echo "1. Listar Usuarios\n";
            echo "2. Editar Usuario\n";
            echo "3. Eliminar Usuario\n";
            echo "0. Salir al Menú Principal\n";

            $eleccion = trim(fgets(STDIN));

            switch ($eleccion) {
                case '1':
                    $this->gestorUsuario->listarUsuarios();
                    break;
                case '2':
                    $this->gestorUsuario->editarUsuario();
                    break;
                case '3':
                    $this->gestorUsuario->eliminarUsuario();
                    break;
                 case '0':
                        return;  
                    default:
                        echo "Opción no válida. Inténtelo de nuevo.\n";
                        break;
            }
        }
    }

    public function menuProyecto() {
        echo "=== Menú de Proyecto ===\n";
        while (true) {
            echo "1. Crear Proyecto\n";
            echo "2. Listar Proyectos\n";
            echo "3. Editar Proyecto\n";
            echo "4. Eliminar Proyecto\n";
            echo "5. Crear Tarea\n";
            echo "6. Listar Tareas de Proyecto\n";
            echo "7. Calcular Camino Crítico\n";
            echo "8. Editar Tarea de Proyecto\n";
            echo "9.Eliminar Tarea de Proyecto\n";
            echo "0. Salir al Menú Principal\n";
    
            $eleccion = trim(fgets(STDIN));
    
            switch ($eleccion) {
                case '1':
                    $this->gestorProyecto->crearProyecto();
                    break;
                case '2':
                    $this->subMenuListarProyectos();
                    break;
                case '3':
                    $this->SubMenuEditarProyectos(); 
                    break;
                case '4':
                    echo "Ingrese el ID del proyecto a eliminar: ";
                     $id_proyecto = trim(fgets(STDIN)); 
                    if ( $id_proyecto) {
                        $this->gestorProyecto-> eliminarProyecto( $id_proyecto);
                    } else {
                        echo "ID de proyecto no válido.\n";
                    }
                    break;
                case '5':
                  
                    $this->gestorTarea->crearTarea($this->gestorProyecto);
                    break;
                case '6':
                     echo "Ingrese el ID del proyecto: ";
                     $id_proyecto = trim(fgets(STDIN));
                     if ($id_proyecto) {
                         $this->gestorProyecto->listarTareasPorProyecto($id_proyecto);
                     } else {
                         echo "ID de proyecto no válido.\n";
                     }
                     break;

                   
                case '7':
                    echo "Ingrese el ID del proyecto para calcular el camino crítico: ";
                        $id_proyecto = trim(fgets(STDIN));
                        if (!empty($id_proyecto)) {
                            $this->gestorTarea->calcularCaminoCritico($id_proyecto);
                        } else {
                            echo "ID del proyecto no válido.\n";
                        }
                        break;     
                case '8':
                    echo "Ingrese el ID de la tarea a editar: ";
                    $id_tarea = trim(fgets(STDIN));  
                    $this->gestorTarea->editarTarea($id_tarea);  
                    break;
                case '9':
                    echo "Ingrese el ID de la tarea a eliminar: ";
                    $id_tarea = trim(fgets(STDIN));  
                    $this->gestorTarea->eliminarTarea($id_tarea);  
                    break;
                case '0':
                    return;  
                default:
                    echo "Opción no válida. Inténtelo de nuevo.\n";
                    break;
            }
        }
    }

    public function subMenuListarProyectos() {
        echo "=== Listar Proyectos por Atributo ===\n";
        while (true) {
            echo "1. Listar por ID\n";
            echo "2. Listar por Nombre\n";
            echo "3. Listar por Fecha de Inicio\n";
            echo "4. Listar por Fecha de Fin\n";
            echo "5. Listar por Estado\n";
            echo "0. Volver al Menú de Proyecto\n";
    
            $eleccion = trim(fgets(STDIN));
    
            try {
                switch ($eleccion) {
                    case '1':
                        echo "Listando proyectos por ID...\n"; 
                        $this->gestorProyecto->listarProyectosPorId();
                        break;
                    case '2':
                        echo "Listando proyectos por Nombre...\n"; 
                        $this->gestorProyecto->listarProyectosPorNombre();
                        break;
                    case '3':
                        echo "Listando proyectos por Fecha de Inicio...\n"; 
                        $this->gestorProyecto->listarProyectosPorFechaInicio();
                        break;
                    case '4':
                        echo "Listando proyectos por Fecha de Fin...\n"; 
                        $this->gestorProyecto->listarProyectosPorFechaFin();
                        break;
                    case '5':
                        echo "Listando proyectos por Estado...\n"; 
                        $this->gestorProyecto->listarProyectosPorEstado();
                        break;
                    case '0':
                        
                        echo "Volviendo al Menú de Proyecto...\n"; 
                        return;
                    default:
                        echo "Opción no válida. Inténtelo de nuevo.\n";
                        break;
                }
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }
        }
    }
    

   

public function SubMenuEditarProyectos() {
   
    $gestorTarea = new GestorTarea($this->gestorProyecto); 
    echo "Ingrese el ID del proyecto a editar: ";
    $id_proyecto = trim(fgets(STDIN));
    $this->gestorProyecto->editarProyecto($id_proyecto);
    return;
    }

}      
    

$gestorTarea = new GestorTarea(null); 
$gestorProyecto = new GestorProyecto($gestorTarea);  


$gestorTarea = new GestorTarea($gestorProyecto);


$gestorUsuario = new GestorUsuario();
$menu = new Menu($gestorUsuario, $gestorProyecto, $gestorTarea);


$menu->iniciar();











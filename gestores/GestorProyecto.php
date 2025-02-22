<?php
require_once './clases/proyecto.php';
require_once './clases/tarea.php';  
require_once './gestores/GestorTarea.php';



class GestorProyecto {
    private $proyectos = [];
    private $archivoJson = './Json/proyecto.json';
    private $gestorTarea; 

    public function __construct($gestorTarea) {
        $this->gestorTarea = $gestorTarea; 
        $this->cargarDesdeJson();
    }
    public function setGestorTarea($gestorTarea) {
        $this->gestorTarea = $gestorTarea;
    }

    
    public function crearProyecto() {
        $id_proyecto = count($this->proyectos) + 1;
    
        echo "Ingrese el nombre del proyecto: ";
        $nombre = trim(fgets(STDIN));
    
        echo "Ingrese la descripción del proyecto: ";
        $descripcion = trim(fgets(STDIN));
    
      
        $fechaInicioValida = false;
        while (!$fechaInicioValida) {
            echo "Ingrese la fecha de inicio (formato: Y-m-d): ";
            $fechaInicio = trim(fgets(STDIN));
    
            
            $fechaInicioObj = DateTime::createFromFormat('Y-m-d', $fechaInicio);
            if ($fechaInicioObj && $fechaInicioObj->format('Y-m-d') === $fechaInicio) {
                $anioInicio = $fechaInicioObj->format('Y');
                if ($anioInicio >= 2025) {
                    $fechaInicioValida = true;
                } else {
                    echo "El año de la fecha de inicio no puede ser inferior a 2025.\n";
                }
            } else {
                echo "El formato de fecha ingresado no es válido. Debe ser Y-m-d. Ejemplo: 2025-02-08.\n";
            }
        }
    
       
        $fechaFinValida = false;
        while (!$fechaFinValida) {
            echo "Ingrese la fecha de fin (formato: Y-m-d): ";
            $fechaFin = trim(fgets(STDIN));
    
           
            $fechaFinObj = DateTime::createFromFormat('Y-m-d', $fechaFin);
            if ($fechaFinObj && $fechaFinObj->format('Y-m-d') === $fechaFin) {
                
                if ($fechaFinObj >= $fechaInicioObj) {
                    $fechaFinValida = true;
                } else {
                    echo "La fecha de fin no puede ser anterior a la fecha de inicio.\n";
                }
            } else {
                echo "El formato de fecha ingresado no es válido. Debe ser Y-m-d. Ejemplo: 2025-02-08.\n";
            }
        }
    
        
        $estado = 'activo';  
    
       
        $nuevoProyecto = new Proyecto($id_proyecto, $nombre, $descripcion, $fechaInicio, $fechaFin, $estado);
    
       
        $this->proyectos[] = $nuevoProyecto;
    
        echo "Proyecto creado exitosamente: " . $nuevoProyecto->getNombre() . " con ID " . $nuevoProyecto->getId_proyecto() . "\n";
    
        $this->guardarEnJSON();
    }
    
    
 
    public function listarProyectosPorId() {
      
        if (count($this->proyectos) > 0) {
          
            foreach ($this->proyectos as $proyecto) {
                echo "ID: " . $proyecto->getId_proyecto() . " - Nombre: " . $proyecto->getNombre() . "\n";
            }
        } else {
            echo "No hay proyectos disponibles.\n";
        }
    }
    public function listarProyectosPorNombre() {
       
        
       
        usort($this->proyectos, function($a, $b) {
            return strcmp($a->getNombre(), $b->getNombre());
        });
    
      
        if (count($this->proyectos) > 0) {
            foreach ($this->proyectos as $proyecto) {
                echo "ID: " . $proyecto->getId_proyecto() . " - Nombre: " . $proyecto->getNombre() . "\n";
            }
        } else {
            echo "No hay proyectos disponibles.\n";
        }
    }
    public function listarProyectosPorFechaInicio() {
      
        
       
        usort($this->proyectos, function($a, $b) {
            return $a->getFechaInicio() <=> $b->getFechaInicio(); 
        });
    
       
        if (count($this->proyectos) > 0) {
            foreach ($this->proyectos as $proyecto) {
                echo "ID: " . $proyecto->getId_proyecto() . " - Nombre: " . $proyecto->getNombre() . " - Fecha de Inicio: " . $proyecto->getFechaInicio()->format('Y-m-d') . "\n";
            }
        } else {
            echo "No hay proyectos disponibles.\n";
        }
    }
    public function listarProyectosPorFechaFin() {
        usort($this->proyectos, function($a, $b) {
            return $a->getFechaFin() <=> $b->getFechaFin(); 
        });
    
      
        if (count($this->proyectos) > 0) {
            foreach ($this->proyectos as $proyecto) {
                echo "ID: " . $proyecto->getId_proyecto() . " - Nombre: " . $proyecto->getNombre() . " - Fecha de Fin: " . $proyecto->getFechaFin()->format('Y-m-d') . "\n";
            }
        } else {
            echo "No hay proyectos disponibles.\n";
        }
    }

    public function listarProyectosPorEstado() {
      
        
       
        usort($this->proyectos, function($a, $b) {
            return strcmp($a->getEstado(), $b->getEstado()); 
        });
    
      
        if (count($this->proyectos) > 0) {
            foreach ($this->proyectos as $proyecto) {
                echo "ID: " . $proyecto->getId_proyecto() . " - Nombre: " . $proyecto->getNombre() . " - Estado: " . $proyecto->getEstado() . "\n";
            }
        } else {
            echo "No hay proyectos disponibles.\n";
        }
    }
    
      
     public function agregarProyecto($proyecto) {
        $this->proyectos[] = $proyecto;
    }
      
      /*public function listarTareasPorProyecto($id_proyecto) {
        $this->cargarDesdeJson();
        $proyecto = null;
        foreach ($this->proyectos as $p) {
            if ($p->getId_proyecto() == $id_proyecto) {
                $proyecto = $p;
                break;
            }
        }

        if (!$proyecto) {
            echo "Proyecto con ID {$id_proyecto} no encontrado.\n";
            return;
        }

       
        $tareasProyecto = $this->gestorTarea->getTareasPorProyecto($id_proyecto);
        
        if (empty($tareasProyecto)) {
            echo "No hay tareas asociadas a este proyecto.\n";
            return;
        }

        echo "=== Tareas del Proyecto: {$proyecto->getNombre()} ===\n";
        foreach ($tareasProyecto as $tarea) {
            echo "ID Tarea: {$tarea->getIdTarea()}\n";
            echo "Nombre: {$tarea->getNombre()}\n";
            echo "Descripción: {$tarea->getDescripcion()}\n";
            echo "Fecha de Inicio: {$tarea->getFechaInicio()->format('Y-m-d')}\n";
            echo "Fecha de Fin: {$tarea->getFechaFin()->format('Y-m-d')}\n";
            echo "-------------------------\n";
        }
    }*/
    public function listarTareasPorProyecto($id_proyecto) {
        $proyecto = $this->buscarProyectoPorId($id_proyecto);  // buscamos el proyecto por id en memoria 
    
        if (!$proyecto) {
            echo "Proyecto con ID {$id_proyecto} no encontrado.\n";
            return;
        }
    
        $tareasProyecto = $proyecto->getTareas();  // para recuperar las tareas
    
        if (empty($tareasProyecto)) {
            echo "No hay tareas asociadas a este proyecto.\n";
            return;
        }
    
        echo "=== Tareas del Proyecto: {$proyecto->getNombre()} ===\n";
        foreach ($tareasProyecto as $tarea) {
            echo "ID Tarea: {$tarea->getIdTarea()}\n";
            echo "Nombre: {$tarea->getNombre()}\n";
            echo "Descripción: {$tarea->getDescripcion()}\n";
            echo "Fecha de Inicio: {$tarea->getFechaInicio()->format('Y-m-d')}\n";
            echo "Fecha de Fin: {$tarea->getFechaFin()->format('Y-m-d')}\n";
            echo "-------------------------\n";
        }
    }
    
       
        
     public function buscarProyectoPorId($id_proyecto) {
        foreach ($this->proyectos as $proyecto) {
            if ($proyecto->getId_proyecto() == $id_proyecto) {
                return $proyecto;
            }
        }
        return null;  
    }


   
    public function editarProyecto($id_proyecto) {
        $proyecto = null;
        foreach ($this->proyectos as $p) {
            if ($p->getId_proyecto() == $id_proyecto) {
                $proyecto = $p;
                break;
            }
        }
    
        if (!$proyecto) {
            echo "Proyecto con ID {$id_proyecto} no encontrado.\n";
            return;
        }
    
        echo "Proyecto encontrado:\n";
        echo "ID: {$proyecto->getId_proyecto()}\n";
        echo "Nombre: {$proyecto->getNombre()}\n";
        echo "Descripción: {$proyecto->getDescripcion()}\n";
        echo "Fecha de Inicio: {$proyecto->getFechaInicio()->format('Y-m-d')}\n";
        echo "Fecha de Fin: {$proyecto->getFechaFin()->format('Y-m-d')}\n";
        echo "Estado: {$proyecto->getEstado()}\n";
    
       
        echo "¿Qué campo desea editar?\n";
        echo "1. Nombre\n";
        echo "2. Descripción\n";
        echo "3. Fecha de Inicio\n";
        echo "4. Fecha de Fin\n";
        echo "5. Estado\n";
        echo "0. Volver\n";
        
        $opcion = trim(fgets(STDIN));
    
        switch ($opcion) {
            case '1':
                echo "Ingrese el nuevo nombre del proyecto: ";
                $nuevoNombre = trim(fgets(STDIN));
                $proyecto->setNombre($nuevoNombre);
                echo "Nombre actualizado.\n";
                break;
            case '2':
                echo "Ingrese la nueva descripción del proyecto: ";
                $nuevaDescripcion = trim(fgets(STDIN));
                $proyecto->setDescripcion($nuevaDescripcion);
                echo "Descripción actualizada.\n";
                break;
            case '3':
                echo "Ingrese la nueva fecha de inicio (formato: Y-m-d): ";
                $nuevaFechaInicio = trim(fgets(STDIN));
                $proyecto->setFechaInicio(new DateTime($nuevaFechaInicio));
                echo "Fecha de inicio actualizada.\n";
                break;
            case '4':
                echo "Ingrese la nueva fecha de fin (formato: Y-m-d): ";
                $nuevaFechaFin = trim(fgets(STDIN));
                $proyecto->setFechaFin(new DateTime($nuevaFechaFin));
                echo "Fecha de fin actualizada.\n";
                break;
            case '5':
                echo "Ingrese el nuevo estado del proyecto: ";
                $nuevoEstado = trim(fgets(STDIN));
                $proyecto->setEstado($nuevoEstado);
                echo "Estado actualizado.\n";
                break;
            case '0':
                echo "Volviendo al menú anterior...\n";
                return;
            default:
                echo "Opción no válida.\n";
                break;
        }
    
        $this->guardarEnJSON();
    }
    
   
    public function eliminarProyecto($id_proyecto) {
        $indiceProyecto = null;
    
       
        foreach ($this->proyectos as $key => $proyecto) {
            if ($proyecto->getId_proyecto() == $id_proyecto) {
                $indiceProyecto = $key;
                break;
            }
        }
    
      
        if ($indiceProyecto === null) {
            echo "Proyecto con ID {$id_proyecto} no encontrado.\n";
            return;
        }
    
       
        $this->eliminarTareasAsociadas($id_proyecto);
    
       
        unset($this->proyectos[$indiceProyecto]);
        $this->proyectos = array_values($this->proyectos); 
    
        echo "Proyecto y sus tareas eliminados exitosamente.\n";
    
        
        $this->guardarEnJSON();
       
    }
    
    public function eliminarTareasAsociadas($id_proyecto) {
        
        $archivoTareas = './Json/tareas.json';
    
     
    
        
        if (!file_exists($archivoTareas)) {
            echo "El archivo tareas.json no existe.\n";
            return; 
        }
    
        
        $contenidoJson = file_get_contents($archivoTareas);
        $tareas = json_decode($contenidoJson, true);
    
       
        if ($tareas === null || !isset($tareas['tareas'])) {
            echo "No se pudo leer correctamente el archivo tareas.json o no contiene tareas.\n";
            return; 
        }
    
       
        $tareasRestantes = array_filter($tareas['tareas'], function($tarea) use ($id_proyecto) {
            return $tarea['id_proyecto'] != $id_proyecto;
        });
    
        
        file_put_contents($archivoTareas, json_encode(['tareas' => array_values($tareasRestantes)], JSON_PRETTY_PRINT));
    
        echo "Tareas asociadas al proyecto eliminadas correctamente.\n";
    }
    

    public function cargarDesdeJson() {
       
        if (file_exists($this->archivoJson)) {
            $contenidoJson = file_get_contents($this->archivoJson);
            $data = json_decode($contenidoJson, true); 
    
           
            if (isset($data['proyecto']) && is_array($data['proyecto'])) {
                $this->proyectos = []; 
    
              
                foreach ($data['proyecto'] as $proyectoData) {
                    $tareasP = [];
                    if (isset($proyectoData['tareas']) && is_array($proyectoData['tareas'])) {
                        foreach ($proyectoData['tareas'] as $idTarea) {
                            $tarea = $this->gestorTarea->buscarTareaPorId($idTarea); 
                            if ($tarea) {
                                $tareasP[] = $tarea; 
                            }
                        }
                    }
    
                   
                    $this->proyectos[] = new Proyecto(
                        $proyectoData['id_proyecto'],
                        $proyectoData['nombre'],
                        $proyectoData['descripcion'],
                        new DateTime($proyectoData['fechaInicio']),
                        new DateTime($proyectoData['fechaFin']),
                        $proyectoData['estado'],
                        $tareasP 
                    );
                }
    
                
                echo "Proyectos cargados correctamente: " . count($this->proyectos) . "\n";
            } else {
                echo "No se encontró la clave 'proyecto' o está vacía en el JSON.\n";
            }
        } else {
            echo "El archivo JSON no existe.\n";
        }
    }
    
   
    
    public function guardarEnJSON() {
        $proyectos = [];

        foreach ($this->proyectos as $proyecto) {
            
            $tareasIds = [];
            foreach ($proyecto->getTareas() as $tarea) {
                $tareasIds[] = $tarea->getIdTarea(); 
            }

          
            $proyectos[] = [
                'id_proyecto' => $proyecto->getId_proyecto(),
                'nombre' => $proyecto->getNombre(),
                'descripcion' => $proyecto->getDescripcion(),
                'fechaInicio' => $proyecto->getFechaInicio()->format('Y-m-d'),
                'fechaFin' => $proyecto->getFechaFin()->format('Y-m-d'),
                'estado' => $proyecto->getEstado(),
                'tareas' => $tareasIds 
            ];
        }

      
        $jsonProyectos = json_encode(['proyecto' => $proyectos], JSON_PRETTY_PRINT);
        file_put_contents($this->archivoJson, $jsonProyectos);
    }
    public function agregarTareaAlProyecto($id_proyecto, $nuevaTarea) {
        foreach ($this->proyectos as $proyecto) {
            if ($proyecto->getId_proyecto() == $id_proyecto) {
                $proyecto->agregarTarea($nuevaTarea); 
                break;
            }
        }
         $this->guardarEnJSON();
       // $this->cargarDesdeJson();
    }
    public function eliminarTareaDeProyecto($id_proyecto, $id_tarea) {
        foreach ($this->proyectos as $proyecto) {
            if ($proyecto->getId_proyecto() == $id_proyecto) {
                $proyecto->eliminarTarea($id_tarea); 
                break;
            }
        }
        $this->guardarEnJSON();
    }
    public function actualizarFechaFinProyecto($id_proyecto) {
      
        $proyecto = null;
        foreach ($this->proyectos as $p) {
            if ($p->getId_proyecto() == $id_proyecto) {
                $proyecto = $p;
                break;
            }
        }
    
        if ($proyecto) {
           
            $fechaFinMaxima = new DateTime('1970-01-01');  
            foreach ($this->gestorTarea->getTareasPorProyecto($id_proyecto) as $tarea) {
                if ($tarea->getFechaFin() > $fechaFinMaxima) {
                    $fechaFinMaxima = $tarea->getFechaFin();
                }
            }
    
           
            $proyecto->setFechaFin($fechaFinMaxima);
            $this->guardarEnJSON();  
    
            echo "Fecha de fin del proyecto actualizada: " . $fechaFinMaxima->format('Y-m-d') . "\n";
        } else {
            echo "Proyecto con ID {$id_proyecto} no encontrado.\n";
        }
    }
    

   
}
<?php
require_once './clases/proyecto.php';
require_once './clases/tarea.php';  
require_once './gestores/GestorTarea.php';



class GestorProyecto {
    private $proyectos = [];
    private $archivoJson = './Json/proyecto.json';
    private $gestorTarea; // Atributo para gestionar tareas

    public function __construct($gestorTarea) {
        $this->gestorTarea = $gestorTarea; // Asignar la instancia de GestorTarea
        $this->cargarDesdeJSON();
    }
    public function setGestorTarea($gestorTarea) {
        $this->gestorTarea = $gestorTarea;
    }

    // Crear un nuevo proyecto
    public function crearProyecto() {
        $id_proyecto = count($this->proyectos) + 1;
    
        echo "Ingrese el nombre del proyecto: ";
        $nombre = trim(fgets(STDIN));
    
        echo "Ingrese la descripción del proyecto: ";
        $descripcion = trim(fgets(STDIN));
    
        // Validación de la fecha de inicio
        $fechaInicioValida = false;
        while (!$fechaInicioValida) {
            echo "Ingrese la fecha de inicio (formato: Y-m-d): ";
            $fechaInicio = trim(fgets(STDIN));
    
            // Validar que el formato sea correcto y que el año no sea inferior a 2025
            $fechaInicioObj = DateTime::createFromFormat('Y-m-d', $fechaInicio);
            if ($fechaInicioObj && $fechaInicioObj->format('Y-m-d') === $fechaInicio) {
                // Validar que el año de la fecha de inicio sea 2025 o superior
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
    
        // Validación de la fecha de fin
        $fechaFinValida = false;
        while (!$fechaFinValida) {
            echo "Ingrese la fecha de fin (formato: Y-m-d): ";
            $fechaFin = trim(fgets(STDIN));
    
            // Validar que el formato de la fecha de fin sea correcto y que no sea inferior a la de inicio
            $fechaFinObj = DateTime::createFromFormat('Y-m-d', $fechaFin);
            if ($fechaFinObj && $fechaFinObj->format('Y-m-d') === $fechaFin) {
                // Comparar la fecha de fin con la de inicio para asegurar que la fecha de fin no sea anterior
                if ($fechaFinObj >= $fechaInicioObj) {
                    $fechaFinValida = true;
                } else {
                    echo "La fecha de fin no puede ser anterior a la fecha de inicio.\n";
                }
            } else {
                echo "El formato de fecha ingresado no es válido. Debe ser Y-m-d. Ejemplo: 2025-02-08.\n";
            }
        }
    
        // Asignar "activo" por defecto sin pedir al usuario
        $estado = 'activo';  // Estado asignado automáticamente
    
        // Crear un nuevo proyecto
        $nuevoProyecto = new Proyecto($id_proyecto, $nombre, $descripcion, $fechaInicio, $fechaFin, $estado);
    
        // Almacenar el nuevo proyecto en el array
        $this->proyectos[] = $nuevoProyecto;
    
        echo "Proyecto creado exitosamente: " . $nuevoProyecto->getNombre() . " con ID " . $nuevoProyecto->getId_proyecto() . "\n";
    
        $this->guardarEnJSON();
    }
    
    
 
    public function listarProyectosPorId() {
      //  var_dump($this->proyectos); // Verifica el contenido de $proyectos
        if (count($this->proyectos) > 0) {
          //  echo "Listando proyectos por ID...\n";
            foreach ($this->proyectos as $proyecto) {
                echo "ID: " . $proyecto->getId_proyecto() . " - Nombre: " . $proyecto->getNombre() . "\n";
            }
        } else {
            echo "No hay proyectos disponibles.\n";
        }
    }
    public function listarProyectosPorNombre() {
       // echo "Listando proyectos por Nombre...\n";
        
        // Ordenar los proyectos por nombre
        usort($this->proyectos, function($a, $b) {
            return strcmp($a->getNombre(), $b->getNombre());
        });
    
        // Mostrar los proyectos ordenados
        if (count($this->proyectos) > 0) {
            foreach ($this->proyectos as $proyecto) {
                echo "ID: " . $proyecto->getId_proyecto() . " - Nombre: " . $proyecto->getNombre() . "\n";
            }
        } else {
            echo "No hay proyectos disponibles.\n";
        }
    }
    public function listarProyectosPorFechaInicio() {
       // echo "Listando proyectos por Fecha de Inicio...\n";
        
        // Ordenar los proyectos por fecha de inicio
        usort($this->proyectos, function($a, $b) {
            return $a->getFechaInicio() <=> $b->getFechaInicio(); // Comparar fechas
        });
    
        // Mostrar los proyectos ordenados
        if (count($this->proyectos) > 0) {
            foreach ($this->proyectos as $proyecto) {
                echo "ID: " . $proyecto->getId_proyecto() . " - Nombre: " . $proyecto->getNombre() . " - Fecha de Inicio: " . $proyecto->getFechaInicio()->format('Y-m-d') . "\n";
            }
        } else {
            echo "No hay proyectos disponibles.\n";
        }
    }
    public function listarProyectosPorFechaFin() {
       // echo "Listando proyectos por Fecha de Fin...\n";
        
        // Ordenar los proyectos por fecha de fin
        usort($this->proyectos, function($a, $b) {
            return $a->getFechaFin() <=> $b->getFechaFin(); // Comparar fechas
        });
    
        // Mostrar los proyectos ordenados
        if (count($this->proyectos) > 0) {
            foreach ($this->proyectos as $proyecto) {
                echo "ID: " . $proyecto->getId_proyecto() . " - Nombre: " . $proyecto->getNombre() . " - Fecha de Fin: " . $proyecto->getFechaFin()->format('Y-m-d') . "\n";
            }
        } else {
            echo "No hay proyectos disponibles.\n";
        }
    }

    public function listarProyectosPorEstado() {
       // echo "Listando proyectos por Estado...\n";
        
        // Ordenar los proyectos por estado
        usort($this->proyectos, function($a, $b) {
            return strcmp($a->getEstado(), $b->getEstado()); // Comparar estados
        });
    
        // Mostrar los proyectos ordenados
        if (count($this->proyectos) > 0) {
            foreach ($this->proyectos as $proyecto) {
                echo "ID: " . $proyecto->getId_proyecto() . " - Nombre: " . $proyecto->getNombre() . " - Estado: " . $proyecto->getEstado() . "\n";
            }
        } else {
            echo "No hay proyectos disponibles.\n";
        }
    }
    
      // Agregar un proyecto al gestor
     public function agregarProyecto($proyecto) {
        $this->proyectos[] = $proyecto;
    }
      // Listar tareas de un proyecto
      public function listarTareasPorProyecto($id_proyecto) {
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

        // Usamos el GestorTarea para obtener las tareas asociadas a este proyecto
        $tareas = $this->gestorTarea->getTareasPorProyecto($id_proyecto);
        
        if (empty($tareas)) {
            echo "No hay tareas asociadas a este proyecto.\n";
            return;
        }

        echo "=== Tareas del Proyecto: {$proyecto->getNombre()} ===\n";
        foreach ($tareas as $tarea) {
            echo "ID Tarea: {$tarea->getIdTarea()}\n";
            echo "Nombre: {$tarea->getNombre()}\n";
            echo "Descripción: {$tarea->getDescripcion()}\n";
            echo "Fecha de Inicio: {$tarea->getFechaInicio()->format('Y-m-d')}\n";
            echo "Fecha de Fin: {$tarea->getFechaFin()->format('Y-m-d')}\n";
            echo "-------------------------\n";
        }
    }
       
         // Método privado para buscar un proyecto por su ID
     public function buscarProyectoPorId($id_proyecto) {
        foreach ($this->proyectos as $proyecto) {
            if ($proyecto->getId_proyecto() == $id_proyecto) {
                return $proyecto;
            }
        }
        return null;  // Si no se encuentra el proyecto
    }


    // Editar un proyecto
    public function editarProyecto($id_proyecto) {
        // Buscar el proyecto con el ID proporcionado
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
    
        // Mostrar los detalles actuales del proyecto
        echo "Proyecto encontrado:\n";
        echo "ID: {$proyecto->getId_proyecto()}\n";
        echo "Nombre: {$proyecto->getNombre()}\n";
        echo "Descripción: {$proyecto->getDescripcion()}\n";
        echo "Fecha de Inicio: {$proyecto->getFechaInicio()->format('Y-m-d')}\n";
        echo "Fecha de Fin: {$proyecto->getFechaFin()->format('Y-m-d')}\n";
        echo "Estado: {$proyecto->getEstado()}\n";
    
        // Preguntar qué campo desea editar
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
    
        // Guardar los cambios en el archivo JSON
        $this->guardarEnJSON();
    }
    
   
    public function eliminarProyecto($id_proyecto) {
        $indiceProyecto = null;
    
        // Buscar el proyecto
        foreach ($this->proyectos as $key => $proyecto) {
            if ($proyecto->getId_proyecto() == $id_proyecto) {
                $indiceProyecto = $key;
                break;
            }
        }
    
        // Verificar si el proyecto existe
        if ($indiceProyecto === null) {
            echo "Proyecto con ID {$id_proyecto} no encontrado.\n";
            return;
        }
    
        // Eliminar las tareas asociadas al proyecto
        $this->eliminarTareasAsociadas($id_proyecto);
    
        // Eliminar el proyecto del array
        unset($this->proyectos[$indiceProyecto]);
        $this->proyectos = array_values($this->proyectos); // Reindexar el array
    
        echo "Proyecto y sus tareas eliminados exitosamente.\n";
    
        // Guardar los cambios en los archivos JSON
        $this->guardarEnJSON();
       
    }
    
    public function eliminarTareasAsociadas($id_proyecto) {
        // Ruta del archivo tareas.json
        $archivoTareas = './Json/tareas.json';
    
     
    
        // Verificar si el archivo tareas.json existe
        if (!file_exists($archivoTareas)) {
            echo "El archivo tareas.json no existe.\n";
            return; // Salir de la función si el archivo no existe
        }
    
        // Cargar las tareas desde el archivo tareas.json
        $contenidoJson = file_get_contents($archivoTareas);
        $tareas = json_decode($contenidoJson, true);
    
        // Verificar si el JSON contiene la clave 'tareas' y que no sea null
        if ($tareas === null || !isset($tareas['tareas'])) {
            echo "No se pudo leer correctamente el archivo tareas.json o no contiene tareas.\n";
            return; // Salir si el archivo no contiene tareas válidas
        }
    
        // Filtrar las tareas que no pertenezcan al proyecto que estamos eliminando
        $tareasRestantes = array_filter($tareas['tareas'], function($tarea) use ($id_proyecto) {
            return $tarea['id_proyecto'] != $id_proyecto;
        });
    
        // Guardar las tareas restantes en el archivo tareas.json
        file_put_contents($archivoTareas, json_encode(['tareas' => array_values($tareasRestantes)], JSON_PRETTY_PRINT));
    
        echo "Tareas asociadas al proyecto eliminadas correctamente.\n";
    }
    
    
    
    
    
    

    public function cargarDesdeJson() {
        // Cargar proyectos desde el archivo JSON
        if (file_exists($this->archivoJson)) {
            $contenidoJson = file_get_contents($this->archivoJson);
            $data = json_decode($contenidoJson, true); // Decodificar JSON en un array asociativo
    
            // Verifica si la clave 'proyecto' existe y si tiene datos
            if (isset($data['proyecto']) && is_array($data['proyecto'])) {
                $this->proyectos = []; // Vaciar proyectos actuales
    
                // Iterar sobre los proyectos en el JSON y cargarlos correctamente
                foreach ($data['proyecto'] as $proyectoData) {
                    // Verificar si el proyecto tiene tareas y cargarlas correctamente
                    $tareas = [];
                    if (isset($proyectoData['tareas']) && is_array($proyectoData['tareas'])) {
                        foreach ($proyectoData['tareas'] as $idTarea) {
                            $tarea = $this->gestorTarea->buscarTareaPorId($idTarea); // Buscar tarea por ID
                            if ($tarea) {
                                $tareas[] = $tarea; // Asignar la tarea al proyecto
                            }
                        }
                    }
    
                    // Crear el objeto Proyecto, pasando las tareas cargadas
                    $this->proyectos[] = new Proyecto(
                        $proyectoData['id_proyecto'],
                        $proyectoData['nombre'],
                        $proyectoData['descripcion'],
                        new DateTime($proyectoData['fechaInicio']),
                        new DateTime($proyectoData['fechaFin']),
                        $proyectoData['estado'],
                        $tareas // Pasar las tareas como un array de objetos Tarea
                    );
                }
    
                // Depuración: Verifica que proyectos se hayan cargado
                echo "Proyectos cargados correctamente: " . count($this->proyectos) . "\n";
            } else {
                echo "No se encontró la clave 'proyecto' o está vacía en el JSON.\n";
            }
        } else {
            echo "El archivo JSON no existe.\n";
        }
    }
    
   
    // Guardar los proyectos en el archivo JSON
    public function guardarEnJSON() {
        $proyectos = [];

        foreach ($this->proyectos as $proyecto) {
            // Obtener solo los IDs de las tareas asociadas
            $tareasIds = [];
            foreach ($proyecto->getTareas() as $tarea) {
                $tareasIds[] = $tarea->getIdTarea(); // Obtener el ID de la tarea
            }

            // Convertir cada proyecto a un array
            $proyectos[] = [
                'id_proyecto' => $proyecto->getId_proyecto(),
                'nombre' => $proyecto->getNombre(),
                'descripcion' => $proyecto->getDescripcion(),
                'fechaInicio' => $proyecto->getFechaInicio()->format('Y-m-d'),
                'fechaFin' => $proyecto->getFechaFin()->format('Y-m-d'),
                'estado' => $proyecto->getEstado(),
                'tareas' => $tareasIds // Guardar solo los IDs de las tareas
            ];
        }

        // Convertir el array de proyectos a JSON y guardarlo en el archivo
        $jsonProyectos = json_encode(['proyecto' => $proyectos], JSON_PRETTY_PRINT);
        file_put_contents($this->archivoJson, $jsonProyectos);
    }
    public function agregarTareaAlProyecto($id_proyecto, $nuevaTarea) {
        foreach ($this->proyectos as $proyecto) {
            if ($proyecto->getId_proyecto() == $id_proyecto) {
                $proyecto->agregarTarea($nuevaTarea); // Usar un método para agregar la tarea al proyecto
                break;
            }
        }
    
        // Guardar el archivo 'proyecto.json' actualizado
        $this->guardarEnJSON();
    }
    public function eliminarTareaDeProyecto($id_proyecto, $id_tarea) {
        foreach ($this->proyectos as $proyecto) {
            if ($proyecto->getId_proyecto() == $id_proyecto) {
                $proyecto->eliminarTarea($id_tarea); // Método para eliminar la tarea del proyecto
                break;
            }
        }
    
        // Guardar el archivo 'proyecto.json' actualizado
        $this->guardarEnJSON();
    }
    public function actualizarFechaFinProyecto($id_proyecto) {
        // Buscar el proyecto
        $proyecto = null;
        foreach ($this->proyectos as $p) {
            if ($p->getId_proyecto() == $id_proyecto) {
                $proyecto = $p;
                break;
            }
        }
    
        if ($proyecto) {
            // Recalcular la fecha de fin del proyecto según las tareas
            $fechaFinMaxima = new DateTime('1970-01-01');  // Fecha mínima posible
            foreach ($this->gestorTarea->getTareasPorProyecto($id_proyecto) as $tarea) {
                if ($tarea->getFechaFin() > $fechaFinMaxima) {
                    $fechaFinMaxima = $tarea->getFechaFin();
                }
            }
    
            // Actualizar la fecha de fin del proyecto
            $proyecto->setFechaFin($fechaFinMaxima);
            $this->guardarEnJSON();  // Guardar cambios en proyecto.json
    
            echo "Fecha de fin del proyecto actualizada: " . $fechaFinMaxima->format('Y-m-d') . "\n";
        } else {
            echo "Proyecto con ID {$id_proyecto} no encontrado.\n";
        }
    }
    

   
}
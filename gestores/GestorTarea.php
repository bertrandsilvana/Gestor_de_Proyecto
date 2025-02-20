<?php
        require_once './clases/tarea.php';
        class GestorTarea {
            private $tareas = [];
            private $archivoJsonTareas = './Json/tareas.json';
            private $gestorProyecto;  
        
             public function __construct($gestorProyecto) {
                $this->gestorProyecto = $gestorProyecto;
                $this->cargarTareaDesdeJson($this->archivoJsonTareas);
             }

             public function setGestorProyecto($gestorProyecto) {
                $this->gestorProyecto = $gestorProyecto;
            }
        

      
             public function cargarTareaDesdeJson() {
                if (file_exists($this->archivoJsonTareas)) {
                    $contenidoJson = file_get_contents($this->archivoJsonTareas);
                    $data = json_decode($contenidoJson, true);
                    if (isset($data['tareas'])) {
                        foreach ($data['tareas'] as $tareaData) {
                            $this->tareas[] = Tarea::fromArray($tareaData); // Crear tarea a partir del array
                        }
                    }
                }
                return $this->tareas; 
            }

            public function buscarTareaPorId($id_tarea) {
                    foreach ($this->tareas as $tarea) {
                        if ($tarea->getIdTarea() == $id_tarea) {
                            return $tarea;
                        }
                    }
                    return null; // Si no  encuentra la tarea
                }


    
            public function obtenerNuevoIdTarea() {
               
                $tareasExistentes = $this->cargarTareaDesdeJson(); 
            
                // Busca el ID más alto entre las tareas
                $maxId = 0;
                foreach ($tareasExistentes as $tareaData) {
                    $idTarea = $tareaData->getIdTarea();  
                    if ($idTarea > $maxId) {
                        $maxId = $idTarea;
                    }
                }
            
                return $maxId + 1; // El nuevo ID es el siguiente número disponible
            }

            public function guardarTareaEnJson($tarea) {
                $tareasData = [];
            
                if (file_exists($this->archivoJsonTareas)) {
                    $contenidoJson = file_get_contents($this->archivoJsonTareas);
                    $data = json_decode($contenidoJson, true);
                    if (isset($data['tareas'])) {
                        foreach ($data['tareas'] as $tareaData) {
                            // Si la tarea coincide con la que estamos actualizando, se actualiza
                            if ($tareaData['id_tarea'] == $tarea->getIdTarea()) {
                                $tareasData[] = $tarea->toArray();
                            } else {
                                $tareasData[] = $tareaData;
                            }
                        }
                    }
                }
            
                // Si la tarea no se encontraba previamente, se agrega
                $idsExistentes = array_column($tareasData, 'id_tarea');
                if (!in_array($tarea->getIdTarea(), $idsExistentes)) {
                    $tareasData[] = $tarea->toArray();
                }
            
                file_put_contents($this->archivoJsonTareas, json_encode(['tareas' => $tareasData], JSON_PRETTY_PRINT));
            }

       
          
              public function crearTarea($gestorProyecto) {

                
                echo "Ingrese el ID del proyecto al que pertenece la tarea: ";
                $id_proyecto = trim(fgets(STDIN));
            
              
                $proyecto = $gestorProyecto->buscarProyectoPorId($id_proyecto);
                if ($proyecto === null) {
                    echo "El proyecto con ID $id_proyecto no existe. Por favor, ingrese un ID válido.\n";
                    return;  
                }
            
                echo "¿La tarea tiene dependencias? (sí/no): ";
                $respuesta = trim(fgets(STDIN));
            
                $dependencias = [];
                if (strtolower($respuesta) == "sí" || strtolower($respuesta) == "si") {
                    echo "Ingrese los IDs de las tareas de las cuales depende (separados por comas): ";
                    $dependencias = explode(",", trim(fgets(STDIN)));  // Convertimos a array y eliminamos espacios en blanco
                    $dependencias = array_map('trim', $dependencias); 
                }
            
                //-------------------------------------------------------------------------------------
            
                if (count($dependencias) > 0) {
                    
                    $fechaInicio = $this->obtenerFechaDeFinDeUltimaDependencia($dependencias, $gestorProyecto);
                } else {
                   
                    $fechaInicioProyecto = $proyecto->getFechaInicio(); 
                    $fechaFinProyecto = $proyecto->getFechaFin();  
                    
                    // Verificar que las fechas sean válidas
                    if (!$fechaInicioProyecto instanceof DateTime || !$fechaFinProyecto instanceof DateTime) {
                        echo "Error: Las fechas del proyecto no son válidas.\n";
                        return;
                    }
                
                  
                    do {
                        echo "Ingrese la fecha de inicio de la tarea (formato: Y-m-d): ";
                        $fechaInicioInput = trim(fgets(STDIN));
                
                        // Validar que la fecha esté en el formato correcto (Y-m-d)
                        $fechaInicio = DateTime::createFromFormat('Y-m-d', $fechaInicioInput);
                
                        // Comprobar si la fecha no es válida
                        if (!$fechaInicio || $fechaInicio->format('Y-m-d') !== $fechaInicioInput) {
                            echo "La fecha ingresada no tiene un formato válido. Debe ser Y-m-d (por ejemplo: 2025-02-18).\n";
                            continue;  // Solicitar de nuevo la fecha
                        }
                
                        // Verificar que la fecha de inicio esté dentro del rango del proyecto
                        if ($fechaInicio < $fechaInicioProyecto) {
                            echo "La fecha de inicio de la tarea no puede ser anterior a la fecha de inicio del proyecto ({$fechaInicioProyecto->format('Y-m-d')}).\n";
                        } elseif ($fechaInicio > $fechaFinProyecto) {
                            echo "La fecha de inicio de la tarea no puede ser posterior a la fecha de fin del proyecto ({$fechaFinProyecto->format('Y-m-d')}).\n";
                        }
                
                    } while ($fechaInicio < $fechaInicioProyecto || $fechaInicio > $fechaFinProyecto);
                }
            
                //-------------------------------------------------------------------------------------
            
            
                echo "Ingrese el nombre de la tarea: ";
                $nombre = trim(fgets(STDIN));
            
                echo "Ingrese la descripción de la tarea: ";
                $descripcion = trim(fgets(STDIN));
            
                
                
                do {
                    echo "Ingrese la duración de la tarea en días: ";
                    $duracion = trim(fgets(STDIN));
            
                    
                    
                    if (!is_numeric($duracion) || $duracion <= 0) {
                        echo "La duración debe ser un número positivo.\n";
                    }
                } while (!is_numeric($duracion) || $duracion <= 0);
            
                
                $fechaFin = clone $fechaInicio;  // Crear una copia de la fecha de inicio
                $fechaFin->modify("+$duracion days");
            
                echo "La fecha de fin calculada para la tarea es: " . $fechaFin->format('Y-m-d') . "\n";
            
               
                $idTarea = $this->obtenerNuevoIdTarea();  // Método para obtener el próximo ID disponible
                $nuevaTarea = new Tarea($idTarea, $nombre, $descripcion, $fechaInicio, $fechaFin, $id_proyecto, $dependencias);
            
             
                $this->guardarTareaEnJson($nuevaTarea);
            
              
                $gestorProyecto->agregarTareaAlProyecto($id_proyecto, $nuevaTarea);
            
                echo "Tarea creada exitosamente: " . $nuevaTarea->getNombre() . " con ID " . $nuevaTarea->getIdTarea() . "\n";
            }

            
           
            
          
             public function obtenerFechaDeFinDeUltimaDependencia($dependencias, $gestorProyecto) {
                
                $ultimaFechaFin = null;
        
                foreach ($dependencias as $idDependencia) {
                    // Busca tarea en el gestor de tareas
                    $tareaDependiente = $this->buscarTareaPorId($idDependencia);
                    if ($tareaDependiente === null) {
                        echo "La tarea con ID $idDependencia no existe. Verifique las dependencias.\n";
                        return null;  // Si la tarea dependiente no existe
                    }
        
           
                    $fechaFinDependiente = $tareaDependiente->getFechaFin();
            
                    if ($ultimaFechaFin === null || $fechaFinDependiente > $ultimaFechaFin) {
                        $ultimaFechaFin = $fechaFinDependiente;
                    }
                }
        
                $fechaInicio = clone $ultimaFechaFin;
                $fechaInicio->modify('+1 day');  
        
                echo "La fecha de inicio de la nueva tarea será: " . $fechaInicio->format('Y-m-d') . "\n";
                
                return $fechaInicio;
            }
          
            
            public function obtenerTodasLasTareas() {
                return $this->tareas;
            }
            

            public function editarTarea($id_tarea) {
                $tarea = $this->buscarTareaPorId($id_tarea);
            
                if (!$tarea) {
                    echo "Tarea con ID {$id_tarea} no encontrada.\n";
                    return;
                }
            
                echo "Tarea encontrada: {$tarea->getNombre()}\n";
                echo "¿Qué campo deseas editar?\n";
                echo "1. Nombre\n";
                echo "2. Descripción\n";
                echo "3. Duración en días\n";  
                echo "3. Duración en días\n";  
                echo "4. Dependencias\n";
                echo "0. Volver\n";
            
                $opcion = trim(fgets(STDIN));
            
                switch ($opcion) {
                    case '1':
                        echo "Ingrese el nuevo nombre de la tarea: ";
                        $nombre = trim(fgets(STDIN));
                        $tarea->setNombre($nombre);
                        break;
            
                    case '2':
                        echo "Ingrese la nueva descripción de la tarea: ";
                        $descripcion = trim(fgets(STDIN));
                        $tarea->setDescripcion($descripcion);
                        break;
            
                    case '3':  
                    case '3':  
                        echo "Ingrese la nueva duración de la tarea en días: ";
                        $duracion = trim(fgets(STDIN));
            
                       
                        $fecha_fin = clone $tarea->getFechaInicio();  
                        $fecha_fin->modify("+$duracion days");
                        $tarea->setFechaFin($fecha_fin);
            
                        // Verificar si las tareas siguientes se ven afectadas
                        $this->verificarYActualizarTareasSiguientes($tarea, $duracion);
            
                        // Verificamos si el cambio afecta el camino crítico y la fecha de fin del proyecto
                        
                        $this->calcularCaminoCritico($tarea->getIdProyecto());
                        break;
            
                    case '4':
                        echo "Ingrese el ID de la tarea dependiente: ";
                        $id_dependencia = trim(fgets(STDIN));
                        $tarea->agregarDependencia($id_dependencia);
                        break;
            
                    case '0':
                        return;
            
                    default:
                        echo "Opción no válida.\n";
                        break;
                }
            
                // Verificar si la fecha de fin del proyecto se ve afectada por la tarea
                $this->actualizarFechaFinProyecto($tarea->getIdProyecto());
            
               
                $this->guardarTareaEnJson($tarea);
                echo "Tarea actualizada.\n";
            }
            
            // Método para verificar y actualizar las tareas siguientes que dependen de la tarea editada
            public function verificarYActualizarTareasSiguientes($tarea, $nuevaDuracion) {
                 // Obtener las tareas del mismo proyecto
                 $tareasProyecto = $this->getTareasPorProyecto($tarea->getIdProyecto());
                
                foreach ($tareasProyecto as $tareaSiguiente) {
                    // Verificar si la tarea siguiente depende de la tarea modificada
                    if (in_array($tarea->getIdTarea(), $tareaSiguiente->getDependencias())) {
                        // Recalcular la fecha de inicio de la tarea siguiente
                        $fecha_inicio_siguiente = clone $tarea->getFechaFin();  
                        $tareaSiguiente->setFechaInicio($fecha_inicio_siguiente);
                        
                        // Calcular la nueva fecha de fin para la tarea siguiente
                        $duracion_siguiente = $tareaSiguiente->getDuracion();  // Obtener la duración de la tarea siguiente
                        $fecha_fin_siguiente = clone $fecha_inicio_siguiente;  
                        $fecha_fin_siguiente->modify("+$duracion_siguiente days");
                        $tareaSiguiente->setFechaFin($fecha_fin_siguiente);
            
                        // Preguntar al usuario si desea confirmar o modificar la fecha de la tarea siguiente
                        echo "La tarea siguiente {$tareaSiguiente->getNombre()} se ve afectada por este cambio. Nueva fecha de inicio: " . $fecha_inicio_siguiente->format('Y-m-d') . ", nueva fecha de fin: " . $fecha_fin_siguiente->format('Y-m-d') . ". ¿Desea confirmar este cambio? (sí/no): ";
                        $respuesta = trim(fgets(STDIN));
                        if (strtolower($respuesta) === "no") {
                            echo "Ingrese la nueva fecha de inicio para {$tareaSiguiente->getNombre()} (formato: Y-m-d): ";
                            $fecha_inicio_input = trim(fgets(STDIN));
                            $fecha_inicio_siguiente = new DateTime($fecha_inicio_input);
                            $tareaSiguiente->setFechaInicio($fecha_inicio_siguiente);
                            
                            // Recalcular la fecha de fin de la tarea siguiente
                            $fecha_fin_siguiente = clone $fecha_inicio_siguiente;
                            $fecha_fin_siguiente->modify("+$duracion_siguiente days");
                            $tareaSiguiente->setFechaFin($fecha_fin_siguiente);
                        }
                    }
                }
            } 
           
            
               
            

         
            public function validarFecha($fecha) {
                // Verificar si la fecha es válida en formato Y-m-d usando regex
                $patron = '/^\d{4}-\d{2}-\d{2}$/';
                return preg_match($patron, $fecha) === 1;
            }
            public function verificarYActualizarCaminoCritico($tarea) {
                echo "=== Recalculando el camino crítico ===\n";
                
                // Variable que indica si el camino crítico ha cambiado
                $camino_critico_afectado = false;
            
            
                foreach ($this->getTareasPorProyecto($tarea->getIdProyecto()) as $tarea_comparada) {
                    if ($tarea_comparada->getFechaFin() > $tarea->getFechaInicio()) {
                        echo "La fecha de una tarea ha afectado el camino crítico.\n";
                        $camino_critico_afectado = true;
                        break;
                    }
                }
            
                // Si alguna tarea afectó el camino crítico, se procede a actualizar el camino
                if ($camino_critico_afectado) {
                
                    $this->calcularCaminoCritico($id_proyecto);
                }
            }
            
            
            
             public function actualizarFechaFinProyecto($id_proyecto) {
                    
                    $tareasDelProyecto = $this->getTareasPorProyecto($id_proyecto);
                    
                    // Ordenamos las tareas por la fecha de fin (de la más tarde a la más temprana)
                    usort($tareasDelProyecto, function($a, $b) {
                        return $a->getFechaFin() <=> $b->getFechaFin();
                    });
                    
                    // La última tarea en la lista será la que determine la fecha de finalización
                    $ultimaTarea = end($tareasDelProyecto);
                    
                    // Actualizar la fecha de finalización del proyecto
                    if ($ultimaTarea) {
                        // Se obtiene la fecha de fin de la última tarea
                        $nuevaFechaFinProyecto = $ultimaTarea->getFechaFin();
                        echo "Fecha de finalización del proyecto actualizada: " . $nuevaFechaFinProyecto->format('Y-m-d') . "\n";
                    }
                    
                    
                }   

                            
        
            public function eliminarTarea($id_tarea) {
                $tareaEliminada = null;
                
                foreach ($this->tareas as $key => $tarea) {
                    if ($tarea->getIdTarea() == $id_tarea) {
                        $tareaEliminada = $tarea;
                        unset($this->tareas[$key]);
                        // Reindexa
                         $this->tareas = array_values($this->tareas);
                        $this->guardarTodasLasTareasEnJson();
                        echo "Tarea eliminada con éxito.\n";
                        break;
                    }
                }
            
                if ($tareaEliminada) {
                    // También actualiza el proyecto correspondiente en proyecto.json
                    $this->gestorProyecto->eliminarTareaDeProyecto($tareaEliminada->getIdProyecto(), $id_tarea);
                } else {
                    echo "Tarea con ID {$id_tarea} no encontrada.\n";
                }
            }
            

       
            public function guardarTodasLasTareasEnJson() {
                $tareasData = [];
            
                // Agregar las tareas actuales al array de tareas
                foreach ($this->tareas as $tarea) {
                    $tareasData[] = [
                        'id_tarea' => $tarea->getIdTarea(),
                        'nombre' => $tarea->getNombre(),
                        'descripcion' => $tarea->getDescripcion(),
                        'fecha_inicio' => $tarea->getFechaInicio()->format('Y-m-d'),
                        'fecha_fin' => $tarea->getFechaFin()->format('Y-m-d'),
                        'id_proyecto' => $tarea->getIdProyecto(),
                        'dependencias' => $tarea->getDependencias(),
                    ];
                }
            
                // Guardar el array de tareas actualizado en tareas.json
                file_put_contents($this->archivoJsonTareas, json_encode(['tareas' => $tareasData], JSON_PRETTY_PRINT));
            }

            public function calcularCaminoCritico($id_proyecto) {
                // Obtener las tareas del proyecto
                $tareasProyecto = $this->getTareasPorProyecto($id_proyecto);
                
                if (empty($tareasProyecto)) {
                    echo "No hay tareas asociadas a este proyecto.\n";
                    return;
                }
            
                // Mostrar la lista de tareas existentes
                echo "=== Lista de Tareas ===\n";
                foreach ($tareasProyecto as $tarea) {
                    echo "ID: " . $tarea->getIdTarea() . ", Nombre: " . $tarea->getNombre() . ", Fecha Inicio: " . $tarea->getFechaInicio()->format('Y-m-d') . ", Fecha Fin: " . $tarea->getFechaFin()->format('Y-m-d') . "\n";
                }
            
                // Ordenar las tareas por fecha de inicio
                usort($tareasProyecto, function($a, $b) {
                    return $a->getFechaInicio() <=> $b->getFechaInicio();
                });
            
                // Determinar el orden de ejecución respetando las dependencias
                $ordenTareas = $this->ordenarTareasPorDependencias($tareasProyecto);
            
                // Calcular el camino crítico dentro de la misma función
                echo "=== Camino Crítico ===\n";
                foreach ($ordenTareas as $tarea) {
                    echo "Tarea: " . $tarea->getNombre() . ", Fecha Inicio: " . $tarea->getFechaInicio()->format('Y-m-d') . ", Fecha Fin: " . $tarea->getFechaFin()->format('Y-m-d') . "\n";
                }
            }
            
            // Método para ordenar las tareas considerando sus dependencias
            public function ordenarTareasPorDependencias($tareas) {
                $tareasOrdenadas = [];  // Almacena el orden correcto de ejecución
                $tareasPendientes = $tareas;  // Lista de tareas sin procesar
            
                // Procesar las tareas mientras haya tareas pendientes
                while (!empty($tareasPendientes)) {
                    $tareasProcesadasEnEstaIteracion = false;  // Flag para verificar si procesamos alguna tarea
            
                    foreach ($tareasPendientes as $key => $tarea) {
                        $dependenciasCumplidas = true;
            
                        // Verificar si todas las dependencias de la tarea están resueltas
                        foreach ($tarea->getDependencias() as $dependencia) {
                            // Comprobamos si la dependencia está en el array de tareas ordenadas
                            if (!in_array($dependencia, array_map(fn($t) => $t->getIdTarea(), $tareasOrdenadas))) {
                                $dependenciasCumplidas = false;
                                break;
                            }
                        }
            
                        // Si todas las dependencias están resueltas, agregamos la tarea al orden
                        if ($dependenciasCumplidas) {
                            $tareasOrdenadas[] = $tarea;
                            unset($tareasPendientes[$key]);  // Eliminar de las tareas pendientes
                            $tareasProcesadasEnEstaIteracion = true;
                            echo "Tarea " . $tarea->getNombre() . " agregada al orden\n";  
                        }
                    }
            
                    // Si no se procesó ninguna tarea en esta iteración, significa que hay un ciclo o tareas sin dependencias resueltas
                    if (!$tareasProcesadasEnEstaIteracion) {
                        echo "Cuidado: hay dependencias no resueltas, posible ciclo en las tareas.\n";
                        break;  
                    }
                }
            
                return $tareasOrdenadas;
            }
            
            
           
      
            
            
                public function getTareasPorProyecto($id_proyecto) {
                    // Devuelve todas las tareas asociadas a un proyecto
                    return array_filter($this->tareas, function($tarea) use ($id_proyecto) {
                        return $tarea->getIdProyecto() == $id_proyecto;
                    });
                }
            }
            
        

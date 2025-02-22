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

                $this->tareas = []; // agregue esto para reiniciar la lista antes de cargarla para probar 
                if (file_exists($this->archivoJsonTareas)) {
                    $contenidoJson = file_get_contents($this->archivoJsonTareas);
                    $data = json_decode($contenidoJson, true);
                    if (isset($data['tareas'])) {
                        foreach ($data['tareas'] as $tareaData) {
                            $this->tareas[] = Tarea::fromArray($tareaData); 
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
                    return null; 
                }


    
            public function obtenerNuevoIdTarea() {
                $tareasExistentes = $this->cargarTareaDesdeJson(); 
                $maxId = 0;
                foreach ($tareasExistentes as $tareaData) {
                    $idTarea = $tareaData->getIdTarea();  
                    if ($idTarea > $maxId) {
                        $maxId = $idTarea;
                    }
                }
            
                return $maxId + 1; 
            }

           /* public function guardarTareaEnJson($tarea) {
                $tareasData = [];
            
                if (file_exists($this->archivoJsonTareas)) {
                    $contenidoJson = file_get_contents($this->archivoJsonTareas);
                    $data = json_decode($contenidoJson, true);
                    if (isset($data['tareas'])) {
                        foreach ($data['tareas'] as $tareaData) {
                            if ($tareaData['id_tarea'] == $tarea->getIdTarea()) {
                                $tareasData[] = $tarea->toArray();
                            } else {
                                $tareasData[] = $tareaData;
                            }
                        }
                    }
                }
            
               
                $idsExistentes = array_column($tareasData, 'id_tarea');
                if (!in_array($tarea->getIdTarea(), $idsExistentes)) {
                    $tareasData[] = $tarea->toArray();
                }
            
                file_put_contents($this->archivoJsonTareas, json_encode(['tareas' => $tareasData], JSON_PRETTY_PRINT));
            
            }*/
            public function guardarTareaEnJson($tarea) {
                $tareasData = [];
            
                // Si el archivo JSON existe, cargar las tareas ya guardadas
                if (file_exists($this->archivoJsonTareas)) {
                    $contenidoJson = file_get_contents($this->archivoJsonTareas);
                    $data = json_decode($contenidoJson, true);
            
                    // Verificar si existe el campo 'tareas' y cargar las tareas previas
                    if (isset($data['tareas'])) {
                        foreach ($data['tareas'] as $tareaData) {
                            $tareasData[] = $tareaData; // Cargar todas las tareas previas
                        }
                    }
                }
            
                // Verificar si la tarea ya está en el archivo (por ID)
                $idsExistentes = array_column($tareasData, 'id_tarea');
                if (!in_array($tarea->getIdTarea(), $idsExistentes)) {
                    // Si la tarea no existe, agregarla
                    $tareasData[] = $tarea->toArray();
                }
            
                // Guardar las tareas, incluida la nueva, en el archivo JSON
                file_put_contents($this->archivoJsonTareas, json_encode(['tareas' => $tareasData], JSON_PRETTY_PRINT));
            }
            
           /*     public function listarTareas() {
                if (empty($this->tareas)) {
                    echo "No hay tareas registrados.\n";
                    return;
                }

                echo "=== Tareas Registrados ===\n";
                foreach ($this->tareas as $tarea) {
                    echo "Id: " . $tarea->getIdTarea() ." Con id Proyecto " . $tarea->getIdProyecto(). "\n" ;
                }
            }*/
          /*  public function listarTareasProyecto($tareasProy) {
                if (empty($tareasProy)) {
                    echo "No hay tareas registrados.\n";
                    return;
                }

                echo "=== Tareas Registrados ===\n";
                foreach ($tareasProy as $tarea) {
                    echo "Id: " . $tarea->getIdTarea() ." Con id Proyecto " . $tarea->getIdProyecto(). "\n" ;
                }
            }*/
            
          
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
                    $dependencias = explode(",", trim(fgets(STDIN)));  
                    $dependencias = array_map('trim', $dependencias); 
                    }
                    
        //-------------------------------------------------------------------------------------
                    
                if (count($dependencias) > 0) {
                            
                    $fechaInicio = $this->obtenerFechaDeFinDeUltimaDependencia($dependencias, $gestorProyecto);
                } else {
                        
                $fechaInicioProyecto = $proyecto->getFechaInicio(); 
                $fechaFinProyecto = $proyecto->getFechaFin();  
                            
                if (!$fechaInicioProyecto instanceof DateTime || !$fechaFinProyecto instanceof DateTime) {
                    echo "Error: Las fechas del proyecto no son válidas.\n";
                    return;
                    }
                        
                        
                            do {
                                echo "Ingrese la fecha de inicio de la tarea (formato: Y-m-d): ";
                                $fechaInicioInput = trim(fgets(STDIN));
                        
                            
                                $fechaInicio = DateTime::createFromFormat('Y-m-d', $fechaInicioInput);
                        
                                
                                if (!$fechaInicio || $fechaInicio->format('Y-m-d') !== $fechaInicioInput) {
                                    echo "La fecha ingresada no tiene un formato válido. Debe ser Y-m-d (por ejemplo: 2025-02-18).\n";
                                    continue;  
                                }
                        
                            
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
                    
                        
                        $fechaFin = clone $fechaInicio; 
                        $fechaFin->modify("+$duracion days");
                    
                        echo "La fecha de fin calculada para la tarea es: " . $fechaFin->format('Y-m-d') . "\n";
                    
                    
                        $idTarea = $this->obtenerNuevoIdTarea(); 
                        $nuevaTarea = new Tarea($idTarea, $nombre, $descripcion, $fechaInicio, $fechaFin, $id_proyecto, $dependencias);
                    
                    
                        $this->guardarTareaEnJson($nuevaTarea);
                        //echo "cant de tareas antes: " . count($this->tareas) . "\n" ;
                        $this->cargarTareaDesdeJson();// vemos si funciona en teoria actualiza  la lista 

                        $this->tareas[] = $nuevaTarea;
                        //$this->listarTareas();
            
                        //echo "cant de tareas despues: " .count($this->tareas) . "\n";

                        $gestorProyecto->agregarTareaAlProyecto($id_proyecto, $nuevaTarea);

                         // probamos si estan todas las tareas 
                         $gestorProyecto->listarTareasPorProyecto($id_proyecto);

                    
                        //$this->listarTareasProyecto($this->getTareasPorProyecto($id_proyecto));
                        echo "Tarea creada exitosamente: " . $nuevaTarea->getNombre() . " con ID " . $nuevaTarea->getIdTarea() . "con ID Proyecto: " . $nuevaTarea->getIdProyecto() . "\n";
                    }

                    
                
                    
                
                    public function obtenerFechaDeFinDeUltimaDependencia($dependencias, $gestorProyecto) {
                        
                        $ultimaFechaFin = null;
                
                        foreach ($dependencias as $idDependencia) {
                            
                            $tareaDependiente = $this->buscarTareaPorId($idDependencia);
                            if ($tareaDependiente === null) {
                                echo "La tarea con ID $idDependencia no existe. Verifique las dependencias.\n";
                                return null; 
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
                    
                            
                                $this->verificarYActualizarTareasSiguientes($tarea, $duracion);
                    
                            
                                
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
                    
                    
                        $this->actualizarFechaFinProyecto($tarea->getIdProyecto());
                    
                    
                        $this->guardarTareaEnJson($tarea);
                        echo "Tarea actualizada.\n";
                    }
                    
                
                    public function verificarYActualizarTareasSiguientes($tarea, $nuevaDuracion) {
                    
                        $tareasProyecto = $this->getTareasPorProyecto($tarea->getIdProyecto());
                        
                        foreach ($tareasProyecto as $tareaSiguiente) {
                        
                            if (in_array($tarea->getIdTarea(), $tareaSiguiente->getDependencias())) {
                            
                                $fecha_inicio_siguiente = clone $tarea->getFechaFin();  
                                $tareaSiguiente->setFechaInicio($fecha_inicio_siguiente);
                                
                                
                                $duracion_siguiente = $tareaSiguiente->getDuracion(); 
                                $fecha_fin_siguiente = clone $fecha_inicio_siguiente;  
                                $fecha_fin_siguiente->modify("+$duracion_siguiente days");
                                $tareaSiguiente->setFechaFin($fecha_fin_siguiente);
                    
                            
                                echo "La tarea siguiente {$tareaSiguiente->getNombre()} se ve afectada por este cambio. Nueva fecha de inicio: " . $fecha_inicio_siguiente->format('Y-m-d') . ", nueva fecha de fin: " . $fecha_fin_siguiente->format('Y-m-d') . ". ¿Desea confirmar este cambio? (sí/no): ";
                                $respuesta = trim(fgets(STDIN));
                                if (strtolower($respuesta) === "no") {
                                    echo "Ingrese la nueva fecha de inicio para {$tareaSiguiente->getNombre()} (formato: Y-m-d): ";
                                    $fecha_inicio_input = trim(fgets(STDIN));
                                    $fecha_inicio_siguiente = new DateTime($fecha_inicio_input);
                                    $tareaSiguiente->setFechaInicio($fecha_inicio_siguiente);
                                    
                                    
                                    $fecha_fin_siguiente = clone $fecha_inicio_siguiente;
                                    $fecha_fin_siguiente->modify("+$duracion_siguiente days");
                                    $tareaSiguiente->setFechaFin($fecha_fin_siguiente);
                                }
                            }
                        }
                    } 
                
                    
                    
                    

                
                    public function validarFecha($fecha) {
                    
                        $patron = '/^\d{4}-\d{2}-\d{2}$/';
                        return preg_match($patron, $fecha) === 1;
                    }
                    public function verificarYActualizarCaminoCritico($tarea) {
                        echo "=== Recalculando el camino crítico ===\n";
                        
                        
                        $camino_critico_afectado = false;
                    
                    
                        foreach ($this->getTareasPorProyecto($tarea->getIdProyecto()) as $tarea_comparada) {
                            if ($tarea_comparada->getFechaFin() > $tarea->getFechaInicio()) {
                                echo "La fecha de una tarea ha afectado el camino crítico.\n";
                                $camino_critico_afectado = true;
                                break;
                            }
                        }
                    
                    
                        if ($camino_critico_afectado) {
                        
                            $this->calcularCaminoCritico($id_proyecto);
                        }
                    }
                    
                    
                    
                    public function actualizarFechaFinProyecto($id_proyecto) {
                            
                            $tareasDelProyecto = $this->getTareasPorProyecto($id_proyecto);
                            
                            
                            usort($tareasDelProyecto, function($a, $b) {
                                return $a->getFechaFin() <=> $b->getFechaFin();
                            });
                            
                        
                            $ultimaTarea = end($tareasDelProyecto);
                            
                        
                            if ($ultimaTarea) {
                            
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
                                $this->tareas = array_values($this->tareas);
                                $this->guardarTodasLasTareasEnJson();
                                echo "Tarea eliminada con éxito.\n";
                                break;
                            }
                        }
                    
                        if ($tareaEliminada) {
                            $this->gestorProyecto->eliminarTareaDeProyecto($tareaEliminada->getIdProyecto(), $id_tarea);
                        } else {
                            echo "Tarea con ID {$id_tarea} no encontrada.\n";
                        }
                    }
                    

       
            public function guardarTodasLasTareasEnJson() {
                $tareasData = [];
            
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
            
                file_put_contents($this->archivoJsonTareas, json_encode(['tareas' => $tareasData], JSON_PRETTY_PRINT));
                
                $this->tareas[] = $tarea;   

            }

            public function calcularCaminoCritico($id_proyecto) {
                
                $tareasProyecto = $this->getTareasPorProyecto($id_proyecto);
                
                if (empty($tareasProyecto)) {
                    echo "No hay tareas asociadas a este proyecto.\n";
                    return;
                }
            
                
                echo "=== Lista de Tareas ===\n";
                foreach ($tareasProyecto as $tarea) {
                    echo "ID: " . $tarea->getIdTarea() . ", Nombre: " . $tarea->getNombre() . ", Fecha Inicio: " . $tarea->getFechaInicio()->format('Y-m-d') . ", Fecha Fin: " . $tarea->getFechaFin()->format('Y-m-d') . "\n";
                }
            
               
                usort($tareasProyecto, function($a, $b) {
                    return $a->getFechaInicio() <=> $b->getFechaInicio();
                });
            
                
                $ordenTareas = $this->ordenarTareasPorDependencias($tareasProyecto);
            
                
                echo "=== Camino Crítico ===\n";
                $ultimaFechaFin = null; 
            
                foreach ($ordenTareas as $tarea) {
                    echo "Tarea: " . $tarea->getNombre() . ", Fecha Inicio: " . $tarea->getFechaInicio()->format('Y-m-d') . ", Fecha Fin: " . $tarea->getFechaFin()->format('Y-m-d') . "\n";
                    
                   
                    if ($ultimaFechaFin === null || $tarea->getFechaFin() > $ultimaFechaFin) {
                        $ultimaFechaFin = $tarea->getFechaFin();
                    }
                }
            
                
                if ($ultimaFechaFin !== null) {
                    echo "=== Fecha de Finalización del Proyecto ===\n";
                    echo "La fecha estimada de finalización del proyecto es: " . $ultimaFechaFin->format('Y-m-d') . "\n";
                }
            }
            
            
            
            public function ordenarTareasPorDependencias($tareas) {
                $tareasOrdenadas = [];  
                $tareasPendientes = $tareas;  
            
                
                while (!empty($tareasPendientes)) {
                    $tareasProcesadasEnEstaIteracion = false; 
            
                    foreach ($tareasPendientes as $key => $tarea) {
                        $dependenciasCumplidas = true;
            
                       
                        foreach ($tarea->getDependencias() as $dependencia) {
                           
                            if (!in_array($dependencia, array_map(fn($t) => $t->getIdTarea(), $tareasOrdenadas))) {
                                $dependenciasCumplidas = false;
                                break;
                            }
                        }
            
                       
                        if ($dependenciasCumplidas) {
                            $tareasOrdenadas[] = $tarea;
                            unset($tareasPendientes[$key]);  
                            $tareasProcesadasEnEstaIteracion = true;
                            echo "Tarea " . $tarea->getNombre() . " agregada al orden\n";  
                        }
                    }
            
                    
                    if (!$tareasProcesadasEnEstaIteracion) {
                        echo "Cuidado: hay dependencias no resueltas, posible ciclo en las tareas.\n";
                        break;  
                    }
                }
            
                return $tareasOrdenadas;
            }
            
            
            
                public function getTareasPorProyecto($id_proyecto) {
                  //  $this->listarTareas();


                    return array_filter($this->tareas, function($tarea) use ($id_proyecto) {

                        return $tarea->getIdProyecto() == $id_proyecto;
                    });
                }
            }
            
        

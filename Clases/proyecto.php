<?php


require_once './clases/tarea.php'; // Asegúrate de incluir la clase Tarea

class Proyecto {
    private $id_proyecto;
    private $nombre;
    private $descripcion;
    private $fechaInicio;
    private $fechaFin;
    private $estado;
    private $tareas = [];

    public function __construct($id_proyecto, $nombre, $descripcion, $fechaInicio, $fechaFin, $estado, $tareas = []) {
        $this->id_proyecto = $id_proyecto;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
       // Comprobamos si $fechaInicio ya es un objeto DateTime
       $this->fechaInicio = $fechaInicio instanceof DateTime ? $fechaInicio : new DateTime($fechaInicio);
        
       // Comprobamos si $fechaFin ya es un objeto DateTime
       $this->fechaFin = $fechaFin instanceof DateTime ? $fechaFin : new DateTime($fechaFin);
       
        $this->estado = $estado;
        $this->tareas = $tareas;  // Inicializa tareas
    }

    // Métodos Getters
    public function getId_proyecto() {
        return $this->id_proyecto;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getFechaInicio() {
        return $this->fechaInicio;
    }

    public function getFechaFin() {
        return $this->fechaFin;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function getTareas() {
        return $this->tareas;
    }

    // Métodos Setters
    public function setId_proyecto($id_proyecto) {
        $this->id_proyecto = $id_proyecto;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function setFechaInicio($fechaInicio) {
        // Comprobamos si $fechaInicio ya es un objeto DateTime
        $this->fechaInicio = $fechaInicio instanceof DateTime ? $fechaInicio : new DateTime($fechaInicio);
    }

    public function setFechaFin($fechaFin) {
        // Comprobamos si $fechaFin ya es un objeto DateTime
        $this->fechaFin = $fechaFin instanceof DateTime ? $fechaFin : new DateTime($fechaFin);
    }
    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function setTareas($tareas) {
        $this->tareas = $tareas;
    }

    // Añadir una tarea


    public function agregarTarea($tarea) {
        $this->tareas[] = $tarea;
    }
   
  

    // Eliminar una tarea por ID
    public function eliminarTarea($id_tarea) {
        foreach ($this->tareas as $key => $tarea) {
            if ($tarea->getIdTarea() == $id_tarea) {
                unset($this->tareas[$key]);
                $this->tareas = array_values($this->tareas); // Reindexar el array
                break;
            }
        }
      
    }

    // Convertir el objeto Proyecto a un array (para guardarlo en JSON)
    public function toArray() {
        $tareasArray = array_map(function($tarea) {
            return $tarea->toArray();  // Convierte cada tarea a un array
        }, $this->tareas);

        return [
            'id_proyecto' => $this->id_proyecto,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'fechaInicio' => $this->fechaInicio->format('Y-m-d'),
            'fechaFin' => $this->fechaFin->format('Y-m-d'),
            'estado' => $this->estado,
            'tareas' => $tareasArray,  // Incluye tareas como array
        ];
    }

        // Crear un objeto Proyecto a partir de un array
        public static function fromArray($array) {
            // Aseguramos que las fechas se convierten a DateTime solo si son cadenas
            $fechaInicio = $array['fechaInicio'] instanceof DateTime ? $array['fechaInicio'] : new DateTime($array['fechaInicio']);
            $fechaFin = $array['fechaFin'] instanceof DateTime ? $array['fechaFin'] : new DateTime($array['fechaFin']);

            $tareas = [];
            foreach ($array['tareas'] as $tareaArray) {
                $tareas[] = Tarea::fromArray($tareaArray);  // Convierte cada tarea desde el array
            }

            return new self(
                $array['id_proyecto'],
                $array['nombre'],
                $array['descripcion'],
                $fechaInicio,
                $fechaFin,
                $array['estado'],
                $tareas  // Pasa las tareas convertidas al objeto
            );
        }
    }
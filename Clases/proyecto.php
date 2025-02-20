<?php


require_once './clases/tarea.php'; 

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
       $this->fechaInicio = $fechaInicio instanceof DateTime ? $fechaInicio : new DateTime($fechaInicio);
       $this->fechaFin = $fechaFin instanceof DateTime ? $fechaFin : new DateTime($fechaFin);
        $this->estado = $estado;
        $this->tareas = $tareas; 
    }

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
        $this->fechaInicio = $fechaInicio instanceof DateTime ? $fechaInicio : new DateTime($fechaInicio);
    }

    public function setFechaFin($fechaFin) {
        $this->fechaFin = $fechaFin instanceof DateTime ? $fechaFin : new DateTime($fechaFin);
    }
    public function setEstado($estado) {
        $this->estado = $estado;
    }
    public function setTareas($tareas) {
        $this->tareas = $tareas;
    }
    public function agregarTarea($tarea) {
        $this->tareas[] = $tarea;
    }
   
    public function eliminarTarea($id_tarea) {
        foreach ($this->tareas as $key => $tarea) {
            if ($tarea->getIdTarea() == $id_tarea) {
                unset($this->tareas[$key]);
                $this->tareas = array_values($this->tareas); 
                break;
            }
        }
      
    }

    public function toArray() {
        $tareasArray = array_map(function($tarea) {
            return $tarea->toArray();  
        }, $this->tareas);

        return [
            'id_proyecto' => $this->id_proyecto,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'fechaInicio' => $this->fechaInicio->format('Y-m-d'),
            'fechaFin' => $this->fechaFin->format('Y-m-d'),
            'estado' => $this->estado,
            'tareas' => $tareasArray, 
        ];
    }

        public static function fromArray($array) {
            $fechaInicio = $array['fechaInicio'] instanceof DateTime ? $array['fechaInicio'] : new DateTime($array['fechaInicio']);
            $fechaFin = $array['fechaFin'] instanceof DateTime ? $array['fechaFin'] : new DateTime($array['fechaFin']);

            $tareas = [];
            foreach ($array['tareas'] as $tareaArray) {
                $tareas[] = Tarea::fromArray($tareaArray);  
            }

            return new self(
                $array['id_proyecto'],
                $array['nombre'],
                $array['descripcion'],
                $fechaInicio,
                $fechaFin,
                $array['estado'],
                $tareas  
            );
        }
    }
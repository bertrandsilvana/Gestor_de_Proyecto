<?php
require_once './clases/proyecto.php';
class Tarea {
    private $id_tarea;
    private $nombre;
    private $descripcion;
    private $fecha_inicio;
    private $fecha_fin;
    private $id_proyecto;
    private $dependencias;  

    public function __construct($id_tarea, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $id_proyecto, $dependencias = []) {
        $this->id_tarea = $id_tarea;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->fecha_inicio = $fecha_inicio instanceof DateTime ? $fecha_inicio : new DateTime($fecha_inicio);
        $this->fecha_fin = $fecha_fin instanceof DateTime ? $fecha_fin : new DateTime($fecha_fin);
        $this->id_proyecto = $id_proyecto;
        $this->dependencias = $dependencias;
    }

   
    public function getIdTarea() {
        return $this->id_tarea;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getFechaInicio() {
        return $this->fecha_inicio;
    }

    public function getFechaFin() {
        return $this->fecha_fin;
    }

    public function getIdProyecto() {
        return $this->id_proyecto;
    }

    public function getDependencias() {
        return $this->dependencias;
    }
    
     public function getDuracion() {
        $intervalo = $this->fecha_inicio->diff($this->fecha_fin);
        return $intervalo->days;
    }

  
    public function setFechaInicio($fecha_inicio) {
        if (!$fecha_inicio instanceof DateTime) {
            $this->fecha_inicio = new DateTime($fecha_inicio);
        } else {
            $this->fecha_inicio = $fecha_inicio;
        }
    }
    
    public function setFechaFin($fecha_fin) {
        if (!$fecha_fin instanceof DateTime) {
            $this->fecha_fin = new DateTime($fecha_fin);
        } else {
            $this->fecha_fin = $fecha_fin;
        }
    }
    

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function agregarDependencia($id_tarea) {
        $this->dependencias[] = $id_tarea;
    }
    public function toArray() {
        return [
            'id_tarea' => $this->id_tarea,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'fecha_inicio' => $this->fecha_inicio->format('Y-m-d'),
            'fecha_fin' => $this->fecha_fin->format('Y-m-d'),
            'id_proyecto' => $this->id_proyecto,
            'dependencias' => $this->dependencias,
        ];
    }
    public static function fromArray($array) {
        
    
        
        $idTarea = isset($array['id_tarea']) ? $array['id_tarea'] : null;
        $nombre = isset($array['nombre']) ? $array['nombre'] : '';
        $descripcion = isset($array['descripcion']) ? $array['descripcion'] : '';
        $fechaInicio = isset($array['fecha_inicio']) ? $array['fecha_inicio'] : '';
        $fechaFin = isset($array['fecha_fin']) ? $array['fecha_fin'] : '';
        $idProyecto = isset($array['id_proyecto']) ? (int) $array['id_proyecto'] : null;
        $dependencias = isset($array['dependencias']) ? $array['dependencias'] : [];
    
        if (empty($idTarea)) {
            throw new InvalidArgumentException("Falta el 'id_tarea' en los datos.");
        }
        if (empty($nombre)) {
            throw new InvalidArgumentException("Falta el 'nombre' en los datos.");
        }
        if (empty($fechaInicio)) {
            throw new InvalidArgumentException("Falta 'fecha_inicio' en los datos.");
        }
        if (empty($fechaFin)) {
            throw new InvalidArgumentException("Falta 'fecha_fin' en los datos.");
        }
        if (empty($idProyecto)) {
            throw new InvalidArgumentException("Falta 'id_proyecto' en los datos.");
        }      
        if (!is_int($idTarea)) {
            $idTarea = (int) $idTarea;
        }  
        if (!empty($fechaInicio) && !$fechaInicio instanceof DateTime) {
            $fechaInicio = new DateTime($fechaInicio); 
        }
        if (!empty($fechaFin) && !$fechaFin instanceof DateTime) {
            $fechaFin = new DateTime($fechaFin); 
        }
    
        return new self($idTarea, $nombre, $descripcion, $fechaInicio, $fechaFin, $idProyecto, $dependencias);
    }
    
    
}


  
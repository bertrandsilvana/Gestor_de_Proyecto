<?php
require_once './clases/proyecto.php';
class Tarea {
    private $id_tarea;
    private $nombre;
    private $descripcion;
    private $fecha_inicio;
    private $fecha_fin;
    private $id_proyecto;
    private $dependencias;  // Dependencias de otras tareas (array de IDs de tareas)

    public function __construct($id_tarea, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $id_proyecto, $dependencias = []) {
        $this->id_tarea = $id_tarea;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        // Comprobamos si $fecha_inicio es ya un objeto DateTime
        $this->fecha_inicio = $fecha_inicio instanceof DateTime ? $fecha_inicio : new DateTime($fecha_inicio);
        
        // Comprobamos si $fecha_fin es ya un objeto DateTime
        $this->fecha_fin = $fecha_fin instanceof DateTime ? $fecha_fin : new DateTime($fecha_fin);
        
        $this->id_proyecto = $id_proyecto;
        $this->dependencias = $dependencias;
    }

    // Métodos getter
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
     // Método getDuracion para calcular la duración de la tarea en días
     public function getDuracion() {
        // Calculamos la diferencia entre la fecha de inicio y fin de la tarea
        $intervalo = $this->fecha_inicio->diff($this->fecha_fin);
        return $intervalo->days;
    }

    // Métodos setter
    public function setFechaInicio($fecha_inicio) {
        // Solo asignamos la fecha si no es un objeto DateTime ya
        if (!$fecha_inicio instanceof DateTime) {
            $this->fecha_inicio = new DateTime($fecha_inicio);
        } else {
            $this->fecha_inicio = $fecha_inicio;
        }
    }
    
    public function setFechaFin($fecha_fin) {
        // Solo asignamos la fecha si no es un objeto DateTime ya
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

    // Método toArray() - convierte el objeto en un array
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
        // Depuración: Ver el contenido del array para comprobar qué datos están llegando
      //  var_dump($array);  // Esto te permitirá ver qué datos están presentes
    
        // Verificar y asignar valores predeterminados si faltan claves
        $idTarea = isset($array['id_tarea']) ? $array['id_tarea'] : null;
        $nombre = isset($array['nombre']) ? $array['nombre'] : '';
        $descripcion = isset($array['descripcion']) ? $array['descripcion'] : '';
        $fechaInicio = isset($array['fecha_inicio']) ? $array['fecha_inicio'] : '';
        $fechaFin = isset($array['fecha_fin']) ? $array['fecha_fin'] : '';
        $idProyecto = isset($array['id_proyecto']) ? (int) $array['id_proyecto'] : null;
        $dependencias = isset($array['dependencias']) ? $array['dependencias'] : [];
    
        // Validar que todos los campos requeridos estén presentes
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
    
        // Convertir 'id_tarea' a entero, para asegurarnos de que se procesa correctamente
        if (!is_int($idTarea)) {
            $idTarea = (int) $idTarea;
        }
    
        // Asegurarse de que las fechas sean cadenas y convertirlas a objetos DateTime si es necesario
        if (!empty($fechaInicio) && !$fechaInicio instanceof DateTime) {
            $fechaInicio = new DateTime($fechaInicio); // Convertir la cadena a DateTime
        }
    
        if (!empty($fechaFin) && !$fechaFin instanceof DateTime) {
            $fechaFin = new DateTime($fechaFin); // Convertir la cadena a DateTime
        }
    
        // Ahora crear la tarea con los datos validados
        return new self($idTarea, $nombre, $descripcion, $fechaInicio, $fechaFin, $idProyecto, $dependencias);
    }
    
    
}


  
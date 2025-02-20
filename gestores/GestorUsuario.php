<?php
require_once './clases/usuario.php';

class GestorUsuario{
    private $usuarios = [];
    
        private $archivoJson = './Json/usuario.json';

        public function __construct()
        {
            $this->cargarDesdeJSON();
        }


    
        public function validarUsuario() {
            echo "Ingrese su nombre: ";
            $nombreUsuarioIngresado = trim(fgets(STDIN));
            echo "Ingrese su clave: ";
            $claveUsuarioIngresada = trim(fgets(STDIN));

            foreach ($this->usuarios as $usuario) {
               
                if ($usuario->getNombre() == $nombreUsuarioIngresado && $usuario->getClave() == $claveUsuarioIngresada) {
                    echo "Usuario Válido \n";;
                    return true; 
                }
            }
            
            echo "Nombre o clave incorrecta.\n";
            return false; 
        }

 
    
    public function crearUsuario() {
        $id_usuario = count($this->usuarios) + 1;

        echo "Ingrese nombre: ";
        $nombre = trim(fgets(STDIN));

        echo "Ingrese email: ";
        $email = trim(fgets(STDIN));

        echo "Ingrese clave: ";
        $clave = trim(fgets(STDIN));
        $nuevoUsuario = new usuario($id_usuario, $nombre, $email, $clave);
        $this->usuarios[] = $nuevoUsuario;
        echo "Usuario creado exitosamente: " . $nuevoUsuario->getNombre() . " con ID" .  $nuevoUsuario->getId_usuario() . "\n";
        
        $this->guardarEnJSON();
    }

    
    public function listarUsuarios() {
        if (empty($this->usuarios)) {
            echo "No hay usuarios registrados.\n";
            return;
        }

        echo "=== Usuarios Registrados ===\n";
        foreach ($this->usuarios as $usuario) {
            echo "Id: " . $usuario->getId_usuario() . "\n". " Nombre: " . $usuario->getNombre(). "\n". " Email: " . $usuario->getEmail(). "\n";
        }
    }


    public function editarUsuario() {
        echo "Ingrese el nombre del usuario que desea editar: ";
        $nombre = trim(fgets(STDIN));
    
        echo "Ingrese la clave del usuario que desea editar: ";
        $clave = trim(fgets(STDIN)); 
        $usuarioEncontrado = false; 
    
        foreach ($this->usuarios as $usuario) {
            if ($usuario->getNombre() == $nombre && $usuario->getClave() == $clave) {
                $usuarioEncontrado = true;
    
                echo "Ingrese el nuevo nombre del usuario: ";
                $nuevoNombre = trim(fgets(STDIN));
                $usuario->setNombre($nuevoNombre);
    
                echo "Ingrese el nuevo email: ";
                $email = trim(fgets(STDIN));
                $usuario->setEmail($email);
    
                echo "Ingrese una nueva clave: ";
                $nuevaClave = trim(fgets(STDIN));
                $usuario->setClave($nuevaClave);
    
                echo "Usuario editado exitosamente: " . $usuario->getNombre() . "\n";
                $this->guardarEnJSON();
                break; 
            }
        }
    
        if (!$usuarioEncontrado) {
            echo "Los datos son incorrectos. No se encontró el usuario.\n";
        }
    }
    
    public function eliminarUsuario() {
        echo "Ingrese el nombre del usuario que desea eliminar: ";
        $nombreIngresado = trim(fgets(STDIN));
    
        echo "Ingrese la clave del usuario que desea eliminar: ";
        $claveIngresada = trim(fgets(STDIN));
    
        $indiceUsuarios = null;
        foreach ($this->usuarios as $key => $usuario) {
            if ($usuario->getNombre() === $nombreIngresado && $usuario->getClave() === $claveIngresada) {
                $indiceUsuarios = $key;
                break;
            }
        }
    
        if ($indiceUsuarios === null) {
            echo "Usuario no encontrado.\n";
            return;
        }
    
        unset($this->usuarios[$indiceUsuarios]);
        $this->usuarios = array_values($this->usuarios); 
    
        echo "Usuario eliminado exitosamente.\n";
        $this->guardarEnJSON();
    }
    public function guardarEnJSON() {
        $usuarios = [];

        foreach ($this->usuarios as $usuario) {
            $usuarios[] = $usuario->ToArray();
        }

        $jsonusuario = json_encode(['usuario' => $usuarios], JSON_PRETTY_PRINT);
        file_put_contents($this->archivoJson, $jsonusuario);
    }

    public function cargarDesdeJSON() {
        if (file_exists($this->archivoJson)) {
            $jsonusuario = file_get_contents($this->archivoJson);
            $usuarios = json_decode($jsonusuario, true)['usuario'];
            $this->usuarios = [];

        
            foreach ($usuarios as $usuarioData) {
                $usuario = new usuario(
                    $usuarioData['id_usuario'],
                    $usuarioData['nombre'],
                    $usuarioData['email'],
                    $usuarioData['clave'],
                    
                );
                $this->usuarios[] = $usuario;
            }
        }   

    }
}
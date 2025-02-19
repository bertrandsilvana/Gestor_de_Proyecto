<?php
class Usuario{
        private $id_usuario;
        private $nombre;
        private $email;
        private $clave;


        public function __construct ($id_usuario, $nombre, $email, $clave){
            $this-> id_usuario = $id_usuario;
            $this-> email = $email;
            $this-> nombre = $nombre;
            $this-> clave = $clave;
        }
        public function getId_usuario (){
            return $this-> id_usuario;
        }
        public function getNombre (){
            return $this-> nombre;
        }
        public function getEmail (){
            return $this-> email;
        }
        public function getClave (){
            return $this-> clave;
        }

        public function setId_usuario ($id_usuario){
            $this-> Id_usuario = $id_usuario;
        }
        public function setNombre ($nombre){
            $this-> nombre= $nombre;
        }
        public function setEmail ($email){
            $this-> email =$email;
        }
        public function setClave ($clave){
            $this-> clave =$clave;
        }
        public function toArray() {
            return [
                'id_usuario' => $this->id_usuario,
                'nombre' => $this->nombre,
                'email' => $this->email,
                'clave' => $this->clave,
                
            ];
        }
        public static function fromArray($array) {
            return new self(
                $array['id_usuario'],
                $array['nombre'],
                $array['email'],
                $array['clave'],
                
            );
        }
    


        }  
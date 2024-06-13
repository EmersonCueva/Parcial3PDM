<?php

    class DbOperations{

        private $con;

        function __construct(){

            require_once dirname(__FILE__) . '/DbConnect.php';
        
            $db = new DbConnect;
            $this ->con = $db->connect();
        }

            //Funcion para crear los equipos en la BD
        public function createEquipo($nombreEquipo,$institucion,$departamento,$municipio,$direccion,$telefono){
          if (!$this->siNombreEquipoExiste($nombreEquipo)) {
                $stmt = $this->con->prepare("INSERT INTO equipos (nombreEquipo,institucion,departamento,municipio,direccion,telefono) VALUES (?,?,?,?,?,?)");
                $stmt->bind_param("ssssss",$nombreEquipo,$institucion,$departamento,$municipio,$direccion,$telefono);
                if ($stmt->execute()) {
                    return EQUIPO_CREATED;
                } else {
                    return EQUIPO_FAILURE;
                }
          }
          return EQUIPO_EXISTS;
            
        }

        //Verificar si existe el nombre del equipo
        private function siNombreEquipoExiste($nombreEquipo){
            $stmt = $this->con->prepare("SELECT idEquipo FROM equipos WHERE nombreEquipo=?");
            $stmt->bind_param("s",$nombreEquipo);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows >0;
        }

        // Funcion para obtener todos los equipos de nuestra base de datos
        public function getAllEquipos() {
        $stmt = $this->con->prepare("SELECT * FROM equipos");
        $stmt->execute();
        $stmt->bind_result($idEquipo, $nombreEquipo, $institucion, $departamento, $municipio, $direccion, $telefono);
        
        $equipos = array();
            //Aqui vamos a definir que elementos queremos que se muestren al momento de obtener el dato de la BD MySQL
        while($stmt->fetch()) {
            $equipo = array();
            $equipo['idEquipo'] = $idEquipo;
            $equipo['nombreEquipo'] = $nombreEquipo;
            $equipo['institucion'] = $institucion;
            $equipo['departamento'] = $departamento;
            $equipo['municipio'] = $municipio;
            $equipo['direccion'] = $direccion;
            $equipo['telefono'] = $telefono;

            array_push($equipos, $equipo);
        }

        return $equipos;
    }

    //Creamos la funcion para los jugadores
    public function createJugador($nombres, $apellidos, $fechaNacimiento, $genero, $posicion, $idEquipo) {
        $stmt = $this->con->prepare("INSERT INTO jugadores (nombres, apellidos, fechaNacimiento, genero, posicion, idEquipo) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $nombres, $apellidos, $fechaNacimiento, $genero, $posicion, $idEquipo);
        if ($stmt->execute()) {
            return JUGADOR_CREATED;
        } else {
            return JUGADOR_FAILURE;
        }
    }

    public function getAllJugadores() {
        $stmt = $this->con->prepare("SELECT idJugador, nombres, apellidos, fechaNacimiento, genero, posicion, idEquipo FROM jugadores");
        $stmt->execute();
        $stmt->bind_result($idJugador, $nombres, $apellidos, $fechaNacimiento, $genero, $posicion, $idEquipo);

        $jugadores = array();
        while ($stmt->fetch()) {
            $jugador = array();
            $jugador['idJugador'] = $idJugador;
            $jugador['nombres'] = $nombres;
            $jugador['apellidos'] = $apellidos;
            $jugador['fechaNacimiento'] = $fechaNacimiento;
            $jugador['genero'] = $genero;
            $jugador['posicion'] = $posicion;
            $jugador['idEquipo'] = $idEquipo;
            array_push($jugadores, $jugador);
        }
        return $jugadores;
    }

    }
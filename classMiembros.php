<?php
class Miembros extends Conexion{

    public function __construct(){
      parent::__construct();
    }

    public function activos(){

        $fecha = new DateTime;
        $fecha_hoy= $fecha->format('Y-m-d');

        $sql_miembros_activos = $this->conexion_db->query("SELECT * FROM miembros 
        LEFT JOIN membresias 
        ON miembros.codigo_activacion = membresias.codigo_activacion
        LEFT JOIN categoria_membresias
        ON membresias.categoria = categoria_membresias.id_categoria
        WHERE membresias.fecha_fin >= '$fecha_hoy' AND estatus='VIGENTE' ORDER BY membresias.fecha_inicio DESC");

        $array_miembros_activos = $sql_miembros_activos->fetch_all(MYSQLI_ASSOC);

        return $array_miembros_activos;

    }

    public function vencidos(){

        $fecha = new DateTime;
        $fecha_hoy= $fecha->format('Y-m-d');

        $sql_miembros_vencidos = $this->conexion_db->query("SELECT * FROM miembros
        LEFT JOIN membresias 
        ON miembros.codigo_activacion = membresias.codigo_activacion
        LEFT JOIN categoria_membresias AS cat
        ON membresias.categoria = cat.id_categoria
        WHERE membresias.fecha_fin < '$fecha_hoy' ORDER BY membresias.fecha_fin DESC");

        $array_miembros_vencidos = $sql_miembros_vencidos->fetch_all(MYSQLI_ASSOC);

        return $array_miembros_vencidos;
    }

    public function totalActivos(){

        $fecha = new DateTime;
        $fecha_hoy= $fecha->format('Y-m-d');
        $consulta = $this->conexion_db->query("SELECT count(id_miembro) AS totalRegistros 
                    FROM miembros 
                    LEFT JOIN membresias 
                    ON miembros.codigo_activacion = membresias.codigo_activacion
                    WHERE membresias.estatus = 'VIGENTE' AND membresias.fecha_fin >= '$fecha_hoy' ");

        $respuesta = $consulta->fetch_all(MYSQLI_ASSOC);

        foreach ($respuesta as $value) {
          $resultado = $value['totalRegistros'];
        }

        return $resultado;
      }

      public function cancelados(){

        $fecha = new DateTime;
        $fecha_hoy= $fecha->format('Y-m-d');

        $sql_miembros_vencidos = $this->conexion_db->query("SELECT * FROM miembros
        LEFT JOIN membresias 
        ON miembros.codigo_activacion = membresias.codigo_activacion
        LEFT JOIN categoria_membresias AS cat
        ON membresias.categoria = cat.id_categoria
        WHERE estatus='CANCELADO' ORDER BY membresias.fecha_fin ASC");

        $array_miembros_vencidos = $sql_miembros_vencidos->fetch_all(MYSQLI_ASSOC);

        return $array_miembros_vencidos;
    }

    public function totalVencidos(){

      $fecha = new DateTime;
      $fecha_hoy= $fecha->format('Y-m-d');

      $consulta = $this->conexion_db->query("SELECT count(id_miembro) AS totalRegistros 
      FROM miembros 
      LEFT JOIN membresias 
      ON miembros.codigo_activacion = membresias.codigo_activacion
      WHERE membresias.fecha_fin < '$fecha_hoy' ");

      $respuesta = $consulta->fetch_all(MYSQLI_ASSOC);

      foreach ($respuesta as $value) {
          $resultado = $value['totalRegistros'];
      }

      return $resultado;
     }
    
    public function resumenCuenta($id_nota){

      $sql = "SELECT cat.categoria, n.id_nota, n.fecha, n.total, 
              n.pagado, mi.nombres, mi.apellido_paterno, mi.apellido_materno, mi.email, mi.telefono, mi.num_membresia
              FROM nota_venta AS n 
              LEFT JOIN miembros AS mi 
              ON n.codigo_activacion = mi.codigo_activacion 
              LEFT JOIN categoria_membresias as cat 
              ON n.tipo_membresia = cat.id_categoria
              WHERE n.id_nota = '$id_nota' AND mi.contratante = 'SI' ";

      // $sql = "SELECT a.folio, a.nombres, a.apellido_paterno, a.apellido_materno,
      //        b.plan, c.categoria, c.precio_anual, c.precio_mensual
      //         FROM miembros as a LEFT JOIN membresias as b 
      //         ON a.codigo_activacion = b.codigo_activacion 
      //         LEFT JOIN categoria_membresias as c
      //         ON b.categoria = c.id_categoria
      //         WHERE a.folio = '$folio' ";

          $ejecutar =  $this->conexion_db->query($sql);
          $respuesta = $ejecutar->fetch_all(MYSQLI_ASSOC);

          return $respuesta;
      }

      public function informacionMiembro($folio){

        $sql = "SELECT * FROM miembros WHERE folio = $folio";

        $ejecutar =  $this->conexion_db->query($sql);
        $respuesta = $ejecutar->fetch_all(MYSQLI_ASSOC);

        return $respuesta;
      }

      public function get_perfil($membresia){

        $sql = "SELECT *  FROM miembros AS a
        LEFT JOIN credenciales AS cred
        ON a.num_membresia = cred.num_membresia
        LEFT JOIN membresias AS m
        ON a.codigo_activacion = m.codigo_activacion
        INNER JOIN nivel_estudios as b 
        ON a.nivel_estudios = b.id_nestudios
        INNER JOIN area_estudios as c
        ON a.area_estudios = c.id_aestudios
        INNER JOIN trabaja_en as d
        ON a.area_trabajo = d.id_trabajo
        INNER JOIN area_responsabilidad as e
        ON a.area_responsabilidad = e.id_aresponsabilidad
        WHERE a.num_membresia = '$membresia'";

        $consulta = $this->conexion_db->query($sql);

        $result = $consulta->fetch_all(MYSQLI_ASSOC);

        return $result;
      }

      public function get_pdf($membresia){
       $sql = "SELECT a.nombres,a.apellido_paterno,a.num_membresia,m.fecha_fin  FROM miembros AS a
        LEFT JOIN membresias AS m
        ON a.codigo_activacion = m.codigo_activacion
        WHERE a.num_membresia = '$membresia'";

      $consulta = $this->conexion_db->query($sql);

      $result = $consulta->fetch_all(MYSQLI_ASSOC);

      return $result;
      }

      //Busvar información para redireccionar a bienvenida

      public function infoBienvenida($folio){

        $sql = "SELECT * FROM miembros WHERE folio = $folio";

        $ejecutar =  $this->conexion_db->query($sql);
        $respuesta = $ejecutar->fetch_all(MYSQLI_ASSOC);

        return $respuesta;
      }

      //////////////////////////////////////////////////

      public function informacionNaylor($membresia){
        $sql = "SELECT mi.email, mi.nombres, mi.apellido_paterno, mi.apellido_materno, 
        mi.genero, mi.fecha_nacimiento, mi.empresa, mi.codigo_pais, mi.telefono, mi.pais, mi.estado, 
        mi.ciudad, mi.direccion, mi.colonia, mi.cp, mi.num_membresia, 
        me.codigo_activacion, me.fecha_inicio, me.fecha_fin 
        FROM miembros AS mi 
        INNER JOIN membresias AS me 
        ON mi.codigo_activacion = me.codigo_activacion 
        WHERE mi.num_membresia = '$membresia' ";
    
        $ejecutar =  $this->conexion_db->query($sql);
        $datosHL = $ejecutar->fetch_all(MYSQLI_ASSOC);
    
        return $datosHL;
    
      }

      public function informacionHL($folio){
        //Aquí tenemos que validar que tipo de entrada al congreso tiene para asignarles los grupos de seguridad 

        $sql = "SELECT mi.email, mi.nombres, mi.apellido_paterno, mi.apellido_materno, 
        mi.genero, mi.fecha_nacimiento, mi.empresa, mi.codigo_pais, mi.telefono, mi.pais, mi.estado, 
        mi.ciudad, mi.direccion, mi.colonia, mi.cp, mi.num_membresia, mi.entrada_congreso, 
        me.codigo_activacion, me.fecha_inicio, me.fecha_fin, me.categoria
        FROM miembros AS mi 
        INNER JOIN membresias AS me 
        ON mi.codigo_activacion = me.codigo_activacion 
        WHERE mi.folio = '$folio' ";

        $ejecutar =  $this->conexion_db->query($sql);
        $datosHL = $ejecutar->fetch_all(MYSQLI_ASSOC);

        return $datosHL;

      }

      public function bienvenidaEmpresarial($codigo){

        $sql = "SELECT a.codigo_activacion, a.categoria, b.nombres, b.apellido_paterno, b.apellido_materno, c.categoria, c.cantidad
          FROM membresias as a
          LEFT JOIN miembros as b
          ON a.codigo_activacion = b.codigo_activacion
          LEFT JOIN categoria_membresias as c
          ON a.categoria = c.id_categoria
          WHERE a.codigo_activacion = '$codigo' AND b.contratante = 'SI' ";

        $ejecutar =  $this->conexion_db->query($sql);
        $respuesta = $ejecutar->fetch_all(MYSQLI_ASSOC);

        return $respuesta;

      }

      public function buscadorActivos($str){

        $fecha = new DateTime;
        $fecha_hoy= $fecha->format('Y-m-d');
        $sql_miembros_activos = $this->conexion_db->query("SELECT * FROM miembros 
        LEFT JOIN membresias 
        ON miembros.codigo_activacion = membresias.codigo_activacion
        WHERE membresias.fecha_fin >= '$fecha_hoy'AND 
        (miembros.email LIKE '%$str%' OR miembros.nombres LIKE  '%$str%'
        OR miembros.apellido_paterno LIKE '%$str%' OR miembros.apellido_materno LIKE '%$str%'
        OR miembros.codigo_activacion LIKE '%$str%' OR miembros.num_membresia LIKE '%$str%')
        ORDER BY membresias.fecha_inicio DESC");

        $array_miembros_activos = $sql_miembros_activos->fetch_all(MYSQLI_ASSOC);

        return $array_miembros_activos;
    }

    function fechaActual(){
      //Saber fecha de hoy
      date_default_timezone_set('America/Mexico_City');
          setlocale(LC_TIME, 'es_MX.UTF-8');
          $fecha_actual=strftime("%Y-%m-%d");

          return $fecha_actual;
    }

    //==== Activar contraseña para un usuario nuevo =====
    function generarContrasena($usuario, $password, $membresia){
      /*
        Para activar la contraseña, verificamos la existencia de la membresía en la tabla de miembros 
        y su fecha de vencimiento.
        Verificamos que no exista aún en la tabla de credenciales.
        Sí todo ok, procedemos ha la generación de las credenciales.
      */
      $fecha = $this->fechaActual();
      //Verificamos existencia y fecha de vencimiento
      $sql = "SELECT a.num_membresia 
              FROM miembros as a
              LEFT JOIN membresias as b
              ON a.codigo_activacion = b.codigo_activacion
              WHERE a.num_membresia = '$membresia' AND b.fecha_fin > '$fecha' AND b.estatus <> 'CANCELADO' ";
              
      $consulta = $this->conexion_db->query($sql);
      $resultado = $consulta->fetch_row();

      if ($resultado != NULL){
        //Si devuelve una fila: existe el # de membresía y está activa      
        $sql_credenciales = "SELECT num_membresia FROM credenciales 
              WHERE num_membresia = '$membresia'";
        $consultaCredencial = $this->conexion_db->query($sql_credenciales);
        $resultado_credencial = $consultaCredencial->fetch_row();

          if ($resultado_credencial != NULL){
            //Si devuelve una fila: existe el # de membresía con credenciales de acceso
            $mensaje = "<script language='JavaScript'>
                  alert('La membresía se encuentra activada');
                  </script>";
            // $mensaje = "La membresía se encuentra activada";
            return $mensaje;
          }
          else{         
            $sql_insert = "INSERT INTO credenciales VALUES (null, '$usuario', '$password', '1', '$membresia')";
            $registro_membresia = $this->conexion_db->query($sql_insert);
              if ($registro_membresia){
                //se registro
                $result = true;
                
                // $mensaje = "Registro realizado con éxito";
              return $result;
              }
              else{
                $mensaje = "<script language='JavaScript'>
                  alert('No pudimos realizar el registro');
                  </script>";
                // $mensaje = "No pudimos realizar el registro";
                return $mensaje;
              }
          }
      }

      else{
        //No existe o es una membresía vencida
        $mensaje = "<script language='JavaScript'>
                  alert('La membresía no existe o se encuentra vencida');
                  </script>";
        // $mensaje = "La membresía no existe o se encuentra vencida";
        return $mensaje;
      }

    }


    // Clasificar según categoria en los grupos correspondientes
    public function grupoSeguridad($cat_membresia, $cat_congreso)
    {

      $membresia = $cat_membresia;
      $profesional_estandar_anual = "Profesional Estándar Anual";
      $profesional_lider_emergente_anual = "Profesional Líder Emergente Anual";
      $profesional_estudiante_anual = "Profesional Estudiante Anual";
      $congreso_presencial = "Congreso Presencial León 2020";
      $congreso_virtual = "Congreso Virtual León 2020";
      $temporal = "Temporal Congreso León 2020";

      $group_key_profesional_estandar_anual = "Prof_EA";
      $group_key_profesional_lider_emergente_anual = "Prof_LEA";
      $group_key_estudiante_anual = "Estudiante";
      $group_key_congreso_presencial = "Congreso_Presencial_L2020";
      $group_key_congreso_virtual = "Congreso_Virtual_L2020";
      $group_key_temporal = "Temporal_L2020";

          
      //si la categoría es null biene del formulario de membresías
      if($cat_congreso != NUll)
      {
        if($cat_congreso == "1" || $cat_congreso =="4"){
          $groupKey_1 = $group_key_profesional_estandar_anual;
          $groupName_1 = $profesional_estandar_anual;
          $groupKey_2 =  $group_key_congreso_presencial;
          $groupName_2 = $congreso_presencial;
        }
        elseif($cat_congreso == "2" || $cat_congreso =="5"){
          $groupKey_1 = $group_key_profesional_lider_emergente_anual;
          $groupName_1 = $profesional_lider_emergente_anual;
          $groupKey_2 =  $group_key_congreso_presencial;
          $groupName_2 = $congreso_presencial;
        }
        elseif($cat_congreso == "3" || $cat_congreso =="6"){
          $groupKey_1 = $group_key_estudiante_anual;
          $groupName_1 = $profesional_estudiante_anual;
          $groupKey_2 =  $group_key_congreso_presencial;
          $groupName_2 = $congreso_presencial;
        }
        elseif($cat_congreso == "7" || $cat_congreso =="10"){
          $groupKey_1 = $group_key_profesional_estandar_anual;
          $groupName_1 = $profesional_estandar_anual;
          $groupKey_2 =  $group_key_congreso_virtual;
          $groupName_2 = $congreso_virtual;
        }
        elseif($cat_congreso == "8" || $cat_congreso =="11"){
          $groupKey_1 = $group_key_profesional_lider_emergente_anual;
          $groupName_1 = $profesional_lider_emergente_anual;
          $groupKey_2 =  $group_key_congreso_virtual;
          $groupName_2 = $congreso_virtual;
        }
        elseif($cat_congreso == "9" || $cat_congreso =="12"){
          $groupKey_1 = $group_key_estudiante_anual;
          $groupName_1 = $profesional_estudiante_anual;
          $groupKey_2 =  $group_key_congreso_virtual;
          $groupName_2 = $congreso_virtual;
        }
        elseif($cat_congreso == "13"){
          $groupKey_1 = $group_key_temporal;
          $groupName_1 = $temporal;
          $groupKey_2 =  "";
          $groupName_2 = "";
        }
      }

      //Armar los grupos a enviar
      $securityGroup = array(
        "SinceDate" => "",
        "BeginDate" => "",
        "EndDate" =>"",
        "GroupKey" => $groupKey_1,
        "GroupName" => $groupName_1,
        "GroupType" => "Security",
        "RoleDescription" => null
      );
      $securityGroup2 = array(
        "SinceDate" => "",
        "BeginDate" => "",
        "EndDate" =>"",
        "GroupKey" => $groupKey_2,
        "GroupName" => $groupName_2,
        "GroupType" => "Security",
        "RoleDescription" => null
      );

      $array_group = array($securityGroup, $securityGroup2);
      return $array_group;
        
    }

     

      
    public function pushHL($folio, $IsActive="True", $IsMember="True", $ExcludeFromDirectory="False"){

      $informacionMiembro = $this->informacionHL($folio);
      foreach($informacionMiembro as $info){

        //clasificación de grupos de seguridad según sea el caso
        //traemos el tipo de entrada al congreso de tenerlo
        // var_dump($info["categoria"], $info["entrada_congreso"]);
        $securityGroup = $this->grupoSeguridad($info["categoria"], $info["entrada_congreso"]);
        // var_dump($securityGroup);
        // die();
        $unMiembro = array(
          //Membresía 
          "LegacyContactKey" => $info["num_membresia"],
          "MemberID"  => $info["num_membresia"],
          "PrefixCode"  => "",
          "FirstName"  => $info["nombres"],
          "MiddleName"  => "",
          "LastName"  => $info["apellido_paterno"]." ".$info["apellido_materno"],
          "SuffixCode"  => "",
          "Designation"  => "",
          "InformalName"  => "",
          "Gender"  => $info["genero"],
          "Ethnicity"  => "",
          "Age"  => "",
          "Birthday"  => $info["fecha_nacimiento"],			
          "MemberSince"  => $info["fecha_inicio"],
          "MemberExpiresOn"  => $info["fecha_fin"],
          "ExcludeFromDirectory"  => $ExcludeFromDirectory,
          "IsActive"  => $IsActive,
          "IsMember" => $IsMember,
          "Title"  => "",
          "CompanyName"  => $info["empresa"],
          "ParentMemberKey"  => $info["codigo_activacion"],
          "Bio"  => "",
          "ProfileImageURL"  => "",
          "EmailAddress"  => $info["email"],
          "Phone1" => "",
          "Phone1Type" => "OFFICE",
          "Phone2" => $info["codigo_pais"].$info["telefono"],
          "Phone2Type" => "MOBILE",
          "Phone3" => "",
          "Phone3Type" => "FAX",
          "Phone4" => "",
          "Phone4Type" => "",
          "Address1" => $info["direccion"],
          "Address2" => $info["colonia"],
          "Address3" => "",
          "City" => $info["ciudad"],
          "State" => $info["estado"],
          "PostalCode" => $info["cp"],
          "Country" => $info["pais"],
          "WebsiteURL"  => "",
          "YouTubeURL" => "",
          "FacebookURL" => "",
          "TwitterURL" => "",
          "GooglePlusURL" => "",
          "LinkedInURL" => "",
          "BloggerURL" => "",
          "WordPressURL" => "",
          "OtherBlogURL" => "",
          "IsOrganization"  => "False"
      );
      }

      
      
      // $securityGroup = array($securityGroup);
    // Declarar el arreglo de miembros en blanco
    // $arregloMiembros = array();
    // Insertar los miembros de uno en uno
    // array_push($arregloMiembros, $unMiembro);
    // $arregloMiembros = array("MemberDetails" => $unMiembro);
    
    // var_dump($arregloMiembros);
    $arregloMiembros = array("MemberDetails" => $unMiembro, "SecurityGroups" => $securityGroup);
    // var_dump($arregloMiembros);
    // Crear la estructura del JSON tal y como esta especificado en Push_API_Integration.pdf
    $tenantCode = "ANPRM";
    $data = array(
        "TenantCode" =>$tenantCode,
        "Items" => [$arregloMiembros]
        );
      // Convertir el objecto con la estructura a un JSON string
        $data_string = json_encode($data);
        // echo ($data_string) ;
        // die();
        
      $remote_server_output = "";
     
        // definimos la URL a la que hacemos la petición
        //url HL => https://data.higherlogic.com/push/v1/members
        //url pruebas => 34.205.75.219
        $url = "https://data.higherlogic.com/push/v1/members";
        $api_key = "vlryMU2EvjaNwE15LuZ2StEEyASfarY7cWgBtSC6";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // indicamos el tipo de petición: POST
        curl_setopt($ch, CURLOPT_POST, TRUE);
        // definimos cada uno de los parámetros
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        // recibimos la respuesta y la guardamos en una variable
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Request Headers: x-api-key: Value provided by Higher Logic, Content-Type: application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'x-api-key:'.$api_key,
            'Content-Type: application/json'));
        $respuesta_servidor = curl_exec ($ch);

        $error = curl_errno($ch);
        if($error){
          $error =  'Error: ' . curl_error($ch);
          return $error;
        }
        
        // cerramos la sesión cURL
        curl_close ($ch);

        // return true;
        return $respuesta_servidor;
    }

    
    public function actualizarPerfil($id, $usuario, $nombres, $ap_paterno, $ap_materno, $genero, $f_nacimiento, $n_estudios,
                                              $a_estudios, $empresa, $a_responsabilidad, $trabaja_en,
                                              $telefono, $lada, $pais, $estado, $ciudad, $direccion, $cp, $colonia, $folio)
      {

      $sql = "UPDATE credenciales SET usuario = '$usuario' WHERE num_membresia = '$id' ";

      $actualizarUsuario = $this->conexion_db->query($sql);   

      if($actualizarUsuario)
      {
        $sql = "UPDATE miembros SET apellido_paterno = '$ap_paterno',
            apellido_materno = '$ap_materno',
            nombres = '$nombres',
            genero = '$genero',
            fecha_nacimiento = '$f_nacimiento',
            nivel_estudios = '$n_estudios',
            area_estudios = '$a_estudios',
            empresa = '$empresa',
            area_trabajo = '$trabaja_en',
            area_responsabilidad = '$a_responsabilidad',
            codigo_pais = '$lada',
            telefono = '$telefono',
            pais = '$pais',
            estado = '$estado',
            ciudad = '$ciudad',
            direccion = '$direccion',
            colonia = '$colonia',
            cp = '$cp'
            WHERE num_membresia = '$id' ";

        $resultado = $this->conexion_db->query($sql);

        if($resultado){
          $resultado = $this->pushHL($folio);
        }

        return $resultado;
        
      }
      
    return false;

  }



  public function entradaCongreso($entrada){
    
    $sql= "SELECT * FROM congreso_leon WHERE id_boleto = $entrada"; 
    $resultado = $this->conexion_db->query($sql);
    $entradas = $resultado->fetch_all(MYSQLI_ASSOC);

    return $entradas;
  }

}
?>
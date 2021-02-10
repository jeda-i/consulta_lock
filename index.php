<!DOCTYPE html>
<html>
    <head>
        <title>Painel Lock</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta http-equiv="refresh" content="2">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
        <link rel="stylesheet" href="css/style.css">
        <link href="https://fonts.googleapis.com/css?family=Frank+Ruhl+Libre:300&display=swap" rel="stylesheet"> 
        <!-- <link rel="shortcut icon" type="image/x-icon" href="img/4050banco.ico"> -->
    </head>
    <body>
        <div class="container-header">
            <!--<img src="img/logo.png" alt="Logomarca" width="20%" height="20%">                       -->
            <h2>Consulta Lock</h2>
            <p class="text-center">Contato DBA: ****************
            <br>
            (71)-****-****
            <br>
            (71)-****-****
            </p>           
        </div> 
                      
        <div class="container-main">     
        <?php
            $conn = oci_connect("usuario","senha","(DESCRIPTION =
            (ADDRESS_LIST =
            (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
            )
            (CONNECT_DATA =
            (SID = SEU_SID)
            )
            )");
            
            if(!$conn){
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }

            $session = "session";
            
            $sql = "select case when s.blocking_session is not null then 'alter system kill session '||''''||(select w.sid from v$$session w where w.sid= (s.blocking_session))||','||(select w.serial# from v$$session w where w.sid= (s.blocking_session))||''''||' immediate;' end ".'"'."matar Lock copiar linha".'"'.", s.osuser,s.machine,LPAD(floor(last_call_et/3600),3,0)||':'||LPAD(floor(mod(last_call_et,3600)/60),2,0)||':'|| LPAD(mod(mod(last_call_et,3600),60),2,0) TEMPO,s.blocking_session, (select w.serial# from v$$session w where w.sid= (s.blocking_session)) serial_que_gerou_lock, (select w.machine from v$$session w where w.sid= (s.blocking_session)) maquina_que_gerou_lock, (select w.module from v$$session w where w.sid= (s.blocking_session)) rotina_que_gerou_lock, (select w.osuser from v$$session w where w.sid= (s.blocking_session)) usuario_que_gerou_lock, s.module,s.SID,S.SERIAL#,s.schemaname from v$$session s where status='ACTIVE' AND username is not null order by (select w.osuser from v$$session w where w.sid= (s.blocking_session)),s.last_call_et";

            $query = oci_parse($conn, $sql);

            $exec = oci_execute($query);
            $tableHTML= "<table class='table table-bordered'>
                    <thead>
                        <tr>
                            <td scope='col'><b>Usuario</b></td>
                            <td scope='col'><b>Fonte</b></td>
                            <td scope='col'><b>Maquina</b></td>
                            <td scope='col'><b>Tempo</b></td>
                            <td scope='col'><b>Serial/sessão bloq</b></td>
                        </tr>
                    </thead>
                    <tbody>";

            while($row = oci_fetch_assoc($query)){
                if($row["USUARIO_QUE_GEROU_LOCK"]!=""){
                    $tableHTML= $tableHTML."<tr>";
                    $tableHTML= $tableHTML."<td scope='row'>".$row["USUARIO_QUE_GEROU_LOCK"]."</td>";
                    $tableHTML= $tableHTML."<td scope='row'>".$row["ROTINA_QUE_GEROU_LOCK"]."</td>";
                    $tableHTML= $tableHTML."<td scope='row'>".$row["MAQUINA_QUE_GEROU_LOCK"]."</td>";
                    $tableHTML= $tableHTML."<td scope='row'>".$row["TEMPO"]."</td>";
                    $tableHTML= $tableHTML."<td scope='row'>".$row["SERIAL_QUE_GEROU_LOCK"]." / ".$row["BLOCKING_SESSION"]."</td>";
                    $tableHTML= $tableHTML."</tr>";
                }
            }
            $tableHTML= $tableHTML."</tbody>
                </table>";
            echo $tableHTML;
            oci_free_statement($query);
            oci_close($conn);
        ?>
            
        </div>   
    </body>
</html>
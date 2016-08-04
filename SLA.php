<?php
$this->breadcrumbs=array(
	'Audit',
);
?>

<script type="text/javaScript">
function autoRefresh(interval) {
	setTimeout("atualizar();",interval);
}
function atualizar() {
  // faz a lógica desejada...
}
onload='javascript:autoRefresh(001000)'

</script>
<?php


#Pega hora atual do servidor 
#ano
$ano_atual = date('Y');
#mes
$mes_atual = date('m');
#Dia
$dia_atual = date('d');

 #Limite Sedex                            01:00:00 
 $limite_sedex10_1 = date('H:i:s', strtotime('010000'));
                                         # 00:30:00
 $limite_sedex10_2 = date('H:i:s', strtotime('003000'));

#Limite Juridico                           02:00:00 
 $limite_juridico = date('H:i:s', strtotime('020000'));
 #                                          01:00:00 
 $limite_juridico_2 = date('H:i:s', strtotime('010000'));

 #Outros                                     23:59:00
 $limite_outros_1 = date('H:i:s', strtotime('235900'));
 #                                           23:00:00
 $limite_outros_2 = date('H:i:s', strtotime('230000'));

include 'conexao.php';

	   
       #Query para buscar todas as informações no banco do dia.
	   #'$ano_atual-$mes_atual-$dia_atual%'
	   $sql = "SELECT * FROM qualicorp_track WHERE tipo = 'Impresso' AND ts_impressao LIKE '%2016-08-02%'
	   	ORDER BY ts_impressao ASC";
             
      $resultado = mysql_query($sql, $conecta);
   
	   
	    $numRegistros = mysql_num_rows($resultado);
		
		if($numRegistros !=0) {
		 $tabela = "<table class='table table-striped'>
                  
                      <thead> 
                            <tr>
                               <th>Tracking</th> 
                               <th>Remetente</th>
                               <th>Destinatário</th>
                               <th>Transporte</th> 
                               <th>Data da Geração</th>
                            </tr> 
                       </thead> 
                      
                       "; 
 
          //Retorna os Resultados da tabela
         $return = "$tabela"; 
 
    // Captura os dados da consulta e inseri na tabela HTML 
       while ($linha = mysql_fetch_array($resultado)) { 
        
        #data
       $data = $linha["ts_impressao"];
       #Transp
       $transp = $linha["transp"];
       
       #Armazena o valor do ts_impressao na variavel $data
       $hora = date('H:i:s', strtotime($data));
       #Busca a hora atual do servidor
       $hora_atual = date('H:i:s');
       
         #formata a data 
       $tempo = gmdate('H:i:s', strtotime( $hora_atual ) - strtotime( $hora ) );

        #Juridico
       $juridico = "";
     
        #Se             ou 
     if($transp == 'PRE' || $transp == 'CRJ' || $transp == 'ENJ' || $transp == 'SEJ' || $transp == 'TEJ'){
 	     
 	     $juridico = "documento_juri";
        } 
         else{
 	          $juridico = "n";
       }
      #juridico
          
         # Se juridico identico a documento_juri ou  Tempo maior que o $limite_juridico 02:00h.
      if($juridico === "documento_juri" && $tempo > $limite_juridico){
            #Vermelho
        $return.= "<td><b><font color='#FF0000'>" . utf8_encode($linha["tracking"]) . "</td></font></b>"; 
        $return.= "<td><b><font color='#FF0000'>" . utf8_encode($linha["remetente"]) . "</td></font></b>";
        $return.= "<td><b><font color='#FF0000'>" . utf8_encode($linha["destinatario"]) . "</td></font></b>";
        $return.= "<td><b><font color='#FF0000'>" . utf8_encode($transp) . "</td></font></b>";
        
         $return.= "<td><b><font color='#FF0000'>" . date('d/m/Y H:i:s', strtotime($data)) . "</td></font></b>";  
 
         $return.= "</tr>"; 
        
    
                include 'YiiMailMessage.php';
          	    $message = new YiiMailMessage('Juridico'.$linha['tracking']);
             	$message->view = 'email1';
				$message->setBody(array('model' => $model), 'text/html');
				$message->addTo('cleyton.souza@pb.com');
				$message->from = Yii::app()->params['eviaremail'];
				Yii::app()->mail->send($message); 


      
      }
        else 
        	if($juridico == "documento_juri" && $tempo < $limite_juridico && $tempo > $limite_juridico_2){
              
              #Amarelo
              $return.= "<td><b><font color='#FFD700'>" . utf8_encode($linha["tracking"]) . "</td></font></b>"; 
              $return.= "<td><b><font color='#FFD700'>" . utf8_encode($linha["remetente"]) . "</td></font></b>";
              $return.= "<td><b><font color='#FFD700'>" . utf8_encode($linha["destinatario"]) . "</td></font></b>";
              $return.= "<td><b><font color='#FFD700'>" . utf8_encode($transp) . "</td></font></b>";
 
              $return.= "<td><b><font color='#FFD700'>" . date('d/m/Y H:i:s', strtotime($data)) . "</td></font></b>";  
 
           $return.= "</tr>"; 
     }

//outros

         else 
         	 if($juridico == "n" && $transp != "SED" && $tempo > $limite_outros_1){
 
               $return.= "<td><b><font color='#FF0000'>" . utf8_encode($linha["tracking"]) . "</td></font></b>"; 
               $return.= "<td><b><font color='#FF0000'>" . utf8_encode($linha["remetente"]) . "</td></font></b>";
               $return.= "<td><b><font color='#FF0000'>" . utf8_encode($linha["destinatario"]) . "</td></font></b>";
               $return.= "<td><b><font color='#FF0000'>" . utf8_encode($transp) . "</td></font></b>";
 
               $return.= "<td><b><font color='#FF0000'>" . date('d/m/Y H:i:s', strtotime($data)) . "</td></font></b>";  
 
               $return.= "</tr>"; 
      }


          else 
          	 
          	 if($juridico == "n" && $transp != "SED" && $tempo < $limite_outros_1 && $tempo > $limite_outros_2){
 
              $return.= "<td><b><font color='#FFD700'>" . utf8_encode($linha["tracking"]) . "</td></font></b>"; 
              $return.= "<td><b><font color='#FFD700'>" . utf8_encode($linha["remetente"]) . "</td></font></b>";
              $return.= "<td><b><font color='#FFD700'>" . utf8_encode($linha["destinatario"]) . "</td></font></b>";
              $return.= "<td><b><font color='#FFD700'>" . utf8_encode($transp) . "</td></font></b>";
 
              $return.= "<td><b><font color='#FFD700'>" . date('d/m/Y H:i:s', strtotime($data)) . "</td></font></b>";  
 
              $return.= "</tr>"; 
      }

             //sedex 10

           else 

           	  if($transp == "SED" && $tempo > $limite_sedex10_1){
 
              $return.= "<td><b><font color='#FF0000'>" . utf8_encode($linha["tracking"]) . "</td></font></b>"; 
              $return.= "<td><b><font color='#FF0000'>" . utf8_encode($linha["remetente"]) . "</td></font></b>";
              $return.= "<td><b><font color='#FF0000'>" . utf8_encode($linha["destinatario"]) . "</td></font></b>";
              $return.= "<td><b><font color='#FF0000'>" . utf8_encode($transp) . "</td></font></b>";
 
              $return.= "<td><b><font color='#FF0000'>" . date('d/m/Y H:i:s', strtotime($data)) . "</td></font></b>";  
 
              $return.= "</tr>"; 
           }#if


            else 

	           if($transp == "SED" && $tempo < $limite_sedex10_1 && $tempo > $limite_sedex10_2){
 
                 $return.= "<td><b><font color='#FFD700'>" . utf8_encode($linha["tracking"]) . "</td></font></b>"; 
                 $return.= "<td><b><font color='#FFD700'>" . utf8_encode($linha["remetente"]) . "</td></font></b>";
                 $return.= "<td><b><font color='#FFD700'>" . utf8_encode($linha["destinatario"]) . "</td></font></b>";
                 $return.= "<td><b><font color='#FFD700'>" . utf8_encode($transp) . "</td></font></b>";
 
                 $return.= "<td><b><font color='#FFD700'>" . date('d/m/Y H:i:s', strtotime($data)) . "</td></font></b>";  
 
                 $return.= "</tr>"; 
              }#if


           else{
 
                 $return.= "<td>" . utf8_encode($linha["tracking"]) . "</td>"; 
                 $return.= "<td>" . utf8_encode($linha["remetente"]) . "</td>";
                 $return.= "<td>" . utf8_encode($linha["destinatario"]) . "</td>";
                 $return.= "<td>" . utf8_encode($transp) . "</td>";
 
                 $return.= "<td>" . date('d/m/Y H:i:s', strtotime($data)) . "</td>";  
 
                 $return.= "</tr>"; 
                }#else

             } #while


                 echo $return.="</tbody></table>"; 
			 
			 
			 
			  } #if($numRegistros !=0)

			  else {
			     echo "Nenhuma correspondencia foi encontrada";
				 }
		?>
<?php
echo "<meta HTTP-EQUIV='refresh' CONTENT='005000;URL=http://localhost/qualicorp/index.php?r=AuditSla'>";
?>

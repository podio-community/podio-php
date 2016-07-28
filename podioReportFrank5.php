<?php
            error_reporting(0);
            require_once 'podio/PodioAPI.php';

            function codigo_fuente($url){
                $url = file($url);
                $codigo = '';
                foreach ($url as $numero => $linea) { 
                    $codigo .= '#<strong>' .$numero . '</strong> : ' . htmlspecialchars($linea) . '<br />';
                }
                return $codigo;
            }

            try {
                $weekday=30;
            foreach($_GET as $key => $value){
              //echo $key . " : " . $value . "<br />\r\n";
                    $weekday=$key;
                 }
              //  echo $weekday;


             Podio::setup("api key goes here", "api secret goes here");
            Podio::authenticate_with_password("usermail goes here", "user pass goes here");
            //Podio::authenticate_with_app("app id goes here", "app token goes here");
            $year = date("Y");
            $array1 = array();
            $array2 = array();
            $opt="";
            $name1="$month1 $FromDayLast/$ToDayLast, $yearLast";
            $name2="$month2 $FromDayActual/$ToDayActual, $yearActual";
            //echo "Name1 is".$name1;
            //echo "Name2 is ".$name2;
            $tabla="<table width=70%>
             <tr>
             <td style=width: 10%; font-size: 7pt;  valign=top><strong></strong></td>
               <td style=width: 10%; font-size: 7pt;  valign=top><strong></strong></td>
               <td style=width: 10%; font-size: 7pt;  valign=top><strong>$columna1</strong></td>
               <td style=width: 10%; font-size: 7pt;  valign=top><strong>$columna2</strong></td>
               <td style=width: 10%; font-size: 7pt;  valign=top><strong>$columna3</strong></td>
               <td style=width: 10%; font-size: 7pt;  valign=top><strong>$columna4</strong></td>
               <td style=width: 10%; font-size: 7pt;  valign=top><strong>$columna5</strong></td>

              </tr>
              ";



            $valores="";  $totalesLast1=""; $totalesLast2=""; $totalesLast3=""; $totalesActual="";
            $campaign = PodioItem::filter(8900539, array( 'sort_by' => 'title',
                                                            'sort_desc' => false)); // Get items from app with app_id=123
            $cant=0; $tot=0;
            foreach ($campaign as $item) {
                //echo "<br/>*************************************************<br/>";
             // echo "<br/>title::".$item->title;
              //echo "<br/>ID::".$item->item_id;

              $se=false;$se1=false;$se2=false;
                if($item->title=="Other"){
                  $se=true;
                }
                if($item->title=="Offsite"){
                    $se1=true;
                }
                if($item->title=="Central"){
                    $se2=true;
                }

                if($se==false && $se1==false && $se2==false){

              $cant = $cant +1;
                $tabla=$tabla."<tr>

               <td style=width: 20%; font-size: 7pt;  valign=top>"
              .$cant."</td>";



              $tabla=$tabla."<td style=width: 20%; font-size: 7pt;  valign=top>"
              .$item->title."</td>";

               $valores=$valores."|".$item->title;

                $app_reference_field_id = 116781926; 
               $attendancetype = 116781927;
               $date =  116781925;
               $adult = 9;
               $fourSeven = 10;
               $Fusion = 6;
               $Forge = 7;
               $WideOpen = 2;
               $Nursery = 5;
               $Preschool = 4;
               $Elementary = 8;

             //$date_lead_created=101404311;m
             $filter_target_item_id = $item->item_id;

            $attendance1 = PodioItem::filter(15171541, array( 'limit' => 400, 'offset' => 0, 
                  'filters' => array(
                    $app_reference_field_id => array($filter_target_item_id),
                    $date  => array(
                                      'from' => date("Y-m-d H:i:s", strtotime("01/01/$year")), 'to' => date("Y-m-d H:i:s")
                                    )
                  ),
                  'limit' => 200, 'offset' => $offset
                ));
                $valor = 0;

                if($attendance1->filtered>0){
                    $sum =0;
                    foreach($attendance1 as $item){

                        $sum = $sum + $item->fields["total-attendance"]->values;        
                                  //  echo "<br>Sum is ".$sum."<br>";
                    }

                    $weekNumber = date("W"); 
                            if($weekday==0)
                                $weekday=$weekNumber;
                    //$weekNumber = $weekNumber -1; 
                         //   echo "The Week Number is ".$weekNumber;
                    $valor = round(($sum/$weekday));
                       //  echo $sum."<br>";
                }else{
                    $valor = 0;
                }
                $totales = $totales.",".$valor;
                 $tot= $tot + $valor;
                $tabla=$tabla."<td style=width: 10%; font-size: 7pt;  valign=top>".$valor."</td>";
                }   
            }

            $tabla=$tabla."</table>";

            $totalesLast1 = substr($totalesLast1,0,strlen($totalesLast1)-1);
            $totalesLast2 = substr($totalesLast2,0,strlen($totalesLast2)-1);
            $totalesLast3 = substr($totalesLast3,0,strlen($totalesLast3)-1);
            //$totalesActual = substr($totalesActual,0,strlen($totalesActual)-1);

            //$totalesLast = $totalesLast ."|".$totalesActual;
            echo $tabla;

            //echo "<br/>totales::::$totalesLast";

            //print_r($array1);
            //print_r($array2);

            $mayor1 = $array1[12];
            $mayor2 = $array2[12];
            $mayor="";
            if($mayor1>$mayor2){
                $mayor=$mayor1;
            }else{
                $mayor=$mayor2;
            }

            $valores = substr($valores,1,strlen($valores)-1);
            $valores = str_replace(" ","%20",$valores);
            $name1 = str_replace(" ","%20",$name1);
            $name2 = str_replace(" ","%20",$name2);

            $columna1 = str_replace(" ","%20",$columna1);
            $columna2 = str_replace(" ","%20",$columna2);
            $columna3 = str_replace(" ","%20",$columna3);
            $columna4 = str_replace(" ","%20",$columna4);
            $columna5 = str_replace(" ","%20",$columna5);

            $valores = substr($valores,0,strlen($valores));
            $valores = str_replace(" ","%20",$valores);
            //echo $valores."<br/>";
            $totales = substr($totales,1,strlen($totales)-1);
            //echo $totales."<br/>";

            $totales2 = str_replace(",","|",$totales);

            $totaless="";
            $varr = explode(",",$totales);
            foreach($varr as $tt){
                //echo "<br/>valor::".$tt;
                $v1= $tt*100;
                $v2= $v1/$tot;
                $totaless = $totaless.",".$v2;
            }
            $totaless = substr($totaless,1,strlen($totaless)-1);

            $url = "https://chart.googleapis.com/chart?cht=p3&chs=700x300&chd=t:$totaless&chl=$totales2&chdl=$valores&chco=FFFF10,FF0000,0072c6|ef3886|ff9900";
            //$url = "https://chart.googleapis.com/chart?cht=p3&chs=250x100&chd=t:60,40&chl=Hello|World";
            //echo $url;

            if(@copy($url, 'cinco.png')){
            //echo "image-saved";
            //echo "http://aparicio.website/uno.png";
            }else{
            //echo "failed"; 
            }

            }



            catch (PodioError $e) {
             echo "error $e"; // Something went wrong. Examine $e->body['error_description'] for a description of the error.
            }

            ?>

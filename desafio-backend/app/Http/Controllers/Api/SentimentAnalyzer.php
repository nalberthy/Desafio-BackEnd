<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use App\Http\Controllers\request\RequestPost;
use Illuminate\Support\Facades\Http;
use DateTime;


class SentimentAnalyzer extends Controller
{
    function __construct() {
        $this->post = new RequestPost;
    }

    public function getClassificacao($text){
        return  $this->get->httpget($text);
    }

    public function postMessage($text){
        return  $this->post->httppost($text);
    }


    public function classificar_Ticket(){

        $json_ticket = Storage::get('public\tickets.json');


        $listatickets = json_decode($json_ticket, true);

        foreach ($listatickets as $i=>$ticket) {
            $class_sentimental = $this-> postMessage(array('message'=> $ticket['Interactions'][0]['Message']));
            
            array_push($ticket, $class_sentimental['classificacao']);

            $ticket['Classificacao'] = $ticket[0];
            unset($ticket[0]);

            if ($class_sentimental['classificacao']['neg'] > $class_sentimental['classificacao']['pos']) {
                array_push($ticket, "Alta");
            }
            elseif($class_sentimental['classificacao']['pos']>$class_sentimental['classificacao']['neg']){
                array_push($ticket,  "Normal");
            }
            else{
                array_push($ticket,  "Normal");
            }

            $ticket['Prioridade'] = $ticket[1];
            unset($ticket[1]);
            
           $listatickets[$i] = $ticket;
        }

        return ($listatickets);
    }

    public function ordenar(Request $request){
        $tipo_ordenacao= $request['tipo_ordenacao'];
        
        if ($tipo_ordenacao == "data_criacao"){
            $listatickets = $this->classificar_Ticket();
            $listaOrdenada = array();

            uasort($listatickets, function($item1, $item2){
                return strtotime($item1['DateCreate']) > strtotime($item2['DateCreate']);
            });
            
            foreach ($listatickets as $ticket) { array_push($listaOrdenada, $ticket);}
           
            return json_encode($listaOrdenada);
        }
        elseif($tipo_ordenacao == "data_atualizacao"){
            $listatickets = $this->classificar_Ticket();
            $listaOrdenada = array();

            uasort($listatickets, function($item1, $item2){
                return strtotime($item1['DateUpdate']) > strtotime($item2['DateUpdate']);
            });
            
            foreach ($listatickets as $ticket) { array_push($listaOrdenada, $ticket);}
    
            return json_encode($listaOrdenada);
        }
        elseif($tipo_ordenacao == "prioridade"){
            $listatickets = $this->classificar_Ticket();
            $listaOrdenada = array();

            uasort($listatickets, function($item1, $item2){
                return strcmp($item1['Prioridade'], $item2['Prioridade']);
            });

            foreach ($listatickets as $ticket) { array_push($listaOrdenada, $ticket);}

            return json_encode($listaOrdenada);
        }
       
    }

    public function calcula_intervalo($data_inicio, $data_fim){

        $dateStart = $data_inicio;
        $dateStart = implode('-', array_reverse(explode('/', substr($dateStart, 0, 10)))).substr($dateStart, 10);
        $dateStart = new DateTime($dateStart);
        
        $dateEnd = $data_fim;
        $dateEnd = implode('-', array_reverse(explode('/', substr($dateEnd, 0, 10)))).substr($dateEnd, 10);
        $dateEnd = new DateTime($dateEnd);
        
        $dateRange = array();
        while($dateStart <= $dateEnd){
            $dateRange[] = $dateStart->format('Y-m-d');
            $dateStart = $dateStart->modify('+1day');
        }
        return ($dateRange);
    }



    public function search_intervalo(Request $request){
        $data_inicio= $request['data_inicio'];
        $data_fim=$request['data_fim'];

        $listatickets = $this->classificar_Ticket();
        $intervalo_datas = $this->calcula_intervalo($data_inicio, $data_fim);
        $listaBusca = array();

        foreach ($listatickets as $ticket) { 
            foreach ($intervalo_datas as $i){ 
                if(substr($ticket['DateCreate'],0,10) == $i){
                    array_push($listaBusca, $ticket);
                };
            }
        }

        return (json_encode($listaBusca));
    }


    public function search_prioridade(Request $request){
        $prioridade=$request['prioridade'];
        $listatickets = $this->classificar_Ticket();
        $listaBusca = array();
        foreach ($listatickets as $ticket) { 
            if($ticket['Prioridade']==$prioridade){
                array_push($listaBusca, $ticket);
            };
        }
        return (json_encode($listaBusca));
    }

}


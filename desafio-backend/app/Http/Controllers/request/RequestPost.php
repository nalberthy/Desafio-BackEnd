<?php

namespace App\Http\Controllers\request;

class RequestPost
{
    public function httppost($json){
        $url = 'http://127.0.0.1:5000/api/sentimento';
        $post = json_encode($json);
        
        $ch = curl_init($url); // Inicializar cURLL
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); //Injete o token no cabeçalho
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1); // Especifique o método de solicitação como POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Seta os campos postados
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Isso seguirá todos os redirecionamentos
        $result = curl_exec($ch); // Execute a instrução cURL

        curl_close($ch); // Feche a conexão cUR
      
        $result= json_decode($result, true);
        
        if($result==null){
            return  ['classificacao'=>''];
        }
        elseif($result){
            return  ['classificacao'=>$result['data']];
        }

    }

}
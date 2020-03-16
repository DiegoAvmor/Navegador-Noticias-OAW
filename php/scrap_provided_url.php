<?php
include_once 'db_connection.php';


$url = $_GET['url'];
scrapWebsite($url);

function scrapWebsite($websiteUrl){

    //Se establece las operaciones con curl para obtener el contenido del sitio web
    $curl = curl_init($websiteUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curl);
    if(curl_errno($curl))
    {
        echo 'Scraper error: ' . curl_error($curl);
        exit;
    }
    curl_close($curl);

    //Se obtiene el titulo del html
    $htmlTitle = geTitleFromHTML($response);

    //Se obtiene el contenido del html en crudo
    $rawWebPage = getRawFromHTML($response);

    //Se obtiene la ultima modificada(No muchos sitios te dan la ultima fecha en que fue modificada)
    $date = date("Y-m-d");
    insertToDB($websiteUrl, $htmlTitle, $rawWebPage, '',$date);
}

function getRawFromHTML($html) {
    //Se elimina el js del html
    $strippedJS = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
    //Se elimina los tags del html
    $strippedHTML = htmlspecialchars(trim(strip_tags($strippedJS)));
    //Se elimina los saltos o breaks del html
    $rawData = preg_replace( '/\r|\n/', '', $strippedHTML );
    //Se elimina el espaciado de mas
    $rawData = preg_replace('/\s\s+/',' ',$rawData);
    return $rawData;
}

function geTitleFromHTML($html){
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $list = $dom->getElementsByTagName("title");
    return ($list->length > 0)? $list->item(0)->textContent : 'No Title Found';
}

function insertToDB($url, $title, $body, $description, $last_modified){
    //Se establece la conexion con la base de datos
    $dbconnection= establish_db_connection();
    //Si existe en la base de datos, se actualiza sus datos
    if($dbconnection->has("website",['title' =>$title])){
        //Se obtiene la ultima fecha modificada del sitio en la base de datos
        $webSite = $dbconnection->get("website",['last_modified'],['title' =>$title]);
        $db_date = $webSite['last_modified'];
        //Se actualiza los campos del registro si se identifica que fue modificado
        if($db_date < $last_modified){
            print_r("Se modifico el recurso, se actualiza");
            $dbconnection->update("website",
                [
                    'body'=>$body,
                    'last_modified'=>$last_modified
                ],
                [
                    'url'=>$url
                ]
            );
        }

    }else{
        //Se crea un registro en la base de datos
        $dbconnection->insert(
            "website",
            [
                'title'=> $title,
                'url'=> $url,
                'body'=> $body,
                'last_modified'=> $last_modified,
            ]
        );
    }
}



?>
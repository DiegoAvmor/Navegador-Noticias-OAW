const MONTHS = [ { "name": "January" }, { "name": "February" }, { "name": "March" }, { "name": "April" }, { "name": "May" }, { "name": "June" }, { "name": "July" }, { "name": "August" }, { "name": "September" }, { "name": "October" }, { "name": "November" }, { "name": "December" } ];

getRSS("retrieved");//Se Obtiene las noticias una vez que se carga el html

//Realiza la accion de buscar las noticias en el url introducido
$('#btn').click(
    function(){
        let input = $('#searchInput');
        if(input.val()){
            getRSS("search",input.val());
            input.val('');//Se limpia la busqueda
        }
    }
); 

//Realiza el colapso de las sublistas
$(function() {
    $('.list-group-item').on('click', function() {
      $('.glyphicon', this)
        .toggleClass('glyphicon-chevron-right')
        .toggleClass('glyphicon-chevron-down');
    });
  
});

/**
 * Metodo que tiene como funcion la obtencion de las noticias
 * a partir de la accion y url introducidos
 * @param {String} action La accion a realizar por parte del backend
 * @param {String} url la url con la que se efectuara la accion
 */
function getRSS(action,url){
    $.ajax({
        url: "php/server.php",
        type: "get",
        data: { 
          url: url,
          action: action
        },
        success: function(response) {
            let parsedResponse = JSON.parse(response);
            let responseArray = new Array();
            parsedResponse.forEach(element => {
                let elementParsed = JSON.parse(element);
                responseArray.push(elementParsed);
            });
            displayDates(responseArray);
        },
        error: function(xhr) {
          console.log("Error");
        }
      });
}

/**
 * Metodo que visualiza las noticias en sus respectiva lista
 * @param {Array} newsArray Arreglo de noticias a visualizar
 * @param {Object} domElement El Objeto de documento donde se añadira las noticias
 * @see convertDate
 * @see createNewList
 */
function displayInformationInList(newsArray,domElement){
    
    domElement.empty();//Se eliminan lo hijos del elemento del DOM

    let dateType = domElement.attr('id');

    newsArray.forEach(news => {
        let dateSelection = convertDate(dateType,news.Date);

        //Creacion del contenedor de la noticia
        let feedContent = document.createElement('div');
        feedContent.setAttribute('class','list-group-item');

        //Creacion del encabezado
        let title = document.createElement('a'); 
        title.innerHTML = news.Title + " Date: " + news.Date;
        title.setAttribute('href',news.TitleURL); //Se adiciona el url de la noticia al encabezado
        feedContent.appendChild(title);//Se añade el encabezado al contenedor

        //Creacion del contenedor del cuerpo de la noticia
        let descriptionContent = document.createElement('div');
        let author = document.createElement('p');
        author.setAttribute('class','text-justify');
        author.innerHTML = news.Author;
        let description = document.createElement('p');
        description.setAttribute('class','text-justify');
        description.innerHTML = news.Description;

        descriptionContent.appendChild(author);//Se añade al contenedor el author
        descriptionContent.appendChild(description);//Se añade al contendor la descripcion
        feedContent.appendChild(descriptionContent); //Se añade al contenedor de la noticia el contenedor del cuerpo de la noticia

        if(checkIfExists(dateSelection)){//Verifica si una sublista existe y en base a eso hace lo correspondiente
            let sublist = $('div#'+dateSelection);
            sublist.append(feedContent);
        }else{
            createNewList(dateSelection,domElement);
            let sublist = $('div#'+dateSelection);
            sublist.append(feedContent);
        }

    });
}

/**
 * Metodo encargado de la creacion de nuevas sublistas en el elemento dom correspondiente
 * @param {String} listname Nombre de la nueva lista
 * @param {Object} domElement Elemento del dom al cual se le añadira las nuevas listas
 */
function createNewList(listname,domElement){
    let collapsableArrow = document.createElement('a');
    collapsableArrow.setAttribute('href','#'+listname);
    collapsableArrow.setAttribute('data-toggle',"collapse");
    collapsableArrow.setAttribute('class','list-group-item');
    let collapsableTitle = document.createElement('i');
    collapsableTitle.setAttribute('class','glyphicon glyphicon-chevron-right');

    //Encabezado de la lista colapsable
    collapsableArrow.append(collapsableTitle);
    collapsableArrow.append(listname);

    domElement.append(collapsableArrow);

    //Se crea el apartado para el contenido
    let collapsableContent = document.createElement('div');
    collapsableContent.setAttribute('class','list-group collapse');
    collapsableContent.setAttribute('id',listname);

    domElement.append(collapsableContent);
}


/**
 * Metodo visualizara las noticias para cada lista, siendo estas
 * en la lista de año, mes y dia
 * @param {Array} array El arreglo de noticias a visualizar
 * @see displayInformationInList
 */
function displayDates(array){
    var domElement;
    let orderedArray = array.slice().sort(function(a,b){
        return new Date(a.Date) - new Date(b.Date);
    });
    domElement = $('div#order-year');
    displayInformationInList(orderedArray,domElement);
    
    domElement = $('div#order-month');
    displayInformationInList(orderedArray,domElement);

    domElement = $('div#order-day');
    displayInformationInList(orderedArray,domElement);

}


/**
 * Metodo que verifica si existe la estructura indicado en el DOM
 * @param {String} elementName El nombre del objeto de tipo div a verificar
 */
function checkIfExists(elementName){
    return ($('div#' + elementName).length);
}

/**
 * Metodo cuya funcion es hacer la conversion de la fecha para solo obtener su año
 * mes o dia
 * @param {String} dateType El tipo de fecha que se desea obtener.
 * @param {String} dateValue La fecha a la que se le aplicara su conversion.
 */
function convertDate(dateType,dateValue){
    let selectedDate = new Date(dateValue);
    if(dateType === "order-year"){
        return selectedDate.getUTCFullYear();
    }
    if(dateType === "order-month"){
        return MONTHS[selectedDate.getUTCMonth()].name;
    }
    if(dateType === "order-day"){
        return selectedDate.getUTCDate();
    }
}